<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
CURRENT FILTER / SORT
=========================================================*/

$current_filter = sanitize_key(
    $_GET['filter']
    ??
    $_POST['filter']
    ??
    'latest'
);

$current_sort = sanitize_key(
    $_GET['sort']
    ??
    $_POST['sort']
    ??
    'latest'
);


/*=========================================================
CURRENT CATEGORY ID
=========================================================*/

$category_id = absint(

    $_POST['category']
    ??
    get_queried_object_id()

);






/*=========================================================
FILTERS
=========================================================*/

$filters = [

    'latest'  => 'Latest',

    'popular' => 'Popular',
    'trending' => 'Trending'

];





?>


<div
    class="category-toolbar"
   
>


    <!--=====================================================
    FILTER TABS
    =====================================================-->

    <div class="category-tabs">


        <?php foreach (
            $filters as $slug => $label
        ) : ?>


            <?php

            $url = add_query_arg(

                [

                    'filter' =>
                        $slug,

                    'sort' =>
                        $current_sort

                ],

                get_category_link(
                    $category_id
                )

            );

            ?>


            <a

                href="<?php echo esc_url($url); ?>"

                data-filter="<?php
                echo esc_attr($slug);
                ?>"

                data-sort="<?php
                echo esc_attr($current_sort);
                ?>"

               

            >

                <?php echo esc_html($label); ?>

            </a>


        <?php endforeach; ?>


       


    </div>


    <!--=====================================================
    SORT
    =====================================================-->

    <div class="category-toolbar-right">


        <label
            for="category-sort"
        >

            Sort by:

        </label>


        <select

            id="category-sort"

            

        >


            <option
                value="latest"

                <?php selected(
                    $current_sort,
                    'latest'
                ); ?>

            >

                Latest

            </option>


            <option
                value="oldest"

                <?php selected(
                    $current_sort,
                    'oldest'
                ); ?>

            >

                Oldest

            </option>


            <option
                value="title"

                <?php selected(
                    $current_sort,
                    'title'
                ); ?>

            >

                Title A-Z

            </option>


            <option
                value="title-desc"

                <?php selected(
                    $current_sort,
                    'title-desc'
                ); ?>

            >

                Title Z-A

            </option>


        </select>


    </div>


</div>
