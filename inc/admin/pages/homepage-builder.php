<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
|--------------------------------------------------------------------------
| Homepage Builder
|--------------------------------------------------------------------------
*/   

add_action(
    'admin_menu',  
    'isago_homepage_builder_menu'
);

function isago_homepage_builder_menu()
{
    add_menu_page(
        'Isago Homepage',
        'Isago',
        'manage_options',
        'isago-home',
        'isago_homepage_builder',
        'dashicons-layout',
        25
    );

    add_submenu_page(
        'isago-home',
        'Homepage Builder',
        'Homepage Builder',
        'manage_options',
        'isago-home',
        'isago_homepage_builder'
    );
}


/*
|--------------------------------------------------------------------------
| Register Settings
|--------------------------------------------------------------------------
*/

add_action(
    'admin_init',
    'isago_register_homepage_settings'
);

function isago_register_homepage_settings()
{
    register_setting(
        'isago_home_options',
        'isago_home_settings'
    );
}


/*
|--------------------------------------------------------------------------
| Homepage Builder Page
|--------------------------------------------------------------------------
*/

function isago_homepage_builder()
{  

    $options = get_option(
        'isago_home_settings',
        []
    );

?>

<div class="wrap isago-admin isago-home-builder">

    <h1>Homepage Builder</h1>

    <form
        method="post"
        action="options.php"
        class="isago-home-builder-form"
    >

        <?php settings_fields('isago_home_options'); ?>


        <div class="isago-builder-sections">

            <?php

            /*
            |--------------------------------------------------------------------------
            | Hero
            |--------------------------------------------------------------------------
            */

            require_once ISAGO_DIR .
                '/inc/admin/fields/hero-fields.php';


            /*
            |--------------------------------------------------------------------------
            | Homepage Sections
            |--------------------------------------------------------------------------
            */

            require_once ISAGO_DIR .
                '/inc/admin/fields/homepage-section-fields.php';


            /*
            |--------------------------------------------------------------------------
            | Homepage Video
            |--------------------------------------------------------------------------
            */

            require_once ISAGO_DIR .
                '/inc/admin/fields/homepage-video-fields.php';


            /*
            |--------------------------------------------------------------------------
            | Sidebar
            |--------------------------------------------------------------------------
            */

            require_once ISAGO_DIR .
                '/inc/admin/fields/sidebar-fields.php';


            /*
            |--------------------------------------------------------------------------
            | Newsletter
            |--------------------------------------------------------------------------
            */

            require_once ISAGO_DIR .
                '/inc/admin/fields/newsletter-fields.php';


            /*
            |--------------------------------------------------------------------------
            | Tags
            |--------------------------------------------------------------------------
            */

            require_once ISAGO_DIR .
                '/inc/admin/fields/tags-fields.php';


            /*
            |--------------------------------------------------------------------------
            | Advertisement
            |--------------------------------------------------------------------------
            */

            // require_once ISAGO_DIR .
            //     '/inc/admin/fields/advertisement-fields.php';

            ?>

        </div>


        <div class="isago-builder-submit">

            <?php submit_button('Save Homepage Settings'); ?>

        </div>

    </form>

</div>

<?php

}