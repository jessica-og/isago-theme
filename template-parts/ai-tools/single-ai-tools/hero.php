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

$platform_url =
    $args['platform_url']
    ?? '';

$platform_description =
    $args['platform_description']
    ?? '';

$platform_category =
    $args['platform_category']
    ?? '';

$platform_image_url =
    $args['platform_image_url']
    ?? '';

$platform_initial =
    $args['platform_initial']
    ?? '';

$total_content =
    absint(
        $args['total_content']
        ?? 0
    );

$prompt_count =
    absint(
        $args['prompt_count']
        ?? 0
    );

$tool_count =
    absint(
        $args['tool_count']
        ?? 0
    );

?>


<section class="ai-platform-page-hero">


    <div
        class="ai-platform-page-hero-glow"
        aria-hidden="true"
    ></div>


    <div
        class="ai-platform-page-grid-pattern"
        aria-hidden="true"
    ></div>


    <div class="container">

   <?php

        get_template_part(
            'template-parts/common/breadcrumb'
        );

        ?>
        <div class="ai-platform-page-hero-layout">


            <!--=================================================
            HERO COPY
            =================================================-->

            <div class="ai-platform-page-hero-copy">


                <div class="ai-platform-page-eyebrow">

                    <span
                        class="ai-platform-page-eyebrow-dot"
                        aria-hidden="true"
                    ></span>

                    AI Platform

                </div>


                <?php if ($platform_category) : ?>

                    <div class="ai-platform-page-category">

                        <i
                            class="fa-solidfa-layer-group "
                            aria-hidden="true"
                        ></i>

                        <?php echo esc_html(
                            $platform_category
                        ); ?>

                    </div>

                <?php endif; ?>


                <h1 class="ai-platform-page-title">

                    <?php echo esc_html(
                        $platform_name
                    ); ?>

                </h1>


                <?php if ($platform_description) : ?>

                    <div class="ai-platform-page-description">

                        <?php
                        echo wp_kses_post(
                          (
                                $platform_description
                            )
                        );
                        ?>

                    </div>

                <?php endif; ?>


                <!--=================================================
                STATS
                =================================================-->

                <div class="ai-platform-page-stats">


                    <div class="ai-platform-page-stat">

                        <strong>

                            <?php echo esc_html(
                                $total_content
                            ); ?>

                        </strong>

                        <span>

                            Articles

                        </span>

                    </div>


                    <div
                        class="ai-platform-page-stat-divider"
                        aria-hidden="true"
                    ></div>


                    <div class="ai-platform-page-stat">

                        <strong>

                            <?php echo esc_html(
                                $prompt_count
                            ); ?>

                        </strong>

                        <span>

                            Prompts

                        </span>

                    </div>


                    <div
                        class="ai-platform-page-stat-divider"
                        aria-hidden="true"
                    ></div>


                    <div class="ai-platform-page-stat">

                        <strong>

                            <?php echo esc_html(
                                $tool_count
                            ); ?>

                        </strong>

                        <span>

                            Tools

                        </span>

                    </div>


                </div>


                <?php if ($platform_url) : ?>

                    <a
                        href="<?php echo esc_url(
                            $platform_url
                        ); ?>"
                       class="isago-ai-tool-categories-hero-link"
                        target="_blank"
                        rel="noopener noreferrer"
                    >

                        Visit
                        <?php echo esc_html(
                            $platform_name
                        ); ?>

                        <i
                            class="fa-solid fa-arrow-up-right-from-square"
                            aria-hidden="true"
                        ></i>

                    </a>

                <?php endif; ?>


            </div>


            <!--=================================================
            HERO VISUAL
            =================================================-->

            <div class="ai-platform-page-hero-visual">


                <div
                    class="ai-platform-page-visual-glow"
                    aria-hidden="true"
                ></div>


                <div
                    class="ai-platform-page-orbit ai-platform-page-orbit-one"
                    aria-hidden="true"
                ></div>


                <div
                    class="ai-platform-page-orbit ai-platform-page-orbit-two"
                    aria-hidden="true"
                ></div>


                <div
                    class="ai-platform-page-orbit ai-platform-page-orbit-three"
                    aria-hidden="true"
                ></div>


                <div class="ai-platform-page-platform-card">


                    <div
                        class="ai-platform-page-platform-card-shine"
                        aria-hidden="true"
                    ></div>


                    <div class="ai-platform-page-platform-image">


                        <?php if ($platform_image_url) : ?>

                            <img
                                src="<?php echo esc_url(
                                    $platform_image_url
                                ); ?>"
                                alt="<?php echo esc_attr(
                                    $platform_name
                                ); ?>"loading="lazy"
                            >

                        <?php else : ?>

                            <span
                                class="ai-platform-page-platform-fallback"
                                aria-hidden="true"
                            >

                                <?php echo esc_html(
                                    $platform_initial
                                ); ?>

                            </span>

                        <?php endif; ?>


                    </div>


                    <div class="ai-platform-page-platform-name">

                        <?php echo esc_html(
                            $platform_name
                        ); ?>

                    </div>


                    <div class="ai-platform-page-platform-type">

                        <span
                            aria-hidden="true"
                        ></span>

                        AI Platform

                    </div>


                </div>


            </div>


        </div>


    </div>


</section>