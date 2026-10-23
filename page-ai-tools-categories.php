<?php

if (!defined('ABSPATH')) {
    exit;
}

get_header();


/*=========================================================
GET AI TOOL CATEGORIES
=========================================================*/

$ai_tool_categories =
    isago_get_ai_tool_categories([
        'hide_empty' => false,
        'orderby'    => 'name',
        'order'      => 'ASC',
    ]);


/*=========================================================
TOTAL CATEGORY COUNT
=========================================================*/

$total_categories =
    count(
        $ai_tool_categories
    );


/*=========================================================
AI TOOLS URL
=========================================================*/

$ai_tools_url =
    home_url(
        '/ai-tools/'
    );


/*=========================================================
TOTAL AI PLATFORM COUNT
=========================================================*/

$all_ai_platforms =
    get_terms([
        'taxonomy'   => 'ai_platform',
        'hide_empty' => false,
    ]);


$total_ai_tools =
    0;


if (
    !is_wp_error(
        $all_ai_platforms
    )
    &&
    !empty(
        $all_ai_platforms
    )
) {

    $total_ai_tools =
        count(
            $all_ai_platforms
        );

}

?>


<main
    class="isago-ai-tool-categories"
>


    <!-- =================================================
    HERO
    ================================================= -->

    <?php

    get_template_part(
        'template-parts/ai-tools/categories/hero',
        null,
        [
            'ai_tools_url'    => $ai_tools_url,
            'total_categories' => $total_categories,
            'total_ai_tools'   => $total_ai_tools,
        ]
    );

    ?>


    <!-- =================================================
    MAIN CONTENT
    ================================================= -->

    <section
        class="isago-ai-tool-categories-content"
    >

        <div class="container">


            <!-- =================================================
            BROWSE BY CATEGORY
            ================================================= -->

            <?php

            get_template_part(
                'template-parts/ai-tools/categories/category-grid',
                null,
                [
                    'ai_tool_categories' =>
                        $ai_tool_categories,

                    'total_categories' =>
                        $total_categories,
                ]
            );

            ?>


            <!-- =================================================
            BROWSE ALL CTA
            ================================================= -->

            <?php

            get_template_part(
                'template-parts/ai-tools/categories/cta',
                null,
                [
                    'ai_tools_url' =>
                        $ai_tools_url,
                ]
            );

            ?>


        </div>

    </section>


</main>


<?php

get_footer();