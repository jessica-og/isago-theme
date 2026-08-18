<?php

$settings = isago_get_sidebar_settings();

$query = isago_single_post_query(
    $settings['featured_post']
);

if (!$query->have_posts()) {
    return;
}

$query->the_post();

?>

<section class="sidebar-widget">

<a href="<?php the_permalink();?>">

<?php the_post_thumbnail('large');?>

</a>

<div class="sidebar-featured">

<div class="latest-date">

<?php echo get_the_date();?>

</div>

<h3>

<a href="<?php the_permalink();?>">

<?php the_title();?>

</a>

</h3>

</div>

</section>

<?php

wp_reset_postdata();