<?php

if (!defined('ABSPATH')) {
    exit;
}

?>

<article class="category-featured-card">

    <a
        class="category-featured-image"
        href="<?php the_permalink(); ?>">

        <?php

        if (has_post_thumbnail()) {

            the_post_thumbnail(
                'large'
            );

        }

        ?>

    </a>

    <div class="category-featured-content">

        <div class="editorial-category">

            <?php

            $category = get_the_category();

            echo !empty($category)
                ? esc_html(strtoupper($category[0]->name))
                : '';

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
                32
            );

            ?>

        </p>

     <div class="category-featured-meta">

    <div class="category-author">

        <?php echo get_avatar(get_the_author_meta('ID'),32); ?>

        <span>

            By <?php the_author(); ?>

        </span>

    </div>

    <div class="category-featured-stats">

        <span>

            <?php echo get_the_date(); ?>

        </span>

        <span>

            •

        </span>

        <span>

            <?php echo number_format(get_comments_number()); ?>

            comments

        </span>

    </div>

</div>

    </div>

</article>