<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
|--------------------------------------------------------------------------
| Get Video URL
|--------------------------------------------------------------------------
*/

function isago_get_video_url($post_id = null)
{

    $post_id = $post_id ?: get_the_ID();

    return get_post_meta(

        $post_id,

        '_isago_video_url',

        true

    );

}

/*
|--------------------------------------------------------------------------
| Get Thumbnail
|--------------------------------------------------------------------------
*/

function isago_get_video_thumbnail($post_id = null)
{

    $post_id = $post_id ?: get_the_ID();

    /*
    --------------------------
    Featured image first
    --------------------------
    */

    if (has_post_thumbnail($post_id)) {

        return get_the_post_thumbnail(

            $post_id,

            'large'

        );

    }




    /*
    --------------------------
    YouTube thumbnail
    --------------------------
    */

    $url = isago_get_video_url($post_id);

    if (!$url) {

        return '';

    }

    if (preg_match(

        '/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([^?&]+)/',

        $url,

        $matches

    )) {

        $id = $matches[1];

        return sprintf(

            '<img src="https://img.youtube.com/vi/%s/maxresdefault.jpg" alt="">',

            esc_attr($id)

        );

    }

    return '';

}



/*
|--------------------------------------------------------------------------
| Get YouTube Video ID
|--------------------------------------------------------------------------
*/

function isago_get_youtube_id($url)
{

    if (empty($url)) {
        return '';
    }

    preg_match(

        '/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([^?&]+)/',

        $url,

        $matches

    );

    return $matches[1] ?? '';

}


/*
|--------------------------------------------------------------------------
| Convert ISO8601 Duration
|--------------------------------------------------------------------------
*/

function isago_format_youtube_duration($duration)
{

    try {

        $interval = new DateInterval($duration);

    } catch (Exception $e) {

        return '';

    }

    $hours = $interval->h;
    $minutes = $interval->i;
    $seconds = $interval->s;

    if ($hours > 0) {

        return sprintf(
            '%d:%02d:%02d',
            $hours,
            $minutes,
            $seconds
        );

    }

    return sprintf(
        '%d:%02d',
        $minutes,
        $seconds
    );

}







/*=========================================================
Video View Counter
=========================================================*/

function isago_track_video_view()
{

    if (!is_singular('isago_video')) {
        return;
    }


    $video_id = get_queried_object_id();


    if (!$video_id) {
        return;
    }


    /*
    |--------------------------------------------------------------------------
    | Prevent repeated counting during the same browser session
    |--------------------------------------------------------------------------
    */

    $cookie_name = 'isago_video_viewed_' . $video_id;


    if (isset($_COOKIE[$cookie_name])) {
        return;
    }


    $views = absint(
        get_post_meta(
            $video_id,
            '_isago_video_views',
            true
        )
    );


    $views++;


    update_post_meta(
        $video_id,
        '_isago_video_views',
        $views
    );


    /*
    |--------------------------------------------------------------------------
    | Remember that this browser has already counted the view
    |--------------------------------------------------------------------------
    */

    setcookie(
        $cookie_name,
        '1',
        time() + DAY_IN_SECONDS,
        COOKIEPATH,
        COOKIE_DOMAIN
    );

}

add_action(
    'wp',
    'isago_track_video_view'
);



