<?php

/**
 * @package    Atomic
 * @copyright	 (c) 2009-2026 Ron Severdia. All rights reserved.
 * @license		 GNU General Public License version 2 or later; see LICENSE.txt
 *
 * Atomic Cards — alternative category blog layout that renders leading and
 * intro articles as a responsive grid of Bootstrap cards. Select it as the
 * menu item type "Category Blog (Atomic Cards)".
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;

$htag = $this->params->get('show_page_heading') ? 'h2' : 'h1';

// Responsive grid classes for the intro article cards, honoring the columns menu setting
$columns     = max(1, (int) $this->params->get('num_columns', 1));
$gridClasses = 'row g-4 row-cols-1';

if ($columns > 1) {
    $gridClasses .= ' row-cols-md-2';
}

if ($columns > 2) {
    $gridClasses .= ' row-cols-lg-' . min($columns, 4);
}
?>
<div class="com-content-category-blog blog atomic-cards" itemscope itemtype="https://schema.org/Blog">
    <?php if ($this->params->get('show_page_heading')) : ?>
        <h1>
            <?php echo $this->escape($this->params->get('page_heading')); ?>
        </h1>
    <?php endif; ?>

    <?php if ($this->params->get('show_category_title', 1)) : ?>
        <<?php echo $htag; ?> class="atomic-cards-title">
            <?php echo $this->escape($this->category->title); ?>
        </<?php echo $htag; ?>>
    <?php endif; ?>

    <?php if ($this->params->get('show_description', 1) || $this->params->def('show_description_image', 1)) : ?>
        <div class="com-content-category-blog__description category-desc clearfix">
            <?php if ($this->params->get('show_description_image') && $this->category->getParams()->get('image')) : ?>
                <?php $catImage = HTMLHelper::cleanImageURL($this->category->getParams()->get('image')); ?>
                <img src="<?php echo $this->escape($catImage->url); ?>" alt="<?php echo $this->escape($this->category->getParams()->get('image_alt', '')); ?>" class="img-fluid">
            <?php endif; ?>
            <?php if ($this->params->get('show_description') && $this->category->description) : ?>
                <?php echo HTMLHelper::_('content.prepare', $this->category->description, '', 'com_content.category'); ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if (empty($this->lead_items) && empty($this->intro_items) && empty($this->link_items)) : ?>
        <?php if ($this->params->get('show_no_articles', 1)) : ?>
            <div class="alert alert-info">
                <span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
                <?php echo Text::_('COM_CONTENT_NO_ARTICLES'); ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if (!empty($this->lead_items)) : ?>
        <div class="com-content-category-blog__items blog-items items-leading atomic-cards-leading row g-4 row-cols-1">
            <?php foreach ($this->lead_items as &$item) : ?>
                <div class="com-content-category-blog__item blog-item col" itemprop="blogPost" itemscope itemtype="https://schema.org/BlogPosting">
                    <?php
                    $this->item = &$item;
                    echo $this->loadTemplate('item');
                    ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($this->intro_items)) : ?>
        <div class="com-content-category-blog__items blog-items atomic-cards-grid <?php echo $gridClasses; ?><?php echo !empty($this->lead_items) ? ' mt-0 pt-4' : ''; ?>">
            <?php foreach ($this->intro_items as &$item) : ?>
                <div class="com-content-category-blog__item blog-item col" itemprop="blogPost" itemscope itemtype="https://schema.org/BlogPosting">
                    <?php
                    $this->item = &$item;
                    echo $this->loadTemplate('item');
                    ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($this->link_items)) : ?>
        <div class="com-content-category-blog__navigation blog-links">
            <?php echo $this->loadTemplate('links'); ?>
        </div>
    <?php endif; ?>

    <?php if ($this->maxLevel != 0 && !empty($this->children[$this->category->id])) : ?>
        <div class="com-content-category-blog__children atomic-cards-children">
            <h3>
                <?php echo Text::_('JGLOBAL_SUBCATEGORIES'); ?>
            </h3>
            <ul class="atomic-cards-subcategories list-unstyled">
                <?php foreach ($this->children[$this->category->id] as $child) : ?>
                    <?php if ($this->params->get('show_empty_categories') || $child->getNumItems(true) || count($child->getChildren())) : ?>
                        <li>
                            <a href="<?php echo Route::_(RouteHelper::getCategoryRoute($child->id, $child->language)); ?>">
                                <?php echo $this->escape($child->title); ?>
                            </a>
                            <?php if ($this->params->get('show_cat_num_articles', 0)) : ?>
                                <span class="badge bg-secondary"><?php echo (int) $child->getNumItems(true); ?></span>
                            <?php endif; ?>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (($this->params->def('show_pagination', 1) == 1 || $this->params->get('show_pagination') == 2) && $this->pagination->pagesTotal > 1) : ?>
        <div class="com-content-category-blog__pagination w-100">
            <?php if ($this->params->def('show_pagination_results', 1)) : ?>
                <p class="counter float-end pt-3 pe-2">
                    <?php echo $this->pagination->getPagesCounter(); ?>
                </p>
            <?php endif; ?>
            <?php echo $this->pagination->getPagesLinks(); ?>
        </div>
    <?php endif; ?>
</div>
