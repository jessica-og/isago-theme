<?php

if (!defined('ABSPATH')) {
    exit;
}


/*
 * ----------------------------------------------------
 * HERO SETTINGS
 * ----------------------------------------------------
 */

$hero =
    isago_get_hero_settings();


/*
 * ----------------------------------------------------
 * FEATURED HERO
 *
 * This is the large center post.
 * It is selected separately in the admin.
 * ----------------------------------------------------
 */

$featured_query =
    isago_get_featured_hero(
        $hero['featured'] ?? 0
    );


/*
 * ----------------------------------------------------
 * LEFT HERO
 *
 * The left hero now supports:
 *
 * Blog
 * AI Prompts
 * AI Tools
 *
 * The content type and category are selected
 * independently in the Homepage Builder.
 * ----------------------------------------------------
 */

$left_type =
    $hero['left_type']
    ?? 'blog';


$left_source =
    $hero['left']
    ?? '';


$left_query =
    isago_get_left_hero(
        $left_type,
        $left_source
    );

?>


<section class="isago-hero">


    <div class="hero-grid">


        <?php

        /*
         * LEFT HERO
         */

        get_template_part(

            'template-parts/hero/hero-left',

            null,

            [

                'query' =>
                    $left_query,

                'type' =>
                    $left_type,

            ]

        );

        ?>


        <?php

        /*
         * MAIN FEATURED HERO
         */

        get_template_part(

            'template-parts/hero/hero-main',

            null,

            [

                'query' =>
                    $featured_query,

            ]

        );

        ?>


    </div>

</section>