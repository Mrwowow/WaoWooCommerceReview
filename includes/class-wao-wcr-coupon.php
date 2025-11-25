<?php
if (!defined('ABSPATH')) {
    exit;
}

class WAO_WCR_Coupon {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('comment_post', array($this, 'process_review_reward'), 10, 3);
        add_action('wao_wcr_delete_expired_coupons', array($this, 'delete_expired_coupons'));

        if (!wp_next_scheduled('wao_wcr_delete_expired_coupons')) {
            wp_schedule_event(time(), 'daily', 'wao_wcr_delete_expired_coupons');
        }
    }

    public function process_review_reward($comment_id, $comment_approved, $commentdata) {
        if ($comment_approved !== 1) {
            return;
        }

        $comment = get_comment($comment_id);

        if (!$comment || $comment->comment_type !== 'review') {
            return;
        }

        $product_id = $comment->comment_post_ID;
        $user_id = $comment->user_id;

        if (!$user_id) {
            return;
        }

        $campaigns = WAO_WCR_Campaign::get_instance()->get_active_campaigns();

        foreach ($campaigns as $campaign) {
            if ($this->should_trigger_campaign($campaign, $user_id, $product_id)) {
                $this->generate_and_send_coupon($campaign, $user_id, $comment_id);
            }
        }
    }

    private function should_trigger_campaign($campaign, $user_id, $product_id) {
        $target_products = json_decode($campaign->target_products, true);
        $target_categories = json_decode($campaign->target_categories, true);

        if (!empty($target_products) && !in_array($product_id, $target_products)) {
            return false;
        }

        if (!empty($target_categories)) {
            $product_categories = wp_get_post_terms($product_id, 'product_cat', array('fields' => 'ids'));
            if (!array_intersect($product_categories, $target_categories)) {
                return false;
            }
        }

        $review_count = $this->get_user_review_count($user_id);

        if ($review_count < $campaign->review_count_required) {
            return false;
        }

        $existing_reward = $this->get_user_campaign_reward($user_id, $campaign->id);
        if ($existing_reward) {
            return false;
        }

        return true;
    }

    private function get_user_review_count($user_id) {
        global $wpdb;

        return $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->comments}
            WHERE user_id = %d
            AND comment_approved = '1'
            AND comment_type = 'review'",
            $user_id
        ));
    }

    private function get_user_campaign_reward($user_id, $campaign_id) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'wao_wcr_review_rewards';

        return $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM $table_name WHERE user_id = %d AND campaign_id = %d",
            $user_id,
            $campaign_id
        ));
    }

    public function generate_and_send_coupon($campaign, $user_id, $comment_id) {
        $coupon_code = $this->generate_coupon_code();

        $coupon_id = $this->create_woocommerce_coupon($coupon_code, $campaign);

        if ($coupon_id) {
            $this->save_reward_record($user_id, $campaign->id, $comment_id, $coupon_code, $coupon_id);

            $user = get_user_by('id', $user_id);
            if ($user) {
                $this->send_coupon_email($user->user_email, $coupon_code, $campaign);
            }

            do_action('wao_wcr_coupon_generated', $coupon_code, $user_id, $campaign);

            return $coupon_code;
        }

        return false;
    }

    private function generate_coupon_code() {
        return 'REVIEW-' . strtoupper(wp_generate_password(8, false));
    }

    private function create_woocommerce_coupon($coupon_code, $campaign) {
        $coupon = new WC_Coupon();
        $coupon->set_code($coupon_code);
        $coupon->set_discount_type($campaign->coupon_type);
        $coupon->set_amount($campaign->coupon_amount);
        $coupon->set_individual_use($campaign->individual_use === 'yes');
        $coupon->set_usage_limit($campaign->usage_limit);
        $coupon->set_usage_limit_per_user(1);
        $coupon->set_minimum_amount($campaign->minimum_spend);
        $coupon->set_free_shipping($campaign->free_shipping === 'yes');
        $coupon->set_exclude_sale_items($campaign->exclude_sale_items === 'yes');

        if ($campaign->coupon_validity_days > 0) {
            $expiry_date = date('Y-m-d', strtotime('+' . $campaign->coupon_validity_days . ' days'));
            $coupon->set_date_expires($expiry_date);
        }

        $coupon_id = $coupon->save();

        return $coupon_id;
    }

    private function save_reward_record($user_id, $campaign_id, $comment_id, $coupon_code, $coupon_id) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'wao_wcr_review_rewards';

        return $wpdb->insert($table_name, array(
            'user_id' => $user_id,
            'campaign_id' => $campaign_id,
            'comment_id' => $comment_id,
            'coupon_code' => $coupon_code,
            'coupon_id' => $coupon_id,
            'status' => 'sent',
            'sent_at' => current_time('mysql')
        ));
    }

    private function send_coupon_email($email, $coupon_code, $campaign) {
        $subject = sprintf(__('Your Review Reward: %s Off!', 'wao-woocommerce-review'), wc_price($campaign->coupon_amount));

        $message = sprintf(
            __('Thank you for your review! Here is your exclusive discount code: %s', 'wao-woocommerce-review'),
            '<strong>' . $coupon_code . '</strong>'
        );

        $message .= '<br><br>';
        $message .= sprintf(__('Discount: %s', 'wao-woocommerce-review'), wc_price($campaign->coupon_amount));

        if ($campaign->coupon_validity_days > 0) {
            $expiry_date = date(get_option('date_format'), strtotime('+' . $campaign->coupon_validity_days . ' days'));
            $message .= '<br>' . sprintf(__('Valid until: %s', 'wao-woocommerce-review'), $expiry_date);
        }

        $headers = array('Content-Type: text/html; charset=UTF-8');

        return wp_mail($email, $subject, $message, $headers);
    }

    public function delete_expired_coupons() {
        if (get_option('wao_wcr_auto_delete_expired_coupons') !== 'yes') {
            return;
        }

        global $wpdb;

        $expired_coupons = $wpdb->get_results("
            SELECT ID FROM {$wpdb->posts}
            WHERE post_type = 'shop_coupon'
            AND post_status = 'publish'
            AND post_excerpt LIKE 'REVIEW-%'
            AND ID IN (
                SELECT post_id FROM {$wpdb->postmeta}
                WHERE meta_key = 'date_expires'
                AND meta_value < UNIX_TIMESTAMP()
                AND meta_value != ''
            )
        ");

        foreach ($expired_coupons as $coupon) {
            wp_delete_post($coupon->ID, true);
        }
    }
}
