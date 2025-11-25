<?php
if (!defined('ABSPATH')) {
    exit;
}

$analytics = WAO_WCR_Analytics::get_instance();
$stats = $analytics->get_dashboard_stats();
?>

<div class="wrap wao-wcr-analytics">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <div class="wao-wcr-analytics-summary">
        <div class="analytics-card">
            <h3><?php _e('Review Performance', 'wao-woocommerce-review'); ?></h3>
            <div class="metric-row">
                <span class="metric-label"><?php _e('Total Reviews:', 'wao-woocommerce-review'); ?></span>
                <span class="metric-value"><?php echo number_format($stats['total_reviews']); ?></span>
            </div>
            <div class="metric-row">
                <span class="metric-label"><?php _e('Average Rating:', 'wao-woocommerce-review'); ?></span>
                <span class="metric-value"><?php echo number_format($stats['average_rating'], 2); ?> / 5.0</span>
            </div>
            <div class="metric-row">
                <span class="metric-label"><?php _e('Reviews with Media:', 'wao-woocommerce-review'); ?></span>
                <span class="metric-value"><?php echo number_format($stats['reviews_with_media']); ?></span>
            </div>
            <div class="metric-row">
                <span class="metric-label"><?php _e('Total Media Files:', 'wao-woocommerce-review'); ?></span>
                <span class="metric-value"><?php echo number_format($stats['total_media_uploads']); ?></span>
            </div>
        </div>

        <div class="analytics-card">
            <h3><?php _e('Reward Metrics', 'wao-woocommerce-review'); ?></h3>
            <div class="metric-row">
                <span class="metric-label"><?php _e('Coupons Generated:', 'wao-woocommerce-review'); ?></span>
                <span class="metric-value"><?php echo number_format($stats['total_coupons']); ?></span>
            </div>
            <div class="metric-row">
                <span class="metric-label"><?php _e('Conversion Rate:', 'wao-woocommerce-review'); ?></span>
                <span class="metric-value">
                    <?php
                    if ($stats['total_reviews'] > 0) {
                        echo number_format(($stats['total_coupons'] / $stats['total_reviews']) * 100, 2) . '%';
                    } else {
                        echo '0%';
                    }
                    ?>
                </span>
            </div>
            <div class="metric-row">
                <span class="metric-label"><?php _e('Media Upload Rate:', 'wao-woocommerce-review'); ?></span>
                <span class="metric-value">
                    <?php
                    if ($stats['total_reviews'] > 0) {
                        echo number_format(($stats['reviews_with_media'] / $stats['total_reviews']) * 100, 2) . '%';
                    } else {
                        echo '0%';
                    }
                    ?>
                </span>
            </div>
        </div>
    </div>

    <div class="wao-wcr-rating-breakdown">
        <h2><?php _e('Rating Distribution', 'wao-woocommerce-review'); ?></h2>
        <div class="rating-chart">
            <?php foreach (array(5, 4, 3, 2, 1) as $rating) : ?>
                <?php
                $count = isset($stats['ratings_breakdown'][$rating]) ? $stats['ratings_breakdown'][$rating] : 0;
                $percentage = $stats['total_reviews'] > 0 ? ($count / $stats['total_reviews']) * 100 : 0;
                ?>
                <div class="rating-row">
                    <span class="rating-stars"><?php echo str_repeat('⭐', $rating); ?></span>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?php echo $percentage; ?>%;"></div>
                    </div>
                    <span class="rating-percentage"><?php echo number_format($percentage, 1); ?>%</span>
                    <span class="rating-count">(<?php echo $count; ?>)</span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="wao-wcr-top-products">
        <h2><?php _e('Most Reviewed Products', 'wao-woocommerce-review'); ?></h2>
        <p class="description"><?php _e('Coming soon: Track which products receive the most reviews and engagement.', 'wao-woocommerce-review'); ?></p>
    </div>
</div>
