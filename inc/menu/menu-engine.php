<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * -----------------------------------------
 * Menu Engine
 * -----------------------------------------
 */


/**
 * -----------------------------------------
 * Isago Menu URL Resolver
 * -----------------------------------------
 *
 * Allows specific top-level navigation items
 * to point to Isago's custom directory pages
 * instead of WordPress taxonomy/category URLs.
 *
 * AI Tools  -> /ai-tools-category/
 * AI Prompts -> /ai-prompts-categories/
 */
function isago_resolve_menu_url($item)
{
    if (!$item || !is_object($item)) {
        return '';
    }

    /*
     * Only override top-level menu items.
     *
     * Child/submenu items continue using the
     * URL assigned to them in WordPress.
     */
    if (
        isset($item->menu_item_parent)
        && absint($item->menu_item_parent) !== 0
    ) {
        return $item->url;
    }

    /*
     * Normalize menu title.
     */
    $title = isset($item->title)
        ? trim(wp_strip_all_tags($item->title))
        : '';

    /*
     * AI TOOLS
     *
     * Always send the main AI Tools navigation
     * item to the AI Tool Categories directory.
     */
    if (strcasecmp($title, 'AI Tools') === 0) {

        return home_url('/ai-tools-categories/');
    }

    /*
     * AI PROMPTS
     *
     * Always send the main AI Prompts navigation
     * item to the AI Prompts Categories directory.
     */
    if (strcasecmp($title, 'AI Prompts') === 0) {

        return home_url('/ai-prompts-categories/');
    }

    /*
     * Everything else keeps the URL assigned
     * to the WordPress menu item.
     */
    return $item->url;
}


/**
 * -----------------------------------------
 * Get Menu
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

    /*
     * Resolve URLs before building the tree.
     *
     * This means both top-level menu items and
     * dropdown templates receive the corrected
     * URL through $item->url.
     */
    foreach ($menu_items as $menu_item) {

        $resolved_url = isago_resolve_menu_url($menu_item);

        if (!empty($resolved_url)) {
            $menu_item->url = $resolved_url;
        }
    }

    return isago_build_menu_tree($menu_items);
}