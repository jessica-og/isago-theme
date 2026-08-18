<?php

if (!defined('ABSPATH')) {
    exit;
}


/*
 * ----------------------------------------------------
 * HERO SETTINGS
 * ----------------------------------------------------
 */

$hero = isago_get_hero_settings();


/*
 * ----------------------------------------------------
 * FEATURED HERO
 *
 * This is the large center post.
 * It is selected separately in the admin.
 * ----------------------------------------------------
 */

$featured_query = isago_get_featured_hero(
    $hero['featured'] ?? 0
);


/*
 * ----------------------------------------------------
 * LEFT HERO
 *
 * These are the two smaller posts.
 * ----------------------------------------------------
 */

$left_query = isago_get_left_hero(
    $hero['left'] ?? []
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
                'query' => $left_query
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
                'query' => $featured_query
            ]
        );
        ?>

    </div>

</section>