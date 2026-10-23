
<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Collect image URLs from Gutenberg blocks and their inner blocks.
 */
function isago_collect_content_image_urls($blocks, &$urls)
{
    foreach ($blocks as $block) {
        if (!empty($block['blockName']) && $block['blockName'] === 'core/image') {
            $url = $block['attrs']['url'] ?? '';

            if (is_string($url) && $url !== '') {
                $urls[] = esc_url_raw($url);
            }
        }

        if (!empty($block['innerBlocks']) && is_array($block['innerBlocks'])) {
            isago_collect_content_image_urls($block['innerBlocks'], $urls);
        }
    }
}

/**
 * Find an existing ImageKit media record by its exact URL.
 */
function isago_find_imagekit_media_by_url($url)
{
    global $wpdb;

    $url = esc_url_raw($url);

    if (!$url) {
        return null;
    }

    $table = isago_media_table();

    return $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM {$table}
             WHERE source = %s AND url = %s
             LIMIT 1",
            'imagekit',
            $url
        )
    );
}

/**
 * Synchronize ImageKit images used in post content.
 *
 * This function only manages references with the
 * article_content_image field key. It does not remove
 * featured-image or other field references.
 */
function isago_sync_content_image_references(
    $post_id,
    $post,
    $update,
    $post_before
) {
    if (
        wp_is_post_revision($post_id) ||
        wp_is_post_autosave($post_id) ||
        (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
    ) {
        return;
    }

    if (!($post instanceof WP_Post)) {
        $post = get_post($post_id);
    }

    if (!$post) {
        return;
    }

    if (!in_array(
        $post->post_type,
        ['post', 'isago_video', 'ai_prompt'],
        true
    )) {
        return;
    }

    // Collect URLs from Gutenberg image blocks.
    $urls = [];
    $blocks = parse_blocks($post->post_content);

    isago_collect_content_image_urls($blocks, $urls);

    // Also collect image URLs from rendered HTML in the content.
    if (preg_match_all(
        '/<img\b[^>]*\bsrc\s*=\s*(["\'])(.*?)\1/i',
        $post->post_content,
        $matches
    )) {
        foreach ($matches[2] as $src) {
            $urls[] = esc_url_raw(
                html_entity_decode($src, ENT_QUOTES, 'UTF-8')
            );
        }
    }

    $urls = array_values(array_unique(array_filter($urls)));

    // Match URLs to existing ImageKit records.
    $media_ids = [];

    foreach ($urls as $url) {
        $media = isago_find_imagekit_media_by_url($url);

        if ($media && !empty($media->id)) {
            $media_ids[] = absint($media->id);
        }
    }

    $media_ids = array_values(array_unique(array_filter($media_ids)));

    // Load existing references managed by this synchronizer.
    $existing_refs = isago_media_get_references_for_object(
        'post',
        $post_id
    );

    $existing_ids = [];

    foreach ($existing_refs as $ref) {
        if ($ref->field_key === 'article_content_image') {
            $existing_ids[] = absint($ref->media_id);
        }
    }

    $existing_ids = array_values(array_unique(array_filter($existing_ids)));

    // Add references for images currently used in the content.
    foreach ($media_ids as $media_id) {
        $result = isago_media_add_reference(
            $media_id,
            'post',
            $post_id,
            'article_content_image'
        );

        if (is_wp_error($result)) {
            error_log(
                'Isago content image sync: could not register media ' .
                $media_id . ' for post ' . $post_id . ': ' .
                $result->get_error_message()
            );
        }
    }

    // Remove obsolete content-image references only after matching
    // the current content. Other field references remain untouched.
  
foreach ($existing_refs as $ref) {
    if ($ref->field_key !== 'article_content_image') {
        continue;
    }

    $old_media_id = absint($ref->media_id);

    if (!$old_media_id || in_array($old_media_id, $media_ids, true)) {
        continue;
    }

    $removed = isago_media_remove_reference(
        $old_media_id,
        'post',
        $post_id,
        'article_content_image'
    );

    if (!$removed) {
        error_log(
            'Isago content image sync: failed to remove reference ' .
            'for media ' . $old_media_id . ', post ' . $post_id
        );
        continue;
    }

    if (isago_media_reference_count($old_media_id) === 0) {
        $cleanup = isago_media_delete_if_unused($old_media_id);

        if (is_wp_error($cleanup)) {
            error_log(
                'Isago content image sync: cleanup failed for media ' .
                $old_media_id . ': ' . $cleanup->get_error_message()
            );
        }
    }
}

}

/**
 * Run after WordPress has saved the post content.
 */
add_action(
    'wp_after_insert_post',
    'isago_sync_content_image_references',
    20,
    4
);
