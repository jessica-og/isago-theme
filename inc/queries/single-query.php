<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
WATCH MORE VIDEOS
=========================================================*/

function isago_get_single_videos($posts = 5)
{

    return new WP_Query([

        'post_type' => 'isago_video',

        'posts_per_page' => absint($posts),

        'post_status' => 'publish',

        'ignore_sticky_posts' => true,

        'orderby' => 'date',

        'order' => 'DESC'

    ]);

}


/*=========================================================
RELATED POSTS / PROMPTS
=========================================================*/

/**
 * Get related content for the current single post.
 *
 * AI Prompt:
 * - Uses ai_prompt_category
 * - Finds other AI Prompts in the same category
 *
 * Normal Post:
 * - Uses normal WordPress category
 * - Finds other normal posts in the same category
 *
 * The current post is always excluded.
 */
function isago_get_related_posts($posts = 6)
{

    $current_post_id = get_the_ID();

    if (!$current_post_id) {
        return new WP_Query([
            'post__in' => [0],
        ]);
    }


    /*=====================================================
    AI PROMPT
    =====================================================*/

    if (is_singular('ai_prompt')) {

        $categories =
            isago_get_ai_prompt_post_categories(
                $current_post_id
            );


        /*
         * No AI Prompt category.
         */

        if (empty($categories)) {

            return new WP_Query([
                'post__in' => [0],
            ]);

        }


        /*
         * Use the first assigned AI Prompt category.
         */

        $category_id =
            absint(
                $categories[0]->term_id
            );


        if (!$category_id) {

            return new WP_Query([
                'post__in' => [0],
            ]);

        }


        return new WP_Query([

            'post_type' => 'ai_prompt',

            'post_status' => 'publish',

            'posts_per_page' => absint($posts),

            'post__not_in' => [
                $current_post_id
            ],

            'ignore_sticky_posts' => true,

            'orderby' => 'date',

            'order' => 'DESC',

            'tax_query' => [

                [

                    'taxonomy' =>
                        'ai_prompt_category',

                    'field' =>
                        'term_id',

                    'terms' => [
                        $category_id
                    ],

                    'include_children' => true,

                    'operator' => 'IN',

                ]

            ],

        ]);

    }


    /*=====================================================
    NORMAL POST
    =====================================================*/

    $categories =
        get_the_category(
            $current_post_id
        );


    /*
     * No normal category.
     */

    if (empty($categories)) {

        return new WP_Query([
            'post__in' => [0],
        ]);

    }


    /*
     * Use the first assigned category.
     */

    $category_id =
        absint(
            $categories[0]->term_id
        );


    if (!$category_id) {

        return new WP_Query([
            'post__in' => [0],
        ]);

    }


    return new WP_Query([

        'post_type' => 'post',

        'post_status' => 'publish',

        'posts_per_page' => absint($posts),

        'post__not_in' => [
            $current_post_id
        ],

        'ignore_sticky_posts' => true,

        'orderby' => 'date',

        'order' => 'DESC',

        'tax_query' => [

            [

                'taxonomy' => 'category',

                'field' => 'term_id',

                'terms' => [
                    $category_id
                ],

                'include_children' => true,

                'operator' => 'IN',

            ]

        ],

    ]);

}










/*=========================================================
AI NEWS
=========================================================*/

/**
 * Get latest AI News posts.
 *
 * Uses the normal WordPress "AI News" category.
 *
 * Category slug:
 * ai-news
 */
function isago_get_ai_news_posts($posts = 6)
{

    $current_post_id =
        get_the_ID();


    /*
     * Find the AI News category
     * by its slug.
     */

    $ai_news_category =
        get_category_by_slug(
            'ai-news'
        );


    /*
     * AI News category does not exist.
     */

    if (
        !$ai_news_category ||
        is_wp_error($ai_news_category)
    ) {

        return new WP_Query([
            'post__in' => [0],
        ]);

    }


    return new WP_Query([

        'post_type' =>
            'post',

        'post_status' =>
            'publish',

        'posts_per_page' =>
            absint($posts),

        'post__not_in' => [

            absint(
                $current_post_id
            )

        ],

        'ignore_sticky_posts' =>
            true,

        'orderby' =>
            'date',

        'order' =>
            'DESC',

        'tax_query' => [

            [

                'taxonomy' =>
                    'category',

                'field' =>
                    'term_id',

                'terms' => [

                    absint(
                        $ai_news_category->term_id
                    )

                ],

                'include_children' =>
                    true,

                'operator' =>
                    'IN',

            ]

        ],

    ]);

}


/*=========================================================
AI NEWS CATEGORY URL
=========================================================*/

/**
 * Get the AI News category URL.
 */
function isago_get_ai_news_category_url()
{

    $ai_news_category =
        get_category_by_slug(
            'ai-news'
        );


    if (
        !$ai_news_category ||
        is_wp_error($ai_news_category)
    ) {

        return '';

    }


    $url =
        get_category_link(
            $ai_news_category->term_id
        );


    if (
        is_wp_error($url)
    ) {

        return '';

    }


    return $url;

}










