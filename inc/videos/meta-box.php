<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
REGISTER VIDEO META BOX
=========================================================*/

add_action(
    'add_meta_boxes',
    'isago_register_video_settings_metabox'
);

function isago_register_video_settings_metabox()
{

    add_meta_box(

        'isago-video-settings',

        'Video Settings',

        'isago_video_meta_callback',

        'isago_video',

        'normal',

        'high'

    );

}


/*=========================================================
VIDEO META BOX
=========================================================*/

function isago_video_meta_callback($post)
{

    /*---------------------------------------------------------
    Nonce
    ---------------------------------------------------------*/

    wp_nonce_field(
        'isago_video_meta',
        'isago_video_meta_nonce'
    );


   



    /*=========================================================
    YOUTUBE URL
    =========================================================*/
$video_source_url =
    get_post_meta(
        $post->ID,
        '_isago_video_source_url',
        true
    );


/*
 * Backward compatibility for existing
 * YouTube-only posts.
 */
if (empty($video_source_url)) {

    $video_source_url =
        get_post_meta(
            $post->ID,
            '_isago_youtube_url',
            true
        );
}


   /*=========================================================
IMAGE + YOUTUBE IMAGE
=========================================================*/

$image_id =
    isago_media_get_field(
        'post',
        $post->ID,
        'image'
    );


$image_preview_url = '';


if ($image_id) {

    $image_preview_url =
        isago_media_get_field_image_url(
            'post',
            $post->ID,
            'image',
            'medium_large'
        );

}


    /*=========================================================
    IMAGE + YOUTUBE URL
    =========================================================*/

  $image_video_source_url =
    get_post_meta(
        $post->ID,
        '_isago_image_video_source_url',
        true
    );


/*
 * Backward compatibility.
 */
if (empty($image_video_source_url)) {

    $image_video_source_url =
        get_post_meta(
            $post->ID,
            '_isago_image_video_url',
            true
        );
}



    ?>

    <div class="isago-video-meta">

        <table class="form-table">




            <!-- =================================================
          VIDEO / SOCIAL URL
            ================================================= -->

            <tr>

                <th>

                 <label
    for="isago-video-source-url"
>
    Video / Social URL
</label>

                </th>

                <td>

                    <div
                        class="isago-video-source-field"
                    >

                  <input
    type="url"
    class="widefat isago-video-source-input"
    id="isago-video-source-url"
    name="isago_video_source_url"
    value="<?php echo esc_attr(
        $video_source_url
    ); ?>"
    placeholder="https://www.youtube.com/watch?v=..."
>

                           <button
        type="button"
        class="button isago-remove-video-source"
        data-target="#isago-video-source-url"
    >
        Remove
    </button>

</div>

<div
    id="isago-youtube-preview"
    class="isago-youtube-preview"
    style="
        display:none;
        margin-top:15px;
    "
></div>

                   

                   <p
    class="description"
    style="
  
        padding-bottom:25px;
    "
>
    Paste a supported video URL such as
    YouTube, Instagram, Pinterest, TikTok,
    or Vimeo.
</p>

                </td>

            </tr>


            <!-- =================================================
            IMAGE + VIDEO / SOCIAL URL
            ================================================= -->

            <tr>

                <th>

                    <label
                        for="isago-image-video-image"
                    >
                        Image Video
                    </label>

                </th>

                <td>


                    <input
                        type="hidden"
                        id="isago-image-video-image"
                        name="isago_image_id"
                        value="<?php echo esc_attr(
                            $image_id
                        ); ?>"
                    >


 <?php
$image_account_id = '';

if ($image_id) {

    $image_account_id =
        isago_get_imagekit_media_account_id(
            $image_id
        );

}
?>


<div class="isago-media-actions">

    <?php
    // ImageKit account selector.
    isago_render_imagekit_account_selector([
        'id'       => 'isago-video-image-account',
        'selected' => $image_account_id,
    ]);

    // Destination folder selector.
    isago_render_imagekit_folder_selector([
        'id'       => 'isago-video-image-folder',
        'selected' => '/Isago-Media/Site-Assets/',
        'label'    => 'Destination folder',
        'style'    => 'width:100%; margin:10px 0;',
    ]);
    ?>

    <button
        type="button"
        class="button button-primary isago-imagekit-upload-button"
        data-target="#isago-image-video-image"
        data-preview="#isago-video-image-preview"
        data-type="image"
        data-account-selector="#isago-video-image-account"
        data-folder-selector="#isago-video-image-folder"
    >
        Upload to ImageKit
    </button>

    <button
        type="button"
        class="button isago-imagekit-select-existing-button"
        data-target="#isago-image-video-image"
        data-preview="#isago-video-image-preview"
        data-type="image"
        data-account-selector="#isago-video-image-account"
        data-folder-selector="#isago-video-image-folder"
    >
        Select Existing Image  
    </button>

    <button
        type="button"
        class="button isago-imagekit-remove-button"
        data-target="#isago-image-video-image"
        data-preview="#isago-video-image-preview"
        data-type="image"
    >
        Remove
    </button>

</div>


<?php if (!empty($image_preview_url)) : ?>

    <div
        id="isago-video-image-preview"
        class="isago-media-preview"
        style="
            margin-top:15px;
        "
    >

        <img
            src="<?php echo esc_url(
                $image_preview_url
            ); ?>"
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
        id="isago-video-image-preview"
        class="isago-media-preview"
        style="
            display:none;
            margin-top:15px;
        "
    ></div>

<?php endif; ?>

                    <p style="margin-top:20px;">

                        <label
                            for="isago-image-video-url"
                        >
<strong>
    Video / Social URL
</strong>
                        </label>

                    </p>


                    <input
                        type="url"
                        class="widefat"
                        id="isago-image-video-url"
                       name="isago_image_video_source_url"
                      value="<?php echo esc_attr(
    $image_video_source_url
); ?>"
                        placeholder="https://www.youtube.com/watch?v=..."
                    >


                    <div
                        class="isago-media-actions"
                    >

                        <button
                            type="button"
                            class="button isago-remove-video-source"
                          data-target="#isago-video-source-url"
                        >
                            Remove
                        </button>

                    </div>


                <p
    class="description"
    style="
        padding-bottom:25px;
    "
>
    Upload an image and optionally attach a
    YouTube, Instagram, Pinterest, TikTok, or
    Vimeo video URL. The image will be used as
    the thumbnail.
</p>

                </td>

            </tr>


<?php
/*=========================================================
IMAGEKIT VIDEO
=========================================================*/

$hosted_video_id = isago_media_get_field(
    'post',
    $post->ID,
    'hosted_video'
);

$hosted_video_url = '';

if ($hosted_video_id) {
    $hosted_video_url = isago_media_get_field_image_url(
        'post',
        $post->ID,
        'hosted_video',
        'full'
    );
}

$hosted_video_account_id = '';

if ($hosted_video_id) {
    $hosted_video_account_id =
        isago_get_imagekit_media_account_id(
            $hosted_video_id
        );
}
?>

<tr>
    <th>
        <label for="isago-hosted-video">
            ImageKit Video
        </label>
    </th>

    <td>
        <input
            type="hidden"
            id="isago-hosted-video"
            name="isago_hosted_video_id"
            value="<?php echo esc_attr($hosted_video_id); ?>"
        >

        <?php
        isago_render_imagekit_account_selector([
            'id'       => 'isago-hosted-video-account',
            'selected' => $hosted_video_account_id,
        ]);

        isago_render_imagekit_folder_selector([
            'id'       => 'isago-hosted-video-folder',
            'selected' => '/Isago-Media/Site-Assets/',
            'label'    => 'Destination folder',
            'style'    => 'width:100%; margin:10px 0;',
        ]);
        ?>

        <div class="isago-media-actions">
            <button
                type="button"
                class="button button-primary isago-imagekit-upload-button"
                data-target="#isago-hosted-video"
                data-preview="#isago-hosted-video-preview"
                data-type="video"
                data-account-selector="#isago-hosted-video-account"
                data-folder-selector="#isago-hosted-video-folder"
            >
                Upload Video to ImageKit
            </button>

            <button
                type="button"
                class="button isago-imagekit-select-existing-video-button"
                data-type="video"
                data-target="#isago-hosted-video"
                data-preview="#isago-hosted-video-preview"
                data-account-selector="#isago-hosted-video-account"
                data-folder-selector="#isago-hosted-video-folder"
            >
                Select Existing Video
            </button>

            <button
                type="button"
                class="button isago-imagekit-remove-button"
                data-target="#isago-hosted-video"
                data-preview="#isago-hosted-video-preview"
                data-type="video"
                data-object-type="post"
                data-object-id="<?php echo esc_attr($post->ID); ?>"
                data-field-key="hosted_video"
                <?php disabled(empty($hosted_video_id)); ?>
            >
                Remove Video
            </button>
        </div>

        <div
            id="isago-hosted-video-preview"
            class="isago-media-preview"
            style="<?php echo empty($hosted_video_url) ? 'display:none;' : 'margin-top:15px;'; ?>"
        >
            <?php if (!empty($hosted_video_url)) : ?>
                <video
                    src="<?php echo esc_url($hosted_video_url); ?>"
                    controls
                    preload="metadata"
                    style="display:block; width:100%; max-width:480px; height:auto;"
                ></video>
            <?php endif; ?>
        </div>

        <p class="description" style="margin-top:12px;">
            Upload a video to ImageKit or reuse an existing video
            from your Isago media library.
        </p>
    </td>
</tr>

           
        </table>




<?php
/*=========================================================
VIDEO AI PROMPT — RICH TEXT EDITOR
=========================================================*/

$video_ai_prompt = get_post_meta(
    $post->ID,
    '_isago_video_ai_prompt',
    true
);
?>

<div class="isago-video-prompt-admin" style="margin-top:25px;">

    <h3>AI Video Prompt</h3>

    <p class="description">
        Write or paste your complete AI video prompt.
        Use headings, paragraphs, bold text, lists and links
        to organize the instructions. Visitors can reveal and
        copy the prompt on the frontend.
    </p>

    <?php
isago_render_rich_text_editor(
    'isago_video_ai_prompt',
    $video_ai_prompt
);
    ?>

</div>



        <!-- =====================================================
        SOURCE INFORMATION
        ===================================================== -->

        <div class="notice notice-info inline">

          <p>

        <strong>Video source:</strong>

        Paste a video or social URL such as
        YouTube, Instagram, Pinterest, TikTok,
        or Vimeo. You can also upload a video
        to ImageKit.

    </p>

        </div>

    </div>

    <?php
}





/*=========================================================
ISAGO VIDEO TAGS — REGISTER TAXONOMY
=========================================================*/

/**
 * Make standard WordPress Tags available to isago_video.
 */
add_action('init', 'isago_connect_tags_to_videos', 100);

function isago_connect_tags_to_videos() {

    if (!taxonomy_exists('post_tag')) {
        return;
    }

    if (post_type_exists('isago_video')) {
        register_taxonomy_for_object_type(
            'post_tag',
            'isago_video'
        );
    }
}


/*=========================================================
VIDEO TAGS META BOX
=========================================================*/

add_action(
    'add_meta_boxes_isago_video',
    'isago_register_video_tags_metabox',
    110
);

function isago_register_video_tags_metabox($post) {

    add_meta_box(
        'isago-video-tags',
        'Tags',
        'isago_video_tags_callback',
        'isago_video',
        'side',
        'default'
    );
}


/*=========================================================
DISPLAY AVAILABLE TAGS
=========================================================*/

function isago_video_tags_callback($post) {

    wp_nonce_field(
        'isago_save_video_tags',
        'isago_video_tags_nonce'
    );

    if (!taxonomy_exists('post_tag')) {
        echo '<p>WordPress Tags taxonomy is unavailable.</p>';
        return;
    }

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

    $selected_tags = wp_get_object_terms(
        $post->ID,
        'post_tag',
        ['fields' => 'ids']
    );

    if (is_wp_error($selected_tags)) {
        $selected_tags = [];
    }

    $selected_tags = array_map(
        'absint',
        $selected_tags
    );
    ?>

    <div style="max-height:300px; overflow-y:auto; padding:8px 2px;">

        <?php if (empty($tags)) : ?>

            <p>No tags have been created yet.</p>

            <p>
                Create tags under
                <strong>Posts → Tags</strong>
                in your WordPress dashboard.
            </p>

        <?php else : ?>

            <?php foreach ($tags as $tag) : ?>

                <label style="display:block; margin-bottom:9px;">

                    <input
                        type="checkbox"
                        name="isago_video_tags[]"
                        value="<?php echo esc_attr($tag->term_id); ?>"
                        <?php checked(
                            in_array(
                                (int) $tag->term_id,
                                $selected_tags,
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
        Select one or more existing tags for this video.
    </p>

    <?php
}


