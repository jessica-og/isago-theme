<?php

if (!defined('ABSPATH')) {
    exit;
}


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
    max(
        5,
        absint(
            $args['posts']
            ?? 5
        )
    );


$content =
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
    $content['items']
    ?? [];


if (
    isago_get_homepage_item_count(
        $items
    ) === 0
) {

    return;

}

?>


<section class="isago-magzine-section">

    <?php

    get_template_part(

        'template-parts/sections/section-header',

        null,

        [

            'title' =>
                $title,

            'link' =>
                $link

        ]

    );

    ?>


    <div class="magzine-grid">


        <!-- =====================================================
        FEATURED
        ====================================================== -->

        <div class="magzine-featured">

            <?php

            if (
                $type === 'blog'
            ) {

                $items->the_post();


                get_template_part(

                    'template-parts/sections/magzine-large'

                );

            }

            elseif (
                $type === 'ai_prompt'
            ) {

                $items->the_post();


                isago_render_homepage_item(

                    get_post(),

                    'ai_prompt'

                );

            }

            elseif (
                $type === 'ai_tool'
            ) {

                $first =
                    array_shift(
                        $items
                    );


                if ($first) {

                    isago_render_homepage_item(

                        $first,

                        'ai_tool'

                    );

                }

            }

            ?>

        </div>


        <!-- =====================================================
        SMALL STORIES
        ====================================================== -->

        <div class="magzine-small-grid">

            <?php

            if (
                $type === 'blog'
            ) {

                while (
                    $items->have_posts()
                ) {

                    $items->the_post();


                    get_template_part(

                        'template-parts/sections/magzine-small'

                    );

                }

            }

            elseif (
                $type === 'ai_prompt'
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

            }

            elseif (
                $type === 'ai_tool'
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

            ?>

        </div>

    </div>

</section>


<?php

if (
    $items instanceof WP_Query
) {

    wp_reset_postdata();

}