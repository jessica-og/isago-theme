<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Theme Setup
 */

function isago_theme_setup() {

    /*
     * Translation
     */

    load_theme_textdomain(
        'isago',
        get_template_directory() . '/languages'
    );

    /*
     * Featured Images
     */

    add_theme_support( 'post-thumbnails' );

    /*
     * Dynamic Title
     */

    add_theme_support( 'title-tag' );

    /*
     * HTML5
     */

    add_theme_support(
        'html5',
        array(
            'search-form',
            'gallery',
            'caption',
            'script',
            'style'
        )
    );

    /*
     * Responsive Embeds
     */

    add_theme_support( 'responsive-embeds' );

    /*
     * Wide Alignments
     */

    add_theme_support( 'align-wide' );

    /*
     * Custom Logo
     */

    add_theme_support(
        'custom-logo',
        array(
            'height'      => 60,
            'width'       => 220,
            'flex-width'  => true,
            'flex-height' => true,
        )
    );

}

add_action(
    'after_setup_theme',
    'isago_theme_setup'
);