<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
VARIABLES
=========================================================*/

$category =
    isset($args['category'])
        ? $args['category']
        : null;


$category_id =
    isset($args['category_id'])
        ? absint($args['category_id'])
        : 0;


$category_name =
    isset($args['category_name'])
        ? $args['category_name']
        : '';


$category_icon =
    isset($args['category_icon'])
        ? $args['category_icon']
        : 'fa-solid fa-wand-magic-sparkles';


$prompt_count =
    isset($args['prompt_count'])
        ? (int) $args['prompt_count']
        : 0;


$prompt_query =
    isset($args['prompt_query'])
        ? $args['prompt_query']
        : null;


$paged =
    isset($args['paged'])
        ? absint($args['paged'])
        : 1;


$ai_prompts_url =
    isset($args['ai_prompts_url'])
        ? $args['ai_prompts_url']
        : home_url(
            '/ai-prompts-library/'
        );


        
/*=========================================================
AI PROMPT SUBCATEGORY TABS
=========================================================*/

$current_category_term = null;

if ($category_id) {
    $current_category_term = get_term(
        $category_id,
        'ai_prompt_category'
    );
}

if (
    !$current_category_term
    ||
    is_wp_error($current_category_term)
) {
    $current_category_term = (
        is_object($category)
        &&
        isset($category->term_id)
    )
        ? get_term(
            absint($category->term_id),
            'ai_prompt_category'
        )
        : null;
}

if (is_wp_error($current_category_term)) {
    $current_category_term = null;
}

$subcategory_tabs_parent = null;
$subcategory_tabs = [];

/*
 * Find immediate subcategories of the current category.
 */
if ($current_category_term) {

    $current_children = get_terms([
        'taxonomy'   => 'ai_prompt_category',
        'parent'     => (int) $current_category_term->term_id,
        'hide_empty' => false,
        'orderby'    => 'name',
        'order'      => 'ASC',
    ]);

    /*
     * If the current category has children,
     * show the current category and its children.
     */
    if (
        !is_wp_error($current_children)
        &&
        !empty($current_children)
    ) {

        $subcategory_tabs_parent =
            $current_category_term;

        $subcategory_tabs =
            $current_children;

    /*
     * Otherwise, show its parent and sibling categories.
     */
    } elseif (
        !empty($current_category_term->parent)
    ) {

        $possible_parent = get_term(
            (int) $current_category_term->parent,
            'ai_prompt_category'
        );

        if (
            $possible_parent
            &&
            !is_wp_error($possible_parent)
        ) {

            $siblings = get_terms([
                'taxonomy'   => 'ai_prompt_category',
                'parent'     => (int) $possible_parent->term_id,
                'hide_empty' => false,
                'orderby'    => 'name',
                'order'      => 'ASC',
            ]);

            if (
                !is_wp_error($siblings)
                &&
                !empty($siblings)
            ) {

                $subcategory_tabs_parent =
                    $possible_parent;

                $subcategory_tabs =
                    $siblings;

            }

        }

    }

}

/*
 * Count published AI Prompts in a category.
 *
 * Include descendants so that the count matches
 * the prompts available on that category's page.
 */
$count_prompts_in_category = static function (
    $term_id
) {

    $count_query = new WP_Query([
        'post_type'              => 'ai_prompt',
        'post_status'            => 'publish',
        'posts_per_page'         => 1,
        'fields'                 => 'ids',
        'no_found_rows'          => false,
        'ignore_sticky_posts'    => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
        'tax_query'              => [
            [
                'taxonomy'         => 'ai_prompt_category',
                'field'            => 'term_id',
                'terms'            => absint($term_id),
                'include_children' => true,
                'operator'         => 'IN',
            ],
        ],
    ]);

    return (int) $count_query->found_posts;

};

/*
 * Reuse the existing main query count for the current
 * category wherever possible.
 */
$get_subcategory_prompt_count = static function (
    $term_id
) use (
    $category_id,
    $prompt_query,
    $count_prompts_in_category
) {

    $term_id = absint($term_id);

    if (
        $term_id === $category_id
        &&
        $prompt_query instanceof WP_Query
    ) {
        return (int) $prompt_query->found_posts;
    }

    return $count_prompts_in_category($term_id);

};

?>


<!-- =================================================
PROMPTS
================================================= -->

<section
    class="ai-platform-directory-results"
>
 
    <div class="container">



        <?php
        /*
         * Render category tabs only when there are
         * subcategories or sibling categories to show.
         */
        if (
            $subcategory_tabs_parent
            &&
            !empty($subcategory_tabs)
        ) :

            $tabs_parent_id = absint(
                $subcategory_tabs_parent->term_id
            );

            $tabs_parent_url = get_term_link(
                $subcategory_tabs_parent,
                'ai_prompt_category'
            );

            $parent_is_active = (
                $category_id === $tabs_parent_id
            );
        ?>

            <?php if (!is_wp_error($tabs_parent_url)) : ?>

                <nav
                    class="ai-prompt-subcategory-navigation"
                    aria-label="<?php
                    echo esc_attr(
                        sprintf(
                            'Browse %s prompt categories',
                            $subcategory_tabs_parent->name
                        )
                    );
                    ?>"
                >

                    
<div class="ai-prompt-subcategory-tabs-shell">

    <!-- Scroll tabs to the left -->
    <button
        type="button"
        class="ai-prompt-subcategory-arrow ai-prompt-subcategory-arrow-left"
        aria-label="Show previous prompt categories"
        data-tabs-scroll="left"
        disabled
    >
        <i class="fa-solid fa-chevron-left" aria-hidden="true"></i>
    </button>

    <!-- Existing category tabs go inside this container -->
    <div
        class="ai-prompt-subcategory-tabs"
        tabindex="0"
        aria-label="Prompt categories"
    >


                        <!-- ALL PROMPTS IN THE PARENT CATEGORY -->

                   
<a
    href="<?php echo esc_url($tabs_parent_url); ?>"
    class="ai-prompt-subcategory-tab<?php
        echo $parent_is_active
            ? ' is-active'
            : '';
    ?>"
    data-ai-prompt-category="<?php
        echo esc_attr(
            $subcategory_tabs_parent->slug
        );
    ?>"
    data-ai-prompt-category-name="<?php
        echo esc_attr(
            $subcategory_tabs_parent->name
        );
    ?>"
    <?php if ($parent_is_active) : ?>
        aria-current="page"
    <?php endif; ?>
>


                            <span class="ai-prompt-subcategory-label">
                                All <?php
                                echo esc_html(
                                    $subcategory_tabs_parent->name
                                );
                                ?>
                            </span>

                            <span class="ai-prompt-subcategory-count">
                                <?php
                                echo esc_html(
                                    number_format_i18n(
                                        $get_subcategory_prompt_count(
                                            $tabs_parent_id
                                        )
                                    )
                                );
                                ?>
                            </span>

                        </a>


                        <!-- SUBCATEGORY TABS -->

                        <?php foreach (
                            $subcategory_tabs as $subcategory_tab
                        ) : ?>

                            <?php

                            $subcategory_url = get_term_link(
                                $subcategory_tab,
                                'ai_prompt_category'
                            );

                            if (is_wp_error($subcategory_url)) {
                                continue;
                            }

                            $subcategory_is_active = (
                                $category_id ===
                                (int) $subcategory_tab->term_id
                            );

                            $subcategory_prompt_count =
                                $get_subcategory_prompt_count(
                                    $subcategory_tab->term_id
                                );

                            ?>

                        
<a
    href="<?php echo esc_url($subcategory_url); ?>"
    class="ai-prompt-subcategory-tab<?php
        echo $subcategory_is_active
            ? ' is-active'
            : '';
    ?>"
    data-ai-prompt-category="<?php
        echo esc_attr(
            $subcategory_tab->slug
        );
    ?>"
    data-ai-prompt-category-name="<?php
        echo esc_attr(
            $subcategory_tab->name
        );
    ?>"
    <?php if ($subcategory_is_active) : ?>
        aria-current="page"
    <?php endif; ?>
>


                                <span class="ai-prompt-subcategory-label">
                                    <?php
                                    echo esc_html(
                                        $subcategory_tab->name
                                    );
                                    ?>
                                </span>

                                <span class="ai-prompt-subcategory-count">
                                    <?php
                                    echo esc_html(
                                        number_format_i18n(
                                            $subcategory_prompt_count
                                        )
                                    );
                                    ?>
                                </span>

                            </a>

                        <?php endforeach; ?>

                    </div>

 

    <!-- Scroll tabs to the right -->
    <button
        type="button"
        class="ai-prompt-subcategory-arrow ai-prompt-subcategory-arrow-right"
        aria-label="Show more prompt categories"
        data-tabs-scroll="right"
    >
        <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
    </button>

</div><!-- /.ai-prompt-subcategory-tabs-shell -->

                </nav>

            <?php endif; ?>

        <?php endif; ?>


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

                    AI PROMPTS DIRECTORY

                </span>


                <h2>

                

                    <?php
                    echo esc_html(
                        $category_name
                    );
                    ?>

                </h2>

            </div>


            <span
                class="ai-platform-results-count"
            >

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
                    ? 'prompt'
                    : 'prompts';
                ?>

            </span>

        </div>


        <?php if (
            $prompt_query
            &&
            $prompt_query->have_posts()
        ) : ?>


            <!-- =================================================
            PROMPT GRID
            ================================================= -->

            <div
                class="ai-platform-directory-grid"
            >


                <?php while (
                    $prompt_query->have_posts()
                ) : ?>


                    <?php

                    $prompt_query->the_post();

                    $prompt =
                        get_post();

                    ?>


                    <?php
                    get_template_part(
                        'template-parts/ai-prompts/components/card',
                        null,
                        array(
                            'prompt' =>
                                $prompt,

                            'category' =>
                                $category,

                            'category_id' =>
                                $category_id,

                            'category_name' =>
                                $category_name,
                        )
                    );
                    ?>


                <?php endwhile; ?>


            </div>


            <!-- =================================================
            PAGINATION
            ================================================= -->

            <?php if (
                $prompt_query->max_num_pages > 1
            ) : ?>


                <?php

                $pagination =
                    paginate_links(
                        array(

                            'base' =>
                                str_replace(
                                    999999999,
                                    '%#%',
                                    esc_url(
                                        get_pagenum_link(
                                            999999999
                                        )
                                    )
                                ),

                            'format' =>
                                '?paged=%#%',

                            'current' =>
                                $paged,

                            'total' =>
                                $prompt_query->max_num_pages,

                            'type' =>
                                'list',

                            'prev_text' =>
                                '<i class="fa-solid fa-arrow-left"></i> Previous',

                            'next_text' =>
                                'Next <i class="fa-solid fa-arrow-right"></i>',

                        )
                    );

                ?>


                <?php if (
                    $pagination
                ) : ?>


                    <nav
                        class="ai-platform-page-pagination"
                        aria-label="AI Prompts pagination"
                    >

                        <?php
                        echo wp_kses_post(
                            $pagination
                        );
                        ?>

                    </nav>


                <?php endif; ?>


            <?php endif; ?>


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
                        class="<?php
                        echo esc_attr(
                            $category_icon
                        );
                        ?>"
                        aria-hidden="true"
                    ></i>

                </div>


                <h2>

                    No AI prompts in this category yet.

                </h2>


                <p>

                    We haven't added any AI prompts
                    to
                    <?php
                    echo esc_html(
                        $category_name
                    );
                    ?>
                    yet. Check back as the Isago
                    prompt library grows.

                </p>


                <a
                    href="<?php
                    echo esc_url(
                        $ai_prompts_url
                    );
                    ?>"
                    class="ai-platform-page-empty-button"
                >

                    Browse all AI prompts

                    <i
                        class="fa-solid fa-arrow-right"
                        aria-hidden="true"
                    ></i>

                </a>

            </div>


        <?php endif; ?>


    </div>

</section>