<?php

if (!defined('ABSPATH')) {
    exit;
}

$query = isago_get_related_posts();

if (!$query->have_posts()) {
    return;
}

?>

<section class="related-posts">

<div class="section-heading">

<h2>

You May Also Like

</h2>

<a href="<?php echo esc_url(get_category_link(get_the_category()[0])); ?>">

View All

</a>

</div>

<div class="related-grid">

<?php

while ($query->have_posts()) :

$query->the_post();

get_template_part(

'template-parts/post/cards/related-card'

);

endwhile;

wp_reset_postdata();

?>

</div>

</section>