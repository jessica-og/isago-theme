<?php

if (!defined('ABSPATH')) {
    exit;
}

$category =
    get_queried_object();

$category_name =
    $category instanceof WP_Term
        ? $category->name
        : 'This category';

?>

<section class="isago-category-coming-soon">

    <div class="isago-category-coming-soon-inner">

        <span class="isago-category-coming-soon-kicker">
            Coming Soon
        </span>


        <h2 class="isago-category-coming-soon-title">

            More from
            <?php echo esc_html($category_name); ?>

        </h2>


        <p class="isago-category-coming-soon-text">

            We’re preparing content for this category.
            Check back soon for new articles, guides,
            tutorials and resources.

        </p>


        <a
            href="<?php echo esc_url(home_url('/')); ?>"
            class="isago-category-coming-soon-button"
        >

            Back to Home

            <i
                class="fa-solid fa-arrow-right"
                aria-hidden="true"
            ></i>

        </a>

    </div>

</section>