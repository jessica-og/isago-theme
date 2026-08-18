<?php

if (!defined('ABSPATH')) {
    exit;
}

/*=========================================================
Category Archive Settings
=========================================================*/

add_action(
    'admin_menu',
    'isago_category_archive_menu'
);

add_action(
    'admin_init',
    'isago_register_category_archive'
);


/*=========================================================
Admin Menu
=========================================================*/

function isago_category_archive_menu()
{

    add_submenu_page(

        'isago-home',

        'Category Archive',

        'Category Archive',

        'manage_options',

        'isago-category-archive',

        'isago_category_archive_page'

    );

}


/*=========================================================
Register Settings
=========================================================*/

function isago_register_category_archive()
{

    register_setting(

        'isago_category_archive_options',

        'isago_category_archive_settings'

    );

}


/*=========================================================
Category Archive Page
=========================================================*/

function isago_category_archive_page()
{

    $options = isago_get_option(
        'isago_category_archive_settings'
    );

?>

<div class="wrap isago-admin isago-category-archive">

    <h1>
        Category Archive
    </h1>


    <form
        method="post"
        action="options.php"
        class="isago-admin-form"
    >

        <?php

        settings_fields(
            'isago_category_archive_options'
        );

        ?>


        <div class="isago-builder-sections">


            <!-- =================================================
            Layout
            ================================================= -->

            <section class="isago-builder-card">

                <button
                    type="button"
                    class="isago-builder-card-header"
                    aria-expanded="false"
                >

                    <span class="isago-builder-card-title">

                        <span class="isago-builder-card-icon">

                            <i class="dashicons dashicons-layout"></i>

                        </span>

                        <span>
                            Layout
                        </span>

                    </span>


                    <span class="isago-builder-toggle">

                        <i class="dashicons dashicons-arrow-down-alt2"></i>

                    </span>

                </button>


                <div class="isago-builder-card-content">

                    <table class="form-table">

                        <tbody>


                            <!-- Featured -->

                            <tr>

                                <th>
                                    Show Featured Story
                                </th>

                                <td>

                                    <label>

                                        <input
                                            type="checkbox"
                                            name="isago_category_archive_settings[featured]"
                                            value="1"
                                            <?php checked(
                                                $options['featured'] ?? 1,
                                                1
                                            ); ?>
                                        >

                                        Enable

                                    </label>

                                </td>

                            </tr>


                            <!-- Toolbar -->

                            <tr>

                                <th>
                                    Show Toolbar
                                </th>

                                <td>

                                    <label>

                                        <input
                                            type="checkbox"
                                            name="isago_category_archive_settings[toolbar]"
                                            value="1"
                                            <?php checked(
                                                $options['toolbar'] ?? 1,
                                                1
                                            ); ?>
                                        >

                                        Enable

                                    </label>

                                </td>

                            </tr>


                            <!-- Breadcrumb -->

                            <tr>

                                <th>
                                    Show Breadcrumb
                                </th>

                                <td>

                                    <label>

                                        <input
                                            type="checkbox"
                                            name="isago_category_archive_settings[breadcrumb]"
                                            value="1"
                                            <?php checked(
                                                $options['breadcrumb'] ?? 1,
                                                1
                                            ); ?>
                                        >

                                        Enable

                                    </label>

                                </td>

                            </tr>


                            <!-- Sidebar -->

                            <tr>

                                <th>
                                    Show Sidebar
                                </th>

                                <td>

                                    <label>

                                        <input
                                            type="checkbox"
                                            name="isago_category_archive_settings[sidebar]"
                                            value="1"
                                            <?php checked(
                                                $options['sidebar'] ?? 1,
                                                1
                                            ); ?>
                                        >

                                        Enable

                                    </label>

                                </td>

                            </tr>


                            <!-- Posts -->

                            <tr>

                                <th>
                                    Posts Per Page
                                </th>

                                <td>

                                    <input
                                        type="number"
                                        min="6"
                                        max="24"
                                        name="isago_category_archive_settings[posts]"
                                        value="<?php echo esc_attr(
                                            $options['posts'] ?? 9
                                        ); ?>"
                                    >

                                </td>

                            </tr>


                            <!-- Columns -->

                            <tr>

                                <th>
                                    Grid Columns
                                </th>

                                <td>

                                    <select
                                        name="isago_category_archive_settings[columns]"
                                    >

                                        <option
                                            value="2"
                                            <?php selected(
                                                $options['columns'] ?? 3,
                                                2
                                            ); ?>
                                        >
                                            2 Columns
                                        </option>


                                        <option
                                            value="3"
                                            <?php selected(
                                                $options['columns'] ?? 3,
                                                3
                                            ); ?>
                                        >
                                            3 Columns
                                        </option>


                                        <option
                                            value="4"
                                            <?php selected(
                                                $options['columns'] ?? 3,
                                                4
                                            ); ?>
                                        >
                                            4 Columns
                                        </option>

                                    </select>

                                </td>

                            </tr>


                        </tbody>

                    </table>

                </div>

            </section>


            <!-- =================================================
            Toolbar
            ================================================= -->

            <section class="isago-builder-card">

                <button
                    type="button"
                    class="isago-builder-card-header"
                    aria-expanded="false"
                >

                    <span class="isago-builder-card-title">

                        <span class="isago-builder-card-icon">

                            <i class="dashicons dashicons-filter"></i>

                        </span>

                        <span>
                            Toolbar
                        </span>

                    </span>


                    <span class="isago-builder-toggle">

                        <i class="dashicons dashicons-arrow-down-alt2"></i>

                    </span>

                </button>


                <div class="isago-builder-card-content">

                    <table class="form-table">

                        <tbody>


                            <!-- Default Filter -->

                            <tr>

                                <th>
                                    Default Filter
                                </th>

                                <td>

                                    <select
                                        name="isago_category_archive_settings[filter]"
                                    >

                                        <option
                                            value="latest"
                                            <?php selected(
                                                $options['filter'] ?? 'latest',
                                                'latest'
                                            ); ?>
                                        >
                                            Latest
                                        </option>


                                        <option
                                            value="popular"
                                            <?php selected(
                                                $options['filter'] ?? 'latest',
                                                'popular'
                                            ); ?>
                                        >
                                            Popular
                                        </option>


                                        <option
                                            value="trending"
                                            <?php selected(
                                                $options['filter'] ?? 'latest',
                                                'trending'
                                            ); ?>
                                        >
                                            Trending
                                        </option>

                                    </select>

                                </td>

                            </tr>


                            <!-- Default Sort -->

                            <tr>

                                <th>
                                    Default Sort
                                </th>

                                <td>

                                    <select
                                        name="isago_category_archive_settings[sort]"
                                    >

                                        <option
                                            value="latest"
                                            <?php selected(
                                                $options['sort'] ?? 'latest',
                                                'latest'
                                            ); ?>
                                        >
                                            Latest
                                        </option>


                                        <option
                                            value="oldest"
                                            <?php selected(
                                                $options['sort'] ?? 'latest',
                                                'oldest'
                                            ); ?>
                                        >
                                            Oldest
                                        </option>


                                        <option
                                            value="az"
                                            <?php selected(
                                                $options['sort'] ?? 'latest',
                                                'az'
                                            ); ?>
                                        >
                                            Title A-Z
                                        </option>


                                        <option
                                            value="za"
                                            <?php selected(
                                                $options['sort'] ?? 'latest',
                                                'za'
                                            ); ?>
                                        >
                                            Title Z-A
                                        </option>

                                    </select>

                                </td>

                            </tr>


                        </tbody>

                    </table>

                </div>

            </section>


        </div>


        <div class="isago-builder-submit">

            <?php

            submit_button(
                'Save Category Archive Settings'
            );

            ?>

        </div>


    </form>

</div>

<?php

}