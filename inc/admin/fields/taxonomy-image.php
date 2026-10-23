<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
ISAGO TAXONOMY HERO IMAGES
=========================================================

ONE IMAGE FOR ALL CATEGORIES
ONE IMAGE FOR ALL TAGS
  
Stored globally in:

isago_taxonomy_hero_images

=========================================================*/


/*
|--------------------------------------------------------------------------
| Register Settings
|--------------------------------------------------------------------------
*/

add_action(
    'admin_init',
    'isago_register_taxonomy_hero_settings'
);

function isago_register_taxonomy_hero_settings()
{

    register_setting(
        'isago_taxonomy_hero_images',
        'isago_taxonomy_hero_images',
        [
            'type'              => 'array',
            'sanitize_callback' => 'isago_sanitize_taxonomy_hero_images',
            'default'           => [
                'category_image_id' => 0,
                'tag_image_id'      => 0,
            ],
        ]
    );

}


/*
|--------------------------------------------------------------------------
| Sanitize Settings
|--------------------------------------------------------------------------
*/

function isago_sanitize_taxonomy_hero_images($input)
{

    if (!is_array($input)) {

        return [
            'category_image_id' => 0,
            'tag_image_id'      => 0,
        ];

    }


    /*
     * These are now CENTRAL ISAGO MEDIA IDs.
     *
     * They are NOT WordPress attachment IDs.
     */

    $category_image_id =
        absint(
            $input['category_image_id'] ?? 0
        );


    $tag_image_id =
        absint(
            $input['tag_image_id'] ?? 0
        );


    /*
     * --------------------------------------------------------
     * CATEGORY HERO
     * --------------------------------------------------------
     */

    $category_result =
        isago_media_set_field(
            'taxonomy_hero',
            1,
            'category_hero_image',
            $category_image_id
        );


    /*
     * --------------------------------------------------------
     * TAG HERO
     * --------------------------------------------------------
     */

    $tag_result =
        isago_media_set_field(
            'taxonomy_hero',
            1,
            'tag_hero_image',
            $tag_image_id
        );


    /*
     * If the media system reports an error, keep the
     * submitted ID out of the saved option.
     */

    if (
        is_wp_error($category_result)
    ) {

        $category_image_id = 0;

        error_log(
            'Isago Taxonomy Hero: Could not save category media field. '
            . $category_result->get_error_message()
        );

    }


    if (
        is_wp_error($tag_result)
    ) {

        $tag_image_id = 0;

        error_log(
            'Isago Taxonomy Hero: Could not save tag media field. '
            . $tag_result->get_error_message()
        );

    }


    return [

        'category_image_id' =>
            $category_image_id,

        'tag_image_id' =>
            $tag_image_id,

    ];

}


/*
|--------------------------------------------------------------------------
| Get Settings
|--------------------------------------------------------------------------
*/

function isago_get_taxonomy_hero_images()
{

    $settings = get_option(
        'isago_taxonomy_hero_images',
        []
    );


    return wp_parse_args(
        is_array($settings)
            ? $settings
            : [],
        [
            'category_image_id' => 0,
            'tag_image_id'      => 0,
        ]
    );

}


/*=========================================================
GET CATEGORY HERO MEDIA ID
=========================================================*/

function isago_get_category_hero_image_id()
{

    $settings =
        isago_get_taxonomy_hero_images();


    return absint(
        $settings['category_image_id'] ?? 0
    );

}


/*=========================================================
GET TAG HERO MEDIA ID
=========================================================*/

function isago_get_tag_hero_image_id()
{

    $settings =
        isago_get_taxonomy_hero_images();


    return absint(
        $settings['tag_image_id'] ?? 0
    );

}





/*=========================================================
GET CATEGORY HERO IMAGE URL
=========================================================*/

function isago_get_category_hero_image_url(
    $size = 'large'
) {

    $media_id =
        isago_get_category_hero_image_id();

    if (!$media_id) {
        return '';
    }

    /*
     * New Isago central media / ImageKit.
     */
    $media =
        isago_media_get(
            $media_id
        );

    if ($media) {

        return
            isago_media_get_field_image_url(
                'taxonomy_hero',
                1,
                'category_hero_image',
                $size
            );

    }

    /*
     * Legacy WordPress attachment.
     */
    if (
        wp_attachment_is_image(
            $media_id
        )
    ) {

        return
            wp_get_attachment_image_url(
                $media_id,
                $size
            );

    }

    return '';
}


/*=========================================================
GET TAG HERO IMAGE URL
=========================================================*/

function isago_get_tag_hero_image_url(
    $size = 'large'
) {

    $media_id =
        isago_get_tag_hero_image_id();

    if (!$media_id) {
        return '';
    }

    /*
     * New Isago central media / ImageKit.
     */
    $media =
        isago_media_get(
            $media_id
        );

    if ($media) {

        return
            isago_media_get_field_image_url(
                'taxonomy_hero',
                1,
                'tag_hero_image',
                $size
            );

    }

    /*
     * Legacy WordPress attachment.
     */
    if (
        wp_attachment_is_image(
            $media_id
        )
    ) {

        return
            wp_get_attachment_image_url(
                $media_id,
                $size
            );

    }

    return '';
}


/*=========================================================
GET CURRENT TAXONOMY HERO IMAGE URL
=========================================================*/

function isago_get_taxonomy_hero_image_url(
    $size = 'large'
) {

    if (is_category()) {

        return
            isago_get_category_hero_image_url(
                $size
            );

    }

    if (is_tag()) {

        return
            isago_get_tag_hero_image_url(
                $size
            );

    }

    return '';
}



/*
|--------------------------------------------------------------------------
| Get Current Taxonomy Hero Image ID
|--------------------------------------------------------------------------
*/

function isago_get_taxonomy_hero_image_id()
{

    if (is_category()) {

        return isago_get_category_hero_image_id();

    }


    if (is_tag()) {

        return isago_get_tag_hero_image_id();

    }


    return 0;

}


/*=========================================================
ADMIN PAGE
=========================================================*/


/*
|--------------------------------------------------------------------------
| Add Settings Page
|--------------------------------------------------------------------------
*/

add_action(
    'admin_menu',
    'isago_taxonomy_hero_menu'
);

function isago_taxonomy_hero_menu()
{

    add_submenu_page(

        'isago-home',

        'Taxonomy Hero Images',

        'Taxonomy Hero Images',

        'manage_options',

        'isago-taxonomy-hero-images',

        'isago_taxonomy_hero_page'

    );

}


/*
|--------------------------------------------------------------------------
| Settings Page
|--------------------------------------------------------------------------
*/

function isago_taxonomy_hero_page()
{

    if (!current_user_can('manage_options')) {

        return;

    }


    $settings = isago_get_taxonomy_hero_images();


    /*
    ---------------------------------------------------------
    Category Image
    ---------------------------------------------------------
    */

    $category_image_id = absint(
        $settings['category_image_id'] ?? 0
    );

  $category_image_url =
    isago_get_category_hero_image_url(
        'large'
    );


    /*
    ---------------------------------------------------------
    Tag Image
    ---------------------------------------------------------
    */

    $tag_image_id = absint(
        $settings['tag_image_id'] ?? 0
    );

$tag_image_url =
    isago_get_tag_hero_image_url(
        'large'
    );





    ?>

    <div class="wrap isago-admin">

        <h1>
            Taxonomy Hero Images
        </h1>


        <p>
            Configure the shared hero images used by
            Isago category and tag archives.
        </p>


        <form
            method="post"
            action="options.php"
        >

            <?php

            settings_fields(
                'isago_taxonomy_hero_images'
            );

            ?>


            <div class="isago-admin-grid">


                <!--=================================================
                CATEGORY IMAGE
                =================================================-->

                <div class="isago-card">

                    <h2>
                        Category Hero Image
                    </h2>


                    <p>
                        This single image is used on
                        <strong>all categories</strong>,
                        including child and deeper categories.
                    </p>


                  <input
    type="hidden"
    id="isago-category-hero-image"
    name="isago_taxonomy_hero_images[category_image_id]"
    class="isago-taxonomy-hero-input"
    value="<?php echo esc_attr(
        $category_image_id
    ); ?>"
>


<?php

$category_account_id =
    isago_get_imagekit_media_account_id(
        $category_image_id
    );


isago_render_imagekit_account_selector(
    [
        'id'       => 'isago-category-hero-image-account',
        'selected' => $category_account_id,
        'label'    => 'ImageKit Account',
        'class'    => 'isago-imagekit-account-selector',
        'style'    => 'width:100%; margin-bottom:10px;',
    ]
);

isago_render_imagekit_folder_selector(
    [
        'id'       => 'isago-category-hero-image-folder',
        'selected' => '/Isago-Media/Site-Assets/',
        'label'    => 'Destination folder',
        'class'    => 'isago-imagekit-folder-selector',
        'style'    => 'width:100%; margin-bottom:10px;',
    ]
);

?>


<div
       id="isago-category-hero-preview"
    class="isago-taxonomy-hero-preview"
    style="<?php
        echo $category_image_url
            ? ''
            : 'display:none;';
    ?>"
>

    <img
        src="<?php echo esc_url(
            $category_image_url
        ); ?>"
        alt="Category hero image preview"
         style="height: 150px; width:150px"
    >

</div>


<button
    type="button"
    class="button button-primary isago-imagekit-upload-button isago-taxonomy-hero-upload"
    data-target="#isago-category-hero-image"
    data-preview="#isago-category-hero-preview"
    data-type="image"
    data-account-selector="#isago-category-hero-image-account"
    data-folder-selector="#isago-category-hero-image-folder"
    data-object-type="taxonomy_hero"
    data-object-id="1"
    data-field-key="category_hero_image"
    style="margin-right:5px;"
>

    <?php

    echo $category_image_id
        ? 'Change Category Hero Image'
        : 'Upload Category Hero Image';

    ?>

</button>
<button
    type="button"
    class="button isago-imagekit-select-existing-button"
    data-target="#isago-category-hero-image"
    data-preview="#isago-category-hero-preview"
    data-type="image"
    data-account-selector="#isago-category-hero-image-account"
    data-folder-selector="#isago-category-hero-image-folder"
>
    Select Existing ImageKit Image
</button>


<button
    type="button"
    class="button isago-imagekit-remove-button isago-taxonomy-hero-remove"
    data-target="#isago-category-hero-image"
    data-preview="#isago-category-hero-preview"
    data-type="image"
    data-object-type="taxonomy_hero"
    data-object-id="1"
    data-field-key="category_hero_image"
    <?php
    if (!$category_image_id) :
        ?>
        disabled
        style="display:none;"
    <?php endif; ?>
>

    Remove Image

</button>

                </div>


                <!--=================================================
                TAG IMAGE
                =================================================-->

                <div class="isago-card">

                    <h2>
                        Tag Hero Image
                    </h2>


                    <p>
                        This single image is used on
                        <strong>all tag archives</strong>.
                    </p>


                  <input
    type="hidden"
    id="isago-tag-hero-image"
    name="isago_taxonomy_hero_images[tag_image_id]"
    class="isago-taxonomy-hero-input"
    value="<?php echo esc_attr(
        $tag_image_id
    ); ?>"
>


<?php

$tag_account_id =
    isago_get_imagekit_media_account_id(
        $tag_image_id
    );


isago_render_imagekit_account_selector(
    [
        'id'       => 'isago-tag-hero-image-account',
        'selected' => $tag_account_id,
        'label'    => 'ImageKit Account',
        'class'    => 'isago-imagekit-account-selector',
        'style'    => 'width:100%; margin-bottom:10px;',
    ]
);
isago_render_imagekit_folder_selector(
    [
        'id'       => 'isago-category-hero-image-folder',
        'selected' => '/Isago-Media/Site-Assets/',
        'label'    => 'Destination folder',
        'class'    => 'isago-imagekit-folder-selector',
        'style'    => 'width:100%; margin-bottom:10px;',
    ]
);
?>


<div
       id="isago-tag-hero-preview"
    class="isago-taxonomy-hero-preview"
    style="<?php
        echo $tag_image_url
            ? ''
            : 'display:none; ';
    ?>"
>

    <img
        src="<?php echo esc_url(
            $tag_image_url
        ); ?>"
        alt="Tag hero image preview"
          style="height: 150px; width:150px"
    >

</div>


<button
    type="button"
    class="button button-primary isago-imagekit-upload-button isago-taxonomy-hero-upload"
    data-target="#isago-tag-hero-image"
    data-preview="#isago-tag-hero-preview"
    data-type="image"
    data-account-selector="#isago-tag-hero-image-account"
    data-folder-selector="#isago-tag-hero-image-folder"
    data-object-type="taxonomy_hero"
    data-object-id="1"
    data-field-key="tag_hero_image"
    style="margin-right:5px;"
>

    <?php

    echo $tag_image_id
        ? 'Change Tag Hero Image'
        : 'Upload Tag Hero Image';

    ?>

</button>
<button
    type="button"
    class="button isago-imagekit-select-existing-button"
    data-target="#isago-tag-hero-image"
    data-preview="#isago-tag-hero-preview"
    data-type="image"
    data-account-selector="#isago-tag-hero-image-account"
    data-folder-selector="#isago-tag-hero-image-folder"
>
    Select Existing ImageKit Image
</button>



<button
    type="button"
    class="button isago-imagekit-remove-button isago-taxonomy-hero-remove"
    data-target="#isago-tag-hero-image"
    data-preview="#isago-tag-hero-preview"
    data-type="image"
    data-object-type="taxonomy_hero"
    data-object-id="1"
    data-field-key="tag_hero_image"
    <?php
    if (!$tag_image_id) :
        ?>
        disabled
        style="display:none;"
    <?php endif; ?>
>

    Remove Image

</button>
                </div>


            </div>


            <?php

            submit_button(
                'Save Hero Images'
            );

            ?>

        </form>

    </div>

    <?php

}







/**
 * Return existing ImageKit images for the taxonomy hero picker.
 */
add_action(
    'wp_ajax_isago_taxonomy_hero_list_images',
    'isago_taxonomy_hero_list_images'
);

function isago_taxonomy_hero_list_images() {

    if (!current_user_can('manage_options')) {
        wp_send_json_error(
            ['message' => 'You do not have permission to view these images.'],
            403
        );
    }

    check_ajax_referer(
        isago_imagekit_upload_nonce_action(),
        'nonce'
    );

    if (!function_exists('isago_media_table')) {
        wp_send_json_error(
            ['message' => 'Isago media functions are unavailable.'],
            500
        );
    }

    global $wpdb;

    $table = isago_media_table();

    $rows = $wpdb->get_results(
        "SELECT id, account_id, file_id, file_path, url,
                thumbnail_url, original_name, width, height
         FROM {$table}
         WHERE source = 'imagekit'
           AND media_type = 'image'
         ORDER BY id DESC
         LIMIT 500"
    );

    if ($rows === null && $wpdb->last_error) {
        wp_send_json_error(
            ['message' => 'Could not load the ImageKit image library.'],
            500
        );
    }

    $items = [];

    foreach ((array) $rows as $row) {
        if (empty($row->url) || empty($row->id)) {
            continue;
        }

        $items[] = [
            'id'            => absint($row->id),
            'account_id'    => sanitize_key($row->account_id),
            'file_id'       => sanitize_text_field($row->file_id),
            'file_path'     => sanitize_textarea_field($row->file_path),
            'url'           => esc_url_raw($row->url),
            'thumbnail_url' => esc_url_raw(
                $row->thumbnail_url ?: $row->url
            ),
            'original_name' => sanitize_text_field($row->original_name),
            'width'         => absint($row->width),
            'height'        => absint($row->height),
        ];
    }

    wp_send_json_success([
        'items' => $items,
    ]);
}