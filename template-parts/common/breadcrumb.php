<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
HELPERS
=========================================================*/

/**
 * Output breadcrumb separator.
 *
 * @return void
 */
function isago_render_breadcrumb_separator() {

    echo '<span class="breadcrumb-separator">';
    echo '<i class="fa-solid fa-angle-right"></i>';
    echo '</span>';

}


/**
 * Render breadcrumb link.
 *
 * @param string $url
 * @param string $label
 * @return void
 */
function isago_render_breadcrumb_link($url, $label) {

    if (!$url || !$label) {
        return;
    }

    isago_render_breadcrumb_separator();

    echo '<a href="' . esc_url($url) . '">';
    echo esc_html($label);
    echo '</a>';

}


/**
 * Render current breadcrumb item.
 *
 * @param string $label
 * @return void
 */
function isago_render_breadcrumb_current($label) {

    if (!$label) {
        return;
    }

    isago_render_breadcrumb_separator();

    echo '<span class="breadcrumb-current">';
    echo esc_html($label);
    echo '</span>';

}


/**
 * Get a WordPress page URL by slug.
 *
 * @param string $slug
 * @return string
 */
function isago_get_breadcrumb_page_url($slug) {

    $page = get_page_by_path($slug);

    if (!$page) {
        return '';
    }

    $url = get_permalink($page->ID);

    return $url ? $url : '';
}


/*=========================================================
BREADCRUMB
=========================================================*/

?>

<nav
    class="isago-breadcrumb"
    aria-label="Breadcrumb"
>

    <a href="<?php echo esc_url(home_url('/')); ?>">
        Home
    </a>


    <?php

    /*=====================================================
    NORMAL BLOG CATEGORY
    =====================================================*/

    if (is_category()) :

        $category = get_queried_object();

        if ($category instanceof WP_Term) :

            $parents = [];

            $parent_id = $category->parent;

            while ($parent_id) {

                $parent = get_category($parent_id);

                if (
                    !$parent
                    ||
                    is_wp_error($parent)
                ) {
                    break;
                }

                $parents[] = $parent;

                $parent_id = $parent->parent;
            }

            $parents = array_reverse($parents);


            foreach ($parents as $parent) :

                isago_render_breadcrumb_link(
                    get_category_link(
                        $parent->term_id
                    ),
                    $parent->name
                );

            endforeach;


            isago_render_breadcrumb_current(
                $category->name
            );

        endif;


    /*=====================================================
    AI PROMPT CATEGORY
    =====================================================*/

    elseif (
        is_tax('ai_prompt_category')
    ) :

        $term = get_queried_object();


        if ($term instanceof WP_Term) :

            /*
             * AI Prompts directory.
             *
             * This is the actual top-level AI Prompt
             * navigation destination in Isago.
             */
            $ai_prompts_url =
                isago_get_breadcrumb_page_url(
                    'ai-prompts-categories'
                );


            if ($ai_prompts_url) {

                isago_render_breadcrumb_link(
                    $ai_prompts_url,
                    'AI Prompts'
                );

            }


            /*
             * Parent prompt categories.
             */
            $ancestors =
                get_ancestors(
                    $term->term_id,
                    'ai_prompt_category',
                    'taxonomy'
                );


            $ancestors =
                array_reverse($ancestors);


            foreach (
                $ancestors
                as $ancestor_id
            ) {

                $ancestor =
                    get_term(
                        $ancestor_id,
                        'ai_prompt_category'
                    );


                if (
                    !$ancestor
                    ||
                    is_wp_error($ancestor)
                ) {
                    continue;
                }


                $ancestor_url =
                    get_term_link($ancestor);


                if (
                    is_wp_error(
                        $ancestor_url
                    )
                ) {
                    continue;
                }


                isago_render_breadcrumb_link(
                    $ancestor_url,
                    $ancestor->name
                );
            }


            /*
             * Current category.
             */
            isago_render_breadcrumb_current(
                $term->name
            );

        endif;


    /*=====================================================
    AI TOOL CATEGORY
    =====================================================*/

    elseif (
        is_tax('ai_tool_category')
    ) :

        $term = get_queried_object();


        if ($term instanceof WP_Term) :

            /*
             * AI Tools top-level directory.
             *
             * Your menu engine currently sends the
             * AI Tools navigation item to the
             * AI Tool Categories directory.
             */
            $ai_tools_url =
                isago_get_breadcrumb_page_url(
                    'ai-tools-categories'
                );


            if ($ai_tools_url) {

                isago_render_breadcrumb_link(
                    $ai_tools_url,
                    'AI Tools'
                );

            }


            /*
             * Parent AI Tool Categories.
             */
            $ancestors =
                get_ancestors(
                    $term->term_id,
                    'ai_tool_category',
                    'taxonomy'
                );


            $ancestors =
                array_reverse($ancestors);


            foreach (
                $ancestors
                as $ancestor_id
            ) {

                $ancestor =
                    get_term(
                        $ancestor_id,
                        'ai_tool_category'
                    );


                if (
                    !$ancestor
                    ||
                    is_wp_error($ancestor)
                ) {
                    continue;
                }


                $ancestor_url =
                    get_term_link($ancestor);


                if (
                    is_wp_error(
                        $ancestor_url
                    )
                ) {
                    continue;
                }


                isago_render_breadcrumb_link(
                    $ancestor_url,
                    $ancestor->name
                );
            }


            /*
             * Current category.
             */
            isago_render_breadcrumb_current(
                $term->name
            );

        endif;


    /*=====================================================
    AI PLATFORM
    =====================================================*/

    elseif (
        is_tax('ai_platform')
    ) :

        $term = get_queried_object();


        if ($term instanceof WP_Term) :

            /*
             * AI Tools directory.
             *
             * This is the broader AI Tools section.
             */
            $ai_tools_url =
                isago_get_breadcrumb_page_url(
                    'ai-tools'
                );


            /*
             * If the AI Tools page exists,
             * use it.
             */
            if ($ai_tools_url) {

                isago_render_breadcrumb_link(
                    $ai_tools_url,
                    'AI Tools'
                );

            } else {

                /*
                 * Fallback to the AI Tool Categories
                 * directory if /ai-tools/ does not exist.
                 */
                $ai_tools_categories_url =
                    isago_get_breadcrumb_page_url(
                        'ai-tools-categories'
                    );


                if ($ai_tools_categories_url) {

                    isago_render_breadcrumb_link(
                        $ai_tools_categories_url,
                        'AI Tools'
                    );

                }

            }


            /*
             * Current platform.
             */
            isago_render_breadcrumb_current(
                $term->name
            );

        endif;


    /*=====================================================
    TAG
    =====================================================*/

    elseif (is_tag()) :

        isago_render_breadcrumb_current(
            single_tag_title(
                '',
                false
            )
        );


    /*=====================================================
    SINGLE CONTENT
    =====================================================*/

    elseif (is_single()) :

        $post_id =
            get_queried_object_id();


        $post_type =
            get_post_type($post_id);


        /*-------------------------------------------------
        AI PROMPT
        -------------------------------------------------*/

        if (
            $post_type === 'ai_prompt'
        ) :

            /*
             * AI Prompts top-level directory.
             */
            $ai_prompts_url =
                isago_get_breadcrumb_page_url(
                    'ai-prompts-categories'
                );


            if ($ai_prompts_url) {

                isago_render_breadcrumb_link(
                    $ai_prompts_url,
                    'AI Prompts'
                );

            }


            /*
             * Prompt categories.
             */
            $terms =
                get_the_terms(
                    $post_id,
                    'ai_prompt_category'
                );


            if (
                !empty($terms)
                &&
                !is_wp_error($terms)
            ) {

                /*
                 * Use the first assigned category
                 * as the breadcrumb path.
                 */
                $term =
                    reset($terms);


                /*
                 * Parent hierarchy.
                 */
                $ancestors =
                    get_ancestors(
                        $term->term_id,
                        'ai_prompt_category',
                        'taxonomy'
                    );


                $ancestors =
                    array_reverse(
                        $ancestors
                    );


                foreach (
                    $ancestors
                    as $ancestor_id
                ) {

                    $ancestor =
                        get_term(
                            $ancestor_id,
                            'ai_prompt_category'
                        );


                    if (
                        !$ancestor
                        ||
                        is_wp_error($ancestor)
                    ) {
                        continue;
                    }


                    $ancestor_url =
                        get_term_link(
                            $ancestor
                        );


                    if (
                        is_wp_error(
                            $ancestor_url
                        )
                    ) {
                        continue;
                    }


                    isago_render_breadcrumb_link(
                        $ancestor_url,
                        $ancestor->name
                    );
                }


                /*
                 * Current prompt category.
                 */
                $term_url =
                    get_term_link($term);


                if (
                    !is_wp_error(
                        $term_url
                    )
                ) {

                    isago_render_breadcrumb_link(
                        $term_url,
                        $term->name
                    );
                }
            }


            /*
             * Current prompt.
             */
            isago_render_breadcrumb_current(
                get_the_title($post_id)
            );


        /*-------------------------------------------------
        VIDEO
        -------------------------------------------------*/

        elseif (
            $post_type === 'isago_video'
        ) :

            $videos_url =
                get_post_type_archive_link(
                    'isago_video'
                );


            if ($videos_url) {

                isago_render_breadcrumb_link(
                    $videos_url,
                    'Videos'
                );

            }


            isago_render_breadcrumb_current(
                get_the_title($post_id)
            );


        /*-------------------------------------------------
        STANDARD BLOG POST
        -------------------------------------------------*/

        elseif (
            $post_type === 'post'
        ) :

            $categories =
                get_the_category(
                    $post_id
                );


            if (
                !empty($categories)
                &&
                !is_wp_error($categories)
            ) {

                $category =
                    reset($categories);


                /*
                 * Parent categories.
                 */
                $ancestors =
                    get_ancestors(
                        $category->term_id,
                        'category',
                        'taxonomy'
                    );


                $ancestors =
                    array_reverse(
                        $ancestors
                    );


                foreach (
                    $ancestors
                    as $ancestor_id
                ) {

                    $ancestor =
                        get_category(
                            $ancestor_id
                        );


                    if (
                        !$ancestor
                        ||
                        is_wp_error($ancestor)
                    ) {
                        continue;
                    }


                    $ancestor_url =
                        get_category_link(
                            $ancestor->term_id
                        );


                    if ($ancestor_url) {

                        isago_render_breadcrumb_link(
                            $ancestor_url,
                            $ancestor->name
                        );

                    }

                }


                /*
                 * Current category.
                 */
                isago_render_breadcrumb_link(
                    get_category_link(
                        $category->term_id
                    ),
                    $category->name
                );

            }


            /*
             * Current post.
             */
            isago_render_breadcrumb_current(
                get_the_title($post_id)
            );

        endif;


    /*=====================================================
    NORMAL WORDPRESS PAGE
    =====================================================*/

    elseif (is_page()) :

        $post_id =
            get_queried_object_id();


        /*
         * Parent pages.
         */
        $ancestors =
            get_post_ancestors(
                $post_id
            );


        $ancestors =
            array_reverse(
                $ancestors
            );


        foreach (
            $ancestors
            as $ancestor_id
        ) {

            $ancestor_url =
                get_permalink(
                    $ancestor_id
                );


            if (!$ancestor_url) {
                continue;
            }


            isago_render_breadcrumb_link(
                $ancestor_url,
                get_the_title(
                    $ancestor_id
                )
            );

        }


        /*
         * Current page.
         */
        isago_render_breadcrumb_current(
            get_the_title($post_id)
        );


    /*=====================================================
    SEARCH
    =====================================================*/

    elseif (is_search()) :

        isago_render_breadcrumb_current(
            'Search'
        );

    endif;

?>

</nav>