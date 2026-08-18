<article class="single-video-card">

<div class="single-video-thumb">

<a href="<?php the_permalink(); ?>">

<?php echo isago_get_video_thumbnail(); ?>

</a>

<button

class="homepage-video-play"

data-video="<?php echo esc_url(isago_get_video_url(get_the_ID())); ?>">

<i class="fa-solid fa-play"></i>

</button>

</div>

<h3>

<a href="<?php the_permalink(); ?>">

<?php the_title(); ?>

</a>

</h3>

<div class="single-video-date">

<?php echo get_the_date(); ?>

</div>

</article>