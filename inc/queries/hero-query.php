<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
HERO QUERIES
=========================================================*/


/**
 * --------------------------------------------------------
 * FEATURED HERO
 * --------------------------------------------------------
 *
 * The main featured hero remains a manually selected
 * WordPress post/video.
 *
 * @param int $id
 *
 * @return WP_Query
 */
function isago_get_featured_hero($id)
{

    return isago_query([

        'post_type' => [

            'post',
            'isago_video'

        ],

        'p' =>
            absint($id),

        'posts_per_page' =>
            1

    ]);

}


/*=========================================================
LEFT HERO
=========================================================*/

/**
 * Get left hero content.
 *
 * Supported types:
 *
 * blog
 * ai_prompt
 * ai_tool
 *
 * Blog:
 *      $source = category slug
 *
 * AI Prompt:
 *      $source = AI Prompt category slug
 *
 * AI Tool:
 *      $source = AI Tool Category term ID
 *
 * @param string $type
 * @param mixed  $source
 *
 * @return WP_Query
 */
function isago_get_left_hero(
    $type = 'blog',
    $source = ''
) {

    $type =
        sanitize_key(
            $type
        );


    /*
     * Empty source.
     */

    if (
        $source === ''
        ||
        $source === null
    ) {

        return new WP_Query();

    }


    /*=====================================================
    BLOG
    =====================================================*/

    if (
        $type === 'blog'
    ) {

        $slug =
            sanitize_title(
                $source
            );


        if (
            empty($slug)
        ) {

            return new WP_Query();

        }


        $category =
            get_term_by(

                'slug',

                $slug,

                'category'

            );


        if (
            !$category
            ||
            is_wp_error($category)
        ) {

            return new WP_Query();

        }


        return isago_query([

            'post_type' => [

                'post',
                'isago_video'

            ],

            'tax_query' => [

                [

                    'taxonomy' =>
                        'category',

                    'field' =>
                        'term_id',

                    'terms' =>
                        $category->term_id,

                    'include_children' =>
                        true

                ]

            ],

            'orderby' =>
                'modified',

            'order' =>
                'DESC',

            'posts_per_page' =>
                2

        ]);

    }


    /*=====================================================
    AI PROMPTS
    =====================================================*/

    if (
        $type === 'ai_prompt'
    ) {

        $slug =
            sanitize_title(
                $source
            );


        if (
            empty($slug)
        ) {

            return new WP_Query();

        }


        /*
         * Verify that the AI Prompt category exists.
         */

        $category =
            get_term_by(

                'slug',

                $slug,

                'ai_prompt_category'

            );


        if (
            !$category
            ||
            is_wp_error($category)
        ) {

            return new WP_Query();

        }


        return isago_query([

            'post_type' =>
                'ai_prompt',

            'tax_query' => [

                [

                    'taxonomy' =>
                        'ai_prompt_category',

                    'field' =>
                        'term_id',

                    'terms' =>
                        $category->term_id,

                    'include_children' =>
                        true

                ]

            ],

            'orderby' =>
                'modified',

            'order' =>
                'DESC',

            'posts_per_page' =>
                2

        ]);

    }


    /*=====================================================
    AI TOOLS
    =====================================================*/

    if (
        $type === 'ai_tool'
    ) {

        /*
         * AI Tool Categories are not directly assigned
         * to ai_platform terms through a normal taxonomy
         * relationship.
         *
         * They are stored in:
         *
         * _isago_ai_tool_categories
         *
         * on each ai_platform term.
         *
         * Therefore use the existing helper.
         */

        if (
            function_exists(
                'isago_get_ai_tool_category_platforms'
            )
        ) {

            $platforms =
                isago_get_ai_tool_category_platforms(
                    absint($source)
                );

        } else {

            $platforms = [];

        }


        if (
            is_wp_error($platforms)
            ||
            !is_array($platforms)
        ) {

            return new WP_Query();

        }


        /*
         * The Hero templates currently expect a WP_Query
         * and use:
         *
         * the_post()
         *
         * the_title()
         *
         * get_the_ID()
         * etc.
         *
         * AI Tools are WP_Term objects, so convert the
         * selected platform terms into their associated
         * platform posts where possible.
         *
         * However, if your AI Tools are displayed directly
         * from ai_platform terms, they cannot be rendered
         * by the existing hero-post templates.
         *
         * For that reason we return the platform terms
         * through a custom query-like object only if a
         * compatible platform post relationship exists.
         */


        /*
         * Get platform term IDs.
         */

        $platform_ids = [];


        foreach (
            $platforms
            as $platform
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


        if (
            empty($platform_ids)
        ) {

            return new WP_Query();

        }


        /*
         * Find content associated with these AI platforms.
         *
         * AI platform terms are attached to AI Prompt,
         * post and video content where applicable.
         *
         * For Hero, use the actual associated content.
         */

        return isago_query([

            'post_type' => [

                'post',
                'isago_video',
                'ai_prompt'

            ],

            'tax_query' => [

                [

                    'taxonomy' =>
                        'ai_platform',

                    'field' =>
                        'term_id',

                    'terms' =>
                        $platform_ids,

                    'include_children' =>
                        true

                ]

            ],

            'orderby' =>
                'modified',

            'order' =>
                'DESC',

            'posts_per_page' =>
                2

        ]);

    }


    /*=====================================================
    INVALID TYPE
    =====================================================*/

    return new WP_Query();

}