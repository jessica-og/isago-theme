<?php

if (!defined('ABSPATH')) {
    exit;
}

get_header();


/*=========================================================
GET AI PROMPT CATEGORIES
=========================================================*/

$ai_prompt_categories =
    get_terms(
        array(

            'taxonomy' =>
                'ai_prompt_category',

            'hide_empty' =>
                false,

            'orderby' =>
                'name',

            'order' =>
                'ASC',

        )
    );


if (
    is_wp_error(
        $ai_prompt_categories
    )
) {

    $ai_prompt_categories =
        array();

}


/*=========================================================
TOTAL CATEGORY COUNT
=========================================================*/

$total_categories =
    count(
        $ai_prompt_categories
    );


/*=========================================================
TOTAL AI PROMPT COUNT
=========================================================*/

$prompt_count_object =
    wp_count_posts(
        'ai_prompt'
    );


$total_prompts = 0;


if (
    $prompt_count_object
    &&
    isset(
        $prompt_count_object->publish
    )
) {

    $total_prompts =
        (int)
        $prompt_count_object->publish;

}


/*=========================================================
AI PROMPTS LIBRARY URL
=========================================================*/

$ai_prompts_url =
    home_url(
        '/ai-prompts-library/'
    );


/*=========================================================
AI TOOLS URL
=========================================================*/

$ai_tools_url =
    home_url(
        '/ai-tools/'
    );

?>


<main
    class="isago-ai-tool-categories"
>


    <?php
    /*
     * HERO
     */

    get_template_part(
        'template-parts/ai-prompts/categories/hero',
        null,
        array(

            'total_categories' =>
                $total_categories,

            'total_prompts' =>
                $total_prompts,

            'ai_prompts_url' =>
                $ai_prompts_url,

            'ai_tools_url' =>
                $ai_tools_url,

        )
    );
    ?>


    <?php
    /*
     * CATEGORY GRID
     */

    get_template_part(
        'template-parts/ai-prompts/categories/category-grid',
        null,
        array(

            'ai_prompt_categories' =>
                $ai_prompt_categories,

            'total_categories' =>
                $total_categories,

        )
    );
    ?>


    <?php
    /*
     * CTA
     */

    get_template_part(
        'template-parts/ai-prompts/categories/cta',
        null,
        array(

            'ai_prompts_url' =>
                $ai_prompts_url,

        )
    );
    ?>


</main>


<?php

get_footer();