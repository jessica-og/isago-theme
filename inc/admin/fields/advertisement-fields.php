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


                <!-- =================================================
                ADVERTISEMENT IMAGE
                ================================================= -->

                <tr>

                    <th>

                        Advertisement Image

                    </th>


                    <td>

                        <?php

                        $sidebar_ad_image_id = absint(
                            $options['sidebar_ad_image'] ?? 0
                        );

                        $sidebar_ad_image_url = '';

                        if ($sidebar_ad_image_id) {

                            $sidebar_ad_image_url =
                                wp_get_attachment_image_url(
                                    $sidebar_ad_image_id,
                                    'medium'
                                );

                        }

                        ?>

                        <input
                            type="hidden"
                            id="isago-sidebar-ad-image"
                            class="isago_image_id"
                            name="isago_home_settings[sidebar_ad_image]"
                            value="<?php echo esc_attr(
                                $sidebar_ad_image_id
                            ); ?>"
                        >


                        <img
                            id="isago-sidebar-ad-image-preview"
                            class="isago-preview"
                            src="<?php echo esc_url(
                                $sidebar_ad_image_url
                            ); ?>"
                            style="
                                max-width:240px;
                                width:100%;
                                height:auto;
                                display:<?php
                                    echo !empty(
                                        $sidebar_ad_image_url
                                    )
                                        ? 'block'
                                        : 'none';
                                ?>;
                                margin-bottom:12px;
                            "
                            alt="Advertisement preview"
                        >


                        <button
                            type="button"
                            class="button isago-upload-button"
                            data-target="#isago-sidebar-ad-image"
                            data-type="image"
                        >

                            Upload Image

                        </button>

                    </td>

                </tr>


                <!-- =================================================
                ADVERTISEMENT LINK
                ================================================= -->

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


                <!-- =================================================
                ENABLE ADVERTISEMENT
                ================================================= -->

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