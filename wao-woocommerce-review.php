<?php
/**
 * Plugin Name: WaoWooCommerceReview
 * Plugin URI: https://github.com/Mrwowow/WaoWooCommerceReview
 * Description: Turn customer feedback into repeat sales. Reward customers with automatic discount coupons for leaving reviews, collect photo/video reviews, and automate review reminder emails.
 * Version: 1.0.0
 * Author: Morgan Victor
 * Author URI: https://github.com/Mrwowow
 * Text Domain: wao-woocommerce-review
 * Domain Path: /languages
 * Requires at least: 5.0
 * Requires PHP: 7.0
 * WC requires at least: 5.0
 * WC tested up to: 9.5
 * Requires Plugins: woocommerce
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('WAO_WCR_VERSION', '1.0.0');
define('WAO_WCR_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WAO_WCR_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WAO_WCR_PLUGIN_FILE', __FILE__);

// Check if WooCommerce is active
if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    add_action('admin_notices', 'wao_wcr_woocommerce_missing_notice');
    return;
}

function wao_wcr_woocommerce_missing_notice() {
    ?>
    <div class="notice notice-error">
        <p><?php _e('WaoWooCommerceReview requires WooCommerce to be installed and active.', 'wao-woocommerce-review'); ?></p>
    </div>
    <?php
}

// Include required files
require_once WAO_WCR_PLUGIN_DIR . 'includes/class-wao-wcr-database.php';
require_once WAO_WCR_PLUGIN_DIR . 'includes/class-wao-wcr-campaign.php';
require_once WAO_WCR_PLUGIN_DIR . 'includes/class-wao-wcr-coupon.php';
require_once WAO_WCR_PLUGIN_DIR . 'includes/class-wao-wcr-email.php';
require_once WAO_WCR_PLUGIN_DIR . 'includes/class-wao-wcr-review.php';
require_once WAO_WCR_PLUGIN_DIR . 'includes/class-wao-wcr-media.php';
require_once WAO_WCR_PLUGIN_DIR . 'includes/class-wao-wcr-analytics.php';
require_once WAO_WCR_PLUGIN_DIR . 'admin/class-wao-wcr-admin.php';
require_once WAO_WCR_PLUGIN_DIR . 'public/class-wao-wcr-public.php';

class WaoWooCommerceReview {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init_hooks();
    }

    private function init_hooks() {
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));

        add_action('plugins_loaded', array($this, 'init'));
    }

    public function init() {
        // Load text domain
        load_plugin_textdomain('wao-woocommerce-review', false, dirname(plugin_basename(__FILE__)) . '/languages');

        // Declare WooCommerce HPOS compatibility
        add_action('before_woocommerce_init', array($this, 'declare_woocommerce_compatibility'));

        // Initialize classes
        WAO_WCR_Database::get_instance();
        WAO_WCR_Campaign::get_instance();
        WAO_WCR_Coupon::get_instance();
        WAO_WCR_Email::get_instance();
        WAO_WCR_Review::get_instance();
        WAO_WCR_Media::get_instance();
        WAO_WCR_Analytics::get_instance();

        if (is_admin()) {
            WAO_WCR_Admin::get_instance();
        } else {
            WAO_WCR_Public::get_instance();
        }
    }

    public function declare_woocommerce_compatibility() {
        if (class_exists('\Automattic\WooCommerce\Utilities\FeaturesUtil')) {
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('custom_order_tables', __FILE__, true);
            \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility('cart_checkout_blocks', __FILE__, true);
        }
    }

    public function activate() {
        // Create database tables
        WAO_WCR_Database::create_tables();

        // Set default options
        $this->set_default_options();

        // Clear rewrite rules
        flush_rewrite_rules();
    }

    public function deactivate() {
        // Clear scheduled events
        wp_clear_scheduled_hook('wao_wcr_send_review_reminders');
        wp_clear_scheduled_hook('wao_wcr_delete_expired_coupons');

        // Clear rewrite rules
        flush_rewrite_rules();
    }

    private function set_default_options() {
        $defaults = array(
            'wao_wcr_enable_photo_reviews' => 'yes',
            'wao_wcr_enable_video_reviews' => 'yes',
            'wao_wcr_max_uploads_per_review' => 5,
            'wao_wcr_enable_review_editing' => 'yes',
            'wao_wcr_enable_helpful_voting' => 'yes',
            'wao_wcr_mask_reviewer_names' => 'no',
            'wao_wcr_sendgrid_api_key' => '',
            'wao_wcr_email_from_name' => get_bloginfo('name'),
            'wao_wcr_email_from_email' => get_option('admin_email'),
            'wao_wcr_review_primary_color' => '#0073aa',
            'wao_wcr_review_star_color' => '#ffa500',
            'wao_wcr_auto_delete_expired_coupons' => 'yes',
        );

        foreach ($defaults as $key => $value) {
            if (false === get_option($key)) {
                add_option($key, $value);
            }
        }
    }
}

// Initialize the plugin
function wao_wcr() {
    return WaoWooCommerceReview::get_instance();
}

wao_wcr();
