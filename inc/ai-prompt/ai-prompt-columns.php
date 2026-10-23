<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
AI PROMPT ADMIN MEDIA THUMBNAIL
=========================================================*/

function isago_get_admin_ai_prompt_thumbnail(
    $post_id
) {

    $post_id =
        absint(
            $post_id
        );


    if (!$post_id) {

        return '';

    }


    /*
    |--------------------------------------------------------------------------
    | COMMON
    |--------------------------------------------------------------------------
    */

    $title =
        get_the_title(
            $post_id
        );


    $alt =
        esc_attr(
            $title
        );


    $thumbnail_style =
        '
            width:80px;
            height:45px;
            object-fit:cover;
            display:block;
            border-radius:4px;
            background:#000;
        ';


    $placeholder_url =
        get_template_directory_uri()
        . '/assets/images/placeholder.png';


    /*
    |--------------------------------------------------------------------------
    | 1. SOCIAL / VIDEO URL
    |--------------------------------------------------------------------------
    |
    | Social URL takes priority.
    |
    */

    $prompt_video_source_url =
        get_post_meta(
            $post_id,
            '_isago_ai_prompt_video_source_url',
            true
        );


    /*
     * Legacy YouTube fallback.
     */

    if (
        empty(
            $prompt_video_source_url
        )
    ) {

        $prompt_video_source_url =
            get_post_meta(
                $post_id,
                '_isago_ai_prompt_youtube_url',
                true
            );

    }


    $prompt_video_source_url =
        is_string(
            $prompt_video_source_url
        )
            ? trim(
                $prompt_video_source_url
            )
            : '';


    /*
    |--------------------------------------------------------------------------
    | 1A. YOUTUBE
    |--------------------------------------------------------------------------
    */

    if (
        !empty(
            $prompt_video_source_url
        )
    ) {

        $youtube_id =
            isago_get_youtube_id(
                $prompt_video_source_url
            );


        if (
            !empty(
                $youtube_id
            )
        ) {

            return sprintf(

                '<img
                    class="isago-admin-ai-prompt-thumbnail"
                    src="https://img.youtube.com/vi/%s/hqdefault.jpg"
                    alt="%s"
                    loading="lazy"
                    width="80"
                    height="45"
                    style="%s"
                >',

                esc_attr(
                    $youtube_id
                ),

                $alt,

                esc_attr(
                    $thumbnail_style
                )

            );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | 1B. OTHER SOCIAL VIDEO
    |--------------------------------------------------------------------------
    |
    | Instagram
    | Pinterest
    | TikTok
    | Vimeo
    |
    */

    if (
        !empty(
            $prompt_video_source_url
        )
        &&
        function_exists(
            'isago_get_social_video_thumbnail_url'
        )
    ) {

        $social_thumbnail_url =
            isago_get_social_video_thumbnail_url(
                $prompt_video_source_url
            );


        if (
            !empty(
                $social_thumbnail_url
            )
        ) {

            return sprintf(

                '<img
                    class="isago-admin-ai-prompt-thumbnail"
                    src="%s"
                    alt="%s"
                    loading="lazy"
                    width="80"
                    height="45"
                    style="%s"
                    onerror="this.onerror=null;this.src=\'%s\';"
                >',

                esc_url(
                    $social_thumbnail_url
                ),

                $alt,

                esc_attr(
                    $thumbnail_style
                ),

                esc_url(
                    $placeholder_url
                )

            );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | 2. PROMPT IMAGE
    |--------------------------------------------------------------------------
    */

    $prompt_image_id =
        isago_media_get_field(
            'post',
            $post_id,
            'ai_prompt_image'
        );


    if (
        $prompt_image_id
    ) {

        $prompt_image_url =
            isago_media_get_field_image_url(
                'post',
                $post_id,
                'ai_prompt_image',
                'medium'
            );


        if (
            $prompt_image_url
        ) {

            return sprintf(

                '<img
                    class="isago-admin-ai-prompt-thumbnail"
                    src="%s"
                    alt="%s"
                    loading="lazy"
                    width="80"
                    height="45"
                    style="%s"
                >',

                esc_url(
                    $prompt_image_url
                ),

                $alt,

                esc_attr(
                    $thumbnail_style
                )

            );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | 3. UPLOADED IMAGEKIT PROMPT VIDEO
    |--------------------------------------------------------------------------
    */

    $prompt_video_id =
        isago_media_get_field(
            'post',
            $post_id,
            'ai_prompt_video'
        );


    if (
        $prompt_video_id
    ) {

        $prompt_video_url =
            isago_media_get_field_url(
                'post',
                $post_id,
                'ai_prompt_video'
            );


        if (
            $prompt_video_url
        ) {

            $video_poster =
                '';


            if (
                $prompt_image_id
            ) {

                $video_poster =
                    isago_media_get_field_image_url(
                        'post',
                        $post_id,
                        'ai_prompt_image',
                        'medium'
                    );

            }


            /*
             * Video with poster.
             */

            if (
                $video_poster
            ) {

                return sprintf(

                    '<video
                        class="isago-admin-ai-prompt-thumbnail"
                        src="%s"
                        poster="%s"
                        muted
                        playsinline
                        preload="metadata"
                        width="80"
                        height="45"
                        title="%s"
                        style="%s"
                    ></video>',

                    esc_url(
                        $prompt_video_url
                    ),

                    esc_url(
                        $video_poster
                    ),

                    $alt,

                    esc_attr(
                        $thumbnail_style
                    )

                );

            }


            /*
             * Video without poster.
             */

            return sprintf(

                '<video
                    class="isago-admin-ai-prompt-thumbnail"
                    src="%s"
                    muted
                    playsinline
                    preload="metadata"
                    width="80"
                    height="45"
                    title="%s"
                    style="%s"
                ></video>',

                esc_url(
                    $prompt_video_url
                ),

                $alt,

                esc_attr(
                    $thumbnail_style
                )

            );

        }

    }





/*
|--------------------------------------------------------------------------
| 2A. SOCIAL PROMPT IMAGE URL
|--------------------------------------------------------------------------
*/

// This stores the resolved, direct image URL.
$social_prompt_image_url = get_post_meta(
    $post_id,
    'isago_ai_prompt_social_image_url',
    true
);

// Display the social image when no uploaded prompt image is available.
if (
    empty($prompt_image_id)
    && is_string($social_prompt_image_url)
    && wp_http_validate_url($social_prompt_image_url)
) {
    return sprintf(
        '<img
            class="isago-admin-ai-prompt-thumbnail"
            src="%s"
            alt="%s"
            loading="lazy"
            width="80"
            height="45"
            style="%s"
            onerror="this.onerror=null;this.src=\'%s\';"
        >',
        esc_url($social_prompt_image_url),
        $alt,
        esc_attr($thumbnail_style),
        esc_url($placeholder_url)
    );
}






    /*
    |--------------------------------------------------------------------------
    | 4. WORDPRESS FEATURED IMAGE
    |--------------------------------------------------------------------------
    */

    if (
        has_post_thumbnail(
            $post_id
        )
    ) {

        return get_the_post_thumbnail(
            $post_id,
            [80, 45],
            [
                'loading' =>
                    'lazy',

                'alt' =>
                    $alt,

                'width' =>
                    80,

                'height' =>
                    45,

                'style' =>
                    $thumbnail_style,
            ]
        );

    }


    /*
    |--------------------------------------------------------------------------
    | 5. PLACEHOLDER
    |--------------------------------------------------------------------------
    */

    return sprintf(

        '<img
            class="isago-admin-ai-prompt-thumbnail"
            src="%s"
            alt="%s"
            loading="lazy"
            width="80"
            height="45"
            style="%s"
        >',

        esc_url(
            $placeholder_url
        ),

        $alt,

        esc_attr(
            $thumbnail_style
        )

    );

}


/*=========================================================
AI PROMPT ADMIN COLUMNS
=========================================================*/

add_filter(
    'manage_ai_prompt_posts_columns',
    'isago_ai_prompt_admin_columns'
);


function isago_ai_prompt_admin_columns(
    $columns
) {

    $new_columns =
        array();


    /*
    |--------------------------------------------------------------------------
    | CHECKBOX
    |--------------------------------------------------------------------------
    */

    if (
        isset(
            $columns['cb']
        )
    ) {

        $new_columns['cb'] =
            $columns['cb'];

    }


    /*
    |--------------------------------------------------------------------------
    | MEDIA
    |--------------------------------------------------------------------------
    */

    $new_columns['thumbnail'] =
        __('Media', 'isago');


    /*
    |--------------------------------------------------------------------------
    | TITLE
    |--------------------------------------------------------------------------
    */

    if (
        isset(
            $columns['title']
        )
    ) {

        $new_columns['title'] =
            $columns['title'];

    }


    /*
    |--------------------------------------------------------------------------
    | CATEGORY
    |--------------------------------------------------------------------------
    */

    $new_columns['category'] =
        __('Category', 'isago');


    /*
    |--------------------------------------------------------------------------
    | AI PLATFORM
    |--------------------------------------------------------------------------
    */

    $new_columns['platform'] =
        __('AI Platform', 'isago');


    /*
    |--------------------------------------------------------------------------
    | DATE
    |--------------------------------------------------------------------------
    */

    if (
        isset(
            $columns['date']
        )
    ) {

        $new_columns['date'] =
            $columns['date'];

    }


    return $new_columns;

}


/*=========================================================
AI PROMPT ADMIN COLUMN CONTENT
=========================================================*/

add_action(
    'manage_ai_prompt_posts_custom_column',
    'isago_ai_prompt_admin_column_content',
    10,
    2
);


function isago_ai_prompt_admin_column_content(
    $column,
    $post_id
) {

    switch (
        $column
    ) {

        /*
        |--------------------------------------------------------------------------
        | MEDIA
        |--------------------------------------------------------------------------
        */

        case 'thumbnail':

            echo isago_get_admin_ai_prompt_thumbnail(
                $post_id
            );

            break;


        /*
        |--------------------------------------------------------------------------
        | CATEGORY
        |--------------------------------------------------------------------------
        */

        case 'category':

            $categories =
                get_the_term_list(
                    $post_id,
                    'ai_prompt_category',
                    '',
                    ', '
                );


            echo $categories
                ? $categories
                : '&mdash;';

            break;


        /*
        |--------------------------------------------------------------------------
        | PLATFORM
        |--------------------------------------------------------------------------
        */

        case 'platform':

            $platforms =
                get_the_term_list(
                    $post_id,
                    'ai_platform',
                    '',
                    ', '
                );


            echo $platforms
                ? $platforms
                : '&mdash;';

            break;

    }

}





/*=========================================================
ISAGO AI PROMPT ADMIN FILTERS AND SORTING
=========================================================*/

/**
 * Add filter and sort controls to:
 * AI Prompts -> All AI Prompts.
 *
 * Existing admin columns remain unchanged.
 */
add_action(
    'restrict_manage_posts',
    'isago_ai_prompt_admin_filter_controls',
    10,
    2
);

function isago_ai_prompt_admin_filter_controls(
    $post_type,
    $which
) {

    // Only show controls on the AI Prompt list screen.
    if (
        $post_type !== 'ai_prompt'
        ||
        $which !== 'top'
    ) {
        return;
    }

    // Selected prompt filter.
    $current_filter = isset(
        $_GET['isago_ai_prompt_filter']
    )
        ? sanitize_key(
            wp_unslash(
                $_GET['isago_ai_prompt_filter']
            )
        )
        : '';

    if (
        !in_array(
            $current_filter,
            ['', 'featured'],
            true
        )
    ) {
        $current_filter = '';
    }

    // Selected sorting option.
    $current_sort = isset(
        $_GET['isago_ai_prompt_sort']
    )
        ? sanitize_key(
            wp_unslash(
                $_GET['isago_ai_prompt_sort']
            )
        )
        : '';

    if (
        !in_array(
            $current_sort,
            ['', 'oldest', 'updated'],
            true
        )
    ) {
        $current_sort = '';
    }

    ?>

    <!-- PROMPT FILTER -->

    <label
        class="screen-reader-text"
        for="isago-ai-prompt-filter"
    >
        Filter AI Prompts
    </label>

    <select
        name="isago_ai_prompt_filter"
        id="isago-ai-prompt-filter"
    >

        <option
            value=""
            <?php selected($current_filter, ''); ?>
        >
            All Prompts
        </option>

        <option
            value="featured"
            <?php selected($current_filter, 'featured'); ?>
        >
            Featured Prompts
        </option>

    </select>


   

   
    <!-- CATEGORY FILTER -->
    <?php
    $selected_category = isset($_GET['isago_ai_prompt_category_filter'])
        ? absint(wp_unslash($_GET['isago_ai_prompt_category_filter']))
        : 0;

    wp_dropdown_categories([
        'show_option_all' => __('All Categories', 'isago'),
        'taxonomy'        => 'ai_prompt_category',
        'name'            => 'isago_ai_prompt_category_filter',
        'id'              => 'isago-ai-prompt-category-filter',
        'selected'        => $selected_category,
        'orderby'         => 'name',
        'order'           => 'ASC',
        'hierarchical'    => true,
        'hide_empty'      => false,
        'value_field'     => 'term_id',
    ]);
    ?>


    <!-- PROMPT SORTING -->


    <label
        class="screen-reader-text"
        for="isago-ai-prompt-sort"
    >
        Sort AI Prompts
    </label>

    <select
        name="isago_ai_prompt_sort"
        id="isago-ai-prompt-sort"
    >

        <option
            value=""
            <?php selected($current_sort, ''); ?>
        >
            Latest Added
        </option>

        <option
            value="oldest"
            <?php selected($current_sort, 'oldest'); ?>
        >
            Oldest Added
        </option>

        <option
            value="updated"
            <?php selected($current_sort, 'updated'); ?>
        >
            Recently Updated
        </option>

    </select>

    <?php
}


/*=========================================================
APPLY AI PROMPT FILTERS AND SORTING
=========================================================*/

add_action(
    'pre_get_posts',
    'isago_ai_prompt_admin_apply_filters'
);

function isago_ai_prompt_admin_apply_filters(
    $query
) {

    // Do not change frontend queries or secondary queries.
    if (
        !is_admin()
        ||
        !$query->is_main_query()
    ) {
        return;
    }

    global $pagenow;

    // Only affect the AI Prompt admin list.
    if (
        $pagenow !== 'edit.php'
        ||
        $query->get('post_type') !== 'ai_prompt'
    ) {
        return;
    }


    /*=====================================================
    FEATURED PROMPT FILTER
    =====================================================*/

    $selected_filter = isset(
        $_GET['isago_ai_prompt_filter']
    )
        ? sanitize_key(
            wp_unslash(
                $_GET['isago_ai_prompt_filter']
            )
        )
        : '';

    if (
        $selected_filter === 'featured'
    ) {

        $meta_query = $query->get('meta_query');

        if (
            !is_array($meta_query)
        ) {
            $meta_query = [];
        }

        /*
         * This uses the existing featured prompt
         * metadata saved by the Isago prompt editor.
         */
        $meta_query[] = [
            'key'     => '_isago_ai_prompt_featured',
            'value'   => '1',
            'compare' => '=',
        ];

        $query->set(
            'meta_query',
            $meta_query
        );
    }




    /*=====================================================
    CATEGORY FILTER
    =====================================================*/

    $selected_category = isset($_GET['isago_ai_prompt_category_filter'])
        ? absint(wp_unslash($_GET['isago_ai_prompt_category_filter']))
        : 0;

    if ($selected_category > 0) {

        $category_term = get_term(
            $selected_category,
            'ai_prompt_category'
        );

        // Only filter using a valid AI Prompt Category term.
        if (
            $category_term instanceof WP_Term &&
            !is_wp_error($category_term)
        ) {

            $tax_query = $query->get('tax_query');

            if (!is_array($tax_query)) {
                $tax_query = [];
            }

            $tax_query[] = [
                'taxonomy'         => 'ai_prompt_category',
                'field'            => 'term_id',
                'terms'            => [$selected_category],
                'include_children' => true,
                'operator'         => 'IN',
            ];

            $query->set('tax_query', $tax_query);
        }
    }




    /*=====================================================
    SORT PROMPTS
    =====================================================*/

    $selected_sort = isset(
        $_GET['isago_ai_prompt_sort']
    )
        ? sanitize_key(
            wp_unslash(
                $_GET['isago_ai_prompt_sort']
            )
        )
        : '';

    switch ($selected_sort) {

        /*
         * Oldest prompts first.
         */
        case 'oldest':

            $query->set(
                'orderby',
                [
                    'date' => 'ASC',
                    'ID'   => 'ASC',
                ]
            );

            break;


        /*
         * Recently modified prompts first.
         */
        case 'updated':

            $query->set(
                'orderby',
                [
                    'modified' => 'DESC',
                    'ID'       => 'DESC',
                ]
            );

            break;


        /*
         * Latest Added is the default WordPress
         * ordering for the AI Prompt admin list.
         *
         * Leave the normal query ordering untouched
         * when this option is selected.
         */
        default:

            break;
    }
}







/*=========================================================
ISAGO AI PROMPT CATEGORY ADMIN SORTING
=========================================================*/

/**
 * Track when an AI Prompt Category is created or edited.
 *
 * For existing categories that have never been edited since
 * this feature was added, sorting falls back to term ID.
 */
function isago_track_ai_prompt_category_update( $term_id ) {

    $term_id = absint( $term_id );

    if ( ! $term_id ) {
        return;
    }

    update_term_meta(
        $term_id,
        '_isago_ai_prompt_category_updated_at',
        time()
    );
}

add_action(
    'created_ai_prompt_category',
    'isago_track_ai_prompt_category_update',
    10,
    1
);

add_action(
    'edited_ai_prompt_category',
    'isago_track_ai_prompt_category_update',
    10,
    1
);


/*=========================================================
CATEGORY SORTING CONTROLS
=========================================================*/

/**
 * Add sorting controls to the AI Prompt Categories screen.
 */
add_action(
    'admin_footer-edit-tags.php',
    'isago_ai_prompt_category_admin_controls'
);

function isago_ai_prompt_category_admin_controls() {

    $screen = get_current_screen();

    // Only show controls on AI Prompt Categories.
    if (
        ! $screen ||
        $screen->base !== 'edit-tags' ||
        $screen->taxonomy !== 'ai_prompt_category'
    ) {
        return;
    }

    $current_sort = isset( $_GET['isago_category_sort'] )
        ? sanitize_key( wp_unslash( $_GET['isago_category_sort'] ) )
        : 'name_asc';

    $allowed_sorts = array(
        'name_asc',
        'name_desc',
        'newest',
        'oldest',
        'updated',
    );

    if ( ! in_array( $current_sort, $allowed_sorts, true ) ) {
        $current_sort = 'name_asc';
    }
    ?>

    <script>
    jQuery(function ($) {

        var form = $('#posts-filter');
        var topNav = form.find('.tablenav.top').first();

        // Stop if the expected WordPress list table is missing.
        if (!form.length || !topNav.length) {
            return;
        }

        // Prevent controls from being added more than once.
        if ($('#isago-category-sort-controls').length) {
            return;
        }

        var controls = $(
            '<div id="isago-category-sort-controls" ' +
            'class="alignleft actions"></div>'
        );

        var select = $(
            '<select id="isago-category-sort"></select>'
        );

        var options = {
            name_asc:  'Name: A to Z',
            name_desc: 'Name: Z to A',
            newest:    'Newest Categories',
            oldest:    'Oldest Categories',
            updated:   'Recently Updated'
        };

        var currentSort = <?php echo wp_json_encode( $current_sort ); ?>;

        $.each(options, function (value, label) {

            var option = $('<option></option>')
                .val(value)
                .text(label);

            if (value === currentSort) {
                option.prop('selected', true);
            }

            select.append(option);
        });

        var applyButton = $(
            '<button type="button" class="button action">' +
            'Apply Sort</button>'
        );

        applyButton.on('click', function () {

            // Preserve the current screen and other URL parameters.
            var url = new URL(window.location.href);

            url.searchParams.set(
                'isago_category_sort',
                select.val()
            );

            // Return to the first page after changing the sort.
            url.searchParams.delete('paged');
            url.searchParams.delete('page');

            window.location.href = url.toString();
        });

        controls.append(select);
        controls.append(' ');
        controls.append(applyButton);

        // Append directly to the top navigation bar.
        topNav.append(controls);

    });
    </script>

    <?php
}


/*=========================================================
APPLY AI PROMPT CATEGORY SORTING — FIXED
=========================================================*/

add_filter(
    'terms_clauses',
    'isago_ai_prompt_category_apply_sorting',
    10,
    3
);

function isago_ai_prompt_category_apply_sorting(
    $clauses,
    $taxonomies,
    $args
) {

    if ( ! is_admin() ) {
        return $clauses;
    }

    // Only affect the AI Prompt Category taxonomy.
    if (
        ! is_array( $taxonomies ) ||
        ! in_array( 'ai_prompt_category', $taxonomies, true )
    ) {
        return $clauses;
    }

    // Only affect its admin category list.
    $screen = get_current_screen();

    if (
        ! $screen ||
        $screen->base !== 'edit-tags' ||
        $screen->taxonomy !== 'ai_prompt_category'
    ) {
        return $clauses;
    }

    $selected_sort = isset( $_GET['isago_category_sort'] )
        ? sanitize_key( wp_unslash( $_GET['isago_category_sort'] ) )
        : 'name_asc';

    $allowed_sorts = array(
        'name_asc',
        'name_desc',
        'newest',
        'oldest',
        'updated',
    );

    if ( ! in_array( $selected_sort, $allowed_sorts, true ) ) {
        $selected_sort = 'name_asc';
    }

    global $wpdb;

    switch ( $selected_sort ) {

        // Alphabetical: A to Z.
        case 'name_asc':

            $clauses['orderby'] =
                'ORDER BY t.name ASC, t.term_id ASC';

            break;

        // Reverse alphabetical: Z to A.
        case 'name_desc':

            $clauses['orderby'] =
                'ORDER BY t.name DESC, t.term_id DESC';

            break;

        // Newest term IDs first.
        case 'newest':

            $clauses['orderby'] =
                'ORDER BY t.term_id DESC';

            break;

        // Oldest term IDs first.
        case 'oldest':

            $clauses['orderby'] =
                'ORDER BY t.term_id ASC';

            break;

        // Recently tracked or edited categories first.
        case 'updated':

            $meta_alias = 'isago_ai_prompt_category_sort_meta';

            $clauses['join'] .= $wpdb->prepare(
                " LEFT JOIN {$wpdb->termmeta} AS {$meta_alias}
                    ON (
                        t.term_id = {$meta_alias}.term_id
                        AND {$meta_alias}.meta_key = %s
                    ) ",
                '_isago_ai_prompt_category_updated_at'
            );

            $clauses['orderby'] =
                "ORDER BY
                    COALESCE(
                        CAST({$meta_alias}.meta_value AS UNSIGNED),
                        t.term_id
                    ) DESC,
                    t.term_id DESC";

            break;
    }

    /*
     * IMPORTANT:
     * The ORDER BY clause above already contains ASC or DESC.
     * Clear the separate direction to prevent WordPress from
     * appending a second direction and generating invalid SQL.
     */
    $clauses['order'] = '';

    return $clauses;
}