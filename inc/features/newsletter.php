<?php

if (!defined('ABSPATH')) {
    exit;
}

/* =========================================================
   NEWSLETTER DATABASE
========================================================= */

add_action('after_switch_theme', 'isago_create_newsletter_table');
add_action('init', 'isago_maybe_create_newsletter_table');

function isago_create_newsletter_table()
{
    global $wpdb;

    $table = $wpdb->prefix . 'isago_newsletter';

    $charset = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE IF NOT EXISTS $table (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        email VARCHAR(190) NOT NULL,
        status VARCHAR(20) NOT NULL DEFAULT 'subscribed',
        subscribed_at DATETIME NOT NULL,
        unsubscribed_at DATETIME NULL,
        PRIMARY KEY (id),
        UNIQUE KEY email (email)
    ) $charset;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    dbDelta($sql);
}


function isago_maybe_create_newsletter_table()
{
    global $wpdb;

    $table = $wpdb->prefix . 'isago_newsletter';

    $exists = $wpdb->get_var(
        $wpdb->prepare(
            "SHOW TABLES LIKE %s",
            $wpdb->esc_like($table)
        )
    );

    if ($exists !== $table) {

        isago_create_newsletter_table();

    }
}



/* =========================================================
   FRONTEND SUBSCRIPTION
========================================================= */

add_action(
    'admin_post_nopriv_isago_newsletter_subscribe',
    'isago_handle_newsletter_subscription'
);

add_action(
    'admin_post_isago_newsletter_subscribe',
    'isago_handle_newsletter_subscription'
);


function isago_handle_newsletter_subscription()
{
    /* -----------------------------------------------------
       SECURITY
    ----------------------------------------------------- */

    if (
        !isset($_POST['isago_newsletter_nonce']) ||
        !wp_verify_nonce(
            $_POST['isago_newsletter_nonce'],
            'isago_newsletter_subscribe'
        )
    ) {

        wp_die('Security check failed.');

    }


    /* -----------------------------------------------------
       EMAIL
    ----------------------------------------------------- */

    $email = isset($_POST['email'])
        ? sanitize_email($_POST['email'])
        : '';


    if (!$email || !is_email($email)) {

        isago_newsletter_redirect(
            'error',
            'Please enter a valid email address.'
        );

    }


    /* -----------------------------------------------------
       DATABASE
    ----------------------------------------------------- */

    global $wpdb;

    $table = $wpdb->prefix . 'isago_newsletter';


    $existing = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM $table WHERE email = %s LIMIT 1",
            $email
        )
    );


    /* -----------------------------------------------------
       ALREADY SUBSCRIBED
    ----------------------------------------------------- */

    if ($existing && $existing->status === 'subscribed') {

        isago_newsletter_redirect(
            'exists',
            'You are already subscribed to the Isago newsletter.'
        );

    }


    /* -----------------------------------------------------
       RE-SUBSCRIBE
    ----------------------------------------------------- */

    if ($existing && $existing->status === 'unsubscribed') {

        $updated = $wpdb->update(
            $table,
            [
                'status'          => 'subscribed',
                'subscribed_at'   => current_time('mysql'),
                'unsubscribed_at' => null,
            ],
            [
                'id' => $existing->id,
            ],
            [
                '%s',
                '%s',
                '%s',
            ],
            [
                '%d',
            ]
        );

        if ($updated === false) {

            isago_newsletter_redirect(
                'error',
                'Something went wrong. Please try again.'
            );

        }

        isago_newsletter_redirect(
            'success',
            'Welcome back! You are subscribed again.'
        );

    }


    /* -----------------------------------------------------
       NEW SUBSCRIBER
    ----------------------------------------------------- */

    $inserted = $wpdb->insert(
        $table,
        [
            'email'        => $email,
            'status'       => 'subscribed',
            'subscribed_at' => current_time('mysql'),
        ],
        [
            '%s',
            '%s',
            '%s',
        ]
    );


    if (!$inserted) {

        isago_newsletter_redirect(
            'error',
            'We could not subscribe you right now. Please try again.'
        );

    }


    /* -----------------------------------------------------
       SUCCESS
    ----------------------------------------------------- */

    isago_newsletter_redirect(
        'success',
        'You are subscribed! Welcome to Isago.'
    );
}


/* =========================================================
   SAFE REDIRECT
========================================================= */

function isago_newsletter_redirect(
    $status,
    $message
) {

    $referer = wp_get_referer();

    if (!$referer) {

        $referer = home_url('/');

    }

    $redirect = add_query_arg(
        [
            'newsletter' => $status,
            'newsletter_message' => rawurlencode($message),
        ],
        $referer
    );

    wp_safe_redirect($redirect);

    exit;
}


/* =========================================================
   UNSUBSCRIBE
========================================================= */

add_action(
    'admin_post_nopriv_isago_newsletter_unsubscribe',
    'isago_handle_newsletter_unsubscribe'
);

add_action(
    'admin_post_isago_newsletter_unsubscribe',
    'isago_handle_newsletter_unsubscribe'
);


function isago_handle_newsletter_unsubscribe()
{

    if (
        !isset($_POST['isago_newsletter_nonce']) ||
        !wp_verify_nonce(
            $_POST['isago_newsletter_nonce'],
            'isago_newsletter_unsubscribe'
        )
    ) {

        wp_die('Security check failed.');

    }


    $email = isset($_POST['email'])
        ? sanitize_email($_POST['email'])
        : '';


    if (!$email || !is_email($email)) {

        wp_die('Invalid email address.');

    }


    global $wpdb;

    $table = $wpdb->prefix . 'isago_newsletter';


    $updated = $wpdb->update(
        $table,
        [
            'status'          => 'unsubscribed',
            'unsubscribed_at' => current_time('mysql'),
        ],
        [
            'email' => $email,
        ],
        [
            '%s',
            '%s',
        ],
        [
            '%s',
        ]
    );


    wp_safe_redirect(
        add_query_arg(
            'newsletter',
            'unsubscribed',
            home_url('/')
        )
    );

    exit;
}