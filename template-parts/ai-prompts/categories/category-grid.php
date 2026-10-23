<?php

if (!defined('ABSPATH')) {
    exit;
}

  

/*=========================================================
VARIABLES
=========================================================*/

$ai_prompt_categories = array();

$ai_prompt_category_prompt_counts = array();


/*=========================================================
GET ALL AI PROMPT CATEGORIES
=========================================================*/

$all_ai_prompt_categories =
    isago_get_ai_prompt_categories(
        array(
            'hide_empty' => false,
            'orderby'    => 'name',
            'order'      => 'ASC',
        )
    );


/*=========================================================
KEEP PARENT CATEGORIES AND COUNT ALL DESCENDANT PROMPTS
=========================================================*/

if (!empty($all_ai_prompt_categories)) {

    foreach ($all_ai_prompt_categories as $category) {

        /*
         * Display only top-level parent categories.
         * Subcategories will be counted but not displayed
         * as separate cards.
         */

        if ((int) $category->parent !== 0) {
            continue;
        }


        /*
         * Count published AI prompts assigned to this
         * parent or any of its descendant categories.
         */

        $category_prompt_query = new WP_Query(
            array(
                'post_type'              => 'ai_prompt',
                'post_status'            => 'publish',
                'posts_per_page'         => 1,
                'fields'                 => 'ids',
                'no_found_rows'          => false,
                'ignore_sticky_posts'    => true,
                'update_post_meta_cache' => false,
                'update_post_term_cache' => false,

                'tax_query' => array(
                    array(
                        'taxonomy'         => 'ai_prompt_category',
                        'field'            => 'term_id',
                        'terms'            => (int) $category->term_id,
                        'include_children' => true,
                        'operator'         => 'IN',
                    ),
                ),
            )
        );


        $prompt_count =
            (int) $category_prompt_query->found_posts;


        /*
         * Hide parent categories if neither the parent
         * nor its descendants contain published prompts.
         */

        if ($prompt_count < 1) {
            continue;
        }


        /*
         * Save the parent category and its total count.
         */

        $ai_prompt_categories[] = $category;

        $ai_prompt_category_prompt_counts[
            (int) $category->term_id
        ] = $prompt_count;

    }

}


/*=========================================================
TOTAL DISPLAYED PARENT CATEGORIES
=========================================================*/

$total_categories = count(
    $ai_prompt_categories
);


?>


<!-- =================================================
MAIN CONTENT
================================================= -->

<section
    class="isago-ai-tool-categories-content"
>



    <div class="container">


        <!-- =================================================
        BROWSE BY CATEGORY
        ================================================= -->

        <section
            class="isago-ai-tool-categories-section"
        >


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
                    $ai_prompt_categories
                )
            ) : ?>


                <div
                    class="isago-ai-tool-category-grid"
                >


                    <?php foreach (
                        $ai_prompt_categories
                        as $category
                    ) : ?>


                        <?php

                        /*=====================================
                        CATEGORY URL
                        =====================================*/

                        $category_url =
                            get_term_link(
                                $category,
                                'ai_prompt_category'
                            );


                        if (
                            is_wp_error(
                                $category_url
                            )
                        ) {

                            continue;

                        }


                        /*=====================================
                        CATEGORY ICON
                        =====================================*/

                        $category_icon =
                            get_term_meta(
                                $category->term_id,
                                '_isago_ai_prompt_category_icon',
                                true
                            );


                        $category_icon =
                            sanitize_text_field(
                                $category_icon
                            );


                        if (
                            empty(
                                $category_icon
                            )
                        ) {

                            $category_icon =
                                'fa-solid fa-wand-magic-sparkles';

                        }


                        
/*=====================================
PROMPT COUNT — PARENT + SUBCATEGORIES
=====================================*/

$prompt_category_count =
    isset(
        $ai_prompt_category_prompt_counts[
            (int) $category->term_id
        ]
    )
        ? (int) $ai_prompt_category_prompt_counts[
            (int) $category->term_id
        ]
        : 0;


                        ?>


                        <a
                            href="<?php
                            echo esc_url(
                                $category_url
                            );
                            ?>"
                            class="isago-ai-tool-category-card"
                        >


                            <!-- ICON -->

                            <span
                                class="isago-ai-tool-category-card-icon"
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


                            <!-- CONTENT -->

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

                                        Explore AI prompts,
                                        ideas and resources
                                        in this category.

                                    </span>


                                <?php endif; ?>


                                <small>

                                    <?php
                                    echo esc_html(
                                        number_format_i18n(
                                            $prompt_category_count
                                        )
                                    );
                                    ?>


                                    <?php
                                    echo (
                                        $prompt_category_count === 1
                                    )
                                        ? 'prompt'
                                        : 'prompts';
                                    ?>

                                </small>


                            </span>


                            <!-- ARROW -->

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


                <div
                    class="isago-ai-tool-categories-empty"
                >


                    <i
                        class="fa-solid fa-folder-open"
                        aria-hidden="true"
                    ></i>


                    <h2>

                        No AI prompt categories yet.

                    </h2>


                    <p>

                        Create AI Prompt Categories
                        in WordPress to organize
                        your prompts.

                    </p>


                </div>


            <?php endif; ?>


        </section>

    </div>


</section>