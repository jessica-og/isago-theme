<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
HOMEPAGE SECTION CONTENT
=========================================================*/

/**
 * Get homepage section content.
 *
 * Supported types:
 *
 * blog
 * ai_prompt
 * ai_tool
 *
 * @param array $section
 * @param int   $posts
 *
 * @return array
 */
function isago_get_home_section_content(
    $section,
    $posts = 4
) {

    $type =
        sanitize_key(
            $section['type']
            ?? 'blog'
        );


    $source =
        $section['slug']
        ?? '__all';

        /*=====================================================
CONTENT ORDER
=====================================================*/

$orderby = sanitize_key(
    $section['orderby'] ?? 'latest'
);

/* Support the previous saved value. */

if ($orderby === 'date') {
    $orderby = 'latest';
}

if (
    !in_array(
        $orderby,
        ['latest', 'older', 'modified'],
        true
    )
) {
    $orderby = 'latest';
}


/*
 * Latest: newest first.
 * Older: oldest first.
 * Modified: recently updated first.
 */

$query_orderby = (
    $orderby === 'modified'
)
    ? 'modified'
    : 'date';

$query_order = (
    $orderby === 'older'
)
    ? 'ASC'
    : 'DESC';

    $posts =
        max(
            1,
            absint($posts)
        );


    /*=====================================================
    BLOG POSTS
    =====================================================*/

    if (
        $type === 'blog'
    ) {

      $query_args = [

    'post_type' => 'post',

    'post_status' => 'publish',

    'posts_per_page' => $posts,

    'ignore_sticky_posts' => true,

    'orderby' => $query_orderby,

    'order' => $query_order,

];


/* Apply the selected blog category. */

if (
    $source !== '__all'
    &&
    $source !== ''
) {

    $category = get_term_by(
        'slug',
        sanitize_title($source),
        'category'
    );

    if (
        $category
        &&
        !is_wp_error($category)
    ) {

        $query_args['tax_query'] = [

            [

                'taxonomy' => 'category',

                'field' => 'term_id',

                'terms' => [
                    absint($category->term_id)
                ],

                'include_children' => true,

            ],

        ];

    } else {

        $query_args['post__in'] = [0];

    }

}


$query = new WP_Query($query_args);

        return [

            'type' =>
                'blog',

            'items' =>
                $query,

            'count' =>
                $query instanceof WP_Query
                    ? $query->post_count
                    : 0,

        ];

    }


    /*=====================================================
    AI PROMPTS
    =====================================================*/

    if (
        $type === 'ai_prompt'
    ) {

        $query_args = [

            'post_type' =>
                'ai_prompt',

            'post_status' =>
                'publish',

            'posts_per_page' =>
                $posts,

            'ignore_sticky_posts' =>
                true,

           'orderby' => $query_orderby,

'order' => $query_order,

        ];


        /*
         * Specific AI Prompt category.
         */

        if (
            $source !== '__all'
            &&
            $source !== ''
        ) {

            $query_args['tax_query'] = [

                [

                    'taxonomy' =>
                        'ai_prompt_category',

                    'field' =>
                        'slug',

                    'terms' =>
                        sanitize_title(
                            $source
                        ),

                    'include_children' =>
                        true,

                ]

            ];

        }


        $query =
            new WP_Query(
                $query_args
            );


        return [

            'type' =>
                'ai_prompt',

            'items' =>
                $query,

            'count' =>
                $query->post_count,

        ];

    }


   /*=====================================================
AI TOOLS
=====================================================*/

if (
    $type === 'ai_tool'
) {

    $platforms = [];


    /*
    |--------------------------------------------------------------------------
    | CURATED TOOL FLAGS
    |--------------------------------------------------------------------------
    |
    | These values correspond to the existing AI Tool
    | Curation fields saved against ai_platform terms.
    |
    */

    $curated_meta_keys = [

        '__curated_new' =>
            '_isago_ai_platform_new',

        '__curated_featured' =>
            '_isago_ai_platform_featured',

        '__curated_trending' =>
            '_isago_ai_platform_hot',

        '__curated_popular' =>
            '_isago_ai_platform_popular',

        '__curated_free' =>
            '_isago_ai_platform_free',

    ];


    /*
    |--------------------------------------------------------------------------
    | 1. ALL AI TOOLS
    |--------------------------------------------------------------------------
    */

    if (
        $source === '__all'
        ||
        $source === ''
    ) {

        $platforms = get_terms([

            'taxonomy' =>
                'ai_platform',

            'hide_empty' =>
                false,

            'orderby' =>
                'name',

            'order' =>
                'ASC',

        ]);

    }


    /*
    |--------------------------------------------------------------------------
    | 2. CURATED AI TOOLS
    |--------------------------------------------------------------------------
    */

    elseif (
        isset(
            $curated_meta_keys[$source]
        )
    ) {

        $platforms = get_terms([

            'taxonomy' =>
                'ai_platform',

            'hide_empty' =>
                false,

            'meta_query' => [

                [

                    'key' =>
                        $curated_meta_keys[$source],

                    'value' =>
                        '1',

                    'compare' =>
                        '=',

                ],

            ],

            'orderby' =>
                'name',

            'order' =>
                'ASC',

        ]);

    }


    /*
    |--------------------------------------------------------------------------
    | 3. AI TOOLS BY CATEGORY
    |--------------------------------------------------------------------------
    |
    | Keep the existing category relationship and
    | numeric term ID behaviour.
    |
    */

    else {

        $category_id =
            absint($source);

        if (
            $category_id > 0
            &&
            function_exists(
                'isago_get_ai_tool_category_platforms'
            )
        ) {

            $platforms =
                isago_get_ai_tool_category_platforms(
                    $category_id
                );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | 4. NORMALIZE RESULTS
    |--------------------------------------------------------------------------
    */

    if (
        is_wp_error($platforms)
        ||
        !is_array($platforms)
    ) {

        $platforms = [];

    }

/*=====================================================
SORT AI TOOLS BEFORE LIMITING RESULTS
=====================================================*/

$platforms = array_values(
    array_filter(
        $platforms,
        function ($platform) {
            return $platform instanceof WP_Term;
        }
    )
);


usort(
    $platforms,
    function ($a, $b) use ($orderby) {

        $a_id = (int) $a->term_id;
        $b_id = (int) $b->term_id;


        /*
         * RECENTLY UPDATED
         *
         * The timestamp is recorded by the hooks
         * added in the next step.
         *
         * Terms without timestamps use term ID
         * as a stable fallback.
         */

        if ($orderby === 'modified') {

            $a_modified = (int) get_term_meta(
                $a_id,
                '_isago_ai_platform_modified_at',
                true
            );

            $b_modified = (int) get_term_meta(
                $b_id,
                '_isago_ai_platform_modified_at',
                true
            );

            if ($a_modified !== $b_modified) {

                return $b_modified <=> $a_modified;

            }

            return $b_id <=> $a_id;

        }


        /*
         * OLDER
         *
         * Older term IDs come first.
         */

        if ($orderby === 'older') {

            return $a_id <=> $b_id;

        }


        /*
         * LATEST
         *
         * Newer term IDs come first.
         * Newly created taxonomy terms normally
         * receive higher IDs.
         */

        return $b_id <=> $a_id;

    }
);


    /*
    |--------------------------------------------------------------------------
    | 5. APPLY HOMEPAGE ITEM LIMIT
    |--------------------------------------------------------------------------
    */

    $platforms = array_slice(
        $platforms,
        0,
        $posts
    );


    /*
    |--------------------------------------------------------------------------
    | 6. RETURN AI TOOL SECTION
    |--------------------------------------------------------------------------
    */

    return [

        'type' =>
            'ai_tool',

        'items' =>
            $platforms,

        'count' =>
            count($platforms),

    ];

}

    /*=====================================================
    INVALID TYPE
    =====================================================*/

    return [

        'type' =>
            $type,

        'items' =>
            [],

        'count' =>
            0,

    ];

}




/*=========================================================
HOMEPAGE SECTION URL
=========================================================*/

/**
 * Get the destination URL for a homepage section.
 *
 * Supported:
 *
 * blog
 * ai_prompt
 * ai_tool
 *
 * @param string $type
 * @param mixed  $source
 *
 * @return string
 */
function isago_get_home_section_url(
    $type = 'blog',
    $source = '__all'
) {

    $type = sanitize_key($type);


    /*
     * Normalize source.
     */

    if (
        $source === ''
        ||
        $source === null
    ) {

        $source = '__all';

    }


  /*=====================================================
BLOG
=====================================================*/

if (
    $type === 'blog'
) {

    /*
     * All Blog Posts.
     *
     * Hard-coded destination:
     *
     * /category/blog/
     *
     * This is used when the homepage builder
     * category/source is set to "__all".
     */

    if (
        $source === '__all'
    ) {

        return home_url(
            '/category/blog/'
        );

    }


    /*
     * Specific Blog Category.
     *
     * Resolve directly through the native
     * WordPress category taxonomy.
     *
     * Works for:
     *
     * Parent categories
     * Child categories
     * Grandchild categories
     */

    $category =
        get_term_by(

            'slug',

            sanitize_title(
                $source
            ),

            'category'

        );


    if (
        $category
        &&
        !is_wp_error($category)
    ) {

        $url =
            get_term_link(
                $category,
                'category'
            );


        if (
            !is_wp_error($url)
            &&
            !empty($url)
        ) {

            return $url;

        }

    }


    /*
     * Fallback.
     */

    return home_url(
        '/category/blog/'
    );

}

    /*=====================================================
    AI PROMPTS
    =====================================================*/

    if (
        $type === 'ai_prompt'
    ) {

        /*
         * All AI Prompts.
         */

        if (
            $source === '__all'
        ) {

            return home_url(
                '/ai-prompts-library/'
            );

        }


        /*
         * Specific AI Prompt category.
         */

        $category =
            get_term_by(

                'slug',

                sanitize_title(
                    $source
                ),

                'ai_prompt_category'

            );


        if (
            $category
            &&
            !is_wp_error($category)
        ) {

            $url =
                get_term_link(

                    $category,

                    'ai_prompt_category'

                );


            if (
                !is_wp_error($url)
                &&
                !empty($url)
            ) {

                return $url;

            }

        }


        /*
         * Fallback to AI Prompt Library.
         */

        return home_url(
            '/ai-prompts-library/'
        );

    }


    /*=====================================================
    AI TOOLS
    =====================================================*/

    if (
        $type === 'ai_tool'
    ) {

        /*
         * Get the main AI Tools directory.
         */

        $directory_url = '';


        if (
            function_exists(
                'isago_get_ai_platform_directory_url'
            )
        ) {

            $directory_url =
                isago_get_ai_platform_directory_url();

        }


        /*
         * Fallback if helper doesn't exist.
         */

        if (
            empty($directory_url)
        ) {

            $directory_url =
                home_url(
                    '/ai-tools/'
                );

        }


        /*
         * All AI Tools.
         */

        if (
            $source === '__all'
        ) {

            return $directory_url;

        }


        /*
         * Specific AI Tool Category.
         *
         * AI Tool Categories are stored by
         * term ID in the homepage builder.
         */

        $category_id =
            absint($source);


        if (
            $category_id > 0
        ) {

            return add_query_arg(

                'platform_category',

                $category_id,

                $directory_url

            );

        }


        return $directory_url;

    }


    /*=====================================================
    FALLBACK
    =====================================================*/

    return home_url('/');

}






/*=========================================================
RENDER SINGLE HOMEPAGE ITEM
=========================================================*/

/**
 * Render a single homepage item.
 *
 * Blog posts are intentionally not rendered here because
 * the existing Blog layout components already handle the
 * global WordPress post.
 *
 * AI Prompts and AI Tools use their existing reusable cards.
 *
 * @param mixed  $item
 * @param string $type
 * @param array  $args
 *
 * @return void
 */
function isago_render_homepage_item(
    $item,
    $type = 'blog',
    $args = []
) {

    $type =
        sanitize_key(
            $type
        );


    /*=====================================================
    AI PROMPT
    =====================================================*/

    if (
        $type === 'ai_prompt'
    ) {

        /*
         * The AI Prompt card expects:
         *
         * $args['prompt']
         *
         * Therefore pass the actual WP_Post object
         * using the correct argument name.
         */

        if (
            !($item instanceof WP_Post)
        ) {

            return;

        }


        get_template_part(

            'template-parts/ai-prompts/components/card',

            null,

            [

                'prompt' =>
                    $item,

                'homepage' =>
                    true,

            ]

        );


        return;

    }


    /*=====================================================
    AI TOOL
    =====================================================*/

    if (
        $type === 'ai_tool'
    ) {

        /*
         * AI Tools are ai_platform taxonomy terms.
         */

        if (
            !($item instanceof WP_Term)
        ) {

            return;

        }


        $platform_categories = [];


        if (
            function_exists(
                'isago_get_ai_platform_categories'
            )
        ) {

            $platform_categories =
                isago_get_ai_platform_categories();

        }


        get_template_part(

            'template-parts/ai-tools/browse-ai-tools/card',

            null,

            [

                'platform' =>
                    $item,

                'platform_categories' =>
                    $platform_categories,

                'homepage' =>
                    true,

            ]

        );


        return;

    }

}


/*=========================================================
RENDER HOMEPAGE ITEMS
=========================================================*/

/**
 * Render homepage items.
 *
 * @param mixed  $items
 * @param string $type
 * @param array  $args
 *
 * @return void
 */
function isago_render_homepage_items(
    $items,
    $type = 'blog',
    $args = []
) {

    $type =
        sanitize_key(
            $type
        );


    /*=====================================================
    WP QUERY
    =====================================================*/

    if (
        $items instanceof WP_Query
    ) {

        while (
            $items->have_posts()
        ) {

            $items->the_post();


            /*
             * AI Prompt
             */

            if (
                $type === 'ai_prompt'
            ) {

                isago_render_homepage_item(

                    get_post(),

                    'ai_prompt',

                    $args

                );

            }

        }


        return;

    }


    /*=====================================================
    TAXONOMY TERMS
    =====================================================*/

    if (
        is_array($items)
    ) {

        foreach (
            $items
            as $item
        ) {

            isago_render_homepage_item(

                $item,

                $type,

                $args

            );

        }

    }

}


/*=========================================================
HOMEPAGE ITEM COUNT
=========================================================*/

/**
 * Get homepage item count.
 *
 * @param mixed $items
 *
 * @return int
 */
function isago_get_homepage_item_count(
    $items
) {

    if (
        $items instanceof WP_Query
    ) {

        return (int)
            $items->post_count;

    }


    if (
        is_array($items)
    ) {

        return count($items);

    }


    return 0;

}