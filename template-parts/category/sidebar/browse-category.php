<?php

if (!defined('ABSPATH')) {
    exit;
}

$parent = get_category_by_slug('free-prompt-library');

if (!$parent) return;

$children = get_categories([
    'taxonomy'   => 'category',
    'child_of'   => $parent->term_id,
    'hide_empty' => true, // only show categories with posts
]);

if (empty($children)) return;
 
?>

<section class="category-widget">

    <h3>Browse Prompts</h3>

    <ul class="category-list">

        <?php foreach ($children as $cat) : ?>

            <li>

                <a href="<?php echo esc_url(get_category_link($cat->term_id)); ?>">

                    <span class="cat-name">
                        <?php echo esc_html($cat->name); ?>
                    </span>

                    <span class="cat-count">
                        <?php echo intval($cat->count); ?>
                    </span>

                </a>

            </li>

        <?php endforeach; ?>

    </ul>

</section>