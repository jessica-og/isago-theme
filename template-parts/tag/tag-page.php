<?php

if (!defined('ABSPATH')) {
    exit;
}

$settings = isago_get_category_archive_settings();

?>

<section class="isago-tag-page">

    <div class="container">

        <?php

        get_template_part(
            'template-parts/tag/tag-header'
        );

        ?>

        <div class="category-page-grid">

            <main class="category-main">

                <?php

                if ($settings['toolbar']) {

                    get_template_part(
                        'template-parts/tag/tag-toolbar'
                    );

                }


                if ($settings['featured']) {

                    get_template_part(
                        'template-parts/tag/tag-featured'
                    );

                }


                $query = isago_get_tag_grid();

                set_query_var(
                    'tag_query',
                    $query
                );

                get_template_part(  
                    'template-parts/tag/tag-grid'
                );


                set_query_var(
                    'isago_pagination_query',
                    $query
                );

                get_template_part(
                    'template-parts/tag/tag-pagination'
                );

                ?>

            </main>


            <?php

            if ($settings['sidebar']) {

                get_template_part(
                    'template-parts/tag/sidebar/sidebar'
                );

            }

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