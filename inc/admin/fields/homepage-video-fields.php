<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
HOMEPAGE VIDEO CONTENT TYPES
=========================================================*/

$content_types = [

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


/*=========================================================
CURRENT CONTENT TYPE
=========================================================*/

$video_content_type =
    sanitize_key(
        $options['homepage_video_type']
        ?? 'blog'
    );


if (
    !isset(
        $content_types[
            $video_content_type
        ]
    )
) {

    $video_content_type = 'blog';

}


/*=========================================================
CURRENT SOURCE
=========================================================*/

$selected_video_category =
    $options[
        'homepage_video_category'
    ]
    ?? '__all';


if (
    $selected_video_category === ''
    ||
    $selected_video_category === null
) {

    $selected_video_category = '__all';

}


/*=========================================================
TYPE-SPECIFIC VALUES
=========================================================*/

$blog_value =
    '__all';

$prompt_value =
    '__all';

$tool_value =
    '__all';


if (
    $video_content_type === 'blog'
) {

    $blog_value =
        $selected_video_category;

}


elseif (
    $video_content_type === 'ai_prompt'
) {

    $prompt_value =
        $selected_video_category;

}


elseif (
    $video_content_type === 'ai_tool'
) {

    $tool_value =
        $selected_video_category;

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

                <i class="dashicons dashicons-video-alt3"></i>

            </span>

            <span>
                Homepage Video Section
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
                SECTION TITLE
                ================================================= -->

                <tr>

                    <th>
                        Section Title
                    </th>

                    <td>

                        <input
                            type="text"
                            class="regular-text"
                            name="isago_home_settings[homepage_video_title]"
                            value="<?php echo esc_attr(
                                $options[
                                    'homepage_video_title'
                                ]
                                ?? 'Videos'
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
                            class="isago-home-video-type"
                            name="isago_home_settings[homepage_video_type]"
                        >

                            <?php foreach (
                                $content_types
                                as $type => $label
                            ) : ?>

                                <option
                                    value="<?php echo esc_attr(
                                        $type
                                    ); ?>"
                                    <?php selected(
                                        $video_content_type,
                                        $type
                                    ); ?>
                                >

                                    <?php echo esc_html(
                                        $label
                                    ); ?>

                                </option>

                            <?php endforeach; ?>

                        </select>


                        <p class="description">

                            Choose the type of content used
                            for this homepage video section.

                        </p>

                    </td>

                </tr>


                <!-- =================================================
                CANONICAL SOURCE
                ================================================= -->

                <input
                    type="hidden"
                    class="isago-home-video-category-value"
                    name="isago_home_settings[homepage_video_category]"
                    value="<?php echo esc_attr(
                        $selected_video_category
                    ); ?>"
                >


                <!-- =================================================
                BLOG CATEGORY
                ================================================= -->

                <tr
                    class="isago-home-video-category-row"
                    data-video-type="blog"
                >

                    <th>
                        Blog Category
                    </th>

                    <td>

                        <select
                            class="isago-home-video-category-selector"
                            data-category-type="blog"
                        >

                            <option
                                value="__all"
                                <?php selected(
                                    $blog_value,
                                    '__all'
                                ); ?>
                            >

                                All Blog Videos

                            </option>


                            <?php foreach (
                                $blog_categories
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

                            Select the Blog category used
                            to filter the videos.

                        </p>

                    </td>

                </tr>


                <!-- =================================================
                AI PROMPT CATEGORY
                ================================================= -->

                <tr
                    class="isago-home-video-category-row"
                    data-video-type="ai_prompt"
                >

                    <th>
                        AI Prompt Category
                    </th>

                    <td>

                        <select
                            class="isago-home-video-category-selector"
                            data-category-type="ai_prompt"
                        >

                            <option
                                value="__all"
                                <?php selected(
                                    $prompt_value,
                                    '__all'
                                ); ?>
                            >

                                All AI Prompt Videos

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

                            Select the AI Prompt category
                            used to filter the videos.

                        </p>

                    </td>

                </tr>


                <!-- =================================================
                AI TOOL CATEGORY
                ================================================= -->

                <tr
                    class="isago-home-video-category-row"
                    data-video-type="ai_tool"
                >

                    <th>
                        AI Tool Category
                    </th>

                    <td>

                        <select
                            class="isago-home-video-category-selector"
                            data-category-type="ai_tool"
                        >

                            <option
                                value="__all"
                                <?php selected(
                                    $tool_value,
                                    '__all'
                                ); ?>
                            >

                                All AI Tool Videos

                            </option>


                            <?php foreach (
                                $ai_tool_categories
                                as $category
                            ) : ?>

                                <option
                                    value="<?php echo esc_attr(
                                        $category->term_id
                                    ); ?>"
                                    <?php selected(
                                        $tool_value,
                                        (string) $category->term_id
                                    ); ?>
                                >

                                    <?php echo esc_html(
                                        $category->name
                                    ); ?>

                                </option>

                            <?php endforeach; ?>

                        </select>


                        <p class="description">

                            Select the AI Tool category
                            used to filter the videos.

                        </p>

                    </td>

                </tr>


                <!-- =================================================
                NUMBER OF VIDEOS
                ================================================= -->

                <tr>

                    <th>
                        Number of Videos
                    </th>

                    <td>

                        <input
                            type="number"
                            min="3"
                            max="6"
                            name="isago_home_settings[homepage_video_posts]"
                            value="<?php echo esc_attr(
                                $options[
                                    'homepage_video_posts'
                                ]
                                ?? 3
                            ); ?>"
                        >

                    </td>

                </tr>


            </tbody>

        </table>

    </div>

</section>


<script>

jQuery(function ($) {

    /*
     * -------------------------------------------------------
     * Synchronize the hidden source field.
     * -------------------------------------------------------
     *
     * The visible selectors do not have a name attribute.
     *
     * Only this hidden field is submitted:
     *
     * homepage_video_category
     *
     */

    function isagoSyncHomepageVideoCategory() {

        var type =
            $('.isago-home-video-type').val();


        var selector =
            $('.isago-home-video-category-row')
                .filter('[data-video-type="' + type + '"]')
                .find('.isago-home-video-category-selector');


        var value =
            selector.length
                ? selector.val()
                : '__all';


        $('.isago-home-video-category-value')
            .val(value || '__all');

    }


    /*
     * -------------------------------------------------------
     * Update visible fields.
     * -------------------------------------------------------
     */

    function isagoUpdateHomepageVideoFields() {

        var type =
            $('.isago-home-video-type').val();


        /*
         * Hide every category selector.
         */

        $('.isago-home-video-category-row')
            .hide();


        /*
         * Show only the selector matching
         * the selected content type.
         */

        $('.isago-home-video-category-row')
            .filter('[data-video-type="' + type + '"]')
            .show();


        /*
         * Synchronize saved value.
         */

        isagoSyncHomepageVideoCategory();

    }


    /*
     * -------------------------------------------------------
     * Initial state.
     * -------------------------------------------------------
     */

    isagoUpdateHomepageVideoFields();


    /*
     * -------------------------------------------------------
     * Content type changed.
     * -------------------------------------------------------
     */

    $(document).on(
        'change',
        '.isago-home-video-type',
        function () {

            isagoUpdateHomepageVideoFields();

        }
    );


    /*
     * -------------------------------------------------------
     * Category changed.
     * -------------------------------------------------------
     */

    $(document).on(
        'change',
        '.isago-home-video-category-selector',
        function () {

            isagoSyncHomepageVideoCategory();

        }
    );


    /*
     * -------------------------------------------------------
     * Before form submission.
     * -------------------------------------------------------
     */

    $('.isago-home-builder-form').on(
        'submit',
        function () {

            isagoSyncHomepageVideoCategory();

        }
    );

});

</script>