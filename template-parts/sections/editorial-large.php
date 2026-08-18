<article class="editorial-large">

    <a href="<?php the_permalink(); ?>">

        <?php the_post_thumbnail('large'); ?>

    </a>

    <div class="editorial-content">

        <div class="grid-category">

            <?php

            echo esc_html(
                get_the_category()[0]->name ?? ''
            );

            ?>

        </div>

        <h2>

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

        <div class="editorial-meta">

            <?php echo get_the_date(); ?>

            •

            <?php the_author(); ?>

        </div>

    </div>

</article>