<?php
/**
 * @copyright   Copyright (C) 2005 - 2020 Ron Severdia All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\Utilities\ArrayHelper;

$module  = $displayData['module'];
$params  = $displayData['params'];
$attribs = $displayData['attribs'];

if ($module->content === null || $module->content === '')
{
	return;
}

$modulePos   = $module->position;
$moduleTag   = $params->get('module_tag', 'div');

$moduleAttribs          = [];
$moduleAttribs['class'] = $module->position . 'module' . htmlspecialchars($params->get('moduleclass_sfx', ''), ENT_QUOTES, 'UTF-8');

$headerTag   = htmlspecialchars($params->get('header_tag', 'h4'), ENT_QUOTES, 'UTF-8');
$headerClass = htmlspecialchars($params->get('header_class', ''), ENT_QUOTES, 'UTF-8');

?>
<<?php echo $moduleTag; ?> <?php echo ArrayHelper::toString($moduleAttribs); ?> id="mobilemenu">
	<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarToggleExternalContent" aria-controls="navbarToggleExternalContent" aria-expanded="false" aria-label="Toggle navigation"><i class="fa-solid fa-bars"></i></button>
	<div class="collapse" id="navbarToggleExternalContent">
		<?php if ($module->showtitle && $headerClass === 'card-title') : ?>
			<<?php echo $headerTag; ?> class="<?php echo $headerClass; ?>"><?php echo $module->title; ?></<?php echo $headerTag; ?>>
		<?php endif; ?>
		<div class="contents"><?php echo $module->content; ?></div>
	</div>
</<?php echo $moduleTag; ?>>