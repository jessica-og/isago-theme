<?php

if (!defined('ABSPATH')) {
    exit;
}


/*=========================================================
PROMPT
=========================================================*/

$prompt =
    isset($args['prompt'])
        ? $args['prompt']
        : null;


if (
    !$prompt
    ||
    is_wp_error($prompt)
) {

    return;

}


/*=========================================================
PROMPT ID
=========================================================*/

$prompt_id =
    absint(
        $prompt->ID
    );


if (!$prompt_id) {

    return;

}


/*=========================================================
PROMPT URL
=========================================================*/

$url =
    get_permalink(
        $prompt_id
    );


/*=========================================================
PROMPT TITLE
=========================================================*/

$title =
    get_the_title(
        $prompt_id
    );


/*=========================================================
UPLOADED VIDEO
=========================================================*/

$prompt_video_id =
    isago_media_get_field(
        'post',
        $prompt_id,
        'ai_prompt_video'
    );


$uploaded_video_url =
    '';


if ($prompt_video_id) {

    $uploaded_video_url =
        isago_media_get_field_url(
            'post',
            $prompt_id,
            'ai_prompt_video'
        );

}


/*=========================================================
VIDEO / SOCIAL URL
=========================================================*/

/*
 * New generic video/social URL field.
 *
 * Supports:
 * - YouTube
 * - Instagram
 * - Pinterest
 * - TikTok
 * - Vimeo
 * - Other supported URLs
 */

$prompt_video_source_url =
    get_post_meta(
        $prompt_id,
        '_isago_ai_prompt_video_source_url',
        true
    );


/*
 * Legacy fallback.
 *
 * Existing prompts that still use the old
 * YouTube meta field will continue to work.
 */

if (
    empty(
        $prompt_video_source_url
    )
) {

    $prompt_video_source_url =
        get_post_meta(
            $prompt_id,
            '_isago_ai_prompt_youtube_url',
            true
        );

}


$prompt_video_source_url =
    is_string(
        $prompt_video_source_url
    )
        ? trim(
            $prompt_video_source_url
        )
        : '';


/*=========================================================
VIDEO STATUS
=========================================================*/

$has_uploaded_prompt_video =
    !empty(
        $uploaded_video_url
    );


$has_prompt_social_video =
    !empty(
        $prompt_video_source_url
    );


$has_prompt_video =
    (
        $has_uploaded_prompt_video
        ||
        $has_prompt_social_video
    );


/*=========================================================
LIGHTBOX VIDEO URL
=========================================================*/

/*
 * URL video gets priority.
 *
 * This means that if both an ImageKit video and a
 * social/video URL exist, the social/video URL is used
 * by the card play button.
 */

$lightbox_video_url =
    '';


if (
    $has_prompt_social_video
) {

    $lightbox_video_url =
        $prompt_video_source_url;

}

elseif (
    $has_uploaded_prompt_video
) {

    $lightbox_video_url =
        $uploaded_video_url;

}


/*=========================================================
PROMPT MEDIA
=========================================================*/

/*
 * Get the Prompt Image directly.
 *
 * This is used as a fallback for social/video URLs
 * when a remote thumbnail cannot be retrieved.
 */

$prompt_image_url =
    isago_media_get_field_image_url(
        'post',
        $prompt_id,
        'ai_prompt_image',
        'medium_large'
    );



/*=========================================================
SOCIAL IMAGE URL FALLBACK
=========================================================*/
$social_image_url = get_post_meta(
    $prompt_id,
    'isago_ai_prompt_social_image_url',
    true
);

if (
    empty($prompt_image_url)
    && is_string($social_image_url)
    && wp_http_validate_url($social_image_url)
) {
    $prompt_image_url = esc_url($social_image_url);
}


/*
 * Start with no media.
 */

$prompt_media =
    '';


/*
 * ---------------------------------------------------------
 * URL / SOCIAL VIDEO
 * ---------------------------------------------------------
 *
 * Supported autoplay previews:
 *
 * - YouTube
 * - TikTok
 * - Vimeo
 *
 * Instagram and Pinterest remain thumbnail previews.
 *
 */

if (
    $has_prompt_social_video
) {

    $social_thumbnail_url =
        '';


    /*
     * -----------------------------------------------------
     * DETECT PROVIDER
     * -----------------------------------------------------
     */

    $social_video_provider =
        '';


    if (
        function_exists(
            'isago_get_social_video_provider'
        )
    ) {

        $social_video_provider =
            isago_get_social_video_provider(
                $prompt_video_source_url
            );

    }


    /*
     * -----------------------------------------------------
     * YOUTUBE THUMBNAIL
     * -----------------------------------------------------
     */

    $youtube_id =
        isago_get_youtube_id(
            $prompt_video_source_url
        );


    if (
        !empty(
            $youtube_id
        )
    ) {

        $social_thumbnail_url =
            sprintf(
                'https://img.youtube.com/vi/%s/hqdefault.jpg',
                rawurlencode(
                    $youtube_id
                )
            );


        $social_video_provider =
            'youtube';

    }


    /*
     * -----------------------------------------------------
     * OTHER SOCIAL / VIDEO THUMBNAIL
     * -----------------------------------------------------
     */

    if (
        empty(
            $social_thumbnail_url
        )
        &&
        function_exists(
            'isago_get_social_video_thumbnail_url'
        )
    ) {

        $social_thumbnail_url =
            isago_get_social_video_thumbnail_url(
                $prompt_video_source_url
            );

    }


    /*
     * -----------------------------------------------------
     * CAN AUTOPLAY INLINE?
     * -----------------------------------------------------
     */

    $social_inline_autoplay =
        in_array(
            $social_video_provider,
            array(
                'youtube',
                'tiktok',
                'vimeo'
            ),
            true
        );


    /*
     * -----------------------------------------------------
     * AUTOPLAY SOCIAL VIDEO PLACEHOLDER
     * -----------------------------------------------------
     *
     * The JavaScript video-preview system will replace
     * this image with the appropriate iframe when the
     * card enters the viewport.
     */

    if (
        $social_inline_autoplay
    ) {

        $poster_url =
            $social_thumbnail_url;


        if (
            empty(
                $poster_url
            )
            &&
            !empty(
                $prompt_image_url
            )
        ) {

            $poster_url =
                $prompt_image_url;

        }


        if (
            empty(
                $poster_url
            )
        ) {

            $poster_url =
                get_template_directory_uri()
                . '/assets/images/placeholder.png';

        }


        $prompt_media =
            sprintf(

                '<div
                    class="isago-social-video-preview"
                    data-video-url="%s"
                    data-provider="%s"
                >

                    <img
                        src="%s"
                        alt="%s"
                        loading="lazy"
                        onerror="this.onerror=null;this.src=\'%s\';"
                    >

                </div>',

                esc_attr(
                    $prompt_video_source_url
                ),

                esc_attr(
                    $social_video_provider
                ),

                esc_url(
                    $poster_url
                ),

                esc_attr(
                    $title
                ),

                esc_url(
                    get_template_directory_uri()
                    . '/assets/images/placeholder.png'
                )

            );

    }


    /*
     * -----------------------------------------------------
     * INSTAGRAM / PINTEREST / OTHER PROVIDERS
     * -----------------------------------------------------
     *
     * These remain image previews.
     */

    elseif (
        !empty(
            $social_thumbnail_url
        )
    ) {

        $prompt_media =
            sprintf(

                '<img
                    src="%s"
                    alt="%s"
                    loading="lazy"
                    onerror="this.onerror=null;this.src=\'%s\';"
                >',

                esc_url(
                    $social_thumbnail_url
                ),

                esc_attr(
                    $title
                ),

                esc_url(
                    get_template_directory_uri()
                    . '/assets/images/placeholder.png'
                )

            );

    }


    /*
     * -----------------------------------------------------
     * PROMPT IMAGE FALLBACK
     * -----------------------------------------------------
     */

    elseif (
        !empty(
            $prompt_image_url
        )
    ) {

        $prompt_media =
            sprintf(

                '<img
                    src="%s"
                    alt="%s"
                    loading="lazy"
                >',

                esc_url(
                    $prompt_image_url
                ),

                esc_attr(
                    $title
                )

            );

    }


    /*
     * -----------------------------------------------------
     * FINAL FALLBACK
     * -----------------------------------------------------
     */

    else {

        $prompt_media =
            sprintf(

                '<img
                    src="%s"
                    alt="%s"
                    loading="lazy"
                >',

                esc_url(
                    get_template_directory_uri()
                    . '/assets/images/placeholder.png'
                ),

                esc_attr(
                    $title
                )

            );

    }

}

/*
 * ---------------------------------------------------------
 * UPLOADED IMAGEKIT VIDEO
 * ---------------------------------------------------------
 *
 * For an uploaded ImageKit video, continue using the
 * existing universal thumbnail helper.
 */

elseif (
    $has_uploaded_prompt_video
) {

    $prompt_media =
        isago_get_post_thumbnail(
            $prompt_id,
            'medium_large',
            array(
                'loading' =>
                    'lazy',

                'alt' =>
                    $title,
            )
        );

}


/*
 * ---------------------------------------------------------
 * NORMAL PROMPT IMAGE
 * ---------------------------------------------------------
 *
 * No URL video and no uploaded video.
 *
 * Use the existing thumbnail system.
 */


else {

    if (!empty($prompt_image_url)) {

        $prompt_media = sprintf(
            '<img src="%s" alt="%s" loading="lazy">',
            esc_url($prompt_image_url),
            esc_attr($title)
        );

    } else {

        $prompt_media =
            isago_get_post_thumbnail(
                $prompt_id,
                'medium_large',
                array(
                    'loading' => 'lazy',
                    'alt'     => $title,
                )
            );

    }

}



$has_prompt_media =
    !empty(
        $prompt_media
    );


/*=========================================================
SHORT DESCRIPTION
=========================================================*/

$prompt_short_description =
    get_post_meta(
        $prompt_id,
        '_isago_ai_prompt_short_description',
        true
    );


if (
    is_string(
        $prompt_short_description
    )
) {

    $prompt_short_description =
        trim(
            wp_strip_all_tags(
                strip_shortcodes(
                    $prompt_short_description
                )
            )
        );

} else {

    $prompt_short_description =
        '';

}


/*=========================================================
SHORT DESCRIPTION PREVIEW
=========================================================*/

$prompt_short_description_preview =
    '';


if (
    !empty(
        $prompt_short_description
    )
) {

    $prompt_short_description_preview =
        wp_trim_words(
            $prompt_short_description,
            26,
            '…'
        );

}


$has_short_description =
    !empty(
        $prompt_short_description_preview
    );


/*=========================================================
PROMPT CONTENT
=========================================================*/

$prompt_content =
    get_post_field(
        'post_content',
        $prompt_id
    );


if (
    is_string(
        $prompt_content
    )
) {

    $prompt_content =
        strip_shortcodes(
            $prompt_content
        );


    $prompt_content =
        wp_strip_all_tags(
            $prompt_content
        );


    $prompt_content =
        trim(
            preg_replace(
                '/\s+/',
                ' ',
                $prompt_content
            )
        );

} else {

    $prompt_content =
        '';

}


/*=========================================================
PROMPT CONTENT PREVIEW
=========================================================*/

$prompt_content_preview =
    '';


if (
    !empty(
        $prompt_content
    )
) {

    $prompt_content_preview =
        wp_trim_words(
            $prompt_content,
            58,
            '…'
        );

}


$has_prompt_content =
    !empty(
        $prompt_content_preview
    );


/*=========================================================
PROMPT CATEGORIES
=========================================================*/

$prompt_categories =
    get_the_terms(
        $prompt_id,
        'ai_prompt_category'
    );


if (
    is_wp_error(
        $prompt_categories
    )
    ||
    empty(
        $prompt_categories
    )
) {

    $prompt_categories =
        array();

}


/*=========================================================
CATEGORY NAME
=========================================================*/

$prompt_category_name =
    'AI Prompt';


/*
 * If the card is being rendered from a category archive,
 * allow the parent template to provide the current category.
 */

if (
    isset(
        $args['category_name']
    )
    &&
    !empty(
        $args['category_name']
    )
) {

    $prompt_category_name =
        $args['category_name'];

}


/*
 * Otherwise use the prompt's first assigned category.
 */

if (
    !empty(
        $prompt_categories
    )
) {

    /*
     * If a category ID was supplied, try to use
     * the matching category first.
     */

    $category_id =
        isset(
            $args['category_id']
        )
            ? absint(
                $args['category_id']
            )
            : 0;


    if (
        $category_id
    ) {

        foreach (
            $prompt_categories
            as $prompt_category
        ) {

            if (
                (int)
                $prompt_category->term_id
                ===
                $category_id
            ) {

                $prompt_category_name =
                    $prompt_category->name;

                break;

            }

        }

    }

    elseif (
        empty(
            $args['category_name']
        )
    ) {

        $prompt_category_name =
            $prompt_categories[0]->name;

    }

}


/*=========================================================
AI PLATFORMS / MODELS
=========================================================*/

$prompt_platforms =
    get_the_terms(
        $prompt_id,
        'ai_platform'
    );


if (
    is_wp_error(
        $prompt_platforms
    )
    ||
    empty(
        $prompt_platforms
    )
) {

    $prompt_platforms =  
        array();

}


$prompt_platforms =
    array_slice(
        $prompt_platforms,
        0,
        3
    );


$has_prompt_platforms =
    !empty(
        $prompt_platforms
    );


/*=========================================================
PROMPT TAGS
=========================================================*/

$prompt_tags =
    get_the_terms(
        $prompt_id,
        'post_tag'
    );


if (
    is_wp_error(
        $prompt_tags
    )
    ||
    empty(
        $prompt_tags
    )
) {

    $prompt_tags =
        array();

}


$prompt_tags =
    array_slice(
        $prompt_tags,
        0,
        4
    );


$has_prompt_tags =
    !empty(
        $prompt_tags
    );


/*=========================================================
PROMPT META
=========================================================*/

$has_prompt_meta =
    (
        $has_prompt_platforms
        ||
        $has_prompt_tags
    );


/*=========================================================
PROMPT VARIABLES
=========================================================*/

$variables =
    isago_get_ai_prompt_variables(
        $prompt_id
    );


$variable_count =
    count(
        $variables
    );


/*=========================================================
CARD CLASSES
=========================================================*/

$card_classes =
    'ai-platform-card';


$card_classes .=
    $has_prompt_media
        ? ' ai-platform-card-has-image'
        : ' ai-platform-card-no-image';



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


/*
 * ---------------------------------------------------------
 * CURRENT USER PURCHASE ACCESS
 * ---------------------------------------------------------
 *
 * Only show "Purchased" when the current logged-in
 * user has an active entitlement for this paid prompt.
 *
 * Free prompts are not considered purchased.
 */

$prompt_user_has_access =
    false;


if (
    $prompt_is_paid
    &&
    is_user_logged_in()
    &&
    function_exists(
        'isago_user_can_access_prompt'
    )
) {

    $prompt_user_has_access =
        isago_user_can_access_prompt(
            $prompt_id
        );

}


/*
 * Price is only displayed when the prompt is paid
 * and the current user does not already have it.
 */

$prompt_price_display =
    $prompt_is_paid
    &&
    !$prompt_user_has_access
        ? isago_format_currency(
            $prompt_price
        )
        : '';


/*=========================================================
SAVED PROMPT
=========================================================*/

$prompt_is_saved =
    false;


if (
    is_user_logged_in()
    &&
    function_exists(
        'isago_is_prompt_saved'
    )
) {

    $prompt_is_saved =
        isago_is_prompt_saved(
            get_current_user_id(),
            $prompt_id
        );

}


?>

<article
    class="<?php echo esc_attr(
        $card_classes
    ); ?>"
>


    <div  class="ai-platform-card-link"
    >


        <?php if (
            $has_prompt_media
        ) : ?>


            <!-- =================================
            PROMPT MEDIA
            ================================= -->

            <div
    class="<?php
    echo esc_attr(
        'ai-platform-card-media-ai-prompts'
        . (
            $has_prompt_video
                ? ' ai-prompt-video-media'
                : ''
        )
    );
    ?>"
>

                <?php
                echo $prompt_media;
                ?>


                <?php if (
                    $has_prompt_video
                    &&
                    !empty(
                        $lightbox_video_url
                    )
                ) : ?>


                    <button
                        type="button"
                        class="homepage-video-play ai-prompt-video-play"
                        data-video="<?php
                        echo esc_attr(
                            $lightbox_video_url
                        );
                        ?>"
                        aria-label="<?php
                        echo esc_attr(
                            'Play ' . $title
                        );
                        ?>"
                    >

                        <i
                            class="fa-solid fa-play"
                            aria-hidden="true"
                        ></i>

                    </button>


                <?php endif; ?>


            </div>


        <?php elseif (
            $has_prompt_content
        ) : ?>


            <!-- =================================
            PROMPT CONTENT PREVIEW
            ================================= -->

            <div
                class="ai-platform-card-prompt-preview"
            >

                <span
                    class="ai-platform-card-prompt-preview-text"
                >

                    <?php
                    echo esc_html(
                        $prompt_content_preview
                    );
                    ?>

                </span>

            </div>


        <?php endif; ?>


        <!-- =====================================
        CARD BODY
        ===================================== -->

        <div
            class="ai-platform-card-body"
        >


            <!-- CATEGORY -->

            <div
                class="ai-platform-card-category-wrap"
            >

                <span
                    class="ai-platform-card-category"
                >

                    <?php
                    echo esc_html(
                        $prompt_category_name
                    );
                    ?>

                </span>


            </div>


            <!-- TITLE -->
<h3 class="ai-platform-card-title">

    <a
        href="<?php echo esc_url($url); ?>"
        class="ai-platform-card-title-link"
    >

        <?php
        echo esc_html($title);
        ?>

    </a>

</h3>


            <!-- SHORT DESCRIPTION -->

            <?php if (
                $has_short_description
            ) : ?>


                <div
                    class="ai-platform-card-description"
                >

                    <?php
                    echo esc_html(
                        $prompt_short_description_preview
                    );
                    ?>

                </div>


            <?php endif; ?>


            <!-- PLATFORM + TAGS -->

            <?php if (
                $has_prompt_meta
            ) : ?>


                <div
                    class="ai-platform-card-meta"
                >


                    <!-- AI PLATFORM / MODEL -->

                    <?php if (
                        $has_prompt_platforms
                    ) : ?>


                       
<?php foreach ($prompt_platforms as $prompt_platform) : ?>

    <?php
    $platform_url = get_term_link(
        $prompt_platform,
        'ai_platform'
    );

    if (is_wp_error($platform_url)) {
        continue;
    }
    ?>

    <a
        href="<?php echo esc_url($platform_url); ?>"
        class="ai-platform-card-platform"
        title="<?php echo esc_attr($prompt_platform->name); ?>"
        aria-label="<?php
            echo esc_attr(
                'Explore ' . $prompt_platform->name
            );
        ?>"
    >
        <i
            class="fa-solid fa-sparkles"
            aria-hidden="true"
        ></i>

        <span>
            <?php echo esc_html($prompt_platform->name); ?>
        </span>
    </a>

<?php endforeach; ?>


                    <?php endif; ?>


                    <!-- TAGS -->

<!-- TAGS -->

<?php if ($has_prompt_tags) : ?>

    <?php foreach ($prompt_tags as $prompt_tag) : ?>

        <?php

        $tag_url = get_term_link(
            $prompt_tag,
            'post_tag'
        );

        if (is_wp_error($tag_url)) {
            continue;
        }

        ?>

        <a
            href="<?php echo esc_url($tag_url); ?>"
            class="ai-platform-card-tag"
            aria-label="<?php
            echo esc_attr(
                'Browse prompts tagged ' . $prompt_tag->name
            );
            ?>"
        >

            <?php
            echo esc_html($prompt_tag->name);
            ?>

        </a>

    <?php endforeach; ?>

<?php endif; ?>



                </div>


            <?php endif; ?>


           <!-- ACTION -->

<div
    class="ai-platform-card-access"
>

    <?php if ($prompt_user_has_access) : ?>

        <span
            class="ai-platform-card-access-badge is-purchased"
        >

            <i
                class="fa-solid fa-circle-check"
                aria-hidden="true"
            ></i>

            Purchased

        </span>


    <?php elseif ($prompt_is_paid) : ?>

        <span
            class="ai-platform-card-access-badge is-free ai-platform-card-price"
        >

            <?php
            echo esc_html(
                $prompt_price_display
            );
            ?>

        </span>


    <?php else : ?>

        <span
            class="ai-platform-card-access-badge is-free"
        >

            Free

        </span>

    <?php endif; ?>


<a
    href="<?php echo esc_url($url); ?>"
    class="ai-platform-card-action"
    aria-label="<?php
    echo esc_attr('View prompt: ' . $title);
    ?>"
>

    View prompt

    <i
        class="fa-solid fa-arrow-right"
        aria-hidden="true"
    ></i>

</a>

</div>

        </div>  


    </div>


<?php if (is_user_logged_in()) : ?>

    <button
        type="button"
        class="isago-save-prompt-button<?php
        echo $prompt_is_saved
            ? ' is-saved'
            : '';
        ?>"
        data-prompt-id="<?php echo esc_attr(
            $prompt_id
        ); ?>"
        aria-pressed="<?php echo $prompt_is_saved
            ? 'true'
            : 'false';
        ?>"
        aria-label="<?php
        echo esc_attr(
            $prompt_is_saved
                ? 'Remove from saved prompts'
                : 'Save prompt'
        );
        ?>"
    >

        <i
            class="<?php
            echo $prompt_is_saved
                ? 'fa-solid fa-bookmark'
                : 'fa-regular fa-bookmark';
            ?>"
            aria-hidden="true"
        ></i>

    </button>

<?php endif; ?>


</article>