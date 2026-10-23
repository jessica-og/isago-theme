document.addEventListener("DOMContentLoaded", () => {

    /*=====================================================
    CATEGORY PAGE
    =====================================================*/

    const page = document.querySelector(
        ".isago-category-page"
    );

    if (!page) {
        return;
    }


    /*=====================================================
    CATEGORY ID
    =====================================================*/

    const header = document.querySelector(
        ".category-header"
    );

    const category = header
        ? parseInt(
            header.dataset.category || "0",
            10
        )
        : 0;


    if (!category) {
        return;
    }


    /*=====================================================
    CATEGORY CONTENT
    =====================================================*/

    const content = document.querySelector(
        ".category-main"
    );

    if (!content) {
        return;
    }


    /*=====================================================
    LOAD CATEGORY
    =====================================================*/

    function loadCategory(
        filter,
        sort,
        pageNumber = 1,
        platform = 0
    ) {

        filter = filter || "latest";

        sort = sort || "latest";

        pageNumber = parseInt(
            pageNumber || 1,
            10
        );

        platform = parseInt(
            platform || 0,
            10
        );


        /*---------------------------------------------
        Loading state
        ---------------------------------------------*/

        content.classList.add(
            "category-loading"
        );


        /*---------------------------------------------
        AJAX FORM
        ---------------------------------------------*/

        const form = new FormData();

        form.append(
            "action",
            "isago_category_archive"
        );

        form.append(
            "nonce",
            isagoCategory.nonce
        );

        form.append(
            "filter",
            filter
        );

        form.append(
            "sort",
            sort
        );

        form.append(
            "page",
            pageNumber
        );

        form.append(
            "category",
            category
        );

        form.append(
            "platform",
            platform
        );


        /*---------------------------------------------
        AJAX REQUEST
        ---------------------------------------------*/

        fetch(
            isagoCategory.ajax,
            {
                method: "POST",
                body: form
            }
        )

        .then(
            response => {

                if (!response.ok) {

                    throw new Error(
                        "Network response was not OK."
                    );

                }

                return response.json();

            }
        )

        .then(
            response => {

                if (!response.success) {

                    console.error(
                        "Isago category AJAX error:",
                        response
                    );

                    return;

                }


                /*-------------------------------------
                Replace content
                -------------------------------------*/

                content.innerHTML =
                    response.data.html;


                /*-------------------------------------
                BUILD CLEAN URL
                -------------------------------------*/

                let basePath =
                    window.location.pathname;


                /*
                 * Remove existing /page/x/
                 */

                basePath =
                    basePath.replace(
                        /\/page\/\d+\/?/,
                        ""
                    );


                /*
                 * Remove trailing slashes.
                 */

                basePath =
                    basePath.replace(
                        /\/+$/,
                        ""
                    );


                /*
                 * Always use page/1 in the AJAX
                 * state so the URL stays consistent
                 * with your existing pagination system.
                 */

                let newUrl =
                    basePath +
                    "/page/" +
                    pageNumber +
                    "/?filter=" +
                    encodeURIComponent(
                        filter
                    ) +
                    "&sort=" +
                    encodeURIComponent(
                        sort
                    );


                /*
                 * Add platform only when selected.
                 */

                if (platform > 0) {

                    newUrl +=
                        "&platform=" +
                        encodeURIComponent(
                            platform
                        );

                }


                /*-------------------------------------
                Update browser URL
                -------------------------------------*/

                history.replaceState(
                    {},
                    "",
                    newUrl
                );


                /*-------------------------------------
                SYNCHRONIZE PLATFORM SELECT
                -------------------------------------*/

                const platformSelect =
                    document.querySelector(
                        "#category-platform"
                    );


                if (platformSelect) {

                    platformSelect.value =
                        platform > 0
                            ? String(platform)
                            : "0";

                }


                /*-------------------------------------
                SYNCHRONIZE SORT SELECT
                -------------------------------------*/

                const sortSelect =
                    document.querySelector(
                        "#category-sort"
                    );


                if (sortSelect) {

                    sortSelect.value =
                        sort;


                    sortSelect.dataset.filter =
                        filter;


                    sortSelect.dataset.platform =
                        platform > 0
                            ? String(platform)
                            : "0";

                }

            }
        )

        .catch(
            error => {

                console.error(
                    "Isago category AJAX error:",
                    error
                );

            }
        )

        .finally(
            () => {

                content.classList.remove(
                    "category-loading"
                );

            }
        );

    }


    /*=====================================================
    FILTER TABS
    =====================================================*/

    document.addEventListener(
        "click",
        function (e) {

            const link =
                e.target.closest(
                    ".category-tabs > a"
                );


            if (!link) {
                return;
            }


            e.preventDefault();


            const filter =
                link.dataset.filter ||
                "latest";


            const sort =
                link.dataset.sort ||
                "latest";


            /*
             * Latest / Popular always clear
             * the selected platform.
             */

            loadCategory(

                filter,

                sort,

                1,

                0

            );

        }
    );


    /*=====================================================
    AI PLATFORM SELECT
    =====================================================*/

    document.addEventListener(
        "change",
        function (e) {

            if (
                e.target.id !==
                "category-platform"
            ) {

                return;

            }


            const platform =
                parseInt(
                    e.target.value || "0",
                    10
                );


            /*---------------------------------------------
            Get current sorting
            ---------------------------------------------*/

            const sortSelect =
                document.querySelector(
                    "#category-sort"
                );


            const sort =
                sortSelect
                    ? (
                        sortSelect.value ||
                        "latest"
                    )
                    : "latest";


            /*---------------------------------------------
            Platform selected
            ---------------------------------------------*/

            if (platform > 0) {

                loadCategory(

                    "platform",

                    sort,

                    1,

                    platform

                );

                return;

            }


            /*---------------------------------------------
            "AI Platforms" placeholder selected.
            Return to Latest.
            ---------------------------------------------*/

            loadCategory(

                "latest",

                sort,

                1,

                0

            );

        }
    );


    /*=====================================================
    SORTING
    =====================================================*/

    document.addEventListener(
        "change",
        function (e) {

            if (
                e.target.id !==
                "category-sort"
            ) {

                return;

            }


            const filter =
                e.target.dataset.filter ||
                "latest";


            const platform =
                parseInt(
                    e.target.dataset.platform ||
                    "0",
                    10
                );


            const sort =
                e.target.value ||
                "latest";


            /*
             * If a platform is selected,
             * keep filter=platform.
             */

            loadCategory(

                filter,

                sort,

                1,

                platform

            );

        }
    );


    /*=====================================================
    PAGINATION
    =====================================================*/

    document.addEventListener(
        "click",
        function (e) {

            const pageLink =
                e.target.closest(
                    ".isago-pagination a"
                );


            if (!pageLink) {
                return;
            }


            e.preventDefault();


            const url =
                new URL(
                    pageLink.href,
                    window.location.origin
                );


            /*---------------------------------------------
            Determine page number
            ---------------------------------------------*/

            let pageNumber = 1;


            const pathMatch =
                url.pathname.match(
                    /\/page\/(\d+)\/?/
                );


            if (pathMatch) {

                pageNumber =
                    parseInt(
                        pathMatch[1],
                        10
                    );

            } else {

                pageNumber =
                    parseInt(
                        url.searchParams.get(
                            "paged"
                        ) || "1",
                        10
                    );

            }


            /*---------------------------------------------
            Determine filter
            ---------------------------------------------*/

            const filter =
                url.searchParams.get(
                    "filter"
                ) || "latest";


            /*---------------------------------------------
            Determine sorting
            ---------------------------------------------*/

            const sort =
                url.searchParams.get(
                    "sort"
                ) || "latest";


            /*---------------------------------------------
            Determine platform
            ---------------------------------------------*/

            const platform =
                parseInt(
                    url.searchParams.get(
                        "platform"
                    ) || "0",
                    10
                );


            /*---------------------------------------------
            Load requested page
            ---------------------------------------------*/

            loadCategory(

                filter,

                sort,

                pageNumber,

                platform

            );

        }
    );


});

