<?php
/**
 * @copyright  Copyright (C) 2022 Ron Severdia  All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$module  = $displayData['module'];
$params  = $displayData['params'];
$attribs = $displayData['attribs'];

if ($module->content === null || $module->content === '')
{
	return;
}

$modulePos   = $module->position;
$moduleTag   = $params->get('module_tag', 'div');

?>
<<?php echo $moduleTag; ?> class="<?php echo $modulePos; ?> module <?php echo htmlspecialchars($params->get('moduleclass_sfx'), ENT_QUOTES, 'UTF-8'); ?>">
<?php echo $module->content; ?>
</<?php echo $moduleTag; ?>>