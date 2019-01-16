<?php
/**
 * @package		Joomla.Site
 * @copyright	Copyright (C) 2005 - 2019 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * This is a file to add template specific chrome to module rendering.  To use it you would
 * set the style attribute for the given module(s) include in your template to use the style
 * for each given modChrome function.
 *
 * eg.  To render a module mod_test in the sliders style, you would use the following include:
 * <jdoc:include type="module" name="test" style="slider" />
 *
 * This gives template designers ultimate control over how modules are rendered.
 *
 * NOTICE: All chrome wrapping methods should be named: modChrome_{STYLE} and take the same
 * two arguments.
 *
 * This module chrome file creates custom output for modules used with the Atomic template.
 * The first function wraps modules using the "container" style in a DIV. The second function
 * uses the "bottommodule" style to change the header on the bottom modules to H6. The third
 * function uses the "sidebar" style to change the header on the sidebar to H3.
 */

function modChrome_default($module, &$params, &$attribs)
{
	if (!empty ($module->content)) : ?>
		<?php if ($module->showtitle) : ?>
			<h6><?php echo $module->title; ?></h6>
		<?php endif; ?>
		<?php echo $module->content; ?>
	<?php endif;
}

function modChrome_container($module, &$params, &$attribs)
{
	if (!empty ($module->content)) : ?>
		<div class="container">
			<?php echo $module->content; ?>
		</div>
	<?php endif;
}
function modChrome_bottommodule($module, &$params, &$attribs)
{
	if (!empty ($module->content)) : ?>
		<?php if ($module->showtitle) : ?>
			<h6><?php echo $module->title; ?></h6>
		<?php endif; ?>
		<?php echo $module->content; ?>
	<?php endif;
}
function modChrome_sidebar($module, &$params, &$attribs)
{
	if (!empty ($module->content)) : ?>
		<?php if ($module->showtitle) : ?>
			<h3><?php echo $module->title; ?></h3>
		<?php endif; ?>
		<?php echo $module->content; ?>
	<?php endif;
}
function modChrome_no($module, &$params, &$attribs)
{
	if ($module->content)
	{
		echo $module->content;
	}
}

function modChrome_well($module, &$params, &$attribs)
{
	$moduleTag     = $params->get('module_tag', 'div');
	$bootstrapSize = (int) $params->get('bootstrap_size', 0);
	$moduleClass   = $bootstrapSize != 0 ? ' span' . $bootstrapSize : '';
	$headerTag     = htmlspecialchars($params->get('header_tag', 'h3'), ENT_COMPAT, 'UTF-8');
	$headerClass   = htmlspecialchars($params->get('header_class', 'page-header'), ENT_COMPAT, 'UTF-8');

	if ($module->content)
	{
		echo '<' . $moduleTag . ' class="well ' . htmlspecialchars($params->get('moduleclass_sfx'), ENT_COMPAT, 'UTF-8') . $moduleClass . '">';

			if ($module->showtitle)
			{
				echo '<' . $headerTag . ' class="' . $headerClass . '">' . $module->title . '</' . $headerTag . '>';
			}

			echo $module->content;
		echo '</' . $moduleTag . '>';
	}
}
