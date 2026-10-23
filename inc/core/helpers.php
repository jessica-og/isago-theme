<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * ---------------------------------------------------------
 * Helper Functions
 * ---------------------------------------------------------
 */


/**
 * Return theme version
 */

function isago_version()
{
    return ISAGO_VERSION;
}


/**
 * ---------------------------------------------------------
 * Get Isago Author
 * ---------------------------------------------------------
 */

function isago_get_author_name()
{

    $theme = isago_get_theme_settings();

    return !empty($theme['author_name'])
        ? $theme['author_name']
        : 'Isago';

}


/**
 * ---------------------------------------------------------
 * Get Isago Author Image
 * ---------------------------------------------------------
 */

function isago_get_author_image()
{

    $theme = isago_get_theme_settings();

    if (!empty($theme['author_image'])) {

        return $theme['author_image'];

    }

    return ISAGO_URI . '/assets/images/placeholder.png';

}