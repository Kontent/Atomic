<?php
/**
 * @package    Atomic
 * @copyright	 (c) 2009-2026 Ron Severdia. All rights reserved.
 * @license		 GNU General Public License version 2 or later; see LICENSE.txt
 *
 * Mobile menu chrome — inner module renderer (alias of "mobilemenu").
 *
 * Kept as an alias so existing module assignments using
 * style="mobilemenupanel" continue to work.
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
<div class="mobilemenu-module">
	<?php if ($module->showtitle) : ?>
		<<?php echo $headerTag; ?> class="mobilemenu-module-title <?php echo $headerClass; ?>"><?php echo $module->title; ?></<?php echo $headerTag; ?>>
	<?php endif; ?>
	<div class="contents"><?php echo $module->content; ?></div>
</div>
