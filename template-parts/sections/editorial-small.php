<article class="editorial-small">

    <a
        class="editorial-small-thumb"
        href="<?php the_permalink(); ?>">

        <?php the_post_thumbnail('medium_large'); ?>

    </a>

    <div class="editorial-small-content">

        <div class="grid-category">

            <?php
            $cat = get_the_category();

            if (!empty($cat)) {
                echo esc_html($cat[0]->name);
            }
            ?>

        </div>

        <h4>

            <a href="<?php the_permalink(); ?>">

                <?php the_title(); ?>

            </a>

        </h4>

        <div class="grid-date">

            <?php echo get_the_date(); ?>

        </div>

    </div>

</article>