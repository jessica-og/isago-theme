<?php

if (!defined('ABSPATH')) {
    exit;
}

$title = $args['title'] ?? '';

$slug = $args['slug'] ?? '';

$link = $args['link'] ?? '#';

$post_count = absint(
    $args['posts'] ?? 5
);

$query = isago_get_category_posts(
    $slug,
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

    <div class="list-layout">

        <?php

        while ($query->have_posts()) :

            $query->the_post();

        ?>

            <article class="list-item">

                <a
                    class="list-thumb"
                    href="<?php the_permalink(); ?>">

                <?php the_post_thumbnail('medium_large'); ?>

                </a>

                <div class="grid-content">

                    <div class="grid-category">

                    <?php

$cat = get_the_category();

echo !empty($cat)
    ? esc_html(strtoupper($cat[0]->name))
    : '';

?>

                    </div>

                    <h3>

                        <a href="<?php the_permalink(); ?>">

                            <?php the_title(); ?>

                        </a>

                    </h3>

                    <p>

                        <?php

                        echo wp_trim_words(

                            get_the_excerpt(),

                            20

                        );

                        ?>

                    </p>

                <div class="editorial-meta">

            <?php echo get_the_date(); ?>

            •

            <?php the_author(); ?>

        </div>

                </div>

            </article>  

        <?php

        endwhile;

        wp_reset_postdata();

        ?>

    </div>

</section>