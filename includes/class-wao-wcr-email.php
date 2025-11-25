<?php
if (!defined('ABSPATH')) {
    exit;
}

class WAO_WCR_Email {

    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action('woocommerce_order_status_completed', array($this, 'schedule_review_reminder'), 10, 1);
        add_action('wao_wcr_send_review_reminder', array($this, 'send_review_reminder'), 10, 2);
        add_action('wp_ajax_wao_wcr_send_test_email', array($this, 'ajax_send_test_email'));
    }

    public function schedule_review_reminder($order_id) {
        $order = wc_get_order($order_id);

        if (!$order) {
            return;
        }

        $campaigns = $this->get_active_email_campaigns();

        foreach ($campaigns as $campaign) {
            if ($campaign->trigger_type === 'after_purchase') {
                $send_time = time() + ($campaign->trigger_days * DAY_IN_SECONDS);

                wp_schedule_single_event($send_time, 'wao_wcr_send_review_reminder', array($order_id, $campaign->id));
            }
        }
    }

    public function send_review_reminder($order_id, $campaign_id) {
        $order = wc_get_order($order_id);
        $campaign = $this->get_email_campaign($campaign_id);

        if (!$order || !$campaign) {
            return;
        }

        if ($this->has_sent_email($order_id, $campaign_id)) {
            return;
        }

        $customer_email = $order->get_billing_email();
        $customer_name = $order->get_billing_first_name();

        $items = $order->get_items();
        $products_html = '';

        foreach ($items as $item) {
            $product = $item->get_product();
            if ($product) {
                $review_url = get_permalink($product->get_id()) . '#reviews';
                $products_html .= sprintf(
                    '<li><a href="%s">%s</a></li>',
                    esc_url($review_url),
                    esc_html($product->get_name())
                );
            }
        }

        $subject = $this->replace_placeholders($campaign->subject, array(
            'customer_name' => $customer_name,
            'order_id' => $order_id
        ));

        $content = $this->replace_placeholders($campaign->content, array(
            'customer_name' => $customer_name,
            'order_id' => $order_id,
            'products_list' => '<ul>' . $products_html . '</ul>'
        ));

        $message = $this->get_email_template($content, $campaign->header_color);

        $result = $this->send_email($customer_email, $subject, $message);

        $this->log_email($order_id, $campaign_id, $customer_email, $result);
    }

    private function replace_placeholders($text, $data) {
        foreach ($data as $key => $value) {
            $text = str_replace('{' . $key . '}', $value, $text);
        }
        return $text;
    }

    private function get_email_template($content, $header_color = '#0073aa') {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
        </head>
        <body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
            <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px;">
                <tr>
                    <td align="center">
                        <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden;">
                            <tr>
                                <td style="background-color: <?php echo esc_attr($header_color); ?>; padding: 30px; text-align: center;">
                                    <h1 style="color: #ffffff; margin: 0; font-size: 24px;"><?php echo esc_html(get_option('wao_wcr_email_from_name', get_bloginfo('name'))); ?></h1>
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 40px 30px;">
                                    <?php echo wp_kses_post($content); ?>
                                </td>
                            </tr>
                            <tr>
                                <td style="background-color: #f9f9f9; padding: 20px; text-align: center; font-size: 12px; color: #666;">
                                    <p style="margin: 0;">&copy; <?php echo date('Y'); ?> <?php echo esc_html(get_bloginfo('name')); ?>. All rights reserved.</p>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }

    private function send_email($to, $subject, $message) {
        $sendgrid_api_key = get_option('wao_wcr_sendgrid_api_key');

        if (!empty($sendgrid_api_key)) {
            return $this->send_via_sendgrid($to, $subject, $message, $sendgrid_api_key);
        } else {
            return $this->send_via_wp_mail($to, $subject, $message);
        }
    }

    private function send_via_sendgrid($to, $subject, $message, $api_key) {
        $from_email = get_option('wao_wcr_email_from_email', get_option('admin_email'));
        $from_name = get_option('wao_wcr_email_from_name', get_bloginfo('name'));

        $data = array(
            'personalizations' => array(
                array(
                    'to' => array(
                        array('email' => $to)
                    ),
                    'subject' => $subject
                )
            ),
            'from' => array(
                'email' => $from_email,
                'name' => $from_name
            ),
            'content' => array(
                array(
                    'type' => 'text/html',
                    'value' => $message
                )
            )
        );

        $response = wp_remote_post('https://api.sendgrid.com/v3/mail/send', array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $api_key,
                'Content-Type' => 'application/json'
            ),
            'body' => json_encode($data),
            'timeout' => 30
        ));

        if (is_wp_error($response)) {
            return false;
        }

        $response_code = wp_remote_retrieve_response_code($response);

        return $response_code === 202;
    }

    private function send_via_wp_mail($to, $subject, $message) {
        $from_email = get_option('wao_wcr_email_from_email', get_option('admin_email'));
        $from_name = get_option('wao_wcr_email_from_name', get_bloginfo('name'));

        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $from_name . ' <' . $from_email . '>'
        );

        return wp_mail($to, $subject, $message, $headers);
    }

    private function log_email($order_id, $campaign_id, $email, $success) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'wao_wcr_email_logs';

        $order = wc_get_order($order_id);
        $user_id = $order ? $order->get_user_id() : 0;

        $wpdb->insert($table_name, array(
            'user_id' => $user_id,
            'order_id' => $order_id,
            'campaign_id' => $campaign_id,
            'email_address' => $email,
            'status' => $success ? 'sent' : 'failed',
            'sent_at' => current_time('mysql')
        ));
    }

    private function has_sent_email($order_id, $campaign_id) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'wao_wcr_email_logs';

        $count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE order_id = %d AND campaign_id = %d AND status = 'sent'",
            $order_id,
            $campaign_id
        ));

        return $count > 0;
    }

    public function get_email_campaign($campaign_id) {
        global $wpdb;

        $table_name = $wpdb->prefix . 'wao_wcr_email_campaigns';

        return $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $campaign_id));
    }

    public function get_active_email_campaigns() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'wao_wcr_email_campaigns';

        return $wpdb->get_results("SELECT * FROM $table_name WHERE status = 'active'");
    }

    public function ajax_send_test_email() {
        check_ajax_referer('wao_wcr_admin_nonce', 'nonce');

        if (!current_user_can('manage_woocommerce')) {
            wp_send_json_error(array('message' => __('Permission denied', 'wao-woocommerce-review')));
        }

        $email = sanitize_email($_POST['email']);
        $subject = sanitize_text_field($_POST['subject']);
        $content = wp_kses_post($_POST['content']);
        $header_color = sanitize_hex_color($_POST['header_color']);

        $message = $this->get_email_template($content, $header_color);
        $result = $this->send_email($email, $subject, $message);

        if ($result) {
            wp_send_json_success(array('message' => __('Test email sent successfully', 'wao-woocommerce-review')));
        } else {
            wp_send_json_error(array('message' => __('Failed to send test email', 'wao-woocommerce-review')));
        }
    }
}
