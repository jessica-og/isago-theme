<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
ISAGO AI PROMPT PERMANENT DELETION
=========================================================*/

/**
 * Permanently remove an AI Prompt.
 *
 * IMPORTANT:
 *
 * - This does NOT reverse a Paystack transaction.
 * - This DOES retain Isago local order records.
 * - This DOES revoke active prompt entitlements.
 * - This DOES retain revoked entitlement records.
 * - This DOES remove prompt performance/view records.
 * - This DOES permanently delete the WordPress prompt.
 * - Existing Isago media cleanup handles the prompt's
 *   associated ImageKit media when the post is deleted.
 *
 * The prompt must already be scheduled for removal and
 * its scheduled removal time must have arrived.
 *
 * @param int $prompt_id
 * @return bool
 */
function isago_delete_ai_prompt_permanently($prompt_id)
{
    global $wpdb;


    $prompt_id =
        absint(
            $prompt_id
        );


    if (!$prompt_id) {

        return false;

    }


    /*
     * The post must still exist and be an AI Prompt.
     */
    if (
        get_post_type(
            $prompt_id
        ) !== 'ai_prompt'
    ) {

        return false;

    }


    /*-----------------------------------------------------
    SAFETY CHECK
    -----------------------------------------------------*/

    $status =
        function_exists(
            'isago_get_ai_prompt_retirement_status'
        )
            ? isago_get_ai_prompt_retirement_status(
                $prompt_id
            )
            : '';


    /*
     * Only scheduled prompts can be permanently
     * removed by the retirement system.
     */
    if (
        $status !== 'scheduled'
    ) {

        return false;

    }


    $removal_at =
        function_exists(
            'isago_get_ai_prompt_removal_at'
        )
            ? isago_get_ai_prompt_removal_at(
                $prompt_id
            )
            : 0;


    /*
     * Never remove a prompt before its scheduled
     * removal timestamp.
     */
    if (
        !$removal_at
        ||
        $removal_at > time()
    ) {

        return false;

    }


    /*-----------------------------------------------------
    REVOKE PROMPT ENTITLEMENTS
    -----------------------------------------------------*/

    /*
     * IMPORTANT:
     *
     * Entitlements are NOT deleted.
     *
     * They are changed from:
     *
     * active
     *
     * to:
     *
     * revoked
     *
     * This preserves the purchase/access history while
     * ensuring the deleted prompt can no longer be accessed.
     */

    if (
        function_exists(
            'isago_get_entitlements_table_name'
        )
    ) {

        $entitlements_table =
            isago_get_entitlements_table_name();


        $now =
            current_time(
                'mysql',
                true
            );


        /*
         * Only active entitlements need to be revoked.
         *
         * Existing revoked records are left untouched.
         */
        $wpdb->query(
            $wpdb->prepare(
                "
                UPDATE {$entitlements_table}
                SET
                    status = 'revoked',
                    revoked_at = %s,
                    updated_at = %s
                WHERE prompt_id = %d
                AND status = 'active'
                ",
                $now,
                $now,
                $prompt_id
            )
        );

    }


    /*-----------------------------------------------------
    KEEP LOCAL ORDERS
    -----------------------------------------------------*/

    /*
     * DO NOT DELETE ORDERS.
     *
     * The financial transaction history must remain
     * available for:
     *
     * - customer purchase history
     * - administrator transaction history
     * - payment auditing
     * - refund/reversal records
     * - Paystack reconciliation
     *
     * The order's prompt_id may point to a prompt that
     * no longer exists.
     *
     * This is intentional.
     *
     * Paystack's own transaction record is also untouched.
     */


    /*-----------------------------------------------------
    REMOVE VIEW / PERFORMANCE RECORDS
    -----------------------------------------------------*/

    if (
        function_exists(
            'isago_get_prompt_views_table_name'
        )
    ) {

        $views_table =
            isago_get_prompt_views_table_name();


        $wpdb->delete(
            $views_table,
            array(
                'prompt_id' =>
                    $prompt_id,
            ),
            array(
                '%d',
            )
        );

    }


    /*-----------------------------------------------------
    CANCEL RETIREMENT EVENTS
    -----------------------------------------------------*/

    if (
        function_exists(
            'isago_get_ai_prompt_retirement_token'
        )
    ) {

        $token =
            isago_get_ai_prompt_retirement_token(
                $prompt_id
            );


        if (
            $token
            &&
            function_exists(
                'isago_cancel_prompt_retirement_events'
            )
        ) {

            isago_cancel_prompt_retirement_events(
                $prompt_id,
                $token
            );

        }

    }


    /*-----------------------------------------------------
    DELETE THE WORDPRESS PROMPT
    -----------------------------------------------------*/

    /*
     * IMPORTANT:
     *
     * The existing Isago media system already hooks into
     * WordPress post deletion and supports the ai_prompt
     * post type.
     *
     * Therefore wp_delete_post() is intentionally used
     * here rather than manually deleting ImageKit media.
     *
     * The existing media cleanup system will determine
     * whether the associated media is still referenced
     * elsewhere before removing the ImageKit object.
     */

    $deleted =
        wp_delete_post(
            $prompt_id,
            true
        );


    if (
        !$deleted
    ) {

        /*
         * Log the failure so an administrator can identify
         * a deletion problem without silently losing track
         * of the scheduled removal.
         */
        error_log(
            sprintf(
                'Isago: Failed to permanently delete AI Prompt ID %d.',
                $prompt_id
            )
        );


        return false;

    }


    return true;
}


/*=========================================================
MANUAL DELETE PROTECTION
=========================================================*/

/**
 * Clean up Isago local records when an administrator
 * permanently deletes an AI Prompt manually.
 *
 * IMPORTANT:
 *
 * Manual deletion is different from scheduled retirement.
 *
 * If an administrator manually deletes an AI Prompt:
 *
 * - Orders are RETAINED.
 * - Active entitlements are REVOKED.
 * - Revoked entitlements remain as history.
 * - Prompt performance records are removed.
 *
 * The existing WordPress/media cleanup system handles
 * the prompt's associated media.
 *
 * @param int     $post_id
 * @param WP_Post $post
 * @param bool    $force_delete
 * @return void
 */
function isago_cleanup_ai_prompt_after_manual_delete(
    $post_id,
    $post = null,
    $force_delete = false
) {

    global $wpdb;


    $post_id =
        absint(
            $post_id
        );


    if (!$post_id) {

        return;

    }


    /*
     * Only clean records when the deleted object was
     * actually an AI Prompt.
     *
     * The post object is supplied by WordPress before
     * the post is completely gone.
     */
    if (
        $post instanceof WP_Post
        &&
        $post->post_type !== 'ai_prompt'
    ) {

        return;

    }


    /*-----------------------------------------------------
    REVOKE ENTITLEMENTS
    -----------------------------------------------------*/

    if (
        function_exists(
            'isago_get_entitlements_table_name'
        )
    ) {

        $entitlements_table =
            isago_get_entitlements_table_name();


        $now =
            current_time(
                'mysql',
                true
            );


        /*
         * Preserve entitlement rows.
         *
         * Only active access is revoked.
         */
        $wpdb->query(
            $wpdb->prepare(
                "
                UPDATE {$entitlements_table}
                SET
                    status = 'revoked',
                    revoked_at = %s,
                    updated_at = %s
                WHERE prompt_id = %d
                AND status = 'active'
                ",
                $now,
                $now,
                $post_id
            )
        );

    }


    /*-----------------------------------------------------
    KEEP LOCAL ORDERS
    -----------------------------------------------------*/

    /*
     * IMPORTANT:
     *
     * Orders are deliberately NOT deleted.
     *
     * The financial history must survive the deletion of
     * the WordPress prompt.
     */


    /*-----------------------------------------------------
    REMOVE VIEW / PERFORMANCE RECORDS
    -----------------------------------------------------*/

    if (
        function_exists(
            'isago_get_prompt_views_table_name'
        )
    ) {

        $views_table =
            isago_get_prompt_views_table_name();


        $wpdb->delete(
            $views_table,
            array(
                'prompt_id' =>
                    $post_id,
            ),
            array(
                '%d',
            )
        );

    }

}


/*=========================================================
REGISTER MANUAL DELETE CLEANUP
=========================================================*/

add_action(
    'after_delete_post',
    'isago_cleanup_ai_prompt_after_manual_delete',
    20,
    3
);