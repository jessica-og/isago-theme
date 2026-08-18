document.addEventListener("DOMContentLoaded", function () {

    /*
    |--------------------------------------------------------------------------
    | DARK MODE
    |--------------------------------------------------------------------------
    */

   /*
|--------------------------------------------------------------------------
| DARK MODE
|--------------------------------------------------------------------------
*/

const darkButtons =
    document.querySelectorAll(".isago-dark-toggle");

if (darkButtons.length) {

    function updateDarkModeIcons() {

        darkButtons.forEach(function (button) {

            const icon = button.querySelector("i");

            if (!icon) {
                return;
            }

            if (
                document.body.classList.contains("dark-mode")
            ) {

                /* Dark mode = SUN */

                icon.classList.remove(
                    "fa-regular",
                    "fa-moon"
                );

                icon.classList.add(
                    "fa-solid",
                    "fa-sun"
                );

            } else {

                /* Light mode = MOON */

                icon.classList.remove(
                    "fa-solid",
                    "fa-sun"
                );

                icon.classList.add(
                    "fa-regular",
                    "fa-moon"
                );

            }

        });

    }


    /*
    |--------------------------------------------------------------------------
    | Restore Saved Theme
    |--------------------------------------------------------------------------
    */

    const savedTheme =
        localStorage.getItem("isago-theme");


    if (savedTheme === "dark") {

        document.body.classList.add(
            "dark-mode"
        );

    }


    updateDarkModeIcons();


    /*
    |--------------------------------------------------------------------------
    | Toggle Theme
    |--------------------------------------------------------------------------
    */

    darkButtons.forEach(function (button) {

        button.addEventListener(
            "click",
            function () {

                document.body.classList.toggle(
                    "dark-mode"
                );


                const isDark =
                    document.body.classList.contains(
                        "dark-mode"
                    );


                localStorage.setItem(
                    "isago-theme",
                    isDark
                        ? "dark"
                        : "light"
                );


                updateDarkModeIcons();

            }
        );

    });

}





    /*
    |--------------------------------------------------------------------------
    | SCROLL POSITION RESTORATION
    |--------------------------------------------------------------------------
    */

    if ("scrollRestoration" in history) {

        history.scrollRestoration = "manual";

    }


    /*
    |--------------------------------------------------------------------------
    | Save Current Scroll Position
    |--------------------------------------------------------------------------
    |
    | We store the position directly on the browser history entry.
    | This means every page/history entry can have its own position.
    |
    */

    let ticking = false;

    function saveScrollPosition() {

        if (ticking) {
            return;
        }

        ticking = true;

        window.requestAnimationFrame(function () {

            const currentState = history.state || {};

            history.replaceState(

                {
                    ...currentState,
                    isagoScrollY: window.scrollY
                },

                "",

                window.location.href

            );

            ticking = false;

        });

    }


    /*
    |--------------------------------------------------------------------------
    | Save while user scrolls
    |--------------------------------------------------------------------------
    */

    window.addEventListener(
        "scroll",
        saveScrollPosition,
        {
            passive: true
        }
    );


    /*
    |--------------------------------------------------------------------------
    | Restore position when returning with Back / Forward
    |--------------------------------------------------------------------------
    */

    function restoreScrollPosition() {

        const state = history.state;

        if (
            state &&
            typeof state.isagoScrollY === "number"
        ) {

            /*
            * Wait until images/content have had a chance
            * to establish their page height.
            */

            requestAnimationFrame(function () {

                requestAnimationFrame(function () {

                    window.scrollTo({

                        top: state.isagoScrollY,

                        left: 0,

                        behavior: "auto"

                    });

                });

            });

        }

    }


    /*
    |--------------------------------------------------------------------------
    | pageshow
    |--------------------------------------------------------------------------
    |
    | pageshow also fires when the browser restores a page from
    | its back/forward cache.
    |
    */

    window.addEventListener(
        "pageshow",
        function () {

            restoreScrollPosition();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Save immediately before leaving the page
    |--------------------------------------------------------------------------
    */

    window.addEventListener(
        "pagehide",
        function () {

            const currentState = history.state || {};

            history.replaceState(

                {
                    ...currentState,
                    isagoScrollY: window.scrollY
                },

                "",

                window.location.href

            );

        }
    );


    /*
    |--------------------------------------------------------------------------
    | BACK TO TOP
    |--------------------------------------------------------------------------
    */

    const backToTop =
        document.querySelector(".isago-back-to-top");


    if (backToTop) {

        function updateBackToTop() {

            if (window.scrollY > 500) {

                backToTop.classList.add("visible");

            } else {

                backToTop.classList.remove("visible");

            }

        }


        window.addEventListener(
            "scroll",
            updateBackToTop,
            {
                passive: true
            }
        );


        updateBackToTop();


        backToTop.addEventListener(
            "click",
            function (event) {

                event.preventDefault();

                window.scrollTo({

                    top: 0,

                    left: 0,

                    behavior: "smooth"

                });

            }
        );

    }

});