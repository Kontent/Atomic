<?php
/**
 * @package    Atomic
 * @copyright	 (c) 2009-2026 Ron Severdia. All rights reserved.
 * @license		 GNU General Public License version 2 or later; see LICENSE.txt
 *
 * Custom radio field for the beta update channel toggle.
 * Swaps the single Atomic update site URL between the stable and beta
 * XML feeds whenever the template settings page is loaded. Since Joomla
 * reloads the page on save, the swap takes effect immediately.
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Field\RadioField;

class JFormFieldBetachannel extends RadioField
{
	private const GA_URL   = 'https://kontent.github.io/Atomic/update.xml';
	private const BETA_URL = 'https://kontent.github.io/Atomic/update-beta.xml';

	public $type = 'Betachannel';

	protected function getInput(): string
	{
		$this->syncUpdateSite();

		return parent::getInput();
	}

	/**
	 * Swap the update site URL to match the current betachannel param.
	 */
	private function syncUpdateSite(): void
	{
		try {
			$db = Factory::getDbo();

			// Read the current betachannel value from the template style
			$query = $db->getQuery(true)
				->select($db->quoteName('params'))
				->from($db->quoteName('#__template_styles'))
				->where($db->quoteName('template') . ' = ' . $db->quote('atomic'))
				->where($db->quoteName('client_id') . ' = 0')
				->order($db->quoteName('home') . ' DESC');
			$db->setQuery($query, 0, 1);
			$paramsJson = $db->loadResult();

			$betaEnabled = 0;

			if ($paramsJson) {
				$registry    = new \Joomla\Registry\Registry($paramsJson);
				$betaEnabled = (int) $registry->get('betachannel', 0);
			}

			$currentUrl = $betaEnabled ? self::BETA_URL : self::GA_URL;
			$oldUrl     = $betaEnabled ? self::GA_URL   : self::BETA_URL;

			// Update the existing Atomic update site row to point to the correct URL
			$query = $db->getQuery(true)
				->update($db->quoteName('#__update_sites'))
				->set($db->quoteName('location') . ' = ' . $db->quote($currentUrl))
				->where('(' . $db->quoteName('location') . ' = ' . $db->quote(self::GA_URL)
					. ' OR ' . $db->quoteName('location') . ' = ' . $db->quote(self::BETA_URL) . ')');
			$db->setQuery($query);
			$db->execute();
		} catch (\Exception $e) {
			// Silently fail — non-critical
		}
	}
}
