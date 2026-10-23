<?php

if (!defined('ABSPATH')) {
    exit;
}

/*=========================================================
Admin Post Helpers
=========================================================*/

/**
 * Get admin posts and videos.
 *
 * @return WP_Post[]
 */
function isago_admin_posts()
{
    return get_posts([
        'post_type'      => [
            'post',
            'isago_video',
        ],
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ]);
}


/*=========================================================
Remove AI Platform Taxonomy Menus From Posts
=========================================================*/

/**
 * Remove AI Tools and AI Tool Categories from
 * the WordPress Posts sidebar menu.
 *
 * These taxonomies remain available through their
 * dedicated AI Platforms / AI Tools admin sections.
 */
function isago_remove_ai_taxonomies_from_posts_menu()
{
    /*
     * AI Tools / AI Platforms taxonomy
     */
    remove_submenu_page(
        'edit.php',
        'edit-tags.php?taxonomy=ai_platform'
    );

    /*
     * AI Tool Categories taxonomy
     */
    remove_submenu_page(
        'edit.php',
        'edit-tags.php?taxonomy=ai_tool_category'
    );
}

add_action(
    'admin_menu',
    'isago_remove_ai_taxonomies_from_posts_menu',
    999
);