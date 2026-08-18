<article class="related-card">

<a href="<?php the_permalink(); ?>">

<?php the_post_thumbnail('medium'); ?>

</a>

<div class="related-content">

<div class="related-category">

<?php

echo esc_html(

get_the_category()[0]->name ?? ''

);

?>

</div>

<h3>

<a href="<?php the_permalink(); ?>">

<?php the_title(); ?>

</a>

</h3>

<div class="related-date">

<?php echo get_the_date(); ?>

</div>

</div>

</article>