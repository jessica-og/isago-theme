(function () {

    'use strict';


    /*=========================================================
    ISAGO DIRECTORY ENGINE
    =========================================================*/

    window.IsagoDirectoryEngine = function (
        config
    ) {


        /*=====================================================
        CHECK CONFIGURATION
        =====================================================*/

        if (
            !config
            ||
            typeof config !== 'object'
        ) {

            return;

        }


        /*=====================================================
        PAGE
        =====================================================*/

        var page =
            document.querySelector(
                config.pageSelector
                    || '.ai-platform-page'
            );


        if (!page) {

            return;

        }


        /*=====================================================
        CONFIGURATION
        =====================================================*/

        var resultsSelector =
            config.resultsSelector
                || '.ai-platform-directory-results';


        var searchFormSelector =
            config.searchFormSelector
                || '[data-directory-search-form]';


        var searchInputSelector =
            config.searchInputSelector
                || '#directory-search';


        var clearSelector =
            config.clearSelector
                || '[data-directory-clear]';


        var loadingClass =
            config.loadingClass
                || 'is-loading';


        var filters =
            Array.isArray(
                config.filters
            )
                ? config.filters
                : [];


        /*=====================================================
        GET CURRENT STATE
        =====================================================*/

        function getState() {

            var url =
                new URL(
                    window.location.href
                );


            var state = {

                search:
                    url.searchParams.get(
                        config.searchParameter
                            || 'directory_search'
                    )
                    || '',

                paged:
                    parseInt(
                        url.searchParams.get(
                            'paged'
                        )
                        || '1',
                        10
                    )

            };


            filters.forEach(
                function (filter) {

                    state[
                        filter.key
                    ] =
                        url.searchParams.get(
                            filter.parameter
                        )
                        || '';

                }
            );


            return state;

        }


        /*=====================================================
        BUILD URL
        =====================================================*/

        function buildUrl(
            state
        ) {

            var url =
                new URL(
                    window.location.href
                );


            /*
             * Remove directory
             * parameters.
             */
            filters.forEach(
                function (filter) {

                    url.searchParams.delete(
                        filter.parameter
                    );

                }
            );


            url.searchParams.delete(
                config.searchParameter
                    || 'directory_search'
            );


            url.searchParams.delete(
                'paged'
            );


            /*
             * Search.
             */
            if (
                state.search
                &&
                state.search.trim() !== ''
            ) {

                url.searchParams.set(
                    config.searchParameter
                        || 'directory_search',
                    state.search.trim()
                );

            }


            /*
             * Filters.
             */
            filters.forEach(
                function (filter) {

                    var value =
                        state[
                            filter.key
                        ] || '';


                    if (
                        value !== ''
                        &&
                        value !== null
                        &&
                        typeof value !== 'undefined'
                    ) {

                        url.searchParams.set(
                            filter.parameter,
                            value
                        );

                    }

                }
            );


            /*
             * Pagination.
             */
            if (
                state.paged
                &&
                parseInt(
                    state.paged,
                    10
                ) > 1
            ) {

                url.searchParams.set(
                    'paged',
                    state.paged
                );

            }


            return url;

        }


        /*=====================================================
        UPDATE SEARCH INPUT
        =====================================================*/

        function updateSearchInput(
            search
        ) {

            var input =
                page.querySelector(
                    searchInputSelector
                );


            if (!input) {

                return;

            }


            input.value =
                search || '';

        }


        /*=====================================================
        UPDATE CLEAR BUTTON
        =====================================================*/

        function updateClearButton(
            search
        ) {

            var clearButton =
                page.querySelector(
                    clearSelector
                );


            if (!clearButton) {

                return;

            }


            if (
                search
                &&
                search.trim() !== ''
            ) {

                clearButton.classList.remove(
                    'is-hidden'
                );

                clearButton.setAttribute(
                    'aria-hidden',
                    'false'
                );

            } else {

                clearButton.classList.add(
                    'is-hidden'
                );

                clearButton.setAttribute(
                    'aria-hidden',
                    'true'
                );

            }

        }


        /*=====================================================
        UPDATE ACTIVE FILTERS
        =====================================================*/

        function updateActiveFilters(
            state
        ) {

            filters.forEach(
                function (filter) {

                    var elements =
                        page.querySelectorAll(
                            '[' +
                            filter.attribute +
                            ']'
                        );


                    elements.forEach(
                        function (element) {

                            var value =
                                element.getAttribute(
                                    filter.attribute
                                )
                                || '';


                            var activeValue =
                                state[
                                    filter.key
                                ]
                                || '';


                            if (
                                value ===
                                activeValue
                            ) {

                                element.classList.add(
                                    'is-active'
                                );

                                element.setAttribute(
                                    'aria-current',
                                    'true'
                                );

                            } else {

                                element.classList.remove(
                                    'is-active'
                                );

                                element.removeAttribute(
                                    'aria-current'
                                );

                            }

                        }
                    );

                }
            );

        }


        /*=====================================================
        UPDATE UI
        =====================================================*/

        function updateUI(
            state
        ) {

            updateSearchInput(
                state.search
            );


            updateClearButton(
                state.search
            );


            updateActiveFilters(
                state
            );


            /*
             * Directory-specific UI.
             */
            if (
                typeof config.onStateApplied ===
                'function'
            ) {

                config.onStateApplied(
                    state,
                    page
                );

            }

        }


        /*=====================================================
        LOADING STATE
        =====================================================*/

function setLoading(loading) {
    page.classList.toggle(
        loadingClass,
        loading
    );

    page.setAttribute(
        'aria-busy',
        loading ? 'true' : 'false'
    );

    // Show or hide the AI Platforms loading indicator.
    if (config.loadingIndicatorSelector) {
        var indicator = document.querySelector(
            config.loadingIndicatorSelector
        );

        if (indicator) {
            indicator.hidden = !loading;
        }
    }
}



        /*=====================================================
        LOAD RESULTS
        =====================================================*/

        function loadResults(
            state,
            updateHistory,
            shouldScroll
        ) {

            state =
                state || getState();


            var formData =
                new FormData();


            /*=================================================
            AJAX ACTION
            =================================================*/

            formData.append(
                'action',
                config.action
            );


            /*=================================================
            NONCE
            =================================================*/

            formData.append(
                'nonce',
                config.nonce
            );


            /*=================================================
            SEARCH
            =================================================*/

            formData.append(
                config.searchParameter
                    || 'directory_search',
                state.search || ''
            );


            /*=================================================
            FILTERS
            =================================================*/

            filters.forEach(
                function (filter) {

                    formData.append(
                        filter.parameter,
                        state[
                            filter.key
                        ] || ''
                    );

                }
            );


            /*=================================================
            PAGED
            =================================================*/

            formData.append(
                'paged',
                state.paged || 1
            );


            setLoading(
                true
            );


            return fetch(
                config.ajaxUrl,
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

                    if (
                        !response.ok
                    ) {

                        throw new Error(
                            'Network response failed.'
                        );

                    }


                    return response.json();

                }
            )


            .then(
                function (response) {

                    if (
                        !response.success
                        ||
                        !response.data
                        ||
                        typeof response.data.html ===
                            'undefined'
                    ) {

                        throw new Error(
                            'Invalid AJAX response.'
                        );

                    }


                    /*=========================================
                    REPLACE RESULTS ONLY
                    =========================================*/

                    var currentResults =
                        page.querySelector(
                            resultsSelector
                        );


                    if (
                        currentResults
                    ) {

                        currentResults.outerHTML =
                            response.data.html;

                    } else {

                        throw new Error(
                            'Results section not found.'
                        );

                    }


                    /*=========================================
                    UPDATE STATE
                    =========================================*/

                    var finalState =
                        Object.assign(
                            {},
                            state,
                            {

                                paged:
                                    response.data.page
                                        ||
                                        state.paged

                            }
                        );


                    updateUI(
                        finalState
                    );


                    /*=========================================
                    UPDATE URL
                    =========================================*/

                    if (
                        updateHistory
                    ) {

                        var newUrl =
                            buildUrl(
                                finalState
                            );


                        window.history.pushState(
                            {
                                isagoDirectory:
                                    true
                            },
                            '',
                            newUrl.toString()
                        );

                    }


                    /*=========================================
                    SCROLL
                    =========================================*/

                    if (
                        shouldScroll !== false
                    ) {

                        var newResults =
                            page.querySelector(
                                resultsSelector
                            );


                        if (
                            newResults
                        ) {

                            var headerOffset =
                                config.headerOffset
                                    || 90;


                            var targetPosition =
                                newResults
                                    .getBoundingClientRect()
                                    .top
                                +
                                window.pageYOffset
                                -
                                headerOffset;


                            window.scrollTo(
                                {

                                    top:
                                        Math.max(
                                            0,
                                            targetPosition
                                        ),

                                    behavior:
                                        'smooth'

                                }
                            );

                        }

                    }


                    return response;

                }
            )


            .catch(
                function (error) {

                    console.error(
                        'Isago Directory:',
                        error
                    );

                }
            )


            .finally(
                function () {

                    setLoading(
                        false
                    );

                }
            );

        }


        /*=====================================================
        SEARCH
        =====================================================*/

        page.addEventListener(
            'submit',
            function (event) {

                var form =
                    event.target.closest(
                        searchFormSelector
                    );


                if (!form) {

                    return;

                }


                event.preventDefault();


                var input =
                    form.querySelector(
                        searchInputSelector
                    );


                var search =
                    input
                        ? input.value.trim()
                        : '';


                var state =
                    getState();


                state.search =
                    search;


                state.paged =
                    1;


                loadResults(
                    state,
                    true,
                    true
                );

            }
        );


        /*=====================================================
        FILTERS
        =====================================================*/

        page.addEventListener(
            'click',
            function (event) {

                var filterElement =
                    null;


                var filterConfig =
                    null;


                /*
                 * Find which filter
                 * was clicked.
                 */
                for (
                    var i = 0;
                    i < filters.length;
                    i++
                ) {

                    var candidate =
                        event.target.closest(
                            '[' +
                            filters[i].attribute +
                            ']'
                        );


                    if (candidate) {

                        filterElement =
                            candidate;

                        filterConfig =
                            filters[i];

                        break;

                    }

                }


                if (
                    !filterElement
                    ||
                    !filterConfig
                ) {

                    return;

                }


                /*
                 * Preserve modifier clicks.
                 */
                if (
                    event.ctrlKey
                    ||
                    event.metaKey
                    ||
                    event.shiftKey
                    ||
                    event.altKey
                    ||
                    event.button !== 0
                ) {

                    return;

                }


                event.preventDefault();


                var value =
                    filterElement.getAttribute(
                        filterConfig.attribute
                    )
                    || '';


                var state =
                    getState();


                state[
                    filterConfig.key
                ] =
                    value;


                state.paged =
                    1;


                loadResults(
                    state,
                    true,
                    true
                );

            }
        );


        /*=====================================================
        CLEAR SEARCH
        =====================================================*/

        page.addEventListener(
            'click',
            function (event) {

                var clearButton =
                    event.target.closest(
                        clearSelector
                    );


                if (!clearButton) {

                    return;

                }


                event.preventDefault();


                var state =
                    getState();


                state.search =
                    '';


                state.paged =
                    1;


                loadResults(
                    state,
                    true,
                    true
                );

            }
        );


        /*=====================================================
        PAGINATION
        =====================================================*/

        page.addEventListener(
            'click',
            function (event) {

                var paginationLink =
                    event.target.closest(
                        config.paginationSelector
                            ||
                            '.ai-platform-page-pagination a'
                    );


                if (!paginationLink) {

                    return;

                }


                /*
                 * Preserve normal
                 * browser behavior.
                 */
                if (
                    event.ctrlKey
                    ||
                    event.metaKey
                    ||
                    event.shiftKey
                    ||
                    event.altKey
                    ||
                    event.button !== 0
                ) {

                    return;

                }


                event.preventDefault();


                var url =
                    new URL(
                        paginationLink.href
                    );


                var paged =
                    parseInt(
                        url.searchParams.get(
                            'paged'
                        )
                        || '1',
                        10
                    );


                var state =
                    getState();


                state.paged =
                    paged;


                loadResults(
                    state,
                    true,
                    true
                );

            }
        );


        /*=====================================================
        BROWSER BACK / FORWARD
        =====================================================*/

        window.addEventListener(
            'popstate',
            function () {

                var state =
                    getState();


                updateUI(
                    state
                );


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


        updateUI(
            initialState
        );


    };

})();