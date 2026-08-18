<?php

if (!defined('ABSPATH')) {
    exit;
}

$category = get_queried_object();

if (!$category) {
    return;
}

$count = (int) $category->count;

?>

<section class="category-widget">

    <h3>

        About

        <?php echo esc_html($category->name); ?>

    </h3>

    <p>

        <?php

        echo !empty($category->description)

            ? esc_html($category->description)

            : 'Explore the latest articles from this tag.';

        ?>

    </p>

    <div class="category-widget-stat">

        <span>Total Articles</span>

        <strong>

            <?php echo esc_html($count); ?>

        </strong>

    </div>

</section>