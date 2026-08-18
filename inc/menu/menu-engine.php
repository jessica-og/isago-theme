<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * -----------------------------------------
 * Menu Engine
 * -----------------------------------------
 */

function isago_get_menu($location = 'primary')
{
    $locations = get_nav_menu_locations();

    if (!isset($locations[$location])) {
        return [];
    }

    $menu_items = wp_get_nav_menu_items($locations[$location]);

    if (!$menu_items) {
        return [];
    }

    return isago_build_menu_tree($menu_items);
}