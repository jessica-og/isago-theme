<?php

if (!defined('ABSPATH')) {
    exit;
}

$query = isago_get_category_featured();

if (!$query->have_posts()) {
    return;
}

/*
---------------------------------------------------------
Save featured post ID
---------------------------------------------------------
*/

set_query_var(
    'isago_category_featured_post_id',
    $query->posts[0]->ID
);

while ($query->have_posts()) :

    $query->the_post();

    get_template_part(
        'template-parts/category/cards/featured-card'
    );

endwhile;

wp_reset_postdata();