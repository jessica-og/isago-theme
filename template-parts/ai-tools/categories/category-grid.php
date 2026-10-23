<?php

if (!defined('ABSPATH')) {
    exit;
}



/*=========================================================
VARIABLES
=========================================================*/

$all_ai_tool_categories =
    isset($args['ai_tool_categories'])
    && is_array($args['ai_tool_categories'])
        ? $args['ai_tool_categories']
        : array();


$ai_tool_categories = array();

$ai_tool_category_tool_counts = array();


/*=========================================================
HIDE CATEGORIES WITH ZERO AI TOOLS
=========================================================*/

foreach ($all_ai_tool_categories as $category) {

    $tool_count =
        isago_get_ai_tool_category_platform_count(
            $category->term_id
        );


    /*
     * Do not display categories with no connected tools.
     */

    if ($tool_count < 1) {
        continue;
    }


    $ai_tool_categories[] = $category;


    /*
     * Store the count so it does not need to be
     * calculated again inside the category grid.
     */

    $ai_tool_category_tool_counts[
        (int) $category->term_id
    ] = $tool_count;

}


/*=========================================================
TOTAL VISIBLE CATEGORIES
=========================================================*/

$total_categories = count(
    $ai_tool_categories
);


?>


<section
    class="isago-ai-tool-categories-section"
>


    <!-- =================================================
    SECTION HEADER
    ================================================= -->

    <div
        class="isago-ai-tool-categories-section-header"
    >


        <div>


            <span
                class="isago-ai-tool-categories-kicker"
            >

                DIRECTORY

            </span>


            <h2>

                Browse categories

            </h2>


        </div>


        <span
            class="isago-ai-tool-categories-section-count"
        >

            <?php

            echo esc_html(
                number_format_i18n(
                    $total_categories
                )
            );

            ?>

            categories

        </span>


    </div>


    <?php if (
        !empty(
            $ai_tool_categories
        )
    ) : ?>


        <!-- =================================================
        CATEGORY GRID
        ================================================= -->

        <div
            class="isago-ai-tool-category-grid"
        >


            <?php foreach (
                $ai_tool_categories
                as $category
            ) : ?>


                <?php

                /*=================================================
                CATEGORY URL
                =================================================*/

                $category_url =
                    isago_get_ai_tool_category_url(
                        $category->term_id
                    );


                /*=================================================
                CATEGORY ICON
                =================================================*/

                $category_icon =
                    isago_get_ai_tool_category_icon(
                        $category->term_id
                    );


                if (
                    empty(
                        $category_icon
                    )
                ) {

                    $category_icon =
                        'fa-solid fa-layer-group';

                }


            
/*=================================================
ACTUAL AI TOOL COUNT
=================================================*/

$tool_count =
    isset(
        $ai_tool_category_tool_counts[
            (int) $category->term_id
        ]
    )
        ? (int) $ai_tool_category_tool_counts[
            (int) $category->term_id
        ]
        : 0;


                ?>


                <a
                    href="<?php echo esc_url(
                        $category_url
                    ); ?>"
                    class="isago-ai-tool-category-card"
                >


                    <!-- =================================================
                    ICON
                    ================================================= -->

                    <span
                        class="isago-ai-tool-category-card-icon"
                    >

                        <i
                            class="<?php echo esc_attr(
                                $category_icon
                            ); ?>"
                            aria-hidden="true"
                        ></i>

                    </span>


                    <!-- =================================================
                    CONTENT
                    ================================================= -->

                    <span
                        class="isago-ai-tool-category-card-content"
                    >


                        <strong>

                            <?php

                            echo esc_html(
                                $category->name
                            );

                            ?>

                        </strong>


                        <?php if (
                            !empty(
                                $category->description
                            )
                        ) : ?>


                            <span>

                                <?php

                                echo esc_html(
                                    wp_trim_words(
                                        wp_strip_all_tags(
                                            $category->description
                                        ),
                                        14
                                    )
                                );

                                ?>

                            </span>


                        <?php else : ?>


                            <span>

                                Explore AI tools,
                                articles and resources
                                in this category.

                            </span>


                        <?php endif; ?>


                        <small>

                            <?php

                            echo esc_html(
                                number_format_i18n(
                                    $tool_count
                                )
                            );

                            ?>


                            <?php

                            echo (
                                $tool_count === 1
                            )
                                ? 'tool'
                                : 'tools';

                            ?>


                        </small>


                    </span>


                    <!-- =================================================
                    ARROW
                    ================================================= -->

                    <span
                        class="isago-ai-tool-category-card-arrow"
                    >

                        <i
                            class="fa-solid fa-arrow-right"
                            aria-hidden="true"
                        ></i>

                    </span>


                </a>


            <?php endforeach; ?>


        </div>


    <?php else : ?>


        <!-- =================================================
        EMPTY STATE
        ================================================= -->

        <div
            class="isago-ai-tool-categories-empty"
        >


            <i
                class="fa-solid fa-folder-open"
                aria-hidden="true"
            ></i>


            <h2>

                No AI tool categories yet.

            </h2>


            <p>

                Create AI Tool Categories in
                WordPress to organize your
                AI tools.

            </p>


        </div>


    <?php endif; ?>


</section>