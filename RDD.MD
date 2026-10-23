
  ###  image thumbnail

       <?php

    echo isago_get_post_thumbnail(

    get_the_ID(),
                'medium_large',
                [
                    'loading' => 'lazy',
                     'alt'     => the_title_attribute([
                        'echo' => false
                    ])
                ]
            );

        ?>

------------------------------------------------------------------------------------------

### ad


    <div class="post-category">
<?php if (isago_ad_enabled('post')) : ?>
      <div class="ad-box">
          <?php

        echo isago_render_ad(
            'post',
            'ad-image',
            'category-banner',
        
        );

        ?>
      </div>
<?php endif; ?>

    </div>




------------------------------------------------------------------------------------------

### AUTHOR  => REPLACE

<div class="post-author">

    <?php

    echo get_avatar(
        get_the_author_meta('ID'),
        42
    );

    ?>

    <span>

        <?php the_author(); ?>

    </span>

</div>

### WITH

<?php

$theme = isago_get_theme_settings();

$author_name = !empty($theme['author_name'])
    ? $theme['author_name']
    : 'Isago';

$author_image = !empty($theme['author_image'])
    ? $theme['author_image']
    : '';

?>

<div class="category-author">

    <?php if (!empty($author_image)) : ?>

        <img
            src="<?php echo esc_url($author_image); ?>"
            width="32"
            height="32"
            alt="<?php echo esc_attr($author_name); ?>"
            loading="lazy"
        >

    <?php endif; ?>

    <span>

        By <?php echo esc_html($author_name); ?>

    </span>

    <span>
        •
    </span>

    <span>

        <?php echo esc_html(get_the_date()); ?>

    </span>

</div>


------------------------------------------------------------------------------------------

### IMAGE UPLOAD


    <td>

        <div class="isago-image-upload">

            <input
                type="text"
                class="isago-image-url regular-text"
                name="isago_theme_settings[author_image]"
                value="<?php echo esc_attr(
                    $options['author_image'] ?? ''
                ); ?>"
            >

            <button
                type="button"
                class="button isago-upload-button"
            >

                Upload Image

            </button>

            <br><br>

            <img
                class="isago-preview"
                src="<?php echo esc_url(
                    $options['author_image'] ?? ''
                ); ?>"
                alt="Author preview"
                style="
                    max-width:200px;
                    height:auto;
                    display:<?php
                        echo !empty($options['author_image'])
                            ? 'block'
                            : 'none';
                    ?>;
                    border-radius:50%;
                "
            >

        </div>

        <p class="description">

            Select the image used for the post author.

        </p>

    </td>


------------------------------------------------------------------------------------------
### box shadow
https://isago.com/wp-json/isago/v1/paystack/webhook
box-shadow: 2px 2px 12px #0002;

  border-color: rgba(0, 212, 245, 0.4);
    box-shadow:
        0 12px 30px rgba(0, 0, 0, 0.06);


 border:1px solid rgba(255,255,255,.12);

   border:1px solid var(--isago-primary);


    background:var(--isago-bg);

    color:var(--isago-text);

#################
add a Custom HTML block
<a class="isago-global-button"
   href="https://example.com"
   target="_blank"
   rel="noopener noreferrer">
    Explore AI Tools →
</a>


<section class="isago-premium-cta">
    <div class="isago-premium-cta-content">
        <span class="isago-premium-cta-label">ISAGO PREMIUM PROMPTS</span>

        <h2>Want More Powerful AI Prompts?</h2>

        <p>
            Take your creativity further with advanced AI prompts,
            detailed templates, and ready-to-use workflows designed
            to help you create better results with less guesswork.
        </p>

        <a href="/ai-prompts/" class="isago-global-button">
            Explore Premium AI Prompts →
        </a>
    </div>
</section>

#### buttons  
transparent -- about box
## glitter
isago-ai-tool-categories-hero-link 
## animated line
isago-animated-button
<span class="isago-animated-button-border border-top"></span>
    <span class="isago-animated-button-border border-right"></span>
    <span class="isago-animated-button-border border-bottom"></span>
    <span class="isago-animated-button-border border-left"></span>

---------------------------------------------------
<button
    type="button"
    class="button isago-imagekit-remove-button"
    data-target="#isago-ai-prompt-image"
    data-preview="#isago-ai-prompt-image-preview"
    data-type="image"
    data-object-type="post"
    data-object-id="<?php echo esc_attr($post->ID); ?>"
    data-field-key="ai_prompt_image"
>
    Remove Image
</button>
 
<button
    type="button"
    class="button isago-imagekit-remove-button"
    data-target="#isago-ai-prompt-video"
    data-preview="#isago-ai-prompt-video-preview"
    data-type="video"
    data-object-type="post"
    data-object-id="<?php echo esc_attr($post->ID); ?>"
    data-field-key="ai_prompt_video"
>
    Remove Video
</button>
---------------------------------------------------------------










    .homepage-video-section{

position:relative;

    isolation:isolate;
}


  
.homepage-video-section::before{

    content:"";

    position:absolute;

    left:50%;

    bottom:-230px;

    transform:translateX(-50%);

    width:90%;

    max-width:1400px;

    height:430px;

    background:

        radial-gradient(
            ellipse at center,

            var(--isago-glow) 0%,

            var(--isago-glow-medium) 22%,

            var(--isago-glow-soft) 42%,

            var(--isago-glow-faint) 58%,

            transparent 76%
        );

    filter:blur(40px);

    pointer-events:none;

    z-index:-1;

}


.homepage-video-card:hover .isago-small-post-title a{
    color:var(--isago-primary);
}



------------------------------------------------------------------------------------------
### post type

        'post_type' => [

            'post',

            'isago_video'

        ],









$prompt_image_html =
    isago_get_post_thumbnail(
        $prompt_id,
        'medium_large',
        array(
            'alt' =>
                get_the_title($prompt_id),
            'loading' =>
                'lazy',
        )
    );


$has_prompt_image =
    !empty(
        $prompt_image_html
    );
























