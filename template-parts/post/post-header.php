<?php

if (!defined('ABSPATH')) {
    exit;
}

$category = get_the_category();
?>

<header class="post-header">

    <div class="post-category">

        <?php

        if (!empty($category)) {

            echo esc_html(strtoupper($category[0]->name));

        }

        ?>

    </div>

    <h1 class="post-title">

        <?php the_title(); ?>

    </h1>

    <?php if (has_excerpt()) : ?>

        <div class="post-excerpt">

            <?php the_excerpt(); ?>

        </div>

    <?php endif; ?>

    <div class="post-meta-row">

        <div class="post-meta">

            <div class="post-author">

                <?php

                echo get_avatar(
                    get_the_author_meta('ID'),
                    42
                );

                ?>

                <span>

                    <?php the_author(); ?>

                </span>

            </div>

            <span>

                <?php echo get_the_date(); ?>

            </span>

            <span>

                <?php echo get_comments_number(); ?> Comments

            </span>

            <span>

                <?php

                echo ceil(

                    str_word_count(

                        wp_strip_all_tags(

                            get_post_field(
                                'post_content',
                                get_the_ID()
                            )

                        )

                    ) / 220

                );

                ?>

                min read

            </span>

        </div>

        <?php

        get_template_part(
            'template-parts/post/post-share'
        );

        ?>

    </div>

    <?php if (has_post_thumbnail()) : ?>

        <div class="post-featured-image">

            <?php

            the_post_thumbnail(
                'full'
            );

            ?>

        </div>

    <?php endif; ?>

</header>