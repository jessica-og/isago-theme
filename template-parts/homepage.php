<?php

if (!defined('ABSPATH')) {
    exit;
}

?>

<section class="isago-homepage">

    <div class="container">

        <div class="homepage-grid">

            <main class="homepage-main">

                <?php

                /*
                |--------------------------------------------------------------------------
                | Hero
                |--------------------------------------------------------------------------
                */

                get_template_part(
                    'template-parts/hero/hero'
                );

                /*
                |--------------------------------------------------------------------------
                | Homepage Builder
                |--------------------------------------------------------------------------
                */

                $sections = isago_get_home_sections();

                foreach ($sections as $section) :

                    $category = get_category_by_slug(
                        $section['slug']
                    );

                    switch ($section['layout']) {

                        /*
                        ---------------------------------------------------------
                        Editorial (4 Cards)
                        ---------------------------------------------------------
                        */

                        case 'editorial':

                            $template =
                                'template-parts/sections/layouts/editorial-section';

                        break;

                        /*
                        ---------------------------------------------------------
                        Magazine
                        ---------------------------------------------------------
                        */

                        case 'magazine':

                            $template =
                                'template-parts/sections/layouts/magazine-section';

                        break;

                        /*
                        ---------------------------------------------------------
                        List
                        ---------------------------------------------------------
                        */

                        case 'list':

                            $template =
                                'template-parts/sections/layouts/list-section';

                        break;

                     
                        /*
                        ---------------------------------------------------------
                        Default
                        ---------------------------------------------------------
                        */

                        default:

                            $template =
                                'template-parts/sections/layouts/grid-section';

                    }

                    get_template_part(

                        $template,

                        null,

                        [

                            'title' => $section['title'],

                            'slug'  => $section['slug'],

                            'posts' => $section['posts'],

                            'link'  => $category
                                ? get_category_link($category)
                                : '#'

                        ]

                    );

                    /*
                    ---------------------------------------------------------
                    Advertisement
                    ---------------------------------------------------------
                    */

                    if ($section['show_ad']) {

                        get_template_part(
                            'template-parts/sections/advertisement'
                        );

                    }

                endforeach;

                ?>

            </main>

            <?php

            get_template_part(
                'template-parts/sidebar/sidebar'
            );

            ?>

        </div>

    </div>

</section>

<section class="homepage-video-wrapper">

    <div class="container">

        <?php

        get_template_part(
            'template-parts/sections/video-section'
        );

        ?>

    </div>

</section>