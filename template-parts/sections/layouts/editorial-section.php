<?php

if (!defined('ABSPATH')) {
    exit;
}

$title        = $args['title'] ?? '';
$section_slug = $args['slug'] ?? '';
$link         = $args['link'] ?? '#';
$post_count   = $args['posts'] ?? 5;

$query = isago_get_category_posts(
    $section_slug,
    $post_count
);

if (!$query->have_posts()) {
    return;
}
?>

<section class="isago-editorial-section">

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

    <div class="editorial-grid">

 <?php

        /*
        -------------------------
        Featured Story
        -------------------------
        */

        $query->the_post();

        get_template_part(
            'template-parts/sections/editorial-large'
        );

        ?>

       

   <div class="editorial-right-grid">

<?php

while ($query->have_posts()) :

    $query->the_post();

    get_template_part(
        'template-parts/sections/editorial-small'
    );

endwhile;

?>

</div>

    </div>

</section>

<?php wp_reset_postdata(); ?>