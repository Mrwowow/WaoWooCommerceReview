<?php
if (!defined('ABSPATH')) {
    exit;
}

$campaigns = WAO_WCR_Campaign::get_instance()->get_all_campaigns();
?>

<div class="wrap wao-wcr-campaigns">
    <h1><?php echo esc_html(get_admin_page_title()); ?>
        <a href="#" class="page-title-action" id="create-campaign-btn"><?php _e('Add New Campaign', 'wao-woocommerce-review'); ?></a>
    </h1>

    <p class="description"><?php _e('Create reward campaigns to automatically send discount coupons when customers leave reviews.', 'wao-woocommerce-review'); ?></p>

    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th><?php _e('Campaign Name', 'wao-woocommerce-review'); ?></th>
                <th><?php _e('Review Count Required', 'wao-woocommerce-review'); ?></th>
                <th><?php _e('Coupon Type', 'wao-woocommerce-review'); ?></th>
                <th><?php _e('Amount', 'wao-woocommerce-review'); ?></th>
                <th><?php _e('Status', 'wao-woocommerce-review'); ?></th>
                <th><?php _e('Actions', 'wao-woocommerce-review'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($campaigns)) : ?>
                <?php foreach ($campaigns as $campaign) : ?>
                    <tr>
                        <td><strong><?php echo esc_html($campaign->name); ?></strong></td>
                        <td><?php echo esc_html($campaign->review_count_required); ?></td>
                        <td><?php echo esc_html(ucfirst(str_replace('_', ' ', $campaign->coupon_type))); ?></td>
                        <td><?php echo wc_price($campaign->coupon_amount); ?></td>
                        <td>
                            <span class="campaign-status status-<?php echo esc_attr($campaign->status); ?>">
                                <?php echo esc_html(ucfirst($campaign->status)); ?>
                            </span>
                        </td>
                        <td>
                            <a href="#" class="edit-campaign" data-id="<?php echo $campaign->id; ?>"><?php _e('Edit', 'wao-woocommerce-review'); ?></a> |
                            <a href="#" class="delete-campaign" data-id="<?php echo $campaign->id; ?>"><?php _e('Delete', 'wao-woocommerce-review'); ?></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="6"><?php _e('No campaigns found. Create your first campaign to start rewarding customers!', 'wao-woocommerce-review'); ?></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div id="campaign-modal" class="wao-wcr-modal" style="display: none;">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2><?php _e('Create/Edit Campaign', 'wao-woocommerce-review'); ?></h2>
        <form id="campaign-form">
            <input type="hidden" id="campaign-id" name="campaign_id">

            <p>
                <label><?php _e('Campaign Name', 'wao-woocommerce-review'); ?></label>
                <input type="text" name="campaign[name]" required class="regular-text">
            </p>

            <p>
                <label><?php _e('Reviews Required', 'wao-woocommerce-review'); ?></label>
                <input type="number" name="campaign[review_count_required]" value="1" min="1" required>
                <span class="description"><?php _e('Number of reviews before reward is triggered', 'wao-woocommerce-review'); ?></span>
            </p>

            <p>
                <label><?php _e('Coupon Type', 'wao-woocommerce-review'); ?></label>
                <select name="campaign[coupon_type]" required>
                    <option value="percent">Percentage Discount</option>
                    <option value="fixed_cart">Fixed Cart Discount</option>
                    <option value="fixed_product">Fixed Product Discount</option>
                </select>
            </p>

            <p>
                <label><?php _e('Coupon Amount', 'wao-woocommerce-review'); ?></label>
                <input type="number" name="campaign[coupon_amount]" step="0.01" min="0" required>
            </p>

            <p>
                <label><?php _e('Validity Days', 'wao-woocommerce-review'); ?></label>
                <input type="number" name="campaign[coupon_validity_days]" value="30" min="1">
            </p>

            <p>
                <label><?php _e('Usage Limit', 'wao-woocommerce-review'); ?></label>
                <input type="number" name="campaign[usage_limit]" value="1" min="1">
            </p>

            <p>
                <label><?php _e('Minimum Spend', 'wao-woocommerce-review'); ?></label>
                <input type="number" name="campaign[minimum_spend]" step="0.01" min="0" value="0">
            </p>

            <p>
                <label>
                    <input type="checkbox" name="campaign[free_shipping]" value="1">
                    <?php _e('Free Shipping', 'wao-woocommerce-review'); ?>
                </label>
            </p>

            <p>
                <label>
                    <input type="checkbox" name="campaign[exclude_sale_items]" value="1">
                    <?php _e('Exclude Sale Items', 'wao-woocommerce-review'); ?>
                </label>
            </p>

            <p>
                <label>
                    <input type="checkbox" name="campaign[individual_use]" value="1">
                    <?php _e('Individual Use Only', 'wao-woocommerce-review'); ?>
                </label>
            </p>

            <p>
                <label><?php _e('Status', 'wao-woocommerce-review'); ?></label>
                <select name="campaign[status]">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </p>

            <p>
                <button type="submit" class="button button-primary"><?php _e('Save Campaign', 'wao-woocommerce-review'); ?></button>
                <button type="button" class="button cancel-modal"><?php _e('Cancel', 'wao-woocommerce-review'); ?></button>
            </p>
        </form>
    </div>
</div>
