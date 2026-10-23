document.addEventListener(
    "DOMContentLoaded",
    function () {

        "use strict";


        console.log(
            "Isago Video Lightbox Ready"
        );


        /*
        |--------------------------------------------------------------------------
        | ELEMENTS
        |--------------------------------------------------------------------------
        */

        const modal =
            document.getElementById(
                "isago-video-lightbox"
            );


        const player =
            document.getElementById(
                "isago-video-player"
            );


        const close =
            document.querySelector(
                ".isago-video-close"
            );


        /*
        |--------------------------------------------------------------------------
        | VALIDATE
        |--------------------------------------------------------------------------
        */

        if (
            !modal ||
            !player ||
            !close
        ) {

            console.error(
                "Isago: Video lightbox elements are missing."
            );

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | YOUTUBE ID
        |--------------------------------------------------------------------------
        */

        function getYouTubeId(
            url
        ) {

            if (!url) {

                return "";

            }


            const match =
                url.match(
                    /(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/|live\/)|youtu\.be\/)([^?&#/]+)/i
                );


            return match
                ? match[1]
                : "";

        }


        /*
        |--------------------------------------------------------------------------
        | INSTAGRAM DATA
        |--------------------------------------------------------------------------
        */

        function getInstagramData(
            url
        ) {

            if (!url) {

                return null;

            }


            const match =
                url.match(
                    /(?:instagram\.com|instagr\.am)\/(p|reel|tv)\/([^/?#]+)/i
                );


            if (!match) {

                return null;

            }


            return {

                type:
                    match[1].toLowerCase(),

                id:
                    match[2]

            };

        }


        /*
        |--------------------------------------------------------------------------
        | PINTEREST
        |--------------------------------------------------------------------------
        */

        function isPinterestUrl(
            url
        ) {

            if (!url) {

                return false;

            }


            return /(?:^|\/\/)(?:www\.)?pinterest\.[^/]+\/pin\//i.test(
                url
            );

        }


        /*
        |--------------------------------------------------------------------------
        | TIKTOK ID
        |--------------------------------------------------------------------------
        */

        function getTikTokId(
            url
        ) {

            if (!url) {

                return "";

            }


            const normalMatch =
                url.match(
                    /tiktok\.com\/(?:@[^/]+\/)?video\/(\d+)/i
                );


            if (
                normalMatch
                &&
                normalMatch[1]
            ) {

                return normalMatch[1];

            }


            const playerMatch =
                url.match(
                    /tiktok\.com\/player\/v1\/(\d+)/i
                );


            if (
                playerMatch
                &&
                playerMatch[1]
            ) {

                return playerMatch[1];

            }


            const embedMatch =
                url.match(
                    /tiktok\.com\/embed\/v2\/(\d+)/i
                );


            if (
                embedMatch
                &&
                embedMatch[1]
            ) {

                return embedMatch[1];

            }


            return "";

        }


        /*
        |--------------------------------------------------------------------------
        | VIMEO ID
        |--------------------------------------------------------------------------
        */

        function getVimeoId(
            url
        ) {

            if (!url) {

                return "";

            }


            const match =
                url.match(
                    /(?:vimeo\.com\/)(?:.*\/)?(\d+)(?:[/?#]|$)/i
                );


            return match
                ? match[1]
                : "";

        }


        /*
        |--------------------------------------------------------------------------
        | URL OBJECT
        |--------------------------------------------------------------------------
        */

        function getUrlObject(
            url
        ) {

            try {

                return new URL(
                    url,
                    window.location.href
                );

            } catch (
                error
            ) {

                return null;

            }

        }


        /*
        |--------------------------------------------------------------------------
        | DIRECT VIDEO
        |--------------------------------------------------------------------------
        */

        function isDirectVideo(
            url
        ) {

            if (!url) {

                return false;

            }


            const urlObject =
                getUrlObject(
                    url
                );


            if (!urlObject) {

                return /\.(mp4|webm|ogg|m4v)(?:[?#].*)?$/i.test(
                    url
                );

            }


            const pathname =
                urlObject.pathname.toLowerCase();


            /*
             * Standard direct video files.
             */

            if (
                /\.(mp4|webm|ogg|m4v)$/.test(
                    pathname
                )
            ) {

                return true;

            }


            /*
             * ImageKit.
             */

            if (
                /(?:^|\.)imagekit\.io$/i.test(
                    urlObject.hostname
                )
            ) {

                return true;

            }


            return false;

        }


        /*
        |--------------------------------------------------------------------------
        | CREATE IFRAME
        |--------------------------------------------------------------------------
        */

        function createIframe(
            src,
            className
        ) {

            const iframe =
                document.createElement(
                    "iframe"
                );


            iframe.className =
                className;


            iframe.src =
                src;


            iframe.allow =
                "autoplay; fullscreen; picture-in-picture; encrypted-media";


            iframe.allowFullscreen =
                true;


            iframe.frameBorder =
                "0";


            iframe.scrolling =
                "no";


            iframe.loading =
                "eager";


            iframe.referrerPolicy =
                "strict-origin-when-cross-origin";


            iframe.setAttribute(
                "title",
                "Isago Video Player"
            );


            return iframe;

        }


        /*
        |--------------------------------------------------------------------------
        | MODAL MODE
        |--------------------------------------------------------------------------
        */

        function setModalMode(
            mode
        ) {

            modal.classList.remove(
                "isago-video-mode-standard",
                "isago-video-mode-instagram",
                "isago-video-mode-pinterest"
            );


            if (
                mode ===
                "instagram"
            ) {

                modal.classList.add(
                    "isago-video-mode-instagram"
                );

            }

            else if (
                mode ===
                "pinterest"
            ) {

                modal.classList.add(
                    "isago-video-mode-pinterest"
                );

            }

            else {

                modal.classList.add(
                    "isago-video-mode-standard"
                );

            }

        }


        /*
        |--------------------------------------------------------------------------
        | SHOW MODAL
        |--------------------------------------------------------------------------
        */

        function showModal(
            mode = "standard"
        ) {

            setModalMode(
                mode
            );


            modal.classList.add(
                "active"
            );


            document.body.classList.add(
                "isago-video-open"
            );

        }


        /*
        |--------------------------------------------------------------------------
        | VIMEO SDK
        |--------------------------------------------------------------------------
        */

        function loadVimeoSDK(
            callback
        ) {

            if (
                window.Vimeo
                &&
                window.Vimeo.Player
            ) {

                callback();

                return;

            }


            const existing =
                document.querySelector(
                    'script[data-isago-vimeo-sdk="1"]'
                );


            if (
                existing
            ) {

                existing.addEventListener(
                    "load",
                    callback,
                    {
                        once: true
                    }
                );

                return;

            }


            const script =
                document.createElement(
                    "script"
                );


            script.src =
                "https://player.vimeo.com/api/player.js";


            script.async =
                true;


            script.setAttribute(
                "data-isago-vimeo-sdk",
                "1"
            );


            script.addEventListener(
                "load",
                callback,
                {
                    once: true
                }
            );


            document.head.appendChild(
                script
            );

        }


        /*
        |--------------------------------------------------------------------------
        | PINTEREST SCRIPT
        |--------------------------------------------------------------------------
        */

        function loadPinterest() {

            const existingScript =
                document.querySelector(
                    'script[data-isago-pinterest="1"]'
                );


            if (
                existingScript
            ) {

                if (
                    window.PinUtils
                    &&
                    typeof window.PinUtils.build ===
                        "function"
                ) {

                    window.PinUtils.build();

                }

                return;

            }


            const script =
                document.createElement(
                    "script"
                );


            script.src =
                "https://assets.pinterest.com/js/pinit.js";


            script.async =
                true;


            script.defer =
                true;


            script.setAttribute(
                "data-isago-pinterest",
                "1"
            );


            script.onload =
                function () {

                    if (
                        window.PinUtils
                        &&
                        typeof window.PinUtils.build ===
                            "function"
                    ) {

                        window.PinUtils.build();

                    }

                };


            document.body.appendChild(
                script
            );

        }


        /*
        |--------------------------------------------------------------------------
        | OPEN LIGHTBOX
        |--------------------------------------------------------------------------
        */

        function openLightbox(
            url
        ) {

            if (!url) {

                console.error(
                    "Isago: No video URL provided."
                );

                return;

            }


            player.innerHTML =
                "";


            url =
                String(
                    url
                ).trim();


            console.log(
                "Isago lightbox opening:",
                url
            );


            /*
            |--------------------------------------------------------------------------
            | 1. DIRECT / IMAGEKIT
            |--------------------------------------------------------------------------
            */

            if (
                isDirectVideo(
                    url
                )
            ) {

                const video =
                    document.createElement(
                        "video"
                    );


                video.className =
                    "isago-lightbox-video";


                video.controls =
                    true;


                video.autoplay =
                    true;


                video.playsInline =
                    true;


                video.preload =
                    "auto";


                video.setAttribute(
                    "autoplay",
                    ""
                );


                video.setAttribute(
                    "playsinline",
                    ""
                );


                video.setAttribute(
                    "webkit-playsinline",
                    ""
                );


                video.src =
                    url;


                video.muted =
                    false;


                player.appendChild(
                    video
                );


                showModal(
                    "standard"
                );


                const playPromise =
                    video.play();


                if (
                    playPromise
                    &&
                    typeof playPromise.catch ===
                        "function"
                ) {

                    playPromise.catch(
                        function () {

                            video.muted =
                                true;


                            video.play().catch(
                                function () {}
                            );

                        }
                    );

                }


                return;

            }


            /*
            |--------------------------------------------------------------------------
            | 2. YOUTUBE
            |--------------------------------------------------------------------------
            */

            const youtubeId =
                getYouTubeId(
                    url
                );


            if (
                youtubeId
            ) {

                const youtubeUrl =
                    "https://www.youtube.com/embed/"
                    +
                    encodeURIComponent(
                        youtubeId
                    )
                    +
                    "?autoplay=1"
                    +
                    "&mute=1"
                    +
                    "&playsinline=1"
                    +
                    "&controls=1"
                    +
                    "&rel=0"
                    +
                    "&enablejsapi=1"
                    +
                    "&origin="
                    +
                    encodeURIComponent(
                        window.location.origin
                    );


                const iframe =
                    createIframe(
                        youtubeUrl,
                        "isago-lightbox-youtube"
                    );


                player.appendChild(
                    iframe
                );


                showModal(
                    "standard"
                );


                return;

            }


            /*
            |--------------------------------------------------------------------------
            | 3. INSTAGRAM
            |--------------------------------------------------------------------------
            */

            const instagram =
                getInstagramData(
                    url
                );


            if (
                instagram
            ) {

                const wrapper =
                    document.createElement(
                        "div"
                    );


                wrapper.className =
                    "isago-lightbox-instagram-wrapper";


                const iframe =
                    createIframe(
                        "https://www.instagram.com/"
                        +
                        instagram.type
                        +
                        "/"
                        +
                        encodeURIComponent(
                            instagram.id
                        )
                        +
                        "/embed/",
                        "isago-lightbox-instagram"
                    );


                wrapper.appendChild(
                    iframe
                );


                player.appendChild(
                    wrapper
                );


                showModal(
                    "instagram"
                );


                return;

            }


            /*
            |--------------------------------------------------------------------------
            | 4. PINTEREST
            |--------------------------------------------------------------------------
            */

            if (
                isPinterestUrl(
                    url
                )
            ) {

                const wrapper =
                    document.createElement(
                        "div"
                    );


                wrapper.className =
                    "isago-lightbox-pinterest-wrapper";


                const pin =
                    document.createElement(
                        "a"
                    );


                pin.href =
                    url;


                pin.setAttribute(
                    "data-pin-do",
                    "embedPin"
                );


                pin.className =
                    "isago-lightbox-pinterest";


                wrapper.appendChild(
                    pin
                );


                player.appendChild(
                    wrapper
                );


                showModal(
                    "pinterest"
                );


                loadPinterest();


                return;

            }


            /*
            |--------------------------------------------------------------------------
            | 5. TIKTOK
            |--------------------------------------------------------------------------
            */

            const tikTokId =
                getTikTokId(
                    url
                );


            if (
                tikTokId
            ) {

                const tikTokUrl =
                    "https://www.tiktok.com/player/v1/"
                    +
                    encodeURIComponent(
                        tikTokId
                    )
                    +
                    "?autoplay=1"
                    +
                    "&muted=1"
                    +
                    "&controls=1"
                    +
                    "&play_button=1"
                    +
                    "&progress_bar=1"
                    +
                    "&volume_control=1"
                    +
                    "&fullscreen_button=1"
                    +
                    "&description=0"
                    +
                    "&music_info=0";


                const iframe =
                    createIframe(
                        tikTokUrl,
                        "isago-lightbox-tiktok"
                    );


                player.appendChild(
                    iframe
                );


                showModal(
                    "standard"
                );


                iframe.addEventListener(
                    "load",
                    function () {

                        try {

                            iframe.contentWindow.postMessage(
                                {
                                    type:
                                        "play",

                                    value:
                                        undefined,

                                    "x-tiktok-player":
                                        true

                                },
                                "*"
                            );

                        } catch (
                            error
                        ) {}

                    }
                );


                return;

            }


            /*
            |--------------------------------------------------------------------------
            | 6. VIMEO
            |--------------------------------------------------------------------------
            */

            const vimeoId =
                getVimeoId(
                    url
                );


            if (
                vimeoId
            ) {

                const vimeoUrl =
                    "https://player.vimeo.com/video/"
                    +
                    encodeURIComponent(
                        vimeoId
                    )
                    +
                    "?autoplay=1"
                    +
                    "&muted=1"
                    +
                    "&playsinline=1"
                    +
                    "&controls=1"
                    +
                    "&title=0"
                    +
                    "&byline=0"
                    +
                    "&portrait=0";


                const iframe =
                    createIframe(
                        vimeoUrl,
                        "isago-lightbox-vimeo"
                    );


                player.appendChild(
                    iframe
                );


                showModal(
                    "standard"
                );


                loadVimeoSDK(
                    function () {

                        if (
                            !window.Vimeo
                            ||
                            !window.Vimeo.Player
                        ) {

                            return;

                        }


                        try {

                            const vimeoPlayer =
                                new Vimeo.Player(
                                    iframe
                                );


                            vimeoPlayer.setVolume(
                                0
                            ).catch(
                                function () {}
                            );


                            vimeoPlayer.play().catch(
                                function () {}
                            );

                        } catch (
                            error
                        ) {}

                    }
                );


                return;

            }


            /*
            |--------------------------------------------------------------------------
            | UNSUPPORTED
            |--------------------------------------------------------------------------
            */

            console.error(
                "Isago: Unsupported video URL:",
                url
            );

        }


        /*
        |--------------------------------------------------------------------------
        | CLOSE
        |--------------------------------------------------------------------------
        */

        function closeModal() {

            modal.classList.remove(
                "active",
                "isago-video-mode-standard",
                "isago-video-mode-instagram",
                "isago-video-mode-pinterest"
            );


            document.body.classList.remove(
                "isago-video-open"
            );


            player.innerHTML =
                "";

        }


        /*
        |--------------------------------------------------------------------------
        | PLAY BUTTONS
        |--------------------------------------------------------------------------
        */

        document.addEventListener(
            "click",
            function (
                event
            ) {

                const button =
                    event.target.closest(
                        ".homepage-video-play"
                    );


                if (!button) {

                    return;

                }


                event.preventDefault();

                event.stopPropagation();


                const url =
                    button.getAttribute(
                        "data-video"
                    );


                if (!url) {

                    return;

                }


                openLightbox(
                    url
                );

            }
        );


        /*
        |--------------------------------------------------------------------------
        | CLOSE BUTTON
        |--------------------------------------------------------------------------
        */

        close.addEventListener(
            "click",
            function (
                event
            ) {

                event.preventDefault();

                closeModal();

            }
        );


        /*
        |--------------------------------------------------------------------------
        | BACKDROP
        |--------------------------------------------------------------------------
        */

        const backdrop =
            modal.querySelector(
                ".isago-video-backdrop"
            );


        if (
            backdrop
        ) {

            backdrop.addEventListener(
                "click",
                function () {

                    closeModal();

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | ESC
        |--------------------------------------------------------------------------
        */

        document.addEventListener(
            "keydown",
            function (
                event
            ) {

                if (
                    event.key === "Escape"
                    &&
                    modal.classList.contains(
                        "active"
                    )
                ) {

                    closeModal();

                }

            }
        );

    }
);