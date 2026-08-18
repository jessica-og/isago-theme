<?php

if (!defined('ABSPATH')) {
    exit;
}

$title        = $args['title'] ?? '';
$section_slug = $args['slug'] ?? '';
$link         = $args['link'] ?? '#';
$post_count   = absint($args['posts'] ?? 4);

$query = isago_get_category_posts(
    $section_slug,
    $post_count
);

if (!$query->have_posts()) {
    return;
}

?>

<section class="isago-category-section">

    <?php

    get_template_part(

        'template-parts/sections/section-header',

        null,

        [

            'title' => $title,

            'link'  => $link

        ]

    );
  
    ?>

    <div class="isago-grid-layout">

        <?php

        while ($query->have_posts()) :

            $query->the_post();

            get_template_part(
                'template-parts/sections/grid-card'
            );

        endwhile;

        wp_reset_postdata();

        ?>

    </div>

</section>