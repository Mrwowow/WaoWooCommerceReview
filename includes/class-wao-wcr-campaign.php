<?php
if (!defined('ABSPATH')) {
    exit;
}

class WAO_WCR_Campaign {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('wp_ajax_wao_wcr_create_campaign', array($this, 'ajax_create_campaign'));
        add_action('wp_ajax_wao_wcr_update_campaign', array($this, 'ajax_update_campaign'));
        add_action('wp_ajax_wao_wcr_delete_campaign', array($this, 'ajax_delete_campaign'));
        add_action('wp_ajax_wao_wcr_get_campaigns', array($this, 'ajax_get_campaigns'));
    }

    public function create_campaign($data) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'wao_wcr_campaigns';

        $insert_data = array(
            'name' => sanitize_text_field($data['name']),
            'status' => isset($data['status']) ? sanitize_text_field($data['status']) : 'active',
            'review_count_required' => absint($data['review_count_required']),
            'target_products' => isset($data['target_products']) ? json_encode($data['target_products']) : '',
            'target_categories' => isset($data['target_categories']) ? json_encode($data['target_categories']) : '',
            'coupon_type' => sanitize_text_field($data['coupon_type']),
            'coupon_amount' => floatval($data['coupon_amount']),
            'coupon_validity_days' => absint($data['coupon_validity_days']),
            'usage_limit' => absint($data['usage_limit']),
            'minimum_spend' => floatval($data['minimum_spend']),
            'free_shipping' => isset($data['free_shipping']) ? 'yes' : 'no',
            'exclude_sale_items' => isset($data['exclude_sale_items']) ? 'yes' : 'no',
            'individual_use' => isset($data['individual_use']) ? 'yes' : 'no',
        );

        $result = $wpdb->insert($table_name, $insert_data);

        if ($result) {
            return $wpdb->insert_id;
        }

        return false;
    }

    public function update_campaign($campaign_id, $data) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'wao_wcr_campaigns';

        $update_data = array(
            'name' => sanitize_text_field($data['name']),
            'status' => sanitize_text_field($data['status']),
            'review_count_required' => absint($data['review_count_required']),
            'target_products' => isset($data['target_products']) ? json_encode($data['target_products']) : '',
            'target_categories' => isset($data['target_categories']) ? json_encode($data['target_categories']) : '',
            'coupon_type' => sanitize_text_field($data['coupon_type']),
            'coupon_amount' => floatval($data['coupon_amount']),
            'coupon_validity_days' => absint($data['coupon_validity_days']),
            'usage_limit' => absint($data['usage_limit']),
            'minimum_spend' => floatval($data['minimum_spend']),
            'free_shipping' => isset($data['free_shipping']) ? 'yes' : 'no',
            'exclude_sale_items' => isset($data['exclude_sale_items']) ? 'yes' : 'no',
            'individual_use' => isset($data['individual_use']) ? 'yes' : 'no',
        );

        return $wpdb->update(
            $table_name,
            $update_data,
            array('id' => $campaign_id)
        );
    }

    public function delete_campaign($campaign_id) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'wao_wcr_campaigns';

        return $wpdb->delete($table_name, array('id' => $campaign_id));
    }

    public function get_campaign($campaign_id) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'wao_wcr_campaigns';

        return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $campaign_id));
    }

    public function get_all_campaigns($status = 'all') {
        global $wpdb;

        $table_name = $wpdb->prefix . 'wao_wcr_campaigns';

        if ($status === 'all') {
            return $wpdb->get_results("SELECT * FROM $table_name ORDER BY created_at DESC");
        } else {
            return $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE status = %s ORDER BY created_at DESC", $status));
        }
    }

    public function get_active_campaigns() {
        return $this->get_all_campaigns('active');
    }

    public function ajax_create_campaign() {
        check_ajax_referer('wao_wcr_admin_nonce', 'nonce');

        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(array('message' => __('Permission denied', 'wao-woocommerce-review')));
        }

        $campaign_id = $this->create_campaign($_POST['campaign']);

        if ($campaign_id) {
            wp_send_json_success(array(
                'message' => __('Campaign created successfully', 'wao-woocommerce-review'),
                'campaign_id' => $campaign_id
            ));
        } else {
            wp_send_json_error(array('message' => __('Failed to create campaign', 'wao-woocommerce-review')));
        }
    }

    public function ajax_update_campaign() {
        check_ajax_referer('wao_wcr_admin_nonce', 'nonce');

        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(array('message' => __('Permission denied', 'wao-woocommerce-review')));
        }

        $campaign_id = absint($_POST['campaign_id']);
        $result = $this->update_campaign($campaign_id, $_POST['campaign']);

        if ($result !== false) {
            wp_send_json_success(array('message' => __('Campaign updated successfully', 'wao-woocommerce-review')));
        } else {
            wp_send_json_error(array('message' => __('Failed to update campaign', 'wao-woocommerce-review')));
        }
    }

    public function ajax_delete_campaign() {
        check_ajax_referer('wao_wcr_admin_nonce', 'nonce');

        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(array('message' => __('Permission denied', 'wao-woocommerce-review')));
        }

        $campaign_id = absint($_POST['campaign_id']);
        $result = $this->delete_campaign($campaign_id);

        if ($result) {
            wp_send_json_success(array('message' => __('Campaign deleted successfully', 'wao-woocommerce-review')));
        } else {
            wp_send_json_error(array('message' => __('Failed to delete campaign', 'wao-woocommerce-review')));
        }
    }

    public function ajax_get_campaigns() {
        check_ajax_referer('wao_wcr_admin_nonce', 'nonce');

        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(array('message' => __('Permission denied', 'wao-woocommerce-review')));
        }

        $campaigns = $this->get_all_campaigns();

        wp_send_json_success(array('campaigns' => $campaigns));
    }
}
