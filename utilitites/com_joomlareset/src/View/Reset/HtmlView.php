<?php
/**
 * @package    Joomla Reset
 * @copyright	 (c) 2026 Ron Severdia. All rights reserved.
 * @license		 GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Severdia\Component\JoomlaReset\Administrator\View\Reset;

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

class HtmlView extends BaseHtmlView
{
	protected $resetInfo;

	public function display($tpl = null)
	{
		/** @var \Severdia\Component\JoomlaReset\Administrator\Model\ResetModel $model */
		$model           = $this->getModel();
		$this->resetInfo = $model->getResetInfo();

		ToolbarHelper::title(Text::_('COM_JOOMLARESET'), 'warning');

		parent::display($tpl);
	}
}
