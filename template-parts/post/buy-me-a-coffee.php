<?php

if (!defined('ABSPATH')) {
    exit;
}

$paystack_coffee_url = 'https://paystack.shop/pay/isago';

?>

<div class="isago-coffee-support">
    <div class="isago-coffee-support-content">
        <span class="isago-coffee-support-icon" aria-hidden="true">
            <i class="fa-solid fa-mug-hot"></i>
        </span>

        <div class="isago-coffee-support-text">
            <h3><?php esc_html_e('Enjoying Isago?', 'isago'); ?></h3>

            <p>
                <?php
                esc_html_e(
                    'If this content has been helpful, you can support Isago by buying us a coffee.',
                    'isago'
                );
                ?>
            </p>
        </div>
    </div>

    <a
        class="isago-global-button isago-coffee-support-button"
        href="<?php echo esc_url($paystack_coffee_url); ?>"
        target="_blank"
        rel="noopener noreferrer"
        aria-label="<?php esc_attr_e('Support Isago with Paystack', 'isago'); ?>"
    >
        <i class="fa-solid fa-mug-hot" aria-hidden="true"></i>
        <span><?php esc_html_e('Buy Me a Coffee', 'isago'); ?></span>
        <i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>
    </a>
</div>