<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
SAVE VIDEO META
=========================================================*/

add_action(
    'save_post_isago_video',
    'isago_save_video_meta'
);

function isago_save_video_meta($post_id)
{

    /*------------------------------------------------------
    Security
    ------------------------------------------------------*/

    if (
        !isset(
            $_POST['isago_video_meta_nonce']
        )
    ) {
        return;
    }


    if (
        !wp_verify_nonce(
            sanitize_text_field(
                wp_unslash(
                    $_POST['isago_video_meta_nonce']
                )
            ),
            'isago_video_meta'
        )
    ) {
        return;
    }


    /*------------------------------------------------------
    Autosave / Revision
    ------------------------------------------------------*/

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


    /*------------------------------------------------------
    Permission
    ------------------------------------------------------*/

    if (
        !current_user_can(
            'edit_post',
            $post_id
        )
    ) {
        return;
    }



/*=====================================================
SAVE IMAGEKIT-HOSTED VIDEO
=====================================================*/

$hosted_video_id = isset($_POST['isago_hosted_video_id'])
    ? absint($_POST['isago_hosted_video_id'])
    : 0;

if ($hosted_video_id) {
    isago_media_set_field(
        'post',
        $post_id,
        'hosted_video',  
        $hosted_video_id
    );
} else {
    isago_media_clear_field(
        'post',
        $post_id,
        'hosted_video'
    );
}



    /*=====================================================
    READ IMAGE
    =====================================================*/

    $image_id = 0;

    if (
        isset(
            $_POST['isago_image_id']
        )
    ) {

        $image_id =
            absint(
                $_POST['isago_image_id']
            );

    }





/*=====================================================
READ VIDEO SOURCE URL
=====================================================*/

$video_source_url = '';

if (
    isset(
        $_POST['isago_video_source_url']
    )
) {

    $video_source_url =
        esc_url_raw(
            wp_unslash(
                $_POST['isago_video_source_url']
            )
        );
}


/*=====================================================
READ IMAGE + VIDEO SOURCE URL
=====================================================*/

$image_video_source_url = '';

if (
    isset(
        $_POST['isago_image_video_source_url']
    )
) {

    $image_video_source_url =
        esc_url_raw(
            wp_unslash(
                $_POST['isago_image_video_source_url']
            )
        );
}


/*=====================================================
MODE 1 — IMAGE + OPTIONAL VIDEO / SOCIAL URL
=====================================================*/

if ($image_id) {

    /*
     * Save the ImageKit image.
     */

    isago_media_set_field(
        'post',
        $post_id,
        'image',
        $image_id
    );


    /*
     * Image mode does not use the
     * normal video-only URL.
     */

    delete_post_meta(
        $post_id,
        '_isago_video_source_url'
    );

    delete_post_meta(
        $post_id,
        '_isago_youtube_url'
    );


    /*
     * Save optional Video / Social URL
     * attached to the image.
     */

    if (
        !empty(
            $image_video_source_url
        )
    ) {

        update_post_meta(
            $post_id,
            '_isago_image_video_source_url',
            $image_video_source_url
        );


        /*
         * Remove the old YouTube-only
         * metadata.
         */

        delete_post_meta(
            $post_id,
            '_isago_image_video_url'
        );

    } else {

        delete_post_meta(
            $post_id,
            '_isago_image_video_source_url'
        );

        delete_post_meta(
            $post_id,
            '_isago_image_video_url'
        );
    }

}


/*=====================================================
MODE 2 — VIDEO / SOCIAL URL ONLY
=====================================================*/

elseif (
    !empty(
        $video_source_url
    )
) {

    /*
     * Save the new generic URL.
     */

    update_post_meta(
        $post_id,
        '_isago_video_source_url',
        $video_source_url
    );


    /*
     * Remove legacy YouTube-only metadata.
     */

    delete_post_meta(
        $post_id,
        '_isago_youtube_url'
    );


    /*
     * Remove Image + Social mode.
     */

    delete_post_meta(
        $post_id,
        '_isago_image_video_source_url'
    );

    delete_post_meta(
        $post_id,
        '_isago_image_video_url'
    );


    /*
     * Remove the ImageKit image.
     */

    isago_media_clear_field(
        'post',
        $post_id,
        'image'
    );

}


/*=====================================================
MODE 3 — NO VIDEO SOURCE
=====================================================*/

else {

    /*
     * Remove all Video / Social URL metadata.
     */

    delete_post_meta(
        $post_id,
        '_isago_video_source_url'
    );

    delete_post_meta(
        $post_id,
        '_isago_youtube_url'
    );


    /*
     * Remove Image + Video / Social metadata.
     */

    delete_post_meta(
        $post_id,
        '_isago_image_video_source_url'
    );

    delete_post_meta(
        $post_id,
        '_isago_image_video_url'
    );


    /*
     * Remove the ImageKit image.
     */

    isago_media_clear_field(
        'post',
        $post_id,
        'image'
    );

}




/*=====================================================
SAVE VIDEO AI PROMPT — RICH TEXT
=====================================================*/

$video_ai_prompt = '';

if (isset($_POST['isago_video_ai_prompt'])) {

    $video_ai_prompt = wp_kses_post(
        wp_unslash($_POST['isago_video_ai_prompt'])
    );
}

if (trim(wp_strip_all_tags($video_ai_prompt)) !== '') {

    update_post_meta(
        $post_id,
        '_isago_video_ai_prompt',
        $video_ai_prompt
    );

} else {

    delete_post_meta(
        $post_id,
        '_isago_video_ai_prompt'
    );
}


    /*=====================================================
    CATEGORIES
    =====================================================*/

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


        $categories =
            array_filter(
                $categories
            );


        wp_set_post_categories(
            $post_id,
            $categories
        );

    } else {

        wp_set_post_categories(
            $post_id,
            []
        );

    }

}






/*=========================================================
SAVE VIDEO TAGS
=========================================================*/

add_action(
    'save_post_isago_video',
    'isago_save_video_tags',
    30
);

function isago_save_video_tags($post_id) {

    /* Security: verify the Tags meta-box nonce. */
    if (
        !isset($_POST['isago_video_tags_nonce']) ||
        !wp_verify_nonce(
            sanitize_text_field(
                wp_unslash($_POST['isago_video_tags_nonce'])
            ),
            'isago_save_video_tags'
        )
    ) {
        return;
    }

    /* Ignore autosaves and revisions. */
    if (
        (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) ||
        wp_is_post_revision($post_id) ||
        wp_is_post_autosave($post_id)
    ) {
        return;
    }

    /* Check editing permissions. */
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (!taxonomy_exists('post_tag')) {
        return;
    }

    /* Read selected tag IDs. */
    $tag_ids = [];

    if (
        isset($_POST['isago_video_tags']) &&
        is_array($_POST['isago_video_tags'])
    ) {
        $tag_ids = array_map(
            'absint',
            wp_unslash($_POST['isago_video_tags'])
        );

        $tag_ids = array_values(
            array_unique(
                array_filter($tag_ids)
            )
        );
    }

    /* Save tags using WordPress's standard Tags taxonomy. */
    $result = wp_set_object_terms(
        $post_id,
        $tag_ids,
        'post_tag',
        false
    );

    if (is_wp_error($result)) {
        error_log(
            'Isago Video Tags: Failed to save tags for video ID ' .
            absint($post_id) . '. ' .
            $result->get_error_message()
        );
    }
}
