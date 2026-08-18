<?php

if (!defined('ABSPATH')) {
    exit;
}

?>

<div class="footer-column">

    <h4>

      Categories

    </h4>

    <?php

    wp_nav_menu([

        'theme_location' => 'footer_support',

        'container'      => false,

        'menu_class'     => 'footer-links',

        'fallback_cb'    => false,

    ]);

    ?>

</div>