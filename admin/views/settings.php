<?php
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap wao-wcr-settings">
    <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

    <?php settings_errors(); ?>

    <form method="post" action="options.php">
        <?php
        settings_fields('wao_wcr_settings');
        ?>

        <div class="wao-wcr-settings-tabs">
            <nav class="nav-tab-wrapper">
                <a href="#general" class="nav-tab nav-tab-active"><?php _e('General', 'wao-woocommerce-review'); ?></a>
                <a href="#email" class="nav-tab"><?php _e('Email Settings', 'wao-woocommerce-review'); ?></a>
                <a href="#display" class="nav-tab"><?php _e('Display', 'wao-woocommerce-review'); ?></a>
            </nav>

            <div class="tab-content">
                <div id="general" class="tab-panel active">
                    <h2><?php _e('Review Settings', 'wao-woocommerce-review'); ?></h2>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php _e('Enable Photo Reviews', 'wao-woocommerce-review'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="wao_wcr_enable_photo_reviews" value="yes" <?php checked(get_option('wao_wcr_enable_photo_reviews'), 'yes'); ?>>
                                    <?php _e('Allow customers to upload photos with reviews', 'wao-woocommerce-review'); ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Enable Video Reviews', 'wao-woocommerce-review'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="wao_wcr_enable_video_reviews" value="yes" <?php checked(get_option('wao_wcr_enable_video_reviews'), 'yes'); ?>>
                                    <?php _e('Allow customers to upload videos with reviews', 'wao-woocommerce-review'); ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Max Uploads Per Review', 'wao-woocommerce-review'); ?></th>
                            <td>
                                <input type="number" name="wao_wcr_max_uploads_per_review" value="<?php echo esc_attr(get_option('wao_wcr_max_uploads_per_review', 5)); ?>" min="1" max="10">
                                <p class="description"><?php _e('Maximum number of files customers can upload per review', 'wao-woocommerce-review'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Enable Review Editing', 'wao-woocommerce-review'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="wao_wcr_enable_review_editing" value="yes" <?php checked(get_option('wao_wcr_enable_review_editing'), 'yes'); ?>>
                                    <?php _e('Allow customers to edit their reviews', 'wao-woocommerce-review'); ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Enable Helpful Voting', 'wao-woocommerce-review'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="wao_wcr_enable_helpful_voting" value="yes" <?php checked(get_option('wao_wcr_enable_helpful_voting'), 'yes'); ?>>
                                    <?php _e('Allow customers to vote reviews as helpful', 'wao-woocommerce-review'); ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Mask Reviewer Names', 'wao-woocommerce-review'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="wao_wcr_mask_reviewer_names" value="yes" <?php checked(get_option('wao_wcr_mask_reviewer_names'), 'yes'); ?>>
                                    <?php _e('Protect reviewer privacy by masking names', 'wao-woocommerce-review'); ?>
                                </label>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Auto Delete Expired Coupons', 'wao-woocommerce-review'); ?></th>
                            <td>
                                <label>
                                    <input type="checkbox" name="wao_wcr_auto_delete_expired_coupons" value="yes" <?php checked(get_option('wao_wcr_auto_delete_expired_coupons'), 'yes'); ?>>
                                    <?php _e('Automatically delete expired review coupons daily', 'wao-woocommerce-review'); ?>
                                </label>
                            </td>
                        </tr>
                    </table>
                </div>

                <div id="email" class="tab-panel">
                    <h2><?php _e('Email Configuration', 'wao-woocommerce-review'); ?></h2>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php _e('SendGrid API Key', 'wao-woocommerce-review'); ?></th>
                            <td>
                                <input type="text" name="wao_wcr_sendgrid_api_key" value="<?php echo esc_attr(get_option('wao_wcr_sendgrid_api_key')); ?>" class="regular-text" placeholder="SG.xxxxxxxxxxxxx">
                                <p class="description"><?php _e('Optional. Leave blank to use WordPress wp_mail()', 'wao-woocommerce-review'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('From Name', 'wao-woocommerce-review'); ?></th>
                            <td>
                                <input type="text" name="wao_wcr_email_from_name" value="<?php echo esc_attr(get_option('wao_wcr_email_from_name', get_bloginfo('name'))); ?>" class="regular-text">
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('From Email', 'wao-woocommerce-review'); ?></th>
                            <td>
                                <input type="email" name="wao_wcr_email_from_email" value="<?php echo esc_attr(get_option('wao_wcr_email_from_email', get_option('admin_email'))); ?>" class="regular-text">
                            </td>
                        </tr>
                    </table>
                </div>

                <div id="display" class="tab-panel">
                    <h2><?php _e('Display Customization', 'wao-woocommerce-review'); ?></h2>
                    <table class="form-table">
                        <tr>
                            <th scope="row"><?php _e('Primary Color', 'wao-woocommerce-review'); ?></th>
                            <td>
                                <input type="color" name="wao_wcr_review_primary_color" value="<?php echo esc_attr(get_option('wao_wcr_review_primary_color', '#0073aa')); ?>">
                                <p class="description"><?php _e('Used for buttons and accents', 'wao-woocommerce-review'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php _e('Star Rating Color', 'wao-woocommerce-review'); ?></th>
                            <td>
                                <input type="color" name="wao_wcr_review_star_color" value="<?php echo esc_attr(get_option('wao_wcr_review_star_color', '#ffa500')); ?>">
                                <p class="description"><?php _e('Color for star ratings and progress bars', 'wao-woocommerce-review'); ?></p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <?php submit_button(); ?>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    $('.nav-tab').on('click', function(e) {
        e.preventDefault();
        var target = $(this).attr('href');

        $('.nav-tab').removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');

        $('.tab-panel').removeClass('active');
        $(target).addClass('active');
    });
});
</script>
