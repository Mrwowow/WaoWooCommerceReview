<?php
if (!defined('ABSPATH')) {
    exit;
}

class WAO_WCR_Public {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_public_assets'));
        add_filter('woocommerce_product_tabs', array($this, 'customize_review_tab'), 98);
        add_action('woocommerce_review_before_comment_meta', array($this, 'display_review_rating_summary'));
    }

    public function enqueue_public_assets() {
        if (is_product() || is_shop() || is_product_category()) {
            wp_enqueue_style('wao-wcr-public-css', WAO_WCR_PLUGIN_URL . 'assets/css/public.css', array(), WAO_WCR_VERSION);
            wp_enqueue_script('wao-wcr-public-js', WAO_WCR_PLUGIN_URL . 'assets/js/public.js', array('jquery'), WAO_WCR_VERSION, true);

            wp_localize_script('wao-wcr-public-js', 'waoWcrPublic', array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('wao_wcr_public_nonce')
            ));

            $custom_css = $this->generate_custom_css();
            wp_add_inline_style('wao-wcr-public-css', $custom_css);
        }
    }

    private function generate_custom_css() {
        $primary_color = get_option('wao_wcr_review_primary_color', '#0073aa');
        $star_color = get_option('wao_wcr_review_star_color', '#ffa500');

        $css = "
        .wao-wcr-review-media img {
            border: 2px solid {$primary_color};
        }

        .wao-wcr-vote-helpful {
            background-color: {$primary_color};
        }

        .wao-wcr-vote-helpful:hover {
            background-color: " . $this->adjust_brightness($primary_color, -20) . ";
        }

        .star-rating span::before,
        .star-rating::before {
            color: {$star_color} !important;
        }

        .wao-wcr-progress-bar-fill {
            background-color: {$star_color};
        }
        ";

        return $css;
    }

    private function adjust_brightness($hex, $steps) {
        $steps = max(-255, min(255, $steps));

        $hex = str_replace('#', '', $hex);
        if (strlen($hex) == 3) {
            $hex = str_repeat(substr($hex, 0, 1), 2) . str_repeat(substr($hex, 1, 1), 2) . str_repeat(substr($hex, 2, 1), 2);
        }

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $r = max(0, min(255, $r + $steps));
        $g = max(0, min(255, $g + $steps));
        $b = max(0, min(255, $b + $steps));

        return '#' . str_pad(dechex($r), 2, '0', STR_PAD_LEFT)
            . str_pad(dechex($g), 2, '0', STR_PAD_LEFT)
            . str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
    }

    public function customize_review_tab($tabs) {
        if (isset($tabs['reviews'])) {
            $tabs['reviews']['callback'] = array($this, 'custom_reviews_tab_content');
        }
        return $tabs;
    }

    public function custom_reviews_tab_content() {
        global $product;

        if (!comments_open()) {
            return;
        }

        include WAO_WCR_PLUGIN_DIR . 'public/templates/reviews-display.php';
    }

    public function display_review_rating_summary() {
        global $product;

        if (!$product) {
            return;
        }

        $rating_counts = $product->get_rating_counts();
        $total_reviews = array_sum($rating_counts);
        $average_rating = $product->get_average_rating();

        if ($total_reviews === 0) {
            return;
        }

        include WAO_WCR_PLUGIN_DIR . 'public/templates/rating-summary.php';
    }
}
