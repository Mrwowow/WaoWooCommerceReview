<?php
if (!defined('ABSPATH')) {
    exit;
}

class WAO_WCR_Review {

    private static $instance = null;

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
    }

    public function add_media_upload_field($comment_form) {
        $enable_photo = get_option('wao_wcr_enable_photo_reviews') === 'yes';
        $enable_video = get_option('wao_wcr_enable_video_reviews') === 'yes';

        if (!$enable_photo && !$enable_video) {
            return $comment_form;
        }

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
        // Direct output method for themes that don't use the filter properly
        if (!is_product()) {
            return;
        }

        $enable_photo = get_option('wao_wcr_enable_photo_reviews') === 'yes';
        $enable_video = get_option('wao_wcr_enable_video_reviews') === 'yes';

        if (!$enable_photo && !$enable_video) {
            return;
        }

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
        if (!isset($_FILES['wao_wcr_media'])) {
            return;
        }

        $files = $_FILES['wao_wcr_media'];
        $max_uploads = absint(get_option('wao_wcr_max_uploads_per_review', 5));

        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }

        $file_count = count($files['name']);

        for ($i = 0; $i < min($file_count, $max_uploads); $i++) {
            if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                continue;
            }

            $file = array(
                'name' => $files['name'][$i],
                'type' => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error' => $files['error'][$i],
                'size' => $files['size'][$i]
            );

            $upload_overrides = array('test_form' => false);
            $uploaded = wp_handle_upload($file, $upload_overrides);

            if (!isset($uploaded['error'])) {
                $media_type = strpos($uploaded['type'], 'image') !== false ? 'image' : 'video';

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
                $media_html .= sprintf(
                    '<video class="wao-wcr-media-item" controls><source src="%s" type="video/mp4">%s</video>',
                    esc_url($media->file_url),
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
}
