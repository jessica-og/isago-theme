<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
DATA
=========================================================*/

$current_page =
    max(
        1,
        absint(
            $args['current_page']
            ?? 1
        )
    );


$total_pages =
    absint(
        $args['total_pages']
        ?? 0
    );


if ($total_pages <= 1) {
    return;
}

?>


<div class="ai-platform-page-pagination">


    <?php

    echo wp_kses_post(
        paginate_links([

            'base' =>
                str_replace(
                    999999999,
                    '%#%',
                    esc_url(
                        get_pagenum_link(
                            999999999
                        )
                    )
                ),

            'format' =>
                '?paged=%#%',

            'current' =>
                $current_page,

            'total' =>
                $total_pages,

            'type' =>
                'list',

            'prev_text' =>
                '<i class="fa-solid fa-arrow-left" aria-hidden="true"></i> Previous',

            'next_text' =>
                'Next <i class="fa-solid fa-arrow-right" aria-hidden="true"></i>',

            'mid_size' =>
                1,

            'end_size' =>
                1

        ])
    );

    ?>


</div>