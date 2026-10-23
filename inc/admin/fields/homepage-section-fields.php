<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
HOMEPAGE SECTION CONTENT TYPES
=========================================================*/

$content_types = [

    'none' =>
        'None',  

    'blog' =>
        'Blog Posts',

    'ai_prompt' =>
        'AI Prompts',

    'ai_tool' =>
        'AI Tools',

];


/*=========================================================
AI PROMPT CATEGORIES
=========================================================*/

$ai_prompt_categories = [];


if (
    function_exists(
        'isago_get_ai_prompt_categories'
    )
) {

    $ai_prompt_categories =
        isago_get_ai_prompt_categories([

            'hide_empty' =>
                false,

            'orderby' =>
                'name',

            'order' =>
                'ASC',

        ]);

}


if (
    is_wp_error(
        $ai_prompt_categories
    )
) {

    $ai_prompt_categories = [];

}


/*=========================================================
AI TOOL CATEGORIES
=========================================================*/

$ai_tool_categories = [];


if (
    function_exists(
        'isago_get_ai_tool_categories'
    )
) {

    $ai_tool_categories =
        isago_get_ai_tool_categories([

            'hide_empty' =>
                false,

            'orderby' =>
                'name',

            'order' =>
                'ASC',

        ]);

}


if (
    is_wp_error(
        $ai_tool_categories
    )
) {

    $ai_tool_categories = [];

}


/*=========================================================
BLOG CATEGORIES
=========================================================*/

$blog_categories =
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


if (
    is_wp_error(
        $blog_categories
    )
) {

    $blog_categories = [];

}

?>


<?php for ($i = 1; $i <= 8; $i++) : ?>

<?php

    /*
     * Sections 1–3:
     *
     * Parent categories.
     *
     * Sections 4–8:
     *
     * Child categories.
     */

    $is_parent_section =
        ($i <= 3);


    /*=====================================================
    CONTENT TYPE
    =====================================================*/

    $content_type =
        $options[
            "section{$i}_type"
        ]
        ?? 'none';


    if (
        !isset(
            $content_types[
                $content_type
            ]
        )
    ) {

        $content_type = 'none';

    }


    /*=====================================================
    CURRENT CANONICAL SOURCE
    =====================================================*/

    $selected_category =
        $options[
            "section{$i}_category"
        ]
        ?? '__all';


    if (
        $selected_category === ''
        ||
        $selected_category === null
    ) {

        $selected_category = '__all';

    }


    /*=====================================================
    BLOG CATEGORIES FOR THIS SECTION
    =====================================================*/

    $allowed_blog_categories = [];


    foreach (
        $blog_categories
        as $category
    ) {

        if (
            $is_parent_section
        ) {

            /*
             * Parent categories only.
             */

            if (
                (int) $category->parent === 0
            ) {

                $allowed_blog_categories[] =
                    $category;

            }

        } else {

            /*
             * Child categories.
             *
             * This includes direct children and
             * deeper descendants.
             */

            if (
                (int) $category->parent !== 0
            ) {

                $allowed_blog_categories[] =
                    $category;

            }

        }

    }


    /*=====================================================
    TYPE-SPECIFIC SAVED VALUES
    =====================================================*/

    $blog_value =
        '__all';

    $prompt_value =
        '__all';

    $tool_value =
        '__all';


    /*
     * Put the existing saved canonical value
     * into the selector belonging to the
     * currently selected content type.
     */

    if (
        $content_type === 'blog'
    ) {

        $blog_value =
            $selected_category;

    }


    elseif (
        $content_type === 'ai_prompt'
    ) {

        $prompt_value =
            $selected_category;

    }


    elseif (
        $content_type === 'ai_tool'
    ) {

        $tool_value =
            $selected_category;

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

                <i class="dashicons dashicons-screenoptions"></i>

            </span>

            <span>

                Homepage Section
                <?php echo esc_html($i); ?>

            </span>

        </span>


        <span class="isago-builder-toggle">

            <i class="dashicons dashicons-arrow-down-alt2"></i>

        </span>

    </button>


    <div class="isago-builder-card-content">

        <table class="form-table">

            <tbody>


                <!-- =================================================
                TITLE
                ================================================= -->

                <tr class="isago-home-section-setting">

                    <th>
                        Title
                    </th>

                    <td>

                        <input
                            class="regular-text"
                            type="text"
                            name="isago_home_settings[section<?php echo esc_attr($i); ?>_title]"
                            value="<?php echo esc_attr(
                                $options[
                                    "section{$i}_title"
                                ]
                                ?? ''
                            ); ?>"
                        >

                    </td>

                </tr>


                <!-- =================================================
                CONTENT TYPE
                ================================================= -->

                <tr>

                    <th>
                        Content Type
                    </th>

                    <td>

                        <select
                            class="isago-home-section-type"
                            name="isago_home_settings[section<?php echo esc_attr($i); ?>_type]"
                            data-section="<?php echo esc_attr($i); ?>"
                        >

                            <?php foreach (
                                $content_types
                                as $type => $label
                            ) : ?>

                                <option
                                    value="<?php echo esc_attr($type); ?>"
                                    <?php selected(
                                        $content_type,
                                        $type
                                    ); ?>
                                >

                                    <?php echo esc_html($label); ?>

                                </option>

                            <?php endforeach; ?>

                        </select>


                        <p class="description">

                            Choose what type of content this homepage
                            section should display.

                            Select <strong>None</strong> to disable
                            this section.

                        </p>

                    </td>

                </tr>


                <!-- =================================================
                CANONICAL SOURCE
                ================================================= -->

                <input
                    type="hidden"
                    class="isago-home-section-category-value"
                    name="isago_home_settings[section<?php echo esc_attr($i); ?>_category]"
                    value="<?php echo esc_attr(
                        $selected_category
                    ); ?>"
                >


                <!-- =================================================
                BLOG CATEGORY
                ================================================= -->

                <tr
                    class="isago-home-category-row isago-home-blog-category"
                    data-section-type="blog"
                >

                    <th>
                        Blog Category
                    </th>

                    <td>

                        <select
                            class="isago-home-category-selector"
                            data-category-type="blog"
                        >

                            <option
                                value="__all"
                                <?php selected(
                                    $blog_value,
                                    '__all'
                                ); ?>
                            >

                                All Blog Posts

                            </option>


                            <?php foreach (
                                $allowed_blog_categories
                                as $category
                            ) : ?>

                                <option
                                    value="<?php echo esc_attr(
                                        $category->slug
                                    ); ?>"
                                    <?php selected(
                                        $blog_value,
                                        $category->slug
                                    ); ?>
                                >

                                    <?php echo esc_html(
                                        $category->name
                                    ); ?>

                                </option>

                            <?php endforeach; ?>

                        </select>


                        <p class="description">

                            <?php if (
                                $is_parent_section
                            ) : ?>

                                Select a parent category or display
                                all blog posts.

                            <?php else : ?>

                                Select a subcategory or display
                                all blog posts.

                            <?php endif; ?>

                        </p>

                    </td>

                </tr>


                <!-- =================================================
                AI PROMPT CATEGORY
                ================================================= -->

                <tr
                    class="isago-home-category-row isago-home-prompt-category"
                    data-section-type="ai_prompt"
                >

                    <th>
                        AI Prompt Category
                    </th>

                    <td>

                        <select
                            class="isago-home-category-selector"
                            data-category-type="ai_prompt"
                        >

                            <option
                                value="__all"
                                <?php selected(
                                    $prompt_value,
                                    '__all'
                                ); ?>
                            >

                                All AI Prompts

                            </option>


                            <?php foreach (
                                $ai_prompt_categories
                                as $category
                            ) : ?>

                                <option
                                    value="<?php echo esc_attr(
                                        $category->slug
                                    ); ?>"
                                    <?php selected(
                                        $prompt_value,
                                        $category->slug
                                    ); ?>
                                >

                                    <?php echo esc_html(
                                        $category->name
                                    ); ?>

                                </option>

                            <?php endforeach; ?>

                        </select>


                        <p class="description">

                            Select a specific AI Prompt category
                            or display all AI Prompts.

                        </p>

                    </td>

                </tr>


                

        <!-- =================================================
AI TOOL CATEGORY / CURATION
================================================= -->

<tr
    class="isago-home-category-row isago-home-tool-category"
    data-section-type="ai_tool"
>

    <th>
        AI Tool Category / Curation
    </th>

    <td>

        <select
            class="isago-home-category-selector"
            data-category-type="ai_tool"
        >

            <!-- ALL TOOLS -->

            <option
                value="__all"
                <?php selected(
                    $tool_value,
                    '__all'
                ); ?>
            >
                All AI Tools
            </option>


            <!-- CURATED TOOL LISTS -->

            <optgroup label="AI Tool Curation">

                <option
                    value="__curated_new"
                    <?php selected(
                        $tool_value,
                        '__curated_new'
                    ); ?>
                >
                    New Tools
                </option>

                <option
                    value="__curated_featured"
                    <?php selected(
                        $tool_value,
                        '__curated_featured'
                    ); ?>
                >
                    Featured Tools
                </option>

                <option
                    value="__curated_trending"
                    <?php selected(
                        $tool_value,
                        '__curated_trending'
                    ); ?>
                >
                    Trending / Hot Tools
                </option>

                <option
                    value="__curated_popular"
                    <?php selected(
                        $tool_value,
                        '__curated_popular'
                    ); ?>
                >
                    Most Popular Tools
                </option>

                <option
                    value="__curated_free"
                    <?php selected(
                        $tool_value,
                        '__curated_free'
                    ); ?>
                >
                    Free Tools
                </option>

            </optgroup>


            <!-- NORMAL TOOL CATEGORIES -->

            <optgroup label="AI Tool Categories">

                <?php foreach (
                    $ai_tool_categories as $category
                ) : ?>

                    <option
                        value="<?php echo esc_attr(
                            $category->term_id
                        ); ?>"
                        <?php selected(
                            (string) $tool_value,
                            (string) $category->term_id
                        ); ?>
                    >
                        <?php echo esc_html(
                            $category->name
                        ); ?>
                    </option>

                <?php endforeach; ?>

            </optgroup>

        </select>

        <p class="description">
            Display all AI Tools, select a curated list
            using your AI Tool Curation flags, or choose
            a specific AI Tool category.
        </p>

    </td>

</tr>


                <!-- =================================================
                NUMBER OF ITEMS
                ================================================= -->

                <tr class="isago-home-section-setting">

                    <th>
                        Number of Items
                    </th>

                    <td>

                        <input
                            type="number"
                            min="3"
                            max="8"
                            name="isago_home_settings[section<?php echo esc_attr($i); ?>_posts]"
                            value="<?php echo esc_attr(
                                $options[
                                    "section{$i}_posts"
                                ]
                                ?? 4
                            ); ?>"
                        >

                    </td>

                </tr>



<!-- =================================================
CONTENT ORDER
================================================= -->

<tr class="isago-home-section-setting">

    <th>
        Order Content By
    </th>

    <td>

        <select
            name="isago_home_settings[section<?php echo esc_attr($i); ?>_orderby]"
        >

            <option
                value="latest"
                <?php selected(
                    $options["section{$i}_orderby"] ?? 'latest',
                    'latest'
                ); ?>
            >
                Latest — Newest First
            </option>

            <option
                value="older"
                <?php selected(
                    $options["section{$i}_orderby"] ?? 'latest',
                    'older'
                ); ?>
            >
                Older — Oldest First
            </option>

            <option
                value="modified"
                <?php selected(
                    $options["section{$i}_orderby"] ?? 'latest',
                    'modified'
                ); ?>
            >
                Recently Updated
            </option>

        </select>

        <p class="description">
            Choose whether this section displays the latest
            content, older content, or recently updated content.
        </p>

    </td>

</tr>



                <!-- =================================================
                LAYOUT
                ================================================= -->

                <tr class="isago-home-section-setting">

                    <th>
                        Layout
                    </th>

                    <td>

                        <select
                            name="isago_home_settings[section<?php echo esc_attr($i); ?>_layout]"
                        >

                            <option
                                value="editorial"
                                <?php selected(
                                    $options[
                                        "section{$i}_layout"
                                    ]
                                    ?? 'editorial',
                                    'editorial'
                                ); ?>
                            >

                                Editorial

                            </option>


                            <option
                                value="magazine"
                                <?php selected(
                                    $options[
                                        "section{$i}_layout"
                                    ]
                                    ?? 'editorial',
                                    'magazine'
                                ); ?>
                            >

                                Magazine

                            </option>


                            <option
                                value="list"
                                <?php selected(
                                    $options[
                                        "section{$i}_layout"
                                    ]
                                    ?? 'editorial',
                                    'list'
                                ); ?>
                            >

                                List

                            </option>


                            <option
                                value="grid"
                                <?php selected(
                                    $options[
                                        "section{$i}_layout"
                                    ]
                                    ?? 'editorial',
                                    'grid'
                                ); ?>
                            >

                                Grid

                            </option>

                        </select>

                    </td>

                </tr>


                <!-- =================================================
                ADVERTISEMENT
                ================================================= -->

                <tr class="isago-home-section-setting">

                    <th>
                        Advertisement
                    </th>

                    <td>

                        <label>

                            <input
                                type="checkbox"
                                value="1"
                                name="isago_home_settings[section<?php echo esc_attr($i); ?>_ad]"
                                <?php checked(
                                    $options[
                                        "section{$i}_ad"
                                    ]
                                    ?? 0,
                                    1
                                ); ?>
                            >

                            Show Advertisement

                        </label>

                    </td>

                </tr>


            </tbody>

        </table>

    </div>

</section>

<?php endfor; ?>


<!-- =========================================================
HOMEPAGE SECTION TYPE / CATEGORY SWITCHING
========================================================= -->

<script>

jQuery(function ($) {


    /*
     * -------------------------------------------------------
     * Update the canonical category value.
     * -------------------------------------------------------
     *
     * WordPress receives only:
     *
     * sectionX_category
     *
     * The visible selectors themselves have NO name
     * attribute, preventing multiple values from being
     * submitted for the same setting.
     */

    function isagoSyncHomepageCategory(section) {

        var type =
            section
                .find('.isago-home-section-type')
                .val();


        var selector =
            section.find(
                '.isago-home-category-row[data-section-type="' +
                type +
                '"] .isago-home-category-selector'
            );


        var value =
            '__all';


        if (
            selector.length
        ) {

            value =
                selector.val()
                || '__all';

        }


        section
            .find('.isago-home-section-category-value')
            .val(value);

    }


    /*
     * -------------------------------------------------------
     * Show/hide fields.
     * -------------------------------------------------------
     */

    function isagoUpdateHomepageSection(section) {

        var type =
            section
                .find('.isago-home-section-type')
                .val();


        /*
         * Hide all category selectors.
         */

        section
            .find('.isago-home-category-row')
            .hide();


        /*
         * Show selected category selector.
         */

        if (
            type !== 'none'
        ) {

            section
                .find(
                    '.isago-home-category-row[data-section-type="' +
                    type +
                    '"]'
                )
                .show();

        }


        /*
         * Hide normal section settings
         * when disabled.
         */

        if (
            type === 'none'
        ) {

            section
                .find('.isago-home-section-setting')
                .hide();

        } else {

            section
                .find('.isago-home-section-setting')
                .show();

        }


        /*
         * Make sure the canonical hidden field
         * contains the active selector value.
         */

        isagoSyncHomepageCategory(
            section
        );

    }


    /*
     * -------------------------------------------------------
     * Initialize sections.
     * -------------------------------------------------------
     */

    $('.isago-builder-card').each(function () {

        var section =
            $(this);


        if (
            section.find(
                '.isago-home-section-type'
            ).length
        ) {

            isagoUpdateHomepageSection(
                section
            );

        }

    });


    /*
     * -------------------------------------------------------
     * Content type changed.
     * -------------------------------------------------------
     */

    $(document).on(
        'change',
        '.isago-home-section-type',
        function () {

            var section =
                $(this).closest(
                    '.isago-builder-card'
                );


            /*
             * If changing content type,
             * use the existing selected value
             * for that content type if available.
             */

            isagoUpdateHomepageSection(
                section
            );

        }
    );


    /*
     * -------------------------------------------------------
     * Category changed.
     * -------------------------------------------------------
     */

    $(document).on(
        'change',
        '.isago-home-category-selector',
        function () {

            var section =
                $(this).closest(
                    '.isago-builder-card'
                );


            isagoSyncHomepageCategory(
                section
            );

        }
    );


    /*
     * -------------------------------------------------------
     * Before form submission.
     * -------------------------------------------------------
     *
     * Final synchronization guarantees the hidden
     * canonical value is correct.
     */

    $('.isago-home-builder-form').on(
        'submit',
        function () {

            var form =
                $(this);


            form.find(
                '.isago-builder-card'
            ).each(function () {

                isagoSyncHomepageCategory(
                    $(this)
                );

            });

        }
    );

});

</script>