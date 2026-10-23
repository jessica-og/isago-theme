<?php

if (!defined('ABSPATH')) {
    exit;
}

$menu = isago_get_menu();

?>

<div class="isago-mobile-overlay"></div>

<aside class="isago-mobile-menu">

    <!-- Close -->
    <button
        class="isago-mobile-close"
        type="button"
        aria-label="Close Menu"
    >
        <i class="fa-solid fa-xmark"></i>
    </button>


    <!-- Scrollable Menu -->
    <div class="isago-mobile-menu-scroll">




        <ul class="isago-mobile-nav">

            <?php foreach ($menu as $item) : ?>

                <?php

                include locate_template(
                    'template-parts/header/mobile-menu-item.php'
                );

                ?>

            <?php endforeach; ?>

        </ul>

    <!-- =========================================
             MOBILE AUTHENTICATION
        ========================================== -->

        <div class="isago-mobile-auth">

            <?php
            get_template_part(
                'template-parts/header/auth'
            );
            ?>

        </div>
      

    </div>

</aside>