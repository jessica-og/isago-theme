<?php

if (!defined('ABSPATH')) {
    exit;
}

$tag = get_queried_object();

if (!$tag) {
    return;
}

$count = (int) $tag->count;

?>

<section class="category-widget">

    <h3>

        About

        <?php echo esc_html($tag->name); ?>

    </h3>

    <p class="about-text">

        <?php

        echo !empty($tag->description)

            ? esc_html($tag->description)

            : 'Explore the latest articles from this tag.';

        ?>

    </p>

    <div class="category-widget-stat">

        <span>Total Articles</span>

      
        (

        <strong class="count-cat">

            <?php echo esc_html($count); ?>

        </strong>

        )

    </div>

</section>