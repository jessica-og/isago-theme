<?php

if (!defined('ABSPATH')) {
    exit;
}


$title =
    $args['title']
    ?? '';


$section_type =
    sanitize_key(
        $args['type']
        ?? 'blog'
    );


$section_slug =
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


$section_content =
    isago_get_home_section_content(

        [

            'type' =>
                $section_type,

            'slug' =>
                $section_slug,
     'orderby' => $args['orderby'] ?? 'latest',

        ],

        $post_count

    );


$items =
    $section_content['items']
    ?? [];


if (
    isago_get_homepage_item_count(
        $items
    ) === 0
) {

    return;

}

?>


<section class="isago-editorial-section">

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


    <div class="editorial-grid">


        <?php

        /*=================================================
        AI PROMPTS
        =================================================*/

        if (
            $section_type === 'ai_prompt'
        ) {

            /*
             * First prompt.
             */

            if (
                $items instanceof WP_Query
            ) {

                $items->the_post();

                isago_render_homepage_item(

                    get_post(),

                    'ai_prompt'

                );

                ?>

                <div class="editorial-right-grid">

                    <?php

                    while (
                        $items->have_posts()
                    ) {

                        $items->the_post();

                        isago_render_homepage_item(

                            get_post(),

                            'ai_prompt'

                        );

                    }

                    ?>

                </div>

                <?php

                wp_reset_postdata();

            }

        }


        /*=================================================
        AI TOOLS
        =================================================*/

        elseif (
            $section_type === 'ai_tool'
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


            ?>

            <div class="editorial-right-grid">

                <?php

                foreach (
                    $items
                    as $platform
                ) {

                    isago_render_homepage_item(

                        $platform,

                        'ai_tool'

                    );

                }

                ?>

            </div>

            <?php

        }


        /*=================================================
        BLOG
        =================================================*/

        else {

            if (
                $items instanceof WP_Query
            ) {

                $items->the_post();


                get_template_part(

                    'template-parts/sections/editorial-large'

                );


                ?>

                <div class="editorial-right-grid">

                    <?php

                    while (
                        $items->have_posts()
                    ) {

                        $items->the_post();


                        get_template_part(

                            'template-parts/sections/editorial-small'

                        );

                    }

                    ?>

                </div>

                <?php

                wp_reset_postdata();

            }

        }

        ?>

    </div>

</section>