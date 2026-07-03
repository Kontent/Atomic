<?php
/**
 * @package    Joomla Reset
 * @copyright	 (c) 2026 Ron Severdia. All rights reserved.
 * @license		 GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Severdia\Component\JoomlaReset\Administrator\Dispatcher;

defined('_JEXEC') or die;

use Joomla\CMS\Access\Exception\NotAllowed;
use Joomla\CMS\Dispatcher\ComponentDispatcher;
use Joomla\CMS\Factory;

class Dispatcher extends ComponentDispatcher
{
	protected function checkAccess()
	{
		$user = Factory::getApplication()->getIdentity();

		if (!$user || !$user->authorise('core.admin')) {
			throw new NotAllowed($this->app->getLanguage()->_('JERROR_ALERTNOAUTHOR'), 403);
		}
	}
}
