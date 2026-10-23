<?php

if (!defined('ABSPATH')) {
    exit;
}

$current_filter = sanitize_key(
    $_GET['filter'] ?? 'latest'
);

$current_sort = sanitize_key(
    $_GET['sort'] ?? 'latest'
);


/*
|--------------------------------------------------------------------------
| Current Tag
|--------------------------------------------------------------------------
*/

$tag_id = absint(

    $_POST['tag']

    ??

    get_queried_object_id()

);


$tag = get_tag($tag_id);

if (!$tag) {
    return;
}


$filters = [

    'latest'   => 'Latest',

    'popular'  => 'Popular',

    'trending' => 'Trending'

];

?>

<div class="category-toolbar">

    <div class="category-tabs">

        <?php foreach ($filters as $slug => $label) : ?>

            <?php

            $url = add_query_arg(

                [

                    'filter' => $slug,

                    'sort' => $current_sort

                ],

                get_tag_link($tag_id)

            );

            ?>

            <a

                href="<?php echo esc_url($url); ?>"

                data-filter="<?php echo esc_attr($slug); ?>"

                data-sort="<?php echo esc_attr($current_sort); ?>"

                class="<?php
                    echo $current_filter === $slug
                        ? 'active'
                        : '';
                ?>"

            >

                <?php echo esc_html($label); ?>

            </a>

        <?php endforeach; ?>

    </div>


    <div class="category-toolbar-right">

        <label>

            Sort by:

        </label>


        <select

            id="tag-sort"

            data-filter="<?php
                echo esc_attr($current_filter);
            ?>"

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