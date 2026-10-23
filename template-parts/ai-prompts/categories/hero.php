<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
VARIABLES
=========================================================*/

$total_categories =
    isset($args['total_categories'])
        ? (int) $args['total_categories']
        : 0;


$total_prompts =
    isset($args['total_prompts'])
        ? (int) $args['total_prompts']
        : 0;


$ai_prompts_url =
    isset($args['ai_prompts_url'])
        ? $args['ai_prompts_url']
        : home_url(
            '/ai-prompts-library/'
        );


$ai_tools_url =
    isset($args['ai_tools_url'])
        ? $args['ai_tools_url']
        : home_url(
            '/ai-tools/'
        );

?>


<!-- =================================================
HERO
================================================= -->

<section
    class="isago-ai-tool-categories-hero"
>


    <div
        class="isago-ai-tool-categories-hero-glow"
    ></div>


    <div
        class="isago-ai-tool-categories-grid-pattern"
    ></div>


    <div class="ai-platform-page-hero-layout container">


        <div
            class="isago-ai-tool-categories-hero-content"
        >


           <?php

        get_template_part(
            'template-parts/common/breadcrumb'
        );

        ?>


            <!-- EYEBROW -->

            <span
                class="isago-ai-tool-categories-eyebrow"
            >

                <span></span>

                AI PROMPTS DIRECTORY

            </span>


            <!-- TITLE -->

            <h1
                class="isago-ai-tool-categories-title"
            >

                Explore AI prompts
                <span>
                    by category.
                </span>

            </h1>


            <!-- DESCRIPTION -->

            <p
                class="isago-ai-tool-categories-description"
            >

                Discover practical AI prompts organized
                by what you want to create, write,
                design, research, automate, learn,
                and accomplish.

            </p>


            <!-- STATS -->

            <div
                class="isago-ai-tool-categories-stats"
            >


                <!-- CATEGORIES -->

                <div
                    class="isago-ai-tool-categories-stat"
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


                <span
                    class="isago-ai-tool-categories-stat-divider"
                ></span>


                <!-- PROMPTS -->

                <div
                    class="isago-ai-tool-categories-stat"
                >

                    <strong>

                        <?php
                        echo esc_html(
                            number_format_i18n(
                                $total_prompts
                            )
                        );
                        ?>

                    </strong>


                    <span>

                        AI Prompts

                    </span>

                </div>


                <span
                    class="isago-ai-tool-categories-stat-divider"
                ></span>


                <!-- DISCOVERY -->

                <div
                    class="isago-ai-tool-categories-stat"
                >

                    <strong>

                        AI

                    </strong>


                    <span>

                        Discovery

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


           <?php

        get_template_part(
            'template-parts/ai-prompts/categories/orbit',
              null,
    [
        'orbit_type' => 'prompts',
    ]
        );

        ?>


    </div>
  

</section>