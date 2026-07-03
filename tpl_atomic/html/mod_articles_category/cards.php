<?php

/**
 * @package    Atomic
 * @copyright	 (c) 2009-2026 Ron Severdia. All rights reserved.
 * @license		 GNU General Public License version 2 or later; see LICENSE.txt
 *
 * Atomic Cards — renders the Articles - Category module (Joomla 4/5) as a
 * responsive grid of Bootstrap cards. Select it under Advanced > Layout.
 * Items are helper-processed objects (->link, ->displayDate, ->displayIntrotext...).
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

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
<div class="mod-articlescategory category-module atomic-cards atomic-cards-module">
    <div class="row g-4 row-cols-1 row-cols-md-3">
        <?php foreach ($items as $item) : ?>
            <?php
            $title = htmlspecialchars((string) ($item->title ?? ''), ENT_QUOTES, 'UTF-8');
            // double_encode=false: the helper link is already &amp;-encoded by Route
            $link  = htmlspecialchars((string) ($item->link ?? ''), ENT_QUOTES, 'UTF-8', false);

            // The helper leaves display* properties empty when the matching
            // module option is off, so empty checks respect the settings.
            // displayDate arrives pre-formatted and localized by the helper.
            $date     = (string) ($item->displayDate ?? '');
            $category = htmlspecialchars((string) ($item->displayCategoryTitle ?? ''), ENT_QUOTES, 'UTF-8');
            $hits     = (string) ($item->displayHits ?? '');
            $intro    = (string) ($item->displayIntrotext ?? '');
            $readmore = htmlspecialchars((string) ($item->displayReadmore ?? ''), ENT_QUOTES, 'UTF-8');

            $images = json_decode($item->images ?? '');
            $imgUrl = '';
            $imgAlt = '';

            if (!empty($images->image_intro)) {
                $img    = HTMLHelper::cleanImageURL($images->image_intro);
                $imgUrl = htmlspecialchars((string) $img->url, ENT_QUOTES, 'UTF-8');
                $imgAlt = htmlspecialchars((string) ($images->image_intro_alt ?? ''), ENT_QUOTES, 'UTF-8');
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

                        <?php if ($date !== '' || $category !== '' || $hits !== '') : ?>
                            <div class="atomic-card-info">
                                <?php if ($date !== '') : ?>
                                    <small class="atomic-card-date"><?php echo htmlspecialchars($date, ENT_QUOTES, 'UTF-8'); ?></small>
                                <?php endif; ?>
                                <?php if ($category !== '') : ?>
                                    <small class="atomic-card-category"><?php echo $category; ?></small>
                                <?php endif; ?>
                                <?php if ($hits !== '') : ?>
                                    <small class="atomic-card-hits">(<?php echo (int) $hits; ?>)</small>
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
