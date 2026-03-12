<?php

/**
 * @package     Joomla.Site
 * @subpackage  mod_menu
 * @copyright   Copyright (C) 2008-2026 Ron Severdia. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 * Default mod_menu override for Atomic — adds Bootstrap 5 nav classes.
 * Renders nav-link on links, dropdown-toggle + data-bs-toggle on parent
 * items, and dropdown-menu on sub-menus.
 */

defined('_JEXEC') or die;

use Joomla\CMS\Helper\ModuleHelper;
use Joomla\Utilities\ArrayHelper;

$id         = '';
$attributes = [];

$attributes['class'] = 'nav menu mod-menu' . $class_sfx;

if ($tagId = $params->get('tag_id', '')) {
    $attributes['id'] = $tagId;
}

?>
<ul <?php echo ArrayHelper::toString($attributes); ?>>
<?php foreach ($list as $i => &$item) {
    $itemParams = $item->getParams();
    $class      = 'nav-item item-' . $item->id;

    if ($item->id == $default_id) {
        $class .= ' default';
    }

    if (
        $item->id == $active_id
        || ($item->type === 'alias' && $itemParams->get('aliasoptions') == $active_id)
    ) {
        $class .= ' current';
    }

    if (in_array($item->id, $path)) {
        $class .= ' active';
    } elseif ($item->type === 'alias') {
        $aliasToId = $itemParams->get('aliasoptions');

        if (count($path) > 0 && $aliasToId == $path[count($path) - 1]) {
            $class .= ' active';
        } elseif (in_array($aliasToId, $path)) {
            $class .= ' alias-parent-active';
        }
    }

    if ($item->type === 'separator') {
        $class .= ' divider';
    }

    if ($item->deeper) {
        $class .= ' deeper';
    }

    if ($item->parent) {
        $class .= ' parent dropdown';
    }

    echo '<li class="' . $class . '">';

    switch ($item->type) :
        case 'separator':
        case 'component':
        case 'heading':
        case 'url':
            require ModuleHelper::getLayoutPath('mod_menu', 'horizontal_' . $item->type);
            break;

        default:
            require ModuleHelper::getLayoutPath('mod_menu', 'horizontal_url');
            break;
    endswitch;

    // The next item is deeper.
    if ($item->deeper) {
        echo '<ul class="dropdown-menu">';
    } elseif ($item->shallower) {
        // The next item is shallower.
        echo '</li>';
        echo str_repeat('</ul></li>', $item->level_diff);
    } else {
        // The next item is on the same level.
        echo '</li>';
    }
}
?></ul>
