<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wao-wcr-rating-summary">
    <div class="summary-score">
        <span class="average-rating"><?php echo number_format($average_rating, 1); ?></span>
        <div class="star-rating">
            <?php echo wc_get_star_rating_html($average_rating); ?>
        </div>
        <span class="total-reviews"><?php printf(_n('%s review', '%s reviews', $total_reviews, 'wao-woocommerce-review'), number_format($total_reviews)); ?></span>
    </div>

    <div class="summary-bars">
        <?php for ($i = 5; $i >= 1; $i--) : ?>
            <?php
            $count = isset($rating_counts[$i]) ? $rating_counts[$i] : 0;
            $percentage = $total_reviews > 0 ? ($count / $total_reviews) * 100 : 0;
            ?>
            <div class="rating-bar-row">
                <span class="rating-label"><?php echo $i; ?> ⭐</span>
                <div class="wao-wcr-progress-bar">
                    <div class="wao-wcr-progress-bar-fill" style="width: <?php echo $percentage; ?>%;"></div>
                </div>
                <span class="rating-count"><?php echo $count; ?></span>
            </div>
        <?php endfor; ?>
    </div>
</div>
