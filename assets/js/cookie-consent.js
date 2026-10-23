document.addEventListener("DOMContentLoaded", function () {

    const root = document.getElementById(
        "isago-cookie-consent"
    );

    if (!root) {
        return;
    }

    const banner = root.querySelector(
        ".isago-cookie-banner"
    );

    const preferences = root.querySelector(
        ".isago-cookie-preferences"
    );

    const analytics = root.querySelector(
        "#isago-cookie-analytics"
    );

    const marketing = root.querySelector(
        "#isago-cookie-marketing"
    );

    const accept = root.querySelector(
        ".isago-cookie-accept"
    );

    const reject = root.querySelector(
        ".isago-cookie-reject"
    );

    const manage = root.querySelector(
        ".isago-cookie-manage"
    );

    const save = root.querySelector(
        ".isago-cookie-save"
    );

    const close = root.querySelector(
        ".isago-cookie-close"
    );


    const cookieName =
        "isago_cookie_consent";


    const cookieDays =
        window.isagoCookie &&
        Number(window.isagoCookie.days)
            ? Number(window.isagoCookie.days)
            : 180;


    function getCookie(name) {

        const cookies =
            document.cookie.split(";");

        for (let cookie of cookies) {

            cookie = cookie.trim();

            if (
                cookie.indexOf(name + "=") === 0
            ) {

                return decodeURIComponent(
                    cookie.substring(
                        name.length + 1
                    )
                );

            }

        }

        return null;

    }


    function setCookie(value) {

        const expires =
            new Date();

        expires.setTime(
            expires.getTime() +
            cookieDays *
            24 *
            60 *
            60 *
            1000
        );

        document.cookie =
            cookieName +
            "=" +
            encodeURIComponent(
                JSON.stringify(value)
            ) +
            ";expires=" +
            expires.toUTCString() +
            ";path=/;SameSite=Lax";

    }


    function getConsent() {

        const value =
            getCookie(cookieName);

        if (!value) {
            return null;
        }

        try {

            return JSON.parse(value);

        } catch (error) {

            return null;

        }

    }


    function hideBanner() {

        root.hidden = true;

        root.setAttribute(
            "aria-hidden",
            "true"
        );

        document.body.classList.remove(
            "isago-cookie-open"
        );

    }


    function showBanner() {

        root.hidden = false;

        root.setAttribute(
            "aria-hidden",
            "false"
        );

        banner.hidden = false;

    }


    function showPreferences() {

        root.hidden = false;

        banner.hidden = true;

        preferences.hidden = false;

        document.body.classList.add(
            "isago-cookie-open"
        );

        const consent =
            getConsent();

        if (consent) {

            analytics.checked =
                !!consent.analytics;

            marketing.checked =
                !!consent.marketing;

        } else {

            analytics.checked = false;

            marketing.checked = false;

        }

        setTimeout(function () {

            if (close) {
                close.focus();
            }

        }, 50);

    }


    function closePreferences() {

        preferences.hidden = true;

        banner.hidden = false;

        document.body.classList.remove(
            "isago-cookie-open"
        );

    }


    function saveConsent(
        analyticsAllowed,
        marketingAllowed
    ) {

        const consent = {

            necessary: true,

            analytics:
                !!analyticsAllowed,

            marketing:
                !!marketingAllowed,

            timestamp:
                Date.now()

        };

        setCookie(consent);

        hideBanner();

        /*
        ----------------------------------------------
        Notify other Isago scripts.
        ----------------------------------------------
        */

        document.dispatchEvent(

            new CustomEvent(
                "isagoCookieConsent",
                {
                    detail: consent
                }
            )

        );

    }


    /*
    --------------------------------------------------
    ACCEPT ALL
    --------------------------------------------------
    */

    if (accept) {

        accept.addEventListener(
            "click",
            function () {

                saveConsent(
                    true,
                    true
                );

            }
        );

    }


    /*
    --------------------------------------------------
    REJECT NON-ESSENTIAL
    --------------------------------------------------
    */

    if (reject) {

        reject.addEventListener(
            "click",
            function () {

                saveConsent(
                    false,
                    false
                );

            }
        );

    }


    /*
    --------------------------------------------------
    MANAGE
    --------------------------------------------------
    */

    if (manage) {

        manage.addEventListener(
            "click",
            function () {

                showPreferences();

            }
        );

    }


    /*
    --------------------------------------------------
    SAVE PREFERENCES
    --------------------------------------------------
    */

    if (save) {

        save.addEventListener(
            "click",
            function () {

                saveConsent(
                    analytics.checked,
                    marketing.checked
                );

            }
        );

    }


    /*
    --------------------------------------------------
    CLOSE
    --------------------------------------------------
    */

    if (close) {

        close.addEventListener(
            "click",
            function () {

                closePreferences();

            }
        );

    }


    /*
    --------------------------------------------------
    ESCAPE
    --------------------------------------------------
    */

    document.addEventListener(
        "keydown",
        function (event) {

            if (
                event.key === "Escape" &&
                !preferences.hidden
            ) {

                closePreferences();

            }

        }
    );


    /*
    --------------------------------------------------
    INITIAL STATE
    --------------------------------------------------
    */

    const existingConsent =
        getConsent();

    if (!existingConsent) {

        showBanner();

    } else {

        hideBanner();

        /*
        ----------------------------------------------
        Let other scripts know the current state.
        ----------------------------------------------
        */

        document.dispatchEvent(

            new CustomEvent(
                "isagoCookieConsent",
                {
                    detail: existingConsent
                }
            )

        );

    }


    /*
    --------------------------------------------------
    PUBLIC API
    --------------------------------------------------
    */

    window.isagoCookie = {

        hasConsent: function (
            category
        ) {

            if (
                category ===
                "necessary"
            ) {

                return true;

            }

            const consent =
                getConsent();

            return !!(
                consent &&
                consent[category]
            );

        },

        openPreferences:
            showPreferences,

        getConsent:
            getConsent

    };

});