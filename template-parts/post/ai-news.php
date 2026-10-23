<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
AI NEWS QUERY
=========================================================*/

$query =
    isago_get_ai_news_posts(
        6
    );


if (
    !$query ||
    !$query->have_posts()
) {
    return;
}


/*=========================================================
AI NEWS URL
=========================================================*/

$ai_news_url =
    isago_get_ai_news_category_url();

?>


<section class="related-posts">


    <!-- =================================================
         SECTION HEADING
         ================================================= -->

    <div class="section-heading">


        <h2>

            AI News

        </h2>


        <?php if ($ai_news_url) : ?>

            <a
                href="<?php echo esc_url($ai_news_url); ?>"
            >

                View All

            </a>

        <?php endif; ?>


    </div>


    <!-- =================================================
         AI NEWS GRID
         ================================================= -->

    <div class="isago-grid-layout">


        <?php

        while (
            $query->have_posts()
        ) :

            $query->the_post();


            /*
             * Use the existing normal
             * related post card.
             */

            get_template_part(

                'template-parts/post/cards/related-card'

            );


        endwhile;


        wp_reset_postdata();

        ?>


    </div>


</section>