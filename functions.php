<?php

/**
 * --------------------------------------------------------
 * ISAGO
 * Premium Editorial WordPress Theme
 * --------------------------------------------------------
 */

if (!defined('ABSPATH')) {
    exit;
}

/*=========================================================
THEME CONSTANTS
=========================================================*/

define('ISAGO_VERSION', '1.0.0');

define('ISAGO_DIR', get_template_directory());

define('ISAGO_URI', get_template_directory_uri());

/*=========================================================
CORE
=========================================================*/

require_once ISAGO_DIR . '/inc/core/setup.php';
require_once ISAGO_DIR . '/inc/core/enqueue.php';
require_once ISAGO_DIR . '/inc/core/options.php';
require_once ISAGO_DIR . '/inc/core/helpers.php';
require_once ISAGO_DIR . '/inc/core/theme-css.php';
require_once ISAGO_DIR . '/inc/core/pagination.php';
require_once ISAGO_DIR . '/inc/core/fonts.php';

/*=========================================================
MENUS
=========================================================*/

require_once ISAGO_DIR . '/inc/menu/menus.php';
require_once ISAGO_DIR . '/inc/menu/menu-engine.php';
require_once ISAGO_DIR . '/inc/menu/menu-builder.php';

/*=========================================================
QUERIES
=========================================================*/
require_once ISAGO_DIR . '/inc/queries/queries.php';
require_once ISAGO_DIR . '/inc/queries/category-query.php';
require_once ISAGO_DIR . '/inc/queries/hero-query.php';
require_once ISAGO_DIR . '/inc/queries/sidebar-query.php';
require_once ISAGO_DIR . '/inc/queries/category-archive.php';
require_once ISAGO_DIR . '/inc/queries/single-query.php';
require_once ISAGO_DIR . '/inc/queries/admin-contact.php';
require_once ISAGO_DIR . '/inc/queries/tag-queries.php';

/*=========================================================
SETTINGS
=========================================================*/
require_once ISAGO_DIR . '/inc/settings/home-builder.php';



/*=========================================================
THEME SETTINGS
=========================================================*/

require_once ISAGO_DIR . '/inc/settings/theme-settings.php';
require_once ISAGO_DIR . '/inc/settings/category-archive-settings.php';
require_once ISAGO_DIR . '/inc/settings/contact-messages.php';
require_once ISAGO_DIR . '/inc/settings/tag.php';

/*=========================================================
FEATURES
=========================================================*/

require_once ISAGO_DIR . '/inc/features/advertisements.php';
require_once ISAGO_DIR . '/inc/features/seo.php';
require_once ISAGO_DIR . '/inc/features/performance.php';
require_once ISAGO_DIR . '/inc/features/social.php';
require_once ISAGO_DIR . '/inc/features/import-export.php';
require_once ISAGO_DIR . '/inc/features/contact-form.php';




/*=========================================================
VIDEOS MODULE
=========================================================*/

require_once ISAGO_DIR . '/inc/videos/loader.php';

/*=========================================================
ADMIN
=========================================================*/

require_once ISAGO_DIR . '/inc/admin/admin.php';
require_once ISAGO_DIR . '/inc/ajax/category-archive.php';
require_once ISAGO_DIR.'/inc/search/loader.php';


add_filter('the_author', function ($name) {
    return 'Isago';
});

add_filter('get_the_author_display_name', function ($name) {
    return 'Isago';
});