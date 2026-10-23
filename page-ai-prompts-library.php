<?php

if (!defined('ABSPATH')) {
    exit;
}


get_header();


/*=========================================================
FILTER VALUES
=========================================================*/

$selected_category = isset(
    $_GET['prompt_category']
)
    ? sanitize_key(
        wp_unslash(
            $_GET['prompt_category']
        )
    )
    : '';


$selected_platform = isset(
    $_GET['prompt_platform']
)
    ? absint(
        $_GET['prompt_platform']
    )
    : 0;


$search = isset(
    $_GET['prompt_search']
)
    ? sanitize_text_field(
        wp_unslash(
            $_GET['prompt_search']
        )
    )
    : '';


/*=========================================================
CURRENT PAGE
=========================================================*/

$current_page = max(
    1,
    get_query_var('paged'),
    get_query_var('page')
);


/*=========================================================
DIRECTORY
=========================================================*/

$directory =
    isago_get_ai_prompt_directory(
        $selected_category,
        $selected_platform,
        $search,
        $current_page
    );


/*=========================================================
ALL PROMPT COUNT
=========================================================*/

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


/*=========================================================
PAGE ARGS
=========================================================*/

$ai_prompt_page_args = array(

    'selected_category' =>
        $directory[
            'selected_category'
        ],

    'selected_platform' =>
        $directory[
            'selected_platform'
        ],

    'search' =>
        $directory[
            'search'
        ],

    'prompt_categories' =>
        $directory[
            'prompt_categories'
        ],

    'prompt_platforms' =>
        $directory[
            'prompt_platforms'
        ],

    'platform_types' =>
        $directory[
            'platform_types'
        ],

    'prompts' =>
        $directory[
            'prompts'
        ],

    'total_prompts' =>
        $directory[
            'total_prompts'
        ],

    'all_prompts_count' =>
        $all_prompts_count,

    'prompt_query' =>
        $directory[
            'prompt_query'
        ],

    'current_page' =>
        $directory[
            'current_page'
        ],

    'prompts_per_page' =>
        $directory[
            'prompts_per_page'
        ],

);


?>


<main class="ai-platform-page">


    <?php


    /*=====================================================
    HERO
    =====================================================*/

    get_template_part(
        'template-parts/ai-prompts/browse-ai-prompts/hero',
        null,
        $ai_prompt_page_args
    );


    /*=====================================================
    DISCOVERY
    =====================================================*/

    get_template_part(
        'template-parts/ai-prompts/browse-ai-prompts/discovery',
        null,
        $ai_prompt_page_args
    );


    /*=====================================================
    RESULTS
    =====================================================*/

    get_template_part(
        'template-parts/ai-prompts/browse-ai-prompts/results',
        null,
        $ai_prompt_page_args
    );


    ?>


</main>


<?php

get_footer();