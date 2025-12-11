jQuery(document).ready(function($) {
    'use strict';

    // Store uploaded file temp IDs
    var uploadedFiles = [];
    var isUploading = false;

    // File size limits
    var maxFileSize = 50 * 1024 * 1024; // 50MB max per file
    var maxTotalSize = 100 * 1024 * 1024; // 100MB total max

    // File input change handler - upload files via AJAX immediately
    $('#wao-wcr-media-files').on('change', function() {
        var files = this.files;
        var totalSize = 0;
        var validFiles = [];

        // Validate files first
        for (var i = 0; i < files.length; i++) {
            totalSize += files[i].size;

            if (files[i].size > maxFileSize) {
                alert('File "' + files[i].name + '" is too large. Maximum file size is 50MB.');
                $(this).val('');
                return;
            }

            validFiles.push(files[i]);
        }

        if (totalSize > maxTotalSize) {
            alert('Total file size exceeds 100MB. Please select fewer or smaller files.');
            $(this).val('');
            return;
        }

        // Upload each file via AJAX
        if (validFiles.length > 0) {
            uploadFilesAjax(validFiles);
        }
    });

    // Upload files via AJAX
    function uploadFilesAjax(files) {
        var $input = $('#wao-wcr-media-files');
        var $container = $input.closest('.wao-wcr-media-upload');

        // Create or get preview container
        var $preview = $container.find('.wao-wcr-upload-preview');
        if (!$preview.length) {
            $preview = $('<div class="wao-wcr-upload-preview"></div>');
            $container.append($preview);
        }

        // Show uploading status
        var $status = $container.find('.wao-wcr-upload-status');
        if (!$status.length) {
            $status = $('<div class="wao-wcr-upload-status"></div>');
            $container.append($status);
        }

        isUploading = true;
        var uploadCount = 0;
        var totalFiles = files.length;

        for (var i = 0; i < files.length; i++) {
            (function(file) {
                var formData = new FormData();
                formData.append('action', 'wao_wcr_upload_media');
                formData.append('nonce', waoWcrPublic.nonce);
                formData.append('file', file);

                $status.html('Uploading ' + (uploadCount + 1) + ' of ' + totalFiles + '...');

                $.ajax({
                    url: waoWcrPublic.ajaxUrl,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        uploadCount++;

                        if (response.success) {
                            uploadedFiles.push(response.data.temp_id);

                            // Add preview
                            var previewHtml = '';
                            if (response.data.type === 'image') {
                                previewHtml = '<div class="wao-wcr-preview-item" data-temp-id="' + response.data.temp_id + '">' +
                                    '<img src="' + response.data.url + '" alt="' + response.data.name + '">' +
                                    '<span class="remove-preview">&times;</span>' +
                                    '</div>';
                            } else {
                                previewHtml = '<div class="wao-wcr-preview-item" data-temp-id="' + response.data.temp_id + '">' +
                                    '<video src="' + response.data.url + '"></video>' +
                                    '<span class="remove-preview">&times;</span>' +
                                    '</div>';
                            }
                            $preview.append(previewHtml);
                        } else {
                            alert('Upload failed: ' + response.data.message);
                        }

                        if (uploadCount >= totalFiles) {
                            isUploading = false;
                            $status.html(uploadedFiles.length + ' file(s) ready');
                        }
                    },
                    error: function() {
                        uploadCount++;
                        alert('Upload failed for ' + file.name);

                        if (uploadCount >= totalFiles) {
                            isUploading = false;
                            $status.html(uploadedFiles.length + ' file(s) ready');
                        }
                    }
                });
            })(files[i]);
        }
    }

    // Remove preview item
    $(document).on('click', '.wao-wcr-preview-item .remove-preview', function() {
        var $item = $(this).closest('.wao-wcr-preview-item');
        var tempId = $item.data('temp-id');

        // Remove from array
        var index = uploadedFiles.indexOf(tempId);
        if (index > -1) {
            uploadedFiles.splice(index, 1);
        }

        $item.remove();

        // Update status
        var $status = $('.wao-wcr-upload-status');
        if (uploadedFiles.length > 0) {
            $status.html(uploadedFiles.length + ' file(s) ready');
        } else {
            $status.html('');
        }
    });

    // Store temp IDs in hidden field before form submit
    var reviewForm = $('#commentform, #review_form_wrapper form, .comment-form');

    if (reviewForm.length) {
        // Add hidden field for temp IDs
        if (!$('#wao_wcr_temp_ids').length) {
            reviewForm.append('<input type="hidden" id="wao_wcr_temp_ids" name="wao_wcr_temp_ids" value="">');
        }

        // Update hidden field on form submit
        reviewForm.on('submit', function(e) {
            if (isUploading) {
                e.preventDefault();
                alert('Please wait for uploads to complete.');
                return false;
            }

            // Store temp IDs in hidden field
            $('#wao_wcr_temp_ids').val(uploadedFiles.join(','));
        });
    }

    // Helpful Vote Handler
    $('.wao-wcr-vote-helpful').on('click', function(e) {
        e.preventDefault();

        var button = $(this);
        var commentId = button.data('comment-id');

        if (button.prop('disabled')) {
            return;
        }

        button.prop('disabled', true);

        $.ajax({
            url: waoWcrPublic.ajaxUrl,
            type: 'POST',
            data: {
                action: 'wao_wcr_vote_helpful',
                comment_id: commentId,
                nonce: waoWcrPublic.nonce
            },
            success: function(response) {
                if (response.success) {
                    button.text('Helpful (' + response.data.count + ')');
                    showMessage(response.data.message, 'success');
                } else {
                    button.prop('disabled', false);
                    showMessage(response.data.message, 'error');
                }
            },
            error: function() {
                button.prop('disabled', false);
                showMessage('An error occurred. Please try again.', 'error');
            }
        });
    });

    // Show Message
    function showMessage(message, type) {
        var messageClass = type === 'success' ? 'wao-wcr-success-message' : 'wao-wcr-error-message';
        var messageHtml = '<div class="' + messageClass + '">' + message + '</div>';

        $('.wao-wcr-reviews-wrapper, #reviews').first().prepend(messageHtml);

        setTimeout(function() {
            $('.' + messageClass).fadeOut(function() {
                $(this).remove();
            });
        }, 3000);
    }

    // Image Lightbox
    $(document).on('click', '.wao-wcr-media-item[data-lightbox]', function(e) {
        e.preventDefault();

        var imageUrl = $(this).attr('href');
        if (!imageUrl) return;

        var lightbox = $('<div class="wao-wcr-lightbox"></div>');
        var lightboxContent = $('<div class="lightbox-content"></div>');
        var closeBtn = $('<span class="lightbox-close">&times;</span>');
        var image = $('<img src="' + imageUrl + '" alt="Review Image">');

        lightboxContent.append(closeBtn, image);
        lightbox.append(lightboxContent);
        $('body').append(lightbox);
        lightbox.fadeIn();

        closeBtn.on('click', function() {
            lightbox.fadeOut(function() { lightbox.remove(); });
        });

        lightbox.on('click', function(e) {
            if ($(e.target).is(lightbox)) {
                lightbox.fadeOut(function() { lightbox.remove(); });
            }
        });
    });

    // Add Lightbox & Preview Styles
    if (!$('#wao-wcr-dynamic-styles').length) {
        var styles = `
            <style id="wao-wcr-dynamic-styles">
                .wao-wcr-lightbox {
                    display: none;
                    position: fixed;
                    z-index: 999999;
                    left: 0;
                    top: 0;
                    width: 100%;
                    height: 100%;
                    background-color: rgba(0, 0, 0, 0.9);
                }
                .lightbox-content {
                    position: relative;
                    margin: auto;
                    padding: 20px;
                    max-width: 90%;
                    max-height: 90%;
                    top: 50%;
                    transform: translateY(-50%);
                    text-align: center;
                }
                .lightbox-content img {
                    max-width: 100%;
                    max-height: 80vh;
                    border-radius: 8px;
                }
                .lightbox-close {
                    position: absolute;
                    top: 10px;
                    right: 25px;
                    color: #fff;
                    font-size: 40px;
                    font-weight: bold;
                    cursor: pointer;
                }
                .lightbox-close:hover {
                    color: #ccc;
                }
                .wao-wcr-upload-preview {
                    display: flex;
                    flex-wrap: wrap;
                    gap: 10px;
                    margin-top: 10px;
                }
                .wao-wcr-preview-item {
                    position: relative;
                    width: 80px;
                    height: 80px;
                    border-radius: 8px;
                    overflow: hidden;
                    border: 2px solid #ddd;
                }
                .wao-wcr-preview-item img,
                .wao-wcr-preview-item video {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                }
                .wao-wcr-preview-item .remove-preview {
                    position: absolute;
                    top: 2px;
                    right: 2px;
                    background: rgba(255,0,0,0.8);
                    color: white;
                    width: 20px;
                    height: 20px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    cursor: pointer;
                    font-size: 14px;
                    line-height: 1;
                }
                .wao-wcr-preview-item .remove-preview:hover {
                    background: red;
                }
                .wao-wcr-upload-status {
                    margin-top: 8px;
                    font-size: 13px;
                    color: #666;
                }
            </style>
        `;
        $('head').append(styles);
    }
});
