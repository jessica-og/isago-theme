<?php

if (!defined('ABSPATH')) {
    exit;
}

$query = get_query_var(
    'isago_pagination_query'
);

if (!$query) {
    return;
}

$total_pages = $query->max_num_pages;

if ($total_pages <= 1) {
    return;
}

$current = isago_get_tag_paged();

?>

<nav class="isago-pagination">

    <?php

    echo paginate_links([

        'base' =>
            trailingslashit(
                get_tag_link(
                    get_queried_object_id()
                )
            ) . 'page/%#%/',

        'format' => '',

        'current' => $current,

        'total' => $total_pages,

        'type' => 'list',

        'prev_text' => '&laquo;',

        'next_text' => '&raquo;'

    ]);

    ?>

</nav>