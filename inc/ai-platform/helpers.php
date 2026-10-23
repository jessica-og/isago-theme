<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
GET POST AI TOOLS
=========================================================*/

function isago_get_post_ai_platforms(
    $post_id = null
) {

    $post_id =
        $post_id
        ? absint($post_id)
        : get_the_ID();


    if (!$post_id) {

        return [];

    }


    $terms =
        get_the_terms(
            $post_id,
            'ai_platform'
        );


    if (
        empty($terms)
        ||
        is_wp_error($terms)
    ) {

        return [];

    }


    return $terms;

}


/*=========================================================
GET PRIMARY AI TOOL
=========================================================*/

function isago_get_primary_ai_platform(
    $post_id = null
) {

    $platforms =
        isago_get_post_ai_platforms(
            $post_id
        );


    if (empty($platforms)) {

        return null;

    }


    return $platforms[0];

}


/*=========================================================
AI TOOL URL
=========================================================*/

function isago_get_ai_platform_url(
    $term
) {

    if (
        !$term
        ||
        is_wp_error($term)
    ) {

        return '';

    }


    $url =
        get_term_link(
            $term,
            'ai_platform'
        );


    if (is_wp_error($url)) {

        return '';

    }


    return $url;

}


/*=========================================================
AI TOOL LABEL
=========================================================*/

function isago_get_ai_platform_label(
    $term
) {

    if (
        !$term
        ||
        is_wp_error($term)
    ) {

        return '';

    }


    return $term->name;

}


/*=========================================================
AI TOOL CTA
=========================================================*/

function isago_get_ai_platform_cta(
    $term,
    $context = 'general'
) {

    if (
        !$term
        ||
        is_wp_error($term)
    ) {

        return '';

    }


    $name =
        $term->name;


    switch ($context) {

        case 'prompt':

            return sprintf(
                'Browse %s prompts',
                $name
            );


        case 'tool':

            return sprintf(
                'Explore %s tools & guides',
                $name
            );


        default:

            return sprintf(
                'Explore %s',
                $name
            );

    }

}


/*=========================================================
GET AI TOOL META
=========================================================*/

function isago_get_ai_platform_meta(
    $term_id,
    $key,
    $default = ''
) {

    $term_id =
        absint($term_id);


    if (
        !$term_id
        ||
        !$key
    ) {

        return $default;

    }


    $value =
        get_term_meta(
            $term_id,
            '_isago_ai_platform_' . $key,
            true
        );


    return $value !== ''
        ? $value
        : $default;

}


/*=========================================================
PLATFORM IMAGE
=========================================================*/

function isago_get_ai_platform_image(
    $term_id,
    $size = 'medium'
) {

    $image_id =
        absint(
            isago_get_ai_platform_meta(
                $term_id,
                'image'
            )
        );


    if (!$image_id) {

        return '';

    }


    $url =
        wp_get_attachment_image_url(
            $image_id,
            $size
        );


    return $url ?: '';

}


/*=========================================================
PLATFORM WEBSITE
=========================================================*/

function isago_get_ai_platform_website(
    $term_id
) {

    return esc_url(
        isago_get_ai_platform_meta(
            $term_id,
            'website'
        )
    );

}


/*=========================================================
PLATFORM DESCRIPTION
=========================================================*/

function isago_get_ai_platform_description(
    $term_id
) {

    $description =
        isago_get_ai_platform_meta(
            $term_id,
            'short_description'
        );


    if (!$description) {

        $description =
            term_description(
                $term_id,
                'ai_platform'
            );

    }


    return wp_kses_post(
        $description
    );

}


/*=========================================================
PLATFORM CATEGORY
=========================================================*/

function isago_get_ai_platform_category(
    $term_id
) {

    $slug =
        isago_get_ai_platform_meta(
            $term_id,
            'category'
        );


    $categories =
        function_exists(
            'isago_get_ai_platform_categories'
        )
        ? isago_get_ai_platform_categories()
        : [];


    if (
        $slug
        &&
        isset(
            $categories[$slug]
        )
    ) {

        return $categories[$slug];

    }


    return '';

}


/*=========================================================
PLATFORM LOGO HTML
=========================================================*/

function isago_get_ai_platform_logo(
    $term_id,
    $size = 'medium'
) {

    $image_id =
        absint(
            isago_get_ai_platform_meta(
                $term_id,
                'image'
            )
        );


    if (!$image_id) {

        return '';

    }


    return wp_get_attachment_image(
        $image_id,
        $size,
        false,
        [

            'class' =>
                'isago-platform-logo-image',

            'alt' =>
                get_term_field(
                    'name',
                    $term_id,
                    'ai_platform'
                )

        ]
    );

}




