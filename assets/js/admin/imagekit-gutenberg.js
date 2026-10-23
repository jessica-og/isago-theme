
(function ($, window, document) {
    'use strict';

    var cfg = window.isagoImageKit;
    var wp = window.wp;

    if (!$ || !cfg || !cfg.ajaxUrl || !cfg.nonce ||
        !wp || !wp.data || !wp.element || !wp.hooks) {
        console.error('Isago ImageKit: required settings/APIs are missing.');
        return;
    }

    var h = wp.element.createElement;
    var Fragment = wp.element.Fragment;
    var useState = wp.element.useState;
    var useRef = wp.element.useRef;
    var Button = wp.components && wp.components.Button;
    var selectedAccount = '';
    var selectedFolder = '/Isago-Media/Blog-Posts-Articles/';

    function accounts() {
        return cfg.accounts && typeof cfg.accounts === 'object'
            ? cfg.accounts : {};
    }

    function folders() {
        return cfg.folders && typeof cfg.folders === 'object'
            ? cfg.folders : {
                '/Isago-Media/Site-Assets/': 'Site Assets',
                '/Isago-Media/AI-News-Articles/': 'AI News Articles',
                '/Isago-Media/Blog-Posts-Articles/': 'Blog Posts Articles',
                '/Isago-Media/AI-Prompts/': 'AI Prompts',
                '/Isago-Media/AI-Tools/': 'AI Tools'
            };
    }

    function account(id) {
        var list = accounts();

        if (id && list[id]) return id;
        if (selectedAccount && list[selectedAccount]) return selectedAccount;

        selectedAccount = Object.keys(list)[0] || '';
        return selectedAccount;
    }

    function request(data) {
        return new Promise(function (resolve, reject) {
            $.ajax({
                url: cfg.ajaxUrl,
                method: 'POST',
                dataType: 'json',
                data: $.extend({ nonce: cfg.nonce }, data)
            }).done(function (response) {
                if (response && response.success && response.data) {
                    resolve(response.data);
                } else {
                    reject(new Error(
                        response && response.data && response.data.message
                            ? response.data.message
                            : 'Isago could not process the request.'
                    ));
                }
            }).fail(function (xhr) {
                reject(new Error(
                    xhr.responseJSON &&
                    xhr.responseJSON.data &&
                    xhr.responseJSON.data.message
                        ? xhr.responseJSON.data.message
                        : 'WordPress AJAX request failed. Check the PHP error log.'
                ));
            });
        });
    }

    /*
     * Upload to ImageKit, then create/retrieve the central Isago media row.
     * This function does not save files to wp-content/uploads.
     */
    function upload(file, accountId, folderPath) {
        accountId = account(accountId);
        folderPath = folderPath || selectedFolder;

        if (!accountId) {
            return Promise.reject(new Error('No ImageKit account is configured.'));
        }

        if (!folderPath) {
            return Promise.reject(new Error('Select a destination folder.'));
        }

        if (!file || !file.type ||
            file.type.indexOf('image/') !== 0 &&
            file.type.indexOf('video/') !== 0) {
            return Promise.reject(new Error('Select an image or video file.'));
        }

        return request({
            action: 'isago_imagekit_auth',
            account_id: accountId
        }).then(function (auth) {
            if (!auth.public_key || !auth.token ||
                !auth.signature || !auth.expire) {
                throw new Error('ImageKit upload credentials are incomplete.');
            }

            var data = new FormData();
            data.append('file', file);
            data.append('fileName', file.name);
            data.append('folder', folderPath);
            data.append('publicKey', auth.public_key);
            data.append('signature', auth.signature);
            data.append('expire', auth.expire);
            data.append('token', auth.token);

            return new Promise(function (resolve, reject) {
                $.ajax({
                    url: 'https://upload.imagekit.io/api/v1/files/upload',
                    method: 'POST',
                    data: data,
                    processData: false,
                    contentType: false,
                    dataType: 'json'
                }).done(resolve).fail(function (xhr) {
                    reject(new Error(
                        xhr.responseJSON && xhr.responseJSON.message
                            ? xhr.responseJSON.message
                            : 'ImageKit upload failed. Check the browser Network tab.'
                    ));
                });
            });
        }).then(function (result) {
            if (!result || !result.fileId || !result.url) {
                throw new Error('ImageKit returned no file ID or URL.');
            }

            /*
             * This is the step that saves the central Isago database record.
             */
            return request({
                action: 'isago_imagekit_save_upload',
                account_id: accountId,
                file_id: result.fileId,
                file_path: result.filePath || '',
                url: result.url,
                thumbnail_url: result.thumbnailUrl || '',
                file_name: result.name || file.name,
                file_type: file.type.indexOf('video/') === 0
                    ? 'video' : 'image',
                mime_type: file.type,
                width: result.width || 0,
                height: result.height || 0,
                file_size: result.size || file.size || 0
            }).then(function (saved) {
                if (!saved.media_id || !saved.url) {
                    throw new Error(
                        'ImageKit received the file, but Isago did not confirm its media database record.'
                    );
                }

                return {
                    id: parseInt(saved.media_id, 10),
                    url: saved.url,
                    thumbnail_url: saved.thumbnail_url || result.thumbnailUrl || '',
                    fileId: result.fileId,
                    accountId: accountId,
                    file_path: result.filePath || '',
                    title: result.name || file.name,
                    alt: '',
                    caption: '',
                    mime: file.type,
                    isago_media_id: parseInt(saved.media_id, 10)
                };
            });
        });
    }

    function showNotice(message, type) {
        try {
            var notices = wp.data.dispatch('core/notices');

            if (notices && notices.createNotice) {
                notices.createNotice(type || 'info', message, {
                    id: 'isago-imagekit-upload-notice',
                    isDismissible: true
                });
                return;
            }
        } catch (error) {}

        if (type === 'error') window.alert(message);
    }

    /*
     * Existing media picker.
     * The PHP AJAX action must return central Isago media records.
     */
    function openExistingPicker(items, props, mediaType) {
        $('.isago-ik-picker-overlay').remove();

        var overlay = document.createElement('div');
        overlay.className = 'isago-ik-picker-overlay';

        var modal = document.createElement('div');
        modal.className = 'isago-ik-picker-modal';

        var header = document.createElement('div');
        header.className = 'isago-ik-picker-header';

        var title = document.createElement('h2');
        title.textContent = 'Select existing ' + mediaType;

        var close = document.createElement('button');
        close.type = 'button';
        close.className = 'button';
        close.textContent = 'Close';

        var search = document.createElement('input');
        search.type = 'search';
        search.placeholder = 'Search media by name or URL…';
        search.className = 'isago-ik-picker-search';

        var status = document.createElement('p');
        status.className = 'isago-ik-picker-status';

        var grid = document.createElement('div');
        grid.className = 'isago-ik-picker-grid';

        header.append(title, close);
        modal.append(header, search, status, grid);
        overlay.appendChild(modal);
        document.body.appendChild(overlay);

        function dismiss() {
            overlay.remove();
            document.removeEventListener('keydown', escape);
        }

        function escape(event) {
            if (event.key === 'Escape') dismiss();
        }

        close.addEventListener('click', dismiss);
        overlay.addEventListener('click', function (event) {
            if (event.target === overlay) dismiss();
        });
        document.addEventListener('keydown', escape);

        function render() {
            var term = search.value.toLowerCase().trim();
            grid.textContent = '';

            var filtered = (items || []).filter(function (item) {
                return [
                    item.original_name, item.file_name, item.file_path, item.url
                ].join(' ').toLowerCase().indexOf(term) !== -1;
            });

            status.textContent = filtered.length
                ? filtered.length + ' media item(s) found.'
                : 'No matching media found.';

            filtered.forEach(function (item) {
                if (!item.url) return;

                var card = document.createElement('button');
                card.type = 'button';
                card.className = 'isago-ik-picker-card';

                if (mediaType === 'video') {
                    var video = document.createElement('video');
                    video.src = item.url;
                    video.muted = true;
                    video.preload = 'metadata';
                    card.appendChild(video);
                } else {
                    var image = document.createElement('img');
                    image.src = item.thumbnail_url || item.url;
                    image.alt = item.original_name || item.file_name || '';
                    card.appendChild(image);
                }

                var label = document.createElement('span');
                label.textContent = item.original_name ||
                    item.file_name || 'ImageKit media';
                card.appendChild(label);

                card.addEventListener('click', function () {
                    var selected = {
                        id: parseInt(item.id || item.media_id || 0, 10),
                        url: item.url,
                        alt: item.original_name || item.file_name || '',
                        title: item.original_name || item.file_name || '',
                        mime: item.mime_type ||
                            (mediaType === 'video' ? 'video/mp4' : 'image/jpeg')
                    };

                    if (typeof props.onSelect === 'function') {
                        props.onSelect(props.multiple ? [selected] : selected);
                    } else {
                        window.alert(
                            'This Gutenberg media control cannot accept an existing file.'
                        );
                    }

                    dismiss();
                });

                grid.appendChild(card);
            });
        }

        search.addEventListener('input', render);
        render();
    }

    function mediaReplacement(Original) {
        return function IsagoMediaUpload(props) {
            props = props || {};

            var inputRef = useRef(null);
            var accountState = useState(account());
            var accountId = accountState[0];
            var setAccountId = accountState[1];

            var folderState = useState(selectedFolder);
            var folderPath = folderState[0];
            var setFolderPath = folderState[1];

            var expandedState = useState(false);
            var expanded = expandedState[0];
            var setExpanded = expandedState[1];

            var busyState = useState(false);
            var busy = busyState[0];
            var setBusy = busyState[1];

            var list = accounts();
            var accountIds = Object.keys(list);
            var folderList = folders();
            var folderPaths = Object.keys(folderList);

            var allowed = Array.isArray(props.allowedTypes)
                ? props.allowedTypes : ['image'];

            var supportsImage = !allowed.length ||
                allowed.indexOf('image') !== -1 ||
                allowed.some(function (type) {
                    return typeof type === 'string' &&
                        type.indexOf('image/') === 0;
                });

            var supportsVideo = allowed.indexOf('video') !== -1 ||
                allowed.some(function (type) {
                    return typeof type === 'string' &&
                        type.indexOf('video/') === 0;
                });

            if (!supportsImage && !supportsVideo) {
                return Original ? h(Original, props) : null;
            }

            function selectFiles(event) {
                var files = Array.prototype.slice.call(event.target.files || []);
                event.target.value = '';

                if (!files.length || busy) return;

                if (typeof props.onSelect !== 'function') {
                    showNotice(
                        'Gutenberg did not provide an image-selection callback.',
                        'error'
                    );
                    return;
                }

                setBusy(true);

                var selectedItems = [];
                var chain = Promise.resolve();

                files.forEach(function (file) {
                    chain = chain.then(function () {
                        var isImage = file.type.indexOf('image/') === 0;
                        var isVideo = file.type.indexOf('video/') === 0;

                        if (!((supportsImage && isImage) ||
                            (supportsVideo && isVideo))) {
                            throw new Error('Choose a supported image or video.');
                        }

                        showNotice('Uploading ' + file.name + ' to ImageKit…', 'info');

                        return upload(file, accountId, folderPath).then(function (media) {
                            selectedItems.push(media);
                        });
                    });
                });

                chain.then(function () {
                    /*
                     * The permanent ImageKit URL and database ID are supplied
                     * to Gutenberg only after the PHP save endpoint succeeds.
                     */
                    props.onSelect(props.multiple ? selectedItems : selectedItems[0]);

                    showNotice(
                        'Upload complete. The ImageKit file and Isago media record were confirmed.',
                        'success'
                    );
                }).catch(function (error) {
                    console.error('Isago ImageKit upload error:', error);
                    showNotice(
                        error.message || 'ImageKit upload failed.',
                        'error'
                    );
                }).finally(function () {
                    setBusy(false);
                });
            }

            function chooseExisting() {
                if (busy) return;
                setBusy(true);

                $.ajax({
                    url: cfg.ajaxUrl,
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        action: 'isago_taxonomy_hero_list_images',
                        nonce: cfg.nonce,
                        media_type: supportsVideo && !supportsImage
                            ? 'video' : 'image'
                    }
                }).done(function (response) {
                    if (!response || !response.success ||
                        !response.data ||
                        !Array.isArray(response.data.items)) {
                        showNotice(
                            response && response.data && response.data.message
                                ? response.data.message
                                : 'The existing-media endpoint did not return a media list.',
                            'error'
                        );
                        return;
                    }

                    openExistingPicker(
                        response.data.items,
                        props,
                        supportsVideo && !supportsImage ? 'video' : 'image'
                    );
                }).fail(function () {
                    showNotice(
                        'Could not load existing media. Check the PHP AJAX endpoint.',
                        'error'
                    );
                }).always(function () {
                    setBusy(false);
                });
            }

            return h(
                Fragment,
                null,
                h(
                    'div',
                    { className: 'isago-ik-upload-control' },
                    h(
                        'button',
                        {
                            type: 'button',
                            className: 'isago-ik-toggle',
                            'aria-expanded': expanded,
                            onClick: function () {
                                setExpanded(!expanded);
                            }
                        },
                      expanded ? 'Hide ImageKit uploader' : 'Open ImageKit uploader'
                    ),
                    expanded ? h(
                        'div',
                        { className: 'isago-ik-upload-options' },
                        h('label', { className: 'isago-ik-label' }, 'ImageKit account'),
                        h(
                            'select',
                            {
                                className: 'isago-ik-select',
                                value: accountId || '',
                                disabled: !accountIds.length || busy,
                                onChange: function (event) {
                                    selectedAccount = event.target.value;
                                    setAccountId(event.target.value);
                                }
                            },
                            accountIds.map(function (id) {
                                return h('option', { key: id, value: id },
                                    list[id].name || id);
                            })
                        ),
                        h('label', { className: 'isago-ik-label' }, 'Destination folder'),
                        h(
                            'select',
                            {
                                className: 'isago-ik-select',
                                value: folderPath,
                                disabled: busy,
                                onChange: function (event) {
                                    selectedFolder = event.target.value;
                                    setFolderPath(event.target.value);
                                }
                            },
                            folderPaths.map(function (path) {
                                return h('option', { key: path, value: path },
                                    folderList[path]);
                            })
                        ),
                        Button ? h(
                            Button,
                            {
                                variant: 'primary',
                                className: 'isago-ik-choose',
                                disabled: busy || !accountIds.length,
                                onClick: function () {
                                    if (inputRef.current) inputRef.current.click();
                                }
                            },
                            busy ? 'Uploading…' : 'Upload a new image'
                        ) : h(
                            'button',
                            {
                                type: 'button',
                                className: 'components-button is-primary isago-ik-choose',
                                disabled: busy || !accountIds.length,
                                onClick: function () {
                                    if (inputRef.current) inputRef.current.click();
                                }
                            },
                            busy ? 'Uploading…' : 'Upload a new image'
                        ),
                        h(
                            'button',
                            {
                                type: 'button',
                                className: 'components-button isago-ik-existing',
                                disabled: busy,
                                onClick: chooseExisting
                            },
                            'Select an existing image'
                        ),
                        h(
                            'p',
                            { className: 'isago-ik-help' },
                            ''
                        )
                    ) : null,
                    h('input', {
                        ref: inputRef,
                        type: 'file',
                        accept:
                            (supportsImage ? 'image/*' : '') +
                            (supportsImage && supportsVideo ? ',' : '') +
                            (supportsVideo ? 'video/*' : ''),
                        multiple: !!props.multiple,
                        onChange: selectFiles,
                        style: { display: 'none' }
                    })
                )
            );
        };
    }

   

function addStyles() {
    var styleId = 'isago-ik-styles';
    var oldStyle = document.getElementById(styleId);

    if (oldStyle) {
        oldStyle.remove();
    }

    var style = document.createElement('style');
    style.id = styleId;

    style.textContent = `
        /* =========================================
           MAIN GUTENBERG IMAGEKIT UPLOADER
        ========================================= */

        .isago-ik-upload-control {
            display: block !important;
            position: relative;
            width: 100% !important;
            max-width: 520px;
            margin: 20px 0 !important;
            padding: 18px !important;
            box-sizing: border-box;
            border: 1px solid #dce7eb !important;
            border-radius: 14px !important;
            background: #fff !important;
            box-shadow: 0 4px 18px rgba(20, 50, 65, .06) !important;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: #243b44;
        }

        .isago-ik-upload-control * {
            box-sizing: border-box;
        }

        .isago-ik-upload-control .isago-ik-toggle {
            display: flex !important;
            align-items: center;
            justify-content: space-between;
            width: 100% !important;
            min-height: 44px !important;
            padding: 12px 15px !important;
            border: 1px solid #bce7ef !important;
            border-radius: 9px !important;
            background: #effbfe !important;
            color: #07566b !important;
            font-size: 13px !important;
            font-weight: 650 !important;
            text-align: left;
            cursor: pointer;
            box-shadow: none !important;
        }

        .isago-ik-upload-control .isago-ik-toggle:hover {
            background: #e1f7fb !important;
            border-color: #00a6c7 !important;
        }

        .isago-ik-upload-control .isago-ik-upload-options {
            display: flex !important;
            flex-direction: column !important;
            align-items: stretch !important;
            gap: 10px !important;
            position: static !important;
            width: 100% !important;
            max-width: none !important;
            margin-top: 14px !important;
            padding: 16px !important;
            border: 1px solid #e4ecef !important;
            border-radius: 10px !important;
            background: #f8fbfc !important;
            box-shadow: none !important;
        }

        .isago-ik-upload-control .isago-ik-label {
            display: block !important;
            margin: 4px 0 0 !important;
            color: #334e58 !important;
            font-size: 12px !important;
            font-weight: 650 !important;
            line-height: 1.5 !important;
        }

        .isago-ik-upload-control select.isago-ik-select {
            display: block !important;
            width: 100% !important;
            max-width: none !important;
            min-height: 42px !important;
            margin: 0 !important;
            padding: 8px 12px !important;
            border: 1px solid #d2e0e5 !important;
            border-radius: 8px !important;
            background: #fff !important;
            color: #243b44 !important;
            font-size: 13px !important;
            box-shadow: none !important;
        }

        .isago-ik-upload-control select.isago-ik-select:focus {
            border-color: #00a6c7 !important;
            outline: none !important;
            box-shadow: 0 0 0 2px rgba(0, 166, 199, .12) !important;
        }

        .isago-ik-upload-control .isago-ik-choose,
        .isago-ik-upload-control .isago-ik-existing {
            display: flex !important;
            align-items: center;
            justify-content: center;
            width: 100% !important;
            min-height: 42px !important;
            margin: 3px 0 0 !important;
            padding: 10px 14px !important;
            border-radius: 8px !important;
            font-size: 13px !important;
            font-weight: 600 !important;
            line-height: 1.4 !important;
            cursor: pointer;
            box-shadow: none !important;
        }

        .isago-ik-upload-control .isago-ik-choose {
            border: 1px solid #007f9e !important;
            background: #007f9e !important;
            color: #fff !important;
        }

        .isago-ik-upload-control .isago-ik-choose:hover {
            border-color: #006b84 !important;
            background: #006b84 !important;
            color: #fff !important;
        }

        .isago-ik-upload-control .isago-ik-existing {
            border: 1px solid #cce5eb !important;
            background: #fff !important;
            color: #07566b !important;
        }

        .isago-ik-upload-control .isago-ik-existing:hover {
            border-color: #00a6c7 !important;
            background: #effbfe !important;
        }

        .isago-ik-upload-control .isago-ik-help {
            margin: 7px 0 0 !important;
            color: #687b83 !important;
            font-size: 11px !important;
            line-height: 1.65 !important;
        }

        .isago-ik-upload-control button:disabled {
            opacity: .6;
            cursor: not-allowed;
        }

     /* =========================================
           HIDE ONLY THE GENERIC GUTENBERG UPLOADER
           IN THE SIDEBAR.
           
           Keep Isago Featured Image meta-box
           buttons visible.
        ========================================= */

        .interface-complementary-area .isago-ik-upload-control,
        .editor-sidebar .isago-ik-upload-control,
        .edit-post-sidebar .isago-ik-upload-control,
        .block-editor-block-inspector .isago-ik-upload-control {
            display: none !important;
        }
/* =========================================
   FORCE ISAGO FEATURED IMAGE BUTTONS VISIBLE
========================================= */

#isago-post-featured-image .isago-imagekit-upload-button,
#isago-post-featured-image .isago-imagekit-select-existing-button,
.isago-featured-image-field .isago-imagekit-upload-button,
.isago-featured-image-field .isago-imagekit-select-existing-button {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    position: relative !important;
    width: 100% !important;
    min-height: 32px !important;
}
        /* =========================================
           EXISTING IMAGE PICKER
        ========================================= */

        .isago-ik-picker-overlay {
            position: fixed !important;
            inset: 0 !important;
            z-index: 1000000 !important;
            display: flex !important;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: rgba(15, 30, 40, .55) !important;
        }

        .isago-ik-picker-modal {
            width: min(900px, 100%) !important;
            max-height: 85vh !important;
            overflow: auto;
            padding: 20px !important;
            border: 1px solid #e0e8ec !important;
            border-radius: 14px !important;
            background: #fff !important;
            box-shadow: 0 20px 60px rgba(0, 0, 0, .2) !important;
        }

        .isago-ik-picker-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 15px;
        }

        .isago-ik-picker-header h2 {
            margin: 0;
            font-size: 18px;
        }

        .isago-ik-picker-search {
            width: 100%;
            min-height: 40px;
            margin-bottom: 12px;
            padding: 8px 12px;
            border: 1px solid #d2e0e5;
            border-radius: 8px;
        }

        .isago-ik-picker-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 12px;
        }

        .isago-ik-picker-card {
            display: flex;
            flex-direction: column;
            gap: 8px;
            min-width: 0;
            padding: 9px;
            border: 1px solid #e0e8ec;
            border-radius: 9px;
            background: #fff;
            cursor: pointer;
            text-align: left;
        }

        .isago-ik-picker-card:hover {
            border-color: #00a6c7;
            background: #f2fbfd;
        }

        .isago-ik-picker-card img,
        .isago-ik-picker-card video {
            width: 100%;
            height: 115px;
            object-fit: contain;
            background: #f4f7f8;
            border-radius: 5px;
        }

        .isago-ik-picker-card span {
            font-size: 12px;
            line-height: 1.45;
            overflow-wrap: anywhere;
        }

        @media (max-width: 700px) {
            .isago-ik-picker-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .isago-ik-upload-control {
                max-width: 100%;
                padding: 12px !important;
            }
        }

        @media (max-width: 420px) {
            .isago-ik-picker-grid {
                grid-template-columns: 1fr;
            }
        }





        
/* Standalone ImageKit uploader in main Gutenberg editor */
#isago-imagekit-main-uploader {
    width: min(100% - 32px, 760px);
    margin: 16px auto 24px;
    box-sizing: border-box;
    font-family: -apple-system, BlinkMacSystemFont,
        "Segoe UI", sans-serif;
}

#isago-imagekit-main-uploader .isago-ik-upload-control {
    width: 100% !important;
    max-width: none !important;
    margin: 0 !important;
    padding: 16px !important;
    border: 1px solid #dbe6ea !important;
    border-radius: 12px !important;
    background: #fff !important;
    box-shadow: 0 3px 14px rgba(0, 0, 0, .05) !important;
}

#isago-imagekit-main-uploader .isago-ik-toggle {
    width: 100% !important;
    border-radius: 8px !important;
}

#isago-imagekit-main-uploader .isago-ik-upload-options {
    display: flex !important;
    flex-direction: column !important;
    gap: 10px !important;
    width: 100% !important;
    margin-top: 12px !important;
}

#isago-imagekit-main-uploader .isago-ik-select {
    width: 100% !important;
    max-width: none !important;
}

#isago-imagekit-main-uploader .isago-ik-choose,
#isago-imagekit-main-uploader .isago-ik-existing {
    width: 100% !important;
    justify-content: center !important;
}

/* Remove custom ImageKit uploader from editor sidebar */
.interface-complementary-area .isago-ik-upload-control,
.interface-complementary-area .isago-imagekit-upload-button,
.interface-complementary-area .isago-imagekit-select-existing-button,
.editor-sidebar .isago-ik-upload-control,
.editor-sidebar .isago-imagekit-upload-button,
.editor-sidebar .isago-imagekit-select-existing-button {
    display: none !important;
}

    `;

    document.head.appendChild(style);
}



function registerMainEditorUploader() {
    if (!wp.element || !wp.data || !wp.blocks) {
        return;
    }

    var Uploader = mediaReplacement(null);
    var root = null;
    var mountNode = null;

    function insertSelectedMedia(selection) {
        var items = Array.isArray(selection)
            ? selection
            : [selection];

        var blocks = items.filter(function (item) {
            return item && item.url;
        }).map(function (item) {
            var isVideo = item.mime &&
                item.mime.indexOf('video/') === 0;

            return wp.blocks.createBlock(
                isVideo ? 'core/video' : 'core/image',
                {
                    url: item.url,
                    id: 0,
                    alt: item.alt || '',
                    caption: item.caption || ''
                }
            );
        });

        if (!blocks.length) return;

        var dispatcher = wp.data.dispatch('core/block-editor');

        if (dispatcher && dispatcher.insertBlocks) {
            dispatcher.insertBlocks(blocks);
        }
    }

    function mount() {
        if (mountNode && mountNode.isConnected) {
            return;
        }

        var editor = document.querySelector(
            '.edit-post-visual-editor, ' +
            '.editor-visual-editor'
        );

        if (!editor) return;

        if (root && typeof root.unmount === 'function') {
            root.unmount();
        }

        root = null;

        mountNode = document.createElement('div');
        mountNode.id = 'isago-imagekit-main-uploader';
        mountNode.className = 'isago-imagekit-main-uploader';

        editor.parentNode.insertBefore(mountNode, editor);

        var element = h(Uploader, {
            allowedTypes: ['image'],
            multiple: true,
            onSelect: insertSelectedMedia
        });

        if (typeof wp.element.createRoot === 'function') {
            root = wp.element.createRoot(mountNode);
            root.render(element);
        } else if (typeof wp.element.render === 'function') {
            wp.element.render(element, mountNode);
        }
    }

    mount();

    var observer = new MutationObserver(function () {
        if (!mountNode || !mountNode.isConnected) {
            mount();
        }
    });

    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
}





function initialize() {
    addStyles();

    registerMainEditorUploader();

    // Keep the MediaUpload integration for Gutenberg blocks
    // that use WordPress's media selection component.
    wp.hooks.addFilter(
        'editor.MediaUpload',
        'isago/imagekit-media-upload',
        mediaReplacement
    );
}


    if (wp.domReady) {
        wp.domReady(initialize);
    } else {
        initialize();
    }
})(window.jQuery, window, document);
