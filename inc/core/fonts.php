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

        'Space Grotesk' =>
            'https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300..700&display=swap',

        'Sora' =>
            'https://fonts.googleapis.com/css2?family=Sora:wght@100..800&display=swap',

        'Archivo' =>
            'https://fonts.googleapis.com/css2?family=Archivo:wght@100..900&display=swap',

        'Archivo Black' =>
            'https://fonts.googleapis.com/css2?family=Archivo+Black&display=swap',

        'Plus Jakarta Sans' =>
            'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@200..800&display=swap',

        'Red Hat Display' =>
            'https://fonts.googleapis.com/css2?family=Red+Hat+Display:wght@300..900&display=swap',

        'Barlow' =>
            'https://fonts.googleapis.com/css2?family=Barlow:wght@100;200;300;400;500;600;700;800;900&display=swap',

        'Barlow Condensed' =>
            'https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@100;200;300;400;500;600;700;800;900&display=swap',

        'Bebas Neue' =>
            'https://fonts.googleapis.com/css2?family=Bebas+Neue&display=swap',

        'Oswald' =>
            'https://fonts.googleapis.com/css2?family=Oswald:wght@200..700&display=swap',

        'Anton' =>
            'https://fonts.googleapis.com/css2?family=Anton&display=swap',

        'League Spartan' =>
            'https://fonts.googleapis.com/css2?family=League+Spartan:wght@100..900&display=swap',

        'Urbanist' =>
            'https://fonts.googleapis.com/css2?family=Urbanist:wght@100..900&display=swap',

        'Albert Sans' =>
            'https://fonts.googleapis.com/css2?family=Albert+Sans:wght@100..900&display=swap',

        'IBM Plex Sans' =>
            'https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@100;200;300;400;500;600;700&display=swap',

        'Public Sans' =>
            'https://fonts.googleapis.com/css2?family=Public+Sans:wght@100..900&display=swap',

        'Work Sans' =>
            'https://fonts.googleapis.com/css2?family=Work+Sans:wght@100..900&display=swap',

        'Figtree' =>
            'https://fonts.googleapis.com/css2?family=Figtree:wght@300..900&display=swap',

        'Rubik' =>
            'https://fonts.googleapis.com/css2?family=Rubik:wght@300..900&display=swap',

        'Exo 2' =>
            'https://fonts.googleapis.com/css2?family=Exo+2:wght@100..900&display=swap',

        'Rajdhani' =>
            'https://fonts.googleapis.com/css2?family=Rajdhani:wght@300;400;500;600;700&display=swap',

        'Titillium Web' =>
            'https://fonts.googleapis.com/css2?family=Titillium+Web:wght@200;300;400;600;700&display=swap',

        'Lexend' =>
            'https://fonts.googleapis.com/css2?family=Lexend:wght@100..900&display=swap',

        'Noto Sans' =>
            'https://fonts.googleapis.com/css2?family=Noto+Sans:wght@100..900&display=swap',

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