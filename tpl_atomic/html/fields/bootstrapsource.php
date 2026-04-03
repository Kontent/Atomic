<?php
/**
 * @package    Atomic
 * @copyright	 (c) 2009-2026 Ron Severdia. All rights reserved.
 * @license		 GNU General Public License version 2 or later; see LICENSE.txt
 *
 * Custom list field: shows only the Bootstrap source option relevant to
 * the installed Joomla major version. CDN, Custom, and Bootswatch options
 * are always shown regardless of Joomla version.
 */

defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Version;

class JFormFieldBootstrapsource extends ListField
{
	public $type = 'Bootstrapsource';

	protected function getOptions(): array
	{
		$major   = Version::MAJOR_VERSION;
		$options = [];

		$o = static function (string $key, int $value, bool $disabled = false): \stdClass {
			$obj           = new \stdClass;
			$obj->value    = (string) $value;
			$obj->text     = Text::_($key);
			$obj->disable  = $disabled;
			$obj->class    = '';
			$obj->onclick  = '';
			$obj->onchange = '';
			return $obj;
		};

		// None — always available
		$options[] = $o('TPL_ATOMIC_NONE', 0);

		// Local Joomla Bootstrap — only the installed version is shown
		if ($major >= 6) {
			$options[] = $o('TPL_ATOMIC_BS5_JOOMLA6', 4);
		} elseif ($major >= 5) {
			$options[] = $o('TPL_ATOMIC_BS5_JOOMLA5', 3);
		} else {
			$options[] = $o('TPL_ATOMIC_BS5_JOOMLA4', 1);
		}

		// CDN and Custom — always available
		$options[] = $o('TPL_ATOMIC_BS5_REMOTE', 2);
		$options[] = $o('TPL_ATOMIC_BS_CUSTOM', 5);

		// Bootswatch Light themes (disabled separator + themes)
		$options[] = $o('TPL_ATOMIC_BOOTSWATCH_OPTION_LIGHT', 0, true);
		$options[] = $o('TPL_ATOMIC_BOOTSWATCH_OPTION_COSMO', 6);
		$options[] = $o('TPL_ATOMIC_BOOTSWATCH_OPTION_FLATLY', 7);
		$options[] = $o('TPL_ATOMIC_BOOTSWATCH_OPTION_MINTY', 8);
		$options[] = $o('TPL_ATOMIC_BOOTSWATCH_OPTION_SPACELAB', 9);
		$options[] = $o('TPL_ATOMIC_BOOTSWATCH_OPTION_YETI', 10);

		// Bootswatch Dark themes (disabled separator + themes)
		$options[] = $o('TPL_ATOMIC_BOOTSWATCH_OPTION_DARK', 0, true);
		$options[] = $o('TPL_ATOMIC_BOOTSWATCH_OPTION_CYBORG', 11);
		$options[] = $o('TPL_ATOMIC_BOOTSWATCH_OPTION_DARKLY', 12);
		$options[] = $o('TPL_ATOMIC_BOOTSWATCH_OPTION_SLATE', 13);
		$options[] = $o('TPL_ATOMIC_BOOTSWATCH_OPTION_SUPERHERO', 14);

		return $options;
	}
}
