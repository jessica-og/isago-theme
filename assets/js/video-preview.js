document.addEventListener(
    "DOMContentLoaded",
    function () {

        "use strict";


        /*
        |--------------------------------------------------------------------------
        | SETTINGS
        |--------------------------------------------------------------------------
        */

        const ROOT_MARGIN =
            "300px 0px";


        /*
        |--------------------------------------------------------------------------
        | PREVIEW DURATION
        |--------------------------------------------------------------------------
        |
        | Uploaded/direct videos are previewed for only
        | the first 10 seconds.
        |
        */

        const PREVIEW_DURATION =
            10;


        /*
        |--------------------------------------------------------------------------
        | UPLOADED / DIRECT VIDEOS ONLY
        |--------------------------------------------------------------------------
        |
        | Only elements with the Isago native video preview
        | class are handled here.
        |
        | Social URLs such as:
        |
        | - YouTube
        | - TikTok
        | - Vimeo
        |
        | are NOT handled by this script.
        |
        */

        const videos =
            document.querySelectorAll(
                ".isago-video-preview-thumbnail"
            );


        /*
        |--------------------------------------------------------------------------
        | PREPARE HTML5 VIDEO
        |--------------------------------------------------------------------------
        */

        function prepareVideo(
            video
        ) {

            video.muted =
                true;


            video.setAttribute(
                "muted",
                ""
            );


            video.playsInline =
                true;


            video.setAttribute(
                "playsinline",
                ""
            );


            video.setAttribute(
                "webkit-playsinline",
                ""
            );


            /*
             * Metadata is enough for the browser to
             * prepare the video without downloading
             * the entire file immediately.
             */

            video.preload =
                "metadata";


            /*
             * We control the 10-second preview manually.
             *
             * Do NOT use the native loop attribute because
             * the preview must stop at 10 seconds first.
             */

            video.loop =
                false;


            video.removeAttribute(
                "loop"
            );

        }


        /*
        |--------------------------------------------------------------------------
        | CHECK FOR UPLOADED VIDEO SOURCE
        |--------------------------------------------------------------------------
        */

        function hasUploadedVideoSource(
            video
        ) {

            if (!video) {

                return false;

            }


            const source =
                video.dataset.videoSrc;


            if (
                !source ||
                typeof source !== "string"
            ) {

                return false;

            }


            return source.trim() !== "";

        }


        /*
        |--------------------------------------------------------------------------
        | LOAD HTML5 VIDEO
        |--------------------------------------------------------------------------
        */

        function loadVideo(
            video
        ) {

            if (!video) {

                return false;

            }


            /*
             * Never load anything that does not have
             * an uploaded/direct video source.
             */

            if (
                !hasUploadedVideoSource(
                    video
                )
            ) {

                return false;

            }


            /*
             * Do not load the same video more than once.
             */

            if (
                video.dataset.loaded ===
                "true"
            ) {

                return true;

            }


            const source =
                video.dataset.videoSrc;


            video.src =
                source;


            video.dataset.loaded =
                "true";


            video.load();


            return true;

        }


        /*
        |--------------------------------------------------------------------------
        | RESET PREVIEW
        |--------------------------------------------------------------------------
        */

        function resetPreview(
            video
        ) {

            if (!video) {

                return;

            }


            try {

                video.pause();


                video.currentTime =
                    0;

            } catch (
                error
            ) {

                /*
                 * Ignore browsers that do not allow
                 * currentTime to be changed yet.
                 */

            }

        }


        /*
        |--------------------------------------------------------------------------
        | PLAY UPLOADED VIDEO PREVIEW
        |--------------------------------------------------------------------------
        */

        function playPreview(
            video
        ) {

            if (!video) {

                return;

            }


            /*
             * Safety check:
             *
             * No source = no preview.
             */

            if (
                !hasUploadedVideoSource(
                    video
                )
            ) {

                return;

            }


            prepareVideo(
                video
            );


            const loaded =
                loadVideo(
                    video
                );


            if (!loaded) {

                return;

            }


            /*
             * Make sure the browser can autoplay
             * the muted preview.
             */

            video.muted =
                true;


            video.playsInline =
                true;


            /*
             * Start from the beginning whenever
             * the preview becomes visible again.
             */

            try {

                video.currentTime =
                    0;

            } catch (
                error
            ) {

                /*
                 * Ignore browsers where the video
                 * metadata is not ready yet.
                 */

            }


            /*
             * Mark this preview as active.
             */

            video.dataset.previewPlaying =
                "true";


            const playPromise =
                video.play();


            if (
                playPromise &&
                typeof playPromise.catch ===
                    "function"
            ) {

                playPromise.catch(
                    function () {

                        /*
                         * Autoplay can still be blocked
                         * by some browsers.
                         */

                    }
                );

            }

        }


        /*
        |--------------------------------------------------------------------------
        | STOP UPLOADED VIDEO PREVIEW
        |--------------------------------------------------------------------------
        */

        function stopPreview(
            video
        ) {

            if (!video) {

                return;

            }


            video.dataset.previewPlaying =
                "false";


            resetPreview(
                video
            );

        }


        /*
        |--------------------------------------------------------------------------
        | LIMIT VIDEO TO FIRST 10 SECONDS
        |--------------------------------------------------------------------------
        |
        | When the video reaches 10 seconds:
        |
        | 1. Pause it.
        | 2. Return it to 0 seconds.
        | 3. Play it again.
        |
        | This creates a continuous 10-second preview loop.
        |
        */

        function handlePreviewTimeUpdate(
            video
        ) {

            if (!video) {

                return;

            }


            /*
             * Only control the video while its preview
             * is active.
             */

            if (
                video.dataset.previewPlaying !==
                "true"
            ) {

                return;

            }


            /*
             * Do not attempt to control the video
             * before its duration is available.
             */

            if (
                !Number.isFinite(
                    video.duration
                )
            ) {

                return;

            }


            /*
             * If the actual video is shorter than
             * 10 seconds, let it reach its natural end.
             */

            if (
                video.duration <=
                PREVIEW_DURATION
            ) {

                return;

            }


            /*
             * Stop exactly at the 10-second mark.
             */

            if (
                video.currentTime >=
                PREVIEW_DURATION
            ) {

                video.pause();


                try {

                    video.currentTime =
                        0;

                } catch (
                    error
                ) {

                    return;

                }


                /*
                 * Continue looping the first 10 seconds
                 * while the video remains visible.
                 */

                if (
                    video.dataset.previewPlaying ===
                    "true"
                ) {

                    const playPromise =
                        video.play();


                    if (
                        playPromise &&
                        typeof playPromise.catch ===
                            "function"
                    ) {

                        playPromise.catch(
                            function () {}
                        );

                    }

                }

            }

        }


        /*
        |--------------------------------------------------------------------------
        | PREPARE ALL UPLOADED VIDEOS
        |--------------------------------------------------------------------------
        */

        videos.forEach(
            function (
                video
            ) {

                /*
                 * Ignore anything without an uploaded
                 * video source.
                 */

                if (
                    !hasUploadedVideoSource(
                        video
                    )
                ) {

                    return;

                }


                prepareVideo(
                    video
                );


                /*
                 * Monitor playback position.
                 */

                video.addEventListener(
                    "timeupdate",
                    function () {

                        handlePreviewTimeUpdate(
                            video
                        );

                    }
                );

            }
        );


        /*
        |--------------------------------------------------------------------------
        | NATIVE VIDEO OBSERVER
        |--------------------------------------------------------------------------
        |
        | Only uploaded/direct HTML5 videos are observed.
        |
        */

        if (
            "IntersectionObserver"
            in window
        ) {

            const videoObserver =
                new IntersectionObserver(
                    function (
                        entries
                    ) {

                        entries.forEach(
                            function (
                                entry
                            ) {

                                const video =
                                    entry.target;


                                /*
                                 * Extra safety check.
                                 */

                                if (
                                    !hasUploadedVideoSource(
                                        video
                                    )
                                ) {

                                    return;

                                }


                                if (
                                    entry.isIntersecting
                                ) {

                                    playPreview(
                                        video
                                    );

                                }

                                else {

                                    stopPreview(
                                        video
                                    );

                                }

                            }
                        );

                    },
                    {
                        rootMargin:
                            ROOT_MARGIN,

                        threshold:
                            0.10

                    }
                );


            /*
             * Observe uploaded videos only.
             */

            videos.forEach(
                function (
                    video
                ) {

                    if (
                        !hasUploadedVideoSource(
                            video
                        )
                    ) {

                        return;

                    }


                    videoObserver.observe(
                        video
                    );

                }
            );

        }

        else {

            /*
             * Fallback for browsers that do not
             * support IntersectionObserver.
             *
             * Only uploaded/direct videos are played.
             */

            videos.forEach(
                function (
                    video
                ) {

                    if (
                        !hasUploadedVideoSource(
                            video
                        )
                    ) {

                        return;

                    }


                    playPreview(
                        video
                    );

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | PAGE VISIBILITY
        |--------------------------------------------------------------------------
        |
        | Stop uploaded video previews when the user
        | leaves the browser tab.
        |
        */

        document.addEventListener(
            "visibilitychange",
            function () {

                if (
                    !document.hidden
                ) {

                    return;

                }


                videos.forEach(
                    function (
                        video
                    ) {

                        if (
                            !hasUploadedVideoSource(
                                video
                            )
                        ) {

                            return;

                        }


                        stopPreview(
                            video
                        );

                    }
                );

            }
        );

    }
);