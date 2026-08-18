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
                <i class="dashicons dashicons-email-alt"></i>
            </span>

            <span>
                Newsletter
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
                        Show Newsletter
                    </th>

                    <td>

                        <label>

                            <input
                                type="checkbox"
                                name="isago_home_settings[newsletter_enable]"
                                value="1"
                                <?php checked(
                                    $options['newsletter_enable'] ?? 1,
                                    1
                                ); ?>
                            >

                            Enable Newsletter

                        </label>

                    </td>

                </tr>


                <tr>

                    <th>
                        Heading
                    </th>

                    <td>

                        <input
                            type="text"
                            class="regular-text"
                            name="isago_home_settings[newsletter_title]"
                            value="<?php echo esc_attr(
                                $options['newsletter_title']
                                ?? 'Stay informed, daily.'
                            ); ?>"
                        >

                    </td>

                </tr>


                <tr>

                    <th>
                        Description
                    </th>

                    <td>

                        <textarea
                            rows="3"
                            cols="60"
                            name="isago_home_settings[newsletter_text]"
                        ><?php

                        echo esc_textarea(

                            $options['newsletter_text']

                            ?? 'Get the latest AI stories delivered to your inbox.'

                        );

                        ?></textarea>

                    </td>

                </tr>


                <tr>

                    <th>
                        Button Text
                    </th>

                    <td>

                        <input
                            type="text"
                            class="regular-text"
                            name="isago_home_settings[newsletter_button]"
                            value="<?php echo esc_attr(
                                $options['newsletter_button']
                                ?? 'Subscribe'
                            ); ?>"
                        >

                    </td>

                </tr>

            </tbody>

        </table>

    </div>

</section>