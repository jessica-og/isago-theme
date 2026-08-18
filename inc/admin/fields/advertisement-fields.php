<?php

if (!defined('ABSPATH')) {
    exit;
}

?>

<section class="isago-builder-card">

    <button
        type="button"
        class="isago-builder-card-header"
        aria-expanded="false"
    >

        <span class="isago-builder-card-title">

            <span class="isago-builder-card-icon">
                <i class="dashicons dashicons-megaphone"></i>
            </span>

            <span>
                Sidebar Advertisement
            </span>

        </span>

        <span class="isago-builder-toggle">
            <i class="dashicons dashicons-arrow-down-alt2"></i>
        </span>

    </button>


    <div class="isago-builder-card-content">

        <table class="form-table">

            <tbody>

                <tr>

                    <th>
                        Advertisement Image
                    </th>

                    <td>

                        <input
                            type="hidden"
                            class="isago-image-url"
                            name="isago_home_settings[sidebar_ad_image]"
                            value="<?php echo esc_attr(
                                $options['sidebar_ad_image'] ?? ''
                            ); ?>"
                        >

                        <img
                            class="isago-preview"
                            src="<?php echo esc_url(
                                $options['sidebar_ad_image'] ?? ''
                            ); ?>"
                            style="
                                max-width:240px;
                                display:block;
                                margin-bottom:12px;
                            "
                            alt=""
                        >

                        <button
                            type="button"
                            class="button isago-upload-button"
                        >
                            Upload Image
                        </button>

                    </td>

                </tr>


                <tr>

                    <th>
                        Advertisement Link
                    </th>

                    <td>

                        <input
                            type="url"
                            class="regular-text"
                            name="isago_home_settings[sidebar_ad_link]"
                            value="<?php echo esc_attr(
                                $options['sidebar_ad_link'] ?? ''
                            ); ?>"
                        >

                    </td>

                </tr>


                <tr>

                    <th>
                        Enable Advertisement
                    </th>

                    <td>

                        <label>

                            <input
                                type="checkbox"
                                value="1"
                                name="isago_home_settings[sidebar_ad_enable]"
                                <?php checked(
                                    $options['sidebar_ad_enable'] ?? 1,
                                    1
                                ); ?>
                            >

                            Show Advertisement

                        </label>

                    </td>

                </tr>

            </tbody>

        </table>

    </div>

</section>