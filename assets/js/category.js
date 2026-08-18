document.addEventListener("DOMContentLoaded", () => {

    const page = document.querySelector(".isago-category-page");

    if (!page) {
        return;
    }

    const header = document.querySelector(".category-header");

    const category = header
        ? header.dataset.category
        : 0;

    const content = document.querySelector(".category-main");

    function loadCategory(filter, sort, pageNumber = 1) {

        content.classList.add("category-loading");

        const form = new FormData();

        form.append("action", "isago_category_archive");
        form.append("nonce", isagoCategory.nonce);
        form.append("filter", filter);
        form.append("sort", sort);
        form.append("page", pageNumber);
        form.append("category", category);

        fetch(isagoCategory.ajax, {

            method: "POST",

            body: form

        })

       .then(response => response.json())

       

        .then(response => {

            if (!response.success) {

                console.log(response);

                return;

            }

            content.innerHTML = response.data.html;

  let basePath = window.location.pathname;

//  Remove existing /page/x/
basePath = basePath.replace(/\/page\/\d+\/?/, '');

//  Remove extra trailing slashes
basePath = basePath.replace(/\/+$/, '');

// Build clean URL
const newUrl =
    basePath +
    "/page/" + pageNumber +
    "/?filter=" + filter +
    "&sort=" + sort;

history.replaceState({}, "", newUrl);

        })

        .catch(error => {

            console.error(error);

        })

        .finally(() => {

            content.classList.remove("category-loading");

        });

    }

    /*
    --------------------------------------------------
    Filters
    --------------------------------------------------
    */

    document.addEventListener("click", function (e) {

        const link = e.target.closest(".category-tabs a");

        if (!link) {
            return;
        }

        e.preventDefault();

        loadCategory(

            link.dataset.filter,

            link.dataset.sort,

            1

        );

    });

    /*
    --------------------------------------------------
    Sorting
    --------------------------------------------------
    */

    document.addEventListener("change", function (e) {

        if (e.target.id !== "category-sort") {
            return;
        }

        loadCategory(

            e.target.dataset.filter,

            e.target.value,

            1

        );

    });

    /*
    --------------------------------------------------
    Pagination
    --------------------------------------------------
    */

    document.addEventListener("click", function (e) {

        const pageLink = e.target.closest(".isago-pagination a");

        if (!pageLink) {
            return;
        }

        e.preventDefault();

       const url = new URL(pageLink.href);


let pageNumber = 1;

const pathMatch = url.pathname.match(/\/page\/(\d+)\/?/);

if (pathMatch) {
    pageNumber = parseInt(pathMatch[1]);
} else {
    pageNumber = url.searchParams.get("paged") || 1;
}

loadCategory(
    url.searchParams.get("filter") || "latest",
    url.searchParams.get("sort") || "latest",
    pageNumber
);

    });

});