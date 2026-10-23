<?php

if (!defined('ABSPATH')) {
    exit;
}



 
/*=========================================================
ISAGO TAXONOMY META BOXES
=========================================================*/

add_action(
    'add_meta_boxes',
    'isago_register_taxonomy_meta_boxes',
    100
);  

function isago_register_taxonomy_meta_boxes()
{

    /*-----------------------------------------------------
    POST CATEGORIES
    -----------------------------------------------------*/

    add_meta_box(
        'isago-post-categories',
        'Categories',
        'isago_post_category_callback',
        'post',
        'side',
        'high'
    );

/*-----------------------------------------------------
POST AI PROMPT CATEGORIES
-----------------------------------------------------*/

add_meta_box(
    'isago-post-ai-prompt-categories',
    'AI Prompt Categories',
    'isago_post_ai_prompt_categories_callback',
    'post',
    'side',
    'high'
);

/*-----------------------------------------------------
POST AI TOOL CATEGORIES
-----------------------------------------------------*/

add_meta_box(
    'isago-post-ai-tool-categories',
    'AI Tool Categories',
    'isago_post_ai_tool_categories_callback',
    'post',
    'side',
    'high'
);


/*-----------------------------------------------------
POST FEATURED
-----------------------------------------------------*/

add_meta_box(
    'isago-post-featured',
    'Featured',
    'isago_post_featured_callback',
    'post',
    'side',
    'high'
);

/*-----------------------------------------------------
POST FEATURED IMAGE
-----------------------------------------------------*/

add_meta_box(
    'isago-post-featured-image',
    'Featured Image',
    'isago_post_featured_image_callback',
    'post',
    'side',
    'high'
);

    /*-----------------------------------------------------
    VIDEO CATEGORIES
    -----------------------------------------------------*/

    add_meta_box(
        'isago-video-categories',
        'Categories',
        'isago_post_category_callback',
        'isago_video',
        'side',
        'high'
    );

/*-----------------------------------------------------
VIDEO AI PROMPT CATEGORIES
-----------------------------------------------------*/

add_meta_box(
    'isago-video-ai-prompt-categories',
    'AI Prompt Categories',
    'isago_post_ai_prompt_categories_callback',
    'isago_video',
    'side',
    'high'
);


/*-----------------------------------------------------
VIDEO AI TOOL CATEGORIES
-----------------------------------------------------*/

add_meta_box(
    'isago-video-ai-tool-categories',
    'AI Tool Categories',
    'isago_post_ai_tool_categories_callback',
    'isago_video',
    'side',
    'high'
);


/*-----------------------------------------------------
VIDEO FEATURED
-----------------------------------------------------*/

add_meta_box(
    'isago-video-featured',
    'Featured',
    'isago_post_featured_callback',
    'isago_video',
    'side',
    'high'
);
    /*-----------------------------------------------------
    POST AI PLATFORM
    -----------------------------------------------------*/

    add_meta_box(
        'isago-ai-platform',
        'AI Platform',
        'isago_ai_platform_callback',
        'post',
        'side',
        'high'
    );


    /*-----------------------------------------------------
    VIDEO AI PLATFORM
    -----------------------------------------------------*/

    add_meta_box(
        'isago-ai-platform-video',
        'AI Platform',
        'isago_ai_platform_callback',
        'isago_video',
        'side',
        'high'
    );

}


/*=========================================================
CATEGORY META BOX
=========================================================*/

function isago_post_category_callback($post)
{

    wp_nonce_field(
        'isago_post_categories',
        'isago_post_categories_nonce'
    );


    $selected = wp_get_post_categories(
        $post->ID
    );


    $selected = array_map(
        'absint',
        $selected
    );


    /*-----------------------------------------------------
    RECURSIVE CATEGORY RENDERER
    -----------------------------------------------------*/

    $render_categories = function (
        $parent_id = 0,
        $level = 0
    ) use (
        &$render_categories,
        $selected
    ) {

        $categories = get_categories(
            [
                'taxonomy'   => 'category',
                'parent'     => absint($parent_id),
                'hide_empty' => false,
                'orderby'    => 'name',
                'order'      => 'ASC'
            ]
        );


        if (empty($categories)) {

            return;

        }


        foreach ($categories as $category) {

            $children = get_categories(
                [
                    'taxonomy'   => 'category',
                    'parent'     => absint($category->term_id),
                    'hide_empty' => false,
                    'orderby'    => 'name',
                    'order'      => 'ASC'
                ]
            );


            /*---------------------------------------------
            TOP LEVEL CATEGORY
            ---------------------------------------------*/

            if ($level === 0) {

                ?>

                <div
                    class="isago-accordion"
                    data-level="0"
                >

                    <button
                        type="button"
                        class="isago-accordion-toggle"
                        aria-expanded="false"
                    >

                        <span class="isago-accordion-title">

                            <?php
                            echo esc_html(
                                $category->name
                            );
                            ?>

                        </span>

                        <?php if (!empty($children)) : ?>

                            <span
                                class="dashicons dashicons-arrow-down-alt2"
                                aria-hidden="true"
                            ></span>

                        <?php endif; ?>

                    </button>


                    <div
                        class="isago-accordion-panel"
                    >

                        <div
                            class="isago-category-level"
                        >

                            <label>

                                <input
                                    type="checkbox"
                                    name="post_category[]"
                                    value="<?php echo absint(
                                        $category->term_id
                                    ); ?>"
                                    <?php checked(
                                        in_array(
                                            $category->term_id,
                                            $selected,
                                            true
                                        )
                                    ); ?>
                                >

                                <span>
                                    <?php
                                    echo esc_html(
                                        $category->name
                                    );
                                    ?>
                                </span>

                            </label>

                        </div>

                        <?php

            } else {

                ?>

                <div
                    class="isago-category-level"
                    style="margin-left: <?php echo absint(
                        $level * 15
                    ); ?>px;"
                >

                    <label>

                        <input
                            type="checkbox"
                            name="post_category[]"
                            value="<?php echo absint(
                                $category->term_id
                            ); ?>"
                            <?php checked(
                                in_array(
                                    $category->term_id,
                                    $selected,
                                    true
                                )
                            ); ?>
                        >

                        <span>

                            <?php
                            echo esc_html(
                                $category->name
                            );
                            ?>

                        </span>

                    </label>

                </div>

                <?php

            }


            /*---------------------------------------------
            CHILDREN
            ---------------------------------------------*/

            if (!empty($children)) {

                $render_categories(
                    $category->term_id,
                    $level + 1
                );

            }


            /*---------------------------------------------
            CLOSE TOP LEVEL
            ---------------------------------------------*/

            if ($level === 0) {

                ?>

                    </div>

                </div>

                <?php

            }

        }

    };


    $render_categories(0, 0);

}



/*=========================================================
ISAGO TAGS — CONNECT TO POSTS AND VIDEOS
=========================================================*/

/**
 * Ensure WordPress Tags are available for both content types.
 */
add_action('init', 'isago_connect_tags_to_content_types', 100);

function isago_connect_tags_to_content_types() {

    if (!taxonomy_exists('post_tag')) {
        return;
    }

    foreach (['post', 'isago_video'] as $post_type) {

        if (post_type_exists($post_type)) {
            register_taxonomy_for_object_type(
                'post_tag',
                $post_type
            );
        }
    }
}


/*=========================================================
TAGS META BOX
=========================================================*/

add_action(
    'add_meta_boxes',
    'isago_register_tags_meta_boxes',
    110
);

function isago_register_tags_meta_boxes() {

    foreach (['post', 'isago_video'] as $post_type) {

        add_meta_box(
            'isago-content-tags',
            'Tags',
            'isago_content_tags_callback',
            $post_type,
            'side',
            'high'
        );
    }
}


/*=========================================================
DISPLAY AVAILABLE TAGS
=========================================================*/

function isago_content_tags_callback($post) {

    wp_nonce_field(
        'isago_save_content_tags',
        'isago_content_tags_nonce'
    );

    if (!taxonomy_exists('post_tag')) {
        echo '<p>WordPress Tags taxonomy is unavailable.</p>';
        return;
    }

    // Get every existing tag, including unused tags.
    $tags = get_terms([
        'taxonomy'   => 'post_tag',
        'hide_empty' => false,
        'orderby'    => 'name',
        'order'      => 'ASC',
    ]);

    if (is_wp_error($tags)) {
        echo '<p>Unable to load available tags.</p>';
        return;
    }

    // Get tags already assigned to this post or video.
    $selected = wp_get_object_terms(
        $post->ID,
        'post_tag',
        ['fields' => 'ids']
    );

    if (is_wp_error($selected)) {
        $selected = [];
    }

    $selected = array_map('absint', $selected);
    ?>

    <div class="isago-content-tags-list"
         style="max-height:300px; overflow-y:auto; padding:8px 2px;">

        <?php if (empty($tags)) : ?>

            <p>No tags have been created yet.</p>

            <p>
                Create tags under
                <strong>Posts → Tags</strong>
                in the WordPress dashboard.
            </p>

        <?php else : ?>

            <?php foreach ($tags as $tag) : ?>

                <label style="display:block; margin-bottom:9px;">

                    <input
                        type="checkbox"
                        name="isago_content_tags[]"
                        value="<?php echo esc_attr($tag->term_id); ?>"
                        <?php checked(
                            in_array(
                                (int) $tag->term_id,
                                $selected,
                                true
                            )
                        ); ?>
                    >

                    <?php echo esc_html($tag->name); ?>

                </label>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>

    <p class="description">
        Select one or more existing tags for this content.
    </p>

    <?php
}


/*=========================================================
SAVE SELECTED TAGS
=========================================================*/

add_action(
    'save_post',
    'isago_save_content_tags',
    30
);

function isago_save_content_tags($post_id) {

    // Only process blog posts and Isago videos.
    if (
        !in_array(
            get_post_type($post_id),
            ['post', 'isago_video'],
            true
        )
    ) {
        return;
    }

    // Require the nonce from our Tags panel.
    if (
        !isset($_POST['isago_content_tags_nonce'])
        ||
        !wp_verify_nonce(
            sanitize_text_field(
                wp_unslash($_POST['isago_content_tags_nonce'])
            ),
            'isago_save_content_tags'
        )
    ) {
        return;
    }

    // Prevent autosave and revision updates.
    if (
        (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
        ||
        wp_is_post_revision($post_id)
        ||
        wp_is_post_autosave($post_id)
    ) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (!taxonomy_exists('post_tag')) {
        return;
    }

    // Read the checked tag IDs.
    $tag_ids = [];

    if (
        isset($_POST['isago_content_tags'])
        &&
        is_array($_POST['isago_content_tags'])
    ) {
        $tag_ids = array_map(
            'absint',
            wp_unslash($_POST['isago_content_tags'])
        );

        $tag_ids = array_values(
            array_unique(
                array_filter($tag_ids)
            )
        );
    }

    // Save selected tags. An empty selection clears this content's tags.
    $result = wp_set_object_terms(
        $post_id,
        $tag_ids,
        'post_tag',
        false
    );

    if (is_wp_error($result)) {
        error_log(
            'Isago Tags: Failed to save tags for content ID '
            . absint($post_id)
            . '. '
            . $result->get_error_message()
        );
    }
}




/*=========================================================
SAVE CATEGORIES
=========================================================*/

add_action(
    'save_post',
    'isago_save_post_categories',
    20
);

function isago_save_post_categories($post_id)
{

    if (
        !isset(
            $_POST['isago_post_categories_nonce']
        )
    ) {

        return;

    }


    $nonce = sanitize_text_field(
        wp_unslash(
            $_POST['isago_post_categories_nonce']
        )
    );


    if (
        !wp_verify_nonce(
            $nonce,
            'isago_post_categories'
        )
    ) {

        return;

    }


    if (
        defined('DOING_AUTOSAVE')
        &&
        DOING_AUTOSAVE
    ) {

        return;

    }


    if (
        wp_is_post_revision(
            $post_id
        )
    ) {

        return;

    }


    $post_type = get_post_type(
        $post_id
    );


    if (
        !in_array(
            $post_type,
            [
                'post',
                'isago_video'
            ],
            true
        )
    ) {

        return;

    }


    if (
        !current_user_can(
            'edit_post',
            $post_id
        )
    ) {

        return;

    }


    $categories = [];


    if (
        isset(
            $_POST['post_category']
        )
        &&
        is_array(
            $_POST['post_category']
        )
    ) {

        $categories = array_map(
            'absint',
            wp_unslash(
                $_POST['post_category']
            )
        );


        $categories = array_values(
            array_unique(
                array_filter(
                    $categories
                )
            )
        );

    }


    wp_set_post_categories(
        $post_id,
        $categories
    );

}


/*=========================================================
AI PLATFORM META BOX
=========================================================*/

function isago_ai_platform_callback($post)
{

    wp_nonce_field(
        'isago_ai_platform',
        'isago_ai_platform_nonce'
    );


    $selected = wp_get_object_terms(
        $post->ID,
        'ai_platform',
        [
            'fields' => 'ids'
        ]
    );


    if (
        is_wp_error(
            $selected
        )
    ) {

        $selected = [];

    }


    $selected = array_map(
        'absint',
        $selected
    );


    /*-----------------------------------------------------
    RECURSIVE PLATFORM RENDERER
    -----------------------------------------------------*/

    $render_platforms = function (
        $parent_id = 0,
        $level = 0
    ) use (
        &$render_platforms,
        $selected
    ) {

        $platforms = get_terms(
            [
                'taxonomy'   => 'ai_platform',
                'parent'     => absint($parent_id),
                'hide_empty' => false,
                'orderby'    => 'name',
                'order'      => 'ASC'
            ]
        );


        if (
            is_wp_error($platforms)
            ||
            empty($platforms)
        ) {

            return;

        }


        foreach ($platforms as $platform) {

            $children = get_terms(
                [
                    'taxonomy'   => 'ai_platform',
                    'parent'     => absint($platform->term_id),
                    'hide_empty' => false,
                    'orderby'    => 'name',
                    'order'      => 'ASC'
                ]
            );


            if ($level === 0) {

                ?>

                <div
                    class="isago-accordion"
                    data-level="0"
                >

                    <button
                        type="button"
                        class="isago-accordion-toggle"
                        aria-expanded="false"
                    >

                        <span class="isago-accordion-title">

                            <?php
                            echo esc_html(
                                $platform->name
                            );
                            ?>

                        </span>

                        <?php if (!empty($children)) : ?>

                            <span
                                class="dashicons dashicons-arrow-down-alt2"
                                aria-hidden="true"
                            ></span>

                        <?php endif; ?>

                    </button>


                    <div
                        class="isago-accordion-panel"
                    >

                        <div
                            class="isago-category-level"
                        >

                            <label>

                                <input
                                    type="checkbox"
                                    name="isago_ai_platform[]"
                                    value="<?php echo absint(
                                        $platform->term_id
                                    ); ?>"
                                    <?php checked(
                                        in_array(
                                            $platform->term_id,
                                            $selected,
                                            true
                                        )
                                    ); ?>
                                >

                                <span>

                                    <?php
                                    echo esc_html(
                                        $platform->name
                                    );
                                    ?>

                                </span>

                            </label>

                        </div>

                        <?php

            } else {

                ?>

                <div
                    class="isago-category-level"
                    style="margin-left: <?php echo absint(
                        $level * 15
                    ); ?>px;"
                >

                    <label>

                        <input
                            type="checkbox"
                            name="isago_ai_platform[]"
                            value="<?php echo absint(
                                $platform->term_id
                            ); ?>"
                            <?php checked(
                                in_array(
                                    $platform->term_id,
                                    $selected,
                                    true
                                )
                            ); ?>
                        >

                        <span>

                            <?php
                            echo esc_html(
                                $platform->name
                            );
                            ?>

                        </span>

                    </label>

                </div>

                <?php

            }


            if (!empty($children)) {

                $render_platforms(
                    $platform->term_id,
                    $level + 1
                );

            }


            if ($level === 0) {

                ?>

                    </div>

                </div>

                <?php

            }

        }

    };


    $render_platforms(0, 0);

}




/*=========================================================
AI PROMPT CATEGORIES META BOX
=========================================================*/

function isago_post_ai_prompt_categories_callback(
    $post
) {

    wp_nonce_field(
        'isago_post_ai_prompt_categories',
        'isago_post_ai_prompt_categories_nonce'
    );


    if (
        !taxonomy_exists(
            'ai_prompt_category'
        )
    ) {

        echo '<p>AI Prompt Categories taxonomy is not available.</p>';

        return;
    }


    $categories =
        get_terms([
            'taxonomy'   => 'ai_prompt_category',
            'hide_empty' => false,
            'orderby'    => 'name',
            'order'      => 'ASC'
        ]);


    if (
        is_wp_error($categories)
        ||
        empty($categories)
    ) {

        echo '<p>No AI Prompt Categories have been created yet.</p>';

        return;
    }


    $selected =
        wp_get_object_terms(
            $post->ID,
            'ai_prompt_category',
            [
                'fields' => 'ids'
            ]
        );


    if (
        is_wp_error($selected)
    ) {

        $selected = [];
    }


    $selected =
        array_map(
            'absint',
            $selected
        );


    ?>

    <div
        class="isago-post-ai-prompt-categories"
        style="
            max-height:300px;
            overflow-y:auto;
            padding:8px;
        "
    >

        <?php foreach (
            $categories as $category
        ) : ?>

            <label
                style="
                    display:block;
                    margin:0 0 8px;
                "
            >

                <input
                    type="checkbox"
                    name="isago_post_ai_prompt_categories[]"
                    value="<?php echo absint(
                        $category->term_id
                    ); ?>"
                    <?php checked(
                        in_array(
                            $category->term_id,
                            $selected,
                            true
                        )
                    ); ?>
                >

                <?php
                echo esc_html(
                    $category->name
                );
                ?>

            </label>

        <?php endforeach; ?>

    </div>

    <p class="description">

        Select the AI Prompt Categories related to this content.

    </p>

    <?php
}




/*=========================================================
AI TOOL CATEGORIES META BOX
=========================================================*/

function isago_post_ai_tool_categories_callback(
    $post
) {

    wp_nonce_field(
        'isago_post_ai_tool_categories',
        'isago_post_ai_tool_categories_nonce'
    );


    if (
        !taxonomy_exists(
            'ai_tool_category'
        )
    ) {

        echo '<p>AI Tool Categories taxonomy is not available.</p>';

        return;
    }


    $categories =
        get_terms([
            'taxonomy'   => 'ai_tool_category',
            'hide_empty' => false,
            'orderby'    => 'name',
            'order'      => 'ASC'
        ]);


    if (
        is_wp_error($categories)
        ||
        empty($categories)
    ) {

        echo '<p>No AI Tool Categories have been created yet.</p>';

        return;
    }


    $selected =
        wp_get_object_terms(
            $post->ID,
            'ai_tool_category',
            [
                'fields' => 'ids'
            ]
        );


    if (
        is_wp_error($selected)
    ) {

        $selected = [];
    }


    $selected =
        array_map(
            'absint',
            $selected
        );


    ?>

    <div
        class="isago-post-ai-tool-categories"
        style="
            max-height:300px;
            overflow-y:auto;
            padding:8px;
        "
    >

        <?php foreach (
            $categories as $category
        ) : ?>

            <label
                style="
                    display:block;
                    margin:0 0 8px;
                "
            >

                <input
                    type="checkbox"
                    name="isago_post_ai_tool_categories[]"
                    value="<?php echo absint(
                        $category->term_id
                    ); ?>"
                    <?php checked(
                        in_array(
                            $category->term_id,
                            $selected,
                            true
                        )
                    ); ?>
                >

                <?php
                echo esc_html(
                    $category->name
                );
                ?>

            </label>

        <?php endforeach; ?>

    </div>

    <p class="description">

        Select the AI Tool Categories related to this content.

    </p>

    <?php
}


/*=========================================================
FEATURED META BOX
=========================================================*/

function isago_post_featured_callback(
    $post
) {

    wp_nonce_field(
        'isago_post_featured',
        'isago_post_featured_nonce'
    );


    $featured =
        get_post_meta(
            $post->ID,
            '_isago_post_featured',
            true
        );

    ?>

    <label>

        <input
            type="checkbox"
            name="isago_post_featured"
            value="1"
            <?php checked(
                $featured,
                '1'
            ); ?>
        >

        <strong>

            Featured

        </strong>

    </label>


    <p class="description">

        Feature this content in Isago's featured areas.

    </p>

    <?php
}


/*=========================================================
FEATURED IMAGE META BOX
=========================================================*/

/**
 * Blog Post Featured Image.
 *
 * Uses the central Isago Media system.
 *
 * Field:
 *     post / featured_image
 *
 * New uploads go directly to ImageKit.
 * Existing WordPress Featured Images are displayed
 * as a migration fallback.
 */
function isago_post_featured_image_callback(
    $post
) {

    wp_nonce_field(
        'isago_post_featured_image',
        'isago_post_featured_image_nonce'
    );


    /*-----------------------------------------------------
    CENTRAL ISAGO MEDIA
    -----------------------------------------------------*/

    $media_id =
        isago_media_get_field(
            'post',
            $post->ID,
            'featured_image'
        );


    $image_url = '';


    if ($media_id) {

        $image_url =
            isago_media_get_field_image_url(
                'post',
                $post->ID,
                'featured_image',
                'medium_large'
            );

    }


    /*-----------------------------------------------------
    LEGACY WORDPRESS FEATURED IMAGE
    -----------------------------------------------------*/

    /*
     * Existing posts may still use WordPress's native
     * Featured Image system.
     *
     * Do not migrate or delete it yet.
     *
     * We only use it as a display fallback while the
     * ImageKit migration is being completed.
     */

    if (
        !$image_url
        &&
        has_post_thumbnail($post->ID)
    ) {

        $image_url =
            get_the_post_thumbnail_url(
                $post->ID,
                'medium_large'
            );

    }


    /*-----------------------------------------------------
    IMAGEKIT ACCOUNT
    -----------------------------------------------------*/

    $account_id =
        isago_get_imagekit_media_account_id(
            $media_id
        );

    ?>

    <div
        class="isago-media-field isago-featured-image-field"
    >

        <!--
        Central Isago Media ID.

        This is the value that gets submitted to the
        post save handler.
        -->
        <input
            type="hidden"
            id="isago-featured-image"
            name="isago_featured_image"
            value="<?php echo esc_attr($media_id); ?>"
        >


       
<?php
// ImageKit account selector.
isago_render_imagekit_account_selector([
    'id'       => 'isago-featured-image-account',
    'selected' => $account_id,
    'label'    => 'ImageKit Account',
    'class'    => 'isago-imagekit-account-selector',
    'style'    => 'width:100%; margin-bottom:10px;',
]);

// Destination folder selector.
isago_render_imagekit_folder_selector([
    'id'       => 'isago-featured-image-folder',
    'selected' => '/Isago-Media/Blog-Posts-Articles/',
    'label'    => 'Destination folder',
    'style'    => 'width:100%; margin:10px 0 15px;',
]);
?>

<!-- Featured image preview -->
<div
    id="isago-featured-image-preview"
    class="isago-media-preview"
    <?php if (!$image_url) : ?>
        style="display:none;"
    <?php endif; ?>
>
    <?php if ($image_url) : ?>
        <img
            src="<?php echo esc_url($image_url); ?>"
            alt="<?php echo esc_attr(get_the_title($post->ID)); ?>"
            style="
                max-width:100%;
                height:auto;
                display:block;
                margin-bottom:10px;
            "
        >
    <?php endif; ?>
</div>

<!-- Upload a new featured image -->
<p>
    <button
        type="button"
        class="button button-primary isago-imagekit-upload-button"
        data-target="#isago-featured-image"
        data-preview="#isago-featured-image-preview"
        data-type="image"
        data-account-selector="#isago-featured-image-account"
        data-folder-selector="#isago-featured-image-folder"
        style="width:100%;"
    >
        Upload to ImageKit
    </button>
</p>

<!-- Select an existing ImageKit image -->
<p>
    <button
        type="button"
        class="button isago-imagekit-select-existing-button"
        data-target="#isago-featured-image"
        data-preview="#isago-featured-image-preview"
        data-type="image"
        data-account-selector="#isago-featured-image-account"
        data-folder-selector="#isago-featured-image-folder"
        style="width:100%;"
    >
        Select Existing Image
    </button>
</p>  

<!-- Remove the assigned featured image -->
<p>
    <button
        type="button"
        class="button isago-imagekit-remove-button"
        data-target="#isago-featured-image"
        data-preview="#isago-featured-image-preview"
        data-type="image"
        data-object-type="post"
        data-object-id="<?php echo esc_attr($post->ID); ?>"
        data-field-key="featured_image"
        style="width:100%;"
        <?php disabled(empty($media_id)); ?>
    >
        Remove Image
    </button>
</p>

<p class="description">
    Upload a new featured image to ImageKit or select an existing
    image from your Isago media library. New uploads will use the
    destination folder selected above.
</p>
</div>


    </div>

    <?php
}


/*=========================================================
SAVE AI PROMPT CATEGORIES
=========================================================*/

add_action(
    'save_post',
    'isago_save_post_ai_prompt_categories',
    24
);

function isago_save_post_ai_prompt_categories(
    $post_id
) {

    if (
        !isset(
            $_POST['isago_post_ai_prompt_categories_nonce']
        )
    ) {

        return;
    }


    if (
        !wp_verify_nonce(
            sanitize_text_field(
                wp_unslash(
                    $_POST[
                        'isago_post_ai_prompt_categories_nonce'
                    ]
                )
            ),
            'isago_post_ai_prompt_categories'
        )
    ) {

        return;
    }


    if (
        defined('DOING_AUTOSAVE')
        &&
        DOING_AUTOSAVE
    ) {

        return;
    }


    if (
        wp_is_post_revision($post_id)
    ) {

        return;
    }


    if (
        !current_user_can(
            'edit_post',
            $post_id
        )
    ) {

        return;
    }


    if (
        !in_array(
            get_post_type($post_id),
            [
                'post',
                'isago_video'
            ],
            true
        )
    ) {

        return;
    }


    $categories = [];


    if (
        isset(
            $_POST['isago_post_ai_prompt_categories']
        )
        &&
        is_array(
            $_POST['isago_post_ai_prompt_categories']
        )
    ) {

        $categories =
            array_map(
                'absint',
                wp_unslash(
                    $_POST[
                        'isago_post_ai_prompt_categories'
                    ]
                )
            );


        $categories =
            array_values(
                array_unique(
                    array_filter(
                        $categories
                    )
                )
            );
    }


    wp_set_object_terms(
        $post_id,
        $categories,
        'ai_prompt_category',
        false
    );
}


/*=========================================================
SAVE AI TOOL CATEGORIES
=========================================================*/

add_action(
    'save_post',
    'isago_save_post_ai_tool_categories',
    25
);

function isago_save_post_ai_tool_categories(
    $post_id
) {

    if (
        !isset(
            $_POST['isago_post_ai_tool_categories_nonce']
        )
    ) {

        return;
    }


    if (
        !wp_verify_nonce(
            sanitize_text_field(
                wp_unslash(
                    $_POST[
                        'isago_post_ai_tool_categories_nonce'
                    ]
                )
            ),
            'isago_post_ai_tool_categories'
        )
    ) {

        return;
    }


    if (
        defined('DOING_AUTOSAVE')
        &&
        DOING_AUTOSAVE
    ) {

        return;
    }


    if (
        wp_is_post_revision($post_id)
    ) {

        return;
    }


    if (
        !current_user_can(
            'edit_post',
            $post_id
        )
    ) {

        return;
    }


    if (
        !in_array(
            get_post_type($post_id),
            [
                'post',
                'isago_video'
            ],
            true
        )
    ) {

        return;
    }


    $categories = [];


    if (
        isset(
            $_POST['isago_post_ai_tool_categories']
        )
        &&
        is_array(
            $_POST['isago_post_ai_tool_categories']
        )
    ) {

        $categories =
            array_map(
                'absint',
                wp_unslash(
                    $_POST[
                        'isago_post_ai_tool_categories'
                    ]
                )
            );


        $categories =
            array_values(
                array_unique(
                    array_filter(
                        $categories
                    )
                )
            );
    }


    wp_set_object_terms(
        $post_id,
        $categories,
        'ai_tool_category',
        false
    );
}


/*=========================================================
SAVE POST FEATURED
=========================================================*/

add_action(
    'save_post',
    'isago_save_post_featured',
    26
);

function isago_save_post_featured(
    $post_id
) {

    if (
        !isset(
            $_POST['isago_post_featured_nonce']
        )
    ) {

        return;
    }


    if (
        !wp_verify_nonce(
            sanitize_text_field(
                wp_unslash(
                    $_POST[
                        'isago_post_featured_nonce'
                    ]
                )
            ),
            'isago_post_featured'
        )
    ) {

        return;
    }


    if (
        defined('DOING_AUTOSAVE')
        &&
        DOING_AUTOSAVE
    ) {

        return;
    }


    if (
        wp_is_post_revision($post_id)
    ) {

        return;
    }


    if (
        !current_user_can(
            'edit_post',
            $post_id
        )
    ) {

        return;
    }


    if (
        !in_array(
            get_post_type($post_id),
            [
                'post',
                'isago_video'
            ],
            true
        )
    ) {

        return;
    }


    if (
        isset(
            $_POST['isago_post_featured']
        )
        &&
        $_POST['isago_post_featured'] === '1'
    ) {

        update_post_meta(
            $post_id,
            '_isago_post_featured',
            '1'
        );

    } else {

        delete_post_meta(
            $post_id,
            '_isago_post_featured'
        );
    }
}



/*=========================================================
SAVE POST FEATURED IMAGE
=========================================================*/

/**
 * Save the Blog Post Featured Image
 * using the central Isago Media system.
 *
 * Field:
 *
 *     post / featured_image
 *
 * The central media system is responsible for:
 *
 * - creating the reference
 * - removing the previous reference
 * - deleting the previous ImageKit file when unused
 * - deleting the previous media database record
 */
add_action(
    'save_post',
    'isago_save_post_featured_image',
    27
);

function isago_save_post_featured_image(
    $post_id
) {

    /*-----------------------------------------------------
    NONCE
    -----------------------------------------------------*/

    if (
        !isset(
            $_POST['isago_post_featured_image_nonce']
        )
    ) {

        return;

    }


    $nonce =
        sanitize_text_field(
            wp_unslash(
                $_POST[
                    'isago_post_featured_image_nonce'
                ]
            )
        );


    if (
        !wp_verify_nonce(
            $nonce,
            'isago_post_featured_image'
        )
    ) {

        return;

    }


    /*-----------------------------------------------------
    AUTOSAVE
    -----------------------------------------------------*/

    if (
        defined('DOING_AUTOSAVE')
        &&
        DOING_AUTOSAVE
    ) {

        return;

    }


    /*-----------------------------------------------------
    REVISION
    -----------------------------------------------------*/

    if (
        wp_is_post_revision(
            $post_id
        )
    ) {

        return;

    }


    /*-----------------------------------------------------
    PERMISSION
    -----------------------------------------------------*/

    if (
        !current_user_can(
            'edit_post',
            $post_id
        )
    ) {

        return;

    }


    /*-----------------------------------------------------
    POST TYPE
    -----------------------------------------------------*/

    if (
        get_post_type(
            $post_id
        ) !== 'post'
    ) {

        return;

    }


    /*-----------------------------------------------------
    MEDIA ID
    -----------------------------------------------------*/

    $media_id = 0;


    if (
        isset(
            $_POST['isago_featured_image']
        )
    ) {

        $media_id =  
            absint(
                $_POST['isago_featured_image']
            );

    }


    /*-----------------------------------------------------
    SAVE CENTRAL MEDIA FIELD
    -----------------------------------------------------*/

    $result =
        isago_media_set_field(
            'post',
            $post_id,
            'featured_image',
            $media_id
        );


    if (
        is_wp_error($result)
    ) {

        error_log(
            'Isago Featured Image: Could not save media field for post '
            . $post_id
            . '. '
            . $result->get_error_message()
        );

    }

}


/*=========================================================
SAVE AI PLATFORM
=========================================================*/

add_action(
    'save_post',
    'isago_save_ai_platform',
    20
);

function isago_save_ai_platform($post_id)
{

    if (
        !isset(
            $_POST['isago_ai_platform_nonce']
        )
    ) {

        return;

    }


    $nonce = sanitize_text_field(
        wp_unslash(
            $_POST['isago_ai_platform_nonce']
        )
    );


    if (
        !wp_verify_nonce(
            $nonce,
            'isago_ai_platform'
        )
    ) {

        return;

    }


    if (
        defined('DOING_AUTOSAVE')
        &&
        DOING_AUTOSAVE
    ) {

        return;

    }


    if (
        wp_is_post_revision(
            $post_id
        )
    ) {

        return;

    }


    if (
        !current_user_can(
            'edit_post',
            $post_id
        )
    ) {

        return;

    }


    $post_type = get_post_type(
        $post_id
    );


    if (
        !in_array(
            $post_type,
            [
                'post',
                'isago_video'
            ],
            true
        )
    ) {

        return;

    }


    $platforms = [];


    if (
        isset(
            $_POST['isago_ai_platform']
        )
        &&
        is_array(
            $_POST['isago_ai_platform']
        )
    ) {

        $platforms = array_map(
            'absint',
            wp_unslash(
                $_POST['isago_ai_platform']
            )
        );


        $platforms = array_values(
            array_unique(
                array_filter(
                    $platforms
                )
            )
        );

    }


    wp_set_object_terms(
        $post_id,
        $platforms,
        'ai_platform',
        false
    );

}








