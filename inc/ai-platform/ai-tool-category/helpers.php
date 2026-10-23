<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
GET ALL AI TOOL CATEGORIES
=========================================================*/

function isago_get_ai_tool_categories($args = [])
{
  
    $defaults = [

        'taxonomy' =>
            'ai_tool_category',

        'hide_empty' =>
            false,

        'orderby' =>
            'name',

        'order' =>
            'ASC',

    ];


    $args = wp_parse_args(
        $args,
        $defaults
    );


    $terms = get_terms($args);


    if (is_wp_error($terms)) {
        return [];
    }


    return $terms;

}


/*=========================================================
GET SINGLE AI TOOL CATEGORY
=========================================================*/

function isago_get_ai_tool_category($term_id)
{

    $term_id = absint($term_id);


    if (!$term_id) {
        return null;
    }


    $term = get_term(
        $term_id,
        'ai_tool_category'
    );


    if (
        !$term ||
        is_wp_error($term)
    ) {
        return null;
    }


    return $term;

}


/*=========================================================
GET AI ToolS CONNECTED TO CATEGORY
=========================================================*/

function isago_get_ai_tool_category_platforms($term_id)
{

    $term_id = absint($term_id);


    if (!$term_id) {
        return [];

    }


    $platforms = get_terms([

        'taxonomy' =>
            'ai_platform',

        'hide_empty' =>
            false,

        'orderby' =>
            'name',

        'order' =>
            'ASC',

    ]);


    if (
        is_wp_error($platforms) ||
        empty($platforms)
    ) {
        return [];
    }


    $connected = [];


    foreach ($platforms as $platform) {

        $category_ids = get_term_meta(
            $platform->term_id,
            '_isago_ai_tool_categories',
            true
        );


        if (!is_array($category_ids)) {
            continue;
        }


        $category_ids = array_map(
            'absint',
            $category_ids
        );


        if (
            in_array(
                $term_id,
                $category_ids,
                true
            )
        ) {

            $connected[] = $platform;

        }

    }


    return $connected;

}


/*=========================================================
COUNT AI ToolS CONNECTED TO CATEGORY
=========================================================*/

function isago_get_ai_tool_category_platform_count($term_id)
{

    return count(
        isago_get_ai_tool_category_platforms(
            $term_id
        )
    );

}


/*=========================================================
GET CATEGORY ICON
=========================================================*/

function isago_get_ai_tool_category_icon($term_id)
{

    $term_id = absint($term_id);


    if (!$term_id) {
        return '';
    }


    return sanitize_text_field(
        get_term_meta(
            $term_id,
            '_isago_ai_tool_category_icon',
            true
        )
    );

}


/*=========================================================
GET CATEGORY URL
=========================================================*/

function isago_get_ai_tool_category_url($term_id)
{

    $term_id = absint($term_id);


    if (!$term_id) {
        return '';
    }


    $term = get_term(
        $term_id,
        'ai_tool_category'
    );


    if (
        !$term ||
        is_wp_error($term)
    ) {
        return '';
    }


    $url = get_term_link(
        $term,
        'ai_tool_category'
    );


    if (is_wp_error($url)) {
        return '';
    }


    return $url;

}




/*=========================================================
GET AI TOOL CATEGORY IDS ASSIGNED TO PLATFORM
=========================================================*/

function isago_get_ai_platform_tool_category_ids($term_id)
{

    $term_id = absint($term_id);


    if (!$term_id) {
        return [];
    }


    $category_ids = get_term_meta(
        $term_id,
        '_isago_ai_tool_categories',
        true
    );


    if (!is_array($category_ids)) {
        return [];
    }


    $category_ids = array_map(
        'absint',
        $category_ids
    );


    $category_ids = array_values(
        array_unique(
            array_filter(
                $category_ids
            )
        )
    );


    return $category_ids;

}


/*=========================================================
GET AI TOOL CATEGORIES ASSIGNED TO PLATFORM
=========================================================*/

function isago_get_ai_platform_tool_categories($term_id)
{

    $category_ids =
        isago_get_ai_platform_tool_category_ids(
            $term_id
        );


    if (empty($category_ids)) {
        return [];
    }


    $categories = get_terms([

        'taxonomy' =>
            'ai_tool_category',

        'include' =>
            $category_ids,

        'hide_empty' =>
            false,

        'orderby' =>
            'include',

    ]);


    if (is_wp_error($categories)) {
        return [];
    }


    return $categories;

}


/*=========================================================
GET AI TOOL CATEGORY NAMES FOR PLATFORM
=========================================================*/

function isago_get_ai_platform_tool_category_names($term_id)
{

    $categories =
        isago_get_ai_platform_tool_categories(
            $term_id
        );


    if (empty($categories)) {
        return [];
    }


    $names = [];


    foreach ($categories as $category) {

        $names[] = $category->name;

    }


    return $names;

}