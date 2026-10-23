<?php

if (!defined('ABSPATH')) {
    exit;
}


/*
|--------------------------------------------------------------------------
| AI TOOL CATEGORY TAXONOMY
|--------------------------------------------------------------------------
|
| AI Tool Categories are separate from AI Tools.
|
| Example:
|
| AI Tool:
|     ChatGPT
|
| Platform Type:
|     LLMs
|
| AI Tool Categories:
|     Writing & Editing
|     Coding & Development
|     Business & Productivity
|     Research & Data Analysis
|
*/


/*=========================================================
REGISTER AI TOOL CATEGORY TAXONOMY
=========================================================*/

add_action(
    'init',
    'isago_register_ai_tool_category_taxonomy',
    20
);


function isago_register_ai_tool_category_taxonomy()
{

    $labels = [

        'name' =>
            __('AI Tool Categories', 'isago'),

        'singular_name' =>
            __('AI Tool Category', 'isago'),

        'search_items' =>
            __('Search AI Tool Categories', 'isago'),

        'all_items' =>
            __('All AI Tool Categories', 'isago'),

        'parent_item' =>
            __('Parent AI Tool Category', 'isago'),

        'parent_item_colon' =>
            __('Parent AI Tool Category:', 'isago'),

        'edit_item' =>
            __('Edit AI Tool Category', 'isago'),

        'update_item' =>
            __('Update AI Tool Category', 'isago'),

        'add_new_item' =>
            __('Add New AI Tool Category', 'isago'),

        'new_item_name' =>
            __('New AI Tool Category Name', 'isago'),

        'menu_name' =>
            __('AI Tool Categories', 'isago'),

        'name_admin_bar' =>
            __('AI Tool Category', 'isago'),

    ];


    $args = [

        /*---------------------------------------------
        LABELS
        ---------------------------------------------*/

        'labels' =>
            $labels,


        /*---------------------------------------------
        HIERARCHY
        ---------------------------------------------*/

        'hierarchical' =>
            true,


        /*---------------------------------------------
        PUBLIC
        ---------------------------------------------*/

        'public' =>
            true,

        'publicly_queryable' =>
            true,


        /*---------------------------------------------
        ADMIN
        ---------------------------------------------*/

        'show_ui' =>
            true,

        'show_admin_column' =>
            false,

        'show_in_nav_menus' =>
            true,

        'show_tagcloud' =>
            false,


        /*---------------------------------------------
        REST
        ---------------------------------------------*/

        'show_in_rest' =>
            true,


        /*---------------------------------------------
        URL
        ---------------------------------------------*/

        'rewrite' => [

            'slug' =>
                'ai-tools-categories',

            'with_front' =>
                false,

            'hierarchical' =>
                true,

        ],


        /*---------------------------------------------
        QUERY VARIABLE
        ---------------------------------------------*/

        'query_var' =>
            'ai_tool_category',


        /*---------------------------------------------
        OBJECT TYPES
        ---------------------------------------------*/

        'object_type' => [

            'post',
            'isago_video',

        ],


        /*---------------------------------------------
        CAPABILITIES
        ---------------------------------------------*/

        'capabilities' => [

            'manage_terms' =>
                'manage_categories',

            'edit_terms' =>
                'manage_categories',

            'delete_terms' =>
                'manage_categories',

            'assign_terms' =>
                'edit_posts',

        ],

    ];


    register_taxonomy(
        'ai_tool_category',
        [
            'post',
            'isago_video',
        ],
        $args
    );

}