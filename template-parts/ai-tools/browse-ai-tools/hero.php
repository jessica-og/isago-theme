<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
VARIABLES
=========================================================*/

$platform_categories =
    $args['platform_categories']
    ?? [];


$total_platforms =
    $args['total_platforms']
    ?? 0;


/*=========================================================
AI TOOL CATEGORY COUNT
=========================================================*/

/*
 * AI Tool Categories are stored in the
 * ai_tool_category taxonomy.
 *
 * This is different from Platform Type
 * (LLMs, Image, Video, etc.).
 */

$total_tool_categories = 0;


if (
    function_exists(
        'isago_get_ai_tool_categories'
    )
) {

    $tool_categories =
        isago_get_ai_tool_categories(
            [
                'hide_empty' => false,
            ]
        );


    if (
        is_array($tool_categories)
        ||
        $tool_categories instanceof Traversable
    ) {

        $total_tool_categories =
            count($tool_categories);

    }

}

?>


<!-- =================================================
HERO
================================================= -->

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

                    AI Tools DIRECTORY

                </span>


                <h1
                    class="ai-platform-page-title"
                >

                    Explore the AI
                    <span>models</span>
                    shaping what comes next.

                </h1>


                <p
                    class="ai-platform-page-description"
                >

                    Discover the AI platforms and
                    models behind the tools, ideas and
                    workflows people are using to create,
                    build and work smarter.

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
                                    $total_platforms
                                )
                            ); ?>

                        </strong>

                        <span>

                          AI  Tools

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
                $total_tool_categories
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

                            Discovery

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