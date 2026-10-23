<?php

if (!defined('ABSPATH')) {
    exit;
}


/*
|--------------------------------------------------------------------------
| Social Settings
|--------------------------------------------------------------------------
*/

$social = isago_get_social_settings();


/*
|--------------------------------------------------------------------------
| Sharing Enabled?
|--------------------------------------------------------------------------
*/

if (empty($social['share'])) {
    return;
}


/*
|--------------------------------------------------------------------------
| Post Data
|--------------------------------------------------------------------------
*/

$post_url = get_permalink();

$post_title = get_the_title();


/*
|--------------------------------------------------------------------------
| New Tab
|--------------------------------------------------------------------------
*/

$target = '';

if (!empty($social['blank'])) {

    $target = ' target="_blank" rel="noopener noreferrer"';

}

?>

<div class="post-share">

    <span class="post-share-label">

        Share

    </span>


    <?php

    /*
    |--------------------------------------------------------------------------
    | Facebook
    |--------------------------------------------------------------------------
    */

    if (!empty($social['facebook'])) :

    ?>

        <a
            class="post-share-facebook"
            href="<?php
                echo esc_url(
                    'https://www.facebook.com/sharer/sharer.php?u=' .
                    rawurlencode($post_url)
                );
            ?>"
            <?php echo $target; ?>
            aria-label="Share on Facebook"
        >

            <i class="fa-brands fa-facebook-f"></i>

        </a>

    <?php

    endif;


    /*
    |--------------------------------------------------------------------------
    | X
    |--------------------------------------------------------------------------
    */

    if (!empty($social['x'])) :

    ?>

        <a
            class="post-share-x"
            href="<?php
                echo esc_url(
                    'https://twitter.com/intent/tweet?url=' .
                    rawurlencode($post_url) .
                    '&text=' .
                    rawurlencode($post_title)
                );
            ?>"
            <?php echo $target; ?>
            aria-label="Share on X"
        >

            <i class="fa-brands fa-x-twitter"></i>

        </a>

    <?php

    endif;


    /*
    |--------------------------------------------------------------------------
    | LinkedIn
    |--------------------------------------------------------------------------
    */

    if (!empty($social['linkedin'])) :

    ?>

        <a
            class="post-share-linkedin"
            href="<?php
                echo esc_url(
                    'https://www.linkedin.com/sharing/share-offsite/?url=' .
                    rawurlencode($post_url)
                );
            ?>"
            <?php echo $target; ?>
            aria-label="Share on LinkedIn"
        >

            <i class="fa-brands fa-linkedin-in"></i>

        </a>

    <?php

    endif;


    /*
    |--------------------------------------------------------------------------
    | Email
    |--------------------------------------------------------------------------
    */

    if (!empty($social['email'])) :

    ?>

        <a
            class="post-share-email"
            href="<?php
                echo esc_attr(
                    'mailto:?subject=' .
                    rawurlencode($post_title) .
                    '&body=' .
                    rawurlencode($post_url)
                );
            ?>"
            aria-label="Share by email"
        >

            <i class="fa-solid fa-envelope"></i>

        </a>

    <?php

    endif;


    /*
    |--------------------------------------------------------------------------
    | Copy Link
    |--------------------------------------------------------------------------
    */

    if (!empty($social['copy'])) :

    ?>
<button
    type="button"
    class="copy-post-link"
    data-link="<?php echo esc_url(get_permalink()); ?>"
    aria-label="Copy article link"
    title="Copy article link">

    <i class="fa-solid fa-link"></i>

</button>

    <?php

    endif;

    ?>

</div>