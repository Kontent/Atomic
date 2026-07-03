<?php

/**
 * @package    Atomic
 * @copyright	 (c) 2009-2026 Ron Severdia. All rights reserved.
 * @license		 GNU General Public License version 2 or later; see LICENSE.txt
 *
 * Atomic Cards — single article card for the featured articles layout.
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Component\Content\Site\Helper\RouteHelper;

$params = $this->item->params;
$images = json_decode($this->item->images ?? '{}');
$info   = $params->get('info_block_position', 0);

$link = Route::_(RouteHelper::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language));

$useDefList = $params->get('show_modify_date') || $params->get('show_publish_date') || $params->get('show_create_date')
    || $params->get('show_hits') || $params->get('show_category') || $params->get('show_parent_category') || $params->get('show_author');
?>
<div class="card h-100 atomic-card">
    <?php if (!empty($images->image_intro)) : ?>
        <?php $img = HTMLHelper::cleanImageURL($images->image_intro); ?>
        <?php $alt = empty($images->image_intro_alt) && empty($images->image_intro_alt_empty)
            ? ''
            : ' alt="' . $this->escape($images->image_intro_alt ?? '') . '"'; ?>
        <?php if ($params->get('link_titles') && $params->get('access-view')) : ?>
            <a href="<?php echo $link; ?>" class="atomic-card-img-link" aria-hidden="true" tabindex="-1">
                <img class="card-img-top atomic-card-img" src="<?php echo $this->escape($img->url); ?>"<?php echo $alt; ?> itemprop="thumbnailUrl" loading="lazy">
            </a>
        <?php else : ?>
            <img class="card-img-top atomic-card-img" src="<?php echo $this->escape($img->url); ?>"<?php echo $alt; ?> itemprop="thumbnailUrl" loading="lazy">
        <?php endif; ?>
    <?php endif; ?>
    <div class="card-body d-flex flex-column">
        <?php if ($this->item->state == 0) : ?>
            <div class="atomic-card-state">
                <span class="badge bg-warning text-dark"><?php echo Text::_('JUNPUBLISHED'); ?></span>
            </div>
        <?php endif; ?>

        <?php if ($params->get('show_title', 1)) : ?>
            <h2 class="card-title h5" itemprop="headline">
                <?php if ($params->get('link_titles') && $params->get('access-view')) : ?>
                    <a href="<?php echo $link; ?>" itemprop="url">
                        <?php echo $this->escape($this->item->title); ?>
                    </a>
                <?php else : ?>
                    <?php echo $this->escape($this->item->title); ?>
                <?php endif; ?>
            </h2>
        <?php endif; ?>

        <?php echo $this->item->event->afterDisplayTitle ?? ''; ?>

        <?php if ($useDefList && ($info == 0 || $info == 2)) : ?>
            <div class="atomic-card-info">
                <?php echo LayoutHelper::render('joomla.content.info_block', ['item' => $this->item, 'params' => $params, 'position' => 'above']); ?>
            </div>
        <?php endif; ?>

        <?php echo $this->item->event->beforeDisplayContent ?? ''; ?>

        <div class="atomic-card-intro" itemprop="articleBody">
            <?php echo HTMLHelper::_('string.truncateComplex', $this->item->introtext, 260); ?>
        </div>

        <?php if ($useDefList && ($info == 1 || $info == 2)) : ?>
            <div class="atomic-card-info">
                <?php echo LayoutHelper::render('joomla.content.info_block', ['item' => $this->item, 'params' => $params, 'position' => 'below']); ?>
            </div>
        <?php endif; ?>

        <?php if ($params->get('show_tags', 1) && !empty($this->item->tags->itemTags)) : ?>
            <?php echo LayoutHelper::render('joomla.content.tags', $this->item->tags->itemTags); ?>
        <?php endif; ?>

        <?php if ($params->get('show_readmore') && !empty($this->item->readmore)) : ?>
            <?php
            if ($params->get('access-view')) {
                $readmoreLink = $link;
            } else {
                $active       = Factory::getApplication()->getMenu()->getActive();
                $loginLink    = new Uri(Route::_('index.php?option=com_users&view=login&Itemid=' . ($active ? $active->id : 0), false));
                $loginLink->setVar('return', base64_encode(RouteHelper::getArticleRoute($this->item->slug, $this->item->catid, $this->item->language)));
                $readmoreLink = (string) $loginLink;
            }
            ?>
            <div class="atomic-card-readmore mt-auto pt-2">
                <?php echo LayoutHelper::render('joomla.content.readmore', ['item' => $this->item, 'params' => $params, 'link' => $readmoreLink]); ?>
            </div>
        <?php endif; ?>

        <?php echo $this->item->event->afterDisplayContent ?? ''; ?>
    </div>
</div>
