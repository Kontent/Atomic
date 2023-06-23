<?php
/**
 * @copyright	Copyright (C) 2020 Ron Severdia. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


class AtomicInstallerScript
{	
	/**
	 * Called after any type of action
	 *
	 * @param   string  $route  Which action is happening (install|uninstall|discover_install|update)
	 * @param   JAdapterInstance  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function postflight($route, JAdapterInstance $adapter)
	{
		$parent = $adapter->getParent();
		$source = $parent->getPath('source');

		// Initialize folders
		$this->initFolders();

		// Only copy over needed css files
		$cssFiles = $this->getCssFiles($route, $source);
		$this->copyFiles($cssFiles, JPATH_ROOT . '/templates/atomic/css');

		// Only copy over needed js files
		$jsFiles = $this->getJsFiles($route, $source);
		$this->copyFiles($jsFiles, JPATH_ROOT . '/templates/atomic/js');
	}

	/**
	 * Initializes necessary folders if they are not created yet
	 *
	 * @access	private
	 */
	private function initFolders()
	{
		$folders = [
			JPATH_ROOT . '/templates/atomic/css',
			JPATH_ROOT . '/templates/atomic/js'
		];

		foreach ($folders as $folder) {
			if (!JFolder::exists($folder)) {
				JFolder::create($folder);
			}			
		}
	}

	/**
	 * Responsible to copy the files across from the source to the destination template folder
	 *
	 * @access	private
	 */
	private function copyFiles($files, $destination)
	{
		if (!$files) {
			return false;
		}

		foreach ($files as $source) {
			$target = $destination . '/' . basename($source);

			JFile::copy($source, $target);
		}

		return true;
	}

	/**
	 * Responsible to determine which css files should be copied over
	 *
	 * @access	private
	 */
	private function getCssFiles($route, $source)
	{
		$exclusion = ['.svn', 'CVS', '.DS_Store', '__MACOSX'];

		if ($route == 'update') {
			$exclusion[] = 'template.css';
		}

		$cssFolder = $source . '/css';
		$files = JFolder::files($cssFolder, '.', false, true, $exclusion);

		return $files;
	}

	/**
	 * Responsible to determine which js files should be copied over
	 *
	 * @access	private
	 */
	private function getJsFiles($route, $source)
	{
		$exclusion = ['.svn', 'CVS', '.DS_Store', '__MACOSX'];

		if ($route == 'update') {
			$exclusion[] = 'template.js';
		}

		$jsFolder = $source . '/js';
		$files = JFolder::files($jsFolder, '.', false, true, $exclusion);

		return $files;	
	}
}