<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
|--------------------------------------------------------------------------
| Homepage Video Query
|--------------------------------------------------------------------------
|
| Homepage videos are ALWAYS restricted to the
| "AI Tutorials" category.
|
|--------------------------------------------------------------------------
*/

function isago_get_homepage_videos($category = '', $posts = 3)
{

    /*
    ---------------------------------------------------------
    Find AI Tutorials category
    ---------------------------------------------------------
    */

    $video_category = get_term_by(
        'name',
        'AI Tutorials',
        'category'
    );


    /*
    ---------------------------------------------------------
    Category does not exist
    ---------------------------------------------------------
    */

    if (!$video_category) {

        return new WP_Query();

    }


    /*
    ---------------------------------------------------------
    Homepage Video Query
    ---------------------------------------------------------
    */

    $args = [

        'post_type' => 'isago_video',

        'post_status' => 'publish',

        'posts_per_page' => absint($posts),

        'ignore_sticky_posts' => true,

        'orderby' => 'date',

        'order' => 'DESC',

        'tax_query' => [

            [

                'taxonomy' => 'category',

                'field' => 'term_id',

                'terms' => $video_category->term_id,

                /*
                 * Only videos directly assigned to
                 * AI Tutorials.
                 */
                'include_children' => false,

            ]

        ]

    ];


    return new WP_Query($args);

}