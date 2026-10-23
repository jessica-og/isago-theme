(function () {

    'use strict';


    /*=========================================================
    AI PROMPTS DIRECTORY
    =========================================================*/

    function init() {


        /*=====================================================
        CONFIG
        =====================================================*/

        if (
            typeof window.IsagoAIPrompts === 'undefined'
        ) {

            console.error(
                'Isago AI Prompts: configuration missing.'
            );

            return;

        }


        /*=====================================================
        PAGE
        =====================================================*/

        var page =
            document.querySelector(
                '.ai-platform-page'
            );


        if (!page) {

            return;

        }


        /*=====================================================
        SEARCH FORM
        =====================================================*/

        var searchForm =
            page.querySelector(
                '[data-ai-prompt-search-form]'
            );


        if (!searchForm) {

            return;

        }


        /*=====================================================
        HELPERS
        =====================================================*/

        function getElement(selector) {

            return page.querySelector(
                selector
            );

        }


        function getResults() {

            return getElement(
                '.ai-platform-directory-results'
            );

        }


        /*=====================================================
        DROPDOWNS
        =====================================================*/

        function getDropdowns() {

            return page.querySelectorAll(
                '.ai-prompt-filter-dropdown'
            );

        }


        function closeDropdowns(except) {

            getDropdowns().forEach(
                function (dropdown) {

                    if (
                        except &&
                        dropdown === except
                    ) {

                        return;

                    }

                    dropdown.removeAttribute(
                        'open'
                    );

                }
            );

        }


        function closeAllDropdowns() {

            closeDropdowns();

        }


        /*=====================================================
        STATE
        =====================================================*/

        function getState() {

            var url =
                new URL(
                    window.location.href
                );


            var paged =
                parseInt(
                    url.searchParams.get(
                        'paged'
                    ) || '1',
                    10
                );


            if (
                isNaN(paged) ||
                paged < 1
            ) {

                paged = 1;

            }


            return {

                search:
                    url.searchParams.get(
                        'prompt_search'
                    ) || '',

                category:
                    url.searchParams.get(
                        'prompt_category'
                    ) || '',

                platform:
                    url.searchParams.get(
                        'prompt_platform'
                    ) || '',

                paged:
                    paged

            };

        }


        /*=====================================================
        UPDATE URL
        =====================================================*/

        function updateUrl(
            state,
            replace
        ) {

            var url =
                new URL(
                    window.location.href
                );


            url.searchParams.delete(
                'prompt_search'
            );

            url.searchParams.delete(
                'prompt_category'
            );

            url.searchParams.delete(
                'prompt_platform'
            );

            url.searchParams.delete(
                'paged'
            );


            if (state.search) {

                url.searchParams.set(
                    'prompt_search',
                    state.search
                );

            }


            if (state.category) {

                url.searchParams.set(
                    'prompt_category',
                    state.category
                );

            }


            if (state.platform) {

                url.searchParams.set(
                    'prompt_platform',
                    state.platform
                );

            }


            if (
                state.paged &&
                state.paged > 1
            ) {

                url.searchParams.set(
                    'paged',
                    state.paged
                );

            }


            if (replace) {

                window.history.replaceState(
                    {
                        isagoAIPrompts: true
                    },
                    '',
                    url.toString()
                );

            } else {

                window.history.pushState(
                    {
                        isagoAIPrompts: true
                    },
                    '',
                    url.toString()
                );

            }

        }


        /*=====================================================
        SEARCH INPUT
        =====================================================*/

        function updateSearchInput(value) {

            var input =
                getElement(
                    '#prompt-search'
                );


            if (input) {

                input.value =
                    value || '';

            }

        }


        /*=====================================================
        CLEAR SEARCH BUTTONS
        =====================================================*/

        function updateClearButton(value) {

            var buttons =
                page.querySelectorAll(
                    '[data-ai-prompt-clear]'
                );


            buttons.forEach(
                function (button) {

                    if (
                        value &&
                        value.trim() !== ''
                    ) {

                        button.classList.remove(
                            'is-hidden'
                        );

                        button.setAttribute(
                            'aria-hidden',
                            'false'
                        );

                    } else {

                        button.classList.add(
                            'is-hidden'
                        );

                        button.setAttribute(
                            'aria-hidden',
                            'true'
                        );

                    }

                }
            );

        }


        /*=====================================================
        UPDATE DROPDOWN LABELS
        =====================================================*/

        function updateDropdownLabels(state) {


            /*-----------------------------------------------
            CATEGORY
            -----------------------------------------------*/

            var categoryLabel =
                getElement(
                    '[data-ai-prompt-category-label]'
                );


            if (categoryLabel) {

                var categoryOption =
                    page.querySelector(
                        '[data-ai-prompt-category="' +
                        CSS.escape(
                            state.category || ''
                        ) +
                        '"]'
                    );


                if (categoryOption) {

                    var categoryName =
                        categoryOption.getAttribute(
                            'data-ai-prompt-category-name'
                        );


                    if (categoryName) {

                        categoryLabel.textContent =
                            categoryName;

                    }

                } else {

                    categoryLabel.textContent =
                        'All categories';

                }

            }


            /*-----------------------------------------------
            AI TOOL
            -----------------------------------------------*/

            var platformLabel =
                getElement(
                    '[data-ai-prompt-platform-label]'
                );


            if (platformLabel) {

                var platformOption =
                    page.querySelector(
                        '[data-ai-prompt-platform="' +
                        CSS.escape(
                            state.platform || ''
                        ) +
                        '"]'
                    );


                if (platformOption) {

                    var platformName =
                        platformOption.getAttribute(
                            'data-ai-prompt-platform-name'
                        );


                    if (platformName) {

                        platformLabel.textContent =
                            platformName;

                    }

                } else {

                    platformLabel.textContent =
                        'All AI tools';

                }

            }

        }


     /*=====================================================
UPDATE ACTIVE FILTERS
=====================================================*/

function updateFilters(state) {


    /*-----------------------------------------------
    CATEGORY
    -----------------------------------------------*/

    var categories =
        page.querySelectorAll(
            '[data-ai-prompt-category]'
        );


    categories.forEach(
        function (item) {

            var value =
                item.getAttribute(
                    'data-ai-prompt-category'
                ) || '';


            var active =
                value ===
                (
                    state.category || ''
                );


            item.classList.toggle(
                'is-active',
                active
            );


            if (active) {

                item.setAttribute(
                    'aria-current',
                    'true'
                );

            } else {

                item.removeAttribute(
                    'aria-current'
                );

            }


            /*
             * Use the existing checkmark.
             * Never create another one.
             */

            var check =
                item.querySelector(
                    '.fa-check'
                );


            if (active) {

                if (!check) {

                    check =
                        document.createElement(
                            'i'
                        );

                    check.className =
                        'fa-solid fa-check';

                    check.setAttribute(
                        'aria-hidden',
                        'true'
                    );

                    item.appendChild(
                        check
                    );

                }

            } else {

                if (check) {

                    check.remove();

                }

            }

        }
    );


    /*-----------------------------------------------
    AI TOOL
    -----------------------------------------------*/

    var platforms =
        page.querySelectorAll(
            '[data-ai-prompt-platform]'
        );


    platforms.forEach(
        function (item) {

            var value =
                item.getAttribute(
                    'data-ai-prompt-platform'
                ) || '';


            var active =
                value ===
                (
                    state.platform || ''
                );


            item.classList.toggle(
                'is-active',
                active
            );


            if (active) {

                item.setAttribute(
                    'aria-current',
                    'true'
                );

            } else {

                item.removeAttribute(
                    'aria-current'
                );

            }


            /*
             * Use the existing checkmark.
             * Never create another one.
             */

            var check =
                item.querySelector(
                    '.fa-check'
                );


            if (active) {

                if (!check) {

                    check =
                        document.createElement(
                            'i'
                        );

                    check.className =
                        'fa-solid fa-check';

                    check.setAttribute(
                        'aria-hidden',
                        'true'
                    );

                    item.appendChild(
                        check
                    );

                }

            } else {

                if (check) {

                    check.remove();

                }

            }

        }
    );


    /*-----------------------------------------------
    DROPDOWN LABELS
    -----------------------------------------------*/

    updateDropdownLabels(
        state
    );

}

        /*=====================================================
        LOADING
        =====================================================*/

      
function setLoading(loading) {
    page.classList.toggle(
        'isago-ai-prompts-loading',
        loading
    );

    page.setAttribute(
        'aria-busy',
        loading ? 'true' : 'false'
    );

    var indicator = document.querySelector(
        '[data-isago-ai-prompts-loading]'
    );

    if (indicator) {
        indicator.hidden = !loading;
    }
}


        /*=====================================================
        SCROLL TO RESULTS
        =====================================================*/

        function scrollToResults() {

            var newResults =
                getResults();


            if (!newResults) {

                return;

            }


            var position =
                newResults
                    .getBoundingClientRect()
                    .top
                +
                window.pageYOffset
                -
                90;


            window.scrollTo(
                {

                    top:
                        Math.max(
                            0,
                            position
                        ),

                    behavior:
                        'smooth'

                }
            );

        }


        /*=====================================================
        AJAX REQUEST
        =====================================================*/

        function loadResults(
            state,
            pushHistory,
            shouldScroll
        ) {

            var formData =
                new FormData();


            formData.append(
                'action',
                'isago_ai_prompts'
            );


            formData.append(
                'nonce',
                IsagoAIPrompts.nonce
            );


            formData.append(
                'prompt_search',
                state.search || ''
            );


            formData.append(
                'prompt_category',
                state.category || ''
            );


            formData.append(
                'prompt_platform',
                state.platform || ''
            );


            formData.append(
                'paged',
                state.paged || 1
            );


            setLoading(true);


            return fetch(
                IsagoAIPrompts.ajaxUrl,
                {

                    method:
                        'POST',

                    credentials:
                        'same-origin',

                    body:
                        formData

                }
            )

            .then(
                function (response) {

                    if (!response.ok) {

                        throw new Error(
                            'HTTP ' +
                            response.status
                        );

                    }


                    return response.json();

                }
            )

            .then(
                function (response) {

                    if (
                        !response ||
                        !response.success ||
                        !response.data ||
                        typeof response.data.html ===
                            'undefined'
                    ) {

                        console.error(
                            'Isago AI Prompts AJAX response:',
                            response
                        );

                        throw new Error(
                            'Invalid AJAX response.'
                        );

                    }


                    var currentResults =
                        getResults();


                    if (!currentResults) {

                        throw new Error(
                            'Results container not found.'
                        );

                    }


                    currentResults.outerHTML =
                        response.data.html;


                    if (
                        response.data.page
                    ) {

                        state.paged =
                            parseInt(
                                response.data.page,
                                10
                            ) || 1;

                    }


                    /*-----------------------------------------
                    URL
                    -----------------------------------------*/

                    if (pushHistory) {

                        updateUrl(
                            state,
                            false
                        );

                    }


                    /*-----------------------------------------
                    UI
                    -----------------------------------------*/

                    updateSearchInput(
                        state.search
                    );


                    updateClearButton(
                        state.search
                    );


                    updateFilters(
                        state
                    );


                    closeAllDropdowns();


                    if (
                        shouldScroll !== false
                    ) {

                        scrollToResults();

                    }


                    return response;

                }
            )

            .catch(
                function (error) {

                    console.error(
                        'Isago AI Prompts:',
                        error
                    );

                }
            )

            .finally(
                function () {

                    setLoading(false);

                }
            );

        }


        /*=====================================================
        SEARCH SUBMIT
        =====================================================*/

        searchForm.addEventListener(
            'submit',
            function (event) {

                event.preventDefault();


                var input =
                    getElement(
                        '#prompt-search'
                    );


                var state =
                    getState();


                state.search =
                    input
                        ? input.value.trim()
                        : '';


                state.paged =
                    1;


                closeAllDropdowns();


                loadResults(
                    state,
                    true,
                    true
                );

            }
        );


        /*=====================================================
        DROPDOWN TOGGLE
        =====================================================*/

        getDropdowns().forEach(
            function (dropdown) {

                dropdown.addEventListener(
                    'toggle',
                    function () {

                        if (dropdown.open) {

                            closeDropdowns(
                                dropdown
                            );

                        }

                    }
                );

            }
        );



/*=====================================================
EXPAND / COLLAPSE CATEGORY CHILDREN
=====================================================*/

page.addEventListener('click', function (event) {

    var toggle = event.target.closest(
        '[data-ai-prompt-category-toggle]'
    );

    if (!toggle || !page.contains(toggle)) {
        return;
    }

    event.preventDefault();
    event.stopPropagation();

    var childrenId = toggle.getAttribute('aria-controls');

    var children = childrenId
        ? document.getElementById(childrenId)
        : null;

    if (!children) {
        return;
    }

    var isExpanding = children.hidden;

    children.hidden = !isExpanding;

    toggle.setAttribute(
        'aria-expanded',
        isExpanding ? 'true' : 'false'
    );

    var icon = toggle.querySelector('i');

    if (icon) {
        icon.classList.toggle(
            'fa-chevron-down',
            isExpanding
        );

        icon.classList.toggle(
            'fa-chevron-right',
            !isExpanding
        );
    }

    var categoryName = toggle
        .closest('.ai-prompt-filter-category-node')
        ?.querySelector(
            ':scope > .ai-prompt-filter-category-row .ai-prompt-filter-option span'
        )
        ?.textContent
        ?.trim();

    toggle.setAttribute(
        'aria-label',
        (isExpanding ? 'Collapse ' : 'Expand ')
        + (categoryName || 'category')
        + ' subcategories'
    );

});





        /*=====================================================
        DIRECTORY CLICKS
        =====================================================*/

        page.addEventListener(
            'click',
            function (event) {

                var target =
                    event.target.closest(
                        'a'
                    );


                if (!target) {

                    return;

                }


                if (
                    !page.contains(
                        target
                    )
                ) {

                    return;

                }


                if (
                    event.button !== 0 ||
                    event.ctrlKey ||
                    event.metaKey ||
                    event.shiftKey ||
                    event.altKey
                ) {

                    return;

                }


               
/*=================================================
CLEAR SEARCH
=================================================*/

if (
    target.hasAttribute('data-ai-prompt-clear')
) {
    event.preventDefault();
    event.stopPropagation();

    var clearState = getState();

    // Remove the search term but preserve active filters.
    clearState.search = '';
    clearState.paged = 1;

    // Clear the visible search field immediately.
    updateSearchInput('');

    // Hide both clear-search buttons.
    updateClearButton('');

    // Close any open filter dropdowns.
    closeAllDropdowns();

    // Fetch the updated results through AJAX.
    loadResults(clearState, true, true);

    return;
}


                /*=================================================
                ALL PROMPTS
                =================================================*/

                if (
                    target.hasAttribute(
                        'data-ai-prompt-all'
                    )
                ) {

                    event.preventDefault();


                    var allState = {

                        search: '',

                        category: '',

                        platform: '',

                        paged: 1

                    };


                    updateSearchInput('');


                    updateClearButton('');


                    updateFilters(
                        allState
                    );


                    closeAllDropdowns();


                    loadResults(
                        allState,
                        true,
                        true
                    );


                    return;

                }


                /*=================================================
                CATEGORY
                =================================================*/

                if (
                    target.hasAttribute(
                        'data-ai-prompt-category'
                    )
                ) {

                    event.preventDefault();


                    var categoryState =
                        getState();


                    categoryState.category =
                        target.getAttribute(
                            'data-ai-prompt-category'
                        ) || '';


                    categoryState.paged =
                        1;


                    /*
                     * Update the label immediately.
                     */

                    var categoryName =
                        target.getAttribute(
                            'data-ai-prompt-category-name'
                        );


                    var categoryLabel =
                        getElement(
                            '[data-ai-prompt-category-label]'
                        );


                    if (
                        categoryLabel &&
                        categoryName
                    ) {

                        categoryLabel.textContent =
                            categoryName;

                    }


                    closeAllDropdowns();


                    loadResults(
                        categoryState,
                        true,
                        true
                    );


                    return;

                }


                /*=================================================
                AI TOOL
                =================================================*/

                if (
                    target.hasAttribute(
                        'data-ai-prompt-platform'
                    )
                ) {

                    event.preventDefault();


                    var platformState =
                        getState();


                    platformState.platform =
                        target.getAttribute(
                            'data-ai-prompt-platform'
                        ) || '';


                    platformState.paged =
                        1;


                    /*
                     * Update the label immediately.
                     */

                    var platformName =
                        target.getAttribute(
                            'data-ai-prompt-platform-name'
                        );


                    var platformLabel =
                        getElement(
                            '[data-ai-prompt-platform-label]'
                        );


                    if (
                        platformLabel &&
                        platformName
                    ) {

                        platformLabel.textContent =
                            platformName;

                    }


                    closeAllDropdowns();


                    loadResults(
                        platformState,
                        true,
                        true
                    );


                    return;

                }


                /*=================================================
                PAGINATION
                =================================================*/

                if (
                    target.closest(
                        '.ai-platform-page-pagination'
                    )
                ) {

                    event.preventDefault();


                    var paginationUrl =
                        new URL(
                            target.href,
                            window.location.origin
                        );


                    var paginationState =
                        getState();


                    paginationState.search =
                        paginationUrl.searchParams.get(
                            'prompt_search'
                        ) || '';


                    paginationState.category =
                        paginationUrl.searchParams.get(
                            'prompt_category'
                        ) || '';


                    paginationState.platform =
                        paginationUrl.searchParams.get(
                            'prompt_platform'
                        ) || '';


                    paginationState.paged =
                        parseInt(
                            paginationUrl.searchParams.get(
                                'paged'
                            ) || '1',
                            10
                        );


                    if (
                        isNaN(
                            paginationState.paged
                        ) ||
                        paginationState.paged < 1
                    ) {

                        paginationState.paged =
                            1;

                    }


                    closeAllDropdowns();


                    loadResults(
                        paginationState,
                        true,
                        true
                    );


                    return;

                }

            }
        );


        /*=====================================================
        CLICK OUTSIDE DROPDOWN
        =====================================================*/

        document.addEventListener(
            'click',
            function (event) {

                var dropdown =
                    event.target.closest(
                        '.ai-prompt-filter-dropdown'
                    );


                if (
                    dropdown &&
                    page.contains(
                        dropdown
                    )
                ) {

                    return;

                }


                closeAllDropdowns();

            }
        );


        /*=====================================================
        ESCAPE
        =====================================================*/

        document.addEventListener(
            'keydown',
            function (event) {

                if (
                    event.key ===
                    'Escape'
                ) {

                    closeAllDropdowns();

                }

            }
        );


        /*=====================================================
        BACK / FORWARD
        =====================================================*/

        window.addEventListener(
            'popstate',
            function () {

                var state =
                    getState();


                updateSearchInput(
                    state.search
                );


                updateClearButton(
                    state.search
                );


                updateFilters(
                    state
                );


                closeAllDropdowns();


                loadResults(
                    state,
                    false,
                    false
                );

            }
        );


        /*=====================================================
        INITIAL STATE
        =====================================================*/

        var initialState =
            getState();


        updateSearchInput(
            initialState.search
        );


        updateClearButton(
            initialState.search
        );


        updateFilters(
            initialState
        );

    }


    /*=========================================================
    START
    =========================================================*/

    if (
        document.readyState ===
        'loading'
    ) {

        document.addEventListener(
            'DOMContentLoaded',
            init
        );

    } else {

        init();

    }


    



})();