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

<?php     echo isago_get_post_thumbnail(

    get_the_ID(),
                'large',
                [
                    'loading' => 'lazy',
                     'alt'     => the_title_attribute([
                        'echo' => false
                    ])
                ]
            );

?>

</a>

<div class="sidebar-featured">

<div class="isago-post-meta">

<?php echo get_the_date();?>

</div>

<h3 class="sidebar-post-title cat-margin">

<a href="<?php the_permalink();?>">

<?php the_title();?>

</a>

</h3>

</div>

</section>

<?php

wp_reset_postdata();