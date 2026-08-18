<?php

if (!defined('ABSPATH')) {
    exit;
}

/*=========================================================
Find Matching Categories
=========================================================*/

function isago_find_categories($keyword)
{
    return get_terms([

        'taxonomy'   => 'category',

        'hide_empty' => false,

        'search'     => $keyword

    ]);
}