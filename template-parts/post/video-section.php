<?php

if (!defined('ABSPATH')) {
    exit;
}

$query = isago_get_single_videos();

if (!$query->have_posts()) {
    return;
}

?>

<section class="single-video-section">

<div class="section-heading">

<h2>

Watch More Videos

</h2>

</div>

<div class="single-video-grid">

<?php

while ($query->have_posts()) :

$query->the_post();

get_template_part(

'template-parts/post/cards/video-card'

);

endwhile;

wp_reset_postdata();

?>

</div>

</section>