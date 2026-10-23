<?php

if (!defined('ABSPATH')) {
    exit;
}


get_header();


/*=========================================================
CURRENT CATEGORY
=========================================================*/

$category =
    get_queried_object();


if (
    !$category
    ||
    is_wp_error($category)
    ||
    $category->taxonomy !== 'ai_tool_category'
) {

    get_footer();

    return;

}


/*=========================================================
CATEGORY DATA
=========================================================*/

$category_id =
    absint(
        $category->term_id
    );


$category_name =
    $category->name;


$category_description =
    $category->description;


/*=========================================================
CATEGORY ICON
=========================================================*/

$category_icon =
    isago_get_ai_tool_category_icon(
        $category_id
    );


if (
    empty(
        $category_icon
    )
) {

    $category_icon =
        'fa-solid fa-layer-group';

}


/*=========================================================
GET CONNECTED AI PLATFORMS
=========================================================*/

$ai_tools =
    isago_get_ai_tool_category_platforms(
        $category_id
    );


/*=========================================================
TOOL COUNT
=========================================================*/

$tool_count =
    count(
        $ai_tools
    );


/*=========================================================
AI TOOLS DIRECTORY URL
=========================================================*/

$ai_tools_url =
    home_url(
        '/ai-tools/'
    );


/*=========================================================
ALL AI TOOL CATEGORIES URL
=========================================================*/

$categories_url =
    home_url(
        '/ai-tool-categories/'
    );


/*=========================================================
TOTAL AI TOOL CATEGORY COUNT
=========================================================*/

$total_categories =
    wp_count_terms([
        'taxonomy' =>
            'ai_tool_category',

        'hide_empty' =>
            false,
    ]);


$total_categories =
    is_wp_error(
        $total_categories
    )
        ? 0
        : (int) $total_categories;

?>


<main
    class="ai-platform-page"
>


    <!-- =================================================
    HERO
    ================================================= -->

    <?php

    get_template_part(
        'template-parts/ai-tools/category/hero',
        null,
        [

            'category' =>
                $category,

            'category_id' =>
                $category_id,

            'category_name' =>
                $category_name,

            'category_description' =>
                $category_description,

            'category_icon' =>
                $category_icon,

            'tool_count' =>
                $tool_count,

            'total_categories' =>
                $total_categories,

            'ai_tools_url' =>
                $ai_tools_url,

            'categories_url' =>
                $categories_url,

        ]
    );

    ?>


    <!-- =================================================
    RESULTS
    ================================================= -->

    <?php

    get_template_part(
        'template-parts/ai-tools/category/tool-grid',
        null,
        [

            'category' =>
                $category,

            'category_id' =>
                $category_id,

            'category_name' =>
                $category_name,

            'category_icon' =>
                $category_icon,

            'tool_count' =>
                $tool_count,

            'ai_tools' =>
                $ai_tools,

            'ai_tools_url' =>
                $ai_tools_url,

        ]
    );

    ?>


    <!-- =================================================
    BACK TO CATEGORIES
    ================================================= -->

    <?php

    get_template_part(
        'template-parts/ai-tools/category/back-to-categories',
        null,
        [

            'categories_url' =>
                $categories_url,

        ]
    );

    ?>


</main>


<?php

get_footer();