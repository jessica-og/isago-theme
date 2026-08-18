<?php

if (!defined('ABSPATH')) {
    exit;
}

?>

<article class="post-content">

    <?php the_content(); ?>

    <?php

    wp_link_pages([

        'before' => '<div class="post-pages">',

        'after'  => '</div>'

    ]);

    ?>

</article>