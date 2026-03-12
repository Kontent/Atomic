<?php

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Table\Menu;

class Com_JoomlaresetInstallerScript
{
	/**
	 * Runs before installation. Repairs the admin menu nested set tree
	 * so that Joomla's installer can successfully add the menu item.
	 */
	public function preflight(string $type, InstallerAdapter $adapter): bool
	{
		if ($type === 'install' || $type === 'update') {
			$this->repairMenuTree();
		}

		return true;
	}

	/**
	 * Rebuild the nested set lft/rgt values for the #__menu table.
	 */
	private function repairMenuTree(): void
	{
		try {
			$db    = Factory::getContainer()->get('Joomla\Database\DatabaseInterface');
			$table = new Menu($db);
			$table->rebuild();
		} catch (\Exception $e) {
			// Don't block installation if rebuild fails
		}
	}
}
