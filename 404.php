<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Isago 404 Page
 *
 * @package Isago
 */

get_header();

?>

<main
    id="primary"
    class="isago-404-page"
    role="main"
>

    <?php

    get_template_part(
        'template-parts/404/404-page'
    );

    ?>

</main>

<?php

get_footer();