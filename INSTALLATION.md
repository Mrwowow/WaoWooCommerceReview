# WaoWooCommerceReview - Installation Guide

## Requirements

- WordPress 5.0 or higher
- WooCommerce 5.0 or higher
- PHP 7.0 or higher
- MySQL 5.6 or higher

## Installation Steps

### Method 1: Manual Installation

1. Download the plugin files or clone the repository
2. Upload the entire `WpWooCommerceViewReview` folder to your WordPress `/wp-content/plugins/` directory
3. Navigate to **Plugins** in your WordPress admin panel
4. Find **WaoWooCommerceReview** in the plugin list
5. Click **Activate**

### Method 2: WordPress Admin Upload

1. Download the plugin as a ZIP file
2. Go to **Plugins > Add New** in WordPress admin
3. Click **Upload Plugin**
4. Choose the ZIP file and click **Install Now**
5. Click **Activate Plugin**

## Initial Setup

After activation, the plugin will automatically:
- Create necessary database tables
- Set default configuration options
- Add a new menu item **Review Rewards** in your WordPress admin

### Configuration Steps

1. **Configure Settings**
   - Navigate to **Review Rewards > Settings**
   - Enable/disable photo and video reviews
   - Set maximum uploads per review
   - Configure email settings (optional SendGrid integration)
   - Customize colors to match your brand

2. **Create Your First Campaign**
   - Go to **Review Rewards > Campaigns**
   - Click **Add New Campaign**
   - Set campaign name (e.g., "First Review Reward")
   - Choose review count required (1 for first review)
   - Select coupon type (Percentage, Fixed Cart, or Fixed Product)
   - Set coupon amount and validity
   - Configure additional options (free shipping, exclude sale items, etc.)
   - Save the campaign

3. **Set Up Email Reminders** (Optional)
   - Go to **Review Rewards > Email Templates**
   - Configure your SendGrid API key (or leave blank to use WordPress mail)
   - Customize the email subject and content
   - Use placeholders: `{customer_name}`, `{order_id}`, `{products_list}`
   - Set trigger timing (e.g., 7 days after purchase)
   - Send a test email to verify

4. **Customize Review Display**
   - Go to **Review Rewards > Settings > Display**
   - Set primary color for buttons and accents
   - Set star rating color
   - Colors will automatically apply to your frontend reviews

## SendGrid Integration (Optional)

For reliable email delivery, you can integrate with SendGrid:

1. Sign up for a SendGrid account at https://sendgrid.com
2. Create an API key in SendGrid dashboard
3. Go to **Review Rewards > Settings > Email**
4. Paste your SendGrid API key
5. Set your preferred "From Name" and "From Email"

If you don't configure SendGrid, the plugin will use WordPress's default `wp_mail()` function.

## Features Overview

### Automated Review Rewards
- Automatically generates unique coupon codes when customers leave reviews
- Emails coupons directly to customers
- Supports multiple campaign types with different triggers

### Photo & Video Reviews
- Customers can upload images and videos with their reviews
- Configurable upload limits
- Automatic media display on product pages

### Email Automation
- Scheduled review reminder emails after purchase
- Customizable templates with placeholders
- SendGrid integration for reliable delivery

### Analytics Dashboard
- Track total reviews and average ratings
- Monitor media upload rates
- View coupon generation metrics
- Rating distribution charts

## Troubleshooting

### Plugin doesn't activate
- **Solution**: Ensure WooCommerce is installed and activated first

### Coupons not generating
- **Solution**: Check that your campaign is set to "Active" status
- Verify the review count required matches customer's review count

### Emails not sending
- **Solution**: Test with a known working email address
- Check spam folder
- Configure SendGrid for better deliverability
- Check WordPress email settings

### Photos not uploading
- **Solution**: Check PHP upload limits in php.ini
- Verify WordPress upload directory has write permissions
- Check file type restrictions in settings

### Database tables not created
- **Solution**: Deactivate and reactivate the plugin
- Check database user permissions

## Uninstallation

To completely remove the plugin:

1. Deactivate the plugin from **Plugins** page
2. Click **Delete** (this will remove all plugin files)
3. To remove database tables and settings, add this to your theme's functions.php temporarily:

```php
// Remove WaoWooCommerceReview data (run once, then remove this code)
add_action('init', function() {
    if (class_exists('WAO_WCR_Database')) {
        WAO_WCR_Database::drop_tables();
    }
    delete_option('wao_wcr_enable_photo_reviews');
    delete_option('wao_wcr_enable_video_reviews');
    delete_option('wao_wcr_max_uploads_per_review');
    // ... delete other options as needed
});
```

## Support & Documentation

For more information, visit:
- GitHub Repository: https://github.com/Mrwowow/WaoWooCommerceReview
- Issues: https://github.com/Mrwowow/WaoWooCommerceReview/issues

## License

GPL v2 or later
