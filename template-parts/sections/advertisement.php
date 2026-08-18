<?php

if (!defined('ABSPATH')) {
    exit;
}

$ad = isago_get_ad('homepage1');

if (empty($ad['enable'])) {
    return;
}

?>

<section class="isago-advertisement">

    <div class="ad-label">
        Advertisement
    </div>

    <div class="ad-box">

        <?php if (!empty($ad['adsense'])) : ?>

            <div class="ad-adsense">

                <?php
                echo $ad['adsense'];
                ?>

            </div>

        <?php elseif (!empty($ad['image'])) : ?>

            <?php if (!empty($ad['url'])) : ?>

                <a
                    class="ad-link"
                    href="<?php echo esc_url($ad['url']); ?>"
                    <?php echo !empty($ad['blank']) ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>
                >

            <?php endif; ?>

                <img
                    class="ad-image"
                    src="<?php echo esc_url($ad['image']); ?>"
                    alt="<?php echo esc_attr($ad['alt']); ?>"
                    loading="lazy"
                >

            <?php if (!empty($ad['url'])) : ?>

                </a>

            <?php endif; ?>

        <?php endif; ?>

    </div>

</section>