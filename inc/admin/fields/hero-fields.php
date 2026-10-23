<?php

if (!defined('ABSPATH')) {
    exit;
}

?>


<section class="isago-builder-card">


    <button
        type="button"
        class="isago-builder-card-header"
        aria-expanded="false"
    >

        <span class="isago-builder-card-title">

            <span class="isago-builder-card-icon">

                <i class="dashicons dashicons-format-image"></i>

            </span>

            <span>
                Hero Settings
            </span>

        </span>


        <span class="isago-builder-toggle">

            <i class="dashicons dashicons-arrow-down-alt2"></i>

        </span>

    </button>


    <div class="isago-builder-card-content">


        <table class="form-table">

            <tbody>


                <!--=================================================
                FEATURED HERO
                =================================================-->

                <tr>

                    <th>
                        Featured Hero Post
                    </th>

                    <td>

                        <?php

                        $featured_posts =
                            isago_admin_posts();

                        ?>

                        <select
                            name="isago_home_settings[hero_featured]"
                        >

                            <option value="">
                                Select Featured Post
                            </option>


                            <?php foreach (
                                $featured_posts
                                as $post
                            ) : ?>

                                <option
                                    value="<?php echo esc_attr(
                                        $post->ID
                                    ); ?>"
                                    <?php selected(
                                        $options['hero_featured'] ?? '',
                                        $post->ID
                                    ); ?>
                                >

                                    <?php echo esc_html(
                                        $post->post_title
                                    ); ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </td>

                </tr>


                <!--=================================================
                LEFT HERO CONTENT TYPE
                =================================================-->

                <tr>

                    <th>
                        Left Cards Content Type
                    </th>

                    <td>

                        <?php

                        $hero_left_type =
                            sanitize_key(
                                $options['hero_left_type']
                                ?? 'blog'
                            );


                        if (
                            !in_array(
                                $hero_left_type,
                                [
                                    'blog',
                                    'ai_prompt',
                                    'ai_tool'
                                ],
                                true
                            )
                        ) {

                            $hero_left_type = 'blog';

                        }

                        ?>

                        <select
                            class="isago-hero-content-type"
                            name="isago_home_settings[hero_left_type]"
                        >

                            <option
                                value="blog"
                                <?php selected(
                                    $hero_left_type,
                                    'blog'
                                ); ?>
                            >
                                Blog
                            </option>


                            <option
                                value="ai_prompt"
                                <?php selected(
                                    $hero_left_type,
                                    'ai_prompt'
                                ); ?>
                            >
                                AI Prompts
                            </option>


                            <option
                                value="ai_tool"
                                <?php selected(
                                    $hero_left_type,
                                    'ai_tool'
                                ); ?>
                            >
                                AI Tools
                            </option>

                        </select>

                    </td>

                </tr>


                <!--=================================================
                LEFT HERO CATEGORY
                =================================================-->

                <tr>

                    <th>
                        Left Cards Category
                    </th>

                    <td>

                        <?php

                        /*
                         * =====================================================
                         * BLOG CATEGORIES
                         * =====================================================
                         */

                        $hero_blog_categories =
                            get_categories([

                                'taxonomy' =>
                                    'category',

                                'hide_empty' =>
                                    false,

                                'orderby' =>
                                    'name',

                                'order' =>
                                    'ASC',

                            ]);


                        /*
                         * =====================================================
                         * AI PROMPT CATEGORIES
                         * =====================================================
                         */

                        $hero_prompt_categories = [];


                        if (
                            function_exists(
                                'isago_get_ai_prompt_categories'
                            )
                        ) {

                            $hero_prompt_categories =
                                isago_get_ai_prompt_categories();

                        }


                        /*
                         * =====================================================
                         * AI TOOL CATEGORIES
                         * =====================================================
                         */

                        $hero_tool_categories = [];


                        if (
                            function_exists(
                                'isago_get_ai_tool_categories'
                            )
                        ) {

                            $hero_tool_categories =
                                isago_get_ai_tool_categories();

                        }


                        /*
                         * =====================================================
                         * SAVED VALUE
                         * =====================================================
                         *
                         * This is the ONLY value that will be submitted.
                         *
                         * Blog:
                         *      category slug
                         *
                         * AI Prompt:
                         *      category slug
                         *
                         * AI Tool:
                         *      category term ID
                         */

                        $hero_left_category =
                            $options[
                                'hero_left_category'
                            ]
                            ?? '';

                        ?>


                        <!--=================================================
                        CANONICAL SAVED VALUE
                        =================================================-->

                        <input
                            type="hidden"
                            class="isago-hero-category-value"
                            name="isago_home_settings[hero_left_category]"
                            value="<?php echo esc_attr(
                                $hero_left_category
                            ); ?>"
                        >


                        <!--=================================================
                        BLOG CATEGORY
                        =================================================-->

                        <select
                            class="isago-hero-category-selector"
                            data-hero-type="blog"
                            <?php
                            echo $hero_left_type === 'blog'
                                ? ''
                                : 'style="display:none;"';
                            ?>
                        >

                            <option value="">
                                Select Blog Category
                            </option>


                            <?php foreach (
                                $hero_blog_categories
                                as $category
                            ) : ?>

                                <option
                                    value="<?php echo esc_attr(
                                        $category->slug
                                    ); ?>"
                                    <?php selected(
                                        $hero_left_type === 'blog'
                                            ? $hero_left_category
                                            : '',
                                        $category->slug
                                    ); ?>
                                >

                                    <?php echo esc_html(
                                        $category->name
                                    ); ?>

                                </option>

                            <?php endforeach; ?>

                        </select>


                        <!--=================================================
                        AI PROMPT CATEGORY
                        =================================================-->

                        <select
                            class="isago-hero-category-selector"
                            data-hero-type="ai_prompt"
                            <?php
                            echo $hero_left_type === 'ai_prompt'
                                ? ''
                                : 'style="display:none;"';
                            ?>
                        >

                            <option value="">
                                Select AI Prompt Category
                            </option>


                            <?php foreach (
                                $hero_prompt_categories
                                as $category
                            ) : ?>

                                <option
                                    value="<?php echo esc_attr(
                                        $category->slug
                                    ); ?>"
                                    <?php selected(
                                        $hero_left_type === 'ai_prompt'
                                            ? $hero_left_category
                                            : '',
                                        $category->slug
                                    ); ?>
                                >

                                    <?php echo esc_html(
                                        $category->name
                                    ); ?>

                                </option>

                            <?php endforeach; ?>

                        </select>


                        <!--=================================================
                        AI TOOL CATEGORY
                        =================================================-->

                        <select
                            class="isago-hero-category-selector"
                            data-hero-type="ai_tool"
                            <?php
                            echo $hero_left_type === 'ai_tool'
                                ? ''
                                : 'style="display:none;"';
                            ?>
                        >

                            <option value="">
                                Select AI Tool Category
                            </option>


                            <?php foreach (
                                $hero_tool_categories
                                as $category
                            ) : ?>

                                <option
                                    value="<?php echo esc_attr(
                                        $category->term_id
                                    ); ?>"
                                    <?php selected(
                                        $hero_left_type === 'ai_tool'
                                            ? $hero_left_category
                                            : '',
                                        $category->term_id
                                    ); ?>
                                >

                                    <?php echo esc_html(
                                        $category->name
                                    ); ?>

                                </option>

                            <?php endforeach; ?>

                        </select>


                    </td>

                </tr>


            </tbody>

        </table>


    </div>

</section>


<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {


        /*
         * =====================================================
         * HERO CONTENT TYPE
         * =====================================================
         */

        const typeSelect =
            document.querySelector(
                '.isago-hero-content-type'
            );


        /*
         * =====================================================
         * HERO CATEGORY SELECTORS
         * =====================================================
         */

        const categorySelectors =
            document.querySelectorAll(
                '.isago-hero-category-selector'
            );


        /*
         * =====================================================
         * CANONICAL SAVED VALUE
         * =====================================================
         */

        const categoryValue =
            document.querySelector(
                '.isago-hero-category-value'
            );


        /*
         * Stop if Hero fields are unavailable.
         */

        if (
            !typeSelect
            ||
            !categoryValue
        ) {

            return;

        }


        /*
         * =====================================================
         * SHOW CORRECT CATEGORY SELECTOR
         * =====================================================
         */

        function updateHeroCategorySelector(
            clearValue = false
        ) {

            const selectedType =
                typeSelect.value;


            categorySelectors.forEach(
                function (selector) {

                    const selectorType =
                        selector.dataset.heroType;


                    if (
                        selectorType === selectedType
                    ) {

                        selector.style.display =
                            '';

                    } else {

                        selector.style.display =
                            'none';

                    }

                }
            );


            /*
             * When changing content type,
             * clear the old category.
             */

            if (
                clearValue
            ) {

                categoryValue.value =
                    '';


                categorySelectors.forEach(
                    function (selector) {

                        selector.value =
                            '';

                    }
                );

            }

        }


        /*
         * =====================================================
         * SYNC CATEGORY
         * =====================================================
         *
         * Only the currently visible selector writes
         * to the canonical hidden field.
         */

        function syncHeroCategory()
        {

            const selectedType =
                typeSelect.value;


            let selectedValue =
                '';


            categorySelectors.forEach(
                function (selector) {

                    if (
                        selector.dataset.heroType ===
                        selectedType
                    ) {

                        selectedValue =
                            selector.value;

                    }

                }
            );


            categoryValue.value =
                selectedValue;

        }


        /*
         * =====================================================
         * CONTENT TYPE CHANGE
         * =====================================================
         */

        typeSelect.addEventListener(
            'change',
            function () {

                updateHeroCategorySelector(
                    true
                );

            }
        );


        /*
         * =====================================================
         * CATEGORY CHANGE
         * =====================================================
         */

        categorySelectors.forEach(
            function (selector) {

                selector.addEventListener(
                    'change',
                    function () {

                        syncHeroCategory();

                    }
                );

            }
        );


        /*
         * =====================================================
         * INITIALIZE
         * =====================================================
         */

        updateHeroCategorySelector(
            false
        );


    }
);

</script>