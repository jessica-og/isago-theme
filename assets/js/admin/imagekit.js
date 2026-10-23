(function ($) {

    'use strict';


    /**
     * ---------------------------------------------------------
     * ISAGO IMAGEKIT UPLOADER
     * ---------------------------------------------------------
     */


    let activeInput = null;


    /**
     * ---------------------------------------------------------
     * CREATE FILE INPUT
     * ---------------------------------------------------------
     */
    function createFileInput(type) {

        const input = document.createElement('input');

        input.type = 'file';

        input.style.display = 'none';

        if (type === 'video') {

            input.accept = 'video/*';

        } else {

            input.accept = 'image/*';

        }

        document.body.appendChild(input);

        return input;
    }


    /**
     * ---------------------------------------------------------
     * GET TARGET
     * ---------------------------------------------------------
     */
    function getTarget(button) {

        return $(button).data('target') || '';

    }


    /**
     * ---------------------------------------------------------
     * GET ACCOUNT
     * ---------------------------------------------------------
     */
 function getAccount(button) {

    const selector =
        $(button).data('account-selector') || '';

    if (selector) {

        const selectedAccount =
            $(selector).val();

        if (selectedAccount) {
            return selectedAccount;
        }

    }

    return (
        $(button).data('account') ||
        'account_1'
    );

}


/**
 * Get the selected ImageKit destination folder.
 */
function getFolder(button) {
    const selector = $(button).data('folder-selector') || '';

    if (selector) {
        const folder = $(selector).val();

        if (folder) {
            return folder;
        }
    }

    return (
        $(button).data('folder') ||
        '/Isago-Media/Site-Assets/'
    );
}



    /**
     * ---------------------------------------------------------
     * GET MEDIA TYPE
     * ---------------------------------------------------------
     */
    function getMediaType(button) {

        return (
            $(button).data('type') ||
            'image'
        );

    }


    /**
     * ---------------------------------------------------------
     * GET PREVIEW
     * ---------------------------------------------------------
     */
    function getPreview(button) {

        const previewSelector =
            $(button).data('preview') || '';

        if (!previewSelector) {
            return $();
        }

        return $(previewSelector);

    }


    /**
     * ---------------------------------------------------------
     * SHOW MESSAGE
     * ---------------------------------------------------------
     */
    function showError(message) {

        window.alert(
            message ||
            'ImageKit upload failed.'
        );

    }






/**
 * ---------------------------------------------------------
 * UPDATE PREVIEW
 * ---------------------------------------------------------
 */
function updatePreview(
    button,
    url,
    mediaType
) {

    const preview =
        getPreview(button);


    if (
        !preview.length ||
        !url
    ) {
        return;
    }


    /*
     * The preview selector points directly to
     * the <img> or <video> element.
     *
     * Do NOT search inside it.
     */


    if (mediaType === 'video') {

        /*
         * This is the actual <video> element.
         */

        if (
            preview.is('video')
        ) {

            /*
             * Remove any existing <source>
             * so replacement works correctly.
             */

            preview.find('source').remove();


            /*
             * Create the new video source.
             */

            const source =
                $('<source>', {
                    src: url,
                    type: 'video/mp4'
                });


            preview.append(
                source
            );


            /*
             * Also set the video src directly.
             * This makes the preview work even when
             * the browser does not immediately use
             * the newly-created source element.
             */

            preview.attr(
                'src',
                url
            );


            /*
             * Force the browser to reload the
             * newly uploaded/replaced video.
             */

            const videoElement =
                preview.get(0);


            if (
                videoElement
            ) {

                videoElement.load();

            }


            preview.show();

            return;
        }


        /*
         * Fallback in case the target is a wrapper.
         */

        let video =
            preview.find('video');


        if (!video.length) {

            video =
                $('<video>', {
                    controls: true,
                    preload: 'metadata'
                });

            preview
                .empty()
                .append(video);

        }


        video.attr(
            'src',
            url
        );


        const videoElement =
            video.get(0);


        if (
            videoElement
        ) {

            videoElement.load();

        }


        preview.show();

        return;
    }


    /*
     * IMAGE
     *
     * In the Video Post metabox the preview selector
     * points directly to the <img>.
     */

    if (
        preview.is('img')
    ) {

        preview
            .attr('src', url)
            .show();

        return;
    }


    /*
     * Fallback for wrapper elements.
     */

    let image =
        preview.find('img');


    if (!image.length) {

        image =
            $('<img>', {
                alt: ''
            });

        preview
            .empty()
            .append(image);

    }


    image
        .attr('src', url)
        .show();


    preview.show();
}


/**
 * ---------------------------------------------------------
 * CLEAR PREVIEW
 * ---------------------------------------------------------
 */
function clearPreview(
    button
) {

    const preview =
        getPreview(button);


    if (!preview.length) {
        return;
    }


    /*
     * Direct image preview.
     */

    if (
        preview.is('img')
    ) {

        preview
            .attr('src', '')
            .hide();

        return;
    }


    /*
     * Direct video preview.
     */

    if (
        preview.is('video')
    ) {

        const videoElement =
            preview.get(0);


        if (
            videoElement
        ) {

            videoElement.pause();

            videoElement.removeAttribute(
                'src'
            );

            videoElement.load();

        }


        preview
            .find('source')
            .remove();


        preview.hide();

        return;
    }


    /*
     * Wrapper fallback.
     */

    preview
        .find('img')
        .attr('src', '')
        .hide();


    preview
        .find('video')
        .each(
            function () {

                this.pause();

                this.removeAttribute(
                    'src'
                );

                this.load();

            }
        );


    preview.hide();
}




/**
 * ---------------------------------------------------------
 * GET YOUTUBE ID
 * ---------------------------------------------------------
 */
function getYouTubeId(url) {

    if (!url) {
        return '';
    }

    url = $.trim(url);

    /*
     * Standard YouTube:
     * https://www.youtube.com/watch?v=VIDEO_ID
     *
     * Short URL:
     * https://youtu.be/VIDEO_ID
     *
     * Embed:
     * https://www.youtube.com/embed/VIDEO_ID
     *
     * Shorts:
     * https://www.youtube.com/shorts/VIDEO_ID
     */
    const match = url.match(
        /(?:youtube\.com\/(?:watch\?v=|embed\/|shorts\/)|youtu\.be\/)([^?&/]+)/
    );

    return match
        ? match[1]
        : '';
}


/**
 * ---------------------------------------------------------
 * UPDATE YOUTUBE PREVIEW
 * ---------------------------------------------------------
 */
function updateYouTubePreview(
    input,
    previewSelector
) {

    const preview =
        $(previewSelector);

    if (!preview.length) {
        return;
    }


    const url =
        $(input)
            .val()
            .trim();


    const youtubeId =
        getYouTubeId(url);


    /*
     * No valid YouTube URL.
     */
    if (!youtubeId) {

        preview
            .hide()
            .empty();

        return;
    }


    /*
     * Get the YouTube thumbnail.
     */
    const thumbnailUrl =
        'https://img.youtube.com/vi/' +
        encodeURIComponent(youtubeId) +
        '/hqdefault.jpg';


    /*
     * Create preview image.
     */
    const image =
        $('<img>', {
            src: thumbnailUrl,
            alt: 'YouTube video preview'
        });


    image.css({
        maxWidth: '480px',
        width: '100%',
        height: 'auto',
        display: 'block'
    });


    /*
     * Show preview.
     */
    preview
        .empty()
        .append(image)
        .show();
}


/**
 * ---------------------------------------------------------
 * INITIALIZE YOUTUBE PREVIEWS
 * ---------------------------------------------------------
 */
$(document).ready(
    function () {


        /*
         * ================================================
         * AI PROMPT YOUTUBE
         * ================================================
         */
        const aiPromptYouTube =
            $('#isago-ai-prompt-youtube');


        if (
            aiPromptYouTube.length
        ) {

            updateYouTubePreview(
                aiPromptYouTube,
                '#isago-youtube-preview'
            );

        }


        /*
         * ================================================
         * VIDEO POST YOUTUBE
         * ================================================
         */
        const videoYouTube =
            $('#isago-youtube-url');


        if (
            videoYouTube.length
        ) {

            updateYouTubePreview(
                videoYouTube,
                '#isago-youtube-preview'
            );

        }


        /*
         * ================================================
         * AI PROMPT — LIVE URL CHANGES
         * ================================================
         */
        $(document).on(
            'input change',
            '#isago-ai-prompt-youtube',
            function () {

                updateYouTubePreview(
                    this,
                    '#isago-youtube-preview'
                );

            }
        );


        /*
         * ================================================
         * VIDEO POST — LIVE URL CHANGES
         * ================================================
         */
        $(document).on(
            'input change',
            '#isago-youtube-url',
            function () {

                updateYouTubePreview(
                    this,
                    '#isago-youtube-preview'
                );

            }
        );

    }
);




    /**
     * ---------------------------------------------------------
     * SET MEDIA ID
     * ---------------------------------------------------------
     */
    function setMediaId(
        button,
        mediaId
    ) {

        const target =
            getTarget(button);


        if (!target) {
            return;
        }


        $(target).val(
            mediaId
        ).trigger('change');

    }


    /**
     * ---------------------------------------------------------
     * REQUEST IMAGEKIT AUTH
     * ---------------------------------------------------------
     */
    function getAuthentication(
        accountId
    ) {

        return $.ajax({

            url: isagoImageKit.ajaxUrl,

            method: 'POST',

            dataType: 'json',

            data: {

                action:
                    'isago_imagekit_auth',

                nonce:
                    isagoImageKit.nonce,

                account_id:
                    accountId

            }

        });

    }


    /**
     * ---------------------------------------------------------
     * SAVE IMAGEKIT RESPONSE
     * ---------------------------------------------------------
     */
    function saveUpload(
        accountId,
        result,
        file,
          mediaType
    ) {

        return $.ajax({

            url: isagoImageKit.ajaxUrl,

            method: 'POST',

            dataType: 'json',

            data: {

                action:
                    'isago_imagekit_save_upload',

                nonce:
                    isagoImageKit.nonce,

                account_id:
                    accountId,

                file_id:
                    result.fileId || '',

                file_path:
                    result.filePath || '',

                url:
                    result.url || '',

                thumbnail_url:
                    result.thumbnailUrl || '',

                file_name:
                    result.name ||
                    file.name ||
                    '',

                file_type:
                  mediaType,

                mime_type:
                    file.type ||
                    '',

                width:
                    result.width ||
                    0,

                height:
                    result.height ||
                    0,

                file_size:
                    result.size ||
                    file.size ||
                    0

            }

        });

    }


    /**
     * ---------------------------------------------------------
     * UPLOAD TO IMAGEKIT
     * ---------------------------------------------------------
     */
    function uploadToImageKit(
        button,
        file
    ) {

        const accountId =
            getAccount(button);


        const mediaType =
            getMediaType(button);


        /*
         * Disable the button while uploading.
         */
        const originalText =
            $(button).text();


        $(button)
            .prop('disabled', true)
            .text('Uploading...');


        /*
         * Get secure authentication parameters.
         */
        getAuthentication(
            accountId
        )

        .done(function (authResponse) {

            if (
                !authResponse.success
                ||
                !authResponse.data
            ) {

                showError(
                    authResponse.data &&
                    authResponse.data.message
                        ? authResponse.data.message
                        : 'Could not authenticate with ImageKit.'
                );

                return;

            }


            const auth =
                authResponse.data;


            /*
             * ImageKit requires multipart/form-data.
             */
            const formData =
                new FormData();


            formData.append(
                'file',
                file
            );


            formData.append(
                'fileName',
                file.name
            );
formData.append('folder', getFolder(button));

            formData.append(
                'publicKey',
                auth.public_key
            );


            formData.append(
                'signature',
                auth.signature
            );


            formData.append(
                'expire',
                auth.expire
            );


            formData.append(
                'token',
                auth.token
            );


            /*
             * Upload directly to ImageKit.
             *
             * WordPress is NOT handling the file bytes.
             */
            $.ajax({

                url:
                    'https://upload.imagekit.io/api/v1/files/upload',

                method: 'POST',

                data:
                    formData,

                processData: false,

                contentType: false,

                dataType: 'json',

                xhr: function () {

                    const xhr =
                        new window.XMLHttpRequest();


                    xhr.upload.addEventListener(
                        'progress',
                        function (event) {

                            if (
                                event.lengthComputable
                            ) {

                                const percent =
                                    Math.round(
                                        (
                                            event.loaded /
                                            event.total
                                        ) * 100
                                    );


                                $(button).text(
                                    'Uploading ' +
                                    percent +
                                    '%...'
                                );

                            }

                        }
                    );


                    return xhr;
                }

            })

            .done(function (result) {

                /*
                 * Now save the ImageKit response
                 * inside the Isago media database.
                 */
                saveUpload(
                    accountId,
                    result,
                    file,
                       mediaType
                )

                .done(function (saveResponse) {

                    if (
                        !saveResponse.success
                    ) {

                        showError(
                            saveResponse.data &&
                            saveResponse.data.message
                                ? saveResponse.data.message
                                : 'Image uploaded but could not be registered in Isago.'
                        );

                        return;

                    }


                    const data =
                        saveResponse.data;


                    /*
                     * Store the central Isago media ID.
                     */
                    setMediaId(
                        button,
                        data.media_id
                    );


                    /*
                     * Display the ImageKit URL.
                     */
                    updatePreview(
                        button,
                        data.url,
                        mediaType
                    );
$(document).trigger(
    'isago:imagekit-uploaded',
    {
        mediaId: data.media_id,
        url: data.url
    }
);
                })

                .fail(function () {

                    showError(
                        'Image uploaded to ImageKit, but Isago could not save the media record.'
                    );

                });

            })

            .fail(function (xhr) {

                let message =
                    'ImageKit upload failed.';


                if (
                    xhr.responseJSON &&
                    xhr.responseJSON.message
                ) {

                    message =
                        xhr.responseJSON.message;

                }


                showError(
                    message
                );

            })

            .always(function () {

                $(button)
                    .prop('disabled', false)
                    .text(originalText);

            });

        })

        .fail(function () {

            showError(
                'Could not connect to the ImageKit authentication endpoint.'
            );

            $(button)
                .prop('disabled', false)
                .text(originalText);

        });

    }


    /**
     * ---------------------------------------------------------
     * IMAGEKIT BUTTON
     * ---------------------------------------------------------
     */
    $(document).on(
        'click',
        '.isago-imagekit-upload-button',
        function (event) {

            event.preventDefault();


            const button =
                this;


            const mediaType =
                getMediaType(button);


            activeInput =
                createFileInput(
                    mediaType
                );


            activeInput.addEventListener(
                'change',
                function () {

                    const file =
                        this.files &&
                        this.files[0];


                    if (!file) {
                        return;
                    }


                    uploadToImageKit(
                        button,
                        file
                    );


                    /*
                     * Remove the temporary input after
                     * the browser has captured the file.
                     */
                    setTimeout(
                        function () {

                            if (
                                activeInput &&
                                activeInput.parentNode
                            ) {

                                activeInput
                                    .parentNode
                                    .removeChild(
                                        activeInput
                                    );

                            }

                        },
                        1000
                    );

                }
            );


            activeInput.click();

        }
    );




/**
 * Import an image from a URL into ImageKit.
 *
 * The image is uploaded to ImageKit and then registered
 * in Isago's central media database.
 */
function importImageKitUrl(button, imageUrl) {
    const accountId = getAccount(button);
    const folder = getFolder(button);
    const mediaType = getMediaType(button);

    if (mediaType !== 'image') {
        showError('URL import currently supports images only.');
        return;
    }

    try {
        const parsedUrl = new URL(imageUrl);

        if (
            parsedUrl.protocol !== 'https:' &&
            parsedUrl.protocol !== 'http:'
        ) {
            throw new Error('Use a valid HTTP or HTTPS image URL.');
        }

        if (
            !parsedUrl.hostname ||
            !parsedUrl.hostname.includes('.')  
        ) {
            throw new Error('Enter a valid public image URL.');
        }
    } catch (error) {
        showError(error.message || 'Enter a valid image URL.');
        return;
    }

    if (!accountId || !folder) {
        showError('Select an ImageKit account and destination folder.');
        return;
    }

    const originalText = $(button).text();

    $(button)
        .prop('disabled', true)
        .text('Importing image...');

    getAuthentication(accountId)
        .done(function (authResponse) {
            if (
                !authResponse.success ||
                !authResponse.data
            ) {
                showError(
                    authResponse.data &&
                    authResponse.data.message
                        ? authResponse.data.message
                        : 'Could not authenticate with ImageKit.'
                );
                return;
            }

            const auth = authResponse.data;
            const filename = (new URL(imageUrl)).pathname
                .split('/')
                .pop()
                .split('?')[0] || 'imported-image.jpg';

            const formData = new FormData();

            /*
             * ImageKit accepts a publicly accessible URL
             * as the file value for URL-based imports.
             */
            formData.append('file', imageUrl);
            formData.append('fileName', filename);
            formData.append('folder', folder);
            formData.append('publicKey', auth.public_key);
            formData.append('signature', auth.signature);
            formData.append('expire', String(auth.expire));
            formData.append('token', auth.token);

            $.ajax({
                url: 'https://upload.imagekit.io/api/v1/files/upload',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json'
            })
            .done(function (result) {
                if (!result || !result.fileId || !result.url) {
                    showError(
                        'ImageKit did not return a valid uploaded image.'
                    );
                    return;
                }

                saveUpload(
                    accountId,
                    result,
                    {
                        name: result.name || filename,
                        type: 'image/*',
                        size: result.size || 0
                    },
                    'image'
                )
                .done(function (saveResponse) {
                    if (
                        !saveResponse ||
                        !saveResponse.success ||
                        !saveResponse.data
                    ) {
                        showError(
                            saveResponse &&
                            saveResponse.data &&
                            saveResponse.data.message
                                ? saveResponse.data.message
                                : 'ImageKit uploaded the image, but Isago could not register it.'
                        );
                        return;
                    }

                    const data = saveResponse.data;

                    setMediaId(button, data.media_id);
                    updatePreview(button, data.url, 'image');

                    $(document).trigger(
                        'isago:imagekit-uploaded',
                        {
                            mediaId: data.media_id,
                            url: data.url
                        }
                    );
                })
                .fail(function () {
                    showError(
                        'ImageKit uploaded the image, but Isago could not save its media record.'
                    );
                });
            })
            .fail(function (xhr) {
                showError(
                    xhr.responseJSON &&
                    xhr.responseJSON.message
                        ? xhr.responseJSON.message
                        : 'Could not import this image. Check that the URL points to a publicly accessible image.'
                );
            });
        })
        .fail(function () {
            showError('Could not connect to ImageKit authentication.');
        })
        .always(function () {
            $(button)
                .prop('disabled', false)
                .text(originalText);
        });
}




  /**
 * ---------------------------------------------------------
 * REMOVE IMAGEKIT MEDIA
 * ---------------------------------------------------------
 */
$(document).on(
    'click',
    '.isago-imagekit-remove-button',
    function (e) {

        e.preventDefault();

        const button = $(this);

        const targetSelector =
            button.data('target');

        const previewSelector =
            button.data('preview');

        const objectType =
            button.data('object-type');

        const objectId =
            parseInt(
                button.data('object-id'),
                10
            ) || 0;

        const fieldKey =
            button.data('field-key');

        const mediaId =
            parseInt(
                $(targetSelector).val(),
                10
            ) || 0;


        /*
         * Nothing to remove.
         */
        if (!mediaId) {

            $(targetSelector).val('');

            clearPreview(
                previewSelector
            );

            button.prop(
                'disabled',
                true
            );

            return;

        }


        /*
         * Prevent double clicking.
         */
        if (button.data('removing')) {
            return;
        }

        button.data(
            'removing',
            true
        );

        button.prop(
            'disabled',
            true
        );


        /*
         * Nonce.
         */
        const nonce =
            isagoImageKit.nonce;


        /*
         * If this media already belongs to a
         * saved object/field, remove the reference
         * and let the server perform cleanup.
         */
        if (
            objectType &&
            objectId &&
            fieldKey
        ) {

            $.ajax({

                url: ajaxurl,

                type: 'POST',

                data: {
                    action:
                        'isago_imagekit_remove_media',

                    nonce:
                        nonce,

                    media_id:
                        mediaId,

                    object_type:
                        objectType,

                    object_id:
                        objectId,

                    field_key:
                        fieldKey
                },

                success: function (response) {

                    if (
                        response &&
                        response.success
                    ) {

                        /*
                         * Clear the field.
                         */
                        $(targetSelector).val('');


                        /*
                         * Clear the preview.
                         */
                        clearPreview(
                            previewSelector
                        );


                        /*
                         * Keep button disabled
                         * because there is now no
                         * media assigned.
                         */
                        button.prop(
                            'disabled',
                            true
                        );

                    } else {

                        let message =
                            'Could not remove the image.';

                        if (
                            response &&
                            response.data &&
                            response.data.message
                        ) {

                            message =
                                response.data.message;

                        }

                        window.alert(
                            message
                        );

                        button.prop(
                            'disabled',
                            false
                        );
                    }

                },

                error: function () {

                    window.alert(
                        'Could not remove the image. Please try again.'
                    );

                    button.prop(
                        'disabled',
                        false
                    );

                },

                complete: function () {

                    button.data(
                        'removing',
                        false
                    );

                }

            });

            return;
        }


        /*
         * This is an ImageKit upload that has
         * not been saved to a post yet.
         *
         * It has a media record but no field
         * reference, so delete it directly.
         */
        $.ajax({

            url: ajaxurl,

            type: 'POST',

            data: {

                action:
                    'isago_imagekit_delete_unreferenced',

                nonce:
                    nonce,

                media_id:
                    mediaId

            },

            success: function (response) {

                if (
                    response &&
                    response.success
                ) {

                    $(targetSelector).val('');

                    clearPreview(
                        previewSelector
                    );

                    button.prop(
                        'disabled',
                        true
                    );

                } else {

                    let message =
                        'Could not remove the image.';

                    if (
                        response &&
                        response.data &&
                        response.data.message
                    ) {

                        message =
                            response.data.message;

                    }

                    window.alert(
                        message
                    );

                    button.prop(
                        'disabled',
                        false
                    );
                }
  
            },

            error: function () {

                window.alert(
                    'Could not remove the image. Please try again.'
                );

                button.prop(
                    'disabled',
                    false
                );

            },

            complete: function () {

                button.data(
                    'removing',
                    false
                );

            }

        });

    }
);


$(document).on(
    'click',
    '.isago-imagekit-import-url-button',
    function (event) {
        event.preventDefault();

        const button = this;

        const imageUrl = window.prompt(
            'Paste the direct URL of the image you want to import into ImageKit:'
        );

        if (!imageUrl || !imageUrl.trim()) {
            return;
        }

        importImageKitUrl(button, imageUrl.trim());
    }
);



/**
 * Open the existing ImageKit media picker.
 * Supports both images and videos.
 */
$(document).on(
    'click',
    '.isago-imagekit-select-existing-button, .isago-imagekit-select-existing-video-button',
    function (event) {
        event.preventDefault();

        const button = this;
        const $button = $(button);
        const mediaType = getMediaType(button);
        const isVideo = mediaType === 'video';
        const originalText = $button.text();

        if ($button.data('loading')) {
            return;
        }

        $button
            .data('loading', true)
            .prop('disabled', true)
            .text(isVideo ? 'Loading videos...' : 'Loading images...');

        $.ajax({
            url: isagoImageKit.ajaxUrl,
            method: 'POST',
            dataType: 'json',
            data: {
                action: 'isago_taxonomy_hero_list_images',
                nonce: isagoImageKit.nonce,
                media_type: mediaType
            }
        })
        .done(function (response) {
            if (
                !response ||
                !response.success ||
                !response.data ||
                !Array.isArray(response.data.items)
            ) {
                showError(
                    response &&
                    response.data &&
                    response.data.message
                        ? response.data.message
                        : 'Could not load your existing ImageKit media.'
                );
                return;
            }

            openExistingImagePicker(
                button,
                response.data.items,
                mediaType
            );
        })
        .fail(function (xhr) {
            const message =
                xhr.responseJSON &&
                xhr.responseJSON.data &&
                xhr.responseJSON.data.message
                    ? xhr.responseJSON.data.message
                    : 'Could not connect to the Isago media library.';

            showError(message);
        })
        .always(function () {
            $button
                .data('loading', false)
                .prop('disabled', false)
                .text(originalText);
        });
    }
);




/**
 * Display existing ImageKit images or videos in a searchable modal.
 */
function openExistingImagePicker(button, items, mediaType) {
    $('.isago-existing-image-picker-overlay').remove();

    const isVideo = mediaType === 'video';

    const $overlay = $('<div>', {
        class: 'isago-existing-image-picker-overlay'
    }).css({
        position: 'fixed',
        inset: '0',
        zIndex: 100100,
        background: 'rgba(0,0,0,0.65)',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        padding: '20px'
    });

    const $modal = $('<div>').css({
        background: '#fff',
        color: '#222',
        width: '100%',
        maxWidth: '1000px',
        maxHeight: '85vh',
        overflowY: 'auto',
        padding: '24px',
        borderRadius: '8px',
        boxSizing: 'border-box'
    });

    const $header = $('<div>').css({
        display: 'flex',
        justifyContent: 'space-between',
        alignItems: 'center',
        gap: '12px',
        marginBottom: '16px'
    });

    $header.append(
        $('<h2>')
            .text('Select an Existing ImageKit ' + (isVideo ? 'Video' : 'Image'))
            .css({ margin: '0' })
    );

    const $close = $('<button>', {
        type: 'button',
        class: 'button',
        text: 'Close'
    });

    $header.append($close);

    const $search = $('<input>', {
        type: 'search',
        placeholder: 'Search by filename or folder...',
        'aria-label': 'Search existing media'
    }).css({
        width: '100%',
        marginBottom: '18px'
    });

    const $grid = $('<div>').css({
        display: 'grid',
        gridTemplateColumns: 'repeat(auto-fill, minmax(150px, 1fr))',
        gap: '14px'
    });

    const $message = $('<p>').css({
        margin: '12px 0',
        color: '#555'
    });

    const $footer = $('<p>').text(
        'Selecting existing media reuses its Isago media record. It does not upload another copy.'
    ).css({
        marginTop: '20px',
        color: '#555',
        fontSize: '13px'
    });

    $modal
        .append($header)
        .append($search)
        .append($message)
        .append($grid)
        .append($footer);

    $overlay.append($modal);
    $('body').append($overlay);

    function closePicker() {
        $overlay.remove();
        $(document).off('keydown.isagoImagePicker');
    }

    $close.on('click', closePicker);

    $overlay.on('click', function (event) {
        if (event.target === $overlay[0]) {
            closePicker();
        }
    });

    $(document).on(
        'keydown.isagoImagePicker',
        function (event) {
            if (event.key === 'Escape') {
                closePicker();
            }
        }
    );

    function renderItems() {
        const query = String($search.val() || '')
            .trim()
            .toLowerCase();

        $grid.empty();

        const filteredItems = items.filter(function (item) {
            const searchable = [
                item.original_name,
                item.file_path,
                item.url
            ].join(' ').toLowerCase();

            return searchable.includes(query);
        });

        $message.text(
            filteredItems.length
                ? filteredItems.length + (isVideo ? ' video(s) found.' : ' image(s) found.')
                : (isVideo ? 'No matching videos found.' : 'No matching images found.')
        );

        filteredItems.forEach(function (item) {
            const $card = $('<button>', {
                type: 'button',
                class: 'button isago-existing-image-picker-item'
            }).css({
                padding: '10px',
                height: 'auto',
                minWidth: '0',
                whiteSpace: 'normal',
                textAlign: 'left',
                display: 'block',
                border: '1px solid #ddd',
                background: '#fff'
            });

            let $preview;

            if (isVideo) {
                $preview = $('<video>', {
                    src: item.url,
                    preload: 'metadata',
                    muted: true,
                    playsinline: true
                }).css({
                    display: 'block',
                    width: '100%',
                    height: '125px',
                    objectFit: 'contain',
                    marginBottom: '8px',
                    background: '#f5f5f5',
                    pointerEvents: 'none'
                });
            } else {
                $preview = $('<img>', {
                    src: item.thumbnail_url || item.url,
                    alt: item.original_name || 'ImageKit image',
                    loading: 'lazy'
                }).css({
                    display: 'block',
                    width: '100%',
                    height: '125px',
                    objectFit: 'contain',
                    marginBottom: '8px',
                    background: '#f5f5f5'
                });
            }

            const $name = $('<span>')
                .text(item.original_name || (isVideo ? 'Untitled video' : 'Untitled image'))
                .css({
                    display: 'block',
                    overflowWrap: 'anywhere',
                    fontSize: '12px'
                });

            const $details = $('<span>')
                .text(
                    item.width && item.height
                        ? item.width + ' × ' + item.height
                        : (isVideo ? 'Video' : '')
                )
                .css({
                    display: 'block',
                    marginTop: '4px',
                    color: '#666',
                    fontSize: '11px'
                });

            $card
                .append($preview)
                .append($name)
                .append($details);

            $card.on('click', function () {
                selectExistingImage(button, item, mediaType);
                closePicker();
            });

            $grid.append($card);
        });
    }

    $search.on('input', renderItems);

    renderItems();
}



/**
 * Assign an existing ImageKit image or video to a field.
 */
function selectExistingImage(button, item, mediaType) {
    if (!item || !item.id || !item.url) {
        showError('The selected media record is incomplete.');
        return;
    }

    // Reuse the existing central Isago media ID.
    setMediaId(button, item.id);

    // Update the correct preview element.
    updatePreview(button, item.url, mediaType);

    // Keep the selected ImageKit account in sync.
    const accountSelector =
        $(button).data('account-selector') || '';

    if (accountSelector && item.account_id) {
        $(accountSelector)
            .val(item.account_id)
            .trigger('change');
    }

    // Match the folder selector when the folder is available.
    const folderSelector =
        $(button).data('folder-selector') || '';

    if (
        folderSelector &&
        item.file_path &&
        item.file_path.includes('/')
    ) {
        const folder = item.file_path.substring(
            0,
            item.file_path.lastIndexOf('/') + 1
        );

        $(folderSelector).find('option').each(function () {
            if (this.value === folder) {
                $(folderSelector)
                    .val(folder)
                    .trigger('change');
            }
        });
    }
}

})(jQuery);