
<?php

if (!defined('ABSPATH')) {
    exit;
}

add_action(
    'admin_enqueue_scripts',
    'isago_enqueue_imagekit_gutenberg_assets',
    30
);

function isago_enqueue_imagekit_gutenberg_assets($hook_suffix)
{
    if (!in_array($hook_suffix, array('post.php', 'post-new.php'), true)) {
        return;
    }

    if (!function_exists('get_current_screen')) {
        return;
    }

    $screen = get_current_screen();

    if (
        !$screen ||
        $screen->base !== 'post' ||
        $screen->post_type !== 'post'
    ) {
        return;
    }

    if (!current_user_can('edit_posts')) {
        return;
    }

    $theme_dir = trailingslashit(get_template_directory());
    $theme_uri = trailingslashit(get_template_directory_uri());

    $imagekit_relative = 'assets/js/admin/imagekit.js';
    $gutenberg_relative = 'assets/js/admin/imagekit-gutenberg.js';

    $imagekit_path = $theme_dir . $imagekit_relative;
    $gutenberg_path = $theme_dir . $gutenberg_relative;

    if (!file_exists($gutenberg_path)) {
        error_log(
            'Isago ImageKit: missing JavaScript file: ' .
            $gutenberg_path
        );
        return;
    }

    if (!wp_script_is('isago-imagekit', 'registered')) {
        wp_register_script(
            'isago-imagekit',
            $theme_uri . $imagekit_relative,
            array('jquery'),
            file_exists($imagekit_path)
                ? (string) filemtime($imagekit_path)
                : '1.0.0',
            true
        );
    }

    wp_enqueue_script('isago-imagekit');

    $accounts_for_js = array();

    if (function_exists('isago_get_imagekit_accounts')) {
        foreach ((array) isago_get_imagekit_accounts() as $id => $account) {
            if (!is_array($account)) {
                continue;
            }

            $id = sanitize_key((string) $id);

            if (!$id) {
                continue;
            }

            $label = '';

            foreach (
                array('name', 'label', 'account_name', 'title') as $key
            ) {
                if (
                    isset($account[$key]) &&
                    is_scalar($account[$key]) &&
                    $account[$key] !== ''
                ) {
                    $label = sanitize_text_field((string) $account[$key]);
                    break;
                }
            }

            $accounts_for_js[$id] = array(
                'name' => $label !== '' ? $label : $id,
            );
        }
    }

    $folder_options = function_exists('isago_imagekit_folder_options')
        ? isago_imagekit_folder_options()
        : array(
            '/Isago-Media/Site-Assets/' => 'Site Assets',
            '/Isago-Media/AI-News-Articles/' => 'AI News Articles',
            '/Isago-Media/Blog-Posts-Articles/' => 'Blog Posts Articles',
            '/Isago-Media/AI-Prompts/' => 'AI Prompts',
            '/Isago-Media/AI-Tools/' => 'AI Tools',
        );

    wp_localize_script(
        'isago-imagekit',
        'isagoImageKit',
        array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('isago_imagekit_upload'),
            'accounts' => $accounts_for_js,
            'folders' => $folder_options,
        )
    );

    wp_enqueue_script(
        'isago-imagekit-gutenberg',
        $theme_uri . $gutenberg_relative,
        array(
            'jquery',
            'isago-imagekit',
            'wp-block-editor',
            'wp-editor',
            'wp-data',
            'wp-element',
            'wp-hooks',
            'wp-components',
            'wp-dom-ready',
        ),
        (string) filemtime($gutenberg_path),
        true
    );
}
