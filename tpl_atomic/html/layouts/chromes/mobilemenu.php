<?php
/**
 * Mobile menu chrome — offcanvas panel only.
 * This chrome now renders only the offcanvas panel (identical to the
 * "mobilemenupanel" chrome). The trigger button is hardcoded in index.php
 * inside the header. Rendering the panel outside the header avoids
 * stacking-context issues caused by backdrop-filter on the header.
 *
 * @copyright   Copyright (C) 2005 - 2026 Ron Severdia All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$module  = $displayData['module'];
$params  = $displayData['params'];

if ($module->content === null || $module->content === '')
{
	return;
}

$headerTag   = htmlspecialchars($params->get('header_tag', 'h4'), ENT_QUOTES, 'UTF-8');
$headerClass = htmlspecialchars($params->get('header_class', ''), ENT_QUOTES, 'UTF-8');
?>
<div class="offcanvas mobilemenu-offcanvas offcanvas-start" data-bs-backdrop="true" data-bs-scroll="false" tabindex="-1" id="mobilemenuOffcanvas" aria-labelledby="mobilemenuLabel">
	<div class="offcanvas-header">
		<?php if ($module->showtitle) : ?>
			<<?php echo $headerTag; ?> class="offcanvas-title <?php echo $headerClass; ?>" id="mobilemenuLabel"><?php echo $module->title; ?></<?php echo $headerTag; ?>>
		<?php endif; ?>
		<button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
	</div>
	<div class="offcanvas-body">
		<div class="contents"><?php echo $module->content; ?></div>
	</div>
</div>
