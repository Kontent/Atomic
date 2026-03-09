<?php

/**
 * @copyright   Copyright (C) 2005 - 2026 Ron Severdia All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
 
defined('_JEXEC') or die;

$direction = $displayData['direction'] ?? 'ltr';
?>

<div class="sidebar-menu offcanvas offcanvas-<?php echo $direction === 'ltr' ? 'start' : 'end'; ?>"
     data-bs-backdrop="false"
     data-bs-scroll="true"
     tabindex="-1"
     id="offcanvasSidebarMenu"
     aria-labelledby="offcanvasSidebarMenuLabel">

    <button class="btn btn-primary offcanvas-toggle" type="button"
            data-bs-toggle="offcanvas"
            data-bs-target="#offcanvasSidebarMenu"
            aria-controls="offcanvasSidebarMenu">
        <span class="offcanvas-toggle-icon offcanvas-toggle-icon--close">
            <i class="fas fa-times"></i>
        </span>
        <span class="offcanvas-toggle-icon offcanvas-toggle-icon--open">
            <i class="fas fa-bars"></i>
        </span>
    </button>

    <div class="offcanvas-content">
        <jdoc:include type="modules" name="sidebar-menu" title="Sidebar Menu" style="none" />
    </div>
</div>