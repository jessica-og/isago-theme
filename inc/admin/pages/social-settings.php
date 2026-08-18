<?php

if (!defined('ABSPATH')) {
    exit;
}


/*--------------------------------------------------------------
Social Media Settings
--------------------------------------------------------------*/

add_action(
    'admin_menu',
    'isago_social_menu'
);

add_action(
    'admin_init',
    'isago_register_social_settings'
);


function isago_social_menu()
{
    add_submenu_page(
        'isago-home',
        'Social Media',
        'Social Media',
        'manage_options',
        'isago-social',
        'isago_social_page'
    );
}


function isago_register_social_settings()
{
    register_setting(
        'isago_social_options',
        'isago_social_settings'
    );
}


function isago_social_page()
{

    $options = isago_get_option(
        'isago_social_settings'
    );

?>

<div class="wrap isago-admin isago-social-manager">

    <h1>Social Media</h1>


    <form
        method="post"
        action="options.php"
        class="isago-social-form"
    >

        <?php settings_fields(
            'isago_social_options'
        ); ?>


        <div class="isago-builder-sections">


            <!-- =================================================
            SOCIAL PROFILES
            ================================================= -->

            <div class="isago-builder-card">

                <button
                    type="button"
                    class="isago-builder-card-header"
                    aria-expanded="false"
                >

                    <span class="isago-builder-card-title">

                        <span
                            class="dashicons dashicons-share isago-builder-card-symbol"
                            aria-hidden="true"
                        ></span>

                        <span>
                            Social Profiles
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

                            <?php

                            $profiles = [

                                'facebook'  => 'Facebook',
                                'x'         => 'X (Twitter)',
                                'instagram' => 'Instagram',
                                'youtube'   => 'YouTube',
                                'tiktok'    => 'TikTok',
                                'linkedin'  => 'LinkedIn',
                                'pinterest' => 'Pinterest',
                                'threads'   => 'Threads'

                            ];

                            foreach ($profiles as $key => $label) :

                            ?>

                                <tr>

                                    <th>
                                        <?php echo esc_html($label); ?>
                                    </th>

                                    <td>

                                        <input
                                            class="regular-text"
                                            type="url"
                                            name="isago_social_settings[<?php echo esc_attr($key); ?>]"
                                            value="<?php echo esc_attr(
                                                $options[$key] ?? ''
                                            ); ?>"
                                            placeholder="https://"
                                        >

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            </div>


            <!-- =================================================
            DISPLAY
            ================================================= -->

            <div class="isago-builder-card">

                <button
                    type="button"
                    class="isago-builder-card-header"
                    aria-expanded="false"
                >

                    <span class="isago-builder-card-title">

                        <span
                            class="dashicons dashicons-visibility isago-builder-card-symbol"
                            aria-hidden="true"
                        ></span>

                        <span>
                            Display
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

                            <?php

                            $display = [

                                'header' => 'Show Icons in Header',
                                'footer' => 'Show Icons in Footer',
                                'share'  => 'Enable Share Buttons',
                                'copy'   => 'Enable Copy Link',
                                'email'  => 'Enable Email Share',
                                'rss'    => 'Show RSS Icon',
                                'blank'  => 'Open Links in New Tab'

                            ];

                            foreach ($display as $key => $label) :

                            ?>

                                <tr>

                                    <th>

                                        <?php echo esc_html($label); ?>

                                    </th>

                                    <td>

                                        <label>

                                            <input
                                                type="checkbox"
                                                value="1"
                                                name="isago_social_settings[<?php echo esc_attr($key); ?>]"
                                                <?php checked(
                                                    $options[$key] ?? 1,
                                                    1
                                                ); ?>
                                            >

                                            Enable

                                        </label>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            </div>


        </div>


        <div class="isago-builder-submit">

            <?php submit_button(
                'Save Social Settings'
            ); ?>

        </div>


    </form>

</div>

<?php

}