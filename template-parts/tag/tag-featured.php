<?php

if (!defined('ABSPATH')) {
    exit;
}

$tag = get_queried_object();

$query = new WP_Query([

    'post_type'      => 'post',

    'post_status'    => 'publish',

    'posts_per_page' => 1,

    'tag_id'         => $tag->term_id

]);

if ($query->have_posts()) {

    set_query_var(
        'isago_featured_post_id',
        $query->posts[0]->ID
    );
}

$post = $query->posts[0];

setup_postdata($post);

?>

<section class="category-featured">

    <?php

    get_template_part(
        'template-parts/category/cards/featured-card'
    );

    ?>

</section>

<?php

wp_reset_postdata();