<?php

if (!defined('ABSPATH')) {
    exit;
}

?>

<section class="post-comments">

<h2>

<?php comments_number(); ?>

</h2>

<?php comments_template(); ?>

</section>