<?php
if (!defined('ABSPATH')) {
    exit;
}

class WAO_WCR_Database {

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

    public static function create_tables() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        // Table for review campaigns
        $campaigns_table = $wpdb->prefix . 'wao_wcr_campaigns';
        $sql_campaigns = "CREATE TABLE IF NOT EXISTS $campaigns_table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            status varchar(20) DEFAULT 'active',
            review_count_required int(11) DEFAULT 1,
            target_products text,
            target_categories text,
            coupon_type varchar(50) NOT NULL,
            coupon_amount decimal(10,2) NOT NULL,
            coupon_validity_days int(11) DEFAULT 30,
            usage_limit int(11) DEFAULT 1,
            minimum_spend decimal(10,2) DEFAULT 0,
            free_shipping varchar(3) DEFAULT 'no',
            exclude_sale_items varchar(3) DEFAULT 'no',
            individual_use varchar(3) DEFAULT 'no',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";

        // Table for review media (photos/videos)
        $media_table = $wpdb->prefix . 'wao_wcr_review_media';
        $sql_media = "CREATE TABLE IF NOT EXISTS $media_table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            comment_id bigint(20) NOT NULL,
            media_type varchar(10) NOT NULL,
            file_url varchar(500) NOT NULL,
            file_name varchar(255) NOT NULL,
            file_size bigint(20) DEFAULT 0,
            uploaded_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY comment_id (comment_id)
        ) $charset_collate;";

        // Table for review rewards tracking
        $rewards_table = $wpdb->prefix . 'wao_wcr_review_rewards';
        $sql_rewards = "CREATE TABLE IF NOT EXISTS $rewards_table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            campaign_id bigint(20) NOT NULL,
            comment_id bigint(20) NOT NULL,
            coupon_code varchar(100) NOT NULL,
            coupon_id bigint(20) DEFAULT 0,
            status varchar(20) DEFAULT 'pending',
            sent_at datetime DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY campaign_id (campaign_id),
            KEY coupon_code (coupon_code)
        ) $charset_collate;";

        // Table for email campaigns
        $emails_table = $wpdb->prefix . 'wao_wcr_email_campaigns';
        $sql_emails = "CREATE TABLE IF NOT EXISTS $emails_table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            name varchar(255) NOT NULL,
            subject varchar(500) NOT NULL,
            content text NOT NULL,
            trigger_type varchar(50) NOT NULL,
            trigger_days int(11) DEFAULT 7,
            status varchar(20) DEFAULT 'active',
            header_color varchar(7) DEFAULT '#0073aa',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";

        // Table for email logs
        $email_logs_table = $wpdb->prefix . 'wao_wcr_email_logs';
        $sql_email_logs = "CREATE TABLE IF NOT EXISTS $email_logs_table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            order_id bigint(20) NOT NULL,
            campaign_id bigint(20) NOT NULL,
            email_address varchar(255) NOT NULL,
            status varchar(20) DEFAULT 'pending',
            sent_at datetime DEFAULT NULL,
            error_message text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id),
            KEY order_id (order_id)
        ) $charset_collate;";

        // Table for review analytics
        $analytics_table = $wpdb->prefix . 'wao_wcr_analytics';
        $sql_analytics = "CREATE TABLE IF NOT EXISTS $analytics_table (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            event_type varchar(50) NOT NULL,
            event_data text,
            user_id bigint(20) DEFAULT 0,
            product_id bigint(20) DEFAULT 0,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY event_type (event_type),
            KEY created_at (created_at)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql_campaigns);
        dbDelta($sql_media);
        dbDelta($sql_rewards);
        dbDelta($sql_emails);
        dbDelta($sql_email_logs);
        dbDelta($sql_analytics);

        // Store database version
        update_option('wao_wcr_db_version', WAO_WCR_VERSION);
    }

    public static function drop_tables() {
        global $wpdb;

        $tables = array(
            $wpdb->prefix . 'wao_wcr_campaigns',
            $wpdb->prefix . 'wao_wcr_review_media',
            $wpdb->prefix . 'wao_wcr_review_rewards',
            $wpdb->prefix . 'wao_wcr_email_campaigns',
            $wpdb->prefix . 'wao_wcr_email_logs',
            $wpdb->prefix . 'wao_wcr_analytics',
        );

        foreach ($tables as $table) {
            $wpdb->query("DROP TABLE IF EXISTS $table");
        }

        delete_option('wao_wcr_db_version');
    }
}
