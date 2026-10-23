<?php

if (!defined('ABSPATH')) {
    exit;
}


get_header();


/*=========================================================
AI TOOLS PAGE
FILTER VALUES
=========================================================*/

$selected_category = isset(
    $_GET['platform_category']
)
    ? sanitize_key(
        wp_unslash(
            $_GET['platform_category']
        )
    )
    : '';


$search = isset(
    $_GET['platform_search']
)
    ? sanitize_text_field(
        wp_unslash(
            $_GET['platform_search']
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
GET DIRECTORY DATA
=========================================================*/

$directory =
    isago_get_ai_platform_directory(
        $selected_category,
        $search,
        $current_page
    );


/*=========================================================
ALL PLATFORM COUNT
=========================================================*/

$all_platforms_count =
    get_terms(
        [

            'taxonomy' =>
                'ai_platform',

            'hide_empty' =>
                false,

            'fields' =>
                'count'

        ]
    );


if (
    is_wp_error(
        $all_platforms_count  
    )
) {

    $all_platforms_count = 0;

}


$all_platforms_count =
    (int) $all_platforms_count;


/*=========================================================
PAGE VARIABLES
=========================================================*/

$ai_platform_page_args = [

    'selected_category' =>
        $directory[
            'selected_category'
        ],

    'search' =>
        $directory[
            'search'
        ],

    'platform_categories' =>
        $directory[
            'platform_categories'
        ],

    'platforms' =>
        $directory[
            'platforms'
        ],

    'total_platforms' =>
        $directory[
            'total_platforms'
        ],

    'all_platforms_count' =>
        $all_platforms_count,

    'platform_pagination' =>
        $directory[
            'platform_pagination'
        ],

    'current_page' =>
        $directory[
            'current_page'
        ],

    'platforms_per_page' =>
        $directory[
            'platforms_per_page'
        ]

];

?>

<main class="ai-platform-page">


    <?php

    /*=====================================================
    HERO
    =====================================================*/

    get_template_part(
        'template-parts/ai-tools/browse-ai-tools/hero',
        null,
        $ai_platform_page_args
    );


    /*=====================================================
    DISCOVERY
    =====================================================*/

    get_template_part(
        'template-parts/ai-tools/browse-ai-tools/discovery',
        null,
        $ai_platform_page_args
    );

  
    /*=====================================================
    RESULTS
    =====================================================*/

    get_template_part(
        'template-parts/ai-tools/browse-ai-tools/results',
        null,
        $ai_platform_page_args
    );

    ?>


</main>


<?php

get_footer();