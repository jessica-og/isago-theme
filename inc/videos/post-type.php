<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
ISAGO VIDEO POST TYPE
=========================================================*/

function isago_register_video_post_type()
{

    register_post_type(
        'isago_video',
        [
  
            'labels' => [

                'name'          => 'Videos',
                'singular_name' => 'Video',
                'add_new'       => 'Add Video',
                'add_new_item'  => 'Add New Video',
                'edit_item'     => 'Edit Video',
                'new_item'      => 'New Video',
                'view_item'     => 'View Video',
                'search_items'  => 'Search Videos',
                'not_found'     => 'No videos found',
                'menu_name'     => 'Videos'

            ],

            'public' => true,

            'has_archive' => true,

            'rewrite' => [

                'slug' => 'videos'

            ],

            'show_in_rest' => true,

            'menu_icon' => 'dashicons-video-alt3',

            'supports' => [

                'title',
                'editor',
                'thumbnail',
                'excerpt'

            ],

            'taxonomies' => [

                'category',
                'post_tag',
                'ai_platform'

            ]

        ]
    );


    /*=====================================================
    CATEGORY
    =====================================================*/

    register_taxonomy_for_object_type(
        'category',
        'isago_video'
    );


    /*=====================================================
    TAGS
    =====================================================*/

    register_taxonomy_for_object_type(
        'post_tag',
        'isago_video'
    );


    /*=====================================================
    AI PLATFORM
    =====================================================*/

    register_taxonomy_for_object_type(
        'ai_platform',
        'isago_video'
    );

}


/*=========================================================
REGISTER
=========================================================*/

add_action(
    'init',
    'isago_register_video_post_type'
);