<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
VARIABLES
=========================================================*/

$category_name =
    isset($args['category_name'])
        ? $args['category_name']
        : '';

$category_description =
    isset($args['category_description'])
        ? $args['category_description']
        : '';

$category_icon =
    isset($args['category_icon'])
        ? $args['category_icon']
        : 'fa-solid fa-wand-magic-sparkles';

$prompt_count =
    isset($args['prompt_count'])
        ? (int) $args['prompt_count']
        : 0;

$total_categories =
    isset($args['total_categories'])
        ? (int) $args['total_categories']
        : 0;

$ai_prompts_url =
    isset($args['ai_prompts_url'])
        ? $args['ai_prompts_url']
        : home_url(
            '/ai-prompts-library/'
        );

?>


<!-- =================================================
HERO
================================================= -->

<section class="ai-platform-page-hero">


    <div
        class="ai-platform-page-hero-glow"
    ></div>


    <div
        class="ai-platform-page-grid-pattern"
    ></div>


    <div class="container">


        <div
            class="ai-platform-page-hero-layout"
        >


            <!-- =================================================
            HERO COPY
            ================================================= -->

            <div
                class="ai-platform-page-hero-copy"
            >


               <?php

        get_template_part(
            'template-parts/common/breadcrumb'
        );

        ?>


                <!-- EYEBROW -->

                <span
                    class="ai-platform-page-eyebrow"
                >

                    <span
                        class="ai-platform-page-eyebrow-dot"
                    ></span>


                    AI PROMPT CATEGORY

                </span>


                <!-- CATEGORY -->

                <span
                    class="ai-platform-page-category"
                >

                    <i
                        class="<?php
                        echo esc_attr(
                            $category_icon
                        );
                        ?>"
                        aria-hidden="true"
                    ></i>


                    <?php
                    echo esc_html(
                        $category_name
                    );
                    ?>

                </span>


                <!-- TITLE -->

                <h1
                    class="ai-platform-page-title"
                >

                    AI prompts for

                    <span>

                        <?php
                        echo esc_html(
                            $category_name
                        );
                        ?>.

                    </span>

                </h1>


                <!-- DESCRIPTION -->

                <div
                    class="ai-platform-page-description"
                >

                    <?php if (
                        !empty(
                            $category_description
                        )
                    ) : ?>

                        <?php
                        echo wp_kses_post(
                            wpautop(
                                $category_description
                            )
                        );
                        ?>

                    <?php else : ?>

                        <p>

                            Discover practical AI prompts
                            for
                            <?php
                            echo esc_html(
                                strtolower(
                                    $category_name
                                )
                            );
                            ?>,
                            curated in the Isago AI
                            prompt library.

                        </p>

                    <?php endif; ?>

                </div>


                <!-- STATS -->

                <div
                    class="ai-platform-page-stats"
                >


                    <div
                        class="ai-platform-page-stat"
                    >

                        <strong>

                            <?php
                            echo esc_html(
                                number_format_i18n(
                                    $prompt_count
                                )
                            );
                            ?>

                        </strong>


                        <span>

                            <?php
                            echo (
                                $prompt_count === 1
                            )
                                ? 'AI Prompt'
                                : 'AI Prompts';
                            ?>

                        </span>

                    </div>


                    <span
                        class="ai-platform-page-stat-divider"
                    ></span>


                    <div
                        class="ai-platform-page-stat"
                    >

                        <strong>

                            AI

                        </strong>


                        <span>

                            Prompt Library

                        </span>

                    </div>


                    <span
                        class="ai-platform-page-stat-divider"
                    ></span>


                    <div
                        class="ai-platform-page-stat"
                    >

                        <strong>

                            <?php
                            echo esc_html(
                                number_format_i18n(
                                    $total_categories
                                )
                            );
                            ?>

                        </strong>


                        <span>

                            Categories

                        </span>

                    </div>


                </div>


                <!-- BROWSE ALL -->

                <a
                    href="<?php
                    echo esc_url(
                        $ai_prompts_url
                    );
                    ?>"
                    class="isago-ai-tool-categories-hero-link"
                >

                    Browse all AI prompts

                    <i
                        class="fa-solid fa-arrow-right"
                        aria-hidden="true"
                    ></i>

                </a>


            </div>


            <!-- =================================================
            HERO VISUAL
            ================================================= -->

            <div
                class="ai-platform-page-hero-visual"
            >


                <div
                    class="ai-platform-page-visual-glow"
                ></div>


                <div
                    class="ai-platform-page-orbit
                           ai-platform-page-orbit-one"
                ></div>


                <div
                    class="ai-platform-page-orbit
                           ai-platform-page-orbit-two"
                ></div>


                <div
                    class="ai-platform-page-orbit
                           ai-platform-page-orbit-three"
                ></div>


                <div
                    class="ai-platform-page-platform-card"
                >


                    <div
                        class="ai-platform-page-platform-card-shine"
                    ></div>


                    <div
                        class="ai-platform-page-platform-image"
                    >

                        <span
                            class="ai-platform-page-platform-fallback"
                        >

                            <i
                                class="<?php
                                echo esc_attr(
                                    $category_icon
                                );
                                ?>"
                                aria-hidden="true"
                            ></i>

                        </span>

                    </div>


                    <strong
                        class="ai-platform-page-platform-name"
                    >

                        <?php
                        echo esc_html(
                            $category_name
                        );
                        ?>

                    </strong>


                    <span
                        class="ai-platform-page-platform-type"
                    >

                        <span></span>

                        <?php
                        echo esc_html(
                            number_format_i18n(
                                $prompt_count
                            )
                        );
                        ?>

                        <?php
                        echo (
                            $prompt_count === 1
                        )
                            ? 'AI Prompt'
                            : 'AI Prompts';
                        ?>

                    </span>


                </div>


            </div>


        </div>


    </div>


</section>