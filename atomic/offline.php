<?php
/**
 * @copyright	Copyright (C) 2025 Ron Severdia. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

if (!isset($this->error)) {
	$this->error = JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
	$this->debug = false;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
	<head>
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
   	 	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
   	 	<meta name="HandheldFriendly" content="true" />
		<meta name="apple-mobile-web-app-capable" content="YES" />
		<meta name="robots" content="noindex">
		<title><?php echo $this->error->getCode(); ?> - <?php echo $this->title; ?></title>
		<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
		<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/js/all.min.js" integrity="sha512-b+nQTCdtTBIRIbraqNEwsjB6UvL3UEMkXnhzd8awtCYh0Kcsjl9uEgwVFVbhoj3uu1DO1ZMacNvLoyJJiNfcvg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
	</head>
	<body class="offline">
    <div class="outer">
        <div class="offline-card">
            <div class="header">
            <?php if (!empty($logo)) : ?>
                <h1><?php echo $logo; ?></h1>
            <?php else : ?>
                <h1><?php echo $sitename; ?></h1>
            <?php endif; ?>
            <?php if ($app->get('offline_image')) : ?>
                <?php echo HTMLHelper::_('image', $app->get('offline_image'), $sitename, [], false, 0); ?>
            <?php endif; ?>
            <?php if ($app->get('display_offline_message', 1) == 1 && str_replace(' ', '', $app->get('offline_message')) != '') : ?>
                <p><?php echo $app->get('offline_message'); ?></p>
            <?php elseif ($app->get('display_offline_message', 1) == 2) : ?>
                <p><?php echo Text::_('JOFFLINE_MESSAGE'); ?></p>
            <?php endif; ?>
            </div>
            <div class="login">
                <jdoc:include type="message" />
                <form action="<?php echo Route::_('index.php', true); ?>" method="post" id="form-login">
                    <fieldset>
                        <label for="username"><?php echo Text::_('JGLOBAL_USERNAME'); ?></label>
                        <input name="username" class="form-control" id="username" type="text">

                        <label for="password"><?php echo Text::_('JGLOBAL_PASSWORD'); ?></label>
                        <input name="password" class="form-control" id="password" type="password">

                        <?php foreach ($extraButtons as $button) :
                            $dataAttributeKeys = array_filter(array_keys($button), function ($key) {
                                return substr($key, 0, 5) == 'data-';
                            });
                            ?>
                            <div class="mod-login__submit form-group">
                                <button type="button"
                                        class="btn btn-secondary w-100 mt-4 <?php echo $button['class'] ?? '' ?>"
                                <?php foreach ($dataAttributeKeys as $key) : ?>
                                    <?php echo $key ?>="<?php echo $button[$key] ?>"
                                <?php endforeach; ?>
                                <?php if ($button['onclick']) : ?>
                                    onclick="<?php echo $button['onclick'] ?>"
                                <?php endif; ?>
                                title="<?php echo Text::_($button['label']) ?>"
                                id="<?php echo $button['id'] ?>"
                                >
                                <?php if (!empty($button['icon'])) : ?>
                                    <span class="<?php echo $button['icon'] ?>"></span>
                                <?php elseif (!empty($button['image'])) : ?>
                                    <?php echo $button['image']; ?>
                                <?php elseif (!empty($button['svg'])) : ?>
                                    <?php echo $button['svg']; ?>
                                <?php endif; ?>
                                <?php echo Text::_($button['label']) ?>
                                </button>
                            </div>
                        <?php endforeach; ?>

                        <button type="submit" name="Submit" class="btn btn-primary"><?php echo Text::_('JLOGIN'); ?></button>

                        <input type="hidden" name="option" value="com_users">
                        <input type="hidden" name="task" value="user.login">
                        <input type="hidden" name="return" value="<?php echo base64_encode(Uri::base()); ?>">
                        <?php echo HTMLHelper::_('form.token'); ?>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</body>
</html>