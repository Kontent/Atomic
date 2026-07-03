<?php
/**
 * @package    Joomla Reset
 * @copyright	 (c) 2026 Ron Severdia. All rights reserved.
 * @license		 GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Severdia\Component\JoomlaReset\Administrator\Controller;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\Utilities\IpHelper;

class ResetController extends BaseController
{
	/**
	 * The literal confirmation word the user must type (language-independent).
	 */
	private const CONFIRM_WORD = 'RESET';

	/**
	 * Whether the audit-log logger has been registered in this request.
	 *
	 * @var boolean
	 */
	private static $auditLoggerRegistered = false;

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

		// Typed confirmation: the literal word must match exactly.
		if (trim($this->input->getString('confirm_text', '')) !== self::CONFIRM_WORD) {
			$this->setRedirect(
				Route::_('index.php?option=com_joomlareset', false),
				Text::_('COM_JOOMLARESET_ERROR_TYPED_CONFIRM'),
				'error'
			);

			$this->redirect();

			return;
		}

		$app      = Factory::getApplication();
		// IpHelper respects Joomla's behind-proxy setting (REMOTE_ADDR alone records the proxy)
		$clientIp = IpHelper::getIp();
		$actor    = sprintf('user #%d (%s) from IP %s', (int) $user->id, $user->username, $clientIp);

		$this->audit('Database reset attempt by ' . $actor);

		/** @var \Severdia\Component\JoomlaReset\Administrator\Model\ResetModel $model */
		$model  = $this->getModel('Reset', 'Administrator');
		$result = $model->resetDatabase();

		if ($result['success']) {
			$this->audit('Database reset SUCCEEDED for ' . $actor);

			$this->setRedirect(
				Route::_('index.php?option=com_joomlareset', false),
				Text::_('COM_JOOMLARESET_SUCCESS'),
				'success'
			);
		} else {
			$this->audit('Database reset FAILED for ' . $actor . ': ' . $result['error']);

			$this->setRedirect(
				Route::_('index.php?option=com_joomlareset', false),
				Text::sprintf('COM_JOOMLARESET_ERROR_FAILED', $result['error']),
				'error'
			);
		}

		$this->redirect();
	}

	/**
	 * Write an audit entry to the component log file. Registers the logger
	 * on first use; a logging failure (e.g. unwritable log directory) must
	 * never block or fail the reset itself.
	 */
	private function audit(string $message): void
	{
		try {
			if (!self::$auditLoggerRegistered) {
				Log::addLogger(
					['text_file' => 'com_joomlareset.log.php'],
					Log::ALL,
					['com_joomlareset']
				);

				self::$auditLoggerRegistered = true;
			}

			Log::add($message, Log::INFO, 'com_joomlareset');
		} catch (\Throwable $e) {
			// Never let audit logging break the request.
		}
	}
}
