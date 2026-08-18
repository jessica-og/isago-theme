<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * ----------------------------------------------------
 * Global Theme Options
 * ----------------------------------------------------
 */

function isago_get_option(
    $group,
    $key = null,
    $default = null
) {

    $options = get_option($group, []);

    if ($key === null) {
        return $options;
    }

    return $options[$key] ?? $default;

}