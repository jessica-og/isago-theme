<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
ISAGO PAGINATION
=========================================================*/

/**
 * Isago pagination helper.
 *
 * Supports:
 *
 * - Normal WordPress queries
 * - Custom term directories
 * - AJAX directories
 * - Explicit current page
 * - Explicit query arguments
 *
 * @param WP_Query|object|null $query
 * @param int                 $current_page
 * @param array               $add_args
 *
 * @return void
 */
function isago_pagination(
    $query = null,
    $current_page = 0,
    $add_args = []
) {

    /*=====================================================
    FALLBACK QUERY
    =====================================================*/

    if (!$query) {

        global $wp_query;

        $query = $wp_query;

    }


    /*=====================================================
    CHECK QUERY
    =====================================================*/

    if (
        !$query
        ||
        !isset($query->max_num_pages)
    ) {

        return;

    }


    /*=====================================================
    TOTAL PAGES
    =====================================================*/

    $total_pages =
        (int) $query->max_num_pages;


    if (
        $total_pages <= 1
    ) {

        return;

    }


    /*=====================================================
    CURRENT PAGE
    =====================================================*/

    if (
        !$current_page
    ) {

        $current_page = max(
            1,
            get_query_var('paged'),
            get_query_var('page')
        );

    }


    $current_page =
        max(
            1,
            min(
                (int) $current_page,
                $total_pages
            )
        );


    /*=====================================================
    CURRENT URL
    =====================================================*/

    /*
     * Use the actual directory/page URL.
     *
     * We intentionally do NOT use the current
     * paginated URL as the base.
     */

    $base_url =
        get_permalink();


    if (
        !$base_url
    ) {

        $base_url =
            home_url('/');

    }


    /*=====================================================
    REMOVE EXISTING PAGINATION
    =====================================================*/

    $base_url =
        remove_query_arg(
            'paged',
            $base_url
        );


    /*=====================================================
    QUERY ARGUMENTS
    =====================================================*/

    $pagination_args = [];


    /*
     * Existing URL query parameters.
     *
     * This allows pagination to preserve
     * filters/search values.
     */

    $existing_args = [];


    foreach (
        [
            'platform_category',
            'platform_search',
            'prompt_category',
            'prompt_platform',
            'prompt_search',
            'filter',
            'sort',
            's'
        ] as $key
    ) {

        if (
            isset($_GET[$key])
            &&
            $_GET[$key] !== ''
        ) {

            $existing_args[$key] =
                sanitize_text_field(
                    wp_unslash(
                        $_GET[$key]
                    )
                );

        }

    }


    /*
     * Existing URL arguments first.
     */

    if (
        !empty($existing_args)
    ) {

        $pagination_args =
            array_merge(
                $pagination_args,
                $existing_args
            );

    }


    /*
     * Explicit arguments supplied
     * by the directory.
     *
     * These take priority.
     */

    if (
        !empty($add_args)
        &&
        is_array($add_args)
    ) {

        foreach (
            $add_args as $key => $value
        ) {

            if (
                $value === ''
                ||
                $value === null
            ) {

                continue;

            }


            $pagination_args[$key] =
                $value;

        }

    }


    /*=====================================================
    PAGINATION LINKS
    =====================================================*/

    echo '<nav class="isago-pagination" aria-label="Pagination">';


    echo paginate_links(

        [

            /*
             * Always generate:
             *
             * /ai-tools/?paged=2
             *
             * instead of relying on WordPress
             * pretty pagination rewriting.
             */

            'base' =>
                esc_url_raw(
                    add_query_arg(
                        'paged',
                        '%#%',
                        $base_url
                    )
                ),

            'format' =>
                '',
  
            'current' =>
                $current_page,

            'total' =>
                $total_pages,

            'mid_size' =>
                1,

            'end_size' =>
                1,

            'prev_text' =>
                '&laquo;',

            'next_text' =>
                'Next',

            'type' =>
                'list',

            'add_args' =>
                $pagination_args,

            'aria_current' =>
                'page'

        ]

    );


    echo '</nav>';

}