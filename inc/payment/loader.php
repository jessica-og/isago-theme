<?php

if (!defined('ABSPATH')) {
    exit;
}


require_once ISAGO_DIR .
    '/inc/payment/currency.php';
    
 require_once ISAGO_DIR .
     '/inc/payment/settings.php';

require_once ISAGO_DIR .
    '/inc/payment/paystack.php';

require_once ISAGO_DIR .
    '/inc/payment/orders.php';

require_once ISAGO_DIR . '/inc/payment/admin-orders.php';

require_once ISAGO_DIR .
    '/inc/payment/entitlements.php';

require_once ISAGO_DIR .
    '/inc/payment/checkout.php';

require_once ISAGO_DIR .
    '/inc/payment/webhook.php';

require_once ISAGO_DIR .
    '/inc/payment/callback.php';












