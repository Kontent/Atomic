<?php

/**
 * @copyright   Copyright (C) 2005 - 2025 Ron Severdia All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;

$module = $displayData['module'];
$params  = $displayData['params'];

$moduleAttribs          = [];
$moduleAttribs['class'] = 'col' . htmlspecialchars($params->get('moduleclass_sfx', ''), ENT_QUOTES, 'UTF-8');

?>

<div <?php echo ArrayHelper::toString($moduleAttribs); ?>>
	<?php echo $module->content; ?>
</div>
