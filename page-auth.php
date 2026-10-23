<?php

/**
 * Isago Authentication Page
 *
 * Dedicated layout for:
 *
 * - Sign In
 * - Sign Up
 * - Lost Password
 * - Reset Password
 */

if (!defined('ABSPATH')) {
    exit;
}

?><!DOCTYPE html>

<html <?php language_attributes(); ?>>

<head>

    <meta charset="<?php bloginfo('charset'); ?>">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <?php wp_head(); ?>

</head>


<body <?php body_class('isago-auth-page'); ?>>

<?php

if (function_exists('wp_body_open')) {

    wp_body_open();
}

?>


<main class="isago-auth-page-main">


 


    <div class="isago-auth-page-content">

        <?php

        if (have_posts()) :

            while (have_posts()) :

                the_post();

                the_content();

            endwhile;

        endif;

        ?>

    </div>


</main>


<?php wp_footer(); ?>


</body>

</html>