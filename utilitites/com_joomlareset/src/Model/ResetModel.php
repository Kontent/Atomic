<?php
/**
 * @package    Joomla Reset
 * @copyright	 (c) 2026 Ron Severdia. All rights reserved.
 * @license		 GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Severdia\Component\JoomlaReset\Administrator\Model;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Version;
use Joomla\Database\DatabaseInterface;

class ResetModel extends BaseDatabaseModel
{
	/**
	 * Get info for the admin UI: Joomla version, admin user, table count.
	 */
	public function getResetInfo(): array
	{
		$db      = $this->getDatabase();
		$version = new Version();
		$major   = Version::MAJOR_VERSION;

		// Get first Super User
		$admin = $this->getFirstSuperUser();

		// Count tables
		$prefix = Factory::getApplication()->get('dbprefix', 'jos_');
		$tables = $this->getJoomlaTables($prefix);

		return [
			'joomla_version'  => $version->getShortVersion(),
			'joomla_major'    => (int) $major,
			'admin_user'      => $admin ? $admin->username : '(none found)',
			'admin_email'     => $admin ? $admin->email : '',
			'table_count'     => count($tables),
			'supported'       => in_array((int) $major, [5, 6], true),
		];
	}

	/**
	 * Execute the full reset sequence.
	 */
	public function resetDatabase(): array
	{
		$db      = $this->getDatabase();
		$prefix  = Factory::getApplication()->get('dbprefix', 'jos_');
		$major   = (int) Version::MAJOR_VERSION;

		if (!in_array($major, [5, 6], true)) {
			return ['success' => false, 'error' => 'Unsupported Joomla version: ' . $major];
		}

		try {
			// Phase 1: Preserve admin user
			$admin = $this->getFirstSuperUser();

			if (!$admin) {
				return ['success' => false, 'error' => 'No Super User found to preserve.'];
			}

			$adminData     = clone $admin;
			$adminGroupMap = $this->getUserGroupMap($admin->id);

			// Phase 2: Drop all tables
			$tables = $this->getJoomlaTables($prefix);

			$db->setQuery('SET FOREIGN_KEY_CHECKS = 0');
			$db->execute();

			foreach ($tables as $table) {
				$db->setQuery('DROP TABLE IF EXISTS ' . $db->quoteName($table));
				$db->execute();
			}

			$db->setQuery('SET FOREIGN_KEY_CHECKS = 1');
			$db->execute();

			// Phase 3: Recreate from bundled SQL
			$sqlDir = JPATH_ADMINISTRATOR . '/components/com_joomlareset/sql/j' . $major;

			foreach (['base.sql', 'supports.sql', 'extensions.sql'] as $file) {
				$sqlFile = $sqlDir . '/' . $file;

				if (!file_exists($sqlFile)) {
					return ['success' => false, 'error' => 'Missing SQL file: ' . $file];
				}

				$this->executeSqlFile($sqlFile, $prefix);
			}

			// Phase 4: Populate #__schemas so Joomla's Database Fix tool knows
			// which migrations have been "applied". Without this it tries to
			// replay every migration from scratch on a freshly reset database
			// and fails because the tables already exist at the current version.
			$this->populateSchemas();

			// Phase 5: Clear checked_out flags on all tables that have them
			$this->clearCheckedOut();

			// Phase 6: Restore admin user
			$this->restoreAdminUser($adminData, $adminGroupMap);

			// Phase 7: Re-register this component
			$this->registerSelf($prefix);

			return ['success' => true, 'error' => ''];
		} catch (\Exception $e) {
			return ['success' => false, 'error' => $e->getMessage()];
		}
	}

	/**
	 * Find the first Super User (group 8).
	 */
	private function getFirstSuperUser(): ?object
	{
		$db = $this->getDatabase();

		$query = $db->getQuery(true)
			->select('u.*')
			->from($db->quoteName('#__users', 'u'))
			->join(
				'INNER',
				$db->quoteName('#__user_usergroup_map', 'm')
				. ' ON ' . $db->quoteName('m.user_id') . ' = ' . $db->quoteName('u.id')
			)
			->where($db->quoteName('m.group_id') . ' = 8')
			->order($db->quoteName('u.id') . ' ASC')
			->setLimit(1);

		$db->setQuery($query);

		return $db->loadObject() ?: null;
	}

	/**
	 * Get all group mappings for a user.
	 */
	private function getUserGroupMap(int $userId): array
	{
		$db = $this->getDatabase();

		$query = $db->getQuery(true)
			->select($db->quoteName('group_id'))
			->from($db->quoteName('#__user_usergroup_map'))
			->where($db->quoteName('user_id') . ' = ' . (int) $userId);

		$db->setQuery($query);

		return $db->loadColumn() ?: [8];
	}

	/**
	 * Get all tables matching the Joomla prefix.
	 */
	private function getJoomlaTables(string $prefix): array
	{
		$db = $this->getDatabase();

		$db->setQuery('SHOW TABLES LIKE ' . $db->quote($prefix . '%'));

		return $db->loadColumn() ?: [];
	}

	/**
	 * Populate the #__schemas table with the latest known migration version
	 * for every extension on disk that ships SQL update files.
	 *
	 * Joomla's "Database → Fix" tool reads #__schemas to decide which
	 * migration files still need to run for each extension. Our reset
	 * recreates the tables at their current version but leaves #__schemas
	 * empty, so without this step Joomla would try to replay every
	 * migration ever shipped and fail on the first one that conflicts
	 * with the already-correct schema.
	 */
	private function populateSchemas(): void
	{
		$db = $this->getDatabase();

		foreach ($this->findMigrationDirs() as $candidate) {
			[$type, $element, $folder, $clientId, $dir] = $candidate;

			$latest = $this->getLatestMigrationVersion($dir);

			if ($latest === null) {
				continue;
			}

			// Look up extension_id for this entry.
			$query = $db->getQuery(true)
				->select($db->quoteName('extension_id'))
				->from($db->quoteName('#__extensions'))
				->where($db->quoteName('type') . ' = ' . $db->quote($type))
				->where($db->quoteName('element') . ' = ' . $db->quote($element))
				->where($db->quoteName('client_id') . ' = ' . (int) $clientId);

			if ($folder !== '') {
				$query->where($db->quoteName('folder') . ' = ' . $db->quote($folder));
			} else {
				$query->where($db->quoteName('folder') . " = ''");
			}

			$db->setQuery($query);
			$extensionId = (int) $db->loadResult();

			if (!$extensionId) {
				continue;
			}

			try {
				$query = $db->getQuery(true)
					->insert($db->quoteName('#__schemas'))
					->columns($db->quoteName(['extension_id', 'version_id']))
					->values((int) $extensionId . ',' . $db->quote($latest));

				$db->setQuery($query);
				$db->execute();
			} catch (\Exception $e) {
				// Already populated for this extension — leave whatever's there alone.
				continue;
			}
		}
	}

	/**
	 * Locate every directory on disk that holds Joomla migration SQL files.
	 *
	 * Returns rows of [type, element, folder, client_id, absolute_dir].
	 * `folder` is only meaningful for plugins (group name); empty otherwise.
	 */
	private function findMigrationDirs(): array
	{
		$result = [];

		// Admin components
		foreach (glob(JPATH_ADMINISTRATOR . '/components/*/sql/updates/mysql', GLOB_ONLYDIR) ?: [] as $dir) {
			$element  = basename(\dirname($dir, 3));
			$result[] = ['component', $element, '', 1, $dir];
		}

		// Site components (rare but possible)
		foreach (glob(JPATH_ROOT . '/components/*/sql/updates/mysql', GLOB_ONLYDIR) ?: [] as $dir) {
			$element  = basename(\dirname($dir, 3));
			$result[] = ['component', $element, '', 0, $dir];
		}

		// Plugins (folder = plugin group, element = plugin name)
		foreach (glob(JPATH_ROOT . '/plugins/*/*/sql/updates/mysql', GLOB_ONLYDIR) ?: [] as $dir) {
			$element  = basename(\dirname($dir, 3));
			$folder   = basename(\dirname($dir, 4));
			$result[] = ['plugin', $element, $folder, 0, $dir];
		}

		// Admin modules
		foreach (glob(JPATH_ADMINISTRATOR . '/modules/*/sql/updates/mysql', GLOB_ONLYDIR) ?: [] as $dir) {
			$element  = basename(\dirname($dir, 3));
			$result[] = ['module', $element, '', 1, $dir];
		}

		// Site modules
		foreach (glob(JPATH_ROOT . '/modules/*/sql/updates/mysql', GLOB_ONLYDIR) ?: [] as $dir) {
			$element  = basename(\dirname($dir, 3));
			$result[] = ['module', $element, '', 0, $dir];
		}

		return $result;
	}

	/**
	 * Return the highest-versioned migration filename (without .sql extension)
	 * in the given directory, or null if the directory has no SQL files.
	 *
	 * Joomla names migration files with a version-like prefix
	 * (e.g. "5.4.6-2025-10-15.sql"), so version_compare gives the right order.
	 */
	private function getLatestMigrationVersion(string $dir): ?string
	{
		$files = glob($dir . '/*.sql') ?: [];

		if (empty($files)) {
			return null;
		}

		$versions = array_map(static fn ($f) => basename($f, '.sql'), $files);

		usort($versions, 'version_compare');

		return end($versions);
	}

	/**
	 * Clear checked_out flags on all tables that have a checked_out column.
	 */
	private function clearCheckedOut(): void
	{
		$db     = $this->getDatabase();
		$tables = ['#__menu', '#__content', '#__categories', '#__modules', '#__contact_details', '#__newsfeeds', '#__banners', '#__fields', '#__tags'];

		foreach ($tables as $table) {
			try {
				$query = $db->getQuery(true)
					->update($db->quoteName($table))
					->set($db->quoteName('checked_out') . ' = NULL')
					->set($db->quoteName('checked_out_time') . ' = NULL');

				$db->setQuery($query);
				$db->execute();
			} catch (\Exception $e) {
				// Table may not exist or lack the column — skip silently
				continue;
			}
		}
	}

	/**
	 * Parse and execute a Joomla SQL file, replacing #__ with the real prefix.
	 */
	private function executeSqlFile(string $filePath, string $prefix): void
	{
		$db  = $this->getDatabase();
		$sql = file_get_contents($filePath);

		// Replace the generic prefix placeholder with the actual prefix
		$sql = str_replace('#__', $prefix, $sql);

		// Split into individual statements
		$statements = $this->splitSql($sql);

		$db->setQuery('SET FOREIGN_KEY_CHECKS = 0');
		$db->execute();

		foreach ($statements as $statement) {
			$statement = trim($statement);

			if (empty($statement)) {
				continue;
			}

			$db->setQuery($statement);
			$db->execute();
		}

		$db->setQuery('SET FOREIGN_KEY_CHECKS = 1');
		$db->execute();
	}

	/**
	 * Split a SQL dump into individual statements, handling quoted strings
	 * and multi-line values correctly.
	 */
	private function splitSql(string $sql): array
	{
		$statements = [];
		$current    = '';
		$inString   = false;
		$stringChar = '';
		$length     = strlen($sql);

		for ($i = 0; $i < $length; $i++) {
			$char = $sql[$i];

			// Handle string literals
			if ($inString) {
				$current .= $char;

				// Check for escaped quote
				if ($char === '\\') {
					// Next char is escaped, append and skip
					if ($i + 1 < $length) {
						$current .= $sql[++$i];
					}
				} elseif ($char === $stringChar) {
					// Check for doubled quote (e.g., '')
					if ($i + 1 < $length && $sql[$i + 1] === $stringChar) {
						$current .= $sql[++$i];
					} else {
						$inString = false;
					}
				}

				continue;
			}

			// Not in a string
			if ($char === '\'' || $char === '"') {
				$inString   = true;
				$stringChar = $char;
				$current   .= $char;

				continue;
			}

			// Skip single-line comments
			if ($char === '-' && $i + 1 < $length && $sql[$i + 1] === '-') {
				// Skip to end of line
				while ($i < $length && $sql[$i] !== "\n") {
					$i++;
				}

				continue;
			}

			// Skip block comments
			if ($char === '/' && $i + 1 < $length && $sql[$i + 1] === '*') {
				$i += 2;

				while ($i + 1 < $length && !($sql[$i] === '*' && $sql[$i + 1] === '/')) {
					$i++;
				}

				$i++; // Skip closing /

				continue;
			}

			// Statement terminator
			if ($char === ';') {
				$trimmed = trim($current);

				if (!empty($trimmed)) {
					$statements[] = $trimmed;
				}

				$current = '';

				continue;
			}

			$current .= $char;
		}

		// Don't forget the last statement if no trailing semicolon
		$trimmed = trim($current);

		if (!empty($trimmed)) {
			$statements[] = $trimmed;
		}

		return $statements;
	}

	/**
	 * Re-insert the preserved admin user and their group mappings.
	 */
	private function restoreAdminUser(object $admin, array $groupIds): void
	{
		$db = $this->getDatabase();

		// Insert user record
		$columns = [
			'id', 'name', 'username', 'email', 'password',
			'block', 'sendEmail', 'registerDate', 'lastvisitDate',
			'activation', 'params', 'lastResetTime', 'resetCount',
			'otpKey', 'otep', 'requireReset',
		];

		$query = $db->getQuery(true)
			->insert($db->quoteName('#__users'))
			->columns($db->quoteName($columns))
			->values(implode(',', [
				(int) $admin->id,
				$db->quote($admin->name),
				$db->quote($admin->username),
				$db->quote($admin->email),
				$db->quote($admin->password),
				(int) ($admin->block ?? 0),
				(int) ($admin->sendEmail ?? 1),
				$db->quote($admin->registerDate ?? date('Y-m-d H:i:s')),
				$db->quote($admin->lastvisitDate ?? ''),
				$db->quote($admin->activation ?? ''),
				$db->quote($admin->params ?? '{}'),
				$db->quote($admin->lastResetTime ?? ''),
				(int) ($admin->resetCount ?? 0),
				$db->quote($admin->otpKey ?? ''),
				$db->quote($admin->otep ?? ''),
				(int) ($admin->requireReset ?? 0),
			]));

		$db->setQuery($query);
		$db->execute();

		// Insert group mappings
		foreach ($groupIds as $groupId) {
			$query = $db->getQuery(true)
				->insert($db->quoteName('#__user_usergroup_map'))
				->columns($db->quoteName(['user_id', 'group_id']))
				->values((int) $admin->id . ',' . (int) $groupId);

			$db->setQuery($query);
			$db->execute();
		}

		// Update default category created_user_id (defaults use 42 as placeholder)
		$query = $db->getQuery(true)
			->update($db->quoteName('#__categories'))
			->set($db->quoteName('created_user_id') . ' = ' . (int) $admin->id)
			->where($db->quoteName('created_user_id') . ' = 42');

		$db->setQuery($query);
		$db->execute();

		// Add asset entry for the admin user
		$query = $db->getQuery(true)
			->insert($db->quoteName('#__assets'))
			->columns($db->quoteName(['parent_id', 'lft', 'rgt', 'level', 'name', 'title', 'rules']))
			->values(implode(',', [
				1,
				0,
				0,
				1,
				$db->quote('com_users.user.' . (int) $admin->id),
				$db->quote($admin->name),
				$db->quote('{}'),
			]));

		$db->setQuery($query);
		$db->execute();
	}

	/**
	 * Re-register com_joomlareset in the extensions table and admin menu.
	 */
	private function registerSelf(string $prefix): void
	{
		$db = $this->getDatabase();

		// Insert into extensions table
		$query = $db->getQuery(true)
			->insert($db->quoteName('#__extensions'))
			->columns($db->quoteName([
				'name', 'type', 'element', 'folder',
				'client_id', 'enabled', 'access', 'protected',
				'locked', 'manifest_cache', 'params', 'custom_data',
			]))
			->values(implode(',', [
				$db->quote('COM_JOOMLARESET'),
				$db->quote('component'),
				$db->quote('com_joomlareset'),
				$db->quote(''),
				1,
				1,
				1,
				0,
				0,
				$db->quote('{}'),
				$db->quote('{}'),
				$db->quote(''),
			]));

		$db->setQuery($query);
		$db->execute();

		$extensionId = $db->insertid();

		// Delete the cached namespace map so Joomla regenerates it
		$cacheFile = JPATH_ADMINISTRATOR . '/cache/autoload_psr4.php';

		if (file_exists($cacheFile)) {
			@unlink($cacheFile);
		}

		// Add to admin menu
		// First get the root menu item id and its rgt value
		$query = $db->getQuery(true)
			->select([$db->quoteName('id'), $db->quoteName('rgt')])
			->from($db->quoteName('#__menu'))
			->where($db->quoteName('menutype') . ' = ' . $db->quote('main'))
			->where($db->quoteName('level') . ' = 0')
			->where($db->quoteName('parent_id') . ' = 0');

		$db->setQuery($query);
		$root = $db->loadObject();

		$rootId  = $root ? (int) $root->id : 1;
		$rootRgt = $root ? (int) $root->rgt : 43;

		// Calculate proper nested set values: insert as last child of root
		$newLft = $rootRgt;
		$newRgt = $rootRgt + 1;

		// Expand root to make room for the new item
		$query = $db->getQuery(true)
			->update($db->quoteName('#__menu'))
			->set($db->quoteName('rgt') . ' = ' . ($rootRgt + 2))
			->where($db->quoteName('id') . ' = ' . $rootId);

		$db->setQuery($query);
		$db->execute();

		// Insert the admin menu item with correct lft/rgt
		$query = $db->getQuery(true)
			->insert($db->quoteName('#__menu'))
			->columns($db->quoteName([
				'menutype', 'title', 'alias', 'note', 'path',
				'link', 'type', 'published', 'parent_id', 'level',
				'component_id',
				'browserNav', 'access', 'img', 'template_style_id',
				'params', 'lft', 'rgt', 'home', 'language', 'client_id',
			]))
			->values(implode(',', [
				$db->quote('main'),
				$db->quote('COM_JOOMLARESET'),
				$db->quote('Joomla-Reset'),
				$db->quote(''),
				$db->quote('Joomla-Reset'),
				$db->quote('index.php?option=com_joomlareset'),
				$db->quote('component'),
				1,
				(int) $rootId,
				1,
				(int) $extensionId,
				0,
				0,
				$db->quote('class:warning'),
				0,
				$db->quote('{}'),
				$newLft,
				$newRgt,
				0,
				$db->quote('*'),
				1,
			]));

		$db->setQuery($query);
		$db->execute();
	}
}
