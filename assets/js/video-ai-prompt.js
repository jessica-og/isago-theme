
document.addEventListener('click', async function (event) {
    const toggleButton = event.target.closest('[data-prompt-toggle]');
    const copyButton = event.target.closest('[data-prompt-copy]');

    if (toggleButton) {
        const wrapper = toggleButton.closest('[data-isago-video-prompt]');

        if (!wrapper) {
            return;
        }

        const panel = wrapper.querySelector('[data-prompt-panel]');
        const label = toggleButton.querySelector('[data-toggle-label]');
        const expanded = toggleButton.getAttribute('aria-expanded') === 'true';
        const nextExpanded = !expanded;

        panel.hidden = !nextExpanded;
        toggleButton.setAttribute('aria-expanded', String(nextExpanded));

        if (label) {
            label.textContent = nextExpanded ? 'Hide Prompt' : 'Show Prompt';
        }

        const icon = toggleButton.querySelector('.isago-video-prompt-chevron');

        if (icon) {
            icon.textContent = nextExpanded ? '−' : '+';
        }

        return;
    }

    if (copyButton) {
        const wrapper = copyButton.closest('[data-isago-video-prompt]');

        if (!wrapper) {
            return;
        }

        const promptElement = wrapper.querySelector('[data-prompt-text]');
        const feedback = wrapper.querySelector('[data-prompt-feedback]');
        const label = copyButton.querySelector('[data-copy-label]');

        if (!promptElement) {
            return;
        }

   const prompt = promptElement.innerText || promptElement.textContent || '';

        try {
            if (navigator.clipboard && window.isSecureContext) {
                await navigator.clipboard.writeText(prompt);
            } else {
                const temporaryTextarea = document.createElement('textarea');

                temporaryTextarea.value = prompt;
                temporaryTextarea.setAttribute('readonly', '');
                temporaryTextarea.style.position = 'fixed';
                temporaryTextarea.style.opacity = '0';

                document.body.appendChild(temporaryTextarea);
                temporaryTextarea.select();

                const copied = document.execCommand('copy');

                temporaryTextarea.remove();

                if (!copied) {
                    throw new Error('Copy command failed');
                }
            }

            if (label) {
                label.textContent = 'Copied!';
            }

            if (feedback) {
                feedback.textContent = 'Prompt copied to your clipboard.';
            }
        } catch (error) {
            if (feedback) {
                feedback.textContent =
                    'Copying was blocked by your browser. Please select and copy the prompt manually.';
            }
        }

        window.setTimeout(function () {
            if (label) {
                label.textContent = 'Copy Prompt';
            }
        }, 2000);
    }
});
