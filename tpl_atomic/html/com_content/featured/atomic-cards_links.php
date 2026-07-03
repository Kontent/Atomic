<?php

/**
 * @package    Atomic
 * @copyright	 (c) 2009-2026 Ron Severdia. All rights reserved.
 * @license		 GNU General Public License version 2 or later; see LICENSE.txt
 *
 * Atomic Cards — "More Articles" link list for the featured articles layout.
 */

defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;

?>
<div class="com-content-featured__links items-more atomic-cards-links">
    <h3>
        <?php echo Text::_('JGLOBAL_MORE_ARTICLES'); ?>
    </h3>
    <ul class="list-unstyled">
        <?php foreach ($this->link_items as $item) : ?>
            <li>
                <a href="<?php echo Route::_(RouteHelper::getArticleRoute($item->slug, $item->catid, $item->language)); ?>">
                    <?php echo $this->escape($item->title); ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>
