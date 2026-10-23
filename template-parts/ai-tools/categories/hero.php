<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
VARIABLES
=========================================================*/

$ai_tools_url =
    isset(
        $args['ai_tools_url']
    )
        ? $args['ai_tools_url']
        : home_url(
            '/ai-tools/'
        );


$total_categories =
    isset(
        $args['total_categories']
    )
        ? (int) $args['total_categories']
        : 0;


$total_ai_tools =
    isset(
        $args['total_ai_tools']
    )
        ? (int) $args['total_ai_tools']
        : 0;

?>


<section
    class="isago-ai-tool-categories-hero"
>


    <div
        class="isago-ai-tool-categories-hero-glow"
    ></div>


    <div
        class="isago-ai-tool-categories-grid-pattern"
    ></div>


    <div class="ai-platform-page-hero-layout  container">


        <div
            class="isago-ai-tool-categories-hero-content"
        >
     <?php

        get_template_part(
            'template-parts/common/breadcrumb'
        );

        ?>

         

            <!-- =================================================
            EYEBROW
            ================================================= -->

            <span
                class="isago-ai-tool-categories-eyebrow"
            >


                <span></span>


                AI TOOLS DIRECTORY


            </span>


            <!-- =================================================
            TITLE
            ================================================= -->

            <h1
                class="isago-ai-tool-categories-title"
            >

                Explore AI tools
                <span>by category.</span>

            </h1>


            <!-- =================================================
            DESCRIPTION
            ================================================= -->

            <p
                class="isago-ai-tool-categories-description"
            >

                Discover AI tools organized by
                what they help you create, automate,
                research, and accomplish.

            </p>


            <!-- =================================================
            STATS
            ================================================= -->

            <div
                class="isago-ai-tool-categories-stats"
            >


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


                <div
                    class="isago-ai-tool-categories-stat"
                >

                    <strong>

                        <?php

                        echo esc_html(
                            number_format_i18n(
                                $total_ai_tools
                            )
                        );

                        ?>

                    </strong>


                    <span>

                        AI Tools

                    </span>


                </div>


                <span
                    class="isago-ai-tool-categories-stat-divider"
                ></span>


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


            <!-- =================================================
            BROWSE ALL
            ================================================= -->

            <a
                href="<?php echo esc_url(
                    $ai_tools_url
                ); ?>"
                class="isago-ai-tool-categories-hero-link"
            >

                Browse all AI tools


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
        'orbit_type' => 'tools',
    ]
        );
?>

    </div>


</section>