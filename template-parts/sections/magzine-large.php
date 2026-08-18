<?php

if (!defined('ABSPATH')) {
    exit;
}

?>

<article class="magzine-large">

    <a
        class="magzine-large-image"
        href="<?php the_permalink(); ?>">

        <?php

        if (has_post_thumbnail()) {

            the_post_thumbnail(
                'large', 
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


    <div class="magzine-large-content">

        <div class="category-text">

            <?php

            $categories = get_the_category();

            if (!empty($categories)) {

                echo esc_html(
                    $categories[0]->name
                );

            }

            ?>

        </div>


        <h2 class="isago-big-post-title">

            <a href="<?php the_permalink(); ?>">

                <?php the_title(); ?>

            </a>

        </h2>


        <p>

            <?php

            echo wp_trim_words(
                get_the_excerpt(),
                28
            );

            ?>

        </p>


        <div class="magzine-meta">

            <span>
                <?php echo esc_html(get_the_date()); ?>
            </span>

            <span class="magzine-meta-separator">
                •
            </span>

            <span>
                <?php echo esc_html(get_the_author()); ?>
            </span>

        </div>

    </div>

</article>