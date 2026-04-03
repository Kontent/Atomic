<?php
/**
 * @package    Atomic
 * @copyright	 (c) 2009-2026 Ron Severdia. All rights reserved.
 * @license		 GNU General Public License version 2 or later; see LICENSE.txt
 *
 * Custom list field: shows only the FontAwesome option relevant to
 * the installed Joomla major version. CDN and Custom options are always shown.
 *
 * Values:
 *   0  = None
 *   1  = FA from Joomla 4 vendor  (J4 only)
 *   2  = FA 7.0.1 CSS from CDN
 *   3  = FA 7.0.1 JS from CDN
 *   4  = Custom CSS snippet
 *   5  = Custom JS snippet
 *   6  = FA from Joomla 5/6 system CSS  (J5+ only)
 */

defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\ListField;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Version;

class JFormFieldFontawesome extends ListField
{
	public $type = 'Fontawesome';

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

		// Local Joomla FA — only the installed version is shown
		if ($major >= 5) {
			$options[] = $o('TPL_ATOMIC_FAJOOMLA5', 6);  // media/system/css/joomla-fontawesome.min.css
		} else {
			$options[] = $o('TPL_ATOMIC_FAJOOMLA4', 1);  // Joomla 4 vendor package
		}

		// CDN and Custom — always available
		$options[] = $o('TPL_ATOMIC_FACSSCDN', 2);
		$options[] = $o('TPL_ATOMIC_FAJSCDN', 3);
		$options[] = $o('TPL_ATOMIC_FACUSTOMCSS', 4);
		$options[] = $o('TPL_ATOMIC_FACUSTOMJS', 5);

		return $options;
	}
}
