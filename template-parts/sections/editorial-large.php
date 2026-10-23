<article class="editorial-large">

    <a href="<?php the_permalink(); ?>">

        <?php   echo isago_get_post_thumbnail(

    get_the_ID(),
                'large',
                [
                    'loading' => 'lazy',
                     'alt'     => the_title_attribute([
                        'echo' => false
                    ])
                ]
            );
            ?>
  
    </a>

    <div class="editorial-content">

        <div class="category-text cat-margin">

            <?php

            echo esc_html(
                get_the_category()[0]->name ?? ''
            );

            ?>

        </div>

        <h2 class="isago-big-post-title">

            <a href="<?php the_permalink(); ?>">

                <?php the_title(); ?>

            </a>

        </h2>

        <p class="isago-excerpt">

            <?php

            echo wp_trim_words(
                get_the_excerpt(),
                28
            );

            ?>

        </p>

       <?php

$theme = isago_get_theme_settings();

$author_name = !empty($theme['author_name'])
    ? $theme['author_name']
    : 'Isago';

?>

<div class="isago-post-meta">

    <?php echo esc_html(get_the_date()); ?>

    <span aria-hidden="true">•</span>

    <?php echo esc_html($author_name); ?>

</div>

    </div>

</article>