<?php

/**
 * @package    Atomic
 * @copyright	 (c) 2009-2026 Ron Severdia. All rights reserved.
 * @license		 GNU General Public License version 2 or later; see LICENSE.txt
 *
 * Atomic List — renders the Articles - Category module (Joomla 4/5) as a
 * compact vertical list with a small square thumbnail, linked title and date.
 * Select it under Advanced > Layout. Items are helper-processed objects.
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
<div class="mod-articlescategory category-module atomic-articles-list-module">
    <ul class="atomic-articles-list list-unstyled">
        <?php foreach ($items as $item) : ?>
            <?php
            $title = htmlspecialchars((string) ($item->title ?? ''), ENT_QUOTES, 'UTF-8');
            // double_encode=false: the helper link is already &amp;-encoded by Route
            $link  = htmlspecialchars((string) ($item->link ?? ''), ENT_QUOTES, 'UTF-8', false);

            // displayDate is empty when the module's date option is off;
            // it arrives pre-formatted and localized by the helper.
            $date = (string) ($item->displayDate ?? '');

            $images = json_decode($item->images ?? '');
            $imgUrl = '';
            $imgAlt = '';

            if (!empty($images->image_intro)) {
                $img    = HTMLHelper::cleanImageURL($images->image_intro);
                $imgUrl = htmlspecialchars((string) $img->url, ENT_QUOTES, 'UTF-8');
                $imgAlt = htmlspecialchars((string) ($images->image_intro_alt ?? ''), ENT_QUOTES, 'UTF-8');
            }
            ?>
            <li class="atomic-articles-list-item d-flex gap-3 align-items-center">
                <?php if ($imgUrl !== '') : ?>
                    <?php if ($link !== '') : ?>
                        <a href="<?php echo $link; ?>" class="atomic-articles-list-thumb flex-shrink-0" aria-hidden="true" tabindex="-1">
                            <img src="<?php echo $imgUrl; ?>" alt="<?php echo $imgAlt; ?>" loading="lazy">
                        </a>
                    <?php else : ?>
                        <span class="atomic-articles-list-thumb flex-shrink-0">
                            <img src="<?php echo $imgUrl; ?>" alt="<?php echo $imgAlt; ?>" loading="lazy">
                        </span>
                    <?php endif; ?>
                <?php endif; ?>
                <div class="atomic-articles-list-body">
                    <?php if ($link !== '' && $params->get('link_titles', 1)) : ?>
                        <a class="atomic-articles-list-title" href="<?php echo $link; ?>"><?php echo $title; ?></a>
                    <?php else : ?>
                        <span class="atomic-articles-list-title"><?php echo $title; ?></span>
                    <?php endif; ?>
                    <?php if ($date !== '') : ?>
                        <small class="atomic-articles-list-date d-block"><?php echo htmlspecialchars($date, ENT_QUOTES, 'UTF-8'); ?></small>
                    <?php endif; ?>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
