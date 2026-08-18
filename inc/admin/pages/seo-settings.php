<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
SEO Settings Page
=========================================================*/

add_action(
    'admin_menu',
    'isago_seo_menu'
);

add_action(
    'admin_init',
    'isago_register_seo'
);


function isago_register_seo()
{
    register_setting(
        'isago_seo_options',
        'isago_seo_settings'
    );
}


function isago_seo_menu()
{
    add_submenu_page(
        'isago-home',
        'SEO',
        'SEO',
        'manage_options',
        'isago-seo',
        'isago_seo_page'
    );
}


function isago_seo_page()
{

    $options = isago_get_option(
        'isago_seo_settings'
    );

?>

<div class="wrap isago-admin isago-seo-manager">

    <h1>SEO Engine</h1>


    <form
        method="post"
        action="options.php"
        class="isago-seo-form"
    >

        <?php settings_fields(
            'isago_seo_options'
        ); ?>


        <div class="isago-builder-sections">


            <!-- =================================================
            GENERAL SEO
            ================================================= -->

            <div class="isago-builder-card">

                <button
                    type="button"
                    class="isago-builder-card-header"
                    aria-expanded="false"
                >

                    <span class="isago-builder-card-title">

                        <span
                            class="dashicons dashicons-admin-settings isago-builder-card-symbol"
                            aria-hidden="true"
                        ></span>

                        <span>
                            General SEO
                        </span>

                    </span>


                    <span
                        class="dashicons dashicons-arrow-down-alt2 isago-builder-card-icon"
                        aria-hidden="true"
                    ></span>

                </button>


                <div class="isago-builder-card-content">

                    <table class="form-table">

                        <tbody>

                            <tr>

                                <th>
                                    Default Meta Title
                                </th>

                                <td>

                                    <input
                                        class="regular-text"
                                        type="text"
                                        name="isago_seo_settings[title]"
                                        value="<?php echo esc_attr(
                                            $options['title']
                                            ?? get_bloginfo('name')
                                        ); ?>"
                                    >

                                </td>

                            </tr>


                            <tr>

                                <th>
                                    Default Meta Description
                                </th>

                                <td>

                                    <textarea
                                        rows="4"
                                        cols="60"
                                        name="isago_seo_settings[description]"
                                    ><?php

                                        echo esc_textarea(
                                            $options['description']
                                            ?? get_bloginfo('description')
                                        );

                                    ?></textarea>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>


            <!-- =================================================
            SCHEMA
            ================================================= -->

            <div class="isago-builder-card">

                <button
                    type="button"
                    class="isago-builder-card-header"
                    aria-expanded="false"
                >

                    <span class="isago-builder-card-title">

                        <span
                            class="dashicons dashicons-editor-code isago-builder-card-symbol"
                            aria-hidden="true"
                        ></span>

                        <span>
                            Schema
                        </span>

                    </span>


                    <span
                        class="dashicons dashicons-arrow-down-alt2 isago-builder-card-icon"
                        aria-hidden="true"
                    ></span>

                </button>


                <div class="isago-builder-card-content">

                    <table class="form-table">

                        <tbody>

                            <tr>

                                <th>
                                    Schema Type
                                </th>

                                <td>

                                    <select
                                        name="isago_seo_settings[schema]"
                                    >

                                        <option
                                            value="Organization"
                                            <?php selected(
                                                $options['schema']
                                                ?? 'Organization',
                                                'Organization'
                                            ); ?>
                                        >
                                            Organization
                                        </option>

                                        <option
                                            value="Person"
                                            <?php selected(
                                                $options['schema']
                                                ?? 'Organization',
                                                'Person'
                                            ); ?>
                                        >
                                            Person
                                        </option>

                                    </select>

                                </td>

                            </tr>


                            <tr>

                                <th>
                                    Enable Breadcrumbs
                                </th>

                                <td>

                                    <label>

                                        <input
                                            type="checkbox"
                                            value="1"
                                            name="isago_seo_settings[breadcrumbs]"
                                            <?php checked(
                                                $options['breadcrumbs'] ?? 1,
                                                1
                                            ); ?>
                                        >

                                        Enable

                                    </label>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>


            <!-- =================================================
            OPEN GRAPH
            ================================================= -->

            <div class="isago-builder-card">

                <button
                    type="button"
                    class="isago-builder-card-header"
                    aria-expanded="false"
                >

                    <span class="isago-builder-card-title">

                        <span
                            class="dashicons dashicons-share-alt2 isago-builder-card-symbol"
                            aria-hidden="true"
                        ></span>

                        <span>
                            Open Graph
                        </span>

                    </span>


                    <span
                        class="dashicons dashicons-arrow-down-alt2 isago-builder-card-icon"
                        aria-hidden="true"
                    ></span>

                </button>


                <div class="isago-builder-card-content">

                    <table class="form-table">

                        <tbody>

                            <tr>

                                <th>
                                    Default Social Image
                                </th>

                                <td>

                                    <input
                                        type="hidden"
                                        class="isago-image-url"
                                        name="isago_seo_settings[og_image]"
                                        value="<?php echo esc_attr(
                                            $options['og_image'] ?? ''
                                        ); ?>"
                                    >


                                    <img
                                        class="isago-preview"
                                        src="<?php echo esc_url(
                                            $options['og_image']
                                            ?? ''
                                        ); ?>"
                                        alt="SEO Social Image"
                                        style="<?php echo empty(
                                            $options['og_image']
                                        ) ? 'display:none;' : ''; ?>"
                                    >


                                    <br>


                                    <button
                                        type="button"
                                        class="button isago-upload-button"
                                    >
                                        Upload Image
                                    </button>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>


            <!-- =================================================
            COMPATIBILITY
            ================================================= -->

            <div class="isago-builder-card">

                <button
                    type="button"
                    class="isago-builder-card-header"
                    aria-expanded="false"
                >

                    <span class="isago-builder-card-title">

                        <span
                            class="dashicons dashicons-admin-plugins isago-builder-card-symbol"
                            aria-hidden="true"
                        ></span>

                        <span>
                            Compatibility
                        </span>

                    </span>


                    <span
                        class="dashicons dashicons-arrow-down-alt2 isago-builder-card-icon"
                        aria-hidden="true"
                    ></span>

                </button>


                <div class="isago-builder-card-content">

                    <table class="form-table">

                        <tbody>

                            <tr>

                                <th>
                                    SEO Plugin Status
                                </th>

                                <td>

                                    <?php if (isago_has_seo_plugin()) : ?>

                                        <span
                                            style="color:#16a34a;font-weight:700;"
                                        >

                                            ✔ SEO Plugin Detected

                                        </span>

                                    <?php else : ?>

                                        <span
                                            style="color:#d63638;font-weight:700;"
                                        >

                                            No SEO Plugin Detected

                                        </span>

                                    <?php endif; ?>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>


        </div>


        <div class="isago-builder-submit">

            <?php submit_button(
                'Save SEO Settings'
            ); ?>

        </div>


    </form>

</div>

<?php

}