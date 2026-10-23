<?php

if (!defined('ABSPATH')) {
    exit;
}

$query = isago_get_tag_featured();

if (!$query->have_posts()) {
    return;
}

$query->the_post();

?>

<section class="category-featured">

    <?php

    get_template_part(
        'template-parts/category/cards/featured-card'
    );

    wp_reset_postdata();

    ?>

</section>