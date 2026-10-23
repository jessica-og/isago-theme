<?php

if (!defined('ABSPATH')) {
    exit;
}


/*
|--------------------------------------------------------------------------
| Build Menu Classes
|--------------------------------------------------------------------------
*/

$classes = [
    'mobile-item'
];


/*
|--------------------------------------------------------------------------
| WordPress Menu Classes
|--------------------------------------------------------------------------
*/

if (!empty($item->classes) && is_array($item->classes)) {

    foreach ($item->classes as $class) {

        if (!empty($class)) {

            $classes[] = sanitize_html_class($class);

        }

    }

}


/*
|--------------------------------------------------------------------------
| Has Children
|--------------------------------------------------------------------------
*/

if (!empty($item->children)) {

    $classes[] = 'mobile-has-children';

}


/*
|--------------------------------------------------------------------------
| Custom Isago Active State
|--------------------------------------------------------------------------
*/

if (!empty($item->isago_active)) {

    $classes[] = 'isago-active';

}


/*
|--------------------------------------------------------------------------
| Remove Duplicates
|--------------------------------------------------------------------------
*/

$classes = array_unique($classes);

?>

<li class="<?php echo esc_attr(implode(' ', $classes)); ?>">


    <div class="mobile-link-row">

        <a href="<?php echo esc_url($item->url); ?>">

            <?php echo esc_html($item->title); ?>

        </a>


        <?php if (!empty($item->children)) : ?>

            <button
                class="mobile-toggle"
                type="button"
                aria-expanded="false"
                aria-label="Toggle submenu"
            >

                <i class="fa-solid fa-plus"></i>

            </button>

        <?php endif; ?>

    </div>


    <?php if (!empty($item->children)) : ?>

        <ul class="mobile-submenu">


            <?php foreach ($item->children as $child) : ?>


                <?php

                $child_classes = [];


                /*
                |--------------------------------------------------------------------------
                | Child WordPress Classes
                |--------------------------------------------------------------------------
                */

                if (
                    !empty($child->classes) &&
                    is_array($child->classes)
                ) {

                    foreach ($child->classes as $class) {

                        if (!empty($class)) {

                            $child_classes[] =
                                sanitize_html_class($class);

                        }

                    }

                }


                /*
                |--------------------------------------------------------------------------
                | Child Has Children
                |--------------------------------------------------------------------------
                */

                if (!empty($child->children)) {

                    $child_classes[] =
                        'mobile-has-children';

                }


                /*
                |--------------------------------------------------------------------------
                | Child Active
                |--------------------------------------------------------------------------
                */

                if (!empty($child->isago_active)) {

                    $child_classes[] =
                        'isago-active';

                }


                $child_classes =
                    array_unique($child_classes);

                ?>


                <li class="<?php echo esc_attr(
                    implode(' ', $child_classes)
                ); ?>">


                    <a href="<?php echo esc_url(
                        $child->url
                    ); ?>">

                        <?php echo esc_html(
                            $child->title
                        ); ?>

                    </a>


                    <?php if (!empty($child->children)) : ?>

                        <ul class="mobile-submenu-level2">


                            <?php foreach (
                                $child->children
                                as $grandchild
                            ) : ?>


                                <?php

                                $grandchild_classes = [];


                                if (
                                    !empty($grandchild->classes) &&
                                    is_array($grandchild->classes)
                                ) {

                                    foreach (
                                        $grandchild->classes
                                        as $class
                                    ) {

                                        if (!empty($class)) {

                                            $grandchild_classes[] =
                                                sanitize_html_class(
                                                    $class
                                                );

                                        }

                                    }

                                }


                                if (
                                    !empty(
                                        $grandchild->isago_active
                                    )
                                ) {

                                    $grandchild_classes[] =
                                        'isago-active';

                                }


                                $grandchild_classes =
                                    array_unique(
                                        $grandchild_classes
                                    );

                                ?>


                                <li class="<?php echo esc_attr(
                                    implode(
                                        ' ',
                                        $grandchild_classes
                                    )
                                ); ?>">


                                    <a
                                        href="<?php echo esc_url(
                                            $grandchild->url
                                        ); ?>"
                                    >

                                        <?php echo esc_html(
                                            $grandchild->title
                                        ); ?>

                                    </a>


                                </li>


                            <?php endforeach; ?>


                        </ul>

                    <?php endif; ?>


                </li>


            <?php endforeach; ?>


        </ul>

    <?php endif; ?>


</li>