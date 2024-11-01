<?php

class METAPRESS_EMAIL_NOTIFICATION_MANAGER {
    public function __construct() {
        add_action('metapress_send_subscription_renewal_reminders_event', array($this, 'metapress_send_subscription_renewal_reminders') );
        if( ! wp_next_scheduled( 'metapress_send_subscription_renewal_reminders_event' ) ) {
            wp_schedule_event( time() + 10, 'hourly', 'metapress_send_subscription_renewal_reminders_event');
        }
    }

    public function metapress_send_subscription_renewal_reminders() {
        $renewal_in_a_day_timestamp = strtotime('+1 day', current_time('timestamp'));
        $metapress_payments_manager = new METAPRESS_PAYMENTS_MANAGER();
        $subscriptions = $metapress_payments_manager->get_renewal_reminder_subscriptions($renewal_in_a_day_timestamp);
        if( ! empty($subscriptions) ) {
            foreach($subscriptions as $subscription) {
                $notice_sent = $this->send_subscription_reminder($subscription);
                if( $notice_sent ) {
                    $metapress_payments_manager->update_subscription($subscription->id, array('notice_sent' => 1));
                }
            }
        }
    }

    public function set_email_content_type() {
        return 'text/html';
    }

    public function send_subscription_reminder($subscription) {
        add_filter( 'wp_mail_content_type', array($this, 'set_email_content_type'));
        global $wp_metapress_textdomain;

        $metapress_checkout_page = get_option('metapress_checkout_page');
        $metapress_checkout_url = get_permalink($metapress_checkout_page);

        $from_name = get_bloginfo('name');
        $send_to_email = $subscription->notification_email;

        $renewal_formatted_date = wp_date( 'M d, Y', $subscription->expires);
        $product_name = get_the_title($subscription->product_id);
        $metapress_checkout_url .= '?mpp='.$subscription->product_id;
        $email_title = $product_name . ' ' . __('Subscription', $wp_metapress_textdomain);

        $email_subject = 'Renewal Reminder - ' . $product_name . ' Subscription';
        $notice_email_message = '<p>This is a friendly reminder that your ' . $product_name . ' subscription expires on <strong>' .$renewal_formatted_date.'</strong>.</p><p>You can renew your subscription <a href="'. $metapress_checkout_url .'">here</a></p>';

        // CREATE EMAIL
        $email_template = file_get_contents(METAPRESS_PLUGIN_BASE_DIR .'/email/emailheader.html');
        $email_template .= file_get_contents(METAPRESS_PLUGIN_BASE_DIR .'/email/notification.html');
        $email_template .= file_get_contents(METAPRESS_PLUGIN_BASE_DIR .'/email/emailfooter.html');

        $setup_email_message = str_replace('{{title}}', $email_title, $email_template);
        $setup_email_message = str_replace('{{notification}}', $notice_email_message, $setup_email_message);
        $setup_email_message = str_replace('{{noticelink}}', $metapress_checkout_url, $setup_email_message);
        $setup_email_message = str_replace('{{noticebutton}}', __('Renew Now', $wp_metapress_textdomain), $setup_email_message);

        return wp_mail($send_to_email, $email_subject, $setup_email_message);
    }
}
$metapress_email_notification_manager = new METAPRESS_EMAIL_NOTIFICATION_MANAGER();
