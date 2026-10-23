<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * ---------------------------------------------------------
 * ISAGO MEDIA MANAGER
 * ---------------------------------------------------------
 *
 * Central functions for creating and managing Isago media
 * records and their references.
 *
 * Supported sources:
 *
 *     wordpress
 *     imagekit
 *
 * Supported media types:
 *
 *     image
 *     video
 */


/**
 * ---------------------------------------------------------
 * CREATE MEDIA RECORD
 * ---------------------------------------------------------
 *
 * Creates a media record and returns its ID.
 *
 * @param array $data
 *
 * @return int|WP_Error
 */
function isago_media_create($data = [])
{
    global $wpdb;

    $table =
        isago_media_table();

    $source =
        sanitize_key(
            $data['source'] ?? 'wordpress'
        );

    $allowed_sources = [
        'wordpress',
        'imagekit',
    ];

    if (
        !in_array(
            $source,
            $allowed_sources,
            true
        )
    ) {

        return new WP_Error(
            'isago_media_invalid_source',
            'Invalid media source.'
        );

    }


    $media_type =
        sanitize_key(
            $data['media_type'] ?? 'image'
        );

    $allowed_types = [
        'image',
        'video',
    ];

    if (
        !in_array(
            $media_type,
            $allowed_types,
            true
        )
    ) {

        return new WP_Error(
            'isago_media_invalid_type',
            'Invalid media type.'
        );

    }


    $now =
        current_time(
            'mysql',
            true
        );


    $inserted =
        $wpdb->insert(

            $table,

            [

                'source' =>
                    $source,

                'media_type' =>
                    $media_type,

                'mime_type' =>
                    sanitize_text_field(
                        $data['mime_type'] ?? ''
                    ),

                'attachment_id' =>
                    absint(
                        $data['attachment_id'] ?? 0
                    ),

                'account_id' =>
                    sanitize_key(
                        $data['account_id'] ?? ''
                    ),

                'file_id' =>
                    sanitize_text_field(
                        $data['file_id'] ?? ''
                    ),

                'file_path' =>
                    sanitize_textarea_field(
                        $data['file_path'] ?? ''
                    ),

                'url' =>
                    esc_url_raw(
                        $data['url'] ?? ''
                    ),

                'thumbnail_url' =>
                    esc_url_raw(
                        $data['thumbnail_url'] ?? ''
                    ),

                'width' =>
                    absint(
                        $data['width'] ?? 0
                    ),

                'height' =>
                    absint(
                        $data['height'] ?? 0
                    ),

                'file_size' =>
                    absint(
                        $data['file_size'] ?? 0
                    ),

                'original_name' =>
                    sanitize_text_field(
                        $data['original_name'] ?? ''
                    ),

                'created_by' =>
                    get_current_user_id(),

                'created_at' =>
                    $now,

                'updated_at' =>
                    $now,

            ],

            [

                '%s',
                '%s',
                '%s',
                '%d',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%d',
                '%d',
                '%d',
                '%s',
                '%d',
                '%s',
                '%s',

            ]

        );


    if (
        $inserted === false
    ) {

        return new WP_Error(
            'isago_media_create_failed',
            'Could not create the media record.'
        );

    }


    return (int) $wpdb->insert_id;
}


/**
 * ---------------------------------------------------------
 * GET MEDIA RECORD
 * ---------------------------------------------------------
 *
 * @param int $media_id
 *
 * @return object|null
 */
function isago_media_get($media_id)
{
    global $wpdb;

    $media_id =
        absint($media_id);

    if (!$media_id) {
        return null;
    }


    $table =
        isago_media_table();


    return $wpdb->get_row(

        $wpdb->prepare(

            "
            SELECT *
            FROM {$table}
            WHERE id = %d
            LIMIT 1
            ",

            $media_id

        )

    );
}


/**
 * ---------------------------------------------------------
 * FIND IMAGEKIT MEDIA
 * ---------------------------------------------------------
 *
 * Finds an ImageKit record by account + file ID.
 *
 * This prevents duplicate records for the same
 * ImageKit asset.
 *
 * @param string $account_id
 * @param string $file_id
 *
 * @return object|null
 */
function isago_media_find_imagekit(
    $account_id,
    $file_id
) {

    global $wpdb;

    $account_id =
        sanitize_key(
            $account_id
        );

    $file_id =
        sanitize_text_field(
            $file_id
        );

    if (
        !$account_id
        ||
        !$file_id
    ) {

        return null;
    }


    $table =
        isago_media_table();


    return $wpdb->get_row(

        $wpdb->prepare(

            "
            SELECT *
            FROM {$table}
            WHERE source = 'imagekit'
            AND account_id = %s
            AND file_id = %s
            LIMIT 1
            ",

            $account_id,
            $file_id

        )

    );
}


/**
 * ---------------------------------------------------------
 * UPDATE MEDIA RECORD
 * ---------------------------------------------------------
 *
 * @param int   $media_id
 * @param array $data
 *
 * @return bool|WP_Error
 */
function isago_media_update(
    $media_id,
    $data = []
) {

    global $wpdb;
$table = isago_media_table();
    $media_id =
        absint($media_id);

    if (!$media_id) {

        return new WP_Error(
            'isago_media_invalid_id',
            'Invalid media ID.'
        );

    }


    $existing =
        isago_media_get(
            $media_id
        );

    if (!$existing) {

        return new WP_Error(
            'isago_media_not_found',
            'Media record could not be found.'
        );

    }


    $update = [];


    if (
        array_key_exists(
            'source',
            $data
        )
    ) {

        $update['source'] =
            sanitize_key(
                $data['source']
            );

    }


    if (
        array_key_exists(
            'media_type',
            $data
        )
    ) {

        $update['media_type'] =
            sanitize_key(
                $data['media_type']
            );

    }


    if (
        array_key_exists(
            'mime_type',
            $data
        )
    ) {

        $update['mime_type'] =
            sanitize_text_field(
                $data['mime_type']
            );

    }


    if (
        array_key_exists(
            'attachment_id',
            $data
        )
    ) {

        $update['attachment_id'] =
            absint(
                $data['attachment_id']
            );

    }


    if (
        array_key_exists(
            'account_id',
            $data
        )
    ) {

        $update['account_id'] =
            sanitize_key(
                $data['account_id']
            );

    }


    if (
        array_key_exists(
            'file_id',
            $data
        )
    ) {

        $update['file_id'] =
            sanitize_text_field(
                $data['file_id']
            );

    }


    if (
        array_key_exists(
            'file_path',
            $data
        )
    ) {

        $update['file_path'] =
            sanitize_textarea_field(
                $data['file_path']
            );

    }


    if (
        array_key_exists(
            'url',
            $data
        )
    ) {

        $update['url'] =
            esc_url_raw(
                $data['url']
            );

    }


    if (
        array_key_exists(
            'thumbnail_url',
            $data
        )
    ) {

        $update['thumbnail_url'] =
            esc_url_raw(
                $data['thumbnail_url']
            );

    }


    if (
        array_key_exists(
            'width',
            $data
        )
    ) {

        $update['width'] =
            absint(
                $data['width']
            );

    }


    if (
        array_key_exists(
            'height',
            $data
        )
    ) {

        $update['height'] =
            absint(
                $data['height']
            );

    }


    if (
        array_key_exists(
            'file_size',
            $data
        )
    ) {

        $update['file_size'] =
            absint(
                $data['file_size']
            );

    }


    if (
        array_key_exists(
            'original_name',
            $data
        )
    ) {

        $update['original_name'] =
            sanitize_text_field(
                $data['original_name']
            );

    }


    if (empty($update)) {
        return true;
    }


    $update['updated_at'] =
        current_time(
            'mysql',
            true
        );


    $result =
        $wpdb->update(

            $table,

            $update,

            [
                'id' => $media_id,
            ]

        );


    if ($result === false) {

        return new WP_Error(
            'isago_media_update_failed',
            'Could not update the media record.'
        );

    }


    return true;
}


/**
 * ---------------------------------------------------------
 * ADD MEDIA REFERENCE
 * ---------------------------------------------------------
 *
 * Associates media with an Isago object.
 *
 * Example:
 *
 *     theme / 0 / about_hero_image
 *
 *     post / 123 / featured_image
 *
 *     term / 45 / ai_platform_image
 *
 * @param int    $media_id
 * @param string $object_type
 * @param int    $object_id
 * @param string $field_key
 *
 * @return int|WP_Error
 */
function isago_media_add_reference(
    $media_id,
    $object_type,
    $object_id,
    $field_key
) {

    global $wpdb;

    $media_id =
        absint($media_id);

    $object_type =
        sanitize_key(
            $object_type
        );

    $object_id =
        absint($object_id);

    $field_key =
        sanitize_key(
            $field_key
        );


    if (
        !$media_id
        ||
        !$object_type
        ||
        !$field_key
    ) {

        return new WP_Error(
            'isago_media_invalid_reference',
            'Invalid media reference.'
        );

    }


    /*
     * Make sure the media actually exists.
     */
    if (
        !isago_media_get(
            $media_id
        )
    ) {

        return new WP_Error(
            'isago_media_missing',
            'The referenced media does not exist.'
        );

    }


    $table =
        isago_media_refs_table();


    $existing =
        $wpdb->get_var(

            $wpdb->prepare(

                "
                SELECT id
                FROM {$table}
                WHERE media_id = %d
                AND object_type = %s
                AND object_id = %d
                AND field_key = %s
                LIMIT 1
                ",

                $media_id,
                $object_type,
                $object_id,
                $field_key

            )

        );


    /*
     * Reference already exists.
     */
    if ($existing) {

        return (int) $existing;

    }


    $now =
        current_time(
            'mysql',
            true
        );


    $inserted =
        $wpdb->insert(

            $table,

            [

                'media_id' =>
                    $media_id,

                'object_type' =>
                    $object_type,

                'object_id' =>
                    $object_id,

                'field_key' =>
                    $field_key,

                'created_at' =>
                    $now,

                'updated_at' =>
                    $now,

            ],

            [

                '%d',
                '%s',
                '%d',
                '%s',
                '%s',
                '%s',

            ]

        );


    if (
        $inserted === false
    ) {

        return new WP_Error(
            'isago_media_reference_failed',
            'Could not create the media reference.'
        );

    }


    return (int) $wpdb->insert_id;
}


/**
 * ---------------------------------------------------------
 * REMOVE MEDIA REFERENCE
 * ---------------------------------------------------------
 *
 * Removes one specific reference.
 *
 * @param int    $media_id
 * @param string $object_type
 * @param int    $object_id
 * @param string $field_key
 *
 * @return bool
 */
function isago_media_remove_reference(
    $media_id,
    $object_type,
    $object_id,
    $field_key
) {

    global $wpdb;

    $result =
        $wpdb->delete(

            isago_media_refs_table(),

            [

                'media_id' =>
                    absint($media_id),

                'object_type' =>
                    sanitize_key(
                        $object_type
                    ),

                'object_id' =>
                    absint($object_id),

                'field_key' =>
                    sanitize_key(
                        $field_key
                    ),

            ],

            [

                '%d',
                '%s',
                '%d',
                '%s',

            ]

        );


    return $result !== false;
}


/**
 * ---------------------------------------------------------
 * GET MEDIA REFERENCE COUNT
 * ---------------------------------------------------------
 *
 * @param int $media_id
 *
 * @return int
 */
function isago_media_reference_count(
    $media_id
) {

    global $wpdb;

    $media_id =
        absint($media_id);

    if (!$media_id) {
        return 0;
    }


    $table =
        isago_media_refs_table();


    return (int) $wpdb->get_var(

        $wpdb->prepare(

            "
            SELECT COUNT(*)
            FROM {$table}
            WHERE media_id = %d
            ",

            $media_id

        )

    );
}


/**
 * ---------------------------------------------------------
 * GET MEDIA REFERENCES
 * ---------------------------------------------------------
 *
 * @param int $media_id
 *
 * @return array
 */
function isago_media_get_references(
    $media_id
) {

    global $wpdb;

    $media_id =
        absint($media_id);

    if (!$media_id) {
        return [];
    }


    $table =
        isago_media_refs_table();


    return $wpdb->get_results(

        $wpdb->prepare(

            "
            SELECT *
            FROM {$table}
            WHERE media_id = %d
            ORDER BY id ASC
            ",

            $media_id

        )

    );
}


/**
 * Get all media references belonging to an object.
 *
 * @param string $object_type
 * @param int    $object_id
 *
 * @return array
 */
function isago_media_get_references_for_object(
    $object_type,
    $object_id
) {

    global $wpdb;

    $object_type = sanitize_key($object_type);
    $object_id   = absint($object_id);

    if (
        empty($object_type)
        ||
        empty($object_id)
    ) {
        return [];
    }

    $table =
        isago_media_refs_table();

    return $wpdb->get_results(
        $wpdb->prepare(
            "
            SELECT *
            FROM {$table}
            WHERE object_type = %s
            AND object_id = %d
            ORDER BY id ASC
            ",
            $object_type,
            $object_id
        )
    );
}

/**
 * ---------------------------------------------------------
 * REMOVE ALL REFERENCES FOR AN OBJECT
 * ---------------------------------------------------------
 *
 * Used when a post, term or theme media field is removed.
 *
 * @param string $object_type
 * @param int    $object_id
 * @param string $field_key
 *
 * @return bool
 */
function isago_media_remove_object_reference(
    $object_type,
    $object_id,
    $field_key
) {

    global $wpdb;

    $result =
        $wpdb->delete(

            isago_media_refs_table(),

            [

                'object_type' =>
                    sanitize_key(
                        $object_type
                    ),

                'object_id' =>
                    absint(
                        $object_id
                    ),

                'field_key' =>
                    sanitize_key(
                        $field_key
                    ),

            ],

            [

                '%s',
                '%d',
                '%s',

            ]

        );


    return $result !== false;
}