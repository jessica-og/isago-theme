<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

?><!DOCTYPE html>

<html <?php language_attributes(); ?>>

<head>

<meta charset="<?php bloginfo('charset'); ?>">

<meta name="viewport"
content="width=device-width, initial-scale=1">

<script>
(function () {

    try {

        const savedTheme =
            localStorage.getItem('isago-theme');

        /*
         * Dark mode is the default.
         *
         * Only an explicitly saved "light" preference
         * should result in light mode.
         */

        if (savedTheme !== 'light') {

            document.documentElement.classList.add(
                'isago-dark-preload'
            );

        }

    } catch (error) {

        /*
         * If localStorage is unavailable,
         * dark mode remains the default.
         */

        document.documentElement.classList.add(
            'isago-dark-preload'
        );

    }

})();
</script>

<?php wp_head(); ?>

</head>

<body <?php body_class(); ?>>

<?php wp_body_open(); ?>

<?php

get_template_part(

'template-parts/header/site-header',

'header'

);

?>