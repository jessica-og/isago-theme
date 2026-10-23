(function () {

    'use strict';


    /*=========================================================
    CONFIGURATION
    =========================================================*/

    const MAX_CONCURRENT_CHECKS = 6;

    const FAILURE_RETRIES = 2;

    const RETRY_DELAY = 1500;

    const RESUME_POLL_DELAY = 3000;


    /*=========================================================
    INITIALIZE
    =========================================================*/

    function initBrokenSocialVideos() {

        const scanButton =
            document.getElementById(
                'isago-broken-video-scan-all'
            );


        const notice =
            document.getElementById(
                'isago-broken-video-notice'
            );


        const progress =
            document.getElementById(
                'isago-broken-video-progress'
            );


        /*
         * Only run this script on the Broken Social Videos page.
         */
        if (!scanButton) {

            return;

        }


        const config =
            window.isagoBrokenSocialVideos || {};


        let scanRunning =
            false;


        /*=====================================================
        NOTICE
        =====================================================*/

        function showNotice(
            message,
            type
        ) {

            if (!notice) {

                return;

            }


            const paragraph =
                notice.querySelector('p');


            notice.classList.remove(
                'notice-info',
                'notice-success',
                'notice-warning',
                'notice-error'
            );


            notice.classList.add(
                'notice-' +
                (
                    type ||
                    'info'
                )
            );


            if (paragraph) {

                paragraph.textContent =
                    message || '';

            }


            notice.style.display =
                'block';

        }


        function hideNotice() {

            if (notice) {

                notice.style.display =
                    'none';

            }

        }


        /*=====================================================
        AJAX REQUEST
        =====================================================*/

        function ajaxRequest(
            action,
            data
        ) {

            return new Promise(
                function (
                    resolve,
                    reject
                ) {

                    /*
                     * Make sure WordPress localized the
                     * AJAX configuration.
                     */
                    if (
                        !config.ajaxUrl
                        ||
                        !config.nonce
                    ) {

                        reject(
                            new Error(
                                'Broken Social Videos AJAX configuration is missing.'
                            )
                        );

                        return;

                    }


                    const body =
                        new URLSearchParams();


                    body.append(
                        'action',
                        action
                    );


                    body.append(
                        'nonce',
                        config.nonce
                    );


                    /*
                     * Add request data.
                     */
                    if (data) {

                        Object.keys(
                            data
                        ).forEach(
                            function (key) {

                                body.append(
                                    key,
                                    data[key]
                                );

                            }
                        );

                    }


                    fetch(
                        config.ajaxUrl,
                        {
                            method:
                                'POST',

                            credentials:
                                'same-origin',

                            headers:
                                {
                                    'Content-Type':
                                        'application/x-www-form-urlencoded; charset=UTF-8'
                                },

                            body:
                                body.toString()
                        }
                    )

                    .then(
                        function (response) {

                            return response
                                .text()
                                .then(
                                    function (text) {

                                        let json;


                                        try {

                                            json =
                                                JSON.parse(
                                                    text
                                                );

                                        } catch (
                                            error
                                        ) {

                                            throw new Error(
                                                text ||
                                                'The server returned an invalid AJAX response.'
                                            );

                                        }


                                        return {

                                            httpStatus:
                                                response.status,

                                            data:
                                                json

                                        };

                                    }
                                );

                        }
                    )

                    .then(
                        function (result) {

                            /*
                             * WordPress AJAX responses normally
                             * contain:
                             *
                             * success: true/false
                             *
                             * data: ...
                             */
                            if (
                                !result.data
                                ||
                                !result.data.success
                            ) {

                                const message =
                                    result.data
                                    &&
                                    result.data.data
                                    &&
                                    result.data.data.message

                                        ?

                                    result.data.data.message

                                        :

                                    'The server rejected the request.';


                                const error =
                                    new Error(
                                        message
                                    );


                                error.httpStatus =
                                    result.httpStatus;


                                throw error;

                            }


                            resolve(
                                result.data
                            );

                        }
                    )

                    .catch(
                        function (error) {

                            reject(
                                error
                            );

                        }
                    );

                }
            );

        }


        /*=====================================================
        SERVER-SIDE SCAN SESSION
        =====================================================*/

        function getSession() {

            return ajaxRequest(
                'isago_get_broken_social_video_scan_session',
                {}
            );

        }


        function getState() {

            return ajaxRequest(
                'isago_get_broken_social_video_scan_state',
                {}
            );

        }


        function startScan() {

            return ajaxRequest(
                'isago_start_broken_social_video_scan',
                {}
            );

        }


        function getNextBatch(
            token
        ) {

            return ajaxRequest(
                'isago_get_broken_social_video_scan_batch',
                {
                    scan_token:
                        token
                }
            );

        }


        /*=====================================================
        PROGRESS
        =====================================================*/

        function renderProgress(
            state,
            prefix
        ) {

            if (!progress) {

                return;

            }


            const completed =
                Number(
                    state.completed || 0
                );


            const total =
                Number(
                    state.total || 0
                );


            const remaining =
                Number(
                    state.remaining || 0
                );


            const percentage =
                Number(
                    state.percentage || 0
                );


            progress.textContent =
                (
                    prefix ||
                    'Scanning'
                ) +
                ': ' +
                completed +
                ' / ' +
                total +
                ' (' +
                percentage +
                '%)' +
                ' — ' +
                remaining +
                ' remaining';

        }


        /*=====================================================
        WAIT
        =====================================================*/

        function wait(
            milliseconds
        ) {

            return new Promise(
                function (
                    resolve
                ) {

                    window.setTimeout(
                        resolve,
                        milliseconds
                    );

                }
            );

        }


        /*=====================================================
        CHECK ONE POST
        =====================================================*/

        async function checkPost(
            postId,
            token
        ) {

            /*
             * Retry temporary failures.
             *
             * attempt 0 = first request
             * attempt 1 = retry
             * attempt 2 = final retry
             */
            for (
                let attempt = 0;
                attempt <= FAILURE_RETRIES;
                attempt++
            ) {

                try {

                    return await ajaxRequest(
                        'isago_check_broken_social_video',
                        {
                            post_id:
                                postId,

                            scan_token:
                                token
                        }
                    );

                } catch (
                    error
                ) {

                    /*
                     * Stop retrying after the configured
                     * number of attempts.
                     */
                    if (
                        attempt >=
                        FAILURE_RETRIES
                    ) {

                        /*
                         * Tell the server this item was not
                         * successfully completed.
                         *
                         * The server-side queue can then
                         * retry it later instead of losing it.
                         */
                        try {

                            await ajaxRequest(
                                'isago_mark_broken_social_video_scan_item',
                                {
                                    post_id:
                                        postId,

                                    scan_token:
                                        token
                                }
                            );

                        } catch (
                            ignore
                        ) {

                            /*
                             * Intentionally ignored.
                             *
                             * The queue remains server-side,
                             * so this item can be retried later.
                             */

                        }


                        throw error;

                    }


                    /*
                     * Small delay before retrying.
                     */
                    await wait(
                        RETRY_DELAY
                    );

                }

            }


            throw new Error(
                'The social-video URL could not be checked.'
            );

        }


        /*=====================================================
        PROCESS CURRENT BATCH
        =====================================================*/

        async function processBatch(
            postIds,
            token,
            state
        ) {

            let nextIndex =
                0;


            let active =
                0;


            let completed =
                0;


            let failed =
                0;


            return new Promise(
                function (
                    resolve
                ) {

                    function launchNext() {

                        /*
                         * Start new checks while we are below
                         * our concurrency limit.
                         */
                        while (
                            active <
                                MAX_CONCURRENT_CHECKS
                            &&
                            nextIndex <
                                postIds.length
                        ) {

                            const postId =
                                postIds[
                                    nextIndex
                                ];


                            nextIndex++;


                            active++;


                            /*
                             * Estimate the current progress
                             * from this browser batch.
                             */
                            const estimatedCompleted =
                                Math.min(
                                    Number(
                                        state.total || 0
                                    ),

                                    Number(
                                        state.completed || 0
                                    )
                                    +
                                    completed
                                    +
                                    1
                                );


                            const total =
                                Number(
                                    state.total || 0
                                );


                            const percentage =
                                total > 0

                                    ?

                                (
                                    estimatedCompleted /
                                    total
                                ) * 100

                                    :

                                100;


                            if (progress) {

                                progress.textContent =
                                    'Scanning: ' +
                                    estimatedCompleted +
                                    ' / ' +
                                    total +
                                    ' (' +
                                    percentage.toFixed(
                                        1
                                    ) +
                                    '%)';

                            }


                            /*
                             * Check this URL.
                             */
                            checkPost(
                                postId,
                                token
                            )

                            .catch(
                                function () {

                                    failed++;

                                }
                            )

                            .finally(
                                function () {

                                    active--;


                                    completed++;


                                    /*
                                     * Update progress.
                                     */
                                    const currentCompleted =
                                        Math.min(
                                            Number(
                                                state.total || 0
                                            ),

                                            Number(
                                                state.completed || 0
                                            )
                                            +
                                            completed
                                        );


                                    const currentTotal =
                                        Number(
                                            state.total || 0
                                        );


                                    const currentPercentage =
                                        currentTotal > 0

                                            ?

                                        (
                                            currentCompleted /
                                            currentTotal
                                        ) * 100

                                            :

                                        100;


                                    if (progress) {

                                        progress.textContent =
                                            'Scanning: ' +
                                            currentCompleted +
                                            ' / ' +
                                            currentTotal +
                                            ' (' +
                                            currentPercentage.toFixed(
                                                1
                                            ) +
                                            '%)';

                                    }


                                    /*
                                     * This batch is finished.
                                     */
                                    if (
                                        completed >=
                                        postIds.length
                                    ) {

                                        resolve(
                                            {
                                                failed:
                                                    failed
                                            }
                                        );

                                        return;

                                    }


                                    /*
                                     * Start the next URL.
                                     */
                                    launchNext();

                                }
                            );

                        }

                    }


                    launchNext();

                }
            );

        }


        /*=====================================================
        FINISH SCAN
        =====================================================*/

        function finishScan(
            state,
            failures
        ) {

            scanRunning =
                false;


            scanButton.disabled =
                false;


            scanButton.textContent =
                'Scan All Links';


            if (progress) {

                progress.textContent =
                    'Scan complete: ' +
                    state.completed +
                    ' / ' +
                    state.total +
                    ' (' +
                    state.percentage +
                    '%)';

            }


            if (failures > 0) {

                showNotice(
                    'Scan completed. ' +
                    failures +
                    ' link(s) could not be verified and will be retried automatically.',
                    'warning'
                );

            } else {

                showNotice(
                    'All social-video links have been scanned.',
                    'success'
                );

            }


            /*
             * Give the user a moment to see the
             * completion message before refreshing.
             */
            window.setTimeout(
                function () {

                    window.location.reload();

                },
                800
            );

        }


        /*=====================================================
        PROCESS SERVER QUEUE
        =====================================================*/

        async function processQueue(
            token,
            initialState
        ) {

            if (!token) {

                throw new Error(
                    'The scan session token is missing.'
                );

            }


            scanRunning =
                true;


            scanButton.disabled =
                true;


            scanButton.textContent =
                'Scanning...';


            let state =
                initialState;


            let failures =
                0;


            renderProgress(
                state,
                'Scanning'
            );


            /*
             * Keep requesting batches until the server
             * tells us the scan is complete.
             */
            while (
                scanRunning
            ) {

                const batchResponse =
                    await getNextBatch(
                        token
                    );


                if (
                    !batchResponse
                    ||
                    !batchResponse.data
                ) {

                    throw new Error(
                        'The server returned an invalid scan response.'
                    );

                }


                /*
                 * The batch endpoint returns the latest
                 * server-side state.
                 */
                if (
                    batchResponse.data.state
                ) {

                    state =
                        batchResponse.data.state;


                    renderProgress(
                        state,
                        'Scanning'
                    );

                }


                /*
                 * Nothing remains.
                 */
                if (
                    batchResponse.data.complete
                ) {

                    finishScan(
                        state,
                        failures
                    );

                    return;

                }


                /*
                 * IDs assigned to this batch.
                 */
                const postIds =
                    Array.isArray(
                        batchResponse.data.post_ids
                    )

                        ?

                    batchResponse.data.post_ids

                        :

                    [];


                /*
                 * If the server temporarily has no batch,
                 * wait and ask again.
                 */
                if (
                    postIds.length === 0
                ) {

                    await wait(
                        RESUME_POLL_DELAY
                    );

                    continue;

                }


                /*
                 * Check the URLs in this batch.
                 */
                const result =
                    await processBatch(
                        postIds,
                        token,
                        state
                    );


                failures +=
                    result.failed;


                /*
                 * Refresh server state after the
                 * batch finishes.
                 */
                try {

                    const stateResponse =
                        await getState();


                    if (
                        stateResponse
                        &&
                        stateResponse.data
                        &&
                        stateResponse.data.state
                    ) {

                        state =
                            stateResponse.data.state;


                        renderProgress(
                            state,
                            'Scanning'
                        );

                    }

                } catch (
                    ignore
                ) {

                    /*
                     * If this refresh fails, the next
                     * batch response remains authoritative.
                     */

                }

            }

        }


        /*=====================================================
        RESUME ACTIVE SCAN
        =====================================================*/

        async function resumeActiveScan() {

            try {

                const response =
                    await getSession();


                const state =
                    response
                    &&
                    response.data
                    &&
                    response.data.state

                        ?

                    response.data.state

                        :

                    null;


                const token =
                    response
                    &&
                    response.data
                    &&
                    response.data.scan_token

                        ?

                    response.data.scan_token

                        :

                    '';


                /*
                 * Nothing currently exists.
                 */
                if (!state) {

                    return;

                }


                /*
                 * A scan is already running.
                 */
                if (
                    state.status ===
                    'running'
                    &&
                    token
                ) {

                    scanButton.disabled =
                        true;


                    scanButton.textContent =
                        'Resuming Scan...';


                    renderProgress(
                        state,
                        'Scan in progress'
                    );


                    await processQueue(
                        token,
                        state
                    );


                    return;

                }


                /*
                 * Show the last completed scan state.
                 */
                if (
                    state.status ===
                    'complete'
                ) {

                    renderProgress(
                        state,
                        'Last scan'
                    );

                }

            } catch (
                error
            ) {

                /*
                 * Do not make a normal page load
                 * unusable because the status check
                 * failed.
                 */
                scanRunning =
                    false;


                scanButton.disabled =
                    false;


                scanButton.textContent =
                    'Scan All Links';

            }

        }


        /*=====================================================
        START NEW SCAN
        =====================================================*/

        async function beginScan() {

            if (
                scanRunning
            ) {

                return;

            }


            hideNotice();


            scanButton.disabled =
                true;


            scanButton.textContent =
                'Preparing...';


            if (progress) {

                progress.textContent =
                    'Creating scan queue...';

            }


            try {

                const response =
                    await startScan();


                const state =
                    response
                    &&
                    response.data
                    &&
                    response.data.state

                        ?

                    response.data.state

                        :

                    null;


                const token =
                    response
                    &&
                    response.data
                    &&
                    response.data.scan_token

                        ?

                    response.data.scan_token

                        :

                    '';


                /*
                 * We must receive a valid server
                 * scan state.
                 */
                if (!state) {

                    throw new Error(
                        'The server did not return a scan state.'
                    );

                }


                /*
                 * There was nothing to scan.
                 */
                if (
                    state.status ===
                    'complete'
                ) {

                    finishScan(
                        state,
                        0
                    );

                    return;

                }


                /*
                 * A valid queue token is required.
                 */
                if (!token) {

                    throw new Error(
                        'The server did not return a scan session token.'
                    );

                }


                /*
                 * Start consuming the persistent
                 * server-side queue.
                 */
                await processQueue(
                    token,
                    state
                );

            } catch (
                error
            ) {

                scanRunning =
                    false;


                scanButton.disabled =
                    false;


                scanButton.textContent =
                    'Scan All Links';


                if (progress) {

                    progress.textContent =
                        '';

                }


                showNotice(
                    error
                    &&
                    error.message

                        ?

                    error.message

                        :

                    'The social-video scan could not be started.',
                    'error'
                );

            }

        }


        /*=====================================================
        SCAN BUTTON
        =====================================================*/

        scanButton.addEventListener(
            'click',
            function (event) {

                event.preventDefault();


                /*
                 * If an active scan was detected after
                 * page load, continue it.
                 */
                if (
                    scanButton.textContent ===
                    'Resume Scan'
                ) {

                    resumeActiveScan();

                    return;

                }


                beginScan();

            }
        );


        /*
         * Useful diagnostic attribute.
         */
        scanButton.setAttribute(
            'data-isago-scanner-ready',
            '1'
        );


        /*=====================================================
        INITIAL SERVER STATE CHECK
        =====================================================*/

        resumeActiveScan();

    }


    /*=========================================================
    DOM READY
    =========================================================*/

    if (
        document.readyState ===
        'loading'
    ) {

        document.addEventListener(
            'DOMContentLoaded',
            initBrokenSocialVideos
        );

    } else {

        initBrokenSocialVideos();

    }

})();