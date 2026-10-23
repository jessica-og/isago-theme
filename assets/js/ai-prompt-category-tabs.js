
(function () {
    'use strict';

    /*=====================================================
    ISAGO AI PROMPT CATEGORY TABS
    Independent AJAX navigation.
    =====================================================*/

    var tabSelector =
        '.ai-prompt-subcategory-navigation ' +
        'a.ai-prompt-subcategory-tab';

    var activeRequest = null;
    var requestNumber = 0;


    /*=====================================================
    LOADING STATE
    =====================================================*/

    function setLoading(results, loading) {
        if (!results) {
            return;
        }

        results.classList.toggle(
            'is-category-tabs-loading',
            loading
        );

        results.setAttribute(
            'aria-busy',
            loading ? 'true' : 'false'
        );
    }


    /*=====================================================
    FETCH AND REPLACE CATEGORY RESULTS
    =====================================================*/

    function loadCategory(url, addHistory) {
        var currentResults = document.querySelector(
            '.ai-platform-directory-results'
        );

        if (!currentResults) {
            console.error(
                'AI Prompt Tabs: results container not found.'
            );
            return;
        }

        var destination;

        try {
            destination = new URL(
                url,
                window.location.href
            );
        } catch (error) {
            console.error(
                'AI Prompt Tabs: invalid category URL.',
                error
            );
            return;
        }

        if (
            destination.origin !==
            window.location.origin
        ) {
            return;
        }

        /* Cancel any previous category request. */
        requestNumber++;

        var thisRequest = requestNumber;

        if (activeRequest) {
            activeRequest.abort();
        }

        var controller = new AbortController();

        activeRequest = controller;

        setLoading(currentResults, true);

        fetch(destination.href, {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            signal: controller.signal
        })
        .then(function (response) {
            if (!response.ok) {
                throw new Error(
                    'Request failed: ' + response.status
                );
            }

            return response.text();
        })
        .then(function (html) {
            if (thisRequest !== requestNumber) {
                return;
            }

            var parser = new DOMParser();

            var newDocument = parser.parseFromString(
                html,
                'text/html'
            );

            var newResults = newDocument.querySelector(
                '.ai-platform-directory-results'
            );

            if (!newResults) {
                throw new Error(
                    'Could not find .ai-platform-directory-results ' +
                    'in the returned page.'
                );
            }

            var oldResults = document.querySelector(
                '.ai-platform-directory-results'
            );

            if (!oldResults) {
                throw new Error(
                    'The current results section no longer exists.'
                );
            }

            /* Replace only the results section. */
            oldResults.replaceWith(newResults);

            /* Update the URL without reloading the page. */
            if (addHistory) {
                window.history.pushState(
                    {
                        isagoPromptCategoryTab: true
                    },
                    '',
                    destination.href
                );
            }

            /* Update the browser tab title. */
            if (newDocument.title) {
                document.title = newDocument.title;
            }

            /* Start the replacement section in a clean state. */
            setLoading(newResults, false);
        })
        .catch(function (error) {
            if (error.name === 'AbortError') {
                return;
            }

            console.error(
                'AI Prompt Tabs: unable to load category.',
                error
            );
        })
        .finally(function () {
            if (thisRequest !== requestNumber) {
                return;
            }

            activeRequest = null;

            var results = document.querySelector(
                '.ai-platform-directory-results'
            );

            setLoading(results, false);
        });
    }


    /*=====================================================
    INTERCEPT CATEGORY TABS BEFORE OTHER CLICK HANDLERS
    =====================================================*/

    window.addEventListener(
        'click',
        function (event) {
            var target = event.target;

            if (
                !target ||
                typeof target.closest !== 'function'
            ) {
                return;
            }

            var link = target.closest(tabSelector);

            if (!link) {
                return;
            }

            /*
             * Keep normal browser behavior for modified clicks.
             */
            if (
                event.button !== 0 ||
                event.ctrlKey ||
                event.metaKey ||
                event.shiftKey ||
                event.altKey ||
                link.hasAttribute('download') ||
                (
                    link.target &&
                    link.target !== '_self'
                )
            ) {
                return;
            }

            var href = link.getAttribute('href');

            if (!href) {
                return;
            }

            var destination;

            try {
                destination = new URL(
                    href,
                    window.location.href
                );
            } catch (error) {
                return;
            }

            if (
                destination.origin !==
                window.location.origin
            ) {
                return;
            }

            /*
             * CRITICAL FIX:
             * Stop the click before it reaches the existing
             * AI Prompts Directory click handlers.
             */
            event.preventDefault();
            event.stopImmediatePropagation();

            if (
                destination.href ===
                window.location.href
            ) {
                return;
            }

            loadCategory(destination.href, true);
        },
        true
    );


    /*=====================================================
    BROWSER BACK / FORWARD
    =====================================================*/

    window.addEventListener(
        'popstate',
        function () {
            loadCategory(
                window.location.href,
                false
            );
        }
    );



    
/*=====================================================
   ISAGO — AI PROMPT CATEGORY TAB SCROLLER
   Works with AJAX-replaced category results.
=====================================================*/

(function () {
    'use strict';

    var shellSelector = '.ai-prompt-subcategory-tabs-shell';
    var tabsSelector = '.ai-prompt-subcategory-tabs';

    /**
     * Update arrow visibility and disabled states.
     */
    function updateTabScroller(shell) {
        if (!shell) {
            return;
        }

        var tabs = shell.querySelector(tabsSelector);

        if (!tabs) {
            return;
        }

        var maxScroll = Math.max(
            0,
            tabs.scrollWidth - tabs.clientWidth
        );

        var hasOverflow = maxScroll > 2;
        var currentScroll = tabs.scrollLeft;

        shell.classList.toggle(
            'is-overflowing',
            hasOverflow
        );

        var leftButton = shell.querySelector(
            '[data-tabs-scroll="left"]'
        );

        var rightButton = shell.querySelector(
            '[data-tabs-scroll="right"]'
        );

        if (leftButton) {
            leftButton.disabled =
                !hasOverflow || currentScroll <= 2;
        }

        if (rightButton) {
            rightButton.disabled =
                !hasOverflow ||
                currentScroll >= maxScroll - 2;
        }
    }

    /**
     * Initialize a tab scroller once.
     */
    function initializeTabScroller(shell) {
        if (!shell) {
            return;
        }

        var tabs = shell.querySelector(tabsSelector);

        if (!tabs) {
            return;
        }

        if (tabs.dataset.isagoScrollerInitialized !== 'true') {
            tabs.dataset.isagoScrollerInitialized = 'true';

            tabs.addEventListener('scroll', function () {
                updateTabScroller(shell);
            }, { passive: true });
        }

        updateTabScroller(shell);
    }

    /**
     * Initialize all category scrollers currently on the page.
     */
    function initializeAllTabScrollers() {
        document.querySelectorAll(shellSelector).forEach(
            initializeTabScroller
        );
    }

    /**
     * Arrow button clicks.
     * Scroll by approximately 75% of the visible tab area.
     */
    document.addEventListener('click', function (event) {
        var button = event.target.closest(
            '[data-tabs-scroll]'
        );

        if (!button) {
            return;
        }

        var shell = button.closest(shellSelector);

        if (!shell) {
            return;
        }

        var tabs = shell.querySelector(tabsSelector);

        if (!tabs) {
            return;
        }

        var direction = button.getAttribute(
            'data-tabs-scroll'
        );

        var distance = Math.max(
            120,
            Math.round(tabs.clientWidth * 0.75)
        );

        tabs.scrollBy({
            left: direction === 'left' ? -distance : distance,
            behavior: window.matchMedia(
                '(prefers-reduced-motion: reduce)'
            ).matches ? 'auto' : 'smooth'
        });
    });

    /**
     * Recalculate arrow states when the viewport changes.
     */
    window.addEventListener(
        'resize',
        initializeAllTabScrollers,
        { passive: true }
    );

    /**
     * Your category AJAX script replaces the results section.
     * Observe those replacements so the new tabs also work.
     */
    var observer = new MutationObserver(function () {
        initializeAllTabScrollers();
    });

    function startTabScroller() {
        initializeAllTabScrollers();

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener(
            'DOMContentLoaded',
            startTabScroller
        );
    } else {
        startTabScroller();
    }
})();


})();
