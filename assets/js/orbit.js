/* =========================================================
   ISAGO ORBIT HERO
   Smooth interactive 3D movement
   ========================================================= */

document.addEventListener(
    'DOMContentLoaded',
    function () {


        const orbitScenes =
            document.querySelectorAll(
                '[data-isago-orbit-scene]'
            );


        if (
            !orbitScenes.length
        ) {

            return;

        }


        orbitScenes.forEach(
            function (scene) {


                const orbit =
                    scene.closest(
                        '[data-isago-orbit]'
                    );


                const system =
                    scene.querySelector(
                        '[data-isago-orbit-system]'
                    );


                if (
                    !orbit ||
                    !system
                ) {

                    return;

                }


                /* =================================================
                   SETTINGS
                   ================================================= */

                const DEFAULT_X = 10;

                const DEFAULT_Y = -12;


                const MAX_X = 32;

                const MIN_X = -32;


                const HOVER_X = 7;

                const HOVER_Y = 10;


                const DRAG_X = 0.25;

                const DRAG_Y = 0.20;


                /*
                 * Higher = faster response.
                 *
                 * 10–14 gives a smooth but
                 * responsive result.
                 */

                const SMOOTHING = 13;


                /* =================================================
                   STATE
                   ================================================= */

                let currentX =
                    DEFAULT_X;

                let currentY =
                    DEFAULT_Y;


                let targetX =
                    DEFAULT_X;

                let targetY =
                    DEFAULT_Y;


                /* =================================================
                   POINTER STATE
                   ================================================= */

                let isDragging =
                    false;


                let activePointerId =
                    null;


                let pointerStartX =
                    0;

                let pointerStartY =
                    0;


                let rotationStartX =
                    DEFAULT_X;

                let rotationStartY =
                    DEFAULT_Y;


                /* =================================================
                   RAF
                   ================================================= */

                let animationFrame =
                    null;


                let previousTime =
                    performance.now();


                /* =================================================
                   RENDER
                   ================================================= */

                function render(
                    timestamp
                ) {


                    /*
                     * Calculate elapsed time.
                     *
                     * This makes smoothing
                     * independent of FPS.
                     */

                    const delta =
                        Math.min(
                            timestamp -
                            previousTime,
                            50
                        );


                    previousTime =
                        timestamp;


                    /*
                     * Frame-rate independent
                     * exponential smoothing.
                     */

                    const easing =
                        1 -
                        Math.exp(
                            -SMOOTHING *
                            delta /
                            1000
                        );


                    currentX +=
                        (
                            targetX -
                            currentX
                        ) *
                        easing;


                    currentY +=
                        (
                            targetY -
                            currentY
                        ) *
                        easing;


                    /*
                     * Stop tiny floating-point
                     * movements.
                     */

                    if (
                        Math.abs(
                            targetX -
                            currentX
                        ) < 0.001
                    ) {

                        currentX =
                            targetX;

                    }


                    if (
                        Math.abs(
                            targetY -
                            currentY
                        ) < 0.001
                    ) {

                        currentY =
                            targetY;

                    }


                    /*
                     * CSS variables only.
                     *
                     * CSS owns transform.
                     */

                    orbit.style.setProperty(
                        '--isago-orbit-x',
                        currentX + 'deg'
                    );


                    orbit.style.setProperty(
                        '--isago-orbit-y',
                        currentY + 'deg'
                    );


                    animationFrame =
                        requestAnimationFrame(
                            render
                        );

                }


                /* =================================================
                   INITIAL STATE
                   ================================================= */

                orbit.style.setProperty(
                    '--isago-orbit-x',
                    DEFAULT_X + 'deg'
                );


                orbit.style.setProperty(
                    '--isago-orbit-y',
                    DEFAULT_Y + 'deg'
                );


                animationFrame =
                    requestAnimationFrame(
                        render
                    );


                /* =================================================
                   POINTER DOWN
                   ================================================= */

                scene.addEventListener(
                    'pointerdown',
                    function (event) {


                        /*
                         * Ignore secondary
                         * mouse buttons.
                         */

                        if (
                            event.pointerType ===
                            'mouse' &&
                            event.button !== 0
                        ) {

                            return;

                        }


                        /*
                         * Ignore secondary
                         * pointers.
                         */

                        if (
                            event.isPrimary ===
                            false
                        ) {

                            return;

                        }


                        isDragging =
                            true;


                        activePointerId =
                            event.pointerId;


                        scene.classList.add(
                            'is-dragging'
                        );


                        /*
                         * VERY IMPORTANT:
                         *
                         * Start from current
                         * displayed rotation.
                         */

                        rotationStartX =
                            currentX;


                        rotationStartY =
                            currentY;


                        pointerStartX =
                            event.clientX;


                        pointerStartY =
                            event.clientY;


                        /*
                         * Stop browser scrolling
                         * / selection.
                         */

                        event.preventDefault();


                        try {

                            scene.setPointerCapture(
                                event.pointerId
                            );

                        } catch (error) {

                            /*
                             * Safe fallback.
                             */

                        }

                    }
                );


                /* =================================================
                   POINTER MOVE
                   ================================================= */

                scene.addEventListener(
                    'pointermove',
                    function (event) {


                        /*
                         * Ignore another pointer.
                         */

                        if (
                            activePointerId !==
                            null &&
                            event.pointerId !==
                            activePointerId
                        ) {

                            return;

                        }


                        /* =========================================
                           DRAG
                           ========================================= */

                        if (
                            isDragging
                        ) {


                            const deltaX =
                                event.clientX -
                                pointerStartX;


                            const deltaY =
                                event.clientY -
                                pointerStartY;


                            targetY =
                                rotationStartY +
                                (
                                    deltaX *
                                    DRAG_X
                                );


                            targetX =
                                rotationStartX -
                                (
                                    deltaY *
                                    DRAG_Y
                                );


                            /*
                             * Limit vertical
                             * rotation only.
                             *
                             * Horizontal rotation
                             * can continue freely.
                             */

                            targetX =
                                Math.max(
                                    MIN_X,
                                    Math.min(
                                        MAX_X,
                                        targetX
                                    )
                                );


                            return;

                        }


                        /* =========================================
                           HOVER PARALLAX
                           ========================================= */

                        const rect =
                            scene.getBoundingClientRect();


                        if (
                            rect.width <= 0 ||
                            rect.height <= 0
                        ) {

                            return;

                        }


                        const mouseX =
                            (
                                event.clientX -
                                rect.left
                            ) /
                            rect.width;


                        const mouseY =
                            (
                                event.clientY -
                                rect.top
                            ) /
                            rect.height;


                        targetY =
                            DEFAULT_Y +
                            (
                                mouseX -
                                0.5
                            ) *
                            HOVER_Y;


                        targetX =
                            DEFAULT_X -
                            (
                                mouseY -
                                0.5
                            ) *
                            HOVER_X;

                    }
                );


                /* =================================================
                   POINTER UP
                   ================================================= */

                function finishPointer(
                    event
                ) {


                    if (
                        !isDragging
                    ) {

                        return;

                    }


                    isDragging =
                        false;


                    scene.classList.remove(
                        'is-dragging'
                    );


                    activePointerId =
                        null;


                    /*
                     * Keep the orbit where
                     * the user released it.
                     *
                     * Do not snap.
                     */

                    if (
                        event &&
                        scene.hasPointerCapture &&
                        scene.hasPointerCapture(
                            event.pointerId
                        )
                    ) {

                        try {

                            scene.releasePointerCapture(
                                event.pointerId
                            );

                        } catch (error) {

                            /*
                             * Nothing required.
                             */

                        }

                    }

                }


                scene.addEventListener(
                    'pointerup',
                    finishPointer
                );


                scene.addEventListener(
                    'pointercancel',
                    finishPointer
                );


                /* =================================================
                   LOST POINTER
                   ================================================= */

                scene.addEventListener(
                    'lostpointercapture',
                    function () {


                        isDragging =
                            false;


                        activePointerId =
                            null;


                        scene.classList.remove(
                            'is-dragging'
                        );

                    }
                );


                /* =================================================
                   POINTER LEAVE
                   ================================================= */

                scene.addEventListener(
                    'pointerleave',
                    function () {


                        if (
                            isDragging
                        ) {

                            return;

                        }


                        /*
                         * Smoothly return to
                         * neutral position.
                         */

                        targetX =
                            DEFAULT_X;

                        targetY =
                            DEFAULT_Y;

                    }
                );


                /* =================================================
                   PAGE HIDDEN
                   ================================================= */

                document.addEventListener(
                    'visibilitychange',
                    function () {


                        if (
                            document.hidden
                        ) {

                            if (
                                animationFrame
                            ) {

                                cancelAnimationFrame(
                                    animationFrame
                                );

                                animationFrame =
                                    null;

                            }


                            return;

                        }


                        if (
                            !animationFrame
                        ) {

                            previousTime =
                                performance.now();


                            animationFrame =
                                requestAnimationFrame(
                                    render
                                );

                        }

                    }
                );


            }
        );

    }
);