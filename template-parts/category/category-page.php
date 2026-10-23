<?php

if (!defined('ABSPATH')) {
    exit;
}


/*
|--------------------------------------------------------------------------
| Category Archive
|--------------------------------------------------------------------------
|
| This template is used for:
|
| /category/name/
| /category/name/page/2/
| /category/name/page/3/
|
|--------------------------------------------------------------------------
*/


$category_id =
    absint(
        get_queried_object_id()
    );


/*
|--------------------------------------------------------------------------
| Safety check
|--------------------------------------------------------------------------
*/

if (!$category_id) {
    return;
}


/*
|--------------------------------------------------------------------------
| Settings
|--------------------------------------------------------------------------
*/

$settings = [

    'toolbar'  => true,

    'featured' => true,

];


/*
|--------------------------------------------------------------------------
| CATEGORY QUERY
|--------------------------------------------------------------------------
|
| Get the category posts before rendering
| the grid.
|
*/

$query =
    isago_get_category_grid();


/*
|--------------------------------------------------------------------------
| CHECK FOR CONTENT
|--------------------------------------------------------------------------
*/

$has_category_posts =
    $query instanceof WP_Query
    &&
    $query->have_posts();

?>


<section
    class="isago-category-page"
    data-category="<?php echo esc_attr($category_id); ?>"
>


    <div class="container">


        <?php

        /*
        |--------------------------------------------------------------------------
        | Header
        |--------------------------------------------------------------------------
        */

        get_template_part(
            'template-parts/category/category-header'
        );

        ?>


        <?php if ($has_category_posts) : ?>


            <div class="category-page-grid">


                <main class="category-main">


                    <?php

                    /*
                    |--------------------------------------------------------------------------
                    | Toolbar
                    |--------------------------------------------------------------------------
                    */

                    if ($settings['toolbar']) {

                        get_template_part(
                            'template-parts/category/category-toolbar'
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Featured
                    |--------------------------------------------------------------------------
                    */

                    if ($settings['featured']) {

                        get_template_part(
                            'template-parts/category/category-featured'
                        );

                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Grid
                    |--------------------------------------------------------------------------
                    */

                    set_query_var(
                        'category_query',
                        $query
                    );


                    get_template_part(
                        'template-parts/category/category-grid'
                    );


                    /*
                    |--------------------------------------------------------------------------
                    | Pagination
                    |--------------------------------------------------------------------------
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

                /*
                |--------------------------------------------------------------------------
                | Sidebar
                |--------------------------------------------------------------------------
                */

                get_template_part(
                    'template-parts/category/sidebar/sidebar'
                );

                ?>


            </div>


        <?php else : ?>


            <?php

            /*
            |--------------------------------------------------------------------------
            | COMING SOON
            |--------------------------------------------------------------------------
            */

            get_template_part(
                'template-parts/category/coming-soon'
            );

            ?>


        <?php endif; ?>


    </div>


</section>