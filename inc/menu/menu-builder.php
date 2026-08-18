<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * -----------------------------------------
 * Build Menu Tree
 * -----------------------------------------
 */
function isago_build_menu_tree($items)
{
    $tree   = [];
    $lookup = [];

    /*
     * Prepare items
     */
    foreach ($items as $item) {

        /*
         * Always reset children.
         */
        $item->children = [];

        /*
         * Always make sure classes exist.
         */
        if (!isset($item->classes) || !is_array($item->classes)) {
            $item->classes = [];
        }

        $lookup[$item->ID] = $item;
    }

    /*
     * Build hierarchy
     */
    foreach ($lookup as $item) {

        if (!empty($item->menu_item_parent)) {

            $parent_id = (int) $item->menu_item_parent;

            if (isset($lookup[$parent_id])) {

                $lookup[$parent_id]->children[] = $item;

            }

        } else {

            $tree[] = $item;

        }
    }

    /*
     * -----------------------------------------
     * Determine active menu item
     * -----------------------------------------
     */
    $current_id = isago_get_current_menu_item_id($lookup);

    /*
     * -----------------------------------------
     * Apply active classes recursively
     * -----------------------------------------
     */
    if ($current_id) {

        isago_mark_active_menu_branch(
            $tree,
            $current_id
        );

    }

    return $tree;
}


/**
 * -----------------------------------------
 * Find Current Menu Item
 * -----------------------------------------
 */
function isago_get_current_menu_item_id($lookup)
{
    /*
     * First try WordPress queried object.
     */
    $queried_object_id = get_queried_object_id();

    if ($queried_object_id) {

        foreach ($lookup as $item) {

            if (
                !empty($item->object_id) &&
                (int) $item->object_id === (int) $queried_object_id
            ) {

                return (int) $item->ID;

            }
        }
    }

    /*
     * -----------------------------------------
     * Fallback: compare URLs
     * -----------------------------------------
     */

    $current_url = isago_normalize_menu_url(
        home_url(
            add_query_arg(
                [],
                $_SERVER['REQUEST_URI'] ?? ''
            )
        )
    );

    foreach ($lookup as $item) {

        if (empty($item->url)) {
            continue;
        }

        $menu_url = isago_normalize_menu_url($item->url);

        if ($menu_url === $current_url) {

            return (int) $item->ID;

        }
    }

    return 0;
}


/**
 * -----------------------------------------
 * Normalize Menu URL
 * -----------------------------------------
 */
function isago_normalize_menu_url($url)
{
    if (empty($url)) {
        return '';
    }

    /*
     * Remove query strings and fragments.
     */
    $url = strtok($url, '?#');

    /*
     * Convert to absolute URL if necessary.
     */
    if (strpos($url, 'http://') !== 0 && strpos($url, 'https://') !== 0) {

        $url = home_url($url);

    }

    /*
     * Remove trailing slash.
     */
    $url = untrailingslashit($url);

    /*
     * Normalize scheme and host.
     */
    $parsed = wp_parse_url($url);

    if (!$parsed) {
        return $url;
    }

    $scheme = isset($parsed['scheme'])
        ? strtolower($parsed['scheme'])
        : 'http';

    $host = isset($parsed['host'])
        ? strtolower($parsed['host'])
        : '';

    $path = isset($parsed['path'])
        ? untrailingslashit($parsed['path'])
        : '';

    return $scheme . '://' . $host . $path;
}


/**
 * -----------------------------------------
 * Mark Active Menu Branch
 * -----------------------------------------
 */
function isago_mark_active_menu_branch(&$items, $current_id)
{
    foreach ($items as $item) {

        /*
         * Check this item.
         */
        if ((int) $item->ID === (int) $current_id) {

            $item->classes[] = 'current-menu-item';
            $item->isago_active = true;

            return true;
        }

        /*
         * Check children.
         */
        if (!empty($item->children)) {

            $child_is_active = isago_mark_active_menu_branch(
                $item->children,
                $current_id
            );

            if ($child_is_active) {

                /*
                 * This item is a parent/ancestor
                 * of the active item.
                 */
                $item->classes[] = 'current-menu-parent';
                $item->classes[] = 'current-menu-ancestor';

                $item->isago_active = true;

                return true;
            }
        }
    }

    return false;
}