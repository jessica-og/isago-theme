<?php

if (!defined('ABSPATH')) {
    exit;
}

$settings = isago_get_home_video_settings();

$query = isago_get_homepage_videos(

    $settings['category'],

    $settings['posts']

);

if (!$query->have_posts()) {
    return;
}

$category = get_category_by_slug(
    $settings['category']
);

?>
<section class="homepage-video-section">

<?php

get_template_part(

'template-parts/sections/section-header',

null,

[

'title'=>$settings['title'],

'link'=>$category
?get_category_link($category)
:'#'

]

);

?>

<div class="homepage-video-grid">

<?php while($query->have_posts()):

$query->the_post();

?>

<article class="homepage-video-card">

<a

class="homepage-video-thumb"

href="<?php the_permalink();?>">

<?php the_post_thumbnail('large');?>

<span class="homepage-video-play">

<i class="fa-solid fa-play"></i>

</span>

</a>

<div class="homepage-video-content">

<div class="homepage-video-badge">

VIDEO

</div>

<h3>

<a href="<?php the_permalink();?>">

<?php the_title();?>

</a>

</h3>

<div class="homepage-video-meta">

<?php echo get_the_date();?>

•

<?php

echo esc_html(

get_post_meta(

get_the_ID(),

'isago_video_duration',

true

)

?:'5:30'

);

?>

</div>

</div>

</article>

<?php endwhile;?>

</div>

</section>

<?php wp_reset_postdata();?>