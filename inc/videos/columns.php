<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
|--------------------------------------------------------------------------
| Admin Columns
|--------------------------------------------------------------------------
*/

add_filter(

    'manage_isago_video_posts_columns',

    function ($columns) {

        return [

            'cb'        => $columns['cb'],

            'thumbnail' => 'Thumbnail',

            'title'     => 'Title',

            'category'  => 'Category',

            'duration'  => 'Duration',

            'date'      => 'Date'

        ];

    }

);

add_action(

    'manage_isago_video_posts_custom_column',

    function ($column, $post_id) {

        switch ($column) {

            case 'thumbnail':

                if (has_post_thumbnail($post_id)) {

                    echo get_the_post_thumbnail(

                        $post_id,

                        [80,45]

                    );

                }

                break;

            case 'category':

                echo get_the_term_list(

                    $post_id,

                    'category',

                    '',

                    ', '

                );

                break;

            case 'duration':

                echo esc_html(

                    get_post_meta(

                        $post_id,

                        '_isago_video_duration',

                        true

                    ) ?: '--'

                );

                break;

        }

    },

    10,

    2

);