<?php

if (!defined('ABSPATH')) {
    exit;
}

/*=========================================================
Browse AI Prompts Categories
=========================================================*/

$parent = get_category_by_slug('ai-prompts');

if (!$parent) {
    return;
}

/*
|--------------------------------------------------------------------------
| Get direct child categories
|--------------------------------------------------------------------------
*/

$children = get_categories([

    'taxonomy'   => 'category',

    'parent'     => $parent->term_id,

    'hide_empty' => true,

    'orderby'    => 'name',

    'order'      => 'ASC',
'number' => 8,
]);

if (empty($children)) {
    return;
}

?>

<section class="category-widget browse-prompts-widget">

    <h3 class="category-widget-title">

        Browse Prompts

    </h3>

    <ul class="category-list">

        <?php foreach ($children as $cat) : ?>

            <li>

                <a
                    href="<?php echo esc_url(
                        get_category_link($cat->term_id)
                    ); ?>"
                >

                    <span class="cat-name">

                        <?php echo esc_html(
                            $cat->name
                        ); ?>

                    </span>

                    <span class="cat-count">

                        <?php echo absint(
                            $cat->count
                        ); ?>

                    </span>

                </a>

            </li>

        <?php endforeach; ?>

    </ul>

</section>