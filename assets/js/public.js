jQuery(document).ready(function($) {
    'use strict';

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

    // Media Upload Preview
    $('#wao-wcr-media-files').on('change', function() {
        var files = this.files;
        var previewContainer = $('.wao-wcr-media-preview');

        if (!previewContainer.length) {
            previewContainer = $('<div class="wao-wcr-media-preview"></div>');
            $(this).after(previewContainer);
        }

        previewContainer.empty();

        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            var reader = new FileReader();

            reader.onload = function(e) {
                var preview = $('<div class="preview-item"></div>');

                if (file.type.startsWith('image/')) {
                    preview.append('<img src="' + e.target.result + '" alt="Preview">');
                } else if (file.type.startsWith('video/')) {
                    preview.append('<video controls><source src="' + e.target.result + '"></video>');
                }

                previewContainer.append(preview);
            };

            reader.readAsDataURL(file);
        }
    });

    // Show Message
    function showMessage(message, type) {
        var messageClass = type === 'success' ? 'wao-wcr-success-message' : 'wao-wcr-error-message';
        var messageHtml = '<div class="' + messageClass + '">' + message + '</div>';

        $('.wao-wcr-reviews-wrapper').prepend(messageHtml);

        setTimeout(function() {
            $('.' + messageClass).fadeOut(function() {
                $(this).remove();
            });
        }, 3000);
    }

    // Star Rating Display
    $('.rating-select').on('change', function() {
        var rating = $(this).val();
        var starsDisplay = $(this).siblings('.stars-display');

        if (!starsDisplay.length) {
            starsDisplay = $('<span class="stars-display"></span>');
            $(this).after(starsDisplay);
        }

        var stars = '';
        for (var i = 0; i < 5; i++) {
            if (i < rating) {
                stars += '⭐';
            } else {
                stars += '☆';
            }
        }

        starsDisplay.html(stars);
    });

    // Image Lightbox (Simple Implementation)
    $(document).on('click', '.wao-wcr-media-item', function(e) {
        e.preventDefault();

        var imageUrl = $(this).attr('href');

        if (!imageUrl) {
            return;
        }

        var lightbox = $('<div class="wao-wcr-lightbox"></div>');
        var lightboxContent = $('<div class="lightbox-content"></div>');
        var closeBtn = $('<span class="lightbox-close">&times;</span>');
        var image = $('<img src="' + imageUrl + '" alt="Review Image">');

        lightboxContent.append(closeBtn, image);
        lightbox.append(lightboxContent);
        $('body').append(lightbox);

        lightbox.fadeIn();

        closeBtn.on('click', function() {
            lightbox.fadeOut(function() {
                lightbox.remove();
            });
        });

        lightbox.on('click', function(e) {
            if ($(e.target).is(lightbox)) {
                lightbox.fadeOut(function() {
                    lightbox.remove();
                });
            }
        });
    });

    // Add Lightbox Styles Dynamically
    if (!$('#wao-wcr-lightbox-styles').length) {
        var lightboxStyles = `
            <style id="wao-wcr-lightbox-styles">
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
            </style>
        `;
        $('head').append(lightboxStyles);
    }
});
