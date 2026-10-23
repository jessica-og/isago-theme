<?php

if (!defined('ABSPATH')) {
    exit;
}


$query =
    $args['query']
    ?? null;


$type =
    sanitize_key(
        $args['type']
        ?? 'blog'
    );


if (
    !$query
    ||
    !$query->have_posts()
) {

    return;

}

?>


<div class="hero-left">

    <?php

    $count = 0;

    while (
        $query->have_posts()
        &&
        $count < 2
    ) :

        $query->the_post();


        /*=================================================
        GET CONTENT CATEGORY
        =================================================*/

        $categories = [];


        /*
         * Blog
         */

        if (
            $type === 'blog'
        ) {

            $categories =
                get_the_category();

        }


        /*
         * AI Prompt
         */

        elseif (
            $type === 'ai_prompt'
        ) {

            $categories =
                get_the_terms(
                    get_the_ID(),
                    'ai_prompt_category'
                );


            if (
                is_wp_error($categories)
            ) {

                $categories = [];

            }

        }

    ?>

        <article class="hero-side-card">

            <a
                href="<?php the_permalink(); ?>"
                aria-label="<?php echo esc_attr(get_the_title()); ?>"
            >

                <?php

                echo isago_get_post_thumbnail(

                    get_the_ID(),

                    'medium_large',

                    [

                        'loading' =>
                            $count === 0
                                ? 'eager'
                                : 'lazy'

                    ]

                );

                ?>

            </a>


            <div class="hero-side-content">


                <?php if (!empty($categories)) : ?>

                    <div class="category-text isago-post-category">

                        <?php echo esc_html(
                            $categories[0]->name
                        ); ?>

                    </div>

                <?php endif; ?>


                <h3 class="isago-small-post-title">

                    <a href="<?php the_permalink(); ?>">

                        <?php the_title(); ?>

                    </a>

                </h3>


                <?php

                $theme =
                    isago_get_theme_settings();


                $author_name =
                    !empty(
                        $theme['author_name']
                    )
                        ? $theme['author_name']
                        : 'Isago';

                ?>


                <div class="isago-post-meta">

                    <?php echo esc_html(
                        get_the_date()
                    ); ?>

                    <span aria-hidden="true">
                        •
                    </span>

                    <?php echo esc_html(
                        $author_name
                    ); ?>

                </div>


            </div>

        </article>

    <?php

        $count++;

    endwhile;


    wp_reset_postdata();

    ?>

</div>