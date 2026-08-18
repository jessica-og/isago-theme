<?php

if (!defined('ABSPATH')) {
    exit;
}

/*=========================================================
Admin Post Helpers
=========================================================*/

function isago_admin_posts()
{

    return get_posts([

        'post_type'      => 'post',

        'posts_per_page' => -1,

        'orderby'        => 'date',

        'order'          => 'DESC'

    ]);  

}