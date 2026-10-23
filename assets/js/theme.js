document.addEventListener("DOMContentLoaded", function () {


    /*
    |--------------------------------------------------------------------------
    | DARK MODE
    |--------------------------------------------------------------------------
    |
    | Isago uses DARK MODE as the default theme.
    |
    | localStorage values:
    |
    | "dark"  = dark mode
    | "light" = light mode
    |
    | If no saved preference exists, DARK MODE is used.
    |
    */


    const darkButtons =
        document.querySelectorAll(".isago-dark-toggle");


    /*
    |--------------------------------------------------------------------------
    | Apply Theme
    |--------------------------------------------------------------------------
    */

    function applyTheme(theme) {

        const isDark =
            theme !== "light";


        document.body.classList.toggle(
            "dark-mode",
            isDark
        );


        /*
        |--------------------------------------------------------------------------
        | Update HTML preload state
        |--------------------------------------------------------------------------
        */

        document.documentElement.classList.toggle(
            "isago-dark-preload",
            isDark
        );


        /*
        |--------------------------------------------------------------------------
        | Update Icons
        |--------------------------------------------------------------------------
        */

        darkButtons.forEach(function (button) {

            const icon =
                button.querySelector("i");


            if (!icon) {
                return;
            }


            if (isDark) {

                /*
                Dark mode is active.
                Show SUN because clicking it switches to light mode.
                */

                icon.classList.remove(
                    "fa-regular",
                    "fa-moon"
                );

                icon.classList.add(
                    "fa-solid",
                    "fa-sun"
                );


                button.setAttribute(
                    "aria-label",
                    "Switch to Light Mode"
                );


                button.setAttribute(
                    "title",
                    "Switch to Light Mode"
                );


            } else {

                /*
                Light mode is active.
                Show MOON because clicking it switches to dark mode.
                */

                icon.classList.remove(
                    "fa-solid",
                    "fa-sun"
                );

                icon.classList.add(
                    "fa-regular",
                    "fa-moon"
                );


                button.setAttribute(
                    "aria-label",
                    "Switch to Dark Mode"
                );


                button.setAttribute(
                    "title",
                    "Switch to Dark Mode"
                );

            }

        });

    }


    /*
    |--------------------------------------------------------------------------
    | Restore Saved Theme
    |--------------------------------------------------------------------------
    |
    | No saved preference = DARK MODE.
    |
    */

    const savedTheme =
        localStorage.getItem("isago-theme");


    const initialTheme =
        savedTheme === "light"
            ? "light"
            : "dark";


    applyTheme(initialTheme);


    /*
    |--------------------------------------------------------------------------
    | Remove Dark Preload Class
    |--------------------------------------------------------------------------
    |
    | The preload class prevents a light-mode flash
    | before JavaScript finishes loading.
    |
    */

    document.documentElement.classList.remove(
        "isago-dark-preload"
    );


    /*
    |--------------------------------------------------------------------------
    | Toggle Theme
    |--------------------------------------------------------------------------
    */

    darkButtons.forEach(function (button) {

        button.addEventListener(
            "click",
            function () {


                const isDark =
                    document.body.classList.contains(
                        "dark-mode"
                    );


                const newTheme =
                    isDark
                        ? "light"
                        : "dark";


                /*
                Save user's preference.
                */

                localStorage.setItem(
                    "isago-theme",
                    newTheme
                );


                /*
                Apply new theme.
                */

                applyTheme(newTheme);

            }
        );

    });



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
    */

    let ticking = false;


    function saveScrollPosition() {

        if (ticking) {
            return;
        }


        ticking = true;


        window.requestAnimationFrame(function () {

            const currentState =
                history.state || {};


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
    | Save While User Scrolls
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
    | Restore Position When Returning
    |--------------------------------------------------------------------------
    */

    function restoreScrollPosition() {

        const state =
            history.state;


        if (
            state &&
            typeof state.isagoScrollY === "number"
        ) {


            requestAnimationFrame(function () {

                requestAnimationFrame(function () {

                    window.scrollTo({

                        top:
                            state.isagoScrollY,

                        left:
                            0,

                        behavior:
                            "auto"

                    });

                });

            });

        }

    }


    /*
    |--------------------------------------------------------------------------
    | Pageshow
    |--------------------------------------------------------------------------
    */

    window.addEventListener(
        "pageshow",
        function () {

            restoreScrollPosition();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Save Before Leaving
    |--------------------------------------------------------------------------
    */

    window.addEventListener(
        "pagehide",
        function () {

            const currentState =
                history.state || {};


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
        document.querySelector(
            ".isago-back-to-top"
        );


    if (backToTop) {


        function updateBackToTop() {

            if (window.scrollY > 500) {

                backToTop.classList.add(
                    "visible"
                );

            } else {

                backToTop.classList.remove(
                    "visible"
                );

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

                    top:
                        0,

                    left:
                        0,

                    behavior:
                        "smooth"

                });

            }
        );

    }

});