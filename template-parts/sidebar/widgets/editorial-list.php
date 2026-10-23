<?php

if (!defined('ABSPATH')) {
    exit;
}

$settings = isago_get_sidebar_settings();

$args = [

'post_type'=>'post',

'posts_per_page'=>$settings['editorial_posts'],

'ignore_sticky_posts'=>true

];

if(!empty($settings['editorial_category'])){

$args['category_name']=$settings['editorial_category'];

}

$query=new WP_Query($args);

if(!$query->have_posts()){

return;

}
?>

<section class="sidebar-widget">

<?php while ($query->have_posts()) : $query->the_post(); ?>

<article class="sidebar-mini-post">

    <a class="sidebar-mini-thumb"
       href="<?php the_permalink(); ?>">

        <?php     echo isago_get_post_thumbnail(

    get_the_ID(),
                'medium_large',
                [
                    'loading' => 'lazy',
                     'alt'     => the_title_attribute([
                        'echo' => false
                    ])
                ]
            );?>

    </a>

    <div>

        <h4 class="sidebar-post-title">

            <a href="<?php the_permalink(); ?>">

                <?php the_title(); ?>

            </a>
  
        </h4>

    </div>

</article>

<?php endwhile; ?>

</section>

<?php wp_reset_postdata(); ?>