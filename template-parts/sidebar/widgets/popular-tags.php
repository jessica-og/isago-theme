<section class="sidebar-widget">

<h3 class="sidebar-title">

POPULAR TAGS

</h3>

<div class="popular-tags">

<?php

$settings = isago_get_sidebar_settings();

$tags = get_tags([

'hide_empty'=>false,

'orderby'=>'count',

'order'=>'DESC',

'number'=>$settings['popular_tags_count']

]);

foreach($tags as $tag):

?>

<a href="<?php echo esc_url(get_tag_link($tag)); ?>">

<?php echo esc_html($tag->name); ?>

</a>

<?php endforeach; ?>

</div>

</section>