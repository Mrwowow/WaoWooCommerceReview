<?php
if (!defined('ABSPATH')) {
    exit;
}

class WAO_WCR_Analytics {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('comment_post', array($this, 'track_review_submitted'), 10, 2);
        add_action('wao_wcr_coupon_generated', array($this, 'track_coupon_generated'), 10, 3);
    }

    public function track_event($event_type, $event_data = array(), $user_id = 0, $product_id = 0) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'wao_wcr_analytics';

        return $wpdb->insert($table_name, array(
            'event_type' => $event_type,
            'event_data' => json_encode($event_data),
            'user_id' => $user_id,
            'product_id' => $product_id
        ));
    }

    public function track_review_submitted($comment_id, $comment_approved) {
        $comment = get_comment($comment_id);

        if (!$comment || $comment->comment_type !== 'review') {
            return;
        }

        $this->track_event('review_submitted', array(
            'comment_id' => $comment_id,
            'rating' => get_comment_meta($comment_id, 'rating', true)
        ), $comment->user_id, $comment->comment_post_ID);
    }

    public function track_coupon_generated($coupon_code, $user_id, $campaign) {
        $this->track_event('coupon_generated', array(
            'coupon_code' => $coupon_code,
            'campaign_id' => $campaign->id,
            'campaign_name' => $campaign->name,
            'coupon_amount' => $campaign->coupon_amount
        ), $user_id);
    }

    public function get_total_reviews() {
        global $wpdb;

        return $wpdb->get_var("
            SELECT COUNT(*) FROM {$wpdb->comments}
            WHERE comment_type = 'review'
            AND comment_approved = '1'
        ");
    }

    public function get_reviews_with_media() {
        return WAO_WCR_Media::get_instance()->get_reviews_with_media_count();
    }

    public function get_total_media_uploads() {
        return WAO_WCR_Media::get_instance()->get_total_media_count();
    }

    public function get_total_coupons_generated() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'wao_wcr_review_rewards';

        return $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
    }

    public function get_reviews_by_rating() {
        global $wpdb;

        $results = $wpdb->get_results("
            SELECT cm.meta_value as rating, COUNT(*) as count
            FROM {$wpdb->comments} c
            INNER JOIN {$wpdb->commentmeta} cm ON c.comment_ID = cm.comment_id
            WHERE c.comment_type = 'review'
            AND c.comment_approved = '1'
            AND cm.meta_key = 'rating'
            GROUP BY cm.meta_value
            ORDER BY cm.meta_value DESC
        ");

        $ratings = array(
            5 => 0,
            4 => 0,
            3 => 0,
            2 => 0,
            1 => 0
        );

        foreach ($results as $result) {
            $ratings[$result->rating] = absint($result->count);
        }

        return $ratings;
    }

    public function get_average_rating() {
        global $wpdb;

        return $wpdb->get_var("
            SELECT AVG(CAST(cm.meta_value AS DECIMAL(3,2)))
            FROM {$wpdb->comments} c
            INNER JOIN {$wpdb->commentmeta} cm ON c.comment_ID = cm.comment_id
            WHERE c.comment_type = 'review'
            AND c.comment_approved = '1'
            AND cm.meta_key = 'rating'
        ");
    }

    public function get_recent_reviews($limit = 10) {
        global $wpdb;

        return $wpdb->get_results($wpdb->prepare("
            SELECT c.*, cm.meta_value as rating
            FROM {$wpdb->comments} c
            INNER JOIN {$wpdb->commentmeta} cm ON c.comment_ID = cm.comment_id
            WHERE c.comment_type = 'review'
            AND c.comment_approved = '1'
            AND cm.meta_key = 'rating'
            ORDER BY c.comment_date DESC
            LIMIT %d
        ", $limit));
    }

    public function get_dashboard_stats() {
        return array(
            'total_reviews' => $this->get_total_reviews(),
            'reviews_with_media' => $this->get_reviews_with_media(),
            'total_media_uploads' => $this->get_total_media_uploads(),
            'total_coupons' => $this->get_total_coupons_generated(),
            'average_rating' => round($this->get_average_rating(), 2),
            'ratings_breakdown' => $this->get_reviews_by_rating()
        );
    }
}
