<?php
defined('_JEXEC') or die;

use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Installer\Adapter\TemplateAdapter;

class AtomicInstallerScript
{	
	public function postflight($route, TemplateAdapter $adapter)
	{
		$parent = $adapter->getParent();
		$source = $parent->getPath('source');

		// Initialize folders
		$this->initFolders();

		// Copy over CSS files
		$cssFiles = $this->getCssFiles($route, $source);
		$this->copyFiles($cssFiles, JPATH_ROOT . '/templates/atomic/css');

		// Ensure custom.css exists
		$this->ensureCustomFile(JPATH_ROOT . '/templates/atomic/css/custom.css', 'CSS');

		// Copy over JS files
		$jsFiles = $this->getJsFiles($route, $source);
		$this->copyFiles($jsFiles, JPATH_ROOT . '/templates/atomic/js');

		// Ensure custom.js exists
		$this->ensureCustomFile(JPATH_ROOT . '/templates/atomic/js/custom.js', 'JS');
	}

	private function initFolders()
	{
		$folders = [
			JPATH_ROOT . '/templates/atomic/css',
			JPATH_ROOT . '/templates/atomic/js'
		];

		foreach ($folders as $folder) {
			if (!Folder::exists($folder)) {
				Folder::create($folder);
			}			
		}
	}

	private function copyFiles($files, $destination)
	{
		if (!$files) {
			return false;
		}

		foreach ($files as $source) {
			$target = $destination . '/' . basename($source);

			// Perform the copy
			File::copy($source, $target);
		}

		return true;
	}

	private function ensureCustomFile($filePath, $type)
	{
		if (!File::exists($filePath)) {
			// Create an empty file or with default content
			File::write($filePath, "/* Custom ".$type." File */\n");
		}
	}

	private function getCssFiles($route, $source)
	{
		$exclusion = ['.svn', 'CVS', '.DS_Store', '__MACOSX'];
		$cssFolder = $source . '/css';
		$files = Folder::files($cssFolder, '.', false, true, $exclusion);

		return $files;
	}

	private function getJsFiles($route, $source)
	{
		$exclusion = ['.svn', 'CVS', '.DS_Store', '__MACOSX'];
		$jsFolder = $source . '/js';
		$files = Folder::files($jsFolder, '.', false, true, $exclusion);

		return $files;	
	}
}
