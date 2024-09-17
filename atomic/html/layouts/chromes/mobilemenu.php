<?php
/**
 * @copyright   Copyright (C) 2005 - 2020 Ron Severdia All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
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

$app = JFactory::getApplication();
$template = $app->getTemplate(true);

$sidebarmenu = $template->params->get('sidebarmenu');
?>
<<?php echo $moduleTag; ?> <?php echo ArrayHelper::toString($moduleAttribs); ?> id="mobilemenu<?php echo $module->id; ?>">
	<button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#navbarToggleExternalContent<?php echo $module->id; ?>" aria-controls="navbarToggleExternalContent<?php echo $module->id; ?>" aria-expanded="false" aria-label="Toggle navigation"><i class="fas fa-bars"></i></button>
	<div class="offcanvas offcanvas-start" data-bs-backdrop="true" data-bs-scroll="false" tabindex="-1" id="navbarToggleExternalContent<?php echo $module->id; ?>">
		
		<?php if($sidebarmenu === '1') : ?>
			<button class="btn btn-primary offcanvas-toggle" type="button" data-bs-toggle="offcanvas" data-bs-target="#navbarToggleExternalContent<?php echo $module->id; ?>">
				<span class="offcanvas-toggle-icon offcanvas-toggle-icon--close"><i class="fas fa-times"></i></span>
				<span class="offcanvas-toggle-icon offcanvas-toggle-icon--open"><i class="fas fa-bars"></i></span>
			</button>
		<?php else: ?>
			<button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
		<?php endif; ?>
		<div class="offcanvas-content d-flex flex-column">
			<div class="offcanvas-body">
				<?php if ($module->showtitle && $headerClass === 'card-title') : ?>
					<<?php echo $headerTag; ?> class="<?php echo $headerClass; ?>"><?php echo $module->title; ?></<?php echo $headerTag; ?>>
				<?php endif; ?>
				<div class="contents"><?php echo $module->content; ?></div>
			</div>
		</div>
	</div>
</<?php echo $moduleTag; ?>>