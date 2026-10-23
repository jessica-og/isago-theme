<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
AI PROMPTS DIRECTORY AJAX
=========================================================*/

add_action(
    'wp_ajax_isago_ai_prompts',
    'isago_ajax_ai_prompts'
);

add_action(
    'wp_ajax_nopriv_isago_ai_prompts',
    'isago_ajax_ai_prompts'
);


/*=========================================================
AI PROMPTS AJAX HANDLER
=========================================================*/

function isago_ajax_ai_prompts()
{

    /*-----------------------------------------------------
    SECURITY
    -----------------------------------------------------*/

    if (
        !check_ajax_referer(
            'isago_ai_prompts',
            'nonce',
            false
        )
    ) {

        wp_send_json_error(
            [
                'message' =>
                    'Security check failed.'
            ],
            403
        );

    }


    /*-----------------------------------------------------
    INPUT
    -----------------------------------------------------*/

    $selected_category =
        isset(
            $_POST['prompt_category']
        )
            ? sanitize_key(
                wp_unslash(
                    $_POST['prompt_category']
                )
            )
            : '';


    $selected_platform =
        isset(
            $_POST['prompt_platform']
        )
            ? absint(
                $_POST['prompt_platform']
            )
            : 0;


    $search =
        isset(
            $_POST['prompt_search']
        )
            ? sanitize_text_field(
                wp_unslash(
                    $_POST['prompt_search']
                )
            )
            : '';


    $current_page =
        isset(
            $_POST['paged']
        )
            ? absint(
                $_POST['paged']
            )
            : 1;


    if (
        $current_page < 1
    ) {

        $current_page = 1;

    }


    /*-----------------------------------------------------
    DIRECTORY
    -----------------------------------------------------*/

    /*
     * IMPORTANT:
     *
     * Use the exact same directory function used
     * by page-ai-prompts.php.
     *
     * This keeps initial page rendering and AJAX
     * rendering completely synchronized.
     */

    if (
        !function_exists(
            'isago_get_ai_prompt_directory'
        )
    ) {

        wp_send_json_error(
            [
                'message' =>
                    'AI Prompt directory function is not available.'
            ],
            500
        );

    }


    $directory =
        isago_get_ai_prompt_directory(
            $selected_category,
            $selected_platform,
            $search,
            $current_page
        );


    /*-----------------------------------------------------
    VALIDATE DIRECTORY
    -----------------------------------------------------*/

    if (
        !is_array(
            $directory
        )
    ) {

        wp_send_json_error(
            [
                'message' =>
                    'AI Prompt directory returned invalid data.'
            ],
            500
        );

    }


    /*-----------------------------------------------------
    ALL PROMPTS COUNT
    -----------------------------------------------------*/

    $all_prompts_count =
        wp_count_posts(
            'ai_prompt'
        );


    $all_prompts_count =
        (
            $all_prompts_count
            &&
            isset(
                $all_prompts_count->publish
            )
        )
            ? (int)
                $all_prompts_count->publish
            : 0;


    /*-----------------------------------------------------
    PAGE ARGS
    -----------------------------------------------------*/

    $ai_prompt_page_args = [

        'selected_category' =>
            $directory[
                'selected_category'
            ]
            ?? $selected_category,


        'selected_platform' =>
            $directory[
                'selected_platform'
            ]
            ?? $selected_platform,


        'search' =>
            $directory[
                'search'
            ]
            ?? $search,


        'prompt_categories' =>
            $directory[
                'prompt_categories'
            ]
            ?? [],


        'prompt_platforms' =>
            $directory[
                'prompt_platforms'
            ]
            ?? [],


        'platform_types' =>
            $directory[
                'platform_types'
            ]
            ?? [],


        'prompts' =>
            $directory[
                'prompts'
            ]
            ?? [],


        'total_prompts' =>
            $directory[
                'total_prompts'
            ]
            ?? 0,


        'all_prompts_count' =>
            $all_prompts_count,


        'prompt_query' =>
            $directory[
                'prompt_query'
            ]
            ?? null,


        'current_page' =>
            $directory[
                'current_page'
            ]
            ?? $current_page,


        'prompts_per_page' =>
            $directory[
                'prompts_per_page'
            ]
            ?? 12,

    ];


    /*-----------------------------------------------------
    RENDER RESULTS
    -----------------------------------------------------*/

    ob_start();


    /*
     * THIS MUST BE THE SAME RESULTS TEMPLATE
     * USED BY page-ai-prompts.php
     */

    get_template_part(
        'template-parts/ai-prompts/browse-ai-prompts/results',
        null,
        $ai_prompt_page_args
    );


    $html =
        ob_get_clean();


    /*-----------------------------------------------------
    CHECK RENDERED HTML
    -----------------------------------------------------*/

    if (
        trim(
            $html
        ) === ''
    ) {

        wp_send_json_error(
            [
                'message' =>
                    'AI Prompt results template returned empty HTML.'
            ],
            500
        );

    }


    /*-----------------------------------------------------
    CURRENT PAGE
    -----------------------------------------------------*/

    $actual_page =
        isset(
            $directory[
                'current_page'
            ]
        )
            ? absint(
                $directory[
                    'current_page'
                ]
            )
            : $current_page;


    if (
        $actual_page < 1
    ) {

        $actual_page = 1;

    }


    /*-----------------------------------------------------
    MAX PAGES
    -----------------------------------------------------*/

    $max_pages = 0;


    if (
        isset(
            $directory[
                'prompt_query'
            ]
        )
        &&
        $directory[
            'prompt_query'
        ] instanceof WP_Query
    ) {

        $max_pages =
            (int)
            $directory[
                'prompt_query'
            ]->max_num_pages;

    }


    /*-----------------------------------------------------
    RESPONSE
    -----------------------------------------------------*/

    wp_send_json_success(
        [

            'html' =>
                $html,

            'page' =>
                $actual_page,

            'max_pages' =>
                $max_pages,

            'total' =>
                isset(
                    $directory[
                        'total_prompts'
                    ]
                )
                    ? (int)
                        $directory[
                            'total_prompts'
                        ]
                    : 0,

        ]
    );

}