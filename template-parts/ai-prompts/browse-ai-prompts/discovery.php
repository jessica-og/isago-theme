<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
VARIABLES
=========================================================*/

$selected_category =
    $args['selected_category']
    ?? '';

$selected_platform =
    isset($args['selected_platform'])
        ? absint($args['selected_platform'])
        : 0;

$search =
    $args['search']
    ?? '';

$prompt_categories =
    $args['prompt_categories']
    ?? array();

$prompt_platforms =
    $args['prompt_platforms']
    ?? array();


/*=========================================================
SELECTED CATEGORY NAME
=========================================================*/

$selected_category_name =
    'All categories';

if (!empty($selected_category)) {

    foreach ($prompt_categories as $category) {

        if (
            $category->slug ===
            $selected_category
        ) {

            $selected_category_name =
                $category->name;

            break;
        }
    }
}


/*=========================================================
SELECTED PLATFORM NAME
=========================================================*/

$selected_platform_name =
    'All AI tools';

if ($selected_platform) {

    foreach ($prompt_platforms as $platform) {

        if (
            (int) $platform->term_id ===
            $selected_platform
        ) {

            $selected_platform_name =
                $platform->name;

            break;
        }
    }
}


/*=========================================================
BUILD FILTER URL
=========================================================*/

$build_filter_url = function (
    $category = '',
    $platform = '',
    $include_search = true
) use ($search) {

    $url_args = array();

    if (!empty($category)) {

        $url_args['prompt_category'] =
            $category;
    }

    if (!empty($platform)) {

        $url_args['prompt_platform'] =
            absint($platform);
    }

    if (
        $include_search
        &&
        !empty($search)
    ) {

        $url_args['prompt_search'] =
            $search;
    }

    $url =
        get_permalink();

    if (!empty($url_args)) {

        $url =
            add_query_arg(
                $url_args,
                $url
            );
    }

    return $url;
};


/*=========================================================
ALL PROMPTS URL
=========================================================*/

$all_prompts_url =
    get_permalink();


/*=========================================================
CLEAR SEARCH URL
=========================================================*/

$clear_search_url =
    $build_filter_url(
        $selected_category,
        $selected_platform,
        false
    );

?>

<section
    class="ai-platform-discovery"
>

    <div class="container">


        <!-- =================================================
        SEARCH
        ================================================= -->

        <form
            method="get"
            action="<?php echo esc_url(get_permalink()); ?>"
            class="ai-platform-discovery-form"
            data-ai-prompt-search-form
        >

            <div
                class="ai-platform-search-box"
            >

                <i
                    class="fa-solid fa-magnifying-glass"
                    aria-hidden="true"
                ></i>


                <label
                    for="prompt-search"
                    class="screen-reader-text"
                >
                    Search AI prompts
                </label>


                <input
                    type="search"
                    id="prompt-search"
                    name="prompt_search"
                    value="<?php echo esc_attr($search); ?>"
                    placeholder="Search prompts, tools, categories, tags..."
                    autocomplete="off"
                >


                <!-- =================================================
                CLEAR SEARCH
                ================================================= -->

                <a
                    href="<?php echo esc_url($clear_search_url); ?>"
                    class="
                        ai-platform-search-clear
                        <?php echo empty($search)
                            ? 'is-hidden'
                            : ''; ?>
                    "
                    aria-label="Clear search"
                    data-ai-prompt-clear
                    <?php echo empty($search)
                        ? 'aria-hidden="true"'
                        : ''; ?>
                >

                    <i
                        class="fa-solid fa-xmark"
                        aria-hidden="true"
                    ></i>

                </a>


                <button
                    type="submit"
                    class="ai-platform-search-button"
                >
                    Search
                </button>

            </div>


            <!-- =================================================
            PRESERVE ACTIVE FILTERS
            ================================================= -->

            <?php if (!empty($selected_category)) : ?>

                <input
                    type="hidden"
                    name="prompt_category"
                    value="<?php echo esc_attr(
                        $selected_category
                    ); ?>"
                >

            <?php endif; ?>


            <?php if ($selected_platform) : ?>

                <input
                    type="hidden"
                    name="prompt_platform"
                    value="<?php echo esc_attr(
                        $selected_platform
                    ); ?>"
                >

            <?php endif; ?>

        </form>


        <!-- =================================================
        FILTER CONTROLS
        ================================================= -->

        <div
            class="ai-prompt-directory-filters"
        >


            <!-- =================================================
            ALL PROMPTS
            ================================================= -->

            <a
                href="<?php echo esc_url(  
                    $all_prompts_url
                ); ?>"
                class="ai-prompt-filter-all"
                data-ai-prompt-all
            >

                <i
                    class="fa-solid fa-layer-group"
                    aria-hidden="true"
                ></i>

                <span>
                    All
                </span>

            </a>
  

            <!-- =================================================
            CATEGORY
            ================================================= -->

            <details
                class="ai-prompt-filter-dropdown"
            >

                <summary
                    class="ai-prompt-filter-button"
                >

                    <span>

                        <i
                            class="fa-solid fa-folder"
                            aria-hidden="true"
                        ></i>

                        <span
                            data-ai-prompt-category-label
                        >
                            <?php echo esc_html(
                                $selected_category_name
                            ); ?>
                        </span>

                    </span>

                    <i
                        class="fa-solid fa-chevron-down"
                        aria-hidden="true"
                    ></i>

                </summary>


                <div
                    class="ai-prompt-filter-menu"
                >


                    <!-- ALL CATEGORIES -->

                    <a
                        href="<?php echo esc_url(
                            $build_filter_url(
                                '',
                                $selected_platform
                            )
                        ); ?>"
                        class="
                            ai-prompt-filter-option
                            <?php echo empty(
                                $selected_category
                            )
                                ? 'is-active'
                                : ''; ?>
                        "
                        data-ai-prompt-category=""
                        data-ai-prompt-category-name="All categories"
                    >

                        <span>
                            All categories
                        </span>

                      

                    </a>


                  
                    <!-- CATEGORY OPTIONS: HIERARCHICAL TREE -->

                    <?php

                    /*
                     * Group categories by their parent term ID.
                     */
                    $categories_by_parent = [];

                    foreach ($prompt_categories as $category) {
                        $parent_id = (int) $category->parent;

                        if (!isset($categories_by_parent[$parent_id])) {
                            $categories_by_parent[$parent_id] = [];
                        }

                        $categories_by_parent[$parent_id][] = $category;
                    }

                    /*
                     * Render each level recursively.
                     */
                    $render_category_tree = function ($parent_id = 0) use (
                        &$render_category_tree,
                        $categories_by_parent,
                        $selected_category,
                        $selected_platform,
                        $build_filter_url
                    ) {

                        if (empty($categories_by_parent[$parent_id])) {
                            return;
                        }

                        foreach ($categories_by_parent[$parent_id] as $category) {

                            $category_id = (int) $category->term_id;

                            $children = $categories_by_parent[$category_id] ?? [];

                            $has_children = !empty($children);

                            $children_id = 'ai-prompt-category-children-' . $category_id;

                            $is_active = (
                                $selected_category === $category->slug
                            );

                            $category_url = $build_filter_url(
                                $category->slug,
                                $selected_platform
                            );

                    ?>

                            <div class="ai-prompt-filter-category-node">

                                <div class="ai-prompt-filter-category-row">

                                    <a
                                        href="<?php echo esc_url($category_url); ?>"
                                        class="ai-prompt-filter-option<?php
                                            echo $is_active ? ' is-active' : '';
                                        ?>"
                                        data-ai-prompt-category="<?php
                                            echo esc_attr($category->slug);
                                        ?>"
                                        data-ai-prompt-category-name="<?php
                                            echo esc_attr($category->name);
                                        ?>"
                                        <?php if ($is_active) : ?>
                                            aria-current="true"
                                        <?php endif; ?>
                                    >

                                        <span>
                                            <?php
                                            echo esc_html($category->name);
                                            ?>
                                        </span>

                                        <?php if ($is_active) : ?>
                                            <i
                                                class="fa-solid fa-check"
                                                aria-hidden="true"
                                            ></i>
                                        <?php endif; ?>

                                    </a>

                                    <?php if ($has_children) : ?>

                                        <button
                                            type="button"
                                            class="ai-prompt-filter-category-toggle"
                                            data-ai-prompt-category-toggle
                                            aria-expanded="false"
                                            aria-controls="<?php
                                                echo esc_attr($children_id);
                                            ?>"
                                            aria-label="<?php
                                                echo esc_attr(
                                                    'Expand ' . $category->name . ' subcategories'
                                                );
                                            ?>"
                                        >

                                            <i
                                                class="fa-solid fa-chevron-right"
                                                aria-hidden="true"
                                            ></i>

                                        </button>

                                    <?php endif; ?>

                                </div>

                                <?php if ($has_children) : ?>

                                    <div
                                        id="<?php echo esc_attr($children_id); ?>"
                                        class="ai-prompt-filter-category-children"
                                        hidden
                                    >

                                        <?php
                                        $render_category_tree($category_id);
                                        ?>

                                    </div>

                                <?php endif; ?>

                            </div>

                    <?php

                        }
                    };

                    /*
                     * Begin with top-level categories.
                     */
                    $render_category_tree(0);

                    ?>


                </div>

            </details>


            <!-- =================================================
            AI TOOL
            ================================================= -->

            <details
                class="ai-prompt-filter-dropdown"
            >

                <summary
                    class="ai-prompt-filter-button"
                >

                    <span>

                        <i
                            class="fa-solid fa-sparkles"
                            aria-hidden="true"
                        ></i>

                        <span
                            data-ai-prompt-platform-label
                        >
                            <?php echo esc_html(
                                $selected_platform_name
                            ); ?>
                        </span>

                    </span>

                    <i
                        class="fa-solid fa-chevron-down"
                        aria-hidden="true"
                    ></i>

                </summary>


                <div
                    class="ai-prompt-filter-menu"
                >


                    <!-- ALL AI TOOLS -->

                    <a
                        href="<?php echo esc_url(
                            $build_filter_url(
                                $selected_category,
                                ''
                            )
                        ); ?>"
                        class="
                            ai-prompt-filter-option
                            <?php echo !$selected_platform
                                ? 'is-active'
                                : ''; ?>
                        "
                        data-ai-prompt-platform=""
                        data-ai-prompt-platform-name="All AI tools"
                    >

                        <span>
                            All AI tools
                        </span>

                    

                    </a>


                    <!-- AI TOOL OPTIONS -->

                    <?php foreach (
                        $prompt_platforms
                        as $platform
                    ) : ?>

                        <a
                            href="<?php echo esc_url(
                                $build_filter_url(
                                    $selected_category,
                                    $platform->term_id
                                )
                            ); ?>"
                            class="
                                ai-prompt-filter-option
                                <?php echo (
                                    $selected_platform ===
                                    (int) $platform->term_id
                                )
                                    ? 'is-active'
                                    : ''; ?>
                            "
                            data-ai-prompt-platform="<?php echo esc_attr(
                                $platform->term_id
                            ); ?>"
                            data-ai-prompt-platform-name="<?php echo esc_attr(
                                $platform->name
                            ); ?>"
                        >

                            <span>
                                <?php echo esc_html(
                                    $platform->name
                                ); ?>
                            </span>

                            <?php if (
                                $selected_platform ===
                                (int) $platform->term_id
                            ) : ?>

                                <i
                                    class="fa-solid fa-check"
                                    aria-hidden="true"
                                ></i>

                            <?php endif; ?>

                        </a>

                    <?php endforeach; ?>

                </div>

            </details>


            <!-- =================================================
            CLEAR SEARCH FILTER
            ================================================= -->

            <?php if (!empty($search)) : ?>

                <a
                    href="<?php echo esc_url(
                        $clear_search_url
                    ); ?>"
                    class="ai-prompt-filter-clear-search"
                    data-ai-prompt-clear
                >

                    <i
                        class="fa-solid fa-xmark"
                        aria-hidden="true"
                    ></i>

                    <span>
                        Clear search
                    </span>

                </a>

            <?php endif; ?>


        </div>

    </div>

</section>