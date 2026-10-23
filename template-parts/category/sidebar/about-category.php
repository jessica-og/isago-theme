<?php

if (!defined('ABSPATH')) {
    exit;
}


$category = get_queried_object();


if (!$category) {
    return;
}


/*
|--------------------------------------------------------------------------
| Recursive Category Count
|--------------------------------------------------------------------------
*/

$count = isago_get_category_total_articles(
    $category->term_id
);

?>


<section class="category-widget">

    <h3>

        About

        <?php echo esc_html(
            $category->name
        ); ?>

    </h3>


    <p>

        <?php

        echo !empty($category->description)

            ? esc_html(
                $category->description
            )

            : 'Explore the latest articles from this category.';

        ?>

    </p>


    <div class="category-widget-stat">

        <span>
            Total Articles
        </span>

        (

        <strong class="count-cat">

            <?php echo esc_html($count); ?>

        </strong>

        )

    </div>

</section>