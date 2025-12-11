<?php
if (!defined('ABSPATH')) {
    exit;
}

class WAO_WCR_Review {

    private static $instance = null;
    private static $upload_field_rendered = false;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_filter('comment_text', array($this, 'display_review_media'), 10, 2);
        add_action('comment_post', array($this, 'process_review_media_upload'), 10, 2);
        add_filter('woocommerce_product_review_comment_form_args', array($this, 'add_media_upload_field'), 99);
        add_action('comment_form_logged_in_after', array($this, 'add_media_upload_field_direct'));
        add_action('comment_form_after_fields', array($this, 'add_media_upload_field_direct'));
        add_action('wp_ajax_wao_wcr_vote_helpful', array($this, 'ajax_vote_helpful'));
        add_action('wp_ajax_nopriv_wao_wcr_vote_helpful', array($this, 'ajax_vote_helpful'));

        // AJAX upload handlers
        add_action('wp_ajax_wao_wcr_upload_media', array($this, 'ajax_upload_media'));
        add_action('wp_ajax_nopriv_wao_wcr_upload_media', array($this, 'ajax_upload_media'));
        add_action('wp_ajax_wao_wcr_attach_media', array($this, 'ajax_attach_media'));
        add_action('wp_ajax_nopriv_wao_wcr_attach_media', array($this, 'ajax_attach_media'));

        // Add allowed mime types for video uploads
        add_filter('upload_mimes', array($this, 'allow_video_uploads'));
    }

    public function allow_video_uploads($mimes) {
        $mimes['mp4'] = 'video/mp4';
        $mimes['mov'] = 'video/quicktime';
        $mimes['avi'] = 'video/x-msvideo';
        $mimes['wmv'] = 'video/x-ms-wmv';
        $mimes['webm'] = 'video/webm';
        $mimes['ogv'] = 'video/ogg';
        $mimes['m4v'] = 'video/x-m4v';
        return $mimes;
    }

    public function add_media_upload_field($comment_form) {
        // Prevent duplicate rendering
        if (self::$upload_field_rendered) {
            return $comment_form;
        }

        $enable_photo = get_option('wao_wcr_enable_photo_reviews') === 'yes';
        $enable_video = get_option('wao_wcr_enable_video_reviews') === 'yes';

        if (!$enable_photo && !$enable_video) {
            return $comment_form;
        }

        self::$upload_field_rendered = true;

        // CRITICAL: Set enctype for file uploads
        $comment_form['format'] = 'html5';
        if (!isset($comment_form['form_attributes'])) {
            $comment_form['form_attributes'] = array();
        }
        $comment_form['form_attributes']['enctype'] = 'multipart/form-data';

        $max_uploads = absint(get_option('wao_wcr_max_uploads_per_review', 5));

        $accepted_types = array();
        if ($enable_photo) {
            $accepted_types[] = 'image/*';
        }
        if ($enable_video) {
            $accepted_types[] = 'video/*';
        }

        $accept_attr = implode(',', $accepted_types);

        $upload_field = '<p class="wao-wcr-media-upload">';
        $upload_field .= '<label for="wao-wcr-media-files">' . __('Upload Photos/Videos (Optional)', 'wao-woocommerce-review') . '</label>';
        $upload_field .= '<input type="file" id="wao-wcr-media-files" name="wao_wcr_media[]" accept="' . esc_attr($accept_attr) . '" multiple max="' . $max_uploads . '">';
        $upload_field .= '<small>' . sprintf(__('You can upload up to %d files', 'wao-woocommerce-review'), $max_uploads) . '</small>';
        $upload_field .= '</p>';

        $comment_form['comment_field'] .= $upload_field;

        return $comment_form;
    }

    public function add_media_upload_field_direct() {
        // Prevent duplicate rendering
        if (self::$upload_field_rendered) {
            return;
        }

        // Direct output method for themes that don't use the filter properly
        if (!is_product()) {
            return;
        }

        $enable_photo = get_option('wao_wcr_enable_photo_reviews') === 'yes';
        $enable_video = get_option('wao_wcr_enable_video_reviews') === 'yes';

        if (!$enable_photo && !$enable_video) {
            return;
        }

        self::$upload_field_rendered = true;

        $max_uploads = absint(get_option('wao_wcr_max_uploads_per_review', 5));

        $accepted_types = array();
        if ($enable_photo) {
            $accepted_types[] = 'image/*';
        }
        if ($enable_video) {
            $accepted_types[] = 'video/*';
        }

        $accept_attr = implode(',', $accepted_types);
        ?>
        <p class="wao-wcr-media-upload comment-form-media">
            <label for="wao-wcr-media-files"><?php _e('Upload Photos/Videos (Optional)', 'wao-woocommerce-review'); ?></label>
            <input type="file" id="wao-wcr-media-files" name="wao_wcr_media[]" accept="<?php echo esc_attr($accept_attr); ?>" multiple style="display:block; width:100%; padding:10px; border:2px dashed #ccc; border-radius:4px; background:#f9f9f9;">
            <small style="display:block; margin-top:5px; color:#666;"><?php printf(__('You can upload up to %d files', 'wao-woocommerce-review'), $max_uploads); ?></small>
        </p>
        <?php
    }

    public function process_review_media_upload($comment_id, $comment_approved) {
        // First, check for AJAX-uploaded files (new method using temp IDs)
        if (isset($_POST['wao_wcr_temp_ids']) && !empty($_POST['wao_wcr_temp_ids'])) {
            $temp_ids = array_filter(array_map('sanitize_text_field', explode(',', $_POST['wao_wcr_temp_ids'])));
            $max_uploads = absint(get_option('wao_wcr_max_uploads_per_review', 5));
            $attached = 0;

            foreach ($temp_ids as $temp_id) {
                if ($attached >= $max_uploads) {
                    break;
                }

                $upload_data = get_transient($temp_id);
                if ($upload_data && is_array($upload_data)) {
                    WAO_WCR_Media::get_instance()->save_review_media(
                        $comment_id,
                        $upload_data['type'],
                        $upload_data['url'],
                        $upload_data['name'],
                        $upload_data['size']
                    );
                    delete_transient($temp_id);
                    $attached++;
                }
            }

            // If we attached files via AJAX, we're done
            if ($attached > 0) {
                return;
            }
        }

        // Fallback: Check if files were uploaded directly via form (legacy method)
        if (!isset($_FILES['wao_wcr_media']) || empty($_FILES['wao_wcr_media']['name'][0])) {
            return;
        }

        $files = $_FILES['wao_wcr_media'];
        $max_uploads = absint(get_option('wao_wcr_max_uploads_per_review', 5));

        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }

        // Handle array of files
        $file_count = is_array($files['name']) ? count($files['name']) : 1;

        for ($i = 0; $i < min($file_count, $max_uploads); $i++) {
            // Skip if no file or has errors
            if (empty($files['name'][$i]) || $files['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }

            $file = array(
                'name' => $files['name'][$i],
                'type' => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error' => $files['error'][$i],
                'size' => $files['size'][$i]
            );

            // Allow file upload without test_form check
            $upload_overrides = array(
                'test_form' => false,
                'test_type' => true
            );

            $uploaded = wp_handle_upload($file, $upload_overrides);

            // Check if upload was successful
            if (!isset($uploaded['error']) && isset($uploaded['url'])) {
                // Determine media type from MIME type
                $media_type = 'video'; // default
                if (isset($uploaded['type'])) {
                    if (strpos($uploaded['type'], 'image') !== false) {
                        $media_type = 'image';
                    } elseif (strpos($uploaded['type'], 'video') !== false) {
                        $media_type = 'video';
                    }
                }

                // Save to database
                WAO_WCR_Media::get_instance()->save_review_media(
                    $comment_id,
                    $media_type,
                    $uploaded['url'],
                    basename($uploaded['file']),
                    $file['size']
                );
            }
        }
    }

    public function display_review_media($comment_text, $comment) {
        if (!$comment || $comment->comment_type !== 'review') {
            return $comment_text;
        }

        $media_items = WAO_WCR_Media::get_instance()->get_review_media($comment->comment_ID);

        if (empty($media_items)) {
            return $comment_text;
        }

        $media_html = '<div class="wao-wcr-review-media">';

        foreach ($media_items as $media) {
            if ($media->media_type === 'image') {
                $media_html .= sprintf(
                    '<a href="%s" class="wao-wcr-media-item" data-lightbox="review-%d"><img src="%s" alt="%s"></a>',
                    esc_url($media->file_url),
                    $comment->comment_ID,
                    esc_url($media->file_url),
                    esc_attr($media->file_name)
                );
            } elseif ($media->media_type === 'video') {
                // Detect video MIME type from file extension
                $file_ext = strtolower(pathinfo($media->file_url, PATHINFO_EXTENSION));
                $mime_types = array(
                    'mp4'  => 'video/mp4',
                    'webm' => 'video/webm',
                    'ogg'  => 'video/ogg',
                    'mov'  => 'video/quicktime',
                    'avi'  => 'video/x-msvideo',
                    'wmv'  => 'video/x-ms-wmv',
                    'm4v'  => 'video/x-m4v'
                );
                $mime_type = isset($mime_types[$file_ext]) ? $mime_types[$file_ext] : 'video/mp4';

                $media_html .= sprintf(
                    '<video class="wao-wcr-media-item wao-wcr-video-review" controls preload="metadata" style="max-width:100%%; height:auto;"><source src="%s" type="%s">%s</video>',
                    esc_url($media->file_url),
                    esc_attr($mime_type),
                    __('Your browser does not support the video tag.', 'wao-woocommerce-review')
                );
            }
        }

        $media_html .= '</div>';

        $helpful_count = get_comment_meta($comment->comment_ID, 'wao_wcr_helpful_count', true);
        $helpful_count = $helpful_count ? absint($helpful_count) : 0;

        $helpful_html = '<div class="wao-wcr-helpful-voting">';
        $helpful_html .= '<button class="wao-wcr-vote-helpful" data-comment-id="' . $comment->comment_ID . '">';
        $helpful_html .= sprintf(__('Helpful (%d)', 'wao-woocommerce-review'), $helpful_count);
        $helpful_html .= '</button>';
        $helpful_html .= '</div>';

        return $comment_text . $media_html . $helpful_html;
    }

    public function ajax_vote_helpful() {
        if (!isset($_POST['comment_id'])) {
            wp_send_json_error(array('message' => __('Invalid request', 'wao-woocommerce-review')));
        }

        $comment_id = absint($_POST['comment_id']);
        $user_id = get_current_user_id();

        $voted_comments = get_user_meta($user_id, 'wao_wcr_voted_helpful', true);
        if (!is_array($voted_comments)) {
            $voted_comments = array();
        }

        if (in_array($comment_id, $voted_comments)) {
            wp_send_json_error(array('message' => __('You have already voted for this review', 'wao-woocommerce-review')));
        }

        $helpful_count = get_comment_meta($comment_id, 'wao_wcr_helpful_count', true);
        $helpful_count = $helpful_count ? absint($helpful_count) : 0;
        $helpful_count++;

        update_comment_meta($comment_id, 'wao_wcr_helpful_count', $helpful_count);

        $voted_comments[] = $comment_id;
        update_user_meta($user_id, 'wao_wcr_voted_helpful', $voted_comments);

        wp_send_json_success(array(
            'message' => __('Thank you for your feedback!', 'wao-woocommerce-review'),
            'count' => $helpful_count
        ));
    }

    /**
     * AJAX handler for uploading media files
     * Files are uploaded first, then attached to comment after submission
     */
    public function ajax_upload_media() {
        // Check nonce
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wao_wcr_public_nonce')) {
            wp_send_json_error(array('message' => __('Security check failed', 'wao-woocommerce-review')));
        }

        if (!isset($_FILES['file'])) {
            wp_send_json_error(array('message' => __('No file uploaded', 'wao-woocommerce-review')));
        }

        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }

        $file = $_FILES['file'];

        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $error_messages = array(
                UPLOAD_ERR_INI_SIZE => 'File exceeds server limit',
                UPLOAD_ERR_FORM_SIZE => 'File exceeds form limit',
                UPLOAD_ERR_PARTIAL => 'File only partially uploaded',
                UPLOAD_ERR_NO_FILE => 'No file uploaded',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file',
                UPLOAD_ERR_EXTENSION => 'Upload blocked by extension'
            );
            $message = isset($error_messages[$file['error']]) ? $error_messages[$file['error']] : 'Unknown upload error';
            wp_send_json_error(array('message' => $message));
        }

        // Check file size (50MB max)
        $max_size = 50 * 1024 * 1024;
        if ($file['size'] > $max_size) {
            wp_send_json_error(array('message' => __('File is too large. Maximum size is 50MB.', 'wao-woocommerce-review')));
        }

        $upload_overrides = array(
            'test_form' => false,
            'test_type' => true
        );

        $uploaded = wp_handle_upload($file, $upload_overrides);

        if (isset($uploaded['error'])) {
            wp_send_json_error(array('message' => $uploaded['error']));
        }

        // Determine media type
        $media_type = 'image';
        if (isset($uploaded['type']) && strpos($uploaded['type'], 'video') !== false) {
            $media_type = 'video';
        }

        // Generate a temporary ID to track this upload
        $temp_id = 'wao_' . uniqid();

        // Store in transient for later attachment to comment
        $upload_data = array(
            'url' => $uploaded['url'],
            'file' => $uploaded['file'],
            'type' => $media_type,
            'name' => basename($uploaded['file']),
            'size' => $file['size']
        );
        set_transient($temp_id, $upload_data, HOUR_IN_SECONDS);

        wp_send_json_success(array(
            'temp_id' => $temp_id,
            'url' => $uploaded['url'],
            'type' => $media_type,
            'name' => basename($uploaded['file'])
        ));
    }

    /**
     * AJAX handler for attaching uploaded media to a comment
     */
    public function ajax_attach_media() {
        if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'wao_wcr_public_nonce')) {
            wp_send_json_error(array('message' => __('Security check failed', 'wao-woocommerce-review')));
        }

        $comment_id = isset($_POST['comment_id']) ? absint($_POST['comment_id']) : 0;
        $temp_ids = isset($_POST['temp_ids']) ? (array) $_POST['temp_ids'] : array();

        if (!$comment_id || empty($temp_ids)) {
            wp_send_json_error(array('message' => __('Invalid request', 'wao-woocommerce-review')));
        }

        $attached = 0;
        foreach ($temp_ids as $temp_id) {
            $upload_data = get_transient($temp_id);
            if ($upload_data) {
                WAO_WCR_Media::get_instance()->save_review_media(
                    $comment_id,
                    $upload_data['type'],
                    $upload_data['url'],
                    $upload_data['name'],
                    $upload_data['size']
                );
                delete_transient($temp_id);
                $attached++;
            }
        }

        wp_send_json_success(array(
            'message' => sprintf(__('%d files attached', 'wao-woocommerce-review'), $attached),
            'attached' => $attached
        ));
    }
}
