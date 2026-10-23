<?php

if (!defined('ABSPATH')) {
    exit;
}


/* =========================================================
   ADMIN MENU
========================================================= */

add_action(
    'admin_menu',
    'isago_newsletter_admin_menu'
);


function isago_newsletter_admin_menu()
{

    add_submenu_page(

        'isago-home',

        'Newsletter Subscribers',

        'Newsletter Subscribers',

        'manage_options',

        'isago-newsletter',

        'isago_newsletter_admin_page'

    );
}


/* =========================================================
   ADMIN PAGE
========================================================= */

function isago_newsletter_admin_page()
{

    if (!current_user_can('manage_options')) {
        return;
    }


    global $wpdb;

    $table = $wpdb->prefix . 'isago_newsletter';


    /* -----------------------------------------------------
       DELETE
    ----------------------------------------------------- */

    if (
        isset($_GET['delete']) &&
        isset($_GET['_wpnonce'])
    ) {

        $id = absint($_GET['delete']);


        if (
            wp_verify_nonce(
                $_GET['_wpnonce'],
                'delete_newsletter_' . $id
            )
        ) {

            $wpdb->delete(
                $table,
                [
                    'id' => $id,
                ],
                [
                    '%d',
                ]
            );

            echo '<div class="notice notice-success is-dismissible">
                <p>Subscriber deleted.</p>
            </div>';
        }
    }


    /* -----------------------------------------------------
       COUNTS
    ----------------------------------------------------- */

    $total = (int) $wpdb->get_var(
        "SELECT COUNT(*) FROM $table"
    );


    $subscribed = (int) $wpdb->get_var(
        $wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE status = %s",
            'subscribed'
        )
    );


    $unsubscribed = (int) $wpdb->get_var(
        $wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE status = %s",
            'unsubscribed'
        )
    );


    /* -----------------------------------------------------
       SUBSCRIBERS
    ----------------------------------------------------- */

    $subscribers = $wpdb->get_results(
        "SELECT *
         FROM $table
         ORDER BY subscribed_at DESC"
    );

    ?>

    <div class="wrap">

        <h1>
            Newsletter Subscribers
        </h1>


        <div
            style="
                display:grid;
                grid-template-columns:repeat(3,1fr);
                gap:20px;
                margin:25px 0;
                max-width:900px;
            "
        >

            <div
                style="
                    background:#fff;
                    border:1px solid #ddd;
                    border-radius:8px;
                    padding:20px;
                "
            >

                <strong>Total</strong>

                <h2>
                    <?php echo esc_html($total); ?>
                </h2>

            </div>


            <div
                style="
                    background:#fff;
                    border:1px solid #ddd;
                    border-radius:8px;
                    padding:20px;
                "
            >

                <strong>Subscribed</strong>

                <h2 style="color:#16a34a;">

                    <?php echo esc_html(
                        $subscribed
                    ); ?>

                </h2>

            </div>


            <div
                style="
                    background:#fff;
                    border:1px solid #ddd;
                    border-radius:8px;
                    padding:20px;
                "
            >

                <strong>Unsubscribed</strong>

                <h2 style="color:#dc2626;">

                    <?php echo esc_html(
                        $unsubscribed
                    ); ?>

                </h2>

            </div>

        </div>


        <table class="widefat fixed striped">

            <thead>

                <tr>

                    <th>
                        Email
                    </th>

                    <th>
                        Status
                    </th>

                    <th>
                        Subscribed
                    </th>

                    <th>
                        Unsubscribed
                    </th>

                    <th>
                        Action
                    </th>

                </tr>

            </thead>


            <tbody>

            <?php if ($subscribers) : ?>

                <?php foreach ($subscribers as $subscriber) : ?>

                    <tr>

                        <td>

                            <strong>
                                <?php echo esc_html(
                                    $subscriber->email
                                ); ?>
                            </strong>

                        </td>


                        <td>

                            <?php if (
                                $subscriber->status === 'subscribed'
                            ) : ?>

                                <span
                                    style="
                                        color:#15803d;
                                        font-weight:600;
                                    "
                                >
                                    ● Subscribed
                                </span>

                            <?php else : ?>

                                <span
                                    style="
                                        color:#dc2626;
                                        font-weight:600;
                                    "
                                >
                                    ● Unsubscribed
                                </span>

                            <?php endif; ?>

                        </td>


                        <td>

                            <?php echo esc_html(
                                $subscriber->subscribed_at
                            ); ?>

                        </td>


                        <td>

                            <?php echo esc_html(
                                $subscriber->unsubscribed_at
                                ?: '—'
                            ); ?>

                        </td>


                        <td>

                            <a
                                href="<?php echo esc_url(
                                    wp_nonce_url(
                                        admin_url(
                                            'admin.php?page=isago-newsletter&delete='
                                            . $subscriber->id
                                        ),
                                        'delete_newsletter_' .
                                        $subscriber->id
                                    )
                                ); ?>"
                                class="button"
                                onclick="return confirm('Delete this subscriber?');"
                            >
                                Delete
                            </a>

                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php else : ?>

                <tr>

                    <td colspan="5">

                        No newsletter subscribers yet.

                    </td>

                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

    <?php
}