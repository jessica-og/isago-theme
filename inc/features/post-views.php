<?php

if (!defined('ABSPATH')) {
    exit;
}

/*=========================================================
Track Post Views
=========================================================*/

function isago_track_post_views()
{
    if (!is_singular()) {
        return;
    }
  
    $post_id = get_queried_object_id();

    if (!$post_id) {
        return;
    }

    /*
    ---------------------------------------------------------
    Current values
    ---------------------------------------------------------
    */

    $total_views = (int) get_post_meta(
        $post_id,
        '_isago_post_views',
        true
    );

    $weekly_views = (int) get_post_meta(
        $post_id,
        '_isago_post_views_week',
        true
    );

    $last_view = (int) get_post_meta(
        $post_id,
        '_isago_post_last_view',
        true
    );

    $now = current_time('timestamp');

    /*
    ---------------------------------------------------------
    Reset weekly counter after 7 days
    ---------------------------------------------------------
    */

    if (
        $last_view &&
        ($now - $last_view) > WEEK_IN_SECONDS
    ) {
        $weekly_views = 0;
    }

    /*
    ---------------------------------------------------------
    Update counters
    ---------------------------------------------------------
    */

    update_post_meta(
        $post_id,
        '_isago_post_views',
        $total_views + 1
    );

    update_post_meta(
        $post_id,
        '_isago_post_views_week',
        $weekly_views + 1
    );

    update_post_meta(
        $post_id,
        '_isago_post_last_view',
        $now
    );
}

add_action(
    'wp',
    'isago_track_post_views'
);