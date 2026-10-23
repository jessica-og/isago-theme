<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
DATA
=========================================================*/

$platform_name =
    $args['platform_name']
    ?? '';

$prompts_query =
    $args['prompts_query']
    ?? null;

$prompts_category_url =
    $args['prompts_category_url']
    ?? '';


if (
    !$prompts_query
    ||
    is_wp_error($prompts_query)
    ||
    !$prompts_query->have_posts()
) {
    return;
}

?>


<section class="ai-platform-page-content">


    <div class="container">


        <div class="ai-platform-page-content-header">


            <div>

                <span
                    class="ai-platform-page-content-kicker"
                >

                    PROMPTS

                </span>


                <h2>

                    Prompts for
                    <?php echo esc_html(
                        $platform_name
                    ); ?>

                </h2>

            </div>


            <?php if ($prompts_category_url) : ?>

                <a
                    href="<?php echo esc_url(
                        $prompts_category_url
                    ); ?>"
                    class="ai-platform-page-section-link"
                >

                    See all

                    <i
                        class="fa-solid fa-arrow-right"
                        aria-hidden="true"
                    ></i>

                </a>

            <?php endif; ?>


        </div>


        <div class="ai-platform-page-article-grid">


            <?php while (
                $prompts_query->have_posts()
            ) : ?>

                <?php
                $prompts_query->the_post();
                ?>


                <article
                    class="ai-platform-page-article"
                >

                    <?php
                    get_template_part(
                        'template-parts/category/cards/post-card'
                    );
                    ?>

                </article>


            <?php endwhile; ?>


        </div>


    </div>


</section>