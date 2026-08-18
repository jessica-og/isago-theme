<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
 * -----------------------------------------
 * Menu Item Classes
 * -----------------------------------------
 */

$classes = [];

if (!empty($item->classes) && is_array($item->classes)) {

    foreach ($item->classes as $class) {

        if (!empty($class)) {
            $classes[] = sanitize_html_class($class);
        }

    }
}

/*
 * Dropdown class
 */
if (!empty($item->children)) {
    $classes[] = 'has-dropdown';
}

/*
 * Custom Isago active class
 */
if (!empty($item->isago_active)) {
    $classes[] = 'isago-active';
}

$classes = array_unique($classes);

?>

<li class="<?php echo esc_attr(implode(' ', $classes)); ?>">

    <a href="<?php echo esc_url($item->url); ?>">

        <span>
            <?php echo esc_html($item->title); ?>
        </span>

        <?php if (!empty($item->children)) : ?>

            <i class="fa-solid fa-chevron-down"></i>

        <?php endif; ?>

    </a>

    <?php

    if (!empty($item->children)) {

        include locate_template(
            'template-parts/header/dropdown.php'
        );

    }

    ?>

</li>