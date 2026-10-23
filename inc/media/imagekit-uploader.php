
<?php
/**
 * Isago Reusable ImageKit Uploader Helpers
 *
 * Provides shared folder options and a reusable folder selector.
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Approved destination folders.
 *
 * Keep these paths consistent with ImageKit Manager.
 */
if (!function_exists('isago_imagekit_folder_options')) {
    function isago_imagekit_folder_options() {
        return [
            '/Isago-Media/Site-Assets/' =>
                'Site Assets',

            '/Isago-Media/AI-News-Articles/' =>
                'AI News Articles',

            '/Isago-Media/Blog-Posts-Articles/' =>
                'Blog Posts Articles',

            '/Isago-Media/AI-Prompts/' =>
                'AI Prompts',

            '/Isago-Media/AI-Tools/' =>
                'AI Tools',
        ];
    }
}

/**
 * Render a reusable ImageKit destination-folder selector.
 *
 * Usage:
 *
 * isago_render_imagekit_folder_selector([
 *     'id'       => 'my-image-folder',
 *     'selected' => '/Isago-Media/Blog-Posts-Articles/',
 *     'label'    => 'Destination folder',
 * ]);
 */
if (!function_exists('isago_render_imagekit_folder_selector')) {
    function isago_render_imagekit_folder_selector($args = []) {
        $args = wp_parse_args(
            $args,
            [
                'id'       => 'isago-imagekit-folder',
                'selected' => '/Isago-Media/Site-Assets/',
                'label'    => 'Destination folder',
                'class'    => 'isago-imagekit-folder-selector',
                'style'    => 'width:100%;',
            ]
        );

        $id = sanitize_html_class($args['id']);
        $selected = (string) $args['selected'];
        $folders = isago_imagekit_folder_options();

        if (!isset($folders[$selected])) {
            $selected = '/Isago-Media/Site-Assets/';
        }

        ?>
        <div class="isago-imagekit-folder-field">
            <label for="<?php echo esc_attr($id); ?>">
                <strong>
                    <?php echo esc_html($args['label']); ?>
                </strong>
            </label>

            <select
                id="<?php echo esc_attr($id); ?>"
                class="<?php echo esc_attr($args['class']); ?>"
                style="<?php echo esc_attr($args['style']); ?>"
            >
                <?php foreach ($folders as $path => $label) : ?>
                    <option
                        value="<?php echo esc_attr($path); ?>"
                        <?php selected($selected, $path); ?>
                    >
                        <?php echo esc_html($label); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php  
    }
}
