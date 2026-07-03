<?php

/**
 * @package    Atomic
 * @copyright	 (c) 2009-2026 Ron Severdia. All rights reserved.
 * @license		 GNU General Public License version 2 or later; see LICENSE.txt
 *
 * Atomic Cards — renders the unified Articles module (Joomla 5.1+/6) as a
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
<div class="mod-articles atomic-cards atomic-cards-module">
    <div class="row g-4 row-cols-1 row-cols-md-3">
        <?php foreach ($items as $item) : ?>
            <?php
            // Items are helper-processed objects on current Joomla versions but
            // are treated defensively so raw article rows degrade cleanly.
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

            // displayDate arrives pre-formatted by the helper (empty when the
            // module's date option is off); only the raw row fallback needs formatting.
            $date = '';

            if (property_exists($item, 'displayDate')) {
                $date = (string) ($item->displayDate ?? '');
            } elseif (!empty($item->publish_up)) {
                $date = HTMLHelper::_('date', $item->publish_up, Text::_('DATE_FORMAT_LC3'));
            }

            $category = htmlspecialchars((string) ($item->displayCategoryTitle ?? ''), ENT_QUOTES, 'UTF-8');

            // displayIntrotext is already truncated to the admin's Introtext Limit
            // by the helper; only the raw introtext fallback needs a cap.
            if (property_exists($item, 'displayIntrotext')) {
                $intro = (string) ($item->displayIntrotext ?? '');
            } else {
                $intro = HTMLHelper::_('string.truncateComplex', (string) ($item->introtext ?? ''), 200);
            }
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

                        <?php if ($date !== '' || $category !== '') : ?>
                            <div class="atomic-card-info">
                                <?php if ($date !== '') : ?>
                                    <small class="atomic-card-date"><?php echo htmlspecialchars($date, ENT_QUOTES, 'UTF-8'); ?></small>
                                <?php endif; ?>
                                <?php if ($category !== '') : ?>
                                    <small class="atomic-card-category"><?php echo $category; ?></small>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($intro !== '') : ?>
                            <div class="atomic-card-intro">
                                <?php echo $intro; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($link !== '' && $params->get('show_readmore')) : ?>
                            <div class="atomic-card-readmore mt-auto pt-2">
                                <a class="btn btn-secondary btn-sm readmore" href="<?php echo $link; ?>">
                                    <?php echo Text::_('TPL_ATOMIC_READ_MORE'); ?><span class="visually-hidden">: <?php echo $title; ?></span>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
