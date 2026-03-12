<?php

namespace Severdia\Component\JoomlaReset\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

class ResetController extends BaseController
{
	public function execute($task = null): void
	{
		if (!Session::checkToken('post')) {
			$this->setRedirect(
				Route::_('index.php?option=com_joomlareset', false),
				Text::_('JINVALID_TOKEN'),
				'error'
			);

			$this->redirect();

			return;
		}

		$user = Factory::getApplication()->getIdentity();

		if (!$user || !$user->authorise('core.admin')) {
			$this->setRedirect(
				Route::_('index.php?option=com_joomlareset', false),
				Text::_('JERROR_ALERTNOAUTHOR'),
				'error'
			);

			$this->redirect();

			return;
		}

		$confirm = $this->input->getInt('confirm_reset', 0);

		if (!$confirm) {
			$this->setRedirect(
				Route::_('index.php?option=com_joomlareset', false),
				Text::_('COM_JOOMLARESET_ERROR_NOT_CONFIRMED'),
				'error'
			);

			$this->redirect();

			return;
		}

		/** @var \Severdia\Component\JoomlaReset\Administrator\Model\ResetModel $model */
		$model  = $this->getModel('Reset', 'Administrator');
		$result = $model->resetDatabase();

		if ($result['success']) {
			$this->setRedirect(
				Route::_('index.php?option=com_joomlareset', false),
				Text::_('COM_JOOMLARESET_SUCCESS'),
				'success'
			);
		} else {
			$this->setRedirect(
				Route::_('index.php?option=com_joomlareset', false),
				Text::sprintf('COM_JOOMLARESET_ERROR_FAILED', $result['error']),
				'error'
			);
		}

		$this->redirect();
	}
}
