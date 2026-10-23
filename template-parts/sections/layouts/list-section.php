<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
SECTION SETTINGS
=========================================================*/

$title =
    $args['title']
    ?? '';


$type =
    sanitize_key(
        $args['type']
        ?? 'blog'
    );


$slug =
    $args['slug']
    ?? '__all';


$link =
    $args['link']
    ?? '#';


$post_count =
    absint(
        $args['posts']
        ?? 5
    );


/*=========================================================
GET SECTION CONTENT
=========================================================*/

$section_content =
    isago_get_home_section_content(

        [

            'type' =>
                $type,

            'slug' =>
                $slug,
'orderby' => $args['orderby'] ?? 'latest',
        ],

        $post_count

    );


$items =
    $section_content['items']
    ?? [];


/*=========================================================
CHECK FOR CONTENT
=========================================================*/

if (
    $items instanceof WP_Query
) {

    if (
        !$items->have_posts()
    ) {

        return;

    }

} elseif (
    is_array($items)
) {

    if (
        empty($items)
    ) {

        return;

    }

} else {

    return;

}

?>


<section class="isago-category-section">


    <?php

    get_template_part(

        'template-parts/sections/section-header',

        null,

        [

            'title' =>
                $title,

            'link' =>
                $link,

        ]

    );

    ?>


    <div class="list-layout">


        <?php

        /*=====================================================
        AI PROMPTS
        =====================================================*/

        if (
            $type === 'ai_prompt'
        ) {

            if (
                $items instanceof WP_Query
            ) {

                while (
                    $items->have_posts()
                ) {

                    $items->the_post();


                    isago_render_homepage_item(

                        get_post(),

                        'ai_prompt'

                    );

                }


                wp_reset_postdata();

            }

        }


        /*=====================================================
        AI TOOLS
        =====================================================*/

        elseif (
            $type === 'ai_tool'
        ) {

            if (
                is_array($items)
            ) {

                foreach (
                    $items
                    as $platform
                ) {

                    isago_render_homepage_item(

                        $platform,

                        'ai_tool'

                    );

                }

            }

        }


        /*=====================================================
        BLOG POSTS
        =====================================================*/

        elseif (
            $type === 'blog'
        ) {

            if (
                $items instanceof WP_Query
            ) {

                while (
                    $items->have_posts()
                ) :

                    $items->the_post();

                    ?>

                    <article class="list-item">

 
                        <!-- =====================================
                        THUMBNAIL
                        ====================================== -->

                        <a
                            class="list-thumb"
                            href="<?php the_permalink(); ?>"
                            aria-label="<?php echo esc_attr(
                                get_the_title()
                            ); ?>"  
                        >

                            <?php

                            echo isago_get_post_thumbnail(

                                get_the_ID(),

                                'medium_large',

                                [

                                    'loading' =>
                                        'lazy',

                                    'alt' =>
                                        the_title_attribute([
                                            'echo' =>
                                                false
                                        ])

                                ]

                            );

                            ?>

                        </a>


                        <!-- =====================================
                        CONTENT
                        ====================================== -->

                        <div class="list-content">


                            <!-- CATEGORY -->

                            <div class="category-text">

                                <?php

                                $categories =
                                    get_the_category();


                                if (
                                    !empty(
                                        $categories
                                    )
                                ) {

                                    echo esc_html(

                                        strtoupper(
                                            $categories[0]->name
                                        )

                                    );

                                }

                                ?>

                            </div>


                            <!-- TITLE -->

                            <h3
                                class="isago-editorial-post-title"
                            >

                                <a
                                    href="<?php the_permalink(); ?>"
                                >

                                    <?php the_title(); ?>

                                </a>

                            </h3>


                            <!-- EXCERPT -->

                            <p class="isago-excerpt">

                                <?php

                                echo wp_trim_words(

                                    get_the_excerpt(),

                                    20

                                );

                                ?>

                            </p>


                            <!-- AUTHOR -->

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


                            <!-- META -->

                            <div class="isago-post-meta">


                                <span>

                                    <?php

                                    echo esc_html(
                                        get_the_date()
                                    );

                                    ?>

                                </span>


                                <span
                                    aria-hidden="true"
                                >

                                    •

                                </span>


                                <span>

                                    <?php

                                    echo esc_html(
                                        $author_name
                                    );

                                    ?>

                                </span>


                            </div>


                        </div>


                    </article>

                    <?php

                endwhile;


                wp_reset_postdata();

            }

        }

        ?>


    </div>


</section>