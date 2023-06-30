<?php
defined('_JEXEC') or die;

use Joomla\CMS\Helper\ModuleHelper;
use Joomla\Utilities\ArrayHelper;

$standard = ['separator', 'component', 'heading', 'url'];
$startLevel = $params->get('startLevel');
$endLevel = $params->get('endLevel');

$attributes          = [];
$attributes['class'] = 'nav menu vertical' . $class_sfx;

if ($tagId = $params->get('tag_id', '')) {
    $attributes['id'] = $tagId;
}

?>

<div <?php echo ArrayHelper::toString($attributes); ?>>
	
	<?php foreach ($list as $i => &$item) { ?>
		<?php
    $itemParams = $item->getParams();
			$class = 'item-' . $item->id;

			if ($item->id == $default_id) {
				$class .= ' default';
			}

			if ($item->id == $active_id || ($item->type === 'alias' && $itemParams->get('aliasoptions') == $active_id)) {
				$class .= ' current';
			}

			if (in_array($item->id, $path)) {
				$class .= ' active';
			} elseif ($item->type === 'alias') {
				$aliasToId = $itemParams->get('aliasoptions');

				if (count($path) > 0 && $aliasToId == $path[count($path) - 1]) {
					$class .= ' active';
				}
				elseif (in_array($aliasToId, $path))
				{
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
				$class .= ' parent';
			}
		?>
		
		<?php if ($item->deeper) { ?>
			<div class="btn-group dropend">
		<?php } ?>

			<?php if (in_array($item->type, $standard)) { ?>
				<?php require(JModuleHelper::getLayoutPath('mod_menu', 'vertical_' . $item->type)); ?>
			<?php } else { ?>
				<?php require(JModuleHelper::getLayoutPath('mod_menu', 'vertical_url')); ?>
			<?php } ?>

			<?php if ($item->deeper) { ?>
				<ul class="dropdown-menu">
			<?php } ?>

			<?php if ($item->shallower) { ?>
				</ul>	
			</div>
			
			<?php } ?>
	<?php } ?>
		
</div>

