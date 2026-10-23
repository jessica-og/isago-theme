<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * ---------------------------------------------------------
 * ISAGO MEDIA RESOLVER
 * ---------------------------------------------------------
 *
 * Provides one consistent API for retrieving media.
 *
 * The caller does not need to know whether the media
 * originates from:
 *
 *     WordPress Media Library
 *     ImageKit
 */


/**
 * ---------------------------------------------------------
 * GET MEDIA
 * ---------------------------------------------------------
 *
 * Accepts an Isago media ID.
 *
 * @param int $media_id
 *
 * @return object|null
 */
function isago_get_media($media_id)
{
    return isago_media_get(
        absint($media_id)
    );
}


/**
 * ---------------------------------------------------------
 * GET MEDIA URL
 * ---------------------------------------------------------
 *
 * Returns the original/full URL for an Isago media record.
 *
 * @param int $media_id
 *
 * @return string
 */
function isago_get_media_url($media_id)
{
    $media =
        isago_get_media(
            $media_id
        );

    if (!$media) {
        return '';
    }


    /*
     * ImageKit
     */
    if (
        $media->source === 'imagekit'
    ) {

        return !empty($media->url)
            ? esc_url($media->url)
            : '';
    }


    /*
     * WordPress
     */
    if (
        $media->source === 'wordpress'
    ) {

        return isago_media_wordpress_url(
            $media
        );

    }

/*
 * External video
 */



if (
    $media->source === 'external'
) {

    return !empty($media->url)
        ? esc_url($media->url)
        : '';

}


    return '';
}


/**
 * ---------------------------------------------------------
 * GET IMAGE URL
 * ---------------------------------------------------------
 *
 * For images, returns the requested WordPress image size
 * when the source is WordPress.
 *
 * ImageKit assets currently return their stored URL.
 *
 * Later we can add ImageKit transformation parameters
 * here without changing the calling code.
 *
 * @param int    $media_id
 * @param string $size
 *
 * @return string
 */
function isago_get_media_image_url(
    $media_id,
    $size = 'full'
) {

    $media =
        isago_get_media(
            $media_id
        );

    if (!$media) {
        return '';
    }


    /*
     * ImageKit
     */
    if (
        $media->source === 'imagekit'
    ) {

        return !empty($media->url)
            ? esc_url($media->url)
            : '';
    }


    /*
     * WordPress
     */
    if (
        $media->source === 'wordpress'
    ) {

        return isago_media_wordpress_image_url(
            $media,
            $size
        );

    }


    return '';
}


/**
 * ---------------------------------------------------------
 * GET THUMBNAIL URL
 * ---------------------------------------------------------
 *
 * @param int $media_id
 *
 * @return string
 */
function isago_get_media_thumbnail_url(
    $media_id
) {

    $media =
        isago_get_media(
            $media_id
        );

    if (!$media) {
        return '';
    }


    /*
     * ImageKit
     */
    if (
        $media->source === 'imagekit'
    ) {

        /*
         * ImageKit upload responses can provide a
         * thumbnail URL.
         */
        if (
            !empty(
                $media->thumbnail_url
            )
        ) {

            return esc_url(
                $media->thumbnail_url
            );

        }


        /*
         * Fallback to the original URL.
         */
        return !empty($media->url)
            ? esc_url($media->url)
            : '';

    }


    /*
     * WordPress
     */
    if (
        $media->source === 'wordpress'
    ) {

        return isago_media_wordpress_image_url(
            $media,
            'thumbnail'
        );

    }


    return '';
}


/**
 * ---------------------------------------------------------
 * GET MEDIA HTML
 * ---------------------------------------------------------
 *
 * Returns an image element for an image media record.
 *
 * This keeps frontend output centralized.
 *
 * @param int    $media_id
 * @param string $size
 * @param string $alt
 * @param array  $attributes
 *
 * @return string
 */
function isago_get_media_image(
    $media_id,
    $size = 'full',
    $alt = '',
    $attributes = []
) {

    $media =
        isago_get_media(
            $media_id
        );

    if (!$media) {
        return '';
    }


    /*
     * WordPress media can use the normal WordPress
     * image renderer.
     */
    if (
        $media->source === 'wordpress'
    ) {

        $attachment_id =
            absint(
                $media->attachment_id
            );

        if (!$attachment_id) {
            return '';
        }


        $attributes['alt'] =
            $alt;


        return wp_get_attachment_image(

            $attachment_id,

            $size,

            false,

            $attributes

        );

    }


    /*
     * ImageKit media.
     */
    if (
        $media->source === 'imagekit'
    ) {

        $url =
            isago_get_media_image_url(
                $media_id,
                $size
            );

        if (!$url) {
            return '';
        }


        $attributes['src'] =
            $url;

        $attributes['alt'] =
            $alt;


        $html_attributes = '';


        foreach (
            $attributes
            as $attribute =>
            $value
        ) {

            /*
             * Do not allow arbitrary HTML attributes
             * to contain unescaped content.
             */
            $attribute =
                sanitize_key(
                    $attribute
                );


            if (
                $attribute === ''
            ) {

                continue;

            }


            $html_attributes .=

                ' '
                .
                esc_attr(
                    $attribute
                )
                .
                '="'
                .
                esc_attr(
                    $value
                )
                .
                '"';

        }


        return '<img'
            .
            $html_attributes
            .
            '>';

    }


    return '';
}


/**
 * ---------------------------------------------------------
 * GET VIDEO URL
 * ---------------------------------------------------------
 *
 * @param int $media_id
 *
 * @return string
 */
function isago_get_media_video_url(
    $media_id
) {

    $media =
        isago_get_media(
            $media_id
        );

    if (!$media) {
        return '';
    }


    /*
     * ImageKit
     */
    if (
        $media->source === 'imagekit'
    ) {

        return !empty($media->url)
            ? esc_url($media->url)
            : '';

    }


    /*
     * WordPress
     */
    if (
        $media->source === 'wordpress'
        &&
        !empty($media->attachment_id)
    ) {

        $url =
            wp_get_attachment_url(
                absint(
                    $media->attachment_id
                )
            );


        return $url
            ? esc_url($url)
            : '';

    }

/*
 * External video
 */

if (
    $media->source === 'external'
    &&
    $media->media_type === 'video'
) {

    return !empty($media->url)
        ? esc_url($media->url)
        : '';

}


    return '';
}


/**
 * ---------------------------------------------------------
 * CHECK MEDIA TYPE
 * ---------------------------------------------------------
 *
 * @param int    $media_id
 * @param string $type
 *
 * @return bool
 */
function isago_media_is_type(
    $media_id,
    $type
) {

    $media =
        isago_get_media(
            $media_id
        );

    if (!$media) {
        return false;
    }


    return (
        $media->media_type ===
        sanitize_key($type)
    );
}