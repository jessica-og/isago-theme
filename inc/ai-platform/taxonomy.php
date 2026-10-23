<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
REGISTER AI Tool TAXONOMY
=========================================================*/

add_action(
    'init',
    'isago_register_ai_platform_taxonomy',
    5
);  


function isago_register_ai_platform_taxonomy()
{

    $labels = [

        'name' =>
            'AI Tools',

        'singular_name' =>
            'AI Tool',

        'search_items' =>
            'Search AI Tools',

        'all_items' =>
            'All AI Tools',

        'edit_item' =>
            'Edit AI Tool',

        'update_item' =>
            'Update AI Tool',

        'add_new_item' =>
            'Add New AI Tool',

        'new_item_name' =>
            'New AI Tool Name',

        'menu_name' =>
            'AI Tools',

    ];
  

    register_taxonomy(

        'ai_platform',

        [
            'post',
            'isago_video',
            'ai_prompt',
        ],

        [

            /*---------------------------------------------
            LABELS
            ---------------------------------------------*/

            'labels' =>
                $labels,


            /*---------------------------------------------
            TAXONOMY TYPE
            ---------------------------------------------*/

            'hierarchical' =>
                false,


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
                true,

            'show_in_rest' =>
                true,


            /*---------------------------------------------
            URL
            ---------------------------------------------*/

            'rewrite' => [

                'slug' =>
                    'ai-tools',

                'with_front' =>
                    false,

                'hierarchical' =>
                    false,

            ],


            /*---------------------------------------------
            QUERY VARIABLE
            ---------------------------------------------*/

            'query_var' =>
                'ai_platform',


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


            /*---------------------------------------------
            REST
            ---------------------------------------------*/

            'rest_base' =>
                'ai-platforms',

        ]

    );

}