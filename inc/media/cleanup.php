<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * ---------------------------------------------------------
 * ISAGO MEDIA CLEANUP
 * ---------------------------------------------------------
 *
 * Safely removes media that is no longer referenced.
 *
 * ImageKit files are deleted only when their Isago media
 * record has no remaining references.
 */


/**
 * ---------------------------------------------------------
 * DELETE MEDIA IF UNUSED
 * ---------------------------------------------------------
 *
 * @param int $media_id
 *
 * @return bool|WP_Error  
 */
function isago_media_delete_if_unused($media_id)
{
    $media_id = absint($media_id);

    if (!$media_id) {
        return false;
    }

    $media = isago_get_media($media_id);

    if (!$media) {
        return false;
    }

    /*
     * Never delete media that is still being used.
     */
    $reference_count =
        isago_media_reference_count($media_id);

    if ($reference_count > 0) {
        return false;
    }

    /*
     * ImageKit files must be deleted from ImageKit first.
     */
    if ($media->source === 'imagekit') {

        if (
            !empty($media->account_id)
            &&
            !empty($media->file_id)
        ) {

            $result =
                isago_imagekit_delete_file(
                    $media->account_id,
                    $media->file_id
                );

            if (is_wp_error($result)) {
                return $result;
            }
        }
    }

    /*
     * Delete the central Isago media record.
     */
    return isago_media_delete_record($media_id);
}


/**
 * ---------------------------------------------------------
 * REMOVE FIELD AND CLEANUP MEDIA
 * ---------------------------------------------------------
 *
 * Removes a field reference and then checks whether the
 * media is no longer used anywhere.
 *
 * @param string $object_type
 * @param int    $object_id
 * @param string $field_key
 *
 * @return bool|WP_Error
 */
function isago_media_remove_field_and_cleanup(
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
        return true;
    }

    /*
     * Remove this field's reference.
     */
    $removed =
        isago_media_clear_field(
            $object_type,
            $object_id,
            $field_key
        );

    if (!$removed) {
        return false;
    }

    /*
     * The media may still be used somewhere else.
     */
    $remaining =
        isago_media_reference_count(
            $media_id
        );

    if ($remaining > 0) {
        return true;
    }

    /*
     * Nobody uses it anymore.
     */
    return isago_media_delete_if_unused(
        $media_id
    );
}


/**
 * ---------------------------------------------------------
 * DELETE CENTRAL MEDIA RECORD
 * ---------------------------------------------------------
 *
 * This function removes only the Isago database record.
 *
 * It assumes the ImageKit file has already been handled.
 *
 * @param int $media_id
 *
 * @return bool
 */
function isago_media_delete_record($media_id)
{
    global $wpdb;

    $media_id = absint($media_id);

    if (!$media_id) {
        return false;
    }

    $media_table =
        isago_media_table();

    $refs_table =
        isago_media_refs_table();

    /*
     * Remove any remaining references.
     */
    $wpdb->delete(
        $refs_table,
        [
            'media_id' => $media_id,
        ],
        [
            '%d',
        ]
    );

    /*
     * Remove the media record.
     */
    $deleted =
        $wpdb->delete(
            $media_table,
            [
                'id' => $media_id,
            ],
            [
                '%d',
            ]
        );

    return $deleted !== false;
}






/**
 * Remove all media references belonging to an object
 * and clean up media that is no longer referenced.
 *
 * @param string $object_type
 * @param int    $object_id
 *
 * @return bool
 */

function isago_media_remove_object_and_cleanup(
    $object_type,
    $object_id
) {
    $object_type = sanitize_key($object_type);
    $object_id   = absint($object_id);

    if (!$object_type || !$object_id) {
        return false;
    }

    $references = isago_media_get_references_for_object(
        $object_type,
        $object_id
    );

    if (empty($references)) {
        error_log(
            sprintf(
                'Isago media cleanup: no references found for %s ID %d.',
                $object_type,
                $object_id
            )
        );

        return true;
    }

    $all_successful = true;

    foreach ($references as $reference) {
        $media_id = absint($reference->media_id);
        $field_key = sanitize_key($reference->field_key);

        if (!$media_id || !$field_key) {
            continue;
        }

        $removed = isago_media_remove_reference(
            $media_id,
            $object_type,
            $object_id,
            $field_key
        );

        if (!$removed) {
            error_log(
                sprintf(
                    'Isago media cleanup: failed to remove reference for media %d, object %s:%d, field %s.',
                    $media_id,
                    $object_type,
                    $object_id,
                    $field_key
                )
            );

            $all_successful = false;
            continue;
        }

        $remaining = isago_media_reference_count($media_id);

        if ($remaining > 0) {
            error_log(
                sprintf(
                    'Isago media cleanup: keeping media %d because %d reference(s) remain.',
                    $media_id,
                    $remaining
                )
            );

            continue;
        }

        $deleted = isago_media_delete_if_unused($media_id);

        if (is_wp_error($deleted)) {
            error_log(
                sprintf(
                    'Isago media cleanup: failed to delete media %d: %s',
                    $media_id,
                    $deleted->get_error_message()
                )
            );

            $all_successful = false;
        } elseif ($deleted === false) {
            error_log(
                sprintf(
                    'Isago media cleanup: media %d was not deleted. Check its record and reference count.',
                    $media_id
                )
            );

            $all_successful = false;
        } else {
            error_log(
                sprintf(
                    'Isago media cleanup: media %d was deleted successfully.',
                    $media_id
                )
            );
        }
    }

    return $all_successful;
}




/*=========================================================
DELETE POST MEDIA WHEN POST IS DELETED
=========================================================*/

/**
 * Remove all central Isago Media references belonging
 * to a deleted post.
 *
 * The cleanup system will only delete an ImageKit file
 * when that media record has no remaining references.
 */
add_action(
    'before_delete_post',
    'isago_media_cleanup_deleted_post',
    10,
    2
);

function isago_media_cleanup_deleted_post(
    $post_id,
    $post
) {

    $post_id = absint(
        $post_id
    );


    if (!$post_id) {

        return;

    }


    /*
     * Only clean media belonging to supported
     * WordPress post objects.
     *
     * At this stage this includes normal Blog Posts
     * and Isago Video posts.
     */
    if (
        !in_array(
            get_post_type($post_id),
            [
                'post',
                'isago_video',
                'ai_prompt'
            ],
            true
        )
    ) {

        return;

    }


    isago_media_remove_object_and_cleanup(
        'post',
        $post_id
    );

}




