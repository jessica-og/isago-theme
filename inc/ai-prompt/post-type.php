<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
REGISTER AI PROMPT POST TYPE
=========================================================*/

function isago_register_ai_prompt_post_type() {

    $labels = array(

        'name' =>
            __('AI Prompts', 'isago'),

        'singular_name' =>
            __('AI Prompt', 'isago'),

        'menu_name' =>
            __('AI Prompts', 'isago'),

        'name_admin_bar' =>
            __('AI Prompt', 'isago'),

        'add_new' =>
            __('Add New', 'isago'),

        'add_new_item' =>
            __('Add New AI Prompt', 'isago'),

        'new_item' =>
            __('New AI Prompt', 'isago'),

        'edit_item' =>
            __('Edit AI Prompt', 'isago'),

        'view_item' =>
            __('View AI Prompt', 'isago'),

        'view_items' =>
            __('View AI Prompts', 'isago'),

        'all_items' =>
            __('All AI Prompts', 'isago'),

        'search_items' =>
            __('Search AI Prompts', 'isago'),

        'not_found' =>
            __('No AI prompts found.', 'isago'),

        'not_found_in_trash' =>
            __('No AI prompts found in Trash.', 'isago'),

        'featured_image' =>
            __('Prompt Featured Image', 'isago'),

        'set_featured_image' =>
            __('Set prompt featured image', 'isago'),

        'remove_featured_image' =>
            __('Remove prompt featured image', 'isago'),

        'use_featured_image' =>
            __('Use as prompt featured image', 'isago'),

    );


    $args = array(

        'labels' =>
            $labels,


        /*=====================================================
        VISIBILITY
        =====================================================*/

        'public' =>
            true,

        'publicly_queryable' =>
            true,

        'show_ui' =>
            true,

        'show_in_menu' =>
            true,

        'show_in_admin_bar' =>
            true,

        'show_in_nav_menus' =>
            true,

        'show_in_rest' =>
            true,


        /*=====================================================
        ARCHIVE
        =====================================================*/

        'has_archive' =>
            true,


        /*=====================================================
        INDIVIDUAL PROMPT URL
        =====================================================*/

        'rewrite' => array(

            'slug' =>
                'ai-prompts',

            'with_front' =>
                false,

        ),


        /*=====================================================
        QUERY VAR
        =====================================================*/

        'query_var' =>
            'ai_prompt',


        /*=====================================================
        SUPPORTED FEATURES
        =====================================================*/

        'supports' => array(

            'title',

            'editor',

            'thumbnail',

            'revisions',

        ),


        /*=====================================================
        ADMIN
        =====================================================*/

        'menu_icon' =>
            'dashicons-lightbulb',

        'menu_position' =>
            20,


        /*=====================================================
        CAPABILITIES
        =====================================================*/

        'capability_type' =>
            'post',

        'map_meta_cap' =>
            true,

    );


    register_post_type(
        'ai_prompt',
        $args
    );

}


add_action(
    'init',
    'isago_register_ai_prompt_post_type',
    10
);


/*=========================================================
CONNECT AI PROMPT TAXONOMIES
=========================================================*/

function isago_connect_ai_prompt_taxonomies() {


    /*=====================================================
    AI PLATFORM
    =====================================================*/

    if (
        taxonomy_exists('ai_platform')
        &&
        post_type_exists('ai_prompt')
    ) {

        register_taxonomy_for_object_type(
            'ai_platform',
            'ai_prompt'
        );

    }


    /*=====================================================
    AI TOOL CATEGORY
    =====================================================*/

    if (
        taxonomy_exists('ai_tool_category')
        &&
        post_type_exists('ai_prompt')
    ) {

        register_taxonomy_for_object_type(
            'ai_tool_category',
            'ai_prompt'
        );

    }


    /*=====================================================
    WORDPRESS TAGS
    =====================================================*/

    if (
        taxonomy_exists('post_tag')
        &&
        post_type_exists('ai_prompt')
    ) {

        register_taxonomy_for_object_type(
            'post_tag',
            'ai_prompt'
        );

    }

}


add_action(
    'init',
    'isago_connect_ai_prompt_taxonomies',
    30
);



/*=========================================================
AI PROMPT ADMIN SUBMENU
=========================================================*/

/*
 * Keep the AI Prompt admin section limited to AI Prompt
 * related navigation.
 *
 * Desired structure:
 *
 * AI Prompts
 * ├── All AI Prompts
 * ├── Add New AI Prompt
 * └── AI Prompt Categories
 *
 * WordPress automatically creates the All AI Prompts,
 * Add New AI Prompt, and taxonomy submenu items.
 *
 * We therefore whitelist the exact submenu URLs instead
 * of checking whether a URL merely contains
 * "post_type=ai_prompt".
 */

function isago_clean_ai_prompt_submenu() {

    global $submenu;


    $parent_slug =
        'edit.php?post_type=ai_prompt';


    if (
        !isset(
            $submenu[
                $parent_slug
            ]
        )
    ) {

        return;

    }


    $allowed_items =
        array(

            'edit.php?post_type=ai_prompt',

            'post-new.php?post_type=ai_prompt',

            'edit-tags.php?taxonomy=ai_prompt_category&post_type=ai_prompt',

        );


    $cleaned =
        array();


    $category_found =
        false;


    foreach (
        $submenu[$parent_slug]
        as $item
    ) {

        if (
            !isset(
                $item[2]
            )
        ) {

            continue;

        }


        $url =
            $item[2];


        /*
         * Keep only the exact AI Prompt URLs.
         */

        if (
            in_array(
                $url,
                $allowed_items,
                true
            )
        ) {

            /*
             * Prevent duplicate AI Prompt Categories.
             */

            if (
                strpos(
                    $url,
                    'taxonomy=ai_prompt_category'
                ) !== false
            ) {

                if ($category_found) {

                    continue;

                }


                $category_found =
                    true;
            }


            $cleaned[] =
                $item;
        }

    }


    /*
     * If WordPress did not automatically create the
     * AI Prompt Categories submenu, create it once.
     */

    if (
        !$category_found
        &&
        current_user_can(
            'manage_categories'
        )
    ) {

        $cleaned[] =
            array(

                __('AI Prompt Categories', 'isago'),

                'manage_categories',

                'edit-tags.php?taxonomy=ai_prompt_category&post_type=ai_prompt',

                '',

            );

    }


    $submenu[$parent_slug] =
        $cleaned;

}


add_action(
    'admin_menu',
    'isago_clean_ai_prompt_submenu',
    999
);
