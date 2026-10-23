<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
VARIABLES
=========================================================*/

$prompt_categories =
    $args['prompt_categories']
    ?? array();


$total_prompts =
    isset(
        $args['total_prompts']
    )
        ? (int) $args['total_prompts']
        : 0;

?>


<section
    class="ai-platform-page-hero"
>

    <div class="container">


        <?php

        get_template_part(
            'template-parts/common/breadcrumb'
        );

        ?>


        <div
            class="ai-platform-page-hero-layout"
        >


            <div
                class="ai-platform-page-hero-copy"
            >


                <span
                    class="ai-platform-directory-kicker"
                >

                    AI PROMPTS LIBRARY

                </span>


                <h1
                    class="ai-platform-page-title"
                >

                    Discover prompts
                    <span>built for AI.</span>

                </h1>


                <p
                    class="ai-platform-page-description"
                >

                    Explore practical AI prompts for
                    writing, research, creativity,
                    productivity, business and more.
                    Find a prompt, customize it and
                    put it to work with your favorite
                    AI tools.

                </p>


                <div
                    class="ai-platform-page-stats"
                >


                    <div
                        class="ai-platform-page-stat"
                    >

                        <strong>

                            <?php echo esc_html(
                                number_format_i18n(
                                    $total_prompts
                                )
                            ); ?>

                        </strong>

                        <span>

                            AI Prompts

                        </span>

                    </div>


                    <div
                        class="ai-platform-page-stat-divider"
                        aria-hidden="true"
                    ></div>


                    <div
                        class="ai-platform-page-stat"
                    >

                        <strong>

                            <?php echo esc_html(
                                number_format_i18n(
                                    count(
                                        $prompt_categories
                                    )
                                )
                            ); ?>

                        </strong>

                        <span>

                            Categories

                        </span>

                    </div>


                    <div
                        class="ai-platform-page-stat-divider"
                        aria-hidden="true"
                    ></div>


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


                </div>


            </div>


            <div
                class="ai-platform-hero-visual"
                aria-hidden="true"
            >


                <div
                    class="ai-platform-orbit orbit-one"
                ></div>


                <div
                    class="ai-platform-orbit orbit-two"
                ></div>


                <div
                    class="ai-platform-orbit orbit-three"
                ></div>


                <div
                    class="ai-platform-hero-glow"
                ></div>


                <div
                    class="ai-platform-hero-core"
                >

                    <span>

                        AI

                    </span>

                </div>


            </div>


        </div>


    </div>

</section>