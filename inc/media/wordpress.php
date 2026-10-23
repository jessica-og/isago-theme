<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * ---------------------------------------------------------
 * ISAGO WORDPRESS MEDIA ADAPTER
 * ---------------------------------------------------------
 *
 * Connects existing WordPress Media Library attachments
 * to the Isago Media System.
 *
 * IMPORTANT:
 *
 * This does NOT move files.
 *
 * Existing WordPress uploads remain exactly where they are.
 *
 * We simply create an Isago media record that points to
 * the existing WordPress attachment.
 */


/**
 * ---------------------------------------------------------
 * GET / CREATE WORDPRESS MEDIA RECORD
 * ---------------------------------------------------------
 *
 * Given a WordPress attachment ID, return its corresponding
 * Isago media record.
 *
 * If one does not exist yet, create it.
 *
 * @param int $attachment_id
 *
 * @return int|WP_Error
 */
function isago_media_from_attachment(
    $attachment_id
) {

    $attachment_id =
        absint(
            $attachment_id
        );


    if (!$attachment_id) {

        return new WP_Error(
            'isago_media_invalid_attachment',
            'Invalid WordPress attachment ID.'
        );

    }


    /*
     * Make sure this is actually an attachment.
     */
    if (
        get_post_type(
            $attachment_id
        ) !== 'attachment'
    ) {

        return new WP_Error(
            'isago_media_not_attachment',
            'The supplied ID is not a WordPress attachment.'
        );

    }


    /*
     * Check whether we already created an Isago media
     * record for this attachment.
     */
    global $wpdb;

    $table =
        isago_media_table();


    $existing_id =
        $wpdb->get_var(

            $wpdb->prepare(

                "
                SELECT id
                FROM {$table}
                WHERE source = 'wordpress'
                AND attachment_id = %d
                LIMIT 1
                ",

                $attachment_id

            )

        );


    if ($existing_id) {

        return (int) $existing_id;

    }


    /*
     * Get attachment information.
     */
    $mime_type =
        get_post_mime_type(
            $attachment_id
        );


    $media_type = 'image';


    if (
        $mime_type
        &&
        strpos(
            $mime_type,
            'video/'
        ) === 0
    ) {

        $media_type = 'video';

    }


    /*
     * WordPress attachment URL.
     */
    $url =
        wp_get_attachment_url(
            $attachment_id
        );


    /*
     * Image dimensions.
     */
    $width  = 0;
    $height = 0;


    if (
        $media_type === 'image'
    ) {

        $metadata =
            wp_get_attachment_metadata(
                $attachment_id
            );


        if (
            is_array($metadata)
        ) {

            $width =
                absint(
                    $metadata['width'] ?? 0
                );

            $height =
                absint(
                    $metadata['height'] ?? 0
                );

        }

    }


    /*
     * File size.
     */
    $file_size = 0;

    $file_path =
        get_attached_file(
            $attachment_id
        );


    if (
        $file_path
        &&
        file_exists($file_path)
    ) {

        $file_size =
            filesize(
                $file_path
            );

    }


    /*
     * Original filename.
     */
    $original_name =
        get_the_title(
            $attachment_id
        );


    /*
     * Create the central Isago media record.
     */
    return isago_media_create(

        [

            'source' =>
                'wordpress',

            'media_type' =>
                $media_type,

            'mime_type' =>
                $mime_type ?: '',

            'attachment_id' =>
                $attachment_id,

            'account_id' =>
                '',

            'file_id' =>
                '',

            'file_path' =>
                $file_path ?: '',

            'url' =>
                $url ?: '',

            'thumbnail_url' =>
                $media_type === 'image'
                    ? (
                        wp_get_attachment_image_url(
                            $attachment_id,
                            'thumbnail'
                        ) ?: ''
                    )
                    : '',

            'width' =>
                $width,

            'height' =>
                $height,

            'file_size' =>
                $file_size,

            'original_name' =>
                $original_name ?: '',

        ]

    );
}


/**
 * ---------------------------------------------------------
 * FIND MEDIA BY ATTACHMENT ID
 * ---------------------------------------------------------
 *
 * Unlike isago_media_from_attachment(), this function
 * does NOT create anything.
 *
 * @param int $attachment_id
 *
 * @return object|null
 */
function isago_media_find_by_attachment(
    $attachment_id
) {

    global $wpdb;

    $attachment_id =
        absint(
            $attachment_id
        );


    if (!$attachment_id) {
        return null;
    }


    $table =
        isago_media_table();


    return $wpdb->get_row(

        $wpdb->prepare(

            "
            SELECT *
            FROM {$table}
            WHERE source = 'wordpress'
            AND attachment_id = %d
            LIMIT 1
            ",

            $attachment_id

        )

    );
}


/**
 * ---------------------------------------------------------
 * GET WORDPRESS MEDIA URL
 * ---------------------------------------------------------
 *
 * Returns the normal WordPress attachment URL.
 *
 * This gives the future resolver a consistent interface.
 *
 * @param object $media
 *
 * @return string
 */
function isago_media_wordpress_url(
    $media
) {

    if (
        empty($media)
        ||
        empty($media->attachment_id)
    ) {

        return '';

    }


    $url =
        wp_get_attachment_url(
            absint(
                $media->attachment_id
            )
        );


    return $url
        ? esc_url(
            $url
        )
        : '';
}


/**
 * ---------------------------------------------------------
 * GET WORDPRESS IMAGE URL
 * ---------------------------------------------------------
 *
 * @param object $media
 * @param string $size
 *
 * @return string
 */
function isago_media_wordpress_image_url(
    $media,
    $size = 'full'
) {

    if (
        empty($media)
        ||
        empty($media->attachment_id)
    ) {

        return '';

    }


    $url =
        wp_get_attachment_image_url(

            absint(
                $media->attachment_id
            ),

            $size

        );


    return $url
        ? esc_url(
            $url
        )
        : '';
}






/**
 * Clean up Isago media when a WordPress post
 * is permanently deleted.
 *
 * @param int $post_id
 */
function isago_media_before_delete_post(
    $post_id
) {

    $post_id = absint($post_id);

    if (!$post_id) {
        return;
    }

    isago_media_remove_object_and_cleanup(
        'post',
        $post_id
    );
}

add_action(
    'before_delete_post',
    'isago_media_before_delete_post'
);






/**
 * ---------------------------------------------------------
 * CLEAN UP MEDIA WHEN A TAXONOMY TERM IS DELETED
 * ---------------------------------------------------------
 *
 * This covers:
 *
 *     AI Platform
 *     Other Isago taxonomies
 *     Any future taxonomy using Isago media
 *
 * @param int    $term_id
 * @param int    $tt_id
 * @param string $taxonomy
 */
function isago_media_before_delete_term(
    $term_id,
    $tt_id,
    $taxonomy
) {

    $term_id =
        absint(
            $term_id
        );

    if (!$term_id) {
        return;
    }

    isago_media_remove_object_and_cleanup(
        'term',
        $term_id
    );
}

add_action(
    'delete_term',
    'isago_media_before_delete_term',
    10,
    3
);







/**
 * ---------------------------------------------------------
 * CLEAN UP ISAGO MEDIA WHEN A WORDPRESS ATTACHMENT
 * IS DELETED
 * ---------------------------------------------------------
 *
 * This does NOT delete the physical WordPress file.
 *
 * WordPress handles deletion of the attachment itself.
 *
 * We only remove the corresponding Isago media record
 * when nothing else references it.
 *
 * @param int $attachment_id
 */
function isago_media_before_delete_attachment(
    $attachment_id
) {

    $attachment_id =
        absint(
            $attachment_id
        );

    if (!$attachment_id) {
        return;
    }

    $media =
        isago_media_find_by_attachment(
            $attachment_id
        );

    if (!$media) {
        return;
    }

    /*
     * Remove the central Isago media record only
     * when it is no longer referenced.
     *
     * WordPress itself is responsible for deleting
     * the actual WordPress upload.
     */
    isago_media_delete_if_unused(
        $media->id
    );
}

add_action(
    'delete_attachment',
    'isago_media_before_delete_attachment'
);
