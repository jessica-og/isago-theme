<?php

if (!defined('ABSPATH')) {
    exit;
}


/*
|--------------------------------------------------------------------------
| Homepage Video Query
|--------------------------------------------------------------------------
|
| Supports three homepage video sources:
|
| 1. Blog
| 2. AI Prompts
| 3. AI Tools
|
| The meaning of $source depends on $type:
|
| Blog:
|     Category slug
|
| AI Prompt:
|     AI Prompt Category slug
|
| AI Tool:
|     AI Tool Category term ID
|
|--------------------------------------------------------------------------
*/


function isago_get_homepage_videos(
    $type = 'blog',
    $source = '',
    $posts = 3
) {

    /*
    |--------------------------------------------------------------------------
    | Sanitize content type
    |--------------------------------------------------------------------------
    */

    $type = sanitize_key($type);


    /*
    |--------------------------------------------------------------------------
    | Allowed content types
    |--------------------------------------------------------------------------
    */

    $allowed_types = [

        'blog',
        'ai_prompt',
        'ai_tool'

    ];


    if (
        !in_array(
            $type,
            $allowed_types,
            true
        )
    ) {

        $type = 'blog';

    }


    /*
    |--------------------------------------------------------------------------
    | Number of posts
    |--------------------------------------------------------------------------
    */

    $posts = absint($posts);


    if ($posts < 1) {

        $posts = 3;

    }


    /*
    |--------------------------------------------------------------------------
    | Base Query
    |--------------------------------------------------------------------------
    */

    $args = [

        'post_type' => 'isago_video',

        'post_status' => 'publish',

        'posts_per_page' => $posts,

        'ignore_sticky_posts' => true,

        'orderby' => 'date',

        'order' => 'DESC',

    ];


    /*
    |--------------------------------------------------------------------------
    | BLOG
    |--------------------------------------------------------------------------
    |
    | $source = native WordPress category slug.
    |
    |--------------------------------------------------------------------------
    */

    if ($type === 'blog') {

        /*
        ----------------------------------------------------------
        No category selected
        ----------------------------------------------------------
        */

        if (
            empty($source)
            ||
            $source === '__all'
        ) {

            return new WP_Query($args);

        }


        /*
        ----------------------------------------------------------
        Find category
        ----------------------------------------------------------
        */

        $category = get_category_by_slug(
            sanitize_title($source)
        );


        /*
        ----------------------------------------------------------
        Category does not exist
        ----------------------------------------------------------
        */

        if (!$category) {

            return new WP_Query();

        }


        /*
        ----------------------------------------------------------
        Category query
        ----------------------------------------------------------
        */

        $args['tax_query'] = [

            [

                'taxonomy' => 'category',

                'field' => 'term_id',

                'terms' => $category->term_id,

                'include_children' => true,

            ]

        ];

    }


    /*
    |--------------------------------------------------------------------------
    | AI PROMPT
    |--------------------------------------------------------------------------
    |
    | $source = ai_prompt_category slug.
    |
    |--------------------------------------------------------------------------
    */

    elseif ($type === 'ai_prompt') {

        /*
        ----------------------------------------------------------
        No category selected
        ----------------------------------------------------------
        */

        if (
            empty($source)
            ||
            $source === '__all'
        ) {

            return new WP_Query($args);

        }


        /*
        ----------------------------------------------------------
        Find AI Prompt category
        ----------------------------------------------------------
        */

        $prompt_category = get_term_by(

            'slug',

            sanitize_title($source),

            'ai_prompt_category'

        );


        /*
        ----------------------------------------------------------
        Category does not exist
        ----------------------------------------------------------
        */

        if (
            !$prompt_category
            ||
            is_wp_error($prompt_category)
        ) {

            return new WP_Query();

        }


        /*
        ----------------------------------------------------------
        AI Prompt category query
        ----------------------------------------------------------
        */

        $args['tax_query'] = [

            [

                'taxonomy' => 'ai_prompt_category',

                'field' => 'term_id',

                'terms' => $prompt_category->term_id,

                'include_children' => true,

            ]

        ];

    }


    /*
    |--------------------------------------------------------------------------
    | AI TOOL
    |--------------------------------------------------------------------------
    |
    | AI Tool Categories are NOT directly attached to videos.
    |
    | The relationship is:
    |
    | AI Tool Category
    |       ↓
    | ai_platform terms
    |       ↓
    | isago_video
    |
    |--------------------------------------------------------------------------
    */

    elseif ($type === 'ai_tool') {

        $tool_category_id = absint($source);


        /*
        ----------------------------------------------------------
        No category selected
        ----------------------------------------------------------
        */

        if (!$tool_category_id) {

            return new WP_Query($args);

        }


        /*
        ----------------------------------------------------------
        Get AI Platforms belonging to this Tool Category
        ----------------------------------------------------------
        */

        $platforms =
            isago_get_ai_tool_category_platforms(
                $tool_category_id
            );


        /*
        ----------------------------------------------------------
        No platforms found
        ----------------------------------------------------------
        */

        if (
            empty($platforms)
            ||
            !is_array($platforms)
        ) {

            return new WP_Query();

        }


        /*
        ----------------------------------------------------------
        Extract platform IDs
        ----------------------------------------------------------
        */

        $platform_ids = [];


        foreach (
            $platforms as $platform
        ) {

            if (
                $platform instanceof WP_Term
            ) {

                $platform_ids[] =
                    absint(
                        $platform->term_id
                    );

            }

        }


        /*
        ----------------------------------------------------------
        No valid platform IDs
        ----------------------------------------------------------
        */

        if (empty($platform_ids)) {

            return new WP_Query();

        }


        /*
        ----------------------------------------------------------
        Query videos assigned to those AI Platforms
        ----------------------------------------------------------
        */

        $args['tax_query'] = [

            [

                'taxonomy' => 'ai_platform',

                'field' => 'term_id',

                'terms' => $platform_ids,

                'include_children' => true,

                'operator' => 'IN',

            ]

        ];

    }


    /*
    |--------------------------------------------------------------------------
    | Execute Query
    |--------------------------------------------------------------------------
    */

    return new WP_Query($args);

}