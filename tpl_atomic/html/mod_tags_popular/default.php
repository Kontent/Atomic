<?php

/**
 * @package     Atomic
 * @subpackage  mod_tags_popular
 *
 * Layout override — renders tags as inline badges with the
 * tag alias added as a CSS class for per-tag colour targeting.
 */

defined('_JEXEC') or die;

use Joomla\CMS\Router\Route;
use Joomla\Component\Tags\Site\Helper\RouteHelper;

?>
<div class="mod-tagspopular tagspopular d-flex flex-wrap gap-2">
<?php foreach ($list as $item) : ?>
    <a href="<?php echo Route::_(RouteHelper::getComponentTagRoute($item->tag_id . ':' . $item->alias, $item->language)); ?>"
       class="tag <?php echo htmlspecialchars($item->alias); ?>">
        <?php echo htmlspecialchars($item->title); ?>
    </a>
<?php endforeach; ?>
</div>
