<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
PAYMENT SETTINGS
=========================================================*/


/**
 * Get Isago payment settings.
 *
 * IMPORTANT:
 * The Paystack secret key is NOT stored in the
 * WordPress options table.
 *
 * It must be defined server-side in wp-config.php:
 *
 * define(
 *     'ISAGO_PAYSTACK_SECRET_KEY',
 *     'sk_test_...'
 * );
 *
 * @return array
 */
function isago_get_payment_settings()
{
    $defaults = array(
        'provider'   => 'paystack',
        'mode'       => 'test',
        'public_key' => '',
        'currency'   => 'NGN',
    );


    $settings = get_option(
        'isago_payment_settings',
        array()
    );


    if (!is_array($settings)) {
        $settings = array();
    }


    $settings = wp_parse_args(
        $settings,
        $defaults
    );


    /*
     * Payment provider is currently Paystack.
     */
    $settings['provider'] = 'paystack';


    /*
     * Payment currency is currently NGN.
     */
    $settings['currency'] = 'NGN';


    /*
     * Never return a secret_key from the WordPress
     * payment settings.
     */
    unset(
        $settings['secret_key']
    );


    return $settings;
}


/*=========================================================
PAYSTACK MODE
=========================================================*/


/**
 * Get Paystack mode.
 *
 * @return string
 */
function isago_get_paystack_mode()
{
    $settings =
        isago_get_payment_settings();


    return (
        isset(
            $settings['mode']
        ) &&
        $settings['mode'] === 'live'
    )
        ? 'live'
        : 'test';
}


/*=========================================================
PAYSTACK PUBLIC KEY
=========================================================*/


/**
 * Get Paystack public key.
 *
 * @return string
 */
function isago_get_paystack_public_key()
{
    $settings =
        isago_get_payment_settings();


    return isset(
        $settings['public_key']
    )
        ? trim(
            $settings['public_key']
        )
        : '';
}


/*=========================================================
PAYSTACK SECRET KEY
=========================================================*/


/**
 * Get Paystack secret key.
 *
 * The secret key is ONLY read from wp-config.php.
 *
 * @return string
 */
function isago_get_paystack_secret_key()
{
    if (
        defined(
            'ISAGO_PAYSTACK_SECRET_KEY'
        ) &&
        ISAGO_PAYSTACK_SECRET_KEY !== ''
    ) {

        return trim(
            ISAGO_PAYSTACK_SECRET_KEY
        );

    }


    return '';
}


/*=========================================================
CHECK PAYMENT CONFIGURATION
=========================================================*/


/**
 * Check whether Paystack is configured.
 *
 * @return bool
 */
function isago_is_paystack_configured()
{
    $public_key =
        isago_get_paystack_public_key();


    $secret_key =
        isago_get_paystack_secret_key();


    return (
        $public_key !== '' &&
        $secret_key !== ''
    );
}


/**
 * Check whether the configured Paystack key pair
 * appears to match the selected mode.
 *
 * This is only a format check.
 *
 * It does NOT prove that the keys are valid.
 *
 * @return bool
 */
function isago_paystack_keys_match_mode()
{
    $mode =
        isago_get_paystack_mode();


    $public_key =
        isago_get_paystack_public_key();


    $secret_key =
        isago_get_paystack_secret_key();


    if (
        $public_key === '' ||
        $secret_key === ''
    ) {

        return false;

    }


    if (
        $mode === 'test'
    ) {

        return (
            strpos(
                $public_key,
                'pk_test_'
            ) === 0 &&
            strpos(
                $secret_key,
                'sk_test_'
            ) === 0
        );

    }


    return (
        strpos(
            $public_key,
            'pk_live_'
        ) === 0 &&
        strpos(
            $secret_key,
            'sk_live_'
        ) === 0
    );
}


/*=========================================================
ADMIN SETTINGS
=========================================================*/


/**
 * Register payment settings.
 */
function isago_register_payment_settings()
{
    register_setting(
        'isago_payment_settings_group',
        'isago_payment_settings',
        array(
            'type'              => 'array',
            'sanitize_callback' => 'isago_sanitize_payment_settings',
            'default'           => array(
                'provider'   => 'paystack',
                'mode'       => 'test',
                'public_key' => '',
                'currency'   => 'NGN',
            ),
        )
    );
}

add_action(
    'admin_init',
    'isago_register_payment_settings'
);


/*=========================================================
SANITIZE SETTINGS
=========================================================*/


/**
 * Sanitize payment settings.
 *
 * @param array $input
 * @return array
 */
function isago_sanitize_payment_settings(
    $input
) {
    if (!is_array($input)) {
        $input = array();
    }


    $output = array();


    /*
     * Provider.
     */
    $output['provider'] =
        'paystack';


    /*
     * Mode.
     */
    $mode =
        isset(
            $input['mode']
        )
            ? sanitize_key(
                $input['mode']
            )
            : 'test';


    if (
        !in_array(
            $mode,
            array(
                'test',
                'live',
            ),
            true
        )
    ) {

        $mode = 'test';

    }


    $output['mode'] =
        $mode;


    /*
     * Public key.
     */
    $output['public_key'] =
        isset(
            $input['public_key']
        )
            ? sanitize_text_field(
                $input['public_key']
            )
            : '';


    /*
     * Currency.
     *
     * Isago currently uses NGN only.
     */
    $output['currency'] =
        'NGN';


    /*
     * IMPORTANT:
     *
     * We intentionally do NOT accept:
     *
     * $input['secret_key']
     *
     * The secret key must never be saved into
     * the WordPress options table.
     */


    return $output;
}


/*=========================================================
ADMIN MENU
=========================================================*/


/**
 * Add payment settings page.
 *
 * We deliberately use the normal WordPress Settings
 * menu instead of assuming an Isago parent menu slug.
 *
 * This prevents an invalid parent menu from breaking
 * the Isago admin area.
 */
function isago_add_payment_settings_page()
{
    add_options_page(
        'Isago Payment Settings',
        'Isago Payments',
        'manage_options',
        'isago-payment-settings',
        'isago_render_payment_settings_page'
    );
}

add_action(
    'admin_menu',
    'isago_add_payment_settings_page',
    30
);


/*=========================================================
ADMIN PAGE
=========================================================*/


/**
 * Render payment settings page.
 */
function isago_render_payment_settings_page()
{
    if (
        !current_user_can(
            'manage_options'
        )
    ) {

        return;

    }


    $settings =
        isago_get_payment_settings();


    $secret_configured =
        isago_get_paystack_secret_key() !== '';


    $keys_match =
        isago_paystack_keys_match_mode();

    ?>

    <div class="wrap">

        <h1>
            Isago Payment Settings
        </h1>


        <p>
            Configure Paystack for Isago payments.
        </p>


        <div
            style="
                max-width: 800px;
                margin: 20px 0;
                padding: 15px 18px;
                background: #fff;
                border-left: 4px solid #00d4f5;
            "
        >

            <strong>
                Payment currency:
            </strong>

            NGN — Nigerian Naira (₦)

            <p style="margin-bottom:0;">

                Isago currently uses NGN for AI Prompt purchases.
                Prompt prices remain stored as normal NGN amounts,
                while Paystack receives the equivalent amount in kobo.

            </p>

        </div>


        <?php if (!$secret_configured) : ?>

            <div class="notice notice-warning">

                <p>

                    <strong>
                        Paystack secret key is not configured.
                    </strong>

                    Add
                    <code>
                        ISAGO_PAYSTACK_SECRET_KEY
                    </code>
                    to your server-side
                    <code>
                        wp-config.php
                    </code>
                    file before testing payments.

                </p>

            </div>

        <?php elseif (!$keys_match) : ?>

            <div class="notice notice-error">

                <p>

                    <strong>
                        Paystack key mode mismatch.
                    </strong>

                    Your selected payment mode is

                    <strong>
                        <?php
                        echo esc_html(
                            strtoupper(
                                $settings['mode']
                            )
                        );
                        ?>
                    </strong>

                    but the configured key prefixes do not
                    match that mode.

                </p>

            </div>

        <?php else : ?>

            <div class="notice notice-success">

                <p>

                    Paystack server configuration appears
                    correctly configured for

                    <strong>
                        <?php
                        echo esc_html(
                            strtoupper(
                                $settings['mode']
                            )
                        );
                        ?>
                    </strong>

                    mode.

                </p>

            </div>

        <?php endif; ?>


        <form
            method="post"
            action="options.php"
        >

            <?php

            settings_fields(
                'isago_payment_settings_group'
            );

            ?>


            <table
                class="form-table"
                role="presentation"
            >


                <!-- PROVIDER -->

                <tr>

                    <th scope="row">
                        Payment Provider
                    </th>

                    <td>

                        <input
                            type="text"
                            value="Paystack"
                            class="regular-text"
                            readonly
                        >

                        <p class="description">

                            Paystack is the payment provider
                            currently used by Isago.

                        </p>

                    </td>

                </tr>


                <!-- MODE -->

                <tr>

                    <th scope="row">
                        Payment Mode
                    </th>

                    <td>

                        <fieldset>

                            <label>

                                <input
                                    type="radio"
                                    name="isago_payment_settings[mode]"
                                    value="test"
                                    <?php
                                    checked(
                                        $settings['mode'],
                                        'test'
                                    );
                                    ?>
                                >

                                Test Mode

                            </label>


                            <br>


                            <label>

                                <input
                                    type="radio"
                                    name="isago_payment_settings[mode]"
                                    value="live"
                                    <?php
                                    checked(
                                        $settings['mode'],
                                        'live'
                                    );
                                    ?>
                                >

                                Live Mode

                            </label>

                        </fieldset>


                        <p class="description">

                            Use Test Mode during development.
                            Only switch to Live Mode after the
                            complete payment flow has been tested.

                        </p>

                    </td>

                </tr>


                <!-- PUBLIC KEY -->

                <tr>

                    <th scope="row">

                        <label
                            for="isago_paystack_public_key"
                        >
                            Paystack Public Key
                        </label>

                    </th>


                    <td>

                        <input
                            type="text"
                            id="isago_paystack_public_key"
                            name="isago_payment_settings[public_key]"
                            value="<?php
                            echo esc_attr(
                                $settings['public_key']
                            );
                            ?>"
                            class="regular-text"
                            autocomplete="off"
                        >


                        <p class="description">

                            Test mode:

                            <code>
                                pk_test_...
                            </code>

                            <br>

                            Live mode:

                            <code>
                                pk_live_...
                            </code>

                        </p>

                    </td>

                </tr>


                <!-- SECRET KEY INFORMATION -->

                <tr>

                    <th scope="row">
                        Paystack Secret Key
                    </th>


                    <td>

                        <p>

                            <strong>
                                Server-side only
                            </strong>

                        </p>


                        <?php if ($secret_configured) : ?>

                            <p>

                                <span
                                    style="
                                        color:#008a20;
                                        font-weight:600;
                                    "
                                >
                                    ● Secret key configured
                                </span>

                            </p>

                        <?php else : ?>

                            <p>

                                <span
                                    style="
                                        color:#b32d2e;
                                        font-weight:600;
                                    "
                                >
                                    ● Secret key not configured
                                </span>

                            </p>

                        <?php endif; ?>


                        <p class="description">

                            The secret key is intentionally
                            <strong>
                                not stored in WordPress settings
                            </strong>
                            and is never displayed here.

                            Define it in
                            <code>
                                wp-config.php
                            </code>
                            using:

                        </p>


                        <pre
                            style="
                                max-width:650px;
                                overflow:auto;
                                padding:12px;
                                background:#f6f7f7;
                                border:1px solid #dcdcde;
                            "
                        ><code>define(
    'ISAGO_PAYSTACK_SECRET_KEY',
    'sk_test_YOUR_TEST_SECRET_KEY'
);</code></pre>

                    </td>

                </tr>


                <!-- CURRENCY -->

                <tr>

                    <th scope="row">
                        Payment Currency
                    </th>


                    <td>

                        <input
                            type="text"
                            value="NGN — Nigerian Naira (₦)"
                            class="regular-text"
                            readonly
                        >


                        <input
                            type="hidden"
                            name="isago_payment_settings[currency]"
                            value="NGN"
                        >


                        <p class="description">

                            Isago payment transactions currently
                            use Nigerian Naira.

                        </p>

                    </td>

                </tr>


            </table>


            <?php

            submit_button(
                'Save Payment Settings'
            );

            ?>

        </form>

    </div>

    <?php
}