<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
RELATED QUERY
=========================================================*/

$query =
    isago_get_related_posts(
        6
    );


if (
    !$query ||
    !$query->have_posts()
) {
    return;
}


/*=========================================================
CURRENT CONTENT TYPE
=========================================================*/

$is_ai_prompt =
    is_singular('ai_prompt');


/*=========================================================
VIEW ALL URL
=========================================================*/

$view_all_url = '';


/*=========================================================
AI PROMPT
=========================================================*/

if ($is_ai_prompt) {

    $prompt_categories =
        isago_get_ai_prompt_post_categories(
            get_the_ID()
        );


    if (!empty($prompt_categories)) {

        $primary_category =
            $prompt_categories[0];


        /*
         * AI Prompt directory.
         *
         * Uses the category slug for filtering.
         */

        $view_all_url =
            add_query_arg(
                'prompt_category',
                $primary_category->slug,
                home_url(
                    '/ai-prompts-library/'
                )
            );

    } else {

        $view_all_url =
            home_url(
                '/ai-prompts-library/'
            );

    }

}


/*=========================================================
NORMAL POST
=========================================================*/

else {

    $categories =
        get_the_category(
            get_the_ID()
        );


    if (!empty($categories)) {

        $category =
            $categories[0];


        $category_url =
            get_category_link(
                $category->term_id
            );


        if (
            !is_wp_error(
                $category_url
            )
        ) {

            $view_all_url =
                $category_url;

        }

    }

}

?>


<section class="related-posts">


    <!-- =================================================
         SECTION HEADING
         ================================================= -->

    <div class="section-heading">

        <h2>

            <?php

            echo esc_html(

                $is_ai_prompt

                    ? 'Related Prompts'

                    : 'You May Also Like'

            );

            ?>

        </h2>


        <?php if ($view_all_url) : ?>

            <a
                href="<?php echo esc_url($view_all_url); ?>"
            >

                View All

            </a>

        <?php endif; ?>

    </div>


    <!-- =================================================
         RELATED GRID
         ================================================= -->

    <div class="isago-grid-layout">


        <?php

        while (
            $query->have_posts()
        ) :

            $query->the_post();


            /*=================================================
            AI PROMPT CARD
            =================================================*/

            if (
                get_post_type(
                    get_the_ID()
                ) === 'ai_prompt'
            ) {

                $prompt =
                    get_post();


                get_template_part(

                    'template-parts/ai-prompts/components/card',

                    null,

                    array(

                        'prompt' =>
                            $prompt

                    )

                );


            }


            /*=================================================
            NORMAL POST CARD
            =================================================*/

            else {

                get_template_part(

                    'template-parts/post/cards/related-card'

                );

            }


        endwhile;


        wp_reset_postdata();

        ?>


    </div>


</section>