<article class="editorial-small">

    <a
        class="editorial-small-thumb"
        href="<?php the_permalink(); ?>">

        <?php   echo isago_get_post_thumbnail(

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

    <div class="editorial-small-content">

        <div class="category-text cat-margin">

            <?php
            $cat = get_the_category();

            if (!empty($cat)) {
                echo esc_html($cat[0]->name);
            }
            ?>

        </div>

        <h4 class="isago-editorial-post-title">

            <a href="<?php the_permalink(); ?>">

                <?php the_title(); ?>

            </a>

        </h4>

        <div class="isago-post-meta">

            <?php echo get_the_date(); ?>

        </div>

    </div>

</article>