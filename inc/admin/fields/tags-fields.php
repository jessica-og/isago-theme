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
                <i class="dashicons dashicons-tag"></i>
            </span>

            <span>
                Popular Tags
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
                        Number of Tags
                    </th>

                    <td>

                        <input
                            type="number"
                            min="5"
                            max="30"
                            name="isago_home_settings[popular_tags_count]"
                            value="<?php echo esc_attr(
                                $options['popular_tags_count'] ?? 12
                            ); ?>"
                        >

                    </td>

                </tr>

            </tbody>

        </table>

    </div>

</section>