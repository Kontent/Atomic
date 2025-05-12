<?php

/**
 * @copyright   Copyright (C) 2005 - 2025 Ron Severdia All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
 
defined('_JEXEC') or die;

$bsthemes = $displayData['bsthemes'] ?? 0;

if ((int) $bsthemes !== 1) {
    return;
}
?>

<div class="dropdown ms-2">
    <button id="themeBtn" class="btn btn-link p-0" type="button"
        data-bs-toggle="dropdown" aria-expanded="false" aria-label="Select theme">
        <i class="fas fa-moon" aria-hidden="true"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="themeBtn">
        <li>
            <button class="dropdown-item d-flex align-items-center" type="button" data-theme="light">
                <i class="fa-solid fa-sun fa-fw me-2"></i>Light
            </button>
        </li>
        <li>
            <button class="dropdown-item d-flex align-items-center" type="button" data-theme="dark">
                <i class="fa-solid fa-moon fa-fw me-2"></i>Dark
            </button>
        </li>
        <li>
            <button class="dropdown-item d-flex align-items-center" type="button" data-theme="auto">
                <i class="fa-solid fa-circle-half-stroke fa-fw me-2"></i>Auto
            </button>
        </li>
    </ul>
</div>