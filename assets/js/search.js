document.addEventListener('DOMContentLoaded', () => {

    console.log('ISAGO SEARCH SYSTEM RUNNING');

    const openButton = document.querySelector('.isago-search-button');
    const overlay = document.querySelector('.isago-search-overlay');
    const closeButton = document.querySelector('.isago-search-close');

    /*
     * OPEN SEARCH OVERLAY
     */
    if (openButton && overlay) {
        openButton.addEventListener('click', () => {
            overlay.classList.add('active');
            document.body.style.overflow = 'hidden';

            const overlayInput = overlay.querySelector('.isago-search-input');

            if (overlayInput) {
                setTimeout(() => overlayInput.focus(), 150);
            }
        });
    }

    /*
     * CLOSE SEARCH OVERLAY
     */
    if (closeButton && overlay) {
        closeButton.addEventListener('click', () => {
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        });
    }

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && overlay) {
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }
    });

    /*
     * LIVE SEARCH
     * Supports the overlay and the search page independently.
     */
    const searchInputs = document.querySelectorAll('.isago-search-input');

    searchInputs.forEach((input) => {

        /*
         * Find the results container belonging to this input.
         */
        const form = input.closest('form');

        let resultsBox = null;

        if (form) {
            resultsBox = form.querySelector('.isago-search-results');
        }

        if (!resultsBox && input.parentElement) {
            resultsBox = input.parentElement.querySelector('.isago-search-results');
        }

        if (!resultsBox) {
            resultsBox = document.createElement('div');
            resultsBox.className = 'isago-search-results';

            if (form) {
                form.appendChild(resultsBox);
            } else {
                input.parentElement.appendChild(resultsBox);
            }
        }

        let timeout = null;
        let controller = null;

        input.addEventListener('input', function () {

            const keyword = this.value.trim();

            clearTimeout(timeout);

            if (controller) {
                controller.abort();
                controller = null;
            }

            if (keyword.length < 2) {
                resultsBox.innerHTML = '';
                resultsBox.style.display = 'none';
                return;
            }

            timeout = setTimeout(async () => {

                if (
                    typeof isago_search === 'undefined' ||
                    !isago_search.ajax_url
                ) {
                    console.error(
                        'Isago search error: AJAX URL is not configured.'
                    );

                    resultsBox.innerHTML =
                        '<div class="no-results">Search is temporarily unavailable.</div>';

                    resultsBox.style.display = 'block';
                    return;
                }

                controller = new AbortController();

                resultsBox.innerHTML =
                    '<div class="search-loading">Searching...</div>';

                resultsBox.style.display = 'block';

                try {

                    const url = new URL(isago_search.ajax_url, window.location.origin);

                    url.searchParams.set('action', 'isago_live_search');
                    url.searchParams.set('keyword', keyword);

                    const response = await fetch(url.toString(), {
                        method: 'GET',
                        credentials: 'same-origin',
                        signal: controller.signal
                    });

                    if (!response.ok) {
                        throw new Error('HTTP error: ' + response.status);
                    }

                    const responseData = await response.json();

                    const data = Array.isArray(responseData)
                        ? responseData
                        : (
                            Array.isArray(responseData.data)
                                ? responseData.data
                                : []
                        );

                    console.log('Isago search results:', data);

                    if (!data.length) {
                        resultsBox.innerHTML =
                            '<div class="no-results">No results found.</div>';

                        resultsBox.style.display = 'block';
                        return;
                    }

                    resultsBox.innerHTML = '';

                    data.forEach((item) => {

                        const link = document.createElement('a');
                        link.href = item.link || '#';
                        link.className = 'search-item';

                       const image = document.createElement('img');

image.alt = '';
image.loading = 'lazy';

if (item.image) {
    image.src = item.image;

    image.addEventListener('error', () => {
        image.style.display = 'none';
    }, { once: true });
} else {
    image.style.display = 'none';
}

                        const title = document.createElement('span');
                        title.textContent = item.title || 'Untitled';

                        link.appendChild(image);
                        link.appendChild(title);

                        resultsBox.appendChild(link);
                    });

                    resultsBox.style.display = 'block';

                } catch (error) {

                    if (error.name === 'AbortError') {
                        return;
                    }

                    console.error('Isago live search failed:', error);

                    resultsBox.innerHTML =
                        '<div class="no-results">Unable to load results. Please try again.</div>';

                    resultsBox.style.display = 'block';
                }

            }, 300);

        });

    });

});