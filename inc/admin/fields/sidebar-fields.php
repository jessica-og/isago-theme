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
                <i class="dashicons dashicons-admin-generic"></i>
            </span>

            <span>
                Sidebar
            </span>

        </span>

        <span class="isago-builder-toggle">
            <i class="dashicons dashicons-arrow-down-alt2"></i>
        </span>

    </button>


    <div class="isago-builder-card-content">

        <table class="form-table">

            <tbody>


                <!-- Latest Posts Count -->

                <tr>

                    <th>
                        Latest Posts Count
                    </th>

                    <td>

                        <input
                            type="number"
                            min="3"
                            max="10"
                            name="isago_home_settings[sidebar_latest_posts]"
                            value="<?php echo esc_attr(
                                $options['sidebar_latest_posts'] ?? 5
                            ); ?>"
                        >

                    </td>

                </tr>


                <!-- Show Thumbnail -->

                <tr>

                    <th>
                        Show Thumbnail
                    </th>

                    <td>

                        <label>

                            <input
                                type="checkbox"
                                name="isago_home_settings[sidebar_latest_thumb]"
                                value="1"
                                <?php checked(
                                    $options['sidebar_latest_thumb'] ?? 1,
                                    1
                                ); ?>
                            >

                            Enable

                        </label>

                    </td>

                </tr>


                <!-- Show Date -->

                <tr>

                    <th>
                        Show Date
                    </th>

                    <td>

                        <label>

                            <input
                                type="checkbox"
                                name="isago_home_settings[sidebar_latest_date]"
                                value="1"
                                <?php checked(
                                    $options['sidebar_latest_date'] ?? 1,
                                    1
                                ); ?>
                            >

                            Enable

                        </label>

                    </td>

                </tr>


                <!-- Featured Story -->

                <tr>

                    <th>
                        Featured Sidebar Story
                    </th>

                    <td>

                        <?php

                        $posts = isago_admin_posts();

                        ?>

                        <select
                            name="isago_home_settings[sidebar_featured_post]"
                        >

                            <option value="">
                                Select Post
                            </option>

                            <?php foreach ($posts as $post) : ?>

                                <option
                                    value="<?php echo esc_attr(
                                        $post->ID
                                    ); ?>"
                                    <?php selected(
                                        $options['sidebar_featured_post'] ?? '',
                                        $post->ID
                                    ); ?>
                                >

                                    <?php echo esc_html(
                                        $post->post_title
                                    ); ?>

                                </option>

                            <?php endforeach; ?>

                        </select>

                    </td>

                </tr>


                <!-- Editorial Category -->

                <tr>

                    <th>
                        Editorial Category
                    </th>

                    <td>

                        <?php

         $editorial_categories = get_categories([

            'hide_empty' => false,

            'orderby' => 'name',

            'order' => 'ASC'

        ]);

        ?>

        <select
            name="isago_home_settings[sidebar_editorial_category]"
        >

            <option value="">
                Select Subcategory
            </option>

            <?php foreach ($editorial_categories as $category) : ?>

                <?php

                /*
                 * Skip parent/top-level categories.
                 */

                if ((int) $category->parent === 0) {
                    continue;
                }

                ?>

                <option
                    value="<?php echo esc_attr($category->slug); ?>"
                    <?php selected(
                        $options['sidebar_editorial_category'] ?? '',
                        $category->slug
                    ); ?>
                >

                    <?php echo esc_html($category->name); ?>

                </option>

            <?php endforeach; ?>

        </select>

                        ?>

                    </td>

                </tr>


                <!-- Editorial Posts -->

                <tr>

                    <th>
                        Editorial Posts
                    </th>

                    <td>

                        <input
                            type="number"
                            min="1"
                            max="10"
                            name="isago_home_settings[sidebar_editorial_posts]"
                            value="<?php echo esc_attr(
                                $options['sidebar_editorial_posts'] ?? 2
                            ); ?>"
                        >

                    </td>

                </tr>


                <!-- Sidebar Videos -->

                <tr>

                    <th colspan="2">

                        <hr>

                        <h2>
                            Sidebar Videos
                        </h2>

                    </th>

                </tr>


                <!-- Video Category -->

            

                <!-- Videos -->

                <tr>

                    <th>
                        Popular Videos to Show
                    </th>

                    <td>

                        <input
                            type="number"
                            min="2"
                            max="8"
                            name="isago_home_settings[sidebar_video_posts]"
                            value="<?php echo esc_attr(
                                $options['sidebar_video_posts'] ?? 3
                            ); ?>"
                        >

                    </td>

                </tr>


            </tbody>

        </table>

    </div>

</section>