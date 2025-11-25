<?php
if (!defined('ABSPATH')) {
    exit;
}

class WAO_WCR_Media {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Constructor
    }

    public function save_review_media($comment_id, $media_type, $file_url, $file_name, $file_size) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'wao_wcr_review_media';

        return $wpdb->insert($table_name, array(
            'comment_id' => $comment_id,
            'media_type' => $media_type,
            'file_url' => $file_url,
            'file_name' => $file_name,
            'file_size' => $file_size
        ));
    }

    public function get_review_media($comment_id) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'wao_wcr_review_media';

        return $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM $table_name WHERE comment_id = %d ORDER BY uploaded_at ASC",
            $comment_id
        ));
    }

    public function delete_review_media($media_id) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'wao_wcr_review_media';

        $media = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $media_id));

        if ($media) {
            $file_path = str_replace(wp_upload_dir()['baseurl'], wp_upload_dir()['basedir'], $media->file_url);
            if (file_exists($file_path)) {
                unlink($file_path);
            }

            return $wpdb->delete($table_name, array('id' => $media_id));
        }

        return false;
    }

    public function get_total_media_count() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'wao_wcr_review_media';

        return $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    }

    public function get_reviews_with_media_count() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'wao_wcr_review_media';

        return $wpdb->get_var("SELECT COUNT(DISTINCT comment_id) FROM $table_name");
    }
}
