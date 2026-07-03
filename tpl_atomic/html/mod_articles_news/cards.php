<?php

/**
 * @package    Atomic
 * @copyright	 (c) 2009-2026 Ron Severdia. All rights reserved.
 * @license		 GNU General Public License version 2 or later; see LICENSE.txt
 *
 * Atomic Cards — renders the Articles - Newsflash module (Joomla 4) as a
 * responsive grid of Bootstrap cards. Select it under Advanced > Layout.
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;

// Grouping modes nest items one level deep — flatten so every mode renders
$items = [];

foreach ((array) $list as $entry) {
    if (is_array($entry)) {
        $items = array_merge($items, array_values($entry));
    } else {
        $items[] = $entry;
    }
}

if (empty($items)) {
    return;
}
?>
<div class="mod-articlesnews newsflash atomic-cards atomic-cards-module">
    <div class="row g-4 row-cols-1 row-cols-md-3">
        <?php foreach ($items as $item) : ?>
            <?php
            // Newsflash items are close to raw article rows — guard every
            // property so a card with missing data degrades cleanly.
            $title = htmlspecialchars((string) ($item->title ?? ''), ENT_QUOTES, 'UTF-8');
            $link  = '';

            if (!empty($item->link)) {
                $link = (string) $item->link;
            } elseif (!empty($item->catid) && (isset($item->slug) || isset($item->id))) {
                $link = Route::_(RouteHelper::getArticleRoute($item->slug ?? $item->id, $item->catid, $item->language ?? ''));
            }

            // double_encode=false: Route::_() links are already &amp;-encoded
            $link   = htmlspecialchars($link, ENT_QUOTES, 'UTF-8', false);
            $images = json_decode($item->images ?? '');
            $imgUrl = '';
            $imgAlt = '';

            if (!empty($images->image_intro)) {
                $img    = HTMLHelper::cleanImageURL($images->image_intro);
                $imgUrl = htmlspecialchars((string) $img->url, ENT_QUOTES, 'UTF-8');
                $imgAlt = htmlspecialchars((string) ($images->image_intro_alt ?? ''), ENT_QUOTES, 'UTF-8');
            }

            $date = '';

            if (property_exists($item, 'displayDate')) {
                $date = (string) ($item->displayDate ?? '');
            } elseif (!empty($item->publish_up)) {
                $date = (string) $item->publish_up;
            }

            $intro    = (string) ($item->displayIntrotext ?? $item->introtext ?? '');
            $readmore = htmlspecialchars((string) ($item->linkText ?? ''), ENT_QUOTES, 'UTF-8');
            ?>
            <div class="col">
                <div class="card h-100 atomic-card">
                    <?php if ($imgUrl !== '') : ?>
                        <?php if ($link !== '') : ?>
                            <a href="<?php echo $link; ?>" class="atomic-card-img-link" aria-hidden="true" tabindex="-1">
                                <img class="card-img-top atomic-card-img" src="<?php echo $imgUrl; ?>" alt="<?php echo $imgAlt; ?>" loading="lazy">
                            </a>
                        <?php else : ?>
                            <img class="card-img-top atomic-card-img" src="<?php echo $imgUrl; ?>" alt="<?php echo $imgAlt; ?>" loading="lazy">
                        <?php endif; ?>
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <h3 class="card-title h5">
                            <?php if ($link !== '' && $params->get('link_titles', 1)) : ?>
                                <a href="<?php echo $link; ?>"><?php echo $title; ?></a>
                            <?php else : ?>
                                <?php echo $title; ?>
                            <?php endif; ?>
                        </h3>

                        <?php if ($date !== '') : ?>
                            <div class="atomic-card-info">
                                <small class="atomic-card-date"><?php echo HTMLHelper::_('date', $date, Text::_('DATE_FORMAT_LC3')); ?></small>
                            </div>
                        <?php endif; ?>

                        <?php if ($intro !== '') : ?>
                            <div class="atomic-card-intro">
                                <?php echo HTMLHelper::_('string.truncateComplex', $intro, 200); ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($link !== '' && $params->get('readmore')) : ?>
                            <div class="atomic-card-readmore mt-auto pt-2">
                                <a class="btn btn-secondary btn-sm readmore" href="<?php echo $link; ?>">
                                    <?php echo $readmore !== '' ? $readmore : Text::_('TPL_ATOMIC_READ_MORE'); ?><span class="visually-hidden">: <?php echo $title; ?></span>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
