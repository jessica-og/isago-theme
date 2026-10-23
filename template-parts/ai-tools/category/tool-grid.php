<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
VARIABLES
=========================================================*/

$category_name =
    isset(
        $args['category_name']
    )
        ? $args['category_name']
        : '';


$category_icon =
    isset(
        $args['category_icon']
    )
        ? $args['category_icon']
        : 'fa-solid fa-layer-group';


$tool_count =
    isset(
        $args['tool_count']
    )
        ? (int) $args['tool_count']
        : 0;


$ai_tools =
    isset(
        $args['ai_tools']
    )
        ? $args['ai_tools']
        : [];


$ai_tools_url =
    isset(
        $args['ai_tools_url']
    )
        ? $args['ai_tools_url']
        : home_url(
            '/ai-tools/'  
        );

?>


<!-- =================================================
RESULTS
================================================= -->

<section
    class="ai-platform-directory-results"
>


    <div class="container">


        <!-- =================================================
        RESULTS HEADER
        ================================================= -->

        <div
            class="ai-platform-results-header"
        >


            <div>


                <span
                    class="ai-platform-results-kicker"
                >

                    AI TOOLS DIRECTORY

                </span>


                <h2>

                    

                    <?php echo esc_html(
                        $category_name
                    ); ?>


                </h2>


            </div>


            <span
                class="ai-platform-results-count"
            >

                <?php

                echo esc_html(
                    number_format_i18n(
                        $tool_count
                    )
                );

                ?>


                <?php echo (
                    $tool_count === 1
                )
                    ? 'tool'
                    : 'tools'; ?>


            </span>


        </div>


        <?php if (
            !empty(
                $ai_tools
            )
        ) : ?>


            <!-- =================================================
            PLATFORM GRID
            ================================================= -->

            <div
                class="ai-platform-directory-grid"
            >


                <?php foreach (
                    $ai_tools
                    as $platform
                ) : ?>


                    <?php

                    /*
                     * Pass the platform into the reusable
                     * AI Platform card component.
                     */

                    get_template_part(
                        'template-parts/ai-tools/browse-ai-tools/card',
                        null,
                        [
                            'platform' =>
                                $platform,
                        ]
                    );

                    ?>


                <?php endforeach; ?>


            </div>


        <?php else : ?>


            <!-- =============================================
            EMPTY STATE
            ============================================= -->

            <div
                class="ai-platform-page-empty"
            >


                <div
                    class="ai-platform-page-empty-icon"
                >


                    <i
                        class="<?php echo esc_attr(
                            $category_icon
                        ); ?>"
                        aria-hidden="true"
                    ></i>


                </div>


                <h2>

                    No AI tools in this category yet.

                </h2>


                <p>

                    We haven't added any AI tools to

                    <?php echo esc_html(
                        $category_name
                    ); ?>

                    yet. Check back as the Isago
                    AI tools directory grows.

                </p>


                <a
                    href="<?php echo esc_url(
                        $ai_tools_url
                    ); ?>"
                    class="ai-platform-page-empty-button"
                >

                    Browse all AI tools


                    <i
                        class="fa-solid fa-arrow-right"
                        aria-hidden="true"
                    ></i>


                </a>


            </div>


        <?php endif; ?>


    </div>


</section>