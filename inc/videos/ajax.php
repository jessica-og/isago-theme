<?php

if (!defined('ABSPATH')) {
    exit;
}

add_action(

    'wp_ajax_isago_video',

    'isago_ajax_video'

);

add_action(

    'wp_ajax_nopriv_isago_video',

    'isago_ajax_video'

);

function isago_ajax_video()
{

    $id = absint($_POST['id']);

    echo wp_oembed_get(

        isago_get_video_url($id),

        [

            'width'=>900

        ]

    );

    wp_die();

}