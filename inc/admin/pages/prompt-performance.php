<?php

if (!defined('ABSPATH')) {
    exit;
}

/*=========================================================
ISAGO PROMPT PERFORMANCE ADMIN PAGE
=========================================================*/

function isago_prompt_performance_admin_menu()
{
    add_submenu_page(
        'isago-home',
        __('Prompt Performance', 'isago'),
        __('Prompt Performance', 'isago'),
        'manage_options',
        'isago-prompt-performance',
        'isago_render_prompt_performance_admin_page'
    );
}

add_action('admin_menu', 'isago_prompt_performance_admin_menu', 40);

function isago_render_prompt_performance_admin_page()
{
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('You do not have permission to view prompt performance.', 'isago'));
    }

    $orderby = isset($_GET['orderby'])
        ? sanitize_key(wp_unslash($_GET['orderby']))
        : 'views';

    $order = isset($_GET['order'])
        ? sanitize_key(wp_unslash($_GET['order']))
        : 'ASC';

    $status = isset($_GET['status'])
        ? sanitize_key(wp_unslash($_GET['status']))
        : 'all';

    $search = isset($_GET['s'])
        ? sanitize_text_field(wp_unslash($_GET['s']))
        : '';

    $allowed_orderby = array(
        'views',
        'purchases',
        'conversion',
        'revenue',
        'last_viewed',
        'last_purchased',
    );

    if (!in_array($orderby, $allowed_orderby, true)) {
        $orderby = 'views';
    }

    $order = $order === 'DESC' ? 'DESC' : 'ASC';

    $page = isset($_GET['paged']) ? max(1, absint($_GET['paged'])) : 1;
    $per_page = 25;
    $offset = ($page - 1) * $per_page;

    $rows = isago_get_prompt_performance_rows(array(
        'orderby' => $orderby,
        'order' => $order,
        'limit' => $per_page,
        'offset' => $offset,
        'search' => $search,
        'status' => $status,
    ));

    $total = isago_get_prompt_performance_total(array(
        'search' => $search,
        'status' => $status,
    ));

    $total_pages = max(1, (int) ceil($total / $per_page));
    ?>

    <div class="wrap">
        <h1>Prompt Performance</h1>

        <p>
            Use this page to identify prompts that are receiving fewer views or purchases. This page does not delete prompts automatically.
        </p>

        <form method="get" style="margin:20px 0;">
            <input type="hidden" name="page" value="isago-prompt-performance">

            <input
                type="search"
                name="s"
                value="<?php echo esc_attr($search); ?>"
                placeholder="Search prompts..."
            >

            <select name="status">
                <option value="all" <?php selected($status, 'all'); ?>>All prompts</option>
                <option value="available" <?php selected($status, 'available'); ?>>Available</option>
                <option value="scheduled" <?php selected($status, 'scheduled'); ?>>Scheduled for removal</option>
                <option value="retired" <?php selected($status, 'retired'); ?>>Retired</option>
            </select>

            <select name="orderby">
                <option value="views" <?php selected($orderby, 'views'); ?>>Views</option>
                <option value="purchases" <?php selected($orderby, 'purchases'); ?>>Purchases</option>
                <option value="conversion" <?php selected($orderby, 'conversion'); ?>>Conversion</option>
                <option value="revenue" <?php selected($orderby, 'revenue'); ?>>Revenue</option>
                <option value="last_viewed" <?php selected($orderby, 'last_viewed'); ?>>Last viewed</option>
                <option value="last_purchased" <?php selected($orderby, 'last_purchased'); ?>>Last purchased</option>
            </select>

            <select name="order">
                <option value="ASC" <?php selected($order, 'ASC'); ?>>Lowest / oldest first</option>
                <option value="DESC" <?php selected($order, 'DESC'); ?>>Highest / newest first</option>
            </select>

            <button class="button button-primary" type="submit">Apply</button>
        </form>

        <table class="widefat fixed striped">
            <thead>
                <tr>
                    <th>Prompt</th>
                    <th>Views</th>
                    <th>Purchases</th>
                    <th>Conversion</th>
                    <th>Revenue</th>
                    <th>Last viewed</th>
                    <th>Last purchased</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rows)) : ?>
                    <tr>
                        <td colspan="8">No prompt performance data found.</td>
                    </tr>
                <?php else : ?>
                    <?php foreach ($rows as $row) : ?>
                        <tr>
                            <td>
                                <strong>
                                    <a href="<?php echo esc_url(get_edit_post_link($row->prompt_id)); ?>">
                                        <?php echo esc_html($row->title); ?>
                                    </a>
                                </strong>
                            </td>
                            <td><?php echo esc_html(number_format_i18n((int) $row->views)); ?></td>
                            <td><?php echo esc_html(number_format_i18n((int) $row->purchases)); ?></td>
                            <td><?php echo esc_html(number_format_i18n((float) $row->conversion, 2)); ?>%</td>
                            <td>
                                <?php
                                echo esc_html(
                                    function_exists('isago_format_currency')
                                        ? isago_format_currency((float) $row->revenue)
                                        : number_format_i18n((float) $row->revenue, 2)
                                );
                                ?>
                            </td>
                            <td>
                                <?php
                                echo $row->last_viewed
                                    ? esc_html(
                                        wp_date(
                                            get_option('date_format'),
                                            strtotime($row->last_viewed . ' UTC'),
                                            wp_timezone()
                                        )
                                    )
                                    : '—';
                                ?>
                            </td>
                            <td>
                                <?php
                                echo $row->last_purchased
                                    ? esc_html(
                                        wp_date(
                                            get_option('date_format'),
                                            strtotime($row->last_purchased . ' UTC'),
                                            wp_timezone()
                                        )
                                    )
                                    : '—';
                                ?>
                            </td>
                            <td><?php echo esc_html(ucfirst($row->retirement_status)); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if ($total_pages > 1) : ?>
            <div class="tablenav bottom">
                <div class="tablenav-pages">
                    <?php
                    echo wp_kses_post(
                        paginate_links(array(
                            'base' => add_query_arg('paged', '%#%'),
                            'format' => '',
                            'current' => $page,
                            'total' => $total_pages,
                            'type' => 'plain',
                            'add_args' => array_filter(array(
                                'page' => 'isago-prompt-performance',
                                's' => $search,
                                'status' => $status,
                                'orderby' => $orderby,
                                'order' => $order,
                            )),
                        ))
                    );
                    ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <?php
}
