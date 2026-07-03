<?php
/**
 * @package    Atomic
 * @copyright	 (c) 2009-2026 Ron Severdia. All rights reserved.
 * @license		 GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Joomla compatibility: plain procedural helpers with no Joomla API calls, so
 * no deprecated J4-only APIs; Bootstrap and Font Awesome sources are
 * version-mapped via install.php. Verified on Joomla 4/5/6; the update feed
 * also opens Joomla 7.
 */
defined('_JEXEC') or die;

/**
 * Single source of truth for the built-in Google Font presets (font param
 * values 3-12). Values 0/1/2/13 (default/custom/none/system) are handled by
 * the callers. Family strings and link URLs must stay byte-identical to the
 * values shipped in earlier releases — existing sites rely on them.
 */
function atomicGoogleFontMap() {
    static $map = [
        3 => [
            'family' => '"Inter", sans-serif',
            'link'   => '<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">',
        ],
        4 => [
            'family' => '"Lato", sans-serif',
            'link'   => '<link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">',
        ],
        5 => [
            'family' => '"Montserrat", sans-serif',
            'link'   => '<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">',
        ],
        6 => [
            'family' => '"Open Sans", sans-serif',
            'link'   => '<link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">',
        ],
        7 => [
            'family' => '"Roboto", sans-serif',
            'link'   => '<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">',
        ],
        8 => [
            'family' => '"Fraunces", serif',
            'link'   => '<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,100..900;1,9..144,100..900&display=swap" rel="stylesheet">',
        ],
        9 => [
            'family' => '"Libre Baskerville", serif',
            'link'   => '<link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">',
        ],
        10 => [
            'family' => '"Merriweather", serif',
            'link'   => '<link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">',
        ],
        11 => [
            'family' => '"Noto Serif", serif',
            'link'   => '<link href="https://fonts.googleapis.com/css2?family=Noto+Serif:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">',
        ],
        12 => [
            'family' => '"Unna", serif',
            'link'   => '<link href="https://fonts.googleapis.com/css2?family=Unna:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">',
        ],
    ];
    return $map;
}
// Normalize a font param value to an int key with the same loose-comparison
// semantics the previous switch statements had ("3" matches 3; non-numeric
// or fractional values match nothing).
function atomicNormalizeFontValue($fontval) {
    return (is_numeric($fontval) && (int) $fontval == $fontval) ? (int) $fontval : null;
}
function getGoogleFontFamily($fontval, $pos, $customfontname = '') {
    $key = atomicNormalizeFontValue($fontval);

    if ($key === 1 || $key === 13) {
        return preg_replace('/[<>{};]/', '', (string) $customfontname);
    }
    if ($key === 2) {
        return 'none';
    }

    $map = atomicGoogleFontMap();
    if ($key !== null && isset($map[$key])) {
        return $map[$key]['family'];
    }

    return $pos === 'body' ? 'var(--bs-body-font-family)' : '';
}
function getGoogleFontLink($fontval) {
    $key = atomicNormalizeFontValue($fontval);
    $map = atomicGoogleFontMap();

    return ($key !== null && isset($map[$key])) ? $map[$key]['link'] : '';
}
function isGoogleFont($fontval) {
    $key = atomicNormalizeFontValue($fontval);

    return $key !== null && isset(atomicGoogleFontMap()[$key]);
}
/**
 * Validate a dash-separated Bootstrap column-ratio string ("12", "4-8",
 * "2-8-2"). Returns $value when it has 1-3 numeric segments, each 1-12,
 * summing to 12 or less; otherwise returns $default. Intended for the
 * bootscolumns/headercolumns/footercolumns params before they are split
 * with explode('-') in index.php.
 */
function atomicValidateColumns(string $value, string $default): string {
    if (!preg_match('/^\d+(-\d+){0,2}$/', $value)) {
        return $default;
    }

    $sum = 0;

    foreach (explode('-', $value) as $segment) {
        $segment = (int) $segment;

        if ($segment < 1 || $segment > 12) {
            return $default;
        }

        $sum += $segment;
    }

    return $sum <= 12 ? $value : $default;
}
// Kebab-case sanitizer for HTML data-attribute values (e.g. the usergroupdata
// data-user attribute in index.php and component.php).
function sanitizeForDataAttr($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9]+/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}