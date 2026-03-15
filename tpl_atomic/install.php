<?php
/**
 * @copyright	Copyright (C) 2008-2026 Ron Severdia. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Version;

class AtomicInstallerScript
{
	public function postflight($route, $adapter)
	{
		$parent = $adapter->getParent();
		$source = $parent->getPath('source');

		// Initialize folders
		$this->initFolders();

		// Copy over CSS files, never overwriting user customizations
		$cssFiles = $this->getFiles($source . '/css', ['template.css']);
		$this->copyFiles($cssFiles, JPATH_ROOT . '/templates/atomic/css');

		// Ensure template.css exists (creates only if missing)
		$this->ensureCustomFile(JPATH_ROOT . '/templates/atomic/css/template.css', 'CSS');

		// Copy over JS files, never overwriting user customizations
		$jsFiles = $this->getFiles($source . '/js', ['template.js']);
		$this->copyFiles($jsFiles, JPATH_ROOT . '/templates/atomic/js');

		// Ensure template.js exists (creates only if missing)
		$this->ensureCustomFile(JPATH_ROOT . '/templates/atomic/js/template.js', 'JS');

		// Sync beta update channel with template setting
		$this->setBetaChannel();

		// Auto-set Bootstrap source to match the installed Joomla version
		$this->setBootstrapDefault($route);

		// Auto-set FontAwesome to the Joomla-bundled version
		$this->setFontAwesomeDefault($route);

		// Enable Atomic styles on fresh install (don't touch on upgrade)
		$this->setAtomicStylesDefault($route);

		// Show success message with link to template settings
		$this->showSuccessMessage();
	}

	private function showSuccessMessage()
	{
		try {
			$db    = Factory::getDbo();
			$query = $db->getQuery(true)
				->select($db->quoteName('id'))
				->from($db->quoteName('#__template_styles'))
				->where($db->quoteName('template') . ' = ' . $db->quote('atomic'))
				->where($db->quoteName('client_id') . ' = 0')
				->order($db->quoteName('home') . ' DESC');
			$db->setQuery($query, 0, 1);
			$styleId = (int) $db->loadResult();

			if ($styleId) {
				$settingsUrl = Uri::root() . 'administrator/index.php?option=com_templates&view=style&layout=edit&id=' . $styleId;
				Factory::getApplication()->enqueueMessage(
					'Atomic template installed successfully. <a href="' . $settingsUrl . '">Open template settings &rsaquo;</a>',
					'success'
				);
			}
		} catch (\Exception $e) {
			// Non-critical — silently skip
		}
	}

	private function setBetaChannel()
	{
		$gaUrl   = 'https://kontent.github.io/Atomic/update.xml';
		$betaUrl = 'https://kontent.github.io/Atomic/update-beta.xml';

		try {
			$db = Factory::getDbo();

			// Read the betachannel param from template styles
			$query = $db->getQuery(true)
				->select($db->quoteName('params'))
				->from($db->quoteName('#__template_styles'))
				->where($db->quoteName('template') . ' = ' . $db->quote('atomic'))
				->where($db->quoteName('client_id') . ' = 0');
			$db->setQuery($query);
			$paramsJson = $db->loadResult();

			$betaEnabled = 0;

			if ($paramsJson) {
				$registry = new \Joomla\Registry\Registry($paramsJson);
				$betaEnabled = (int) $registry->get('betachannel', 0);
			}

			// Swap the single update site URL between GA and beta
			$targetUrl = $betaEnabled ? $betaUrl : $gaUrl;

			$query = $db->getQuery(true)
				->update($db->quoteName('#__update_sites'))
				->set($db->quoteName('location') . ' = ' . $db->quote($targetUrl))
				->where('(' . $db->quoteName('location') . ' = ' . $db->quote($gaUrl)
					. ' OR ' . $db->quoteName('location') . ' = ' . $db->quote($betaUrl) . ')');
			$db->setQuery($query);
			$db->execute();
		} catch (\Exception $e) {
			// Silently fail — non-critical
		}
	}

	/**
	 * Auto-set the bootstrapsource param to the appropriate value for the
	 * installed Joomla version. On fresh install it always sets the value.
	 * On update it corrects the value only if it is a Joomla-version-specific
	 * option that no longer matches the running Joomla version (e.g. after a
	 * Joomla major-version upgrade). CDN, Custom, and Bootswatch selections
	 * (values 0, 2, 5–14) are never touched on update.
	 */
	private function setBootstrapDefault(string $route): void
	{
		try {
			$major = Version::MAJOR_VERSION;

			// Map Joomla major version → bootstrapsource value (local vendor)
			$map       = [4 => 1, 5 => 3, 6 => 4];
			$bsDefault = $map[$major] ?? 2; // fall back to CDN

			// Values that are Joomla-version-specific (not CDN/Custom/Bootswatch)
			$versionSpecific = [1, 3, 4];

			$db    = Factory::getDbo();
			$query = $db->getQuery(true)
				->select([$db->quoteName('id'), $db->quoteName('params')])
				->from($db->quoteName('#__template_styles'))
				->where($db->quoteName('template') . ' = ' . $db->quote('atomic'))
				->where($db->quoteName('client_id') . ' = 0');
			$db->setQuery($query);
			$styles = $db->loadObjectList();

			foreach ($styles as $style) {
				$registry = new \Joomla\Registry\Registry($style->params);
				$current  = (int) $registry->get('bootstrapsource', -1);

				// Set when: fresh install, first run (no prior value),
				// or current value is a wrong-version local option.
				$wrongVersion = in_array($current, $versionSpecific) && $current !== $bsDefault;

				if (in_array($route, ['install', 'discover_install']) || $current === -1 || $wrongVersion) {
					$registry->set('bootstrapsource', $bsDefault);

					$updateQuery = $db->getQuery(true)
						->update($db->quoteName('#__template_styles'))
						->set($db->quoteName('params') . ' = ' . $db->quote($registry->toString()))
						->where($db->quoteName('id') . ' = ' . (int) $style->id);
					$db->setQuery($updateQuery);
					$db->execute();
				}
			}
		} catch (\Exception $e) {
			// Silently fail — non-critical
		}
	}

	/**
	 * Auto-set the fontawesome param to the Joomla-bundled version.
	 * On fresh install it always sets the value. On update it only
	 * corrects if the value is still the default "None" (0).
	 */
	private function setFontAwesomeDefault(string $route): void
	{
		try {
			$major = Version::MAJOR_VERSION;

			// Map Joomla major version → fontawesome value (local vendor)
			$faDefault = $major >= 5 ? 6 : 1;

			$db    = Factory::getDbo();
			$query = $db->getQuery(true)
				->select([$db->quoteName('id'), $db->quoteName('params')])
				->from($db->quoteName('#__template_styles'))
				->where($db->quoteName('template') . ' = ' . $db->quote('atomic'))
				->where($db->quoteName('client_id') . ' = 0');
			$db->setQuery($query);
			$styles = $db->loadObjectList();

			foreach ($styles as $style) {
				$registry = new \Joomla\Registry\Registry($style->params);
				$current  = (int) $registry->get('fontawesome', -1);

				// Set on fresh install, first run (no value stored), or if still "None"
				if (in_array($route, ['install', 'discover_install']) || $current === -1 || $current === 0) {
					$registry->set('fontawesome', $faDefault);

					$updateQuery = $db->getQuery(true)
						->update($db->quoteName('#__template_styles'))
						->set($db->quoteName('params') . ' = ' . $db->quote($registry->toString()))
						->where($db->quoteName('id') . ' = ' . (int) $style->id);
					$db->setQuery($updateQuery);
					$db->execute();
				}
			}
		} catch (\Exception $e) {
			// Silently fail — non-critical
		}
	}

	/**
	 * Enable Atomic styles on fresh install. On upgrade, leave the
	 * existing value untouched so user preferences are preserved.
	 */
	private function setAtomicStylesDefault(string $route): void
	{
		if (!in_array($route, ['install', 'discover_install'])) {
			return;
		}

		try {
			$db    = Factory::getDbo();
			$query = $db->getQuery(true)
				->select([$db->quoteName('id'), $db->quoteName('params')])
				->from($db->quoteName('#__template_styles'))
				->where($db->quoteName('template') . ' = ' . $db->quote('atomic'))
				->where($db->quoteName('client_id') . ' = 0');
			$db->setQuery($query);
			$styles = $db->loadObjectList();

			foreach ($styles as $style) {
				$registry = new \Joomla\Registry\Registry($style->params);
				$registry->set('atomicstyles', 1);

				$updateQuery = $db->getQuery(true)
					->update($db->quoteName('#__template_styles'))
					->set($db->quoteName('params') . ' = ' . $db->quote($registry->toString()))
					->where($db->quoteName('id') . ' = ' . (int) $style->id);
				$db->setQuery($updateQuery);
				$db->execute();
			}
		} catch (\Exception $e) {
			// Silently fail — non-critical
		}
	}

	private function initFolders()
	{
		$folders = [
			JPATH_ROOT . '/templates/atomic/css',
			JPATH_ROOT . '/templates/atomic/js',
		];

		foreach ($folders as $folder) {
			if (!is_dir($folder)) {
				mkdir($folder, 0755, true);
			}
		}
	}

	private function getFiles($folder, $extraExclude = [])
	{
		if (!is_dir($folder)) {
			return [];
		}

		$exclude = array_merge(['.svn', 'CVS', '.DS_Store', '__MACOSX'], $extraExclude);
		$files   = [];

		foreach (glob($folder . '/*') as $file) {
			if (is_file($file) && !in_array(basename($file), $exclude)) {
				$files[] = $file;
			}
		}

		return $files;
	}

	private function copyFiles($files, $destination)
	{
		if (empty($files)) {
			return false;
		}

		foreach ($files as $source) {
			$target = $destination . '/' . basename($source);
			copy($source, $target);
		}

		return true;
	}

	private function ensureCustomFile($filePath, $type)
	{
		if (!file_exists($filePath)) {
			file_put_contents($filePath, "/* Custom " . $type . " File */\n");
		}
	}
}
