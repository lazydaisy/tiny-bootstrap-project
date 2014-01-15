<?php

function theme_tiny_process_css($css, $theme) {

    // Set the url for @font-face
    if (!empty($theme->settings->font)) {
        $font = $theme->settings->font;
    } else {
        $font = '';
    }
    $css = theme_tiny_set_font($css, $font);

    // Set font-face for @font-face
    if (!empty($theme->settings->fontface)) {
        $fontface = $theme->settings->fontface;
    } else {
        $fontface = 'Mordred';

    }
    $css = theme_tiny_set_fontface($css, $fontface);

    // Set the background brand icon for tiny-navbar
    if (!empty($theme->settings->navbaricon)) {
        $navbaricon = $theme->settings->navbaricon;
    } else {
        $navbaricon = null;
    }
    $css = theme_tiny_set_navbaricon($css, $navbaricon);

    // Set custom CSS
    if (!empty($theme->settings->customcss)) {
        $customcss = $theme->settings->customcss;
    } else {
        $customcss = null;
    }
    $css = theme_tiny_set_customcss($css, $customcss);

    return $css;
}

function theme_tiny_set_font($css, $font) {
    global $OUTPUT;
    $tag = '[[setting:font]]';
    $replacement = $font;
    if (is_null($replacement)) {
        $replacement = '';
    }
    $css = str_replace($tag, $replacement, $css);

    return $css;
}

function theme_tiny_set_fontface($css, $fontface) {
    global $OUTPUT;
    $tag = '[[setting:fontface]]';
    $replacement = $fontface;
    if (is_null($replacement)) {
        $replacement = 'Tempus Sans ITC';
    }

    $css = str_replace($tag, $replacement, $css);

    return $css;
}

function theme_tiny_set_navbaricon($css, $navbaricon) {
    global $OUTPUT;
    $tag = '[[setting:navbaricon]]';
    $replacement = $navbaricon;
    if (is_null($replacement)) {
        $replacement = $OUTPUT->pix_url('icon-m','theme');
    }
    $css = str_replace($tag, $replacement, $css);
    return $css;
}

function theme_tiny_set_customcss($css, $customcss) {
    $tag = '[[setting:customcss]]';
    $replacement = $customcss;
    if (is_null($replacement)) {
        $replacement = '';
    }

    $css = str_replace($tag, $replacement, $css);

    return $css;
}