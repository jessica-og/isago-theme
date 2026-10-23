<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
CURRENT PROMPT
=========================================================*/

$prompt_id = get_the_ID();

if (!$prompt_id) {
    return;
}

/*=========================================================
PROMPT ACCESS
=========================================================*/

$prompt_access =
    function_exists('isago_get_ai_prompt_access')
        ? isago_get_ai_prompt_access($prompt_id)
        : 'free';


$prompt_price =
    function_exists('isago_get_ai_prompt_price')
        ? isago_get_ai_prompt_price($prompt_id)
        : '0.00';


$prompt_is_paid =
    $prompt_access === 'paid';


$prompt_price_display =
    $prompt_is_paid
        ? isago_format_currency($prompt_price)
        : '';


/*=========================================================
PROMPT RETIREMENT NOTICE
=========================================================*/

/*
 * The notice is shown only when:
 *
 * 1. The prompt is paid.
 * 2. The prompt is currently scheduled for removal.
 * 3. The current logged-in user has an active entitlement.
 *
 * Visitors and users who have not purchased the prompt
 * will not see this notice.
 */

$prompt_removal_notice =
    false;

$prompt_removal_timestamp =
    0;

$prompt_removal_date =
    '';


if (
    $prompt_is_paid
    &&
    is_user_logged_in()
    &&
    function_exists(
        'isago_is_ai_prompt_removal_scheduled'
    )
    &&
    isago_is_ai_prompt_removal_scheduled(
        $prompt_id
    )
) {

    $current_user_id =
        get_current_user_id();


    $has_prompt_entitlement =
        function_exists(
            'isago_user_has_prompt_entitlement'
        )
            ? isago_user_has_prompt_entitlement(
                $current_user_id,
                $prompt_id
            )
            : false;


    if ($has_prompt_entitlement) {

        $prompt_removal_timestamp =
            function_exists(
                'isago_get_ai_prompt_removal_at'
            )
                ? isago_get_ai_prompt_removal_at(
                    $prompt_id
                )
                : 0;


        if (
            $prompt_removal_timestamp
        ) {

            $prompt_removal_date =
                wp_date(
                    'F j, Y',
                    $prompt_removal_timestamp,
                    wp_timezone()
                );


            $prompt_removal_notice =
                true;

        }

    }

}

/*=========================================================
PROMPT MEDIA
=========================================================*/

/*
 * AI Prompt media is stored in the central
 * Isago media system.
 */



/*=========================================================
PROMPT IMAGE
=========================================================*/

$prompt_image_id = isago_media_get_field(
    'post',
    $prompt_id,
    'ai_prompt_image'
);

$prompt_image_url = '';

if ($prompt_image_id) {
    $prompt_image_url = isago_media_get_field_image_url(
        'post',
        $prompt_id,
        'ai_prompt_image',
        'large'
    );
}

/*=========================================================
SOCIAL IMAGE URL FALLBACK
=========================================================*/

// Use the social image only when no uploaded prompt image exists.
if (empty($prompt_image_url)) {

    $social_image_source_url = get_post_meta(
        $prompt_id,
        'isago_ai_prompt_social_image_url',
        true
    );

    if (
        is_string($social_image_source_url)
        && trim($social_image_source_url) !== ''
        && function_exists('isago_ai_prompt_resolve_social_image_url')
    ) {
        $prompt_image_url = isago_ai_prompt_resolve_social_image_url(
            $social_image_source_url
        );
    }
}


/*=========================================================
PROMPT VIDEO
=========================================================*/

$prompt_video_id =
    isago_media_get_field(
        'post',
        $prompt_id,
        'ai_prompt_video'
    );


$prompt_video_url =
    '';


if ($prompt_video_id) {

    $prompt_video_url =
        isago_media_get_field_url(
            'post',
            $prompt_id,
            'ai_prompt_video'
        );

}


/*=========================================================
AI PROMPT VIDEO / SOCIAL URL
=========================================================*/

/*
 * Current source URL.
 *
 * Supports:
 * - YouTube
 * - Instagram
 * - Pinterest
 * - TikTok
 * - Vimeo
 * - Other supported social/video URLs
 */

$prompt_video_source_url =
    esc_url(
        get_post_meta(
            $prompt_id,
            '_isago_ai_prompt_video_source_url',
            true
        )
    );


/*=========================================================
LEGACY YOUTUBE URL
=========================================================*/

$prompt_youtube_url =
    esc_url(
        get_post_meta(
            $prompt_id,
            '_isago_ai_prompt_youtube_url',
            true
        )
    );


/*=========================================================
RESOLVE PROMPT VIDEO URL
=========================================================*/

/*
 * Priority:
 *
 * 1. ImageKit uploaded video
 * 2. Current AI Prompt social/video source URL
 * 3. Legacy YouTube URL
 */

if (!$prompt_video_url) {

    if ($prompt_video_source_url) {

        $prompt_video_url =
            $prompt_video_source_url;

    } elseif ($prompt_youtube_url) {

        $prompt_video_url =
            $prompt_youtube_url;

    }

}

/*=========================================================
AI PROMPT CATEGORIES
=========================================================*/

$prompt_categories = get_the_terms(
    $prompt_id,
    'ai_prompt_category'
);

if (
    is_wp_error($prompt_categories)
    ||
    empty($prompt_categories)
) {

    $prompt_categories = array();

}


/*=========================================================
BUILD CATEGORY PATHS
=========================================================*/

$category_paths = array();

foreach (
    $prompt_categories as $category
) {

    $ancestors = get_ancestors(
        $category->term_id,
        'ai_prompt_category'
    );

    $ancestors = array_reverse(
        $ancestors
    );

    $path = array();

    foreach (
        $ancestors as $ancestor_id
    ) {

        $ancestor = get_term(
            $ancestor_id,
            'ai_prompt_category'
        );

        if (
            $ancestor
            &&
            !is_wp_error($ancestor)
        ) {

            $path[] = $ancestor;

        }

    }

    $path[] = $category;

    $category_paths[] = $path;

}


/*=========================================================
REMOVE DUPLICATE CATEGORY PATHS
=========================================================*/

$unique_category_paths = array();

foreach (
    $category_paths as $path
) {

    $key = implode(
        '-',
        wp_list_pluck(
            $path,
            'term_id'
        )
    );

    $unique_category_paths[$key] =
        $path;

}

$category_paths =
    array_values(
        $unique_category_paths
    );


/*=========================================================
AI PLATFORMS / AI TOOLS
=========================================================*/

$prompt_platforms = get_the_terms(
    $prompt_id,
    'ai_platform'
);

if (
    is_wp_error($prompt_platforms)
    ||
    empty($prompt_platforms)
) {

    $prompt_platforms = array();

}


/*=========================================================
PROMPT TAGS
=========================================================*/

$prompt_tags = get_the_terms(
    $prompt_id,
    'post_tag'
);

if (
    is_wp_error($prompt_tags)
    ||
    empty($prompt_tags)
) {

    $prompt_tags = array();

}




/*=========================================================
PROMPT ACCESS CHECK
=========================================================*/

/*
 * FREE PROMPTS
 * Everyone can access.
 *
 * PAID PROMPTS
 * Access is granted only when the current user
 * has a valid active entitlement.
 *
 * The entitlement system also verifies the
 * underlying paid order.
 */

$prompt_can_access = false;


if (
    function_exists(
        'isago_user_can_access_prompt'
    )
) {

    $prompt_can_access =
        isago_user_can_access_prompt(
            $prompt_id
        );

}





/*=========================================================
PROMPT CONTENT
=========================================================*/

$raw_prompt_content = '';

$rendered_prompt_content = '';


if ($prompt_can_access) {

    $raw_prompt_content =
        get_post_field(
            'post_content',
            $prompt_id
        );


    $rendered_prompt_content =
        apply_filters(
            'the_content',
            $raw_prompt_content
        );

}


/*=========================================================
PROMPT VARIABLES
=========================================================*/

/*
 * The Gutenberg Code block is the source of truth
 * for variables used by the frontend.
 *
 * Variables are detected from:
 *
 * {{variable-name}}
 *
 * The saved _isago_ai_prompt_variables meta is still
 * used by the Gutenberg toolbar to define available
 * variables, but the frontend only displays variables
 * that actually exist inside the prompt content.
 */

$prompt_variables = array();


/*=========================================================
PROMPT INSTRUCTIONS
=========================================================*/

$prompt_instructions =
    get_post_meta(
        $prompt_id,
        '_isago_ai_prompt_instructions',
        true
    );


$prompt_instructions =
    is_string(
        $prompt_instructions
    )
        ? trim(
            $prompt_instructions
        )
        : '';

/*
 * Search the raw Gutenberg content for variables.
 */

if (
    preg_match_all(
        '/\{\{\s*([a-zA-Z0-9_-]+)\s*\}\}/',
        $raw_prompt_content,
        $matches
    )
) {

    if (
        !empty(
            $matches[1]
        )
    ) {

        foreach (
            $matches[1]
            as $variable
        ) {

            $variable =
                strtolower(
                    trim(
                        $variable
                    )
                );


            $variable =
                preg_replace(
                    '/[^a-z0-9_-]+/',
                    '-',
                    $variable
                );


            $variable =
                preg_replace(
                    '/^-+|-+$/',
                    '',
                    $variable
                );


            if (
                $variable !== ''
            ) {

                $prompt_variables[] =
                    $variable;

            }

        }

    }

}



/*=========================================================
AI PROMPT TEMPLATE
=========================================================*/

$prompt_template = get_post_meta(
    $prompt_id,
    '_isago_ai_prompt_template',
    true
);

$prompt_template = is_string($prompt_template)
    ? trim($prompt_template)
    : '';



/*
 * Remove duplicates while preserving the
 * order in which variables appear in the prompt.
 */

$prompt_variables =
    array_values(
        array_unique(
            $prompt_variables
        )
    );


/*=========================================================
VARIABLE DATA
=========================================================*/

$variable_data = array();


foreach (
    $prompt_variables
    as $variable
) {

    $label =
        str_replace(
            array(
                '-',
                '_'
            ),
            ' ',
            $variable
        );


    $label =
        ucwords(
            $label
        );


    $variable_data[] =
        array(
            'name'  => $variable,
            'label' => $label,
        );

}
?>

<section
    class="isago-ai-prompt-single"
    data-prompt-id="<?php echo esc_attr($prompt_id); ?>"
>

  


    <?php
    /*=====================================================
    VIDEO / IMAGE
    =====================================================*/
    ?>

    <?php if ($prompt_video_url || $prompt_image_url) : ?>

        <div class="isago-ai-prompt-media">

            <?php if ($prompt_video_url) : ?>

                <?php
                /*
                 * Use the same class used by the existing
                 * Isago video lightbox.
                 */
                ?>

               <div class="isago-ai-prompt-video">

    <?php
    /*
     * Universal AI Prompt video/poster image.
     *
     * This helper already supports:
     *
     * - ImageKit AI Prompt video
     * - AI Prompt social/video URL
     * - YouTube
     * - AI Prompt image fallback
     * - Featured image fallback
     */
    ?>

    <?php

    echo isago_get_post_thumbnail(
        $prompt_id,
        'large',
        array(
            'class' =>
                'isago-ai-prompt-video-thumbnail',
            'loading' =>
                'lazy',
            'alt' =>
                get_the_title($prompt_id),
        )
    );

    ?>


    <button
        type="button"
        class="homepage-video-play isago-ai-prompt-video-play"
        data-video="<?php echo esc_url(
            $prompt_video_url
        ); ?>"
        aria-label="<?php echo esc_attr(
            'Play ' . get_the_title($prompt_id)
        ); ?>"
    >

        <i class="fa-solid fa-play"></i>

    </button>

</div>


            <?php elseif ($prompt_image_url) : ?>

                <img
                    src="<?php echo esc_url(
                        $prompt_image_url
                    ); ?>"
                    alt="<?php echo esc_attr(
                        get_the_title($prompt_id)
                    ); ?>"
                    loading="lazy"
                >

            <?php endif; ?>

        </div>

    <?php endif; ?>


<?php if ($prompt_removal_notice) : ?>

    <div
        class="isago-ai-prompt-retirement-notice"
        role="status"
    >

        <div
            class="isago-ai-prompt-retirement-notice-icon"
        >

            <i
                class="fa-solid fa-circle-exclamation"
                aria-hidden="true"
            ></i>

        </div>


        <div
            class="isago-ai-prompt-retirement-notice-content"
        >

            <h3>

                <?php esc_html_e(
                    'Important notice about this prompt',
                    'isago'
                ); ?>

            </h3>


            <p>

                <?php

                printf(
                    wp_kses(
                        __(
                            'We’re sorry, but this prompt is scheduled to be removed from Isago on <strong>%s</strong>.',
                            'isago'
                        ),
                        array(
                            'strong' => array(),
                        )
                    ),
                    esc_html(
                        $prompt_removal_date
                    )
                );

                ?>

            </p>


            <p>

                <?php esc_html_e(
                    'Because you purchased this prompt, we wanted to give you advance notice. A copy of the prompt has also been sent to the email address on your Isago account. Please save it somewhere secure before the removal date.',
                    'isago'
                ); ?>

            </p>


            <p>

                <?php esc_html_e(
                    'We sincerely apologize for the inconvenience and appreciate your support of Isago.',
                    'isago'
                ); ?>

            </p>

        </div>

    </div>

<?php endif; ?>

<!--=====================================================
PROMPT ACCESS
=====================================================-->

<div
    class="isago-ai-prompt-access"
>

    <div
        class="isago-ai-prompt-access-status"
    >

        <?php if ($prompt_is_paid) : ?>

            <span
                class="isago-ai-prompt-access-badge is-paid"
            >

                <i
                    class="fa-solid fa-lock"
                    aria-hidden="true"
                ></i>

                <?php
                esc_html_e(
                    'Paid Prompt',
                    'isago'
                );
                ?>

            </span>

        <?php else : ?>

            <span
                class="isago-ai-prompt-access-badge is-free"
            >

                <i
                    class="fa-solid fa-unlock"
                    aria-hidden="true"
                ></i>

                <?php
                esc_html_e(
                    'Free Prompt',
                    'isago'
                );
                ?>

            </span>

        <?php endif; ?>

    </div>


    <?php if ($prompt_is_paid) : ?>

        <div
            class="isago-ai-prompt-price"
        >

            <?php
            echo esc_html(
                $prompt_price_display
            );
            ?>

        </div>

    <?php endif; ?>

</div>


       <?php
    /*=====================================================
    PROMPT WORKSPACE
    =====================================================*/
       ?>

    <div
        class="isago-ai-prompt-workspace
        <?php echo !empty($prompt_variables)
            ? 'has-variables'
            : 'no-variables'; ?>"
    >


        <?php
        /*=================================================
        PROMPT HEADER
        =================================================*/
        ?>

        <div class="isago-ai-prompt-workspace-header">

            <div class="isago-ai-prompt-workspace-title">

                <span class="isago-ai-prompt-icon">
                    <i class="fa-solid fa-terminal"></i>
                </span>

                <span>
                    <?php esc_html_e(
                        'AI Prompt',
                        'isago'
                    ); ?>
                </span>

            </div>


            <div class="isago-ai-prompt-workspace-actions">

                <button
                    type="button"
                    class="isago-ai-prompt-copy"
                >

                    <i class="fa-regular fa-copy"></i>

                    <span>
                        <?php esc_html_e(
                            'Copy',
                            'isago'
                        ); ?>
                    </span>

                </button>

            </div>

        </div>


        <div class="isago-ai-prompt-workspace-body">
 <?php if ($prompt_can_access) : ?>

            <?php
            /*=============================================
            PROMPT CONTENT
            =============================================*/
            ?>

            <div class="isago-ai-prompt-content">

                <div
                    class="isago-ai-prompt-content-inner"
                    id="isago-ai-prompt-preview"
                >

                    <?php
                    echo $rendered_prompt_content;
                    ?>

                </div>

            </div>


            <?php
            /*=============================================
            VARIABLES
            =============================================*/
            ?>

            <?php if (!empty($variable_data)) : ?>

                <aside class="isago-ai-prompt-variables">

                    <div class="isago-ai-prompt-variables-header">

                        <div>

                            <i class="fa-solid fa-sliders"></i>

                            <span>
                                <?php esc_html_e(
                                    'Fill in the variables',
                                    'isago'
                                ); ?>
                            </span>

                        </div>


                        <span class="isago-ai-prompt-variable-count">

                            0/<?php echo count(
                                $variable_data
                            ); ?>

                        </span>

                    </div>


                    <div class="isago-ai-prompt-variable-list">

                        <?php foreach (
                            $variable_data as $variable
                        ) : ?>

                            <div
                                class="isago-ai-prompt-variable-field"
                                data-variable="<?php echo esc_attr(
                                    $variable['name']
                                ); ?>"
                            >

                                <label>

                                    <?php echo esc_html(
                                        $variable['label']
                                    ); ?>

                                </label>


                                <input
                                    type="text"
                                    class="isago-ai-prompt-variable-input"
                                    data-variable="<?php echo esc_attr(
                                        $variable['name']
                                    ); ?>"
                                    placeholder="<?php echo esc_attr(
                                        $variable['label']
                                    ); ?>"
                                    autocomplete="off"
                                >

                            </div>

                        <?php endforeach; ?>

                    </div>


                    <button
                        type="button"
                        class="isago-ai-prompt-reset"
                    >

                        <i class="fa-solid fa-rotate-left"></i>

                        <?php esc_html_e(
                            'Reset variables',
                            'isago'
                        ); ?>

                    </button>

                </aside>

            <?php endif; ?>

 <?php else : ?>

            <div class="isago-ai-prompt-locked">

                <div class="isago-ai-prompt-locked-icon">

                    <i
                        class="fa-solid fa-lock"
                        aria-hidden="true"
                    ></i>

                </div>


                <div class="isago-ai-prompt-locked-content">

                    <span class="isago-ai-prompt-locked-label">
                        <?php
                        esc_html_e(
                            'Paid Prompt',
                            'isago'
                        );
                        ?>
                    </span>


                    <h3>
                        <?php
                        esc_html_e(
                            'Unlock this AI prompt',
                            'isago'
                        );
                        ?>
                    </h3>


                    <p>
                        <?php
                        esc_html_e(
                            'This premium prompt is available after purchase.',
                            'isago'
                        );
                        ?>
                    </p>


                    <div class="isago-ai-prompt-locked-price">

                        <?php
                        echo esc_html(
                            $prompt_price_display
                        );
                        ?>

                    </div>


                    <div class="isago-ai-prompt-locked-actions">

                        <?php if (!is_user_logged_in()) : ?>

                            <a
                                href="<?php echo esc_url(wp_login_url(get_permalink($prompt_id))); ?>"
                                class="isago-ai-prompt-button isago-ai-prompt-button-secondary"
                            >
                                <?php
                                esc_html_e(
                                    'Sign In',
                                    'isago'
                                );
                                ?>
                            </a>

                        <?php endif; ?>


                     <a
    href="#"
    class="isago-ai-prompt-button isago-ai-prompt-button-primary isago-purchase-prompt"
    data-prompt-id="<?php echo esc_attr($prompt_id); ?>"
>
                            <?php
                            esc_html_e(
                                'Purchase Prompt',
                                'isago'
                            );
                            ?>

                            <i
                                class="fa-solid fa-arrow-right"
                                aria-hidden="true"
                            ></i>
                        </a>

                    </div>

                </div>

            </div>

        <?php endif; ?>

    </div>

        


        <?php
        /*=================================================
        PROMPT FOOTER
        =================================================*/
        ?>

        <div class="isago-ai-prompt-workspace-footer">


            <?php
            /*=============================================
            CATEGORIES
            =============================================*/
            ?>

            <div class="isago-ai-prompt-meta-left">

                <?php if (!empty($category_paths)) : ?>

                    <?php foreach (
                        $category_paths as $path
                    ) : ?>

                        <div class="isago-ai-prompt-meta-group">

                            <span class="isago-ai-prompt-meta-label">
                                <?php esc_html_e(
                                    'Category',
                                    'isago'
                                ); ?>
                            </span>

                            <span class="isago-ai-prompt-category-path">

                                <?php foreach (
                                    $path as $index => $category
                                ) : ?>

                                    <?php if ($index > 0) : ?>

                                        <span class="isago-ai-prompt-category-separator">
                                            <i class="fa-solid fa-chevron-right"></i>
                                        </span>

                                    <?php endif; ?>

                                    <a
                                        href="<?php echo esc_url(
                                            get_term_link(
                                                $category,
                                                'ai_prompt_category'
                                            )
                                        ); ?>"
                                    >
                                        <?php echo esc_html(
                                            $category->name
                                        ); ?>
                                    </a>

                                <?php endforeach; ?>

                            </span>

                        </div>

                    <?php endforeach; ?>

                <?php endif; ?>

  <?php
        /*=================================================
        TAGS
        =================================================*/
        ?>

        <?php if (!empty($prompt_tags)) : ?>

            <div class="isago-ai-prompt-meta-group">

                <span class="isago-ai-prompt-meta-label">
                    <?php esc_html_e(
                        'Tags',
                        'isago'
                    ); ?>
                </span>


                <span class="isago-ai-prompt-tags">

                    <?php foreach (
                        $prompt_tags as $tag
                    ) : ?>

                        <?php
                        $tag_link =
                            get_term_link(
                                $tag,
                                'post_tag'
                            );
                        ?>


                        <?php if (
                            !is_wp_error(
                                $tag_link
                            )
                        ) : ?>

                            <a
                                href="<?php echo esc_url(
                                    $tag_link
                                ); ?>"
                                class="isago-ai-prompt-tag"
                            >
                                <?php echo esc_html(
                                    $tag->name
                                ); ?>
                            </a>

                        <?php endif; ?>

                    <?php endforeach; ?>

                </span>

            </div>

        <?php endif; ?>




                <?php
                /*=========================================
                AI PLATFORMS
                =========================================*/
                ?>

                <?php if (!empty($prompt_platforms)) : ?>

                    <div class="isago-ai-prompt-platforms">

                        <span class="isago-ai-prompt-meta-label">
                            <?php esc_html_e(
                                'Recommended Tools',
                                'isago'
                            ); ?>
                        </span>


                        <div class="isago-ai-prompt-platform-list">

                            <?php foreach (
                                $prompt_platforms as $platform
                            ) : ?>

                                <?php
                       $platform_image_id =
    isago_media_get_field(
        'term',
        $platform->term_id,
        'ai_platform_image'
    );


$platform_image_url =
    '';


if ($platform_image_id) {

    $platform_image_url =
        isago_media_get_field_image_url(
            'term',
            $platform->term_id,
            'ai_platform_image',
            'thumbnail'
        );

}
                                ?>


                                <div class="isago-ai-prompt-platform">

                                    <?php if ($platform_image_url) : ?>

                                        <img
                                            src="<?php echo esc_url(
                                                $platform_image_url
                                            ); ?>"
                                            alt=""
                                            loading="lazy"
                                        >

                                    <?php else : ?>

                                        <span class="isago-ai-prompt-platform-fallback">
                                            <i class="fa-solid fa-sparkles"></i>
                                        </span>

                                    <?php endif; ?>


                                    <span class="isago-ai-prompt-platform-name">

                                        <?php echo esc_html(
                                            $platform->name
                                        ); ?>

                                    </span>


                                    <?php if ($platform_website) : ?>

                                        <a
                                            class="isago-ai-prompt-open-tool"
                                            href="<?php echo esc_url(
                                                $platform_website
                                            ); ?>"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                        >
hhh
                                            <span>
                                                <?php esc_html_e(
                                                    'Open',
                                                    'isago'
                                                ); ?>
                                            </span>

                                            <i class="fa-solid fa-arrow-up-right-from-square"></i>

                                        </a>

                                    <?php endif; ?>

                                </div>

                            <?php endforeach; ?>

                        </div>

                    </div>

                <?php endif; ?>


            </div>

        </div>

    </div>


    
<?php
/*=====================================================
AI PROMPT TEMPLATE — FRONTEND DISPLAY
=====================================================*/

$show_prompt_template =
    $prompt_template !== ''
    &&
    $prompt_can_access;
?>

<?php if ($show_prompt_template) : ?>

    <section
        class="isago-ai-prompt-instructions"
        aria-labelledby="isago-ai-prompt-instructions-title"
    >

        <div class="isago-ai-prompt-instructions-header">

            <div class="isago-ai-prompt-instructions-title">

                <span class="isago-ai-prompt-instructions-icon">
                    <i
                        class="fa-solid fa-terminal"
                        aria-hidden="true"
                    ></i>
                </span>

                <div>

                    <span class="isago-ai-prompt-instructions-eyebrow">
                        <?php
                        esc_html_e(
                            'Ready to Use',
                            'isago'
                        );
                        ?>
                    </span>

                    <h2 id="isago-ai-prompt-instructions-title">
                        <?php
                        esc_html_e(
                            'AI Prompt Variable Example',
                            'isago'
                        );
                        ?>
                    </h2>

                </div>

            </div>

        </div>

        <div class="isago-ai-prompt-template-body">

            <?php
            /*
             * Preserve rich-text formatting.
             * Also support older plain-text content.
             */

            if (
                preg_match(
                    '/<(p|h[1-6]|ul|ol|li|blockquote|div)\b/i',
                    $prompt_template
                )
            ) {

                echo wp_kses_post(
                    $prompt_template
                );

            } else {

                echo wp_kses_post(
                    wpautop(
                        $prompt_template
                    )
                );

            }
            ?>

        </div>

    </section>

<?php endif; ?>


<?php
/*=====================================================
HOW TO USE THIS PROMPT
=====================================================*/

$show_prompt_instructions =
    $prompt_instructions !== ''
    &&
    (
        !$prompt_is_paid
        ||
        $prompt_can_access
    );
?>

<?php if ($show_prompt_instructions) : ?>

    <section
        class="isago-ai-prompt-instructions"
        aria-labelledby="isago-ai-prompt-instructions-title"
    >

        <div class="isago-ai-prompt-instructions-header">

            <div class="isago-ai-prompt-instructions-title">

                <span class="isago-ai-prompt-instructions-icon">

                    <i
                        class="fa-solid fa-circle-info"
                        aria-hidden="true"
                    ></i>

                </span>

                <div  >

                    <span class="isago-ai-prompt-instructions-eyebrow">

                        <?php
                        esc_html_e(
                            'Guide',
                            'isago'
                        );
                        ?>

                    </span>

                    <h2
                        id="isago-ai-prompt-instructions-title"
                    >

                        <?php
                        esc_html_e(
                            'How to Use This Prompt',
                            'isago'
                        );
                        ?>

                    </h2>

                </div>

            </div>

        </div>


        <div class="isago-ai-prompt-instructions-body">

          
<?php
if (trim($prompt_instructions) !== '') {
    echo wp_kses_post($prompt_instructions);
}
?>


        </div>

    </section>

<?php endif; ?>




    <?php
    /*=====================================================
    HIDDEN PROMPT TEMPLATE
    =====================================================*/
    ?>

 <script
    type="application/json"
    id="isago-ai-prompt-data"
>
    <?php

    /*
     * Only expose prompt content to users who
     * are actually allowed to access it.
     *
     * This is important because anything placed
     * inside this JSON block is visible in the
     * browser page source.
     */

    $prompt_data_content =
        $prompt_can_access
            ? $raw_prompt_content
            : '';

    $prompt_data_rendered_content =
        $prompt_can_access
            ? $rendered_prompt_content
            : '';


    echo wp_json_encode(
        array(
            'content' =>
                $prompt_data_content,

            'rendered_content' =>
                $prompt_data_rendered_content,

            'variables' =>
                $prompt_can_access
                    ? $variable_data
                    : array(),
        )
    );

    ?>
</script>


    <?php
    /*=====================================================
    WORDPRESS PAGE BREAKS
    =====================================================*/
    ?>

    <?php

    wp_link_pages(
        array(
            'before' =>
                '<div class="post-pages">',
            'after' =>
                '</div>',
        )
    );

    ?>

</section>