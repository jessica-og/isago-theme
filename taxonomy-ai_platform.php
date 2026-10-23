<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
CURRENT AI PLATFORM
=========================================================*/

$platform = get_queried_object();


if (
    !$platform
    ||
    is_wp_error($platform)
    ||
    empty($platform->term_id)
    ||
    $platform->taxonomy !== 'ai_platform'
) {
    return;
}


$platform_id = absint(
    $platform->term_id
);


$platform_name = $platform->name;


/*=========================================================
PLATFORM DATA
=========================================================*/

$platform_url = isago_get_ai_platform_website(
    $platform_id
);


$platform_description = isago_get_ai_platform_description(
    $platform_id
);


$platform_category = isago_get_ai_platform_category(
    $platform_id
);


/*=========================================================
PLATFORM INFORMATION
=========================================================*/

$how_to_use = isago_get_ai_platform_meta(
    $platform_id,
    'how_to_use'
);


$best_for = isago_get_ai_platform_meta(
    $platform_id,
    'best_for'
);


$behavior = isago_get_ai_platform_meta(
    $platform_id,
    'behavior'
);


$tips = isago_get_ai_platform_meta(
    $platform_id,
    'tips'
);


$pricing = isago_get_ai_platform_meta(
    $platform_id,
    'pricing'
);



/*=========================================================
PLATFORM IMAGE
=========================================================*/

$platform_image_id =
    isago_media_get_field(
        'term',
        $platform_id,
        'ai_platform_image'
    );


$platform_image_url = '';


if ($platform_image_id) {

    $platform_image_url =
        isago_media_get_field_image_url(
            'term',
            $platform_id,
            'ai_platform_image',
            'medium'
        );

}


/*=========================================================
CONTENT QUERIES
=========================================================*/

$prompts_query = isago_get_ai_platform_prompts(
    $platform_id,
    6
);


$tools_query = isago_get_ai_platform_tools(
    $platform_id,
    6
);


/*=========================================================
LATEST PLATFORM CONTENT
=========================================================*/

$current_page = max(
    1,
    absint(
        get_query_var('paged')
    ),
    absint(
        get_query_var('page')
    )
);


$latest_query = isago_get_ai_platform_posts(
    $platform_id,
    [
        'posts_per_page' => 9,
        'paged'          => $current_page
    ]
);


/*=========================================================
TOTAL CONTENT
=========================================================*/

$total_content = absint(
    $latest_query->found_posts
);


/*=========================================================
PROMPT COUNT
=========================================================*/

$prompt_count = 0;


if (
    $prompts_query
    &&
    !is_wp_error($prompts_query)
) {

    $prompt_count = absint(
        $prompts_query->found_posts
    );

}


/*=========================================================
TOOL COUNT
=========================================================*/

$tool_count = 0;


if (
    $tools_query
    &&
    !is_wp_error($tools_query)
) {

    $tool_count = absint(
        $tools_query->found_posts
    );

}


/*=========================================================
CATEGORY LINKS
=========================================================*/

$prompts_category =
    isago_get_category_by_slug(
        'ai-prompts'
    );


$prompts_category_url = '';

$tools_category_url = '';


/*=====================================================
AI PROMPTS DIRECTORY
=====================================================*/

if ($prompts_category) {

    $prompts_category_url =
        get_category_link(
            $prompts_category->term_id
        );

}


/*=====================================================
BROWSE ALL AI TOOLS PAGE
=====================================================*/

$tools_page =
    get_page_by_path(
        'ai-tools'
    );


if (
    $tools_page
    &&
    $tools_page->post_status === 'publish'
) {

    $tools_category_url =
        get_permalink(
            $tools_page->ID
        );

}

/*=========================================================
PLATFORM INITIAL
=========================================================*/

$platform_initial =
    function_exists('mb_substr')
        ? mb_strtoupper(
            mb_substr(
                $platform_name,
                0,
                1
            )
        )
        : strtoupper(
            substr(
                $platform_name,
                0,
                1
            )
        );


/*=========================================================
INFORMATION CARDS
=========================================================*/

$information_cards = [

    [
        'title'   => 'How to use ' . $platform_name,
        'content' => $how_to_use
    ],

    [
        'title'   => 'Best for',
        'content' => $best_for
    ],

    [
        'title'   => 'How it works',
        'content' => $behavior
    ],

    [
        'title'   => 'Tips for better results',
        'content' => $tips
    ],

    [
        'title'   => 'Pricing',
        'content' => $pricing
    ]

];


$has_information = false;


foreach ($information_cards as $information_card) {

    if (
        !empty(
            $information_card['content']
        )
    ) {

        $has_information = true;

        break;

    }

}


/*=========================================================
PAGE
=========================================================*/

get_header();

?>


<main class="ai-platform-page">


    <?php
    /*=====================================================
    HERO
    =====================================================*/

    get_template_part(
        'template-parts/ai-tools/single-ai-tools/hero',
        null,
        [
            'platform_name'        => $platform_name,
            'platform_url'         => $platform_url,
            'platform_description' => $platform_description,
            'platform_category'    => $platform_category,
            'platform_image_url'   => $platform_image_url,
            'platform_initial'     => $platform_initial,
            'total_content'        => $total_content,
            'prompt_count'         => $prompt_count,
            'tool_count'           => $tool_count
        ]
    );
    ?>


    <?php
    /*=====================================================
    OVERVIEW
    =====================================================*/

    get_template_part(
        'template-parts/ai-tools/single-ai-tools/overview',
        null,
        [
            'platform_name'    => $platform_name,
            'information_cards' => $information_cards,
            'has_information'  => $has_information
        ]
    );
    ?>


    <?php
    /*=====================================================
    PROMPTS
    =====================================================*/

    get_template_part(
        'template-parts/ai-tools/single-ai-tools/prompts',
        null,
        [
            'platform_name'        => $platform_name,
            'prompts_query'        => $prompts_query,
            'prompts_category_url' => $prompts_category_url
        ]
    );
    ?>


    <?php
    wp_reset_postdata();
    ?>


    <?php
    /*=====================================================
    TOOLS
    =====================================================*/

    get_template_part(
        'template-parts/ai-tools/single-ai-tools/tools',
        null,
        [
            'platform_name'      => $platform_name,
            'tools_query'        => $tools_query,
            'tools_category_url' => $tools_category_url
        ]
    );
    ?>


    <?php
    wp_reset_postdata();
    ?>


    <?php
    /*=====================================================
    LATEST CONTENT
    =====================================================*/

    get_template_part(
        'template-parts/ai-tools/single-ai-tools/latest',
        null,
        [
            'platform_name' => $platform_name,
            'latest_query'  => $latest_query,
            'total_content' => $total_content,
            'current_page'  => $current_page
        ]
    );
    ?>


</main>


<?php

wp_reset_postdata();

get_footer();

?>