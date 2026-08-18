<?php

if (!defined('ABSPATH')) {
    exit;
}
$settings = isago_get_category_archive_settings();
$query = get_query_var('category_query');

if (!$query || !$query->have_posts()) {
    return;
}

?>

<div
class="category-post-grid"
style="--isago-category-columns:<?php echo absint($settings['columns']); ?>;">

<?php

while ($query->have_posts()) :

    $query->the_post();

    get_template_part(
        'template-parts/category/cards/post-card'
    );

endwhile;

wp_reset_postdata();

?>

</div>