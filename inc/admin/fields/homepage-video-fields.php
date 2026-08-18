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
                <i class="dashicons dashicons-video-alt3"></i>
            </span>

            <span>
                Homepage Video Section
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
                        Section Title
                    </th>

                    <td>

                        <input
                            type="text"
                            class="regular-text"
                            name="isago_home_settings[homepage_video_title]"
                            value="<?php echo esc_attr(
                                $options['homepage_video_title'] ?? 'Videos'
                            ); ?>"
                        >

                    </td>

                </tr>


                <tr>

                    <th>
                        Category
                    </th>

                    <td>

                        <?php

                        $video_category = get_term_by(
                            'name',
                            'AI Tutorials',
                            'category'
                        );

                        $video_category_slug = $video_category
                            ? $video_category->slug
                            : 'ai-tutorials';

                        ?>

                        <input
                            type="text"
                            class="regular-text"
                            value="AI Tutorials"
                            readonly
                        >

                        <input
                            type="hidden"
                            name="isago_home_settings[homepage_video_category]"
                            value="<?php echo esc_attr(
                                $video_category_slug
                            ); ?>"
                        >

                        <p class="description">

                            Homepage videos are restricted to the
                            <strong>AI Tutorials</strong> category.

                        </p>

                    </td>

                </tr>


                <tr>

                    <th>
                        Number of Videos
                    </th>

                    <td>

                        <input
                            type="number"
                            min="3"
                            max="6"
                            name="isago_home_settings[homepage_video_posts]"
                            value="<?php echo esc_attr(
                                $options['homepage_video_posts'] ?? 3
                            ); ?>"
                        >

                    </td>

                </tr>

            </tbody>

        </table>

    </div>

</section>