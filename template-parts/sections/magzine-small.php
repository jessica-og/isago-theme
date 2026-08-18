<?php

if (!defined('ABSPATH')) {
    exit;
}

?>

<article class="magzine-small">

    <a
        class="magzine-small-image"
        href="<?php the_permalink(); ?>">

        <?php

        if (has_post_thumbnail()) {

            the_post_thumbnail(
                'medium_large',
                [
                    'loading' => 'lazy',
                     'alt'     => the_title_attribute([
                        'echo' => false
                    ])
                ]
            );

        } else {

            ?>

            <img
                src="<?php echo esc_url(
                    get_template_directory_uri()
                    . '/assets/images/placeholder.png'
                ); ?>"
                alt="<?php the_title_attribute(); ?>"
                loading="lazy">

            <?php

        }

        ?>

    </a>


    <div class="magzine-small-content">

        <div class="magzine-category">

            <?php

            $categories = get_the_category();

            if (!empty($categories)) {

                echo esc_html(
                    $categories[0]->name
                );

            }

            ?>

        </div>


        <h3>

            <a href="<?php the_permalink(); ?>">

                <?php the_title(); ?>

            </a>

        </h3>


        <div class="magzine-small-meta">

            <?php echo esc_html(get_the_date()); ?>

        </div>

    </div>

</article>