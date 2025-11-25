<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap wao-wcr-emails">
    <h1><?php echo esc_html(get_admin_page_title()); ?>
        <a href="#" class="page-title-action" id="create-email-btn"><?php _e('Add Email Template', 'wao-woocommerce-review'); ?></a>
    </h1>

    <p class="description"><?php _e('Create automated email campaigns to remind customers to leave reviews.', 'wao-woocommerce-review'); ?></p>

    <div class="wao-wcr-email-info">
        <h3><?php _e('Available Placeholders:', 'wao-woocommerce-review'); ?></h3>
        <ul>
            <li><code>{customer_name}</code> - Customer's first name</li>
            <li><code>{order_id}</code> - Order number</li>
            <li><code>{products_list}</code> - List of products with review links</li>
        </ul>
    </div>

    <div class="wao-wcr-email-preview">
        <h2><?php _e('Test Email', 'wao-woocommerce-review'); ?></h2>
        <p>
            <input type="email" id="test-email-address" placeholder="your-email@example.com" class="regular-text">
            <button type="button" class="button" id="send-test-email"><?php _e('Send Test Email', 'wao-woocommerce-review'); ?></button>
        </p>
    </div>

    <div class="email-template-editor">
        <h2><?php _e('Default Review Reminder Template', 'wao-woocommerce-review'); ?></h2>

        <form id="email-template-form">
            <p>
                <label><?php _e('Subject Line', 'wao-woocommerce-review'); ?></label>
                <input type="text" name="subject" value="<?php _e('How was your recent purchase?', 'wao-woocommerce-review'); ?>" class="widefat">
            </p>

            <p>
                <label><?php _e('Trigger', 'wao-woocommerce-review'); ?></label>
                <select name="trigger_type">
                    <option value="after_purchase"><?php _e('After Purchase Completion', 'wao-woocommerce-review'); ?></option>
                    <option value="after_delivery"><?php _e('After Delivery', 'wao-woocommerce-review'); ?></option>
                </select>
            </p>

            <p>
                <label><?php _e('Send After (days)', 'wao-woocommerce-review'); ?></label>
                <input type="number" name="trigger_days" value="7" min="1">
            </p>

            <p>
                <label><?php _e('Header Color', 'wao-woocommerce-review'); ?></label>
                <input type="color" name="header_color" value="#0073aa">
            </p>

            <p>
                <label><?php _e('Email Content', 'wao-woocommerce-review'); ?></label>
                <?php
                $default_content = '<p>Hi {customer_name},</p>
<p>Thank you for your recent purchase! We would love to hear your feedback.</p>
<p>Please take a moment to review the following products:</p>
{products_list}
<p>As a thank you, you will receive an exclusive discount code after submitting your review!</p>
<p>Best regards,<br>' . get_bloginfo('name') . '</p>';

                wp_editor($default_content, 'email_content', array(
                    'textarea_name' => 'content',
                    'textarea_rows' => 10,
                    'media_buttons' => false,
                    'teeny' => true
                ));
                ?>
            </p>

            <p>
                <button type="submit" class="button button-primary"><?php _e('Save Template', 'wao-woocommerce-review'); ?></button>
            </p>
        </form>
    </div>
</div>
