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
                <i class="dashicons dashicons-format-image"></i>
            </span>

            <span>
                Hero Settings
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
                        Featured Hero Post
                    </th>

                    <td>

                        <?php

                        $featured_posts =
                            isago_admin_posts();

                        ?>

                        <select
                            name="isago_home_settings[hero_featured]"
                        >

                            <option value="">
                                Select Featured Post
                            </option>

                            <?php foreach (
                                $featured_posts
                                as $post
                            ) : ?>

                               <option
                    value="<?php echo esc_attr($post->ID); ?>"
                    <?php selected(
                        $options['hero_featured'] ?? '',
                        $post->ID
                    ); ?>
                >

                    <?php echo esc_html($post->post_title); ?>

                </option>

                            <?php endforeach; ?>

                        </select>

                    </td>

                </tr>


                <tr>

                    <th>
                        Left Cards Category
                    </th>

                    <td>
 <?php

        /*
         * Get ONLY top-level categories.
         *
         * parent => 0 means the category has no parent.
         */

        $hero_parent_categories = get_categories([

            'taxonomy'   => 'category',

            'parent'     => 0,

            'hide_empty' => false,

            'orderby'    => 'name',

            'order'      => 'ASC',

        ]);

        ?>

        <select
            name="isago_home_settings[hero_left_category]"
        >

            <option value="">
                Select Parent Category
            </option>

            <?php foreach ($hero_parent_categories as $category) : ?>

                <option
                    value="<?php echo esc_attr($category->slug); ?>"
                    <?php selected(
                        $options['hero_left_category'] ?? '',
                        $category->slug
                    ); ?>
                >

                    <?php echo esc_html($category->name); ?>

                </option>

            <?php endforeach; ?>

        </select>


                    </td>

                </tr>

            </tbody>

        </table>

    </div>

</section>