
<?php

if (!defined('ABSPATH')) {
    exit;
}

$post_id = get_the_ID();

$video_ai_prompt = get_post_meta(
    $post_id,
    '_isago_video_ai_prompt',
    true
);

// Do not display the prompt section when no prompt has been saved.
if (!is_string($video_ai_prompt) || trim($video_ai_prompt) === '') {
    return;
}

?>

<section
    class="isago-video-ai-prompt"
    data-isago-video-prompt
    aria-labelledby="isago-video-prompt-title"
>

    <div class="isago-video-ai-prompt-header">

        <div class="isago-video-ai-prompt-heading">

            <span class="isago-video-ai-prompt-eyebrow">
                AI VIDEO PROMPT
            </span>

            <h2 id="isago-video-prompt-title">
                Create This Video With AI
            </h2>

            <p>
                Explore the prompt used to create this video.
                Reveal it, copy it, and adapt it for your own project.
            </p>

        </div>

        <button
            type="button"
            class="isago-video-prompt-toggle"
            data-prompt-toggle
            aria-expanded="false"
        >
            <span data-toggle-label>Show Prompt</span>

            <span class="isago-video-prompt-chevron" aria-hidden="true">
                +
            </span>
        </button>

    </div>

    <div
        class="isago-video-prompt-panel"
        data-prompt-panel
        hidden
    >

        <div class="isago-video-prompt-toolbar">

            <span class="isago-video-prompt-label">
                FULL AI PROMPT
            </span>

            <button
                type="button"
                class="isago-video-prompt-copy"
                data-prompt-copy
            >
                <span data-copy-label>Copy Prompt</span>
            </button>

        </div>

  <div
    class="isago-video-prompt-text"
    data-prompt-text
>
    <?php echo wp_kses_post($video_ai_prompt); ?>
</div>

        <p
            class="isago-video-prompt-feedback"
            data-prompt-feedback
            aria-live="polite"
        ></p>

    </div>

</section>
