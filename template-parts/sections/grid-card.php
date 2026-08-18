<?php

if (!defined('ABSPATH')) {
    exit;
}

?>

<article class="grid-card">

    <a class="grid-image" href="<?php the_permalink(); ?>">

        <?php

        if (has_post_thumbnail()) {

            the_post_thumbnail(

                'medium_large',

                [

                    'loading' => 'lazy',

                    'alt' => the_title_attribute(
                        [
                            'echo' => false
                        ]
                    )

                ]

            );

        } else {

        ?>

            <img

                src="<?php echo esc_url(get_template_directory_uri()); ?>/assets/images/placeholder.png"

                alt="<?php the_title_attribute(); ?>">

        <?php

        }

        ?>

    </a>

    <div class="grid-content">

        <div class="cat-margin category-text">

            <?php

            $cat = get_the_category();

            echo !empty($cat)
                ? esc_html(strtoupper($cat[0]->name))
                : '';

            ?>

        </div>

        <h3 class="isago-small-post-title">

            <a href="<?php the_permalink(); ?>">

                <?php the_title(); ?>

            </a>

        </h3>

        <p class="isago-excerpt">

            <?php echo wp_trim_words(get_the_excerpt(), 12); ?>

        </p>

        <div class="isago-post-meta">

        <?php echo get_the_date(); ?>

            •

            <?php the_author(); ?>

        </div>

    </div>

</article>