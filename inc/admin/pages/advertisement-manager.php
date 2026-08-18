<?php

if (!defined('ABSPATH')) {
    exit;
}


/*--------------------------------------------------------------
Advertisement Manager
--------------------------------------------------------------*/

add_action(
    'admin_menu',
    'isago_ads_menu'
);

add_action(
    'admin_init',
    'isago_register_ads_settings'
);


function isago_ads_menu()
{
    add_submenu_page(
        'isago-home',
        'Advertisement Manager',
        'Advertisement Manager',
        'manage_options',
        'isago-ads',
        'isago_ads_page'
    );
}


function isago_register_ads_settings()
{
    register_setting(
        'isago_ads_options',
        'isago_ads_settings'
    );
}


function isago_ads_page()
{

    $options = isago_get_option(
        'isago_ads_settings'
    );

    $slots = [

        'header'    => 'Header Banner',
        
        'category'  => 'Category Banner',

         'tag'  => 'Tag Banner',

        'homepage1' => 'Homepage Banner 1',

        'homepage2' => 'Homepage Banner 2',

        'homepage3' => 'Homepage Banner 3',

        'homepage4' => 'Homepage Banner 4',

        'sidebar'   => 'Sidebar Banner',

        'footer'    => 'Footer Banner'

    ];

?>

<div class="wrap isago-admin isago-ads-manager">

    <h1>Advertisement Manager</h1>

    <form
        method="post"
        action="options.php"
        class="isago-ads-form"
    >

        <?php settings_fields(
            'isago_ads_options'
        ); ?>


        <div class="isago-builder-sections">

            <?php foreach ($slots as $key => $label) : ?>

                <div class="isago-builder-card">

                    <!-- =================================================
                    ACCORDION HEADER
                    ================================================= -->

        <button
    type="button"
    class="isago-builder-card-header"
    aria-expanded="false"
>

    <span class="isago-builder-card-title">

        <span
            class="dashicons dashicons-megaphone isago-builder-card-symbol"
            aria-hidden="true"
        ></span>

        <span>
            <?php echo esc_html($label); ?>
        </span>

    </span>

    <span
        class="dashicons dashicons-arrow-down-alt2 isago-builder-card-icon"
        aria-hidden="true"
    ></span>

</button>


                    <!-- =================================================
                    ACCORDION CONTENT
                    ================================================= -->

                    <div class="isago-builder-card-content">

                        <table class="form-table">

                            <tbody>


                                <!-- Enable -->

                                <tr>

                                    <th>
                                        Enable
                                    </th>

                                    <td>

                                        <label>

                                            <input
                                                type="checkbox"
                                                name="isago_ads_settings[<?php echo esc_attr($key); ?>][enable]"
                                                value="1"
                                                <?php checked(
                                                    $options[$key]['enable'] ?? 0,
                                                    1
                                                ); ?>
                                            >

                                            Enable Banner

                                        </label>

                                    </td>

                                </tr>


                                <!-- Banner Image -->

                                <tr>

                                    <th>
                                        Banner Image
                                    </th>

                                    <td>

                                        <input
                                            type="hidden"
                                            class="isago-image-url"
                                            name="isago_ads_settings[<?php echo esc_attr($key); ?>][image]"
                                            value="<?php echo esc_attr(
                                                $options[$key]['image'] ?? ''
                                            ); ?>"
                                        >


                                        <img
                                            class="isago-preview"
                                            src="<?php
                                                echo esc_url(
                                                    $options[$key]['image']
                                                    ?? ISAGO_URI . '/assets/images/placeholder.png'
                                                );
                                            ?>"
                                            alt="<?php echo esc_attr($label); ?>"
                                        >


                                        <br>


                                        <button
                                            type="button"
                                            class="button isago-upload-button"
                                        >

                                            Upload Banner

                                        </button>

                                    </td>

                                </tr>


                                <!-- Destination URL -->

                                <tr>

                                    <th>
                                        Destination URL
                                    </th>

                                    <td>

                                        <input
                                            class="regular-text"
                                            type="url"
                                            name="isago_ads_settings[<?php echo esc_attr($key); ?>][url]"
                                            value="<?php echo esc_attr(
                                                $options[$key]['url'] ?? ''
                                            ); ?>"
                                        >

                                    </td>

                                </tr>


                                <!-- Alt Text -->

                                <tr>

                                    <th>
                                        Alt Text
                                    </th>

                                    <td>

                                        <input
                                            class="regular-text"
                                            type="text"
                                            name="isago_ads_settings[<?php echo esc_attr($key); ?>][alt]"
                                            value="<?php echo esc_attr(
                                                $options[$key]['alt'] ?? ''
                                            ); ?>"
                                        >

                                    </td>

                                </tr>


                                <!-- Open New Tab -->

                                <tr>

                                    <th>
                                        Open in New Tab
                                    </th>

                                    <td>

                                        <label>

                                            <input
                                                type="checkbox"
                                                name="isago_ads_settings[<?php echo esc_attr($key); ?>][blank]"
                                                value="1"
                                                <?php checked(
                                                    $options[$key]['blank'] ?? 0,
                                                    1
                                                ); ?>
                                            >

                                            Open in new tab

                                        </label>

                                    </td>

                                </tr>


                                <!-- AdSense -->

                                <tr>

                                    <th>
                                        Google AdSense Code
                                    </th>

                                    <td>

                                        <textarea
                                            rows="6"
                                            cols="60"
                                            name="isago_ads_settings[<?php echo esc_attr($key); ?>][adsense]"
                                        ><?php

                                            echo esc_textarea(
                                                $options[$key]['adsense'] ?? ''
                                            );

                                        ?></textarea>

                                    </td>

                                </tr>


                            </tbody>

                        </table>

                    </div>

                </div>

            <?php endforeach; ?>

        </div>


        <div class="isago-builder-submit">

            <?php submit_button(
                'Save Advertisement Settings'
            ); ?>

        </div>

    </form>

</div>

<?php

}