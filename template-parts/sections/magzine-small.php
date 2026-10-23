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

    echo isago_get_post_thumbnail(

    get_the_ID(),
                'medium_large',
                [
                    'loading' => 'lazy',
                     'alt'     => the_title_attribute([
                        'echo' => false
                    ])
                ]
            );

        ?>

    </a>


    <div class="magzine-small-content">

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


        <h3 class="isago-small-post-title">

            <a href="<?php the_permalink(); ?>">

                <?php the_title(); ?>

            </a>

        </h3>


        <div class="isago-post-meta">

            <?php echo esc_html(get_the_date()); ?>

        </div>

    </div>

</article>