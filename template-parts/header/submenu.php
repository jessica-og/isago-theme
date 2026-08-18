<?php

if (!defined('ABSPATH')) {
    exit;
}

?>

<ul class="isago-submenu">

    <?php foreach ($submenu_item->children as $child) : ?>

        <?php

        $classes = [];

        /*
         * Existing WordPress classes
         */
        if (!empty($child->classes) && is_array($child->classes)) {

            foreach ($child->classes as $class) {

                if (!empty($class)) {
                    $classes[] = sanitize_html_class($class);
                }

            }

        }

        /*
         * Nested submenu
         */
        if (!empty($child->children)) {
            $classes[] = 'has-submenu';
        }

        /*
         * Isago active state
         */
        if (!empty($child->isago_active)) {
            $classes[] = 'isago-active';
        }

        $classes = array_unique($classes);

        ?>

        <li class="<?php echo esc_attr(implode(' ', $classes)); ?>">

            <a href="<?php echo esc_url($child->url); ?>">

                <span>
                    <?php echo esc_html($child->title); ?>
                </span>

                <?php if (!empty($child->children)) : ?>

                    <i class="fa-solid fa-chevron-right"></i>

                <?php endif; ?>

            </a>

            <?php

            if (!empty($child->children)) {

                $submenu_item = $child;

                include locate_template(
                    'template-parts/header/submenu.php'
                );

            }

            ?>

        </li>

    <?php endforeach; ?>

</ul>