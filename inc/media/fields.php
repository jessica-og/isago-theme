<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * ---------------------------------------------------------
 * ISAGO MEDIA FIELD API
 * ---------------------------------------------------------
 *
 * Connects Isago fields to the central media system.
 *
 * Example:
 *
 *     about_hero_image
 *     ai_platform_image
 *     ai_prompt_image
 *     video_thumbnail
 *
 * A field points to an Isago media record.
 *
 * The actual media can be:
 *
 *     WordPress Media Library
 *     ImageKit
 */


/**
 * ---------------------------------------------------------
 * SET FIELD MEDIA
 * ---------------------------------------------------------
 *
 * Attach a media record to an Isago field.
 *
 * @param string $object_type
 * @param int    $object_id
 * @param string $field_key
 * @param int    $media_id
 *
 * @return bool
 */
function isago_media_set_field(
    $object_type,
    $object_id,
    $field_key,
    $media_id
) {

    $object_type =
        sanitize_key(
            $object_type
        );

    $object_id =
        absint(
            $object_id
        );

    $field_key =
        sanitize_key(
            $field_key
        );

    $media_id =
        absint(
            $media_id
        );


    if (
        empty($object_type)
        ||
        empty($field_key)
        ||
        empty($media_id)
    ) {

        return false;
    }


    /*
     * Make sure the new media exists.
     */
    $new_media =
        isago_media_get(
            $media_id
        );

    if (!$new_media) {
        return false;
    }


    /*
     * Find the media currently attached to this field.
     *
     * IMPORTANT:
     *
     * The central reference table is the source of truth
     * once a field has been migrated to the new media system.
     */
    $old_media_id =
        isago_media_get_field(
            $object_type,
            $object_id,
            $field_key
        );


    /*
     * Nothing changed.
     */
    if (
        $old_media_id
        &&
        $old_media_id === $media_id
    ) {

        return true;
    }


    /*
     * Remove the existing field reference first.
     */
    if ($old_media_id) {

        $removed =
            isago_media_remove_object_reference(
                $object_type,
                $object_id,
                $field_key
            );

        if (!$removed) {
            return false;
        }
    }


    /*
     * Attach the new media.
     */
    $reference_id =
        isago_media_add_reference(
            $media_id,
            $object_type,
            $object_id,
            $field_key
        );


    if (is_wp_error($reference_id)) {
        return false;
    }


    if (!$reference_id) {
        return false;
    }


    /*
     * The old media may now be unused.
     *
     * If its reference count is zero, cleanup.php will:
     *
     * 1. Delete the ImageKit file.
     * 2. Delete the central media record.
     */
    if ($old_media_id) {

        $cleanup =
            isago_media_delete_if_unused(
                $old_media_id
            );

        if (is_wp_error($cleanup)) {
            return false;
        }
    }


    return true;
}

/**
 * ---------------------------------------------------------
 * GET FIELD MEDIA ID
 * ---------------------------------------------------------
 *
 * Return the Isago media ID attached to a field.
 *
 * @param string $object_type
 * @param int    $object_id
 * @param string $field_key
 *
 * @return int
 */
function isago_media_get_field(
    $object_type,
    $object_id,
    $field_key
) {

    global $wpdb;

    $table =
        isago_media_refs_table();


    $object_type =
        sanitize_key(
            $object_type
        );

    $object_id =
        absint(
            $object_id
        );

    $field_key =
        sanitize_key(
            $field_key
        );


    if (
        empty($object_type)
        ||
        empty($field_key)
    ) {

        return 0;

    }


    $media_id =
        $wpdb->get_var(

            $wpdb->prepare(

                "
                SELECT media_id
                FROM {$table}
                WHERE object_type = %s
                AND object_id = %d
                AND field_key = %s
                LIMIT 1
                ",

                $object_type,
                $object_id,
                $field_key

            )

        );


    return absint(
        $media_id
    );
}



/**
 * ---------------------------------------------------------
 * MIGRATE LEGACY FIELD MEDIA REFERENCE
 * ---------------------------------------------------------
 *
 * Creates a central media reference for an existing field
 * when the field still has its media ID stored in legacy
 * metadata but does not yet have a row in isago_media_refs.
 *
 * This is useful while existing Isago fields are being
 * migrated to the central media system.
 *
 * @param string $object_type
 * @param int    $object_id
 * @param string $field_key
 * @param int    $legacy_media_id
 *
 * @return bool
 */
function isago_media_migrate_field_reference(
    $object_type,
    $object_id,
    $field_key,
    $legacy_media_id
) {

    $object_type =
        sanitize_key(
            $object_type
        );

    $object_id =
        absint(
            $object_id
        );

    $field_key =
        sanitize_key(
            $field_key
        );

    $legacy_media_id =
        absint(
            $legacy_media_id
        );


    if (
        empty($object_type)
        ||
        empty($field_key)
        ||
        empty($legacy_media_id)
    ) {

        return false;
    }


    /*
     * If the field already has a central reference,
     * there is nothing to migrate.
     */
    $existing_media_id =
        isago_media_get_field(
            $object_type,
            $object_id,
            $field_key
        );


    if ($existing_media_id) {
        return true;
    }


    /*
     * Make sure the media record exists.
     */
    $media =
        isago_media_get(
            $legacy_media_id
        );


    if (!$media) {
        return false;
    }


    /*
     * Create the central reference.
     */
    $reference_id =
        isago_media_add_reference(
            $legacy_media_id,
            $object_type,
            $object_id,
            $field_key
        );


    if (is_wp_error($reference_id)) {
        return false;
    }


    return (bool) $reference_id;
}




/**
 * ---------------------------------------------------------
 * GET FIELD MEDIA
 * ---------------------------------------------------------
 *
 * Return the complete media record attached to a field.
 *
 * @param string $object_type
 * @param int    $object_id
 * @param string $field_key
 *
 * @return object|null
 */
function isago_media_get_field_record(
    $object_type,
    $object_id,
    $field_key
) {

    $media_id =
        isago_media_get_field(
            $object_type,
            $object_id,
            $field_key
        );


    if (!$media_id) {
        return null;
    }


    return isago_get_media(
        $media_id
    );
}


/**
 * ---------------------------------------------------------
 * REMOVE FIELD MEDIA
 * ---------------------------------------------------------
 *
 * Remove the reference from a field.
 *
 * IMPORTANT:
 *
 * This does NOT immediately delete the actual media file.
 *
 * The cleanup layer will later determine whether the media
 * is still referenced elsewhere before deleting an ImageKit
 * file.
 *
 * @param string $object_type
 * @param int    $object_id
 * @param string $field_key
 *
 * @return bool
 */
function isago_media_clear_field(
    $object_type,
    $object_id,
    $field_key
) {

    $object_type =
        sanitize_key(
            $object_type
        );

    $object_id =
        absint(
            $object_id
        );

    $field_key =
        sanitize_key(
            $field_key
        );


    if (
        empty($object_type)
        ||
        empty($field_key)
    ) {

        return false;

    }


    return isago_media_remove_object_reference(
        $object_type,
        $object_id,
        $field_key
    );
}


/**
 * ---------------------------------------------------------
 * SET FIELD FROM WORDPRESS ATTACHMENT
 * ---------------------------------------------------------
 *
 * Convert an existing WordPress Media Library attachment
 * into an Isago media record and attach it to a field.
 *
 * The original WordPress attachment is NOT moved.
 *
 * @param string $object_type
 * @param int    $object_id
 * @param string $field_key
 * @param int    $attachment_id
 *
 * @return int|false
 */
function isago_media_set_field_from_attachment(
    $object_type,
    $object_id,
    $field_key,
    $attachment_id
) {

    $attachment_id =
        absint(
            $attachment_id
        );


    if (!$attachment_id) {
        return false;
    }


    /*
     * Convert the existing WordPress attachment into
     * a central Isago media record.
     */
    $media =
        isago_media_from_attachment(
            $attachment_id
        );


    if (!$media) {
        return false;
    }


    $media_id =
        absint(
            $media->id
        );


    if (!$media_id) {
        return false;
    }


    if (
        !isago_media_set_field(
            $object_type,
            $object_id,
            $field_key,
            $media_id
        )
    ) {

        return false;

    }


    return $media_id;
}


/**
 * ---------------------------------------------------------
 * GET FIELD URL
 * ---------------------------------------------------------
 *
 * Convenient helper for frontend/template code.
 *
 * @param string $object_type
 * @param int    $object_id
 * @param string $field_key
 *
 * @return string
 */
function isago_media_get_field_url(
    $object_type,
    $object_id,
    $field_key
) {

    $media_id =
        isago_media_get_field(
            $object_type,
            $object_id,
            $field_key
        );


    if (!$media_id) {
        return '';
    }


    return isago_get_media_url(
        $media_id
    );
}


/**
 * ---------------------------------------------------------
 * GET FIELD IMAGE URL
 * ---------------------------------------------------------
 *
 * @param string $object_type
 * @param int    $object_id
 * @param string $field_key
 * @param string $size
 *
 * @return string
 */
function isago_media_get_field_image_url(
    $object_type,
    $object_id,
    $field_key,
    $size = 'full'
) {

    $media_id =
        isago_media_get_field(
            $object_type,
            $object_id,
            $field_key
        );


    if (!$media_id) {
        return '';
    }


    return isago_get_media_image_url(
        $media_id,
        $size
    );
}


/**
 * ---------------------------------------------------------
 * GET FIELD IMAGE HTML
 * ---------------------------------------------------------
 *
 * @param string $object_type
 * @param int    $object_id
 * @param string $field_key
 * @param string $size
 * @param string $alt
 * @param array  $attributes
 *
 * @return string
 */
function isago_media_get_field_image(
    $object_type,
    $object_id,
    $field_key,
    $size = 'full',
    $alt = '',
    $attributes = []
) {

    $media_id =
        isago_media_get_field(
            $object_type,
            $object_id,
            $field_key
        );


    if (!$media_id) {
        return '';
    }


    return isago_get_media_image(
        $media_id,
        $size,
        $alt,
        $attributes
    );
}