<?php

if (!defined('ABSPATH')) {
    exit;
}
  
?>

<article class="grid-card">

    <a class="grid-image" href="<?php the_permalink(); ?>">

        <?php

           echo isago_get_post_thumbnail(

    get_the_ID(),

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