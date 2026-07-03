<?php
/**
 * @package    Joomla Reset
 * @copyright	 (c) 2026 Ron Severdia. All rights reserved.
 * @license		 GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

/** @var \Severdia\Component\JoomlaReset\Administrator\View\Reset\HtmlView $this */

$info = $this->resetInfo;
?>

<div class="container-fluid">
	<?php if (!$info['supported']) : ?>
		<div class="alert alert-danger">
			<h4 class="alert-heading"><?php echo Text::_('COM_JOOMLARESET_UNSUPPORTED_TITLE'); ?></h4>
			<p><?php echo Text::sprintf('COM_JOOMLARESET_UNSUPPORTED_DESC', $info['joomla_version']); ?></p>
		</div>
	<?php else : ?>
		<div class="row">
			<div class="col-lg-8">
				<div class="card mb-4">
					<div class="card-header bg-danger text-white">
						<h3 class="card-title mb-0">
							<span class="icon-warning" aria-hidden="true"></span>
							<?php echo Text::_('COM_JOOMLARESET_HEADING'); ?>
						</h3>
					</div>
					<div class="card-body">
						<p class="lead"><?php echo Text::_('COM_JOOMLARESET_WARNING_TEXT'); ?></p>

						<table class="table table-bordered">
							<tbody>
								<tr>
									<th scope="row" style="width: 200px;"><?php echo Text::_('COM_JOOMLARESET_JOOMLA_VERSION'); ?></th>
									<td>
										<span class="badge bg-info"><?php echo $this->escape($info['joomla_version']); ?></span>
									</td>
								</tr>
								<tr>
									<th scope="row"><?php echo Text::_('COM_JOOMLARESET_SQL_SET'); ?></th>
									<td>
										<span class="badge bg-secondary">Joomla <?php echo $this->escape($info['joomla_major']); ?></span>
									</td>
								</tr>
								<tr>
									<th scope="row"><?php echo Text::_('COM_JOOMLARESET_ADMIN_USER'); ?></th>
									<td>
										<strong><?php echo $this->escape($info['admin_user']); ?></strong>
										<?php if ($info['admin_email']) : ?>
											(<?php echo $this->escape($info['admin_email']); ?>)
										<?php endif; ?>
									</td>
								</tr>
								<tr>
									<th scope="row"><?php echo Text::_('COM_JOOMLARESET_TABLE_COUNT'); ?></th>
									<td>
										<span class="badge bg-warning text-dark"><?php echo (int) $info['table_count']; ?></span>
										<?php echo Text::_('COM_JOOMLARESET_TABLES_DROPPED'); ?>
									</td>
								</tr>
							</tbody>
						</table>

						<?php if (!empty($info['tables'])) : ?>
							<details class="mb-3">
								<summary><?php echo Text::sprintf('COM_JOOMLARESET_SHOW_TABLES', count($info['tables'])); ?></summary>
								<ul class="mt-2 mb-0 small font-monospace" style="max-height: 300px; overflow-y: auto;">
									<?php foreach ($info['tables'] as $tableName) : ?>
										<li><?php echo $this->escape($tableName); ?></li>
									<?php endforeach; ?>
								</ul>
							</details>
						<?php endif; ?>

						<div class="alert alert-warning">
							<h5><?php echo Text::_('COM_JOOMLARESET_WILL_DESTROY'); ?></h5>
							<ul class="mb-0">
								<li><?php echo Text::_('COM_JOOMLARESET_DESTROY_ARTICLES'); ?></li>
								<li><?php echo Text::_('COM_JOOMLARESET_DESTROY_CATEGORIES'); ?></li>
								<li><?php echo Text::_('COM_JOOMLARESET_DESTROY_MENUS'); ?></li>
								<li><?php echo Text::_('COM_JOOMLARESET_DESTROY_MODULES'); ?></li>
								<li><?php echo Text::_('COM_JOOMLARESET_DESTROY_USERS'); ?></li>
								<li><?php echo Text::_('COM_JOOMLARESET_DESTROY_MEDIA_REFS'); ?></li>
								<li><?php echo Text::_('COM_JOOMLARESET_DESTROY_CONFIG'); ?></li>
							</ul>
						</div>

						<div class="alert alert-success">
							<h5><?php echo Text::_('COM_JOOMLARESET_WILL_PRESERVE'); ?></h5>
							<ul class="mb-0">
								<li><?php echo Text::sprintf('COM_JOOMLARESET_PRESERVE_ADMIN', $this->escape($info['admin_user'])); ?></li>
								<li><?php echo Text::_('COM_JOOMLARESET_PRESERVE_EXTENSION'); ?></li>
								<li><?php echo Text::_('COM_JOOMLARESET_PRESERVE_FILES'); ?></li>
							</ul>
						</div>

						<form action="<?php echo Route::_('index.php?option=com_joomlareset&task=reset.execute'); ?>" method="post" id="reset-form">
							<div class="form-check mb-3">
								<input class="form-check-input" type="checkbox" id="confirm_reset" name="confirm_reset" value="1">
								<label class="form-check-label fw-bold text-danger" for="confirm_reset">
									<?php echo Text::_('COM_JOOMLARESET_CONFIRM_CHECKBOX'); ?>
								</label>
							</div>

							<div class="mb-3">
								<label class="form-label fw-bold" for="confirm_text">
									<?php echo Text::_('COM_JOOMLARESET_TYPE_RESET_LABEL'); ?>
								</label>
								<input type="text" class="form-control" id="confirm_text" name="confirm_text" value="" autocomplete="off" spellcheck="false" style="max-width: 240px;">
							</div>

							<button type="submit" class="btn btn-danger btn-lg" id="reset-button" disabled>
								<span class="icon-warning" aria-hidden="true"></span>
								<?php echo Text::_('COM_JOOMLARESET_BUTTON'); ?>
							</button>

							<?php echo HTMLHelper::_('form.token'); ?>
						</form>
					</div>
				</div>
			</div>

			<div class="col-lg-4">
				<div class="card">
					<div class="card-header">
						<h4 class="card-title mb-0"><?php echo Text::_('COM_JOOMLARESET_HOW_IT_WORKS'); ?></h4>
					</div>
					<div class="card-body">
						<ol>
							<li><?php echo Text::_('COM_JOOMLARESET_STEP_1'); ?></li>
							<li><?php echo Text::_('COM_JOOMLARESET_STEP_2'); ?></li>
							<li><?php echo Text::_('COM_JOOMLARESET_STEP_3'); ?></li>
							<li><?php echo Text::_('COM_JOOMLARESET_STEP_4'); ?></li>
							<li><?php echo Text::_('COM_JOOMLARESET_STEP_5'); ?></li>
						</ol>
					</div>
				</div>
			</div>
		</div>
	<?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
	var checkbox = document.getElementById('confirm_reset');
	var confirmText = document.getElementById('confirm_text');
	var button = document.getElementById('reset-button');

	function updateButtonState() {
		if (!button) {
			return;
		}

		var checked = checkbox && checkbox.checked;
		var typed = confirmText && confirmText.value.trim() === 'RESET';

		button.disabled = !(checked && typed);
	}

	if (checkbox) {
		checkbox.addEventListener('change', updateButtonState);
	}

	if (confirmText) {
		confirmText.addEventListener('input', updateButtonState);
	}

	updateButtonState();

	var form = document.getElementById('reset-form');
	if (form) {
		form.addEventListener('submit', function(e) {
			if (!confirm('<?php echo Text::_('COM_JOOMLARESET_FINAL_CONFIRM', true); ?>')) {
				e.preventDefault();
			}
		});
	}
});
</script>
