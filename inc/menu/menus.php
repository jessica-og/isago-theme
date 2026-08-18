<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Register Menus
 */

function isago_register_menus() {

register_nav_menus(

[

'primary'           => __('Primary Menu','isago'),

'topbar'            => __('Top Menu','isago'),

'footer'            => __('Footer Menu','isago'),

'footer_explore'    => __('Footer Explore','isago'),

'footer_resources'  => __('Footer Resources','isago'),

'footer_support'    => __('Footer Support','isago'),

'footer_bottom'     => __('Footer Bottom','isago'),

]

);

}

add_action(
    'after_setup_theme',
    'isago_register_menus'
);