<?php
if (!defined('ABSPATH')) {
    exit;
}

class WAO_WCR_Admin {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function add_admin_menu() {
        add_menu_page(
            __('WaoWooCommerceReview', 'wao-woocommerce-review'),
            __('Review Rewards', 'wao-woocommerce-review'),
            'manage_woocommerce',
            'wao-wcr-dashboard',
            array($this, 'render_dashboard_page'),
            'dashicons-star-filled',
            56
        );

        add_submenu_page(
            'wao-wcr-dashboard',
            __('Dashboard', 'wao-woocommerce-review'),
            __('Dashboard', 'wao-woocommerce-review'),
            'manage_woocommerce',
            'wao-wcr-dashboard',
            array($this, 'render_dashboard_page')
        );

        add_submenu_page(
            'wao-wcr-dashboard',
            __('Campaigns', 'wao-woocommerce-review'),
            __('Campaigns', 'wao-woocommerce-review'),
            'manage_woocommerce',
            'wao-wcr-campaigns',
            array($this, 'render_campaigns_page')
        );

        add_submenu_page(
            'wao-wcr-dashboard',
            __('Email Templates', 'wao-woocommerce-review'),
            __('Email Templates', 'wao-woocommerce-review'),
            'manage_woocommerce',
            'wao-wcr-emails',
            array($this, 'render_emails_page')
        );

        add_submenu_page(
            'wao-wcr-dashboard',
            __('Settings', 'wao-woocommerce-review'),
            __('Settings', 'wao-woocommerce-review'),
            'manage_woocommerce',
            'wao-wcr-settings',
            array($this, 'render_settings_page')
        );

        add_submenu_page(
            'wao-wcr-dashboard',
            __('Analytics', 'wao-woocommerce-review'),
            __('Analytics', 'wao-woocommerce-review'),
            'manage_woocommerce',
            'wao-wcr-analytics',
            array($this, 'render_analytics_page')
        );
    }

    public function enqueue_admin_assets($hook) {
        if (strpos($hook, 'wao-wcr') === false) {
            return;
        }

        wp_enqueue_style('wao-wcr-admin-css', WAO_WCR_PLUGIN_URL . 'assets/css/admin.css', array(), WAO_WCR_VERSION);
        wp_enqueue_script('wao-wcr-admin-js', WAO_WCR_PLUGIN_URL . 'assets/js/admin.js', array('jquery'), WAO_WCR_VERSION, true);

        wp_localize_script('wao-wcr-admin-js', 'waoWcrAdmin', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wao_wcr_admin_nonce')
        ));
    }

    public function register_settings() {
        register_setting('wao_wcr_settings', 'wao_wcr_enable_photo_reviews');
        register_setting('wao_wcr_settings', 'wao_wcr_enable_video_reviews');
        register_setting('wao_wcr_settings', 'wao_wcr_max_uploads_per_review');
        register_setting('wao_wcr_settings', 'wao_wcr_enable_review_editing');
        register_setting('wao_wcr_settings', 'wao_wcr_enable_helpful_voting');
        register_setting('wao_wcr_settings', 'wao_wcr_mask_reviewer_names');
        register_setting('wao_wcr_settings', 'wao_wcr_sendgrid_api_key');
        register_setting('wao_wcr_settings', 'wao_wcr_email_from_name');
        register_setting('wao_wcr_settings', 'wao_wcr_email_from_email');
        register_setting('wao_wcr_settings', 'wao_wcr_review_primary_color');
        register_setting('wao_wcr_settings', 'wao_wcr_review_star_color');
        register_setting('wao_wcr_settings', 'wao_wcr_auto_delete_expired_coupons');
    }

    public function render_dashboard_page() {
        include WAO_WCR_PLUGIN_DIR . 'admin/views/dashboard.php';
    }

    public function render_campaigns_page() {
        include WAO_WCR_PLUGIN_DIR . 'admin/views/campaigns.php';
    }

    public function render_emails_page() {
        include WAO_WCR_PLUGIN_DIR . 'admin/views/emails.php';
    }

    public function render_settings_page() {
        include WAO_WCR_PLUGIN_DIR . 'admin/views/settings.php';
    }

    public function render_analytics_page() {
        include WAO_WCR_PLUGIN_DIR . 'admin/views/analytics.php';
    }
}
