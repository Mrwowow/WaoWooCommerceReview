<?php
if (!defined('ABSPATH')) {
    exit;
}

$analytics = WAO_WCR_Analytics::get_instance();
$stats = $analytics->get_dashboard_stats();
?>

<div class="wrap wao-wcr-dashboard">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <div class="wao-wcr-stats-grid">
        <div class="wao-wcr-stat-card">
            <div class="stat-icon">⭐</div>
            <div class="stat-content">
                <h3><?php echo number_format($stats['total_reviews']); ?></h3>
                <p><?php _e('Total Reviews', 'wao-woocommerce-review'); ?></p>
            </div>
        </div>

        <div class="wao-wcr-stat-card">
            <div class="stat-icon">📸</div>
            <div class="stat-content">
                <h3><?php echo number_format($stats['reviews_with_media']); ?></h3>
                <p><?php _e('Reviews with Media', 'wao-woocommerce-review'); ?></p>
            </div>
        </div>

        <div class="wao-wcr-stat-card">
            <div class="stat-icon">🎁</div>
            <div class="stat-content">
                <h3><?php echo number_format($stats['total_coupons']); ?></h3>
                <p><?php _e('Coupons Generated', 'wao-woocommerce-review'); ?></p>
            </div>
        </div>

        <div class="wao-wcr-stat-card">
            <div class="stat-icon">⭐</div>
            <div class="stat-content">
                <h3><?php echo number_format($stats['average_rating'], 1); ?></h3>
                <p><?php _e('Average Rating', 'wao-woocommerce-review'); ?></p>
            </div>
        </div>
    </div>

    <div class="wao-wcr-dashboard-grid">
        <div class="wao-wcr-panel">
            <h2><?php _e('Rating Distribution', 'wao-woocommerce-review'); ?></h2>
            <div class="rating-bars">
                <?php foreach (array(5, 4, 3, 2, 1) as $rating) : ?>
                    <?php
                    $count = isset($stats['ratings_breakdown'][$rating]) ? $stats['ratings_breakdown'][$rating] : 0;
                    $percentage = $stats['total_reviews'] > 0 ? ($count / $stats['total_reviews']) * 100 : 0;
                    ?>
                    <div class="rating-bar">
                        <span class="rating-label"><?php echo $rating; ?> ⭐</span>
                        <div class="bar-container">
                            <div class="bar-fill" style="width: <?php echo $percentage; ?>%;"></div>
                        </div>
                        <span class="rating-count"><?php echo $count; ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="wao-wcr-panel">
            <h2><?php _e('Recent Reviews', 'wao-woocommerce-review'); ?></h2>
            <div class="recent-reviews">
                <?php
                $recent_reviews = $analytics->get_recent_reviews(5);
                if (!empty($recent_reviews)) :
                    foreach ($recent_reviews as $review) :
                        $product = wc_get_product($review->comment_post_ID);
                        ?>
                        <div class="review-item">
                            <div class="review-header">
                                <strong><?php echo esc_html($review->comment_author); ?></strong>
                                <span class="review-rating"><?php echo str_repeat('⭐', $review->rating); ?></span>
                            </div>
                            <p class="review-product"><?php echo $product ? esc_html($product->get_name()) : ''; ?></p>
                            <p class="review-excerpt"><?php echo wp_trim_words($review->comment_content, 15); ?></p>
                            <span class="review-date"><?php echo human_time_diff(strtotime($review->comment_date), current_time('timestamp')) . ' ' . __('ago', 'wao-woocommerce-review'); ?></span>
                        </div>
                    <?php endforeach;
                else : ?>
                    <p><?php _e('No reviews yet.', 'wao-woocommerce-review'); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="wao-wcr-quick-links">
        <h2><?php _e('Quick Actions', 'wao-woocommerce-review'); ?></h2>
        <div class="quick-links-grid">
            <a href="<?php echo admin_url('admin.php?page=wao-wcr-campaigns'); ?>" class="quick-link-card">
                <span class="dashicons dashicons-megaphone"></span>
                <span><?php _e('Manage Campaigns', 'wao-woocommerce-review'); ?></span>
            </a>
            <a href="<?php echo admin_url('admin.php?page=wao-wcr-emails'); ?>" class="quick-link-card">
                <span class="dashicons dashicons-email"></span>
                <span><?php _e('Email Templates', 'wao-woocommerce-review'); ?></span>
            </a>
            <a href="<?php echo admin_url('admin.php?page=wao-wcr-settings'); ?>" class="quick-link-card">
                <span class="dashicons dashicons-admin-settings"></span>
                <span><?php _e('Settings', 'wao-woocommerce-review'); ?></span>
            </a>
            <a href="<?php echo admin_url('edit-comments.php?comment_type=review'); ?>" class="quick-link-card">
                <span class="dashicons dashicons-admin-comments"></span>
                <span><?php _e('All Reviews', 'wao-woocommerce-review'); ?></span>
            </a>
        </div>
    </div>
</div>
