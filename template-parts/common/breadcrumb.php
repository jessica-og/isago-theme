<?php

if (!defined('ABSPATH')) {
    exit;
}

?>

<nav class="isago-breadcrumb">

    <a href="<?php echo esc_url(home_url('/')); ?>">

        Home

    </a>

    <?php if (is_category()) : ?>

        <?php

        $category = get_queried_object();

        $parents = [];

        while ($category->parent != 0) {

            $category = get_category($category->parent);

            $parents[] = $category;
        }

        $parents = array_reverse($parents);

        foreach ($parents as $parent) :

        ?>

            <span>

                <i class="fa-solid fa-angle-right"></i>

            </span>

            <a href="<?php echo esc_url(get_category_link($parent->term_id)); ?>">

                <?php echo esc_html($parent->name); ?>

            </a>

        <?php endforeach; ?>

        <span>

            <i class="fa-solid fa-angle-right"></i>

        </span>

        <span class="breadcrumb-current">

            <?php echo esc_html(single_cat_title('', false)); ?>

        </span>

    <?php elseif (is_tag()) : ?>

        <span>

            <i class="fa-solid fa-angle-right"></i>

        </span>

        <span class="breadcrumb-current">

            <?php echo esc_html(single_tag_title('', false)); ?>

        </span>

    <?php endif; ?>

</nav>