<?php

if (!defined('ABSPATH')) {
    exit;
}

?>

<?php for ($i = 1; $i <= 8; $i++) : ?>
<?php
    $is_parent_section = ($i <= 3);

    $selected_category =
        $options["section{$i}_category"] ?? '';

    $categories = get_categories([

        'taxonomy'   => 'category',

        'hide_empty' => false,

        'orderby'    => 'name',

        'order'      => 'ASC',

    ]);
    $allowed_categories = [];

    foreach ($categories as $category) {
        if ($is_parent_section) {

            if ((int) $category->parent === 0) {

                $allowed_categories[] = $category;

            }

        }

        else {

            if ((int) $category->parent !== 0) {

                $allowed_categories[] = $category;

            }

        }

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
                <i class="dashicons dashicons-screenoptions"></i>
            </span>

            <span>
                Homepage Section <?php echo $i; ?>
            </span>

        </span>

        <span class="isago-builder-toggle">

            <i class="dashicons dashicons-arrow-down-alt2"></i>

        </span>

    </button>


    <div class="isago-builder-card-content">

        <table class="form-table">

            <tbody>


                <!-- Title -->

                <tr>

                    <th>
                        Title
                    </th>

                    <td>

                        <input
                            class="regular-text"
                            type="text"
                            name="isago_home_settings[section<?php echo $i; ?>_title]"
                            value="<?php echo esc_attr(
                                $options["section{$i}_title"] ?? ''
                            ); ?>"
                        >

                    </td>

                </tr>


                <!-- Category -->

                <tr>

                    <th>
                        Category
                    </th>

                    <td>

                      <select
                name="isago_home_settings[section<?php echo esc_attr($i); ?>_category]"
            >

                <option value="">
                    Select Category
                </option>

                <?php foreach ($allowed_categories as $category) : ?>

                    <option
                        value="<?php echo esc_attr($category->slug); ?>"
                        <?php selected(
                            $selected_category,
                            $category->slug
                        ); ?>
                    >

                        <?php echo esc_html($category->name); ?>

                    </option>

                <?php endforeach; ?>

            </select>

            <p class="description">

                <?php if ($is_parent_section) : ?>

                    Select a parent category.

                <?php else : ?>

                    Select a subcategory.

                <?php endif; ?>

            </p>

                    </td>

                </tr>


                <!-- Number of Posts -->

                <tr>

                    <th>
                        Number of Posts
                    </th>

                    <td>

                        <input
                            type="number"
                            min="3"
                            max="8"
                            name="isago_home_settings[section<?php echo $i; ?>_posts]"
                            value="<?php echo esc_attr(
                                $options["section{$i}_posts"] ?? 4
                            ); ?>"
                        >

                    </td>

                </tr>


                <!-- Layout -->

                <tr>

                    <th>
                        Layout
                    </th>

                    <td>

                        <select
                            name="isago_home_settings[section<?php echo $i; ?>_layout]"
                        >

                            <option
                                value="editorial"
                                <?php selected(
                                    $options["section{$i}_layout"] ?? 'editorial',
                                    'editorial'
                                ); ?>
                            >
                                Editorial
                            </option>


                            <option
                                value="magazine"
                                <?php selected(
                                    $options["section{$i}_layout"] ?? 'editorial',
                                    'magazine'
                                ); ?>
                            >
                                Magazine
                            </option>


                            <option
                                value="list"
                                <?php selected(
                                    $options["section{$i}_layout"] ?? 'editorial',
                                    'list'
                                ); ?>
                            >
                                List
                            </option>


                            <option
                                value="grid"
                                <?php selected(
                                    $options["section{$i}_layout"] ?? 'editorial',
                                    'grid'
                                ); ?>
                            >
                                Grid
                            </option>

                        </select>

                    </td>

                </tr>


                <!-- Advertisement -->

                <tr>

                    <th>
                        Advertisement
                    </th>

                    <td>

                        <label>

                            <input
                                type="checkbox"
                                value="1"
                                name="isago_home_settings[section<?php echo $i; ?>_ad]"
                                <?php checked(
                                    $options["section{$i}_ad"] ?? 0,
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

<?php endfor; ?>