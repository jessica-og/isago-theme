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


                foreach (
                    $sections
                    as $section
                ) :

                    /*
                    |--------------------------------------------------------------------------
                    | SECTION TYPE
                    |--------------------------------------------------------------------------
                    */

                    $section_type =
                        sanitize_key(
                            $section['type']
                            ?? 'blog'
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | SECTION SOURCE
                    |--------------------------------------------------------------------------
                    */

                    $section_slug =
                        $section['slug']
                        ?? '__all';


                    /*
                    |--------------------------------------------------------------------------
                    | HOMEPAGE AD SLOT
                    |--------------------------------------------------------------------------
                    |
                    | Use the original section number instead of the
                    | loop index. This keeps ad slots stable when a
                    | section is disabled.
                    |
                    */

                    $section_number =
                        absint(
                            $section['number']
                            ?? 0
                        );


                    if (
                        $section_number > 0
                    ) {

                        $homepage_ad_slot =
                            'homepage'
                            . $section_number;

                    } else {

                        $homepage_ad_slot =
                            'homepage1';

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | SECTION LINK
                    |--------------------------------------------------------------------------
                    |
                    | All View All URL logic is handled centrally by:
                    |
                    | isago_get_home_section_url()
                    |
                    */

                    $section_link =
                        isago_get_home_section_url(

                            $section_type,

                            $section_slug,
                            

                        );


                    /*
                    |--------------------------------------------------------------------------
                    | LAYOUT
                    |--------------------------------------------------------------------------
                    */

                    switch (
                        $section['layout']
                        ?? 'grid'
                    ) {


                        /*
                        |--------------------------------------------------------------------------
                        | Editorial
                        |--------------------------------------------------------------------------
                        */

                        case 'editorial':

                            $template =
                                'template-parts/sections/layouts/editorial-section';

                            break;


                        /*
                        |--------------------------------------------------------------------------
                        | Magazine
                        |--------------------------------------------------------------------------
                        */

                        case 'magazine':

                            $template =
                                'template-parts/sections/layouts/magazine-section';

                            break;


                        /*
                        |--------------------------------------------------------------------------
                        | List
                        |--------------------------------------------------------------------------
                        */

                        case 'list':

                            $template =
                                'template-parts/sections/layouts/list-section';

                            break;


                        /*
                        |--------------------------------------------------------------------------
                        | Grid
                        |--------------------------------------------------------------------------
                        */

                        case 'grid':

                            $template =
                                'template-parts/sections/layouts/grid-section';

                            break;


                        /*
                        |--------------------------------------------------------------------------
                        | Default
                        |--------------------------------------------------------------------------
                        */

                        default:

                            $template =
                                'template-parts/sections/layouts/grid-section';

                            break;

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | RENDER HOMEPAGE SECTION
                    |--------------------------------------------------------------------------
                    */

                    get_template_part(

                        $template,

                        null,

                        [

                            'title' =>
                                $section['title']
                                ?? '',

                            'type' =>
                                $section_type,

                            'slug' =>
                                $section_slug,

                            'posts' =>
                                absint(
                                    $section['posts']
                                    ?? 4
                                ),
                            'orderby' => $section['orderby'] ?? 'latest',
                            'link' =>
                                $section_link,

                        ]

                    );


                    /*
                    |--------------------------------------------------------------------------
                    | ADVERTISEMENT
                    |--------------------------------------------------------------------------
                    */

                    if (
                        !empty(  
                            $section['show_ad']
                        )
                    ) {

                        get_template_part(

                            'template-parts/sections/advertisement',

                            null,

                            [

                                'slot' =>
                                    $homepage_ad_slot,

                            ]

                        );

                    }

                endforeach;

                ?>

            </main>


            <?php

            /*
            |--------------------------------------------------------------------------
            | SIDEBAR
            |--------------------------------------------------------------------------
            */

            get_template_part(
                'template-parts/sidebar/sidebar'
            );

            ?>

        </div>

    </div>

</section>


<?php

/*
|--------------------------------------------------------------------------
| HOMEPAGE VIDEO SECTION
|--------------------------------------------------------------------------
*/

?>

<section class="homepage-video-wrapper">

    <div class="container">

        <?php

        get_template_part(
            'template-parts/sections/video-section'
        );

        ?>

    </div>

</section>