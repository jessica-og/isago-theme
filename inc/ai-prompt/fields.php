<?php

if (!defined('ABSPATH')) {
    exit;
}




/*=========================================================
AI PROMPT ADMIN FIELDS
=========================================================*/


/*=========================================================
META BOX
=========================================================*/

function isago_ai_prompt_add_meta_box()
{

    add_meta_box(
        'isago_ai_prompt_details',
        __('AI Prompt Details', 'isago'),
        'isago_ai_prompt_render_meta_box',
        'ai_prompt',
        'normal',
        'high'
    );
}

add_action(  
    'add_meta_boxes',
    'isago_ai_prompt_add_meta_box'
);


/*=========================================================
RENDER META BOX
=========================================================*/

function isago_ai_prompt_render_meta_box(
    $post
) {

    wp_nonce_field(
        'isago_save_ai_prompt',
        'isago_ai_prompt_nonce'
    );


    /*-----------------------------------------------------
    VALUES
    -----------------------------------------------------*/

    $short_description = get_post_meta(
        $post->ID,
        '_isago_ai_prompt_short_description',
        true
    );


    /*-----------------------------------------------------
    PROMPT INSTRUCTIONS
    -----------------------------------------------------*/

    $prompt_instructions = get_post_meta(
        $post->ID,
        '_isago_ai_prompt_instructions',
        true
    );


    $variables = get_post_meta(
        $post->ID,
        '_isago_ai_prompt_variables',
        true
    );


    if (!is_array($variables)) {
        $variables = array();
    }


    $featured = get_post_meta(
        $post->ID,
        '_isago_ai_prompt_featured',
        true
    );


$prompt_template = get_post_meta(
    $post->ID,
    '_isago_ai_prompt_template',
    true
);





/*-----------------------------------------------------
PROMPT ACCESS
-----------------------------------------------------*/

$prompt_access =
    isago_get_ai_prompt_access(
        $post->ID
    );


$prompt_price =
    isago_get_ai_prompt_price(
        $post->ID
    );
   


    /*-----------------------------------------------------
    PROMPT IMAGE
    -----------------------------------------------------*/

$prompt_image_id = isago_media_get_field(
    'post',
    $post->ID,
    'ai_prompt_image'
);

$prompt_image_url = '';

if ($prompt_image_id) {
    $prompt_image_url = isago_media_get_field_image_url(
        'post',
        $post->ID,
        'ai_prompt_image',
        'medium'
    );
}



/*=========================================================
SOCIAL IMAGE URL FALLBACK
=========================================================*/

$social_image_url = get_post_meta(
    $post->ID,
    'isago_ai_prompt_social_image_url',
    true
);

if (
    empty($prompt_image_url)
    && is_string($social_image_url)
    && $social_image_url !== ''
) {
    $prompt_image_url = isago_ai_prompt_resolve_social_image_url(
        $social_image_url
    );
}


    /*-----------------------------------------------------
    PROMPT VIDEO
    -----------------------------------------------------*/

  
$prompt_video_id =
    isago_media_get_field(
        'post',
        $post->ID,
        'ai_prompt_video'
    );

$prompt_video_url = '';

if ($prompt_video_id) {

    $prompt_video_url =
        isago_media_get_field_url(
            'post',
            $post->ID,
            'ai_prompt_video'
        );
}


/*-----------------------------------------------------
PROMPT VIDEO / SOCIAL URL
-----------------------------------------------------*/

$prompt_video_source_url =
    get_post_meta(
        $post->ID,
        '_isago_ai_prompt_video_source_url',
        true
    );


/*
 * Backward compatibility for existing prompts.
 */
if (empty($prompt_video_source_url)) {

    $prompt_video_source_url =
        get_post_meta(
            $post->ID,
            '_isago_ai_prompt_youtube_url',
            true
        );
}

    /*-----------------------------------------------------
    PROMPT CATEGORIES
    -----------------------------------------------------*/

    $categories =
        isago_get_ai_prompt_categories(
            array(
                'hide_empty' => false,
                'orderby'    => 'name',
                'order'      => 'ASC',
            )
        );


    $assigned_categories =
        wp_get_post_terms(
            $post->ID,
            'ai_prompt_category',
            array(
                'fields' => 'ids',
            )
        );


    if (
        is_wp_error(
            $assigned_categories
        )
    ) {

        $assigned_categories = array();
    }


    $assigned_categories =
        array_map(
            'absint',
            $assigned_categories
        );


        
    /*-----------------------------------------------------
    AI PLATFORMS / MODELS
    -----------------------------------------------------*/

    $platforms =
        isago_get_ai_prompt_platforms(
            array(
                'hide_empty' => false,
                'orderby'    => 'name',
                'order'      => 'ASC',
            )
        );


    $assigned_platforms =
        wp_get_post_terms(
            $post->ID,
            'ai_platform',
            array(
                'fields' => 'ids',
            )
        );


    if (
        is_wp_error(
            $assigned_platforms
        )
    ) {

        $assigned_platforms = array();
    }


    $assigned_platforms =
        array_map(
            'absint',
            $assigned_platforms
        );

    ?>

    <div class="isago-ai-prompt-admin">


  
<!--=================================================
SHORT DESCRIPTION — RICH TEXT EDITOR
=================================================-->

<div
    class="isago-ai-prompt-short-description-admin"
    style="margin-bottom:30px;"
>
    <h3>
        <?php esc_html_e('Short Description', 'isago'); ?>
    </h3>

    <p class="description">
        <?php
        esc_html_e(
            'Write a short summary of what this prompt helps users create or accomplish. You can use paragraphs, bold text, links, and lists.',
            'isago'
        );
        ?>
    </p>

    <?php
    isago_render_rich_text_editor(
        'isago_ai_prompt_short_description_editor',
        $short_description,
        'isago_ai_prompt_short_description'
    );
    ?>
</div>



<!--=================================================
AI PROMPT TEMPLATE
=================================================-->

<div
    class="isago-ai-prompt-template-admin"
    style="
        margin-bottom:30px;
        padding:20px;
        border:1px solid #ddd;
        background:#f8f8f8;
    "
>

    <h3>
        <?php
        esc_html_e(
            'AI Prompt Template',
            'isago'
        );
        ?>
    </h3>

    <p class="description">
        <?php
        esc_html_e(
            'Write the actual reusable prompt here. Use placeholders such as {{character-description}} wherever users should enter their own information. This content is separate from the main article.',
            'isago'
        );
        ?>
    </p>

    <?php
    isago_render_rich_text_editor(
        'isago_ai_prompt_template_editor',
        $prompt_template,
        'isago_ai_prompt_template'
    );
    ?>

</div>


        <!--=================================================
        PROMPT ACCESS
        =================================================-->

        <div
            class="isago-ai-prompt-access-box"
        >

            <div class="isago-ai-prompt-access-heading">

                <div>

                    <span class="isago-ai-prompt-access-label">
                        <?php
                        esc_html_e(
                            'PROMPT ACCESS',
                            'isago'
                        );
                        ?>
                    </span>

                    <h3>
                        <?php
                        esc_html_e(
                            'Free or Paid',
                            'isago'
                        );
                        ?>
                    </h3>

                </div>

            </div>


            <p class="description">

                <?php
                esc_html_e(
                    'Choose whether this AI Prompt is available to everyone for free or requires a purchase.',
                    'isago'
                );
                ?>

            </p>


            <div
                class="isago-ai-prompt-access-options"
            >


                <!--=========================================
                FREE
                ==========================================-->

                <label
                    class="isago-ai-prompt-access-option"
                >

                    <input
                        type="radio"
                        name="isago_ai_prompt_access"
                        value="free"
                        <?php checked(
                            $prompt_access,
                            'free'
                        ); ?>
                    >

                    <span
                        class="isago-ai-prompt-access-option-content"
                    >

                        <strong>
                            <?php
                            esc_html_e(
                                'Free',
                                'isago'
                            );
                            ?>
                        </strong>

                        <small>
                            <?php
                            esc_html_e(
                                'Anyone can access this prompt.',
                                'isago'
                            );
                            ?>
                        </small>

                    </span>

                </label>

<br><br>
                <!--=========================================
                PAID
                ==========================================-->

                <label
                    class="isago-ai-prompt-access-option"
                >

                    <input
                        type="radio"
                        name="isago_ai_prompt_access"
                        value="paid"
                        <?php checked(
                            $prompt_access,
                            'paid'
                        ); ?>
                    >

                    <span
                        class="isago-ai-prompt-access-option-content"
                    >

                        <strong>
                            <?php
                            esc_html_e(
                                'Paid',
                                'isago'
                            );
                            ?>
                        </strong>

                        <small>
                            <?php
                            esc_html_e(
                                'Users must purchase this prompt to access it.',
                                'isago'
                            );
                            ?>
                        </small>

                    </span>

                </label>


            </div>


            <!--=========================================
            PRICE
            ==========================================-->

            <div
                class="isago-ai-prompt-price-row"
                <?php
                if ($prompt_access !== 'paid') :
                ?>
                    style="display:none;"
                <?php endif; ?>
            >

                <label
                    for="isago_ai_prompt_price"
                >

                    <strong>
                        <?php
                        esc_html_e(
                            'Price ',
                            'isago'
                        );
                        ?>
                    </strong>  

                </label>


                <div
                    class="isago-ai-prompt-price-input-wrap"
                >

                    <input
                        type="number"
                        id="isago_ai_prompt_price"
                        name="isago_ai_prompt_price"
                        value="<?php
                        echo esc_attr(
                            $prompt_price !== '0.00'
                                ? $prompt_price
                                : ''
                        );
                        ?>"
                        min="0.01"
                        step="0.01"
                        inputmode="decimal"
                        placeholder="0.00"
                    >

                </div>


                <p class="description">

                    <?php
                    esc_html_e(
                        'Enter the selling price for this prompt. The payment currency will be configured separately in the payment system.',
                        'isago'
                    );
                    ?>

                </p>

            </div>

        </div>


  



      <!--=================================================
        PROMPT MEDIA
        =================================================-->

<div class="isago-accordion">

    <button
        type="button"
        class="isago-accordion-toggle"
        aria-expanded="false"
    >

        <span>
            <?php
            esc_html_e(
                'Media',
                'isago'
            );
            ?>
        </span>

        <span
            class="dashicons dashicons-arrow-down-alt2"
            aria-hidden="true"
        ></span>

    </button>


    <div class="isago-accordion-panel">

            <h3>
                <?php
                esc_html_e(
                    'Prompt Media',
                    'isago'
                );
                ?>
            </h3>


            <p class="description">

                <?php
                esc_html_e(
                    'Add an optional image, uploaded video, and/or
video from YouTube, Instagram, Pinterest,
TikTok, or Vimeo.',
                    'isago'
                );
                ?>

            </p>


  <!--=================================================
PROMPT IMAGE
=================================================-->

<div
    class="isago-media-field"
    style="
        margin-top:25px;
        padding-top:20px;
        border-top:1px solid #ddd;
    "
>

    <p>

        <strong>
            <?php
            esc_html_e(
                'Prompt Image',
                'isago'
            );
            ?>
        </strong>

    </p>

    <?php

    $current_imagekit_account =
        isago_get_imagekit_media_account_id(
            $prompt_image_id
        );

    $imagekit_account_selector_id =
        isago_render_imagekit_account_selector(
            [
                'id' =>
                    'isago_ai_prompt_imagekit_account',

                'selected' =>
                    $current_imagekit_account,

                'label' =>
                    'ImageKit Account',
            ]
        );
isago_render_imagekit_folder_selector([
    'id'       => 'isago_ai_prompt_imagekit_folder',
    'selected' => '/Isago-Media/AI-Prompts/',
    'label'    => 'Destination folder',
    'class'    => 'isago-imagekit-folder-selector',
    'style'    => 'width:100%; margin-bottom:10px;',
]);
    ?>

    <input
        type="hidden"
        id="isago-ai-prompt-image"
        name="isago_ai_prompt_image_id"
        value="<?php
        echo esc_attr(
            $prompt_image_id
        );
        ?>"
    >



<?php
// Show the original URL entered by the editor, not the resolved image URL.
$social_image_url = get_post_meta(
    $post->ID,
    '_isago_ai_prompt_social_image_source_url',
    true
);

// Backward compatibility for prompts saved before the source URL was stored.
if (empty($social_image_url)) {
    $social_image_url = get_post_meta(
        $post->ID,
        'isago_ai_prompt_social_image_url',
        true
    );
}
?>




    <?php if ($prompt_image_url) : ?>

        <div
            id="isago-ai-prompt-image-preview"
            class="isago-media-preview"
            style="margin:15px 0;"
        >

            <img
                src="<?php
                echo esc_url(
                    $prompt_image_url
                );
                ?>"
                alt=""
                style="
                    max-width:300px;
                    width:auto;
                    height:auto;
                    display:block;
                "
            >

        </div>

    <?php else : ?>

        <div
            id="isago-ai-prompt-image-preview"
            class="isago-media-preview"
            style="
                display:none;
                margin:15px 0;
            "
        ></div>

    <?php endif; ?>

    <p>

        <button
            type="button"
            class="button button-primary isago-imagekit-upload-button"
            data-target="#isago-ai-prompt-image"
            data-preview="#isago-ai-prompt-image-preview"
            data-type="image"
            data-account-selector="<?php
            echo esc_attr(
                '#' . $imagekit_account_selector_id
            );
            ?>"
            data-folder-selector="#isago_ai_prompt_imagekit_folder"
        >
            Upload Image
        </button>

<button
    type="button"
    class="button isago-imagekit-select-existing-button"
    data-target="#isago-ai-prompt-image"
    data-preview="#isago-ai-prompt-image-preview"
    data-type="image"
    data-account-selector="<?php
        echo esc_attr('#' . $imagekit_account_selector_id);
    ?>"
    data-folder-selector="#isago_ai_prompt_imagekit_folder"
>
    Select Existing ImageKit Image
</button>

        <button
            type="button"
            class="button isago-imagekit-remove-button"
            data-target="#isago-ai-prompt-image"
            data-preview="#isago-ai-prompt-image-preview"
            data-type="image"
                data-object-type="post"
    data-object-id="<?php echo esc_attr($post->ID); ?>"
    data-field-key="ai_prompt_image"
        >
            Remove Image
        </button>

    </p>
<div class="isago-ai-prompt-social-image-url" style="margin:15px 0;">

    <label for="isago_ai_prompt_social_image_url">
        <strong>Prompt Image URL</strong>
    </label>

    <input
        type="url"
        class="widefat"
        id="isago_ai_prompt_social_image_url"
        name="isago_ai_prompt_social_image_url"
        value="<?php echo esc_attr($social_image_url); ?>"
        placeholder="https://example.com/image.webp"
    >

    <p class="description">
        Paste a direct image URL. You can also enter a public
        Pinterest or Instagram post URL if you intend to extract
        its image separately.
    </p>

</div>
</div>


       <!--=================================================
PROMPT VIDEO
=================================================-->

<div
    class="isago-media-field"
    style="
        margin-top:25px;
        padding-top:20px;
        border-top:1px solid #ddd;
    "
>

    <p>

        <strong>
            <?php
            esc_html_e(
                'Prompt Video',
                'isago'
            );
            ?>
        </strong>

    </p>

    <?php

    /*
     * Get the current Isago media record.
     */
    $prompt_video_id =
        isago_media_get_field(
            'post',
            $post->ID,
            'ai_prompt_video'
        );

    $prompt_video_url = '';

    if ($prompt_video_id) {

        $prompt_video_url =
            isago_media_get_field_url(
                'post',
                $post->ID,
                'ai_prompt_video'
            );
    }

    /*
     * Get the ImageKit account that owns
     * the current video.
     */
    $current_video_imagekit_account =
        isago_get_imagekit_media_account_id(
            $prompt_video_id
        );

    $video_imagekit_account_selector_id =
        isago_render_imagekit_account_selector(
            [
                'id' =>
                    'isago_ai_prompt_video_imagekit_account',

                'selected' =>
                    $current_video_imagekit_account,

                'label' =>
                    'ImageKit Account',
            ]
        );
isago_render_imagekit_folder_selector([
    'id'       => 'isago_ai_prompt_video_imagekit_folder',
    'selected' => '/Isago-Media/AI-Prompts/',
    'label'    => 'Destination folder',
    'class'    => 'isago-imagekit-folder-selector',
    'style'    => 'width:100%; margin-bottom:10px;',
]);
    ?>

    <input
        type="hidden"
        id="isago-ai-prompt-video"
        name="isago_ai_prompt_video_id"
        value="<?php
        echo esc_attr(
            $prompt_video_id
        );
        ?>"
    >

    <?php if ($prompt_video_url) : ?>

        <div
            id="isago-ai-prompt-video-preview"
            class="isago-media-preview"
            style="margin:15px 0;"
        >

            <video
                controls
                preload="metadata"
                style="
                    max-width:500px;
                    width:100%;
                    height:auto;
                    display:block;
                "
            >

                <source
                    src="<?php
                    echo esc_url(
                        $prompt_video_url
                    );
                    ?>"
                >

            </video>

        </div>

    <?php else : ?>

        <div
            id="isago-ai-prompt-video-preview"
            class="isago-media-preview"
            style="
                display:none;
                margin:15px 0;
            "
        ></div>

    <?php endif; ?>

    <p>

        <button
            type="button"
            class="button button-primary isago-imagekit-upload-button"
            data-target="#isago-ai-prompt-video"
            data-preview="#isago-ai-prompt-video-preview"
            data-type="video"
            data-account-selector="<?php
            echo esc_attr(
                '#' .
                $video_imagekit_account_selector_id

            );
            ?>"
            data-folder-selector="#isago_ai_prompt_video_imagekit_folder"
        >
            Upload Video
        </button>
  
<button
    type="button"
    class="button isago-imagekit-select-existing-video-button"
    data-type="video"
    data-target="#isago-ai-prompt-video"
    data-preview="#isago-ai-prompt-video-preview"
    data-account-selector="#isago_ai_prompt_video_imagekit_account"
    data-folder-selector="#isago_ai_prompt_video_imagekit_folder"
>
    Select Existing Video
</button>

        <button
            type="button"
            class="button isago-imagekit-remove-button"
            data-target="#isago-ai-prompt-video"
            data-preview="#isago-ai-prompt-video-preview"
            data-type="video"
    data-object-type="post"
    data-object-id="<?php echo esc_attr($post->ID); ?>"
    data-field-key="ai_prompt_video"
        >
            Remove Video
        </button>

    </p>

</div>







       <!--=================================================
VIDEO / SOCIAL URL
=================================================-->

<div
    style="
        margin-top:25px;
        padding-top:20px;
        border-top:1px solid #ddd;
    "
    class="isago-video-source-field"
>

    <p>

        <label
            for="isago-ai-prompt-video-source"
        >

            <strong>
                <?php
                esc_html_e(
                    'Video / Social URL',
                    'isago'
                );
                ?>
            </strong>

        </label>

    </p>


    <input
        type="url"
        class="widefat"
        id="isago-ai-prompt-video-source"
        name="isago_ai_prompt_video_source_url"
        value="<?php
        echo esc_attr(
            $prompt_video_source_url
        );
        ?>"
        placeholder="https://www.youtube.com/watch?v=..."
    >


    <p class="description">

        <?php
        esc_html_e(
            'Paste a YouTube, Instagram, Pinterest, TikTok, or Vimeo video URL.',
            'isago'
        );
        ?>

    </p>
<div
    id="isago-ai-prompt-social-video-preview"
    style="
        display:none;
        margin-top:15px;
        max-width:640px;
    "
    aria-live="polite"
></div>

</div>
    </div>


    </div>







        <!--=================================================
        VARIABLES
        =================================================-->

        <div
            style="
                padding:20px;
                background:#f8f8f8;
                border:1px solid #ddd;
                margin-bottom:30px;
            "
        >

            <h3>
                <?php
                esc_html_e(
                    'Prompt Variables',
                    'isago'
                );
                ?>
            </h3>


            <p>

                <?php
                esc_html_e(
                    'Add the variable names used inside the prompt. For example: character-description, setting, style, tone.',
                    'isago'
                );
                ?>

            </p>


            <div
                id="isago-ai-prompt-variables"
            >

                <?php if (!empty($variables)) : ?>

                  <?php foreach ($variables as $variable) : ?>

    <div
        class="isago-ai-prompt-variable"
        style="
            display:flex;
            align-items:center;
            gap:8px;
            margin-bottom:8px;
        "
    >

        <input
            type="text"
            name="isago_ai_prompt_variables[]"
            value="<?php
            echo esc_attr(
                $variable
            );
            ?>"
            placeholder="character-description"
            style="width:300px;"
        >


   


        <button
            type="button"
            class="button isago-remove-prompt-variable"
        >

            <?php
            esc_html_e(
                'Remove',
                'isago'
            );
            ?>

        </button>

    </div>

<?php endforeach; ?>

                <?php endif; ?>

            </div>


            <button
                type="button"
                class="button"
                id="isago-add-prompt-variable"
            >

                <?php
                esc_html_e(
                    'Add Variable',
                    'isago'
                );
                ?>

            </button>

        </div>


        <!--=================================================
        AI PLATFORMS / MODELS
        =================================================-->

      <!--=================================================
AI PLATFORMS / MODELS
=================================================-->

<div class="isago-accordion">

    <button
        type="button"
        class="isago-accordion-toggle"
        aria-expanded="false"
    >

        <span>
            <?php
            esc_html_e(
                'AI Tools / Models',
                'isago'
            );
            ?>
        </span>

        <span
            class="dashicons dashicons-arrow-down-alt2"
            aria-hidden="true"
        ></span>

    </button>


    <div class="isago-accordion-panel">

            <h3>
                <?php
                esc_html_e(
                    'AI Platforms / Models',
                    'isago'
                );
                ?>
            </h3>


            <p class="description">

                <?php
                esc_html_e(
                    'Select the AI platform or model this prompt is designed for. These options are fetched from your existing AI Platforms.',
                    'isago'
                );
                ?>

            </p>


            <?php if (!empty($platforms)) : ?>

                <div
                    style="
                        display:grid;
                        grid-template-columns:
                            repeat(
                                auto-fill,
                                minmax(220px, 1fr)
                            );
                        gap:8px;
                        max-height:280px;
                        overflow-y:auto;
                        padding:12px;
                        border:1px solid #ddd;
                        background:#fafafa;
                    "
                >

                    <?php foreach ($platforms as $platform) : ?>

                        <?php

                        $platform_id =
                            absint(
                                $platform->term_id
                            );


                        $checked =
                            in_array(
                                $platform_id,
                                $assigned_platforms,
                                true
                            );

                        ?>

                        <label
                            style="
                                display:flex;
                                align-items:center;
                                gap:8px;
                                padding:8px;
                            "
                        >

                            <input
                                type="checkbox"
                                name="isago_ai_prompt_platforms[]"
                                value="<?php
                                echo esc_attr(
                                    $platform_id
                                );
                                ?>"
                                <?php checked(
                                    $checked,
                                    true
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

                    <?php endforeach; ?>

                </div>

            <?php else : ?>

                <div
                    style="
                        padding:15px;
                        background:#fff8e5;
                        border:1px solid #e5c76b;
                    "
                >

                    <?php
                    esc_html_e(
                        'No AI Platforms are available yet. Create AI Platforms first.',
                        'isago'
                    );
                    ?>

                </div>

            <?php endif; ?>

        </div>

    </div>


      <!--=================================================
PROMPT CATEGORIES
=================================================-->

<div class="isago-accordion">

    <button
        type="button"
        class="isago-accordion-toggle"
        aria-expanded="false"
    >

        <span>
            <?php
            esc_html_e(
                'AI Prompt Categories',
                'isago'
            );
            ?>
        </span>

        <span
            class="dashicons dashicons-arrow-down-alt2"
            aria-hidden="true"
        ></span>

    </button>


    <div class="isago-accordion-panel">

            <h3>
                <?php
                esc_html_e(
                    'AI Prompt Categories',
                    'isago'
                );
                ?>
            </h3>


            <p class="description">

                <?php
                esc_html_e(
                    'Assign one or more categories to this prompt.',
                    'isago'
                );
                ?>

            </p>


            <?php

            isago_render_ai_prompt_term_checkboxes(
                $categories,
                $assigned_categories,
                'isago_ai_prompt_categories'
            );

            ?>

        </div>

    </div>


<!--=================================================
PROMPT TAGS
=================================================-->

<?php

/*-----------------------------------------------------
GET ALL WORDPRESS TAGS
-----------------------------------------------------*/

$prompt_tags =
    get_terms(
        array(
            'taxonomy'   => 'post_tag',
            'hide_empty' => false,
            'orderby'    => 'name',
            'order'      => 'ASC',
        )
    );


if (
    is_wp_error(
        $prompt_tags
    )
) {

    $prompt_tags =
        array();

}


/*-----------------------------------------------------
GET ASSIGNED TAGS
-----------------------------------------------------*/

$assigned_tags =
    wp_get_post_terms(
        $post->ID,
        'post_tag',
        array(
            'fields' => 'ids',
        )
    );


if (
    is_wp_error(
        $assigned_tags
    )
) {

    $assigned_tags =
        array();

}


$assigned_tags =
    array_map(
        'absint',
        $assigned_tags
    );

?>


<div class="isago-accordion">

    <button
        type="button"
        class="isago-accordion-toggle"
        aria-expanded="false"
    >

        <span>
            <?php
            esc_html_e(
                'Tags',
                'isago'
            );
            ?>
        </span>

        <span
            class="dashicons dashicons-arrow-down-alt2"
            aria-hidden="true"
        ></span>

    </button>


    <div class="isago-accordion-panel">


    <h3>

        <?php
        esc_html_e(
            'Prompt Tags',
            'isago'
        );
        ?>

    </h3>


    <p class="description">

        <?php
        esc_html_e(
            'Select existing WordPress tags that describe this prompt.',
            'isago'
        );
        ?>

    </p>


    <?php if (
        !empty(
            $prompt_tags
        )
    ) : ?>


        <div
            style="
                display:grid;
                grid-template-columns:
                    repeat(
                        auto-fill,
                        minmax(180px, 1fr)
                    );
                gap:8px;
                max-height:280px;
                overflow-y:auto;
                padding:12px;
                border:1px solid #ddd;
                background:#fafafa;
            "
        >


            <?php foreach (
                $prompt_tags
                as $prompt_tag
            ) : ?>


                <?php

                $tag_id =
                    absint(
                        $prompt_tag->term_id
                    );


                $checked =
                    in_array(
                        $tag_id,
                        $assigned_tags,
                        true
                    );

                ?>


                <label
                    style="
                        display:flex;
                        align-items:center;
                        gap:8px;
                        padding:8px;
                        cursor:pointer;
                    "
                >


                    <input
                        type="checkbox"
                        name="isago_ai_prompt_tags[]"
                        value="<?php
                        echo esc_attr(
                            $tag_id
                        );
                        ?>"
                        <?php checked(
                            $checked,
                            true
                        ); ?>
                    >


                    <span>

                        <?php
                        echo esc_html(
                            $prompt_tag->name
                        );
                        ?>

                    </span>


                </label>


            <?php endforeach; ?>


        </div>


    <?php else : ?>


        <div
            style="
                padding:15px;
                background:#fff8e5;
                border:1px solid #e5c76b;
            "
        >

            <?php
            esc_html_e(
                'No WordPress tags have been created yet. Create tags first using the Tags panel.',
                'isago'
            );
            ?>

        </div>


    <?php endif; ?>


</div>

    </div>



        <!--=================================================
        FEATURED
        =================================================-->

        <div
            style="
                margin-bottom:30px;
                padding-top:20px;
                border-top:1px solid #ddd;
            "
        >

            <label>

                <input
                    type="checkbox"
                    name="isago_ai_prompt_featured"
                    value="1"
                    <?php checked(
                        $featured,
                        '1'
                    ); ?>
                >


                <strong>

                    <?php
                    esc_html_e(
                        'Featured Prompt',
                        'isago'
                    );
                    ?>

                </strong>

            </label>


            <p class="description">

                <?php
                esc_html_e(
                    'Featured prompts can be highlighted in the AI Prompt Library.',
                    'isago'
                );
                ?>

            </p>

        </div>


   

    </div>

    <?php
}


/*=========================================================
AI PROMPT INSTRUCTIONS — SEPARATE META BOX
=========================================================*/

/**
 * Register the How to Use This Prompt meta box.
 */
add_action(
    'add_meta_boxes_ai_prompt',
    'isago_ai_prompt_add_instructions_meta_box'
);

function isago_ai_prompt_add_instructions_meta_box( $post ) {

    add_meta_box(
        'isago_ai_prompt_instructions_box',
        __( 'How to Use This Prompt', 'isago' ),
        'isago_ai_prompt_render_instructions_meta_box',
        'ai_prompt',
        'normal',
        'high'
    );
}


/**
 * Display the rich-text editor.
 */
function isago_ai_prompt_render_instructions_meta_box( $post ) {

    // Load previously saved instructions.
    $prompt_instructions = get_post_meta(
        $post->ID,
        '_isago_ai_prompt_instructions',
        true
    );
    ?>

    <div class="isago-ai-prompt-instructions-admin">

        <p class="description">
            <?php
            esc_html_e(
                'Write clear instructions explaining how users should use this prompt. You can add headings, paragraphs, bold text, links, numbered steps, and bullet points.',
                'isago'
            );
            ?>
        </p>

        <?php
   isago_render_rich_text_editor(
    'isago_ai_prompt_instructions_editor',
    $prompt_instructions,
    'isago_ai_prompt_instructions'
);
        ?>

        <p class="description">
            <?php
            esc_html_e(
                'Use the Visual tab to format your instructions, or the Text tab to edit HTML directly.',
                'isago'
            );
            ?>
        </p>

    </div>

    <?php
}







/*=========================================================
TERM CHECKBOX RENDERER — HIERARCHICAL CATEGORIES
=========================================================*/

function isago_render_ai_prompt_term_checkboxes(
    $terms,
    $selected,
    $field_name
) {

    if (empty($terms)) {
        echo '<p>';
        esc_html_e(
            'No terms have been created yet.',
            'isago'
        );
        echo '</p>';
        return;
    }

    // Normalize selected term IDs.
    $selected = array_map('absint', (array) $selected);

    // Group terms by their parent ID.
    $children = array();

    foreach ($terms as $term) {
        $parent_id = absint($term->parent ?? 0);

        if (!isset($children[$parent_id])) {
            $children[$parent_id] = array();
        }

        $children[$parent_id][] = $term;
    }

    // Sort siblings alphabetically without separating
    // children from their own parent.
    foreach ($children as &$siblings) {
        usort($siblings, function ($a, $b) {
            return strcasecmp($a->name, $b->name);
        });
    }
    unset($siblings);

    // Render each category and recursively render its children.
    $render_terms = function ($parent_id, $level = 0) use (
        &$render_terms,
        &$children,
        $selected,
        $field_name
    ) {

        if (empty($children[$parent_id])) {
            return;
        }

        echo '<ul class="isago-prompt-category-tree isago-prompt-category-level-' .
            absint($level) . '">';

        foreach ($children[$parent_id] as $term) {

            $term_id = absint($term->term_id);
            $checked = in_array($term_id, $selected, true);

            echo '<li class="isago-prompt-category-item">';

            echo '<label>';

            echo '<input type="checkbox" name="' .
                esc_attr($field_name) .
                '[]" value="' .
                esc_attr($term_id) .
                '" ';

            checked($checked, true);

            echo '>';

            echo '<span>' .
                esc_html($term->name) .
                '</span>';

            echo '</label>';

            // Keep all descendants beneath their parent.
            $render_terms($term_id, $level + 1);

            echo '</li>';
        }

        echo '</ul>';
    };

    ?>
    <div class="isago-prompt-category-tree-container">
        <?php $render_terms(0); ?>
    </div>
    <?php
}



/*=========================================================
SAVE AI PROMPT
=========================================================*/

function isago_save_ai_prompt(
    $post_id
) {

    if (
        !isset(
            $_POST[
                'isago_ai_prompt_nonce'
            ]
        )
    ) {
        return;
    }


    if (
        !wp_verify_nonce(
            sanitize_text_field(
                wp_unslash(
                    $_POST[
                        'isago_ai_prompt_nonce'
                    ]
                )
            ),
            'isago_save_ai_prompt'
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
        get_post_type(
            $post_id
        ) !== 'ai_prompt'
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



/*=====================================================
SAVE SHORT DESCRIPTION — RICH TEXT
=====================================================*/

if (isset($_POST['isago_ai_prompt_short_description'])) {

    $short_description = wp_kses_post(
        wp_unslash(
            $_POST['isago_ai_prompt_short_description']
        )
    );

    if (trim(wp_strip_all_tags($short_description)) !== '') {

        update_post_meta(
            $post_id,
            '_isago_ai_prompt_short_description',
            $short_description
        );

    } else {

        delete_post_meta(
            $post_id,
            '_isago_ai_prompt_short_description'
        );
    }
}


 


/*=====================================================
SAVE PROMPT INSTRUCTIONS — RICH TEXT
=====================================================*/

if (isset($_POST['isago_ai_prompt_instructions'])) {

    $submitted_instructions = wp_unslash(
        $_POST['isago_ai_prompt_instructions']
    );

    $prompt_instructions = isago_ai_prompt_format_instructions(
        $submitted_instructions
    );

    if (trim(wp_strip_all_tags($prompt_instructions)) !== '') {

        update_post_meta(
            $post_id,
            '_isago_ai_prompt_instructions',
            $prompt_instructions
        );

    } else {

        delete_post_meta(
            $post_id,
            '_isago_ai_prompt_instructions'
        );
    }
}



 

/*=====================================================
SAVE AI PROMPT TEMPLATE — RICH TEXT
=====================================================*/

if (isset($_POST['isago_ai_prompt_template'])) {

    $prompt_template = wp_kses_post(
        wp_unslash(
            $_POST['isago_ai_prompt_template']
        )
    );

    if (trim(wp_strip_all_tags($prompt_template)) !== '') {

        update_post_meta(
            $post_id,
            '_isago_ai_prompt_template',
            $prompt_template
        );

    } else {

        delete_post_meta(
            $post_id,
            '_isago_ai_prompt_template'
        );

    }
}




/*=====================================================
PROMPT ACCESS
=====================================================*/

$prompt_access =
    'free';


if (
    isset(
        $_POST[
            'isago_ai_prompt_access'
        ]
    )
) {

    $requested_access =
        sanitize_key(
            wp_unslash(
                $_POST[
                    'isago_ai_prompt_access'
                ]
            )
        );


    if (
        in_array(
            $requested_access,
            array(
                'free',
                'paid'
            ),
            true
        )
    ) {

        $prompt_access =
            $requested_access;
    }
}


/*
 * Always store a known access type.
 */

update_post_meta(
    $post_id,
    '_isago_ai_prompt_access',
    $prompt_access
);


/*=====================================================
PROMPT PRICE
=====================================================*/

if (
    $prompt_access === 'paid'
) {

    $raw_price = '';


    if (
        isset(
            $_POST[
                'isago_ai_prompt_price'
            ]
        )
    ) {

        $raw_price =
            trim(
                wp_unslash(
                    $_POST[
                        'isago_ai_prompt_price'
                    ]
                )
            );
    }


    /*
     * Normalize decimal input.
     */

    $raw_price =
        str_replace(
            ',',
            '.',
            $raw_price
        );


    /*
     * Only accept numeric values.
     */

    if (
        is_numeric(
            $raw_price
        )
    ) {

        $price =
            (float) $raw_price;


        /*
         * A paid prompt must have
         * a positive price.
         */

        if ($price > 0) {

            update_post_meta(
                $post_id,
                '_isago_ai_prompt_price',
                number_format(
                    $price,
                    2,
                    '.',
                    ''
                )
            );

        } else {

            /*
             * Invalid paid price:
             *
             * Keep the prompt safe by
             * reverting it to Free.
             */

            update_post_meta(
                $post_id,
                '_isago_ai_prompt_access',
                'free'
            );


            delete_post_meta(
                $post_id,
                '_isago_ai_prompt_price'
            );
        }

    } else {

        /*
         * Invalid price:
         *
         * Revert to Free rather than
         * creating a paid prompt with
         * no valid price.
         */

        update_post_meta(
            $post_id,
            '_isago_ai_prompt_access',
            'free'
        );


        delete_post_meta(
            $post_id,
            '_isago_ai_prompt_price'
        );
    }

} else {

    /*
     * Free prompts do not need a price.
     */

    delete_post_meta(
        $post_id,
        '_isago_ai_prompt_price'
    );
}


    /*=====================================================
    PROMPT IMAGE
    =====================================================*/

if (isset($_POST['isago_ai_prompt_image_id'])) {

    $media_id = absint(
        wp_unslash(
            $_POST['isago_ai_prompt_image_id']
        )
    );

    if ($media_id) {

        $media = isago_get_media($media_id);

        if ($media) {

            isago_media_set_field(
                'post',
                $post_id,
                'ai_prompt_image',
                $media_id
            );

            update_post_meta(
                $post_id,
                '_isago_ai_prompt_image_id',
                $media_id
            );
        }

    } else {

        isago_media_remove_field_and_cleanup(
            'post',
            $post_id,
            'ai_prompt_image'
        );

        delete_post_meta(
            $post_id,
            '_isago_ai_prompt_image_id'
        );
    }
}



/*=====================================================
PROMPT SOCIAL IMAGE URL
=====================================================*/

if (isset($_POST['isago_ai_prompt_social_image_url'])) {

    $source_url = esc_url_raw(
        trim(
            wp_unslash(
                $_POST['isago_ai_prompt_social_image_url']
            )
        )
    );

    if ($source_url && wp_http_validate_url($source_url)) {

        // Keep the original Pin URL separately.
        update_post_meta(
            $post_id,
            '_isago_ai_prompt_social_image_source_url',
            $source_url
        );

        // Resolve the actual image URL.
        $resolved_url =
            isago_ai_prompt_resolve_social_image_url($source_url);

        if ($resolved_url) {

            update_post_meta(
                $post_id,
                'isago_ai_prompt_social_image_url',
                $resolved_url
            );

        } else {

            delete_post_meta(
                $post_id,
                'isago_ai_prompt_social_image_url'
            );
        }

    } else {

        delete_post_meta(
            $post_id,
            '_isago_ai_prompt_social_image_source_url'
        );

        delete_post_meta(
            $post_id,
            'isago_ai_prompt_social_image_url'
        );
    }
}



/*=====================================================
PROMPT VIDEO
=====================================================*/

if (
    isset(
        $_POST[
            'isago_ai_prompt_video_id'
        ]
    )
) {

    $media_id =
        absint(
            wp_unslash(
                $_POST[
                    'isago_ai_prompt_video_id'
                ]
            )
        );

    if ($media_id) {

        $media =
            isago_get_media(
                $media_id
            );

        if ($media) {

            isago_media_set_field(
                'post',
                $post_id,
                'ai_prompt_video',
                $media_id
            );

            update_post_meta(
                $post_id,
                '_isago_ai_prompt_video_id',
                $media_id
            );
        }

    } else {

        isago_media_remove_field_and_cleanup(
            'post',
            $post_id,
            'ai_prompt_video'
        );

        delete_post_meta(
            $post_id,
            '_isago_ai_prompt_video_id'
        );
    }
}


/*=====================================================
PROMPT VIDEO / SOCIAL URL
=====================================================*/

if (
    isset(
        $_POST[
            'isago_ai_prompt_video_source_url'
        ]
    )
) {

    $video_source_url =
        trim(
            wp_unslash(
                $_POST[
                    'isago_ai_prompt_video_source_url'
                ]
            )
        );


    if (
        $video_source_url === ''
    ) {

        delete_post_meta(
            $post_id,
            '_isago_ai_prompt_video_source_url'
        );

        /*
         * Remove legacy YouTube URL too.
         */

        delete_post_meta(
            $post_id,
            '_isago_ai_prompt_youtube_url'
        );

    }

    elseif (
        preg_match(
            '#^https?://#i',
            $video_source_url
        )
    ) {

        update_post_meta(
            $post_id,
            '_isago_ai_prompt_video_source_url',
            esc_url_raw(
                $video_source_url
            )
        );

        /*
         * Remove legacy YouTube URL.
         */

        delete_post_meta(
            $post_id,
            '_isago_ai_prompt_youtube_url'
        );
    }
}

  /*=====================================================
VARIABLES
=====================================================*/

$variables = array();


if (
    isset(
        $_POST[
            'isago_ai_prompt_variables'
        ]
    )
    &&
    is_array(
        $_POST[
            'isago_ai_prompt_variables'
        ]
    )
) {

    $raw_variables =
        wp_unslash(
            $_POST[
                'isago_ai_prompt_variables'
            ]
        );


    foreach (
        $raw_variables
        as $variable
    ) {

        $variable =
            sanitize_text_field(
                $variable
            );


        $variable =
            strtolower(
                trim(
                    $variable
                )
            );


        /*
         * Match the same variable format
         * used by the Gutenberg toolbar.
         */

        $variable =
            preg_replace(
                '/[^a-z0-9_-]+/',
                '-',
                $variable
            );


        $variable =
            preg_replace(
                '/^-+|-+$/',
                '',
                $variable
            );


        if (
            $variable !== ''
        ) {

            $variables[] =
                $variable;

        }

    }


    /*
     * Remove duplicates while preserving order.
     */

    $variables =
        array_values(
            array_unique(
                $variables
            )
        );

}


if (!empty($variables)) {

    update_post_meta(
        $post_id,
        '_isago_ai_prompt_variables',
        $variables
    );

} else {

    delete_post_meta(
        $post_id,
        '_isago_ai_prompt_variables'
    );

}

    /*=====================================================
    FEATURED
    =====================================================*/

    if (
        isset(
            $_POST[
                'isago_ai_prompt_featured'
            ]
        )
        &&
        $_POST[
            'isago_ai_prompt_featured'
        ] === '1'
    ) {

        update_post_meta(
            $post_id,
            '_isago_ai_prompt_featured',
            '1'
        );

    } else {

        delete_post_meta(
            $post_id,
            '_isago_ai_prompt_featured'
        );
    }





    /*=====================================================
    AI PROMPT CATEGORIES
    =====================================================*/

    isago_save_ai_prompt_taxonomy(
        $post_id,
        'isago_ai_prompt_categories',
        'ai_prompt_category'
    );

/*=====================================================
PROMPT TAGS
=====================================================*/

isago_save_ai_prompt_taxonomy(
    $post_id,
    'isago_ai_prompt_tags',
    'post_tag'
);
    /*=====================================================
    AI PLATFORMS / MODELS
    =====================================================*/

    isago_save_ai_prompt_taxonomy(
        $post_id,
        'isago_ai_prompt_platforms',
        'ai_platform'
    );
}

add_action(
    'save_post_ai_prompt',
    'isago_save_ai_prompt'
);


/*=========================================================
SAVE TAXONOMY ASSIGNMENT
=========================================================*/

function isago_save_ai_prompt_taxonomy(
    $post_id,
    $field_name,
    $taxonomy
) {

    $term_ids = array();


    if (
        isset(
            $_POST[$field_name]
        )
        &&
        is_array(
            $_POST[$field_name]
        )
    ) {

        $term_ids =
            array_map(
                'absint',
                wp_unslash(
                    $_POST[$field_name]
                )
            );


        $term_ids =
            array_filter(
                $term_ids
            );


        $term_ids =
            array_values(
                array_unique(
                    $term_ids
                )
            );
    }


    if (!empty($term_ids)) {

        $valid_terms =
            get_terms(
                array(
                    'taxonomy'   => $taxonomy,
                    'include'    => $term_ids,
                    'hide_empty' => false,
                    'fields'     => 'ids',
                )
            );


        if (
            is_wp_error(
                $valid_terms
            )
        ) {

            $valid_terms = array();
        }


        $valid_terms =
            array_map(
                'absint',
                $valid_terms
            );


        wp_set_post_terms(
            $post_id,
            $valid_terms,
            $taxonomy,
            false
        );

    } else {

        wp_set_post_terms(
            $post_id,
            array(),
            $taxonomy,
            false
        );
    }
}




/**
 * Convert basic Markdown into HTML for AI Prompt instructions.
 *
 * Existing HTML is preserved so TinyMCE formatting is not
 * converted a second time.
 */
function isago_ai_prompt_format_instructions($content) {

    $content = trim((string) $content);

    if ($content === '') {
        return '';
    }

    // Preserve existing rich-text HTML.
    if (preg_match('/<\s*[a-z][^>]*>/i', $content)) {
        return wp_kses_post($content);
    }

    // Normalize line endings.
    $content = str_replace(["\r\n", "\r"], "\n", $content);

    // Escape plain text before adding safe HTML.
    $content = esc_html($content);

    // Convert Markdown links: [text](https://example.com)
    $content = preg_replace(
        '/\[([^\]]+)\]\((https?:\/\/[^\s)]+)\)/',
        '<a href="$2">$1</a>',
        $content
    );

    // Convert bold and italic Markdown.
    $content = preg_replace(
        '/\*\*(.+?)\*\*|__(.+?)__/',
        '<strong>$1$2</strong>',
        $content
    );

    $content = preg_replace(
        '/(?<!\*)\*([^*\n]+)\*(?!\*)|(?<!_)_([^_\n]+)_(?!_)/',
        '<em>$1$2</em>',
        $content
    );

    $lines = explode("\n", $content);
    $output = [];
    $list_type = '';

    foreach ($lines as $line) {

        $line = trim($line);

        // Blank line: close any open list.
        if ($line === '') {
            if ($list_type !== '') {
                $output[] = '</' . $list_type . '>';
                $list_type = '';
            }
            continue;
        }

        // Markdown headings.
        if (preg_match('/^(#{1,4})\s+(.+)$/', $line, $match)) {

            if ($list_type !== '') {
                $output[] = '</' . $list_type . '>';
                $list_type = '';
            }

            $level = min(strlen($match[1]) + 1, 6);
            $output[] = '<h' . $level . '>' .
                $match[2] .
                '</h' . $level . '>';

            continue;
        }

        // Numbered list items: 1. Item or 1) Item
        if (preg_match('/^\d+[.)]\s+(.+)$/', $line, $match)) {
            $type = 'ol';
            $item = $match[1];
        }
        // Bulleted list items: - Item, * Item, or + Item
        elseif (preg_match('/^[-*+]\s+(.+)$/', $line, $match)) {
            $type = 'ul';
            $item = $match[1];
        } else {
            $type = '';
            $item = '';
        }

        if ($type !== '') {

            if ($list_type !== $type) {
                if ($list_type !== '') {
                    $output[] = '</' . $list_type . '>';
                }

                $output[] = '<' . $type . '>';
                $list_type = $type;
            }

            $output[] = '<li>' . $item . '</li>';
            continue;
        }

        if ($list_type !== '') {
            $output[] = '</' . $list_type . '>';
            $list_type = '';
        }

        $output[] = '<p>' . $line . '</p>';
    }

    if ($list_type !== '') {
        $output[] = '</' . $list_type . '>';
    }

    return wp_kses_post(implode("\n", $output));
}




/*=========================================================
PROMPT CATEGORY ADD FIELDS
=========================================================*/

function isago_ai_prompt_category_add_fields()
{
    ?>

    <!--=====================================================
    FONT AWESOME ICON
    =====================================================-->

    <div class="form-field">

        <label
            for="isago_ai_prompt_category_icon"
        >

            <?php
            esc_html_e(
                'Font Awesome Icon',
                'isago'
            );
            ?>

        </label>


        <input
            type="text"
            id="isago_ai_prompt_category_icon"
            name="isago_ai_prompt_category_icon"
            value=""
            class="regular-text"
            placeholder="fa-solid fa-pen"
        >


        <div
            id="isago-ai-prompt-category-icon-preview"
            style="
                margin:15px 0;
                min-height:40px;
                font-size:32px;
                line-height:1;
                display:flex;
                align-items:center;
            "
        ></div>


        <p class="description">

            <?php
            esc_html_e(
                'Enter a Font Awesome icon class. Example: fa-solid fa-pen',
                'isago'
            );
            ?>

        </p>


        <p class="description">

            <strong>
                <?php
                esc_html_e(
                    'Examples:',
                    'isago'
                );
                ?>
            </strong>

            <code>fa-solid fa-pen</code>,
            <code>fa-solid fa-image</code>,
            <code>fa-solid fa-video</code>,
            <code>fa-solid fa-code</code>,
            <code>fa-solid fa-robot</code>

        </p>

    </div>


  

    <?php
}

add_action(
    'ai_prompt_category_add_form_fields',
    'isago_ai_prompt_category_add_fields'
);


/*=========================================================
PROMPT CATEGORY EDIT FIELDS
=========================================================*/

function isago_ai_prompt_category_edit_fields(
    $term
) {

    $term_id =
        absint(
            $term->term_id
        );


    $icon =
        get_term_meta(
            $term_id,
            '_isago_ai_prompt_category_icon',
            true
        );



    ?>

    <!--=====================================================
    FONT AWESOME ICON
    =====================================================-->

    <tr class="form-field">

        <th scope="row">

            <label
                for="isago_ai_prompt_category_icon"
            >

                <?php
                esc_html_e(
                    'Font Awesome Icon',
                    'isago'
                );
                ?>

            </label>

        </th>


        <td>

            <input
                type="text"
                id="isago_ai_prompt_category_icon"
                name="isago_ai_prompt_category_icon"
                value="<?php
                echo esc_attr(
                    $icon
                );
                ?>"
                class="regular-text"
                placeholder="fa-solid fa-pen"
            >


            <div
                id="isago-ai-prompt-category-icon-preview"
                style="
                    margin:15px 0;
                    min-height:40px;
                    font-size:32px;
                    line-height:1;
                    display:flex;
                    align-items:center;
                "
            >

                <?php if ($icon) : ?>

                    <i
                        class="<?php
                        echo esc_attr(
                            $icon
                        );
                        ?>"
                        aria-hidden="true"
                    ></i>

                <?php endif; ?>

            </div>


            <p class="description">

                <?php
                esc_html_e(
                    'Enter a Font Awesome icon class. Example: fa-solid fa-pen',
                    'isago'
                );
                ?>

            </p>


            <p class="description">

                <strong>
                    <?php
                    esc_html_e(
                        'Examples:',
                        'isago'
                    );
                    ?>
                </strong>

                <code>fa-solid fa-pen</code>,
                <code>fa-solid fa-image</code>,
                <code>fa-solid fa-video</code>,
                <code>fa-solid fa-code</code>,
                <code>fa-solid fa-robot</code>

            </p>

        </td>

    </tr>




    <?php
}

add_action(
    'ai_prompt_category_edit_form_fields',
    'isago_ai_prompt_category_edit_fields'
);


/*=========================================================
SAVE PROMPT CATEGORY FIELDS
=========================================================*/

function isago_save_ai_prompt_category_fields(
    $term_id
) {

    $term_id =
        absint(
            $term_id
        );


    if (!$term_id) {
        return;
    }


    if (
        !current_user_can(
            'manage_categories'
        )
    ) {
        return;
    }


    /*-----------------------------------------------------
    FONT AWESOME ICON
    -----------------------------------------------------*/

    if (
        isset(
            $_POST[
                'isago_ai_prompt_category_icon'
            ]
        )
    ) {

        $icon =
            sanitize_text_field(
                wp_unslash(
                    $_POST[
                        'isago_ai_prompt_category_icon'
                    ]
                )
            );


        if ($icon !== '') {

            update_term_meta(
                $term_id,
                '_isago_ai_prompt_category_icon',
                $icon
            );

        } else {

            delete_term_meta(
                $term_id,
                '_isago_ai_prompt_category_icon'
            );
        }
    }


    /*-----------------------------------------------------
    REMOVE OLD CATEGORY INTRODUCTION
    -----------------------------------------------------*/

    delete_term_meta(
        $term_id,
        '_isago_ai_prompt_category_intro'
    );



}

add_action(
    'created_ai_prompt_category',
    'isago_save_ai_prompt_category_fields'
);

add_action(
    'edited_ai_prompt_category',
    'isago_save_ai_prompt_category_fields'
);


/*=========================================================
LOAD FONT AWESOME ON AI PROMPT CATEGORY ADMIN SCREEN
=========================================================*/

function isago_ai_prompt_category_admin_font_awesome()
{

    $screen =
        get_current_screen();


    if (
        !$screen ||
        $screen->taxonomy !== 'ai_prompt_category'
    ) {
        return;
    }


    wp_enqueue_style(
        'isago-font-awesome-admin-prompt-category',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css',
        array(),
        '6.7.2'
    );
}

add_action(
    'admin_enqueue_scripts',
    'isago_ai_prompt_category_admin_font_awesome'
);


/*=========================================================
PROMPT CATEGORY FONT AWESOME PREVIEW
=========================================================*/

function isago_ai_prompt_category_admin_icon_script()
{

    $screen =
        get_current_screen();


    if (
        !$screen ||
        $screen->taxonomy !== 'ai_prompt_category'
    ) {
        return;
    }

    ?>

    <script>

    jQuery(function($) {

        "use strict";


        function isagoUpdateAiPromptCategoryIconPreview()
        {

            var $input =
                $("#isago_ai_prompt_category_icon");


            var $preview =
                $("#isago-ai-prompt-category-icon-preview");


            if (
                !$input.length ||
                !$preview.length
            ) {
                return;
            }


            var value =
                $.trim(
                    $input.val()
                );


            $preview.empty();


            if (!value) {
                return;
            }


            var classes =
                value.split(/\s+/);


            var validClasses = [];


            $.each(
                classes,
                function(
                    index,
                    className
                ) {

                    if (
                        /^fa-[a-z0-9-]+$/i.test(
                            className
                        )
                    ) {

                        validClasses.push(
                            className
                        );

                    }

                }
            );


            if (!validClasses.length) {
                return;
            }


            var $icon =
                $("<i>", {

                    "class":
                        validClasses.join(" "),

                    "aria-hidden":
                        "true"

                });


            $preview.append(
                $icon
            );
        }


        $(document).on(
            "input",
            "#isago_ai_prompt_category_icon",
            function() {

                isagoUpdateAiPromptCategoryIconPreview();

            }
        );


        $(document).on(
            "change",
            "#isago_ai_prompt_category_icon",
            function() {

                isagoUpdateAiPromptCategoryIconPreview();

            }
        );


        isagoUpdateAiPromptCategoryIconPreview();

    });

    </script>

    <?php
}

add_action(
    'admin_footer',
    'isago_ai_prompt_category_admin_icon_script'
);



/*=========================================================
AI PROMPT ACCESS ADMIN UI
=========================================================*/

function isago_ai_prompt_access_admin_script()
{
    $screen =
        get_current_screen();


    if (
        !$screen
        ||
        $screen->post_type !== 'ai_prompt'
        ||
        !in_array(
            $screen->base,
            array(
                'post',
                'post-new'
            ),
            true
        )
    ) {
        return;
    }

    ?>

    <script>

    jQuery(function($) {

        "use strict";


        function isagoToggleAiPromptPrice()
        {

            var isPaid =
                $(
                    'input[name="isago_ai_prompt_access"][value="paid"]'
                ).is(':checked');


            var $priceRow =
                $('.isago-ai-prompt-price-row');


            if (
                !$priceRow.length
            ) {

                return;
            }


            if (isPaid) {

                $priceRow
                    .stop(true, true)
                    .slideDown(180);

            } else {

                $priceRow
                    .stop(true, true)
                    .slideUp(180);

            }

        }


        $(document).on(
            'change',
            'input[name="isago_ai_prompt_access"]',
            function() {

                isagoToggleAiPromptPrice();

            }
        );


        isagoToggleAiPromptPrice();

    });

    </script>

    <?php
}

add_action(
    'admin_footer',
    'isago_ai_prompt_access_admin_script'
);




/*=========================================================
AI PROMPT SOCIAL VIDEO PREVIEW — AJAX
=========================================================*/

function isago_ai_prompt_social_video_preview_ajax() {

    check_ajax_referer(
        'isago_ai_prompt_social_video_preview',
        'nonce'
    );

    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_send_json_error(
            array( 'message' => 'Permission denied.' ),
            403
        );
    }

    $url = isset( $_POST['url'] )
        ? esc_url_raw( trim( wp_unslash( $_POST['url'] ) ) )
        : '';

    if ( ! $url || ! wp_http_validate_url( $url ) ) {
        wp_send_json_error(
            array( 'message' => 'Enter a valid URL.' )
        );
    }

    $host = strtolower(
        (string) wp_parse_url( $url, PHP_URL_HOST )
    );

    $allowed_hosts = array(
        'youtube.com',
        'www.youtube.com',
        'm.youtube.com',
        'youtu.be',
        'www.youtu.be',
        'vimeo.com',
        'www.vimeo.com',
        'player.vimeo.com',
        'tiktok.com',
        'www.tiktok.com',
        'm.tiktok.com',
        'vm.tiktok.com',
        'instagram.com',
        'www.instagram.com',
        'pinterest.com',
        'www.pinterest.com',
        'pin.it',
    );

    $allowed = false;

    foreach ( $allowed_hosts as $allowed_host ) {
        if (
            $host === $allowed_host ||
            (
                in_array(
                    $allowed_host,
                    array(
                        'youtube.com',
                        'vimeo.com',
                        'tiktok.com',
                        'instagram.com',
                        'pinterest.com',
                    ),
                    true
                ) &&
                str_ends_with(
                    $host,
                    '.' . $allowed_host
                )
            )
        ) {
            $allowed = true;
            break;
        }
    }

    if ( ! $allowed ) {
        wp_send_json_error(
            array( 'message' => 'This video provider is not supported.' )
        );
    }

    $embed = wp_oembed_get(
        $url,
        array(
            'width'  => 640,
            'height' => 360,
        )
    );

    if ( ! $embed ) {
        wp_send_json_error(
            array(
                'message' =>
                    'An embed preview is unavailable for this URL or provider.'
            )
        );
    }

    $allowed_html = wp_kses_allowed_html( 'post' );

    $allowed_html['iframe'] = array(
        'src'             => true,
        'width'           => true,
        'height'          => true,
        'title'           => true,
        'frameborder'     => true,
        'allow'            => true,
        'allowfullscreen' => true,
        'loading'         => true,
        'referrerpolicy'  => true,
        'sandbox'         => true,
        'class'           => true,
        'style'           => true,
    );

    wp_send_json_success(
        array(
            'html' => wp_kses( $embed, $allowed_html ),
        )
    );
}

add_action(
    'wp_ajax_isago_ai_prompt_social_video_preview',
    'isago_ai_prompt_social_video_preview_ajax'
);



/*=========================================================
AI PROMPT SOCIAL VIDEO PREVIEW — ADMIN SCRIPT
=========================================================*/

function isago_ai_prompt_social_video_preview_script() {

    $screen = get_current_screen();

    if (
        ! $screen ||
        $screen->post_type !== 'ai_prompt' ||
        ! in_array(
            $screen->base,
            array( 'post', 'post-new' ),
            true
        )
    ) {
        return;
    }

    $nonce = wp_create_nonce(
        'isago_ai_prompt_social_video_preview'
    );
    ?>

    <script>
    jQuery(function($) {

        var $input = $('#isago-ai-prompt-video-source');
        var $preview = $('#isago-ai-prompt-social-video-preview');

        if (!$input.length || !$preview.length) {
            return;
        }

        var request = null;
        var lastUrl = '';

        function updateSocialVideoPreview() {

            var url = $.trim($input.val());

            if (!url) {
                lastUrl = '';
                $preview.empty().hide();
                return;
            }

            if (url === lastUrl) {
                return;
            }

            lastUrl = url;

            if (request) {
                request.abort();
            }

            $preview
                .show()
                .text('Loading video preview…');

            request = $.ajax({
                url: ajaxurl,
                method: 'POST',
                dataType: 'json',
                data: {
                    action: 'isago_ai_prompt_social_video_preview',
                    nonce: <?php echo wp_json_encode( $nonce ); ?>,
                    url: url
                }
            });

            request.done(function(response) {

                if ($.trim($input.val()) !== url) {
                    return;
                }

                if (
                    response &&
                    response.success &&
                    response.data &&
                    response.data.html
                ) {
                    $preview
                        .html(response.data.html)
                        .show();
                } else {
                    var message =
                        response &&
                        response.data &&
                        response.data.message
                            ? response.data.message
                            : 'Preview unavailable. Open the URL to view the post.';

                    $preview
                        .empty()
                        .append(
                            $('<p>', {
                                class: 'description',
                                text: message
                            })
                        )
                        .append(
                            $('<a>', {
                                href: url,
                                target: '_blank',
                                rel: 'noopener noreferrer',
                                text: 'Open video or social post'
                            })
                        )
                        .show();
                }
            });

            request.fail(function(xhr, status) {

                if (status === 'abort') {
                    return;
                }

                if ($.trim($input.val()) !== url) {
                    return;
                }

                $preview
                    .empty()
                    .append(
                        $('<p>', {
                            class: 'description',
                            text: 'Preview could not be loaded. You can still open the URL.'
                        })
                    )
                    .append(
                        $('<a>', {
                            href: url,
                            target: '_blank',
                            rel: 'noopener noreferrer',
                            text: 'Open video or social post'
                        })
                    )
                    .show();
            });
        }

        $input.on(
            'change',
            updateSocialVideoPreview
        );

        $input.on(
            'paste',
            function() {
                setTimeout(updateSocialVideoPreview, 100);
            }
        );

        $input.on(
            'keydown',
            function(event) {
                if (event.key === 'Enter') {
                    updateSocialVideoPreview();
                }
            }
        );

        // Load a preview for an existing saved URL on Edit.
        if ($.trim($input.val())) {
            updateSocialVideoPreview();
        }
    });
    </script>

    <?php
}

add_action(
    'admin_footer',
    'isago_ai_prompt_social_video_preview_script'
);