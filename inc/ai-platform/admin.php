<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
ISAGO AI Tool ADMIN
=========================================================*/


/*=========================================================
AI Tool ADMIN MENU
=========================================================*/

/*
 * AI Tools is a taxonomy rather than a custom post type.
 *
 * We therefore create a dedicated top-level WordPress
 * admin menu and place the AI Tool taxonomy screens
 * underneath it.
 *
 * Desired structure:
 *
 * AI Tools
 * ├── All AI Tools
 * ├── Add New AI Tool
 * └── AI Tool Categories
 */


/*---------------------------------------------------------
TOP-LEVEL AI ToolS MENU
---------------------------------------------------------*/

function isago_ai_platform_admin_menu()
{

    if (
        !current_user_can(
            'manage_categories'
        )
    ) {

        return;

    }


    add_menu_page(

        __('AI Tools', 'isago'),

        __('AI Tools', 'isago'),

        'manage_categories',

        'isago-ai-platforms',

        'isago_ai_platform_admin_redirect',

        'dashicons-admin-generic',

        21

    );


    /*-----------------------------------------------------
    All AI Tools
    -----------------------------------------------------*/

    add_submenu_page(

        'isago-ai-platforms',

        __('All AI Tools', 'isago'),

        __('All AI Tools', 'isago'),

        'manage_categories',

        'edit-tags.php?taxonomy=ai_platform'

    );


    /*-----------------------------------------------------
    ADD New AI Tool
    -----------------------------------------------------*/

    add_submenu_page(

        'isago-ai-platforms',

        __('Add New AI Tool', 'isago'),

        __('Add New AI Tool', 'isago'),

        'manage_categories',

        'edit-tags.php?taxonomy=ai_platform#addtag'

    );


    /*-----------------------------------------------------
    AI TOOL CATEGORIES
    -----------------------------------------------------*/

    add_submenu_page(

        'isago-ai-platforms',

        __('AI Tool Categories', 'isago'),

        __('AI Tool Categories', 'isago'),

        'manage_categories',

        'edit-tags.php?taxonomy=ai_tool_category'

    );

}

add_action(
    'admin_menu',
    'isago_ai_platform_admin_menu',
    20
);


/*=========================================================
AI Tool TOP-LEVEL REDIRECT
=========================================================*/

/*
 * Clicking the main "AI Tools" menu item should take
 * the administrator directly to All AI Tools.
 */

function isago_ai_platform_admin_redirect()
{

    if (
        !current_user_can(
            'manage_categories'
        )
    ) {

        wp_die(
            esc_html__(
                'You do not have permission to manage AI Tools.',
                'isago'
            )
        );

    }


    wp_safe_redirect(
        admin_url(
            'edit-tags.php?taxonomy=ai_platform'
        )
    );

    exit;

}


/*=========================================================
RENAME / CLEAN AI Tool SUBMENU
=========================================================*/

/*
 * WordPress may automatically create a submenu entry
 * for the custom top-level page.
 *
 * We force the visible menu structure to remain:
 *
 * AI Tools
 * ├── All AI Tools
 * ├── Add New AI Tool
 * └── AI Tool Categories
 */

function isago_ai_platform_admin_menu_cleanup()
{

    global $submenu;


    $parent_slug =
        'isago-ai-platforms';


    if (
        !isset(
            $submenu[
                $parent_slug
            ]
        )
    ) {

        return;

    }


    $cleaned =
        array();


    $seen =
        array();


    foreach (
        $submenu[
            $parent_slug
        ]
        as $item
    ) {

        if (
            !isset(
                $item[2]
            )
        ) {

            continue;

        }


        $url =
            html_entity_decode(
                $item[2]
            );


        /*-------------------------------------------------
        All AI Tools
        -------------------------------------------------*/

        if (
            $url ===
            'edit-tags.php?taxonomy=ai_platform'
        ) {

            if (
                isset(
                    $seen[
                        'ai_platform'
                    ]
                )
            ) {

                continue;

            }


            $item[0] =
                __('All AI Tools', 'isago');


            $seen[
                'ai_platform'
            ] = true;


            $cleaned[] =
                $item;


            continue;

        }


        /*-------------------------------------------------
        ADD New AI Tool
        -------------------------------------------------*/

        if (
            $url ===
            'edit-tags.php?taxonomy=ai_platform#addtag'
        ) {

            if (
                isset(
                    $seen[
                        'ai_platform_add'
                    ]
                )
            ) {

                continue;

            }


            $item[0] =
                __('Add New AI Tool', 'isago');


            $seen[
                'ai_platform_add'
            ] = true;


            $cleaned[] =
                $item;


            continue;

        }


        /*-------------------------------------------------
        AI TOOL CATEGORIES
        -------------------------------------------------*/

        if (
            $url ===
            'edit-tags.php?taxonomy=ai_tool_category'
        ) {

            if (
                isset(
                    $seen[
                        'ai_tool_category'
                    ]
                )
            ) {

                continue;

            }


            $item[0] =
                __('AI Tool Categories', 'isago');


            $seen[
                'ai_tool_category'
            ] = true;


            $cleaned[] =
                $item;


            continue;

        }

    }


    $submenu[
        $parent_slug
    ] =
        $cleaned;

}

add_action(
    'admin_menu',
    'isago_ai_platform_admin_menu_cleanup',
    999
);


/*=========================================================
KEEP AI ToolS MENU EXPANDED
=========================================================*/

/*
 * IMPORTANT:
 *
 * The AI Tool screens are WordPress taxonomy screens.
 *
 * Their actual admin URLs are:
 *
 * edit-tags.php?taxonomy=ai_platform
 *
 * and:
 *
 * edit-tags.php?taxonomy=ai_tool_category
 *
 * WordPress does not automatically know that these screens
 * belong to our custom "AI Tools" top-level menu.
 *
 * Therefore, without these filters, WordPress treats
 * "AI Tools" as inactive and only displays its submenu
 * when the administrator hovers over it.
 *
 * These filters tell WordPress to keep the AI Tools
 * menu expanded while working inside these screens.
 */


/*---------------------------------------------------------
SET AI ToolS AS THE ACTIVE PARENT MENU
---------------------------------------------------------*/

function isago_ai_platform_admin_parent_file(
    $parent_file
)
{

    $screen =
        get_current_screen();


    if (!$screen) {

        return $parent_file;

    }


    /*-----------------------------------------------------
    AI Tool TAXONOMY
    -----------------------------------------------------*/

    if (
        isset(
            $screen->taxonomy
        )
        &&
        $screen->taxonomy ===
        'ai_platform'
    ) {

        return 'isago-ai-platforms';

    }


    /*-----------------------------------------------------
    AI TOOL CATEGORY TAXONOMY
    -----------------------------------------------------*/

    if (
        isset(
            $screen->taxonomy
        )
        &&
        $screen->taxonomy ===
        'ai_tool_category'
    ) {

        return 'isago-ai-platforms';

    }


    return $parent_file;

}

add_filter(
    'parent_file',
    'isago_ai_platform_admin_parent_file',
    99
);


/*=========================================================
SET ACTIVE AI Tool SUBMENU
=========================================================*/

/*
 * This controls which submenu item WordPress considers
 * active while the AI Tools menu is expanded.
 */

function isago_ai_platform_admin_submenu_file(
    $submenu_file
)
{

    $screen =
        get_current_screen();


    if (!$screen) {

        return $submenu_file;

    }


    /*-----------------------------------------------------
    AI Tool SCREEN
    -----------------------------------------------------*/

    if (
        isset(
            $screen->taxonomy
        )
        &&
        $screen->taxonomy ===
        'ai_platform'
    ) {

        return 'edit-tags.php?taxonomy=ai_platform';

    }


    /*-----------------------------------------------------
    AI TOOL CATEGORY SCREEN
    -----------------------------------------------------*/

    if (
        isset(
            $screen->taxonomy
        )
        &&
        $screen->taxonomy ===
        'ai_tool_category'
    ) {

        return 'edit-tags.php?taxonomy=ai_tool_category';

    }


    return $submenu_file;

}

add_filter(
    'submenu_file',
    'isago_ai_platform_admin_submenu_file',
    99
);


/*=========================================================
REMOVE AI Tool TAXONOMY FROM AI PROMPTS MENU
=========================================================*/

/*
 * ai_platform is connected to ai_prompt so prompts can
 * store their associated AI Tools.
 *
 * That relationship must remain.
 *
 * We only remove the taxonomy's admin navigation from
 * the AI Prompts menu.
 */

function isago_remove_ai_platform_from_prompt_menu()
{

    global $submenu;


    $parent_slug =
        'edit.php?post_type=ai_prompt';


    if (
        !isset(
            $submenu[
                $parent_slug
            ]
        )
    ) {

        return;

    }


    $cleaned =
        array();


    foreach (
        $submenu[
            $parent_slug
        ]
        as $item
    ) {

        if (
            !isset(
                $item[2]
            )
        ) {

            continue;

        }


        $url =
            html_entity_decode(
                $item[2]
            );


        /*-------------------------------------------------
        REMOVE AI Tool
        -------------------------------------------------*/

        if (
            strpos(
                $url,
                'taxonomy=ai_platform'
            ) !== false
        ) {

            continue;

        }


        /*-------------------------------------------------
        REMOVE AI TOOL CATEGORIES
        -------------------------------------------------*/

        if (
            strpos(
                $url,
                'taxonomy=ai_tool_category'
            ) !== false
        ) {

            continue;

        }


        /*-------------------------------------------------
        REMOVE WORDPRESS TAGS
        -------------------------------------------------*/

        if (
            strpos(
                $url,
                'taxonomy=post_tag'
            ) !== false
        ) {

            continue;

        }


        $cleaned[] =
            $item;

    }


    $submenu[
        $parent_slug
    ] =
        $cleaned;

}

add_action(
    'admin_menu',
    'isago_remove_ai_platform_from_prompt_menu',
    999
);


/*=========================================================
AI TOOL ADMIN ASSETS
=========================================================*/

add_action(
    'admin_enqueue_scripts',
    'isago_ai_platform_admin_assets'
);

function isago_ai_platform_admin_assets(
    $hook
)
{

    $screen =
        get_current_screen();


    if (!$screen) {

        return;

    }


    /*-----------------------------------------------------
    ONLY AI PLATFORM TAXONOMY SCREENS
    -----------------------------------------------------*/

    if (
        $screen->taxonomy !==
        'ai_platform'
        ||
        !in_array(
            $screen->base,
            [
                'edit-tags',
                'term'
            ],
            true
        )
    ) {

        return;

    }


    /*-----------------------------------------------------
    ISAGO ADMIN JAVASCRIPT
    -----------------------------------------------------*/

    /*
     * The accordion system lives inside the main
     * Isago admin.js file.
     *
     * Both screens must load it:
     *
     * edit-tags.php
     * term.php
     */

    wp_enqueue_script(
        'isago-admin',
        ISAGO_URI .
            '/assets/js/admin/admin.js',
        [
            'jquery'
        ],
        ISAGO_VERSION,
        true
    );


    /*-----------------------------------------------------
    WORDPRESS MEDIA
    -----------------------------------------------------*/

    wp_enqueue_media();


    /*-----------------------------------------------------
    AI PLATFORM IMAGE SCRIPT
    -----------------------------------------------------*/

    wp_enqueue_script(
        'isago-ai-platform-image',
        ISAGO_URI .
            '/assets/js/admin/ai-platform-image.js',
        [
            'jquery',
            'media-editor',
            'media-views'
        ],
        ISAGO_VERSION . '-' . time(),
        true
    );

}





/*=========================================================
AI TOOL ADMIN FILTER CONTROLS
=========================================================*/

/**
 * Add controls directly to the All AI Tools list toolbar.
 */
add_action(
    'admin_footer-edit-tags.php',
    'isago_ai_platform_admin_filter_toolbar'
);

function isago_ai_platform_admin_filter_toolbar()
{
    $screen = get_current_screen();

    if (
        !$screen
        ||
        $screen->base !== 'edit-tags'
        ||
        $screen->taxonomy !== 'ai_platform'
    ) {
        return;
    }

    $filters = [
        ''         => 'All AI Tools',
        'new'      => 'New Tools',
        'featured' => 'Featured',
        'hot'      => 'Trending / Hot',
        'popular'  => 'Popular',
        'free'     => 'Free Tools',
    ];

    $sort_options = [
        ''          => 'Default Order',
        'newest'    => 'Newest Added',
        'oldest'    => 'Oldest Added',
        'name_asc'  => 'Name A–Z',
        'name_desc' => 'Name Z–A',
    ];

    $current_filter = isset($_GET['isago_ai_platform_filter'])
        ? sanitize_key(
            wp_unslash($_GET['isago_ai_platform_filter'])
        )
        : '';

    $current_sort = isset($_GET['isago_ai_platform_sort'])
        ? sanitize_key(
            wp_unslash($_GET['isago_ai_platform_sort'])
        )
        : '';

    ?>
    <script>
    (function () {
        function initIsagoAiToolFilters() {
            const toolbar = document.querySelector(
                '#posts-filter .tablenav.top .alignleft.actions'
            );

            if (
                !toolbar ||
                document.getElementById('isago-ai-platform-filter-controls')
            ) {
                return;
            }

            const filterOptions = <?php
                echo wp_json_encode($filters);
            ?>;

            const sortOptions = <?php
                echo wp_json_encode($sort_options);
            ?>;

            const selectedFilter = <?php
                echo wp_json_encode($current_filter);
            ?>;

            const selectedSort = <?php
                echo wp_json_encode($current_sort);
            ?>;

            const controls = document.createElement('span');

            controls.id = 'isago-ai-platform-filter-controls';
            controls.style.display = 'inline-flex';
            controls.style.alignItems = 'center';
            controls.style.flexWrap = 'wrap';
            controls.style.gap = '6px';
            controls.style.marginLeft = '10px';
            controls.style.verticalAlign = 'middle';

            function createSelect(id, label, options, selectedValue) {
                const select = document.createElement('select');

                select.id = id;
                select.setAttribute('aria-label', label);
                select.style.maxWidth = '180px';

                Object.entries(options).forEach(function (item) {
                    const option = document.createElement('option');

                    option.value = item[0];
                    option.textContent = item[1];

                    select.appendChild(option);
                });

                select.value = selectedValue;

                return select;
            }

            const filterSelect = createSelect(
                'isago-ai-platform-filter',
                'Filter AI Tools',
                filterOptions,
                selectedFilter
            );

            const sortSelect = createSelect(
                'isago-ai-platform-sort',
                'Sort AI Tools',
                sortOptions,
                selectedSort
            );

            const applyButton = document.createElement('button');

            applyButton.type = 'button';
            applyButton.className = 'button';
            applyButton.textContent = 'Filter';

            applyButton.addEventListener('click', function () {
                const url = new URL(window.location.href);

                if (filterSelect.value) {
                    url.searchParams.set(
                        'isago_ai_platform_filter',
                        filterSelect.value
                    );
                } else {
                    url.searchParams.delete(
                        'isago_ai_platform_filter'
                    );
                }

                if (sortSelect.value) {
                    url.searchParams.set(
                        'isago_ai_platform_sort',
                        sortSelect.value
                    );
                } else {
                    url.searchParams.delete(
                        'isago_ai_platform_sort'
                    );
                }

                // Return to page one after changing the filter.
                url.searchParams.delete('paged');

                window.location.assign(url.toString());
            });

            controls.appendChild(filterSelect);
            controls.appendChild(sortSelect);
            controls.appendChild(applyButton);

            toolbar.appendChild(controls);

            /*
             * Keep the selected filter and sort when searching.
             */
            const searchForm = document.querySelector(
                'form.search-form'
            );

            if (searchForm) {
                function ensureHiddenInput(name) {
                    let input = searchForm.querySelector(
                        'input[name="' + name + '"]'
                    );

                    if (!input) {
                        input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = name;
                        searchForm.appendChild(input);
                    }

                    return input;
                }

                const filterInput = ensureHiddenInput(
                    'isago_ai_platform_filter'
                );

                const sortInput = ensureHiddenInput(
                    'isago_ai_platform_sort'
                );

                searchForm.addEventListener('submit', function () {
                    filterInput.value = filterSelect.value;
                    sortInput.value = sortSelect.value;
                });
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener(
                'DOMContentLoaded',
                initIsagoAiToolFilters
            );
        } else {
            initIsagoAiToolFilters();
        }
    })();
    </script>
    <?php
}


/*=========================================================
APPLY AI TOOL ADMIN FILTERS AND SORTING
=========================================================*/

add_filter(
    'get_terms_args',
    'isago_ai_platform_admin_apply_filters',
    10,
    2
);

function isago_ai_platform_admin_apply_filters(
    $args,
    $taxonomies
) {
    if (
        !is_admin()
        ||
        !in_array(
            'ai_platform',
            (array) $taxonomies,
            true
        )
    ) {
        return $args;
    }

    if (
        !isset($_GET['taxonomy'])
        ||
        sanitize_key(
            wp_unslash($_GET['taxonomy'])
        ) !== 'ai_platform'
    ) {
        return $args;
    }

    global $pagenow;

    if (
        isset($pagenow)
        &&
        $pagenow !== 'edit-tags.php'
    ) {
        return $args;
    }

    $flag_meta_keys = [
        'new'      => '_isago_ai_platform_new',
        'featured' => '_isago_ai_platform_featured',
        'hot'      => '_isago_ai_platform_hot',
        'popular'  => '_isago_ai_platform_popular',
        'free'     => '_isago_ai_platform_free',
    ];

    $selected_filter = isset($_GET['isago_ai_platform_filter'])
        ? sanitize_key(
            wp_unslash($_GET['isago_ai_platform_filter'])
        )
        : '';

    /*
     * Filter by the existing AI Tool curation flags.
     */
    if (isset($flag_meta_keys[$selected_filter])) {
        if (
            !isset($args['meta_query'])
            ||
            !is_array($args['meta_query'])
        ) {
            $args['meta_query'] = [];
        }

        $args['meta_query'][] = [
            'key'     => $flag_meta_keys[$selected_filter],
            'value'   => '1',
            'compare' => '=',
        ];
    }

    $selected_sort = isset($_GET['isago_ai_platform_sort'])
        ? sanitize_key(
            wp_unslash($_GET['isago_ai_platform_sort'])
        )
        : '';

    switch ($selected_sort) {
        case 'newest':
            $args['orderby'] = 'term_id';
            $args['order']   = 'DESC';
            break;

        case 'oldest':
            $args['orderby'] = 'term_id';
            $args['order']   = 'ASC';
            break;

        case 'name_asc':
            $args['orderby'] = 'name';
            $args['order']   = 'ASC';
            break;

        case 'name_desc':
            $args['orderby'] = 'name';
            $args['order']   = 'DESC';
            break;
    }

    return $args;
}

