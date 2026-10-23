<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
|--------------------------------------------------------------------------
| Theme Settings Page
|--------------------------------------------------------------------------
*/

add_action(  
    'admin_init',
    'isago_register_theme_settings'
);

function isago_register_theme_settings()
{
    register_setting(
        'isago_theme_options',
        'isago_theme_settings'
    );  

       register_setting(

        'isago_theme_options',

        'isago_cookie_settings'

    );
}


/*
|--------------------------------------------------------------------------
| Admin Menu
|--------------------------------------------------------------------------
*/

add_action(
    'admin_menu',
    'isago_theme_settings_menu'
);

function isago_theme_settings_menu()
{
    add_submenu_page(
        'isago-home',
        'Theme Settings',
        'Theme Settings',
        'manage_options',
        'isago-theme-settings',
        'isago_theme_settings_page'
    );
}


/*
|--------------------------------------------------------------------------
| Theme Settings Page
|--------------------------------------------------------------------------
*/

function isago_theme_settings_page()
{

    $options = isago_get_option(
        'isago_theme_settings'
    );
  
?>

<div class="wrap isago-admin isago-theme-settings">

    <h1>
        Theme Settings
    </h1>


    <form
        method="post"
        action="options.php"
        class="isago-admin-form"
    >

        <?php

        settings_fields(
            'isago_theme_options'
        );

        ?>


        <div class="isago-builder-sections">


            <!-- =====================================================
            Branding
            ===================================================== -->

            <section class="isago-builder-card">

                <button
                    type="button"
                    class="isago-builder-card-header"
                    aria-expanded="false"
                >

                    <span class="isago-builder-card-title">

                        <span class="isago-builder-card-icon">

                            <i class="dashicons dashicons-admin-customizer"></i>

                        </span>

                        <span>
                            Branding
                        </span>

                    </span>


                    <span class="isago-builder-toggle">

                        <i class="dashicons dashicons-arrow-down-alt2"></i>

                    </span>

                </button>


                <div class="isago-builder-card-content">

                    <table class="form-table">

                        <tbody>

                            <?php

                            require_once ISAGO_DIR .
                                '/inc/admin/pages/theme-fields/branding.php';

                            ?>

                        </tbody>

                    </table>

                </div>

            </section>


<!-- =====================================================
Author
===================================================== -->

<section class="isago-builder-card">

    <button
        type="button"
        class="isago-builder-card-header"
        aria-expanded="false"
    >

        <span class="isago-builder-card-title">

            <span class="isago-builder-card-icon">

                <i class="dashicons dashicons-admin-users"></i>

            </span>

            <span>
                Author
            </span>

        </span>

        <span class="isago-builder-toggle">

            <i class="dashicons dashicons-arrow-down-alt2"></i>

        </span>

    </button>


    <div class="isago-builder-card-content">

        <table class="form-table">

            <tbody>

                <?php

                require_once ISAGO_DIR .
                    '/inc/admin/pages/theme-fields/author.php';

                ?>

            </tbody>

        </table>

    </div>

</section>


            <!-- =====================================================
            Header
            ===================================================== -->

            <section class="isago-builder-card">

                <button
                    type="button"
                    class="isago-builder-card-header"
                    aria-expanded="false"
                >

                    <span class="isago-builder-card-title">

                        <span class="isago-builder-card-icon">

                            <i class="dashicons dashicons-menu"></i>

                        </span>

                        <span>
                            Header
                        </span>

                    </span>


                    <span class="isago-builder-toggle">

                        <i class="dashicons dashicons-arrow-down-alt2"></i>

                    </span>

                </button>


                <div class="isago-builder-card-content">

                    <table class="form-table">

                        <tbody>

                            <?php

                            require_once ISAGO_DIR .
                                '/inc/admin/pages/theme-fields/header.php';

                            ?>

                        </tbody>

                    </table>

                </div>

            </section>


            <!-- =====================================================
            Layout
            ===================================================== -->

            <section class="isago-builder-card">

                <button
                    type="button"
                    class="isago-builder-card-header"
                    aria-expanded="false"
                >

                    <span class="isago-builder-card-title">

                        <span class="isago-builder-card-icon">

                            <i class="dashicons dashicons-layout"></i>

                        </span>

                        <span>
                            Layout
                        </span>

                    </span>


                    <span class="isago-builder-toggle">

                        <i class="dashicons dashicons-arrow-down-alt2"></i>

                    </span>

                </button>


                <div class="isago-builder-card-content">

                    <table class="form-table">

                        <tbody>

                            <?php

                            require_once ISAGO_DIR .
                                '/inc/admin/pages/theme-fields/layout.php';

                            ?>

                        </tbody>

                    </table>

                </div>

            </section>





            


            <!-- =====================================================
            Footer
            ===================================================== -->

            <section class="isago-builder-card">

                <button
                    type="button"
                    class="isago-builder-card-header"
                    aria-expanded="false"
                >

                    <span class="isago-builder-card-title">

                        <span class="isago-builder-card-icon">

                            <i class="dashicons dashicons-editor-aligncenter"></i>

                        </span>

                        <span>
                            Footer
                        </span>

                    </span>


                    <span class="isago-builder-toggle">

                        <i class="dashicons dashicons-arrow-down-alt2"></i>

                    </span>

                </button>


                <div class="isago-builder-card-content">

                    <table class="form-table">

                        <tbody>

                            <?php

                            require_once ISAGO_DIR .
                                '/inc/admin/pages/theme-fields/footer.php';

                            ?>

                        </tbody>

                    </table>

                </div>

            </section>


            <!-- =====================================================
            About Page
            ===================================================== -->

            <section class="isago-builder-card">

                <button
                    type="button"
                    class="isago-builder-card-header"
                    aria-expanded="false"
                >

                    <span class="isago-builder-card-title">

                        <span class="isago-builder-card-icon">

                            <i class="dashicons dashicons-info"></i>

                        </span>

                        <span>
                            About Page
                        </span>

                    </span>


                    <span class="isago-builder-toggle">

                        <i class="dashicons dashicons-arrow-down-alt2"></i>

                    </span>

                </button>


                <div class="isago-builder-card-content">

                    <table class="form-table">

                        <tbody>

                            <?php

                            require_once ISAGO_DIR .
                                '/inc/admin/pages/theme-fields/about.php';

                            ?>

                        </tbody>

                    </table>

                </div>

            </section>


            <!-- =====================================================
            Contact Page
            ===================================================== -->

            <section class="isago-builder-card">

                <button
                    type="button"
                    class="isago-builder-card-header"
                    aria-expanded="false"
                >

                    <span class="isago-builder-card-title">

                        <span class="isago-builder-card-icon">

                            <i class="dashicons dashicons-email"></i>

                        </span>

                        <span>
                            Contact Page
                        </span>

                    </span>


                    <span class="isago-builder-toggle">

                        <i class="dashicons dashicons-arrow-down-alt2"></i>

                    </span>

                </button>


                <div class="isago-builder-card-content">

                    <table class="form-table">

                        <tbody>

                            <?php

                            require_once ISAGO_DIR .
                                '/inc/admin/pages/theme-fields/contact.php';

                            ?>

                        </tbody>

                    </table>

                </div>

            </section>




    <!-- =====================================================
            Cookie Consent Page
            ===================================================== -->

            <section class="isago-builder-card">

                <button
                    type="button"
                    class="isago-builder-card-header"
                    aria-expanded="false"
                >

                    <span class="isago-builder-card-title">

                        <span class="isago-builder-card-icon">

                            <i class="dashicons dashicons-sos"></i>

                        </span>

                        <span>
                            Cookie Consent Page
                        </span>

                    </span>


                    <span class="isago-builder-toggle">

                        <i class="dashicons dashicons-arrow-down-alt2"></i>

                    </span>

                </button>


                <div class="isago-builder-card-content">

                    <table class="form-table">

                        <tbody>

                            <?php

                            require_once ISAGO_DIR .
                                '/inc/admin/pages/theme-fields/cookie-consent.php';

                            ?>

                        </tbody>

                    </table>

                </div>

            </section>


        </div>


        <div class="isago-builder-submit">

            <?php

            submit_button(
                'Save Theme Settings'
            );

            ?>

        </div>


    </form>

</div>

<?php

}