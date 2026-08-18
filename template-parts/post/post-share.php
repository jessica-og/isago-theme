<?php

if (!defined('ABSPATH')) {
    exit;
}

$url = urlencode(get_permalink());

$title = urlencode(get_the_title());

?>

<div class="post-share">

    <span>

        Share

    </span>

    <a

        target="_blank"

        href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $url; ?>">

        <i class="fa-brands fa-facebook-f"></i>

    </a>

    <a

        target="_blank"

        href="https://twitter.com/intent/tweet?url=<?php echo $url; ?>&text=<?php echo $title; ?>">

        <i class="fa-brands fa-x-twitter"></i>

    </a>

    <a

        target="_blank"

        href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo $url; ?>">

        <i class="fa-brands fa-linkedin-in"></i>

    </a>

    <button

        class="copy-post-link"

        data-link="<?php the_permalink(); ?>">

        <i class="fa-solid fa-link"></i>

    </button>

</div>