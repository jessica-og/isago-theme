<?php

if (!defined('ABSPATH')) {
    exit;
}

$menu = isago_get_menu();

?>

<nav class="isago-navigation">

    <div class="container">

        <ul class="isago-menu">

            <?php foreach ($menu as $item) : ?>

                <?php

                include locate_template(
                    'template-parts/header/menu-item.php'
                );

                ?>

            <?php endforeach; ?>

        </ul>

    </div>

</nav>