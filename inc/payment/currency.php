<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
CURRENCY SETTINGS
=========================================================*/

function isago_get_supported_currencies()
{
    return array(

        'USD' => array(
            'name'   => 'US Dollar',
            'symbol' => '$',
        ),

        'EUR' => array(
            'name'   => 'Euro',
            'symbol' => '€',
        ),

        'GBP' => array(
            'name'   => 'British Pound',
            'symbol' => '£',
        ),

        'NGN' => array(
            'name'   => 'Nigerian Naira',
            'symbol' => '₦',
        ),
    );
}


/*=========================================================
GET CURRENCY SETTINGS
=========================================================*/

function isago_get_currency_settings()
{
    $settings =
        get_option(
            'isago_payment_settings',
            array()
        );


    if (!is_array($settings)) {

        $settings = array();

    }


    $supported =
        isago_get_supported_currencies();


    $currency =
        isset($settings['currency'])
            ? strtoupper(
                sanitize_key(
                    $settings['currency']
                )
            )
            : 'NGN';


    if (!isset($supported[$currency])) {

        $currency = 'NGN';

    }


    return array(
        'currency' => $currency,
    );
}


/*=========================================================
GET CURRENCY CODE
=========================================================*/

function isago_get_currency_code()
{
    $settings =
        isago_get_currency_settings();


    return $settings['currency'];
}


/*=========================================================
GET CURRENCY INFORMATION
=========================================================*/

function isago_get_currency_info(
    $currency_code = ''
) {

    $supported =
        isago_get_supported_currencies();


    if (empty($currency_code)) {

        $currency_code =
            isago_get_currency_code();

    }


    $currency_code =
        strtoupper(
            sanitize_key(
                $currency_code
            )
        );


    if (!isset($supported[$currency_code])) {

        $currency_code = 'NGN';

    }


    return array(
        'code'   => $currency_code,
        'name'   => $supported[$currency_code]['name'],
        'symbol' => $supported[$currency_code]['symbol'],
    );
}


/*=========================================================
GET CURRENCY SYMBOL
=========================================================*/

function isago_get_currency_symbol()
{
    $currency =
        isago_get_currency_info();


    return $currency['symbol'];
}


/*=========================================================
NORMALIZE MONEY VALUE
=========================================================*/

function isago_normalize_currency_amount(
    $amount
) {

    if (!is_numeric($amount)) {

        return '0.00';

    }


    $amount =
        (float) $amount;


    if ($amount < 0) {

        $amount = 0;

    }


    return number_format(
        $amount,
        2,
        '.',
        ''
    );
}


/*=========================================================
FORMAT MONEY
=========================================================*/

function isago_format_currency(
    $amount
) {

    $currency =
        isago_get_currency_info();


    $amount =
        isago_normalize_currency_amount(
            $amount
        );


    return $currency['symbol']
        . number_format(
            (float) $amount,
            2,
            '.',
            ','
        );
}