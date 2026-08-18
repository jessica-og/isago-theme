<?php

if (!defined('ABSPATH')) {
    exit;
}

?>

<div class="footer-column">

    <h4>

        Resources

    </h4>

    <?php

    wp_nav_menu([

        'theme_location' => 'footer_resources',

        'container'      => false,

        'menu_class'     => 'footer-links',

        'fallback_cb'    => false,

    ]);

    ?>

</div>