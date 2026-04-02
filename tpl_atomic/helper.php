<?php
defined('_JEXEC') or die;

function getGoogleFontFamily($fontval, $pos, $customfontname = '') {
    $fontfamily	= '';
    switch($fontval) :
        case 1:
            $fontfamily = $customfontname;
            break;
        case 2:
            $fontfamily = 'none';
            break;
        case 3:
            $fontfamily = '"Inter", sans-serif';
            break;
        case 4:
            $fontfamily = '"Lato", sans-serif';
            break;
        case 5:
            $fontfamily = '"Montserrat", sans-serif';
            break;
        case 6:
            $fontfamily = '"Open Sans", sans-serif';
            break;
        case 7:
            $fontfamily = '"Roboto", sans-serif';
            break;
        case 8:
            $fontfamily = '"Fraunces", serif';
            break;
        case 9:
            $fontfamily = '"Libre Baskerville", serif';
            break;
        case 10:
            $fontfamily = '"Merriweather", serif';
            break;
        case 11:
            $fontfamily = '"Noto Serif", serif';
            break;
        case 12:
            $fontfamily = '"Unna", serif';
            break;
        case 13:
            $fontfamily = $customfontname;
            break;
        default:
            $fontfamily = $pos === 'body' ? 'var(--bs-body-font-family)' : '';
    endswitch;
    return $fontfamily;
}
function getGoogleFontLink($fontval) {
    $fontlink = '';
    switch($fontval) :
        case 3:
            $fontlink = '<link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">';
            break;
        case 4:
            $fontlink = '<link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">';
            break;
        case 5:
            $fontlink = '<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">';
            break;
        case 6:
            $fontlink = '<link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">';
            break;
        case 7:
            $fontlink = '<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">';
            break;
        case 8:
            $fontlink = '<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,100..900;1,9..144,100..900&display=swap" rel="stylesheet">';
            break;
        case 9:
            $fontlink = '<link href="https://fonts.googleapis.com/css2?family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">';
            break;
        case 10:
            $fontlink = '<link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,wght@0,300;0,400;0,700;0,900;1,300;1,400;1,700;1,900&display=swap" rel="stylesheet">';
            break;
        case 11:
            $fontlink = '<link href="https://fonts.googleapis.com/css2?family=Noto+Serif:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">';
            break;
        case 12:
            $fontlink = '<link href="https://fonts.googleapis.com/css2?family=Unna:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">';
            break;
    endswitch;
    return $fontlink;
}
function isGoogleFont($fontval) {
    $isGoogleFont	= false;
    switch($fontval) :
        case 3:
        case 4:
        case 5:
        case 6:
        case 7:
        case 8:
        case 9:
        case 10:
        case 11:
        case 12:
            $isGoogleFont = true;
            break;
    endswitch;
    return $isGoogleFont;
}