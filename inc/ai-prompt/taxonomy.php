<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
AI PROMPT CATEGORY TAXONOMY
=========================================================*/

function isago_register_ai_prompt_category_taxonomy() {

    $labels = array(

        'name' =>
            __('AI Prompt Categories', 'isago'),

        'singular_name' =>
            __('AI Prompt Category', 'isago'),

        'search_items' =>
            __('Search AI Prompt Categories', 'isago'),

        'all_items' =>
            __('All AI Prompt Categories', 'isago'),

        'parent_item' =>
            __('Parent AI Prompt Category', 'isago'),

        'parent_item_colon' =>
            __('Parent AI Prompt Category:', 'isago'),

        'edit_item' =>
            __('Edit AI Prompt Category', 'isago'),

        'update_item' =>
            __('Update AI Prompt Category', 'isago'),

        'add_new_item' =>
            __('Add New AI Prompt Category', 'isago'),

        'new_item_name' =>
            __('New AI Prompt Category Name', 'isago'),

        'menu_name' =>
            __('AI Prompt Categories', 'isago'),

        'name_admin_bar' =>
            __('AI Prompt Category', 'isago'),

    );


    $args = array(

        'labels' =>
            $labels,


        /*=====================================================
        HIERARCHY
        =====================================================*/

        'hierarchical' =>
            true,


        /*=====================================================
        VISIBILITY
        =====================================================*/

        'public' =>
            true,

        'publicly_queryable' =>
            true,

        'show_ui' =>
            true,

        'show_admin_column' =>
            true,

        'show_in_nav_menus' =>
            true,

        'show_tagcloud' =>
            false,

        'show_in_rest' =>
            true,


        /*=====================================================
        REWRITE
        =====================================================*/

        'rewrite' => array(

            'slug' =>
                'ai-prompts-categories',

            'with_front' =>
                false,

            /*
             * Keep true because the taxonomy itself is
             * hierarchical.
             *
             * Example:
             *
             * /ai-prompts-library/writing/
             *
             * /ai-prompts-library/writing/blog-writing/
             */

            'hierarchical' =>
                true,

        ),


        /*=====================================================
        QUERY VAR
        =====================================================*/

        'query_var' =>
            'ai_prompt_category',


        /*=====================================================
        CAPABILITIES
        =====================================================*/

        'capabilities' => array(

            'manage_terms' =>
                'manage_categories',

            'edit_terms' =>
                'manage_categories',

            'delete_terms' =>
                'manage_categories',

            'assign_terms' =>
                'edit_posts',

        ),

    );


    register_taxonomy(
        'ai_prompt_category',
        array(
            'ai_prompt'
        ),
        $args
    );

}


add_action(
    'init',
    'isago_register_ai_prompt_category_taxonomy',
    20
);