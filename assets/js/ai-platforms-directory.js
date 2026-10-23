(function () {

    'use strict';


    /*=========================================================
    CHECK CONFIGURATION
    =========================================================*/

    if (
        typeof IsagoAIPlatforms === 'undefined'
    ) {

        return;

    }


    /*=========================================================
    AI PLATFORMS DIRECTORY
    =========================================================*/

    IsagoDirectoryEngine({

        /*=====================================================
        AJAX
        =====================================================*/

        action:
            'isago_ai_platforms',

        nonce:
            IsagoAIPlatforms.nonce,

        ajaxUrl:
            IsagoAIPlatforms.ajaxUrl,


        /*=====================================================
        PAGE
        =====================================================*/

        pageSelector:
            '.ai-platform-page',

        resultsSelector:
            '.ai-platform-directory-results',

            loadingIndicatorSelector:
    '[data-ai-platforms-loading]',

        searchFormSelector:
            '[data-ai-platform-search-form]',

        searchInputSelector:
            '#platform-search',

        clearSelector:
            '[data-ai-platform-clear]',

        paginationSelector:
            '.ai-platform-page-pagination a',


        /*=====================================================
        SEARCH
        =====================================================*/

        searchParameter:
            'platform_search',


        /*=====================================================
        FILTERS
        =====================================================*/

        filters: [

            {

                key:
                    'category',

                attribute:
                    'data-ai-platform-category',

                parameter:
                    'platform_category'

            }

        ],


        /*=====================================================
        HEADER OFFSET
        =====================================================*/

        headerOffset:
            90

    });

})();