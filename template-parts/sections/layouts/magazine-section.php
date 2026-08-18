<?php

if (!defined('ABSPATH')) {
    exit;
}

$title = $args['title'] ?? '';

$section_slug = $args['slug'] ?? '';

$link = $args['link'] ?? '#';

$post_count = absint(
    $args['posts'] ?? 5
);

/*
|--------------------------------------------------------------------------
| Magazine Query
|--------------------------------------------------------------------------
|
| We need at least 5 posts:
| 1 featured story
| 4 smaller stories
|
*/

$query = isago_get_category_posts(
    $section_slug,
    max(5, $post_count)
);

if (!$query->have_posts()) {
    return;
}

?>

<section class="isago-magzine-section">

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

    <div class="magzine-grid">

        <!-- =====================================================
        FEATURED STORY
        ====================================================== -->

        <div class="magzine-featured">

            <?php

            $query->the_post();

            get_template_part(
                'template-parts/sections/magzine-large'
            );

            ?>

        </div>


        <!-- =====================================================
        SMALL STORIES
        ====================================================== -->

        <div class="magzine-small-grid">

            <?php

            while ($query->have_posts()) :

                $query->the_post();

                get_template_part(
                    'template-parts/sections/magzine-small'
                );

            endwhile;

            ?>

        </div>

    </div>

</section>

<?php

wp_reset_postdata();