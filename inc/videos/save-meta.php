<?php

if (!defined('ABSPATH')) {
    exit;
}

function isago_save_video_meta($post_id)
{

    if (

        !isset($_POST['isago_video_nonce']) ||

        !wp_verify_nonce(

            $_POST['isago_video_nonce'],

            'isago_video_nonce'

        )

    ) {

        return;

    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {

        return;

    }

    if (get_post_type($post_id) !== 'isago_video') {

        return;

    }

  update_post_meta(
    $post_id,
    '_isago_video_source',
    sanitize_text_field(
        $_POST['isago_video_source'] ?? 'youtube'
    )
);

update_post_meta(
    $post_id,
    '_isago_video_url',
    esc_url_raw(
        $_POST['isago_video_url'] ?? ''
    )
);

update_post_meta(
    $post_id,
    '_isago_video_duration',
    sanitize_text_field(
        $_POST['isago_video_duration'] ?? ''
    )
);

update_post_meta(
    $post_id,
    '_isago_video_featured',
    isset($_POST['isago_video_featured']) ? 1 : 0
);

}

add_action(
    'save_post',
    'isago_save_video_meta'
);