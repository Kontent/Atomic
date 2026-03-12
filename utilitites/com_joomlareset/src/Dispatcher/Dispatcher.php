<?php

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
