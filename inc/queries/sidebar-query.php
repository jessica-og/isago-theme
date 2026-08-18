<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
Single Post
=========================================================*/

function isago_single_post_query($id)
{
    return isago_query([

        'p' => absint($id),

        'posts_per_page' => 1

    ]);
}


/*=========================================================
Popular Posts
=========================================================*/

function isago_popular_posts_query($count = 5)
{
    return isago_query([

        'posts_per_page' => absint($count),

        'orderby' => 'comment_count',

    ]);
}


/*=========================================================
Sidebar Editorial Posts
=========================================================*/

function isago_category_sidebar_posts(
    $category_slug = '',
    $count = 8
) {

    if (empty($category_slug)) {

        return new WP_Query();

    }


    $category = get_category_by_slug(
        sanitize_title($category_slug)
    );


    /*
    |--------------------------------------------------------------------------
    | Only allow subcategories
    |--------------------------------------------------------------------------
    */

    if (
        !$category ||
        (int) $category->parent === 0
    ) {

        return new WP_Query();

    }


    /*
    |--------------------------------------------------------------------------
    | Selected subcategory only
    |--------------------------------------------------------------------------
    */

    return isago_query([

        'category__in' => [
            (int) $category->term_id
        ],

        'posts_per_page' => absint($count),

        'ignore_sticky_posts' => true,

        'orderby' => 'date',

        'order' => 'DESC',

        'tax_query' => [

            [

                'taxonomy' => 'category',

                'field' => 'term_id',

                'terms' => $category->term_id,

                'include_children' => false

            ]

        ]

    ]);

}


/*=========================================================
Popular Sidebar Videos
=========================================================*/

function isago_popular_sidebar_videos($count = 3)
{

    /*
    |--------------------------------------------------------------------------
    | Find AI Tutorials
    |--------------------------------------------------------------------------
    */

    $category = get_term_by(
        'name',
        'AI Tutorials',
        'category'
    );


    /*
    |--------------------------------------------------------------------------
    | AI Tutorials category does not exist
    |--------------------------------------------------------------------------
    */

    if (!$category) {

        return new WP_Query();

    }


    /*
    |--------------------------------------------------------------------------
    | Popular Videos
    |--------------------------------------------------------------------------
    |
    | Videos are ordered by _isago_video_views.
    |
    | The meta_query keeps videos with no views as well.
    | Those videos are treated as having 0 views.
    |
    |--------------------------------------------------------------------------
    */

    return new WP_Query([

        'post_type' => 'isago_video',

        'post_status' => 'publish',

        'posts_per_page' => absint($count),

        'ignore_sticky_posts' => true,

        'tax_query' => [

            [

                'taxonomy' => 'category',

                'field' => 'term_id',

                'terms' => $category->term_id,

                'include_children' => false

            ]

        ],

        'meta_query' => [

            'relation' => 'OR',

            [

                'key' => '_isago_video_views',

                'compare' => 'EXISTS'

            ],

            [

                'key' => '_isago_video_views',

                'compare' => 'NOT EXISTS'

            ]

        ],

        'orderby' => [

            'video_views' => 'DESC',

            'date' => 'DESC'

        ],

        'meta_key' => '_isago_video_views'

    ]);

}