<?php

if (!defined('ABSPATH')) {
    exit;
}



function isago_get_admin_video_thumbnail(
    $post_id
) {

    $post_id =
        absint(
            $post_id
        );


    if (!$post_id) {

        return '';

    }


    /*
    |--------------------------------------------------------------------------
    | COMMON
    |--------------------------------------------------------------------------
    */

    $title =
        get_the_title(
            $post_id
        );


    $alt =
        esc_attr(
            $title
        );


    $thumbnail_style =
        '
            width:80px;
            height:45px;
            object-fit:cover;
            display:block;
            border-radius:4px;
        ';


    /*
    |--------------------------------------------------------------------------
    | 1. WORDPRESS FEATURED IMAGE
    |--------------------------------------------------------------------------
    |
    | Keep featured image as the highest-priority thumbnail.
    |
    */

    if (
        has_post_thumbnail(
            $post_id
        )
    ) {

        return get_the_post_thumbnail(
            $post_id,
            [80, 45],
            [
                'loading' =>
                    'lazy',

                'alt' =>
                    $alt,

                'width' =>
                    80,

                'height' =>
                    45,

                'style' =>
                    $thumbnail_style,
            ]
        );

    }


    /*
    |--------------------------------------------------------------------------
    | 2. IMAGEKIT IMAGE
    |--------------------------------------------------------------------------
    |
    | Central media field:
    |
    |     post / image
    |
    */

    $image_id =
        isago_media_get_field(
            'post',
            $post_id,
            'image'
        );


    if (
        $image_id
    ) {

        $image_url =
            isago_media_get_field_image_url(
                'post',
                $post_id,
                'image',
                'medium'
            );


        if (
            $image_url
        ) {

            return sprintf(

                '<img
                    src="%s"
                    alt="%s"
                    loading="lazy"
                    width="80"
                    height="45"
                    style="%s"
                >',

                esc_url(
                    $image_url
                ),

                $alt,

                esc_attr(
                    $thumbnail_style
                )

            );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | 3. VIDEO / SOCIAL URL
    |--------------------------------------------------------------------------
    |
    | Use the central helper so the admin column supports:
    |
    | - YouTube
    | - Instagram
    | - Pinterest
    | - TikTok
    | - Vimeo
    |
    | The helper also supports the legacy URL fields.
    |
    */

    $video_url =
        '';


    if (
        function_exists(
            'isago_get_video_url'
        )
    ) {

        $video_url =
            isago_get_video_url(
                $post_id
            );

    }


    /*
    |--------------------------------------------------------------------------
    | FALLBACK: CURRENT VIDEO URL FIELD
    |--------------------------------------------------------------------------
    */

    if (
        empty(
            $video_url
        )
    ) {

        $video_url =
            get_post_meta(
                $post_id,
                '_isago_video_source_url',
                true
            );

    }


    /*
    |--------------------------------------------------------------------------
    | FALLBACK: IMAGE + VIDEO URL FIELD
    |--------------------------------------------------------------------------
    */

    if (
        empty(
            $video_url
        )
    ) {

        $video_url =
            get_post_meta(
                $post_id,
                '_isago_image_video_source_url',
                true
            );

    }


    /*
    |--------------------------------------------------------------------------
    | FALLBACK: LEGACY YOUTUBE FIELD
    |--------------------------------------------------------------------------
    */

    if (
        empty(
            $video_url
        )
    ) {

        $video_url =
            get_post_meta(
                $post_id,
                '_isago_youtube_url',
                true
            );

    }


    /*
    |--------------------------------------------------------------------------
    | FALLBACK: LEGACY IMAGE + YOUTUBE FIELD
    |--------------------------------------------------------------------------
    */

    if (
        empty(
            $video_url
        )
    ) {

        $video_url =
            get_post_meta(
                $post_id,
                '_isago_image_video_url',
                true
            );

    }


    $video_url =
        is_string(
            $video_url
        )
            ? trim(
                $video_url
            )
            : '';


    /*
    |--------------------------------------------------------------------------
    | 3A. YOUTUBE
    |--------------------------------------------------------------------------
    */

    if (
        !empty(
            $video_url
        )
    ) {

        $youtube_id =
            isago_get_youtube_id(
                $video_url
            );


        if (
            !empty(
                $youtube_id
            )
        ) {

            return sprintf(

                '<img
                    src="https://img.youtube.com/vi/%s/hqdefault.jpg"
                    alt="%s"
                    loading="lazy"
                    width="80"
                    height="45"
                    style="%s"
                >',

                esc_attr(
                    $youtube_id
                ),

                $alt,

                esc_attr(
                    $thumbnail_style
                )

            );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | 3B. OTHER SOCIAL PLATFORMS
    |--------------------------------------------------------------------------
    |
    | Try the shared social thumbnail helper.
    |
    */

    if (
        !empty(
            $video_url
        )
        &&
        function_exists(
            'isago_get_social_video_thumbnail_url'
        )
    ) {

        $social_thumbnail_url =
            isago_get_social_video_thumbnail_url(
                $video_url
            );


        if (
            !empty(
                $social_thumbnail_url
            )
        ) {

            return sprintf(

                '<img
                    src="%s"
                    alt="%s"
                    loading="lazy"
                    width="80"
                    height="45"
                    style="%s"
                    onerror="this.onerror=null;this.src=\'%s\';"
                >',

                esc_url(
                    $social_thumbnail_url
                ),

                $alt,

                esc_attr(
                    $thumbnail_style
                ),

                esc_url(
                    get_template_directory_uri()
                    . '/assets/images/placeholder.png'
                )

            );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | 4. PLACEHOLDER
    |--------------------------------------------------------------------------
    */

    return sprintf(

        '<img
            src="%s"
            alt="%s"
            loading="lazy"
            width="80"
            height="45"
            style="%s"
        >',

        esc_url(
            get_template_directory_uri()
            . '/assets/images/placeholder.png'
        ),

        $alt,

        esc_attr(
            $thumbnail_style
        )

    );

}

/*
|--------------------------------------------------------------------------
| Admin Columns
|--------------------------------------------------------------------------
*/

add_filter(

    'manage_isago_video_posts_columns',

    function ($columns) {

        return [

            'cb'        => $columns['cb'],

            'thumbnail' => 'Thumbnail',

            'title'     => 'Title',

            'category'  => 'Category',

            'duration'  => 'Duration',

            'date'      => 'Date'

        ];

    }

);


/*
|--------------------------------------------------------------------------
| Admin Column Content
|--------------------------------------------------------------------------
*/

add_action(

    'manage_isago_video_posts_custom_column',

    function ($column, $post_id) {

        switch ($column) {


            /*
            |--------------------------------------------------------------------------
            | Thumbnail
            |--------------------------------------------------------------------------
            */

            case 'thumbnail':

                echo isago_get_admin_video_thumbnail(
                    $post_id
                );

                break;


            /*
            |--------------------------------------------------------------------------
            | Category
            |--------------------------------------------------------------------------
            */

            case 'category':

                echo get_the_term_list(

                    $post_id,

                    'category',

                    '',

                    ', '

                );

                break;


            /*
            |--------------------------------------------------------------------------
            | Duration
            |--------------------------------------------------------------------------
            */

            case 'duration':

                echo esc_html(

                    get_post_meta(

                        $post_id,

                        '_isago_video_duration',

                        true

                    ) ?: '--'

                );

                break;

        }

    },

    10,

    2

);