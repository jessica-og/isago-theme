<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
ISAGO PROMPT RETIREMENT NOTIFICATIONS
=========================================================*/


/**
 * Get users who currently have an active entitlement
 * for a specific AI Prompt.
 *
 * @param int $prompt_id
 * @return array
 */
function isago_get_prompt_retirement_customers(
    $prompt_id
) {

    global $wpdb;


    $prompt_id =
        absint(
            $prompt_id
        );


    if (
        !$prompt_id
        ||
        !function_exists(
            'isago_get_entitlements_table_name'
        )
    ) {

        return array();

    }


    $entitlements_table =
        isago_get_entitlements_table_name();


    $users_table =
        $wpdb->users;


    $rows =
        $wpdb->get_results(
            $wpdb->prepare(
                "
                SELECT DISTINCT
                    e.user_id,
                    u.user_email,
                    u.display_name
                FROM {$entitlements_table} e
                INNER JOIN {$users_table} u
                    ON u.ID = e.user_id
                WHERE e.prompt_id = %d
                AND e.status = 'active'
                AND u.user_email <> ''
                ORDER BY e.user_id ASC
                ",
                $prompt_id
            )
        );


    return is_array(
        $rows
    )
        ? $rows
        : array();
}


/*=========================================================
NOTIFICATION TRACKING
=========================================================*/

/**
 * Check whether a specific retirement notification
 * has already been sent to a user.
 *
 * Tracking is tied to the retirement token so a new
 * retirement schedule creates a new notification cycle.
 *
 * @param int    $prompt_id
 * @param string $token
 * @param string $type
 * @param int    $user_id
 * @return bool
 */
function isago_prompt_retirement_notification_sent(
    $prompt_id,
    $token,
    $type,
    $user_id
) {

    $prompt_id =
        absint(
            $prompt_id
        );


    $user_id =
        absint(
            $user_id
        );


    $token =
        sanitize_key(
            $token
        );


    $type =
        sanitize_key(
            $type
        );


    if (
        !$prompt_id
        ||
        !$token
        ||
        !$type
        ||
        !$user_id
    ) {

        return false;

    }


    $key =
        '_isago_prompt_retirement_' .
        $type .
        '_sent_' .
        $token;


    $sent =
        get_post_meta(
            $prompt_id,
            $key,
            true
        );


    if (
        !is_array(
            $sent
        )
    ) {

        $sent =
            array();

    }


    return isset(
        $sent[
            (string) $user_id
        ]
    );
}


/**
 * Mark a retirement notification as sent.
 *
 * @param int    $prompt_id
 * @param string $token
 * @param string $type
 * @param int    $user_id
 * @return void
 */
function isago_mark_prompt_retirement_notification_sent(
    $prompt_id,
    $token,
    $type,
    $user_id
) {

    $prompt_id =
        absint(
            $prompt_id
        );


    $user_id =
        absint(
            $user_id
        );


    $token =
        sanitize_key(
            $token
        );


    $type =
        sanitize_key(
            $type
        );


    if (
        !$prompt_id
        ||
        !$token
        ||
        !$type
        ||
        !$user_id
    ) {

        return;

    }


    $key =
        '_isago_prompt_retirement_' .
        $type .
        '_sent_' .
        $token;


    $sent =
        get_post_meta(
            $prompt_id,
            $key,
            true
        );


    if (
        !is_array(
            $sent
        )
    ) {

        $sent =
            array();

    }


    $sent[
        (string) $user_id
    ] =
        current_time(
            'mysql',
            true
        );


    update_post_meta(
        $prompt_id,
        $key,
        $sent
    );
}


/*=========================================================
PROMPT EMAIL CONTENT
=========================================================*/

/**
 * Get the actual prompt content for the retirement email.
 *
 * The prompt must still exist at the time this function
 * runs.
 *
 * HTML/Gutenberg markup is converted into readable
 * plain text for the email.
 *
 * @param int $prompt_id
 * @return string
 */
function isago_get_prompt_retirement_email_content(
    $prompt_id
) {

    $prompt_id =
        absint(
            $prompt_id
        );


    if (!$prompt_id) {

        return '';

    }


    $prompt_content =
        get_post_field(
            'post_content',
            $prompt_id
        );


    if (
        !is_string(
            $prompt_content
        )
    ) {

        return '';

    }


    /*
     * Remove Gutenberg/HTML markup while keeping the
     * actual prompt text.
     */
    $prompt_content =
        wp_strip_all_tags(
            strip_shortcodes(
                $prompt_content
            )
        );


    $prompt_content =
        html_entity_decode(
            $prompt_content,
            ENT_QUOTES,
            get_bloginfo(
                'charset'
            )
            ?: 'UTF-8'
        );


    /*
     * Normalize excessive blank lines without changing
     * the actual wording of the prompt.
     */
    $prompt_content =
        preg_replace(
            "/\r\n|\r/",
            "\n",
            $prompt_content
        );


    $prompt_content =
        preg_replace(
            "/\n{3,}/",
            "\n\n",
            $prompt_content
        );


    return trim(
        $prompt_content
    );
}


/*=========================================================
SEND RETIREMENT EMAIL
=========================================================*/

/**
 * Send a prompt retirement email to one purchaser.
 *
 * @param object $user
 * @param int    $prompt_id
 * @param string $type
 * @param int    $removal_timestamp
 * @return bool
 */
function isago_send_prompt_retirement_email(
    $user,
    $prompt_id,
    $type,
    $removal_timestamp
) {

    $prompt_id =
        absint(
            $prompt_id
        );


    $removal_timestamp =
        absint(
            $removal_timestamp
        );


    $type =
        sanitize_key(
            $type
        );


    if (
        !$prompt_id
        ||
        !$removal_timestamp
    ) {

        return false;

    }


    /*
     * Supported notification types.
     */
    if (
        !in_array(
            $type,
            array(
                'initial',
                'final',
            ),
            true
        )
    ) {

        return false;

    }


    /*-----------------------------------------------------
    EMAIL ADDRESS
    -----------------------------------------------------*/

    $email =
        isset(
            $user->user_email
        )
            ? sanitize_email(
                $user->user_email
            )
            : '';


    if (
        !$email
        ||
        !is_email(
            $email
        )
    ) {

        return false;

    }


    /*-----------------------------------------------------
    PROMPT INFORMATION
    -----------------------------------------------------*/

    $title =
        get_the_title(
            $prompt_id
        );


    if (
        !$title
    ) {

        $title =
            'AI Prompt';

    }


    $site_name =
        wp_specialchars_decode(
            get_bloginfo(
                'name'
            ),
            ENT_QUOTES
        );


    $prompt_url =
        get_permalink(
            $prompt_id
        );


    /*
     * Capture the actual prompt while it still exists.
     *
     * This is NOT archived in WordPress.
     *
     * It is simply included in the email sent to the
     * purchaser.
     */
    $prompt_content =
        isago_get_prompt_retirement_email_content(
            $prompt_id
        );


    /*-----------------------------------------------------
    REMOVAL DATE
    -----------------------------------------------------*/

    $removal_date =
        wp_date(
            get_option(
                'date_format'
            )
            . ' '
            .
            get_option(
                'time_format'
            ),
            $removal_timestamp,
            wp_timezone()
        );


    /*-----------------------------------------------------
    CUSTOMER NAME
    -----------------------------------------------------*/

    $customer_name =
        '';


    if (
        !empty(
            $user->display_name
        )
    ) {

        $customer_name =
            sanitize_text_field(
                $user->display_name
            );

    }


    $greeting =
        'Hello';


    if (
        $customer_name !== ''
    ) {

        $greeting .=
            ' ' .
            $customer_name;

    }


    /*=====================================================
    INITIAL NOTICE
    =====================================================*/

    if (
        $type === 'initial'
    ) {

        $subject =
            sprintf(
                '[%s] Prompt removal notice: %s',
                $site_name,
                $title
            );


        $message =
            $greeting .
            ",\n\n";


        $message .=
            "We’re sorry to let you know that the Isago prompt \"" .
            $title .
            "\" is scheduled to be permanently removed from Isago on " .
            $removal_date .
            ".\n\n";


        $message .=
            "Because you purchased this prompt, we wanted to give you advance notice so you have time to keep a copy for your records.\n\n";


        $message .=
            "Please copy and securely store the prompt somewhere you control before the removal date. After the removal date, the prompt will no longer be available through Isago.\n\n";


        if (
            $prompt_content !== ''
        ) {

            $message .=
                "Here is a copy of the prompt you purchased:\n\n";


            $message .=
                "========================================\n";


            $message .=
                $prompt_content .
                "\n";


            $message .=
                "========================================\n\n";

        }


        if (
            $prompt_url
        ) {

            $message .=
                "You can still view the prompt on Isago while it remains available:\n";


            $message .=
                $prompt_url .
                "\n\n";

        }


        $message .=
            "We sincerely apologize for the inconvenience and appreciate your support of Isago.\n\n";


        $message .=
            "Thank you for using Isago.\n";


        $message .=
            $site_name;

    }


    /*=====================================================
    FINAL REMINDER
    =====================================================*/

    elseif (
        $type === 'final'
    ) {

        $subject =
            sprintf(
                '[%s] Final reminder: %s is being removed',
                $site_name,
                $title
            );


        $message =
            $greeting .
            ",\n\n";


        $message .=
            "This is a final reminder that the Isago prompt \"" .
            $title .
            "\" is scheduled to be permanently removed on " .
            $removal_date .
            ".\n\n";


        $message .=
            "If you have not already done so, please copy and securely store the prompt before it is removed.\n\n";


        if (
            $prompt_content !== ''
        ) {

            $message .=
                "Here is a copy of the prompt you purchased:\n\n";


            $message .=
                "========================================\n";


            $message .=
                $prompt_content .
                "\n";


            $message .=
                "========================================\n\n";

        }


        if (
            $prompt_url
        ) {

            $message .=
                "You can still view the prompt on Isago while it remains available:\n";


            $message .=
                $prompt_url .
                "\n\n";

        }


        $message .=
            "We apologize again for the inconvenience and appreciate your support of Isago.\n\n";


        $message .=
            "Thank you,\n";


        $message .=
            $site_name;

    }


    else {

        return false;

    }


    /*-----------------------------------------------------
    EMAIL HEADERS
    -----------------------------------------------------*/

    $headers =
        array(
            'Content-Type: text/plain; charset=UTF-8',
        );


    /*-----------------------------------------------------
    SEND EMAIL
    -----------------------------------------------------*/

    return wp_mail(
        $email,
        $subject,
        $message,
        $headers
    );
}


/*=========================================================
SEND NOTIFICATIONS TO ALL CURRENT PURCHASERS
=========================================================*/

/**
 * Send a retirement notification to all users who
 * currently have active access to the prompt.
 *
 * @param int    $prompt_id
 * @param string $type
 * @return int
 */
function isago_send_prompt_retirement_notifications(
    $prompt_id,
    $type = 'initial'
) {

    $prompt_id =
        absint(
            $prompt_id
        );


    $type =
        sanitize_key(
            $type
        );


    if (
        !$prompt_id
        ||
        !in_array(
            $type,
            array(
                'initial',
                'final',
            ),
            true
        )
    ) {

        return 0;

    }


    /*
     * Get the retirement token.
     */
    $token =
        get_post_meta(
            $prompt_id,
            '_isago_ai_prompt_retirement_token',
            true
        );


    /*
     * Get the scheduled removal timestamp.
     */
    $removal_timestamp =
        (int)
        get_post_meta(
            $prompt_id,
            '_isago_ai_prompt_removal_at',
            true
        );


    if (
        !$token
        ||
        !$removal_timestamp
    ) {

        return 0;

    }


    /*
     * Get purchasers with active entitlement.
     */
    $customers =
        isago_get_prompt_retirement_customers(
            $prompt_id
        );


    $sent_count =
        0;


    foreach (
        $customers as $customer
    ) {

        $user_id =
            absint(
                $customer->user_id
            );


        if (!$user_id) {

            continue;

        }


        /*
         * Prevent duplicate email delivery.
         */
        if (
            isago_prompt_retirement_notification_sent(
                $prompt_id,
                $token,
                $type,
                $user_id
            )
        ) {

            continue;

        }


        /*
         * Send the email.
         */
        $sent =
            isago_send_prompt_retirement_email(
                $customer,
                $prompt_id,
                $type,
                $removal_timestamp
            );


        if (
            $sent
        ) {

            isago_mark_prompt_retirement_notification_sent(
                $prompt_id,
                $token,
                $type,
                $user_id
            );


            $sent_count++;

        }

    }


    return $sent_count;
}