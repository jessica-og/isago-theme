<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * ---------------------------------------------------------
 * ISAGO IMAGEKIT ACCOUNTS
 * ---------------------------------------------------------
 *
 * Automatically discovers all ImageKit accounts configured
 * in wp-config.php.
 *
 * Supported constants:
 *
 *     ISAGO_IMAGEKIT_ACCOUNT_1
 *     ISAGO_IMAGEKIT_ACCOUNT_2
 *     ISAGO_IMAGEKIT_ACCOUNT_3
 *     ...
 *
 * Private keys remain in wp-config.php.
 *
 * This file does not store private keys in the database.
 */


/**
 * ---------------------------------------------------------
 * GET ALL IMAGEKIT ACCOUNTS
 * ---------------------------------------------------------
 *
 * Automatically discovers sequential ImageKit account
 * constants.
 *
 * Example:
 *
 *     ISAGO_IMAGEKIT_ACCOUNT_1
 *     ISAGO_IMAGEKIT_ACCOUNT_2
 *     ISAGO_IMAGEKIT_ACCOUNT_3
 *
 * becomes:
 *
 *     account_1
 *     account_2
 *     account_3
 *
 * @return array
 */
function isago_get_imagekit_accounts()
{
    $accounts = [];

    /*
     * Look for ImageKit accounts sequentially.
     *
     * We stop after the first missing account number.
     *
     * This means accounts should be numbered:
     *
     * 1, 2, 3, 4...
     */
    for ($number = 1; $number <= 100; $number++) {

        $constant_name =
            'ISAGO_IMAGEKIT_ACCOUNT_' . $number;


        /*
         * Account does not exist.
         */
        if (!defined($constant_name)) {

            /*
             * Stop at the first missing account.
             */
            break;
        }


        $account =
            constant($constant_name);


        /*
         * Account configuration must be an array.
         */
        if (!is_array($account)) {
            continue;
        }


        $account_id =
            'account_' . $number;


        /*
         * Basic required information.
         */
        $imagekit_id =
            sanitize_text_field(
                $account['id'] ?? ''
            );


        $public_key =
            sanitize_text_field(
                $account['public_key'] ?? ''
            );


        /*
         * IMPORTANT:
         *
         * Do NOT run the private key through a text
         * sanitizer.
         *
         * It is a secret credential and must be passed
         * unchanged to the ImageKit API authentication layer.
         */
        $private_key =
            $account['private_key'] ?? '';


        $url =
            esc_url_raw(
                $account['url'] ?? ''
            );


        $name =
            sanitize_text_field(
                $account['name']
                ??
                'ImageKit Account ' . $number
            );


        /*
         * An account without the basic credentials is not
         * usable, so don't register it.
         */
        if (
            empty($imagekit_id)
            ||
            empty($public_key)
            ||
            empty($private_key)
            ||
            empty($url)
        ) {

            continue;
        }


        /*
         * Store the normalized account.
         */
        $accounts[$account_id] = [

            'id' =>
                $imagekit_id,

            'public_key' =>
                $public_key,

            'private_key' =>
                $private_key,

            'url' =>
                $url,

            'name' =>
                $name,

        ];
    }


    return $accounts;
}


/**
 * ---------------------------------------------------------
 * GET ONE IMAGEKIT ACCOUNT
 * ---------------------------------------------------------
 *
 * @param string $account_id
 *
 * @return array|null
 */
function isago_get_imagekit_account(
    $account_id
) {

    $account_id =
        sanitize_key(
            $account_id
        );


    if (!$account_id) {
        return null;
    }


    $accounts =
        isago_get_imagekit_accounts();


    if (
        empty($accounts[$account_id])
    ) {

        return null;
    }


    return $accounts[$account_id];
}


/**
 * ---------------------------------------------------------
 * GET DEFAULT IMAGEKIT ACCOUNT
 * ---------------------------------------------------------
 *
 * The first configured account becomes the default.
 *
 * @return array|null
 */
function isago_get_default_imagekit_account()
{
    $accounts =
        isago_get_imagekit_accounts();


    if (empty($accounts)) {
        return null;
    }


    $first_key =
        array_key_first(
            $accounts
        );


    return
        $accounts[$first_key]
        ??
        null;
}