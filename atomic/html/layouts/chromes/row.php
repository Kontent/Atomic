<?php

/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   (C) 2019 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;

$module = $displayData['module'];
$params  = $displayData['params'];

$moduleAttribs          = [];
$moduleAttribs['class'] = 'row ' . htmlspecialchars($params->get('moduleclass_sfx', ''), ENT_QUOTES, 'UTF-8');


?>

<div <?php echo ArrayHelper::toString($moduleAttribs); ?>>
	<?php echo $module->content; ?>
</div>
