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
    absint(
        $args['posts']
        ?? 4
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


    <div class="isago-grid-layout">

        <?php


        /*=================================================
        AI PROMPTS
        =================================================*/

        if (
            $type === 'ai_prompt'
        ) {

            isago_render_homepage_items(

                $items,

                'ai_prompt'

            );

        }


        /*=================================================
        AI TOOLS
        =================================================*/

        elseif (
            $type === 'ai_tool'
        ) {

            isago_render_homepage_items(

                $items,

                'ai_tool'

            );

        }


        /*=================================================
        BLOG
        =================================================*/

        else {

            while (
                $items->have_posts()
            ) {

                $items->the_post();


                get_template_part(
                    'template-parts/sections/grid-card'
                );

            }


            wp_reset_postdata();

        }

        ?>

    </div>

</section>