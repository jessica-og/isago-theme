<?php

if (!defined('ABSPATH')) {
    exit;
}

$settings = [

    'toolbar'  => true,
    'featured' => true

];
?>

<section class="isago-category-page">

    <div class="container">

        <?php

        /*  
        ---------------------------------------------------------
        category Header
        ---------------------------------------------------------
        */

        get_template_part(
            'template-parts/tag/tag-header'
        );

        ?>

        <div class="category-page-grid">

            <main class="category-main">

                <?php


if ($settings['toolbar']) {

    get_template_part(
        'template-parts/category/category-toolbar'
    );

}

                /*
                ---------------------------------------------------------
                Featured Post
                ---------------------------------------------------------
                */

             if ($settings['featured']) {

    get_template_part(
        'template-parts/tag/tag-featured'
    );

}
                /*
                ---------------------------------------------------------
                Posts Grid
                ---------------------------------------------------------
                */

     $query = isago_get_tag_grid();

            set_query_var(
                'category_query',
                $query
            );
get_template_part(
    'template-parts/category/category-grid'
);

/*
---------------------------------------------------------
Pagination
---------------------------------------------------------
*/

      set_query_var(
                'isago_pagination_query',
                $query
            );

get_template_part(
    'template-parts/category/category-pagination'
);
                ?>

            </main>

   <?php
  


    get_template_part(
        'template-parts/category/sidebar/sidebar'
    );



?>

        </div>

    </div>

</section>