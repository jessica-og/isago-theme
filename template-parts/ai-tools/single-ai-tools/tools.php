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

$tools_query =
    $args['tools_query']
    ?? null;

$tools_category_url =
    $args['tools_category_url']
    ?? '';


if (
    !$tools_query
    ||
    is_wp_error($tools_query)
    ||
    !$tools_query->have_posts()
) {
    return;
}

?>


<section
    class="ai-platform-page-content ai-platform-page-content-tools"
>


    <div class="container">


        <div class="ai-platform-page-content-header">


            <div>

                <span
                    class="ai-platform-page-content-kicker"
                >

                    AI TOOLS

                </span>


                <h2>

                    <?php echo esc_html(
                        $platform_name
                    ); ?>

                    Tools

                </h2>

            </div>


            <?php if ($tools_category_url) : ?>

                <a
                    href="<?php echo esc_url(
                        $tools_category_url
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
                $tools_query->have_posts()
            ) : ?>

                <?php
                $tools_query->the_post();
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