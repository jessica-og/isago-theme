<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once ISAGO_DIR . '/inc/notifications/prompt-retirement.php';

/*=========================================================
ISAGO AI PROMPT RETIREMENT
=========================================================*/

function isago_get_ai_prompt_retirement_status($prompt_id)
{
    $prompt_id = absint($prompt_id);
    $status = get_post_meta($prompt_id, '_isago_ai_prompt_retirement_status', true);

    if (!in_array($status, array('available', 'scheduled', 'retired'), true)) {
        $status = 'available';
    }

    return $status;
}

function isago_is_ai_prompt_retired($prompt_id)
{
    return isago_get_ai_prompt_retirement_status($prompt_id) === 'retired';
}

function isago_is_ai_prompt_removal_scheduled($prompt_id)
{
    return isago_get_ai_prompt_retirement_status($prompt_id) === 'scheduled';
}

function isago_get_ai_prompt_removal_at($prompt_id)
{
    return (int) get_post_meta(absint($prompt_id), '_isago_ai_prompt_removal_at', true);
}

function isago_get_ai_prompt_retirement_token($prompt_id)
{
    return sanitize_key(
        (string) get_post_meta(
            absint($prompt_id),
            '_isago_ai_prompt_retirement_token',
            true
        )
    );
}

function isago_parse_prompt_retirement_datetime($value)
{
    $value = trim((string) $value);

    if ($value === '') {
        return 0;
    }

    $datetime = DateTimeImmutable::createFromFormat(
        'Y-m-d\\TH:i',
        $value,
        wp_timezone()
    );

    if (!$datetime) {
        return 0;
    }

    return $datetime->getTimestamp();
}

function isago_schedule_prompt_retirement_events($prompt_id)
{
    $prompt_id = absint($prompt_id);
    $token = isago_get_ai_prompt_retirement_token($prompt_id);
    $removal_at = isago_get_ai_prompt_removal_at($prompt_id);

    if (!$prompt_id || !$token || !$removal_at) {
        return;
    }

    wp_clear_scheduled_hook(
        'isago_prompt_retirement_final_notice',
        array($prompt_id, $token)
    );

    wp_clear_scheduled_hook(
        'isago_prompt_retirement_remove',
        array($prompt_id, $token)
    );

    $final_notice_at = $removal_at - DAY_IN_SECONDS;

    if ($final_notice_at > time()) {
        wp_schedule_single_event(
            $final_notice_at,
            'isago_prompt_retirement_final_notice',
            array($prompt_id, $token)
        );
    }

    if ($removal_at > time()) {
        wp_schedule_single_event(
            $removal_at,
            'isago_prompt_retirement_remove',
            array($prompt_id, $token)
        );
    }
}

function isago_cancel_prompt_retirement_events($prompt_id, $token = '')
{
    $prompt_id = absint($prompt_id);

    if (!$token) {
        $token = isago_get_ai_prompt_retirement_token($prompt_id);
    }

    if (!$prompt_id || !$token) {
        return;
    }

    wp_clear_scheduled_hook(
        'isago_prompt_retirement_final_notice',
        array($prompt_id, $token)
    );

    wp_clear_scheduled_hook(
        'isago_prompt_retirement_remove',
        array($prompt_id, $token)
    );
}

function isago_schedule_ai_prompt_retirement(
    $prompt_id,
    $removal_at,
    $notice_days = 7
) {
    $prompt_id = absint($prompt_id);
    $removal_at = (int) $removal_at;
    $notice_days = max(1, min(90, absint($notice_days)));

    if (!$prompt_id || get_post_type($prompt_id) !== 'ai_prompt') {
        return new WP_Error(
            'invalid_prompt',
            'Invalid AI Prompt.'
        );
    }

    if (!$removal_at || $removal_at <= time()) {
        return new WP_Error(
            'invalid_removal_date',
            'The removal date must be in the future.'
        );
    }

    $old_token = isago_get_ai_prompt_retirement_token($prompt_id);
    if ($old_token) {
        isago_cancel_prompt_retirement_events($prompt_id, $old_token);
    }

    $token = wp_generate_password(20, false, false);

    update_post_meta($prompt_id, '_isago_ai_prompt_retirement_status', 'scheduled');
    update_post_meta($prompt_id, '_isago_ai_prompt_removal_at', $removal_at);
    update_post_meta($prompt_id, '_isago_ai_prompt_retirement_notice_days', $notice_days);
    update_post_meta($prompt_id, '_isago_ai_prompt_retirement_token', $token);
    update_post_meta($prompt_id, '_isago_ai_prompt_retirement_scheduled_at', time());
    update_post_meta($prompt_id, '_isago_ai_prompt_retirement_scheduled_by', get_current_user_id());

    isago_schedule_prompt_retirement_events($prompt_id);

    /* Send the first notice immediately. */
    isago_send_prompt_retirement_notifications($prompt_id, 'initial');

    return true;
}

function isago_cancel_ai_prompt_retirement($prompt_id)
{
    $prompt_id = absint($prompt_id);
    $token = isago_get_ai_prompt_retirement_token($prompt_id);

    if (!$prompt_id) {
        return false;
    }

    isago_cancel_prompt_retirement_events($prompt_id, $token);

    update_post_meta($prompt_id, '_isago_ai_prompt_retirement_status', 'available');
    delete_post_meta($prompt_id, '_isago_ai_prompt_removal_at');
    delete_post_meta($prompt_id, '_isago_ai_prompt_retirement_notice_days');
    delete_post_meta($prompt_id, '_isago_ai_prompt_retirement_token');
    delete_post_meta($prompt_id, '_isago_ai_prompt_retirement_scheduled_at');
    delete_post_meta($prompt_id, '_isago_ai_prompt_retirement_scheduled_by');

    return true;
}

function isago_retire_ai_prompt($prompt_id)
{
    $prompt_id = absint($prompt_id);

    if (!$prompt_id || get_post_type($prompt_id) !== 'ai_prompt') {
        return false;
    }

    $token = isago_get_ai_prompt_retirement_token($prompt_id);

    if ($token) {
        isago_cancel_prompt_retirement_events($prompt_id, $token);
    }

    /*
     * Phase 2 permanently removes the prompt and its local
     * purchase records when the scheduled removal date arrives.
     *
     * If the deletion component is unavailable, fail closed
     * rather than silently marking the prompt retired.
     */
    if (function_exists('isago_delete_ai_prompt_permanently')) {
        return isago_delete_ai_prompt_permanently($prompt_id);
    }

    return false;
}

add_action('isago_prompt_retirement_final_notice', function ($prompt_id, $token) {
    $prompt_id = absint($prompt_id);

    if (!$prompt_id || get_post_type($prompt_id) !== 'ai_prompt') {
        return;
    }

    if (isago_get_ai_prompt_retirement_status($prompt_id) !== 'scheduled') {
        return;
    }

    if (isago_get_ai_prompt_retirement_token($prompt_id) !== sanitize_key($token)) {
        return;
    }

    isago_send_prompt_retirement_notifications($prompt_id, 'final');
}, 10, 2);

add_action('isago_prompt_retirement_remove', function ($prompt_id, $token) {
    $prompt_id = absint($prompt_id);

    if (!$prompt_id || get_post_type($prompt_id) !== 'ai_prompt') {
        return;
    }

    if (isago_get_ai_prompt_retirement_status($prompt_id) !== 'scheduled') {
        return;
    }

    if (isago_get_ai_prompt_retirement_token($prompt_id) !== sanitize_key($token)) {
        return;
    }

    $removal_at = isago_get_ai_prompt_removal_at($prompt_id);

    if (!$removal_at || $removal_at > time()) {
        return;
    }

    isago_retire_ai_prompt($prompt_id);
}, 10, 2);

/* Safety net: if WP-Cron was delayed, process due retirements. */
add_action('admin_init', function () {
    if (!current_user_can('manage_options')) {
        return;
    }

    $query = new WP_Query(array(
        'post_type' => 'ai_prompt',
        'post_status' => 'any',
        'posts_per_page' => 100,
        'fields' => 'ids',
        'meta_query' => array(
            array(
                'key' => '_isago_ai_prompt_retirement_status',
                'value' => 'scheduled',
            ),
            array(
                'key' => '_isago_ai_prompt_removal_at',
                'value' => time(),
                'type' => 'NUMERIC',
                'compare' => '<=',
            ),
        ),
    ));

    foreach ($query->posts as $prompt_id) {
        isago_retire_ai_prompt($prompt_id);
    }

    wp_reset_postdata();
});

/*=========================================================
RETIREMENT META BOX
=========================================================*/

function isago_ai_prompt_retirement_meta_box()
{
    add_meta_box(
        'isago_ai_prompt_retirement',
        __('Prompt Retirement', 'isago'),
        'isago_render_ai_prompt_retirement_meta_box',
        'ai_prompt',
        'side',
        'high'
    );
}

add_action('add_meta_boxes', 'isago_ai_prompt_retirement_meta_box');

function isago_render_ai_prompt_retirement_meta_box($post)
{
    wp_nonce_field(
        'isago_save_prompt_retirement',
        'isago_prompt_retirement_nonce'
    );

    $status = isago_get_ai_prompt_retirement_status($post->ID);
    $removal_at = isago_get_ai_prompt_removal_at($post->ID);
    $notice_days = (int) get_post_meta(
        $post->ID,
        '_isago_ai_prompt_retirement_notice_days',
        true
    );

    if (!$notice_days) {
        $notice_days = 7;
    }

    $local_value = '';

    if ($removal_at) {
        $local_value = wp_date(
            'Y-m-d\\TH:i',
            $removal_at,
            wp_timezone()
        );
    }
    ?>

    <p>
        <strong>Status:</strong>
        <?php echo esc_html(ucfirst($status)); ?>
    </p>

    <?php if ($status === 'retired') : ?>
        <p style="color:#b32d2e;">
           This prompt is scheduled for permanent removal. The prompt and its associated media will be deleted after the scheduled removal date.
        </p>
    <?php else : ?>
        <p>
            Schedule a future date when this prompt will stop being available. Customers with an active purchase will be emailed automatically.
        </p>

        <p>
            <label for="isago-prompt-removal-at">
                <strong>Removal date and time</strong>
            </label>
        </p>

        <input
            type="datetime-local"
            id="isago-prompt-removal-at"
            name="isago_prompt_removal_at"
            value="<?php echo esc_attr($local_value); ?>"
            style="width:100%;"
        >

        <p>
            <label for="isago-prompt-notice-days">
                <strong>Notice period (days)</strong>
            </label>
        </p>

        <input
            type="number"
            min="1"
            max="90"
            id="isago-prompt-notice-days"
            name="isago_prompt_retirement_notice_days"
            value="<?php echo esc_attr($notice_days); ?>"
            style="width:100%;"
        >

        <p class="description">
            The first customer email is sent when you schedule the removal. A final reminder is sent one day before removal.
        </p>

        <?php if ($status === 'scheduled' && $removal_at) : ?>
            <p style="color:#996800;">
                <strong>Scheduled for:</strong><br>
                <?php
                echo esc_html(
                    wp_date(
                        get_option('date_format') . ' ' . get_option('time_format'),
                        $removal_at,
                        wp_timezone()
                    )
                );
                ?>
            </p>

            <label>
                <input
                    type="checkbox"
                    name="isago_cancel_prompt_retirement"
                    value="1"
                >
                Cancel scheduled removal
            </label>
        <?php endif; ?>
    <?php endif; ?>

    <?php
}

function isago_save_ai_prompt_retirement_meta_box($post_id)
{
    if (!isset($_POST['isago_prompt_retirement_nonce'])) {
        return;
    }

    if (!wp_verify_nonce(
        sanitize_text_field(wp_unslash($_POST['isago_prompt_retirement_nonce'])),
        'isago_save_prompt_retirement'
    )) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (wp_is_post_revision($post_id)) {
        return;
    }

    if (get_post_type($post_id) !== 'ai_prompt') {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['isago_cancel_prompt_retirement'])) {
        isago_cancel_ai_prompt_retirement($post_id);
        return;
    }

    if (isago_get_ai_prompt_retirement_status($post_id) === 'retired') {
        return;
    }

    if (!isset($_POST['isago_prompt_removal_at'])) {
        return;
    }

    $removal_at = isago_parse_prompt_retirement_datetime(
        wp_unslash($_POST['isago_prompt_removal_at'])
    );

    if (!$removal_at) {
        return;
    }

    $notice_days = isset($_POST['isago_prompt_retirement_notice_days'])
        ? absint(wp_unslash($_POST['isago_prompt_retirement_notice_days']))
        : 7;

    $result = isago_schedule_ai_prompt_retirement(
        $post_id,
        $removal_at,
        $notice_days
    );

    if (is_wp_error($result)) {
        return;
    }
}

add_action('save_post_ai_prompt', 'isago_save_ai_prompt_retirement_meta_box', 30);
