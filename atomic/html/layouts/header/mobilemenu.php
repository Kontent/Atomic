<?php

/**
 * @copyright   Copyright (C) 2005 - 2025 Ron Severdia All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
 
defined('_JEXEC') or die;

$containerClass = $displayData['containerClass'] ?? 'container';
?>

<div class="mobile-menu">
    <div class="<?php echo htmlspecialchars($containerClass, ENT_QUOTES, 'UTF-8'); ?>">
        <div class="row">
            <jdoc:include type="modules" name="mobilemenu" title="Mobile Menu" style="mobilemenu" />
        </div>
    </div>
</div>