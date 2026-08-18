<?php

if (!defined('ABSPATH')) {
    exit;
}

add_action(
    'wp_enqueue_scripts',
    'isago_enqueue_fonts'
);

function isago_enqueue_fonts()
{
    $theme = isago_get_theme_settings();

    $font = $theme['font_family'] ?? 'Inter';

    $fonts = [

        'Inter' =>
            'https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap',

        'Nunito' =>
            'https://fonts.googleapis.com/css2?family=Nunito:wght@200..1000&display=swap',

        'Poppins' =>
            'https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap',

        'Roboto' =>
            'https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap',

        'Open Sans' =>
            'https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;500;600;700;800&display=swap',

        'Montserrat' =>
            'https://fonts.googleapis.com/css2?family=Montserrat:wght@100..900&display=swap',

        'Manrope' =>
            'https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&display=swap',

        'Outfit' =>
            'https://fonts.googleapis.com/css2?family=Outfit:wght@100..900&display=swap',

        'Lora' =>
            'https://fonts.googleapis.com/css2?family=Lora:wght@400;500;600;700&display=swap',

        'Playfair Display' =>
            'https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800;900&display=swap',

        'Merriweather' =>
            'https://fonts.googleapis.com/css2?family=Merriweather:wght@300;400;700;900&display=swap',

        'Cormorant Garamond' =>
            'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600;700&display=swap',

        'Libre Baskerville' =>
            'https://fonts.googleapis.com/css2?family=Libre+Baskerville:wght@400;700&display=swap',

        'Source Serif 4' =>
            'https://fonts.googleapis.com/css2?family=Source+Serif+4:wght@200..900&display=swap',

        'Crimson Text' =>
            'https://fonts.googleapis.com/css2?family=Crimson+Text:wght@400;600;700&display=swap',

        'DM Serif Display' =>
            'https://fonts.googleapis.com/css2?family=DM+Serif+Display&display=swap',

    ];

    if (isset($fonts[$font])) {

        wp_enqueue_style(
            'isago-font',
            $fonts[$font],
            [],
            null
        );
    }
}