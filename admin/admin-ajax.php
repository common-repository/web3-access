<?php

if( ! function_exists('metapress_custom_is_secure_admin_ajax') ) {
function metapress_custom_is_secure_admin_ajax() {
    $metapress_ajax_request_allowed = false;
    if(is_user_logged_in() && current_user_can('manage_options')) {
        $metapress_ajax_request_allowed = true;
    }
    return $metapress_ajax_request_allowed;
}
}

if( ! function_exists('metapress_custom_admin_exit_ajax') ) {
function metapress_custom_admin_exit_ajax($error_message, $error_code) {
    if( empty($error_code) ) {
        $error_code = 400;
    }
    status_header($error_code);
    _e($error_message);
    exit;
}
}


class MetaPress_Admin_Requests_Ajax_Manager {

    public function __construct() {
        add_action( 'wp_ajax_metapress_load_admin_payments_ajax_request', array($this, 'metapress_load_admin_payments_ajax_request') );
        add_action( 'wp_ajax_metapress_load_admin_subscriptions_ajax_request', array($this, 'metapress_load_admin_subscriptions_ajax_request') );
        add_action( 'wp_ajax_metapress_admin_update_subscription_price_ajax_request', array($this, 'metapress_admin_update_subscription_price_ajax_request') );
        add_action( 'wp_ajax_metapress_admin_delete_subscription_ajax_request', array($this, 'metapress_admin_delete_subscription_ajax_request') );
        add_action( 'wp_ajax_metapress_load_admin_overview_payments_ajax_request', array($this, 'metapress_load_admin_overview_payments_ajax_request') );
    }

    public function metapress_load_admin_payments_ajax_request() {
        if( metapress_custom_is_secure_admin_ajax() ) {
            global $wp_metapress_textdomain;
            $offset = 0;
            $token = null;
            $payment_status = null;
            $filters = array();
            if( isset($_GET["offset"]) ) {
                $offset = intval($_GET["offset"]);
            }

            if( isset($_GET["token"]) && ! empty($_GET["token"]) ) {
                $filters['token'] = sanitize_text_field($_GET["token"]);
            }

            if( isset($_GET["status"]) && ! empty($_GET["status"]) ) {
                $filters['status'] = sanitize_text_field($_GET["status"]);
            }

            if( isset($_GET["product"]) && ! empty($_GET["product"]) ) {
                $filters['product'] = intval($_GET["product"]);
            }

            if( isset($_GET["address"]) && ! empty($_GET["address"]) ) {
                $filters['address'] = sanitize_text_field($_GET["address"]);
            }

            if( isset($_GET["from_date"]) && ! empty($_GET["from_date"]) ) {
                $filters['from_date'] = intval($_GET["from_date"]);
            }

            if( isset($_GET["to_date"]) && ! empty($_GET["to_date"]) ) {
                $filters['to_date'] = intval($_GET["to_date"]);
            }

            $metapress_payments_manager = new METAPRESS_PAYMENTS_MANAGER();
            $payments = $metapress_payments_manager->get_all_payments($offset, $filters);
            $count_payments = 0;
            $found_payments = array();
            if( ! empty($payments) ) {
                $count_payments = count($payments);
                foreach($payments as $payment) {
                    $view_transaction_url = $metapress_payments_manager->find_network_explorer_url($payment->network);
                    if (substr($view_transaction_url, -1) !== '/') {
                        $view_transaction_url .= '/';
                    }
                    $view_transaction_url .= 'tx/'.$payment->transaction_hash;
                    $product_name = get_the_title($payment->product_id);
                    $product_edit_link = admin_url('post.php?post='.$payment->product_id.'&action=edit');
                    $add_payment = array(
                        'id' => $payment->id,
                        'payment_owner' => $payment->payment_owner,
                        'token'  => $payment->token,
                        'token_amount'  => $payment->token_amount,
                        'transaction_time' => wp_date('F j, Y', $payment->transaction_time),
                        'network' => $payment->network,
                        'transaction_hash' => $payment->transaction_hash,
                        'transaction_status' => $payment->transaction_status,
                        'view_url' => $view_transaction_url,
                        'product_name' => $product_name,
                        'product_edit_link' => $product_edit_link,
                    );
                    $found_payments[] = $add_payment;
                }
            }
            if( ! empty($found_payments) ) {
                $return_payments = array('count' => $count_payments, 'payments' => $found_payments);
            } else {
                $return_payments = array('count' => 0);
            }
            echo wp_json_encode($return_payments);
        } else {
            $wpvs_error_message = __('You are restricted from doing this.', $wp_metapress_textdomain);
            metapress_custom_admin_exit_ajax($wpvs_error_message, 401);
        }
        wp_die();
    }

    public function metapress_load_admin_subscriptions_ajax_request() {
        if( metapress_custom_is_secure_admin_ajax() ) {
            global $wp_metapress_textdomain;
            $offset = 0;
            $filters = array();
            if( isset($_GET["offset"]) ) {
                $offset = intval($_GET["offset"]);
            }

            if( isset($_GET["product"]) && ! empty($_GET["product"]) ) {
                $filters['product'] = intval($_GET["product"]);
            }

            if( isset($_GET["address"]) && ! empty($_GET["address"]) ) {
                $filters['address'] = sanitize_text_field($_GET["address"]);
            }

            $metapress_payments_manager = new METAPRESS_PAYMENTS_MANAGER();
            $subscriptions = $metapress_payments_manager->get_all_subscriptions($offset, $filters);
            $count_subscriptions = 0;
            $found_subscriptions = array();
            $current_wp_time = current_time('timestamp');
            if( ! empty($subscriptions) ) {
                $count_subscriptions = count($subscriptions);
                foreach($subscriptions as $subscription) {
                    $product_name = get_the_title($subscription->product_id);
                    $product_edit_link = admin_url('post.php?post='.$subscription->product_id.'&action=edit');
                    $subscription_status = 'Expired';
                    if( $subscription->expires > $current_wp_time ) {
                        $subscription_status = 'Active';
                    }
                    $add_subscription = array(
                        'id' => $subscription->id,
                        'payment_owner' => $subscription->payment_owner,
                        'expires' => wp_date('F j, Y', $subscription->expires),
                        'status' => $subscription_status,
                        'price' => number_format($subscription->price/100, 2, '.', ''),
                        'payment_method' => $subscription->payment_method,
                        'product_name' => $product_name,
                        'product_edit_link' => $product_edit_link,
                    );
                    $found_subscriptions[] = $add_subscription;
                }
            }
            if( ! empty($found_subscriptions) ) {
                $return_subscriptions = array('count' => $count_subscriptions, 'subscriptions' => $found_subscriptions);
            } else {
                $return_subscriptions = array('count' => 0);
            }
            echo wp_json_encode($return_subscriptions);
        } else {
            $wpvs_error_message = __('You are restricted from doing this.', $wp_metapress_textdomain);
            metapress_custom_admin_exit_ajax($wpvs_error_message, 401);
        }
        wp_die();
    }

    public function metapress_load_admin_overview_payments_ajax_request() {
      if( metapress_custom_is_secure_admin_ajax() ) {

          $token = 'ETH';
          if( isset($_GET["token"]) && ! empty($_GET["token"]) ) {
              $token = sanitize_text_field($_GET["token"]);
          }
          $current_time = current_time('timestamp');

          // GET CURRENT YEAR
          $current_year = date('Y-01-01', $current_time);
          $current_year_timestamp = strtotime($current_year);

          // GET CURRENT MONTH
          $current_month = date('m-Y', $current_time);
          $set_first_day = '01-'.$current_month;
          $first_day_of_month = date($set_first_day);
          $current_month_timestamp = strtotime($first_day_of_month);
          $seven_days_ago = strtotime('-7 days', $current_time);
          $metapress_payments_manager = new METAPRESS_PAYMENTS_MANAGER();

          // GET LAST WEEK PAYMENTS
          $last_week_amount = 0;
          $last_week_payments = $metapress_payments_manager->get_timeframe_payments($seven_days_ago, $current_time, $token);

          if( ! empty($last_week_payments) ) {
              foreach($last_week_payments as $payment) {
                  $last_week_amount += $payment->token_amount;
              }
          }

          if( ! empty($last_week_amount) ) {
              $last_week_amount = number_format($last_week_amount,6);
          }

          // GET THIS MONTH PAYMENTS

          $this_month_amount = 0;
          $this_month_payments = $metapress_payments_manager->get_timeframe_payments($current_month_timestamp, $current_time, $token);

          if( ! empty($this_month_payments) ) {
              foreach($this_month_payments as $payment) {
                  $this_month_amount += $payment->token_amount;
              }
          }

          if( ! empty($this_month_amount) ) {
              $this_month_amount = number_format($this_month_amount,6);
          }

          // GET THIS YEAT PAYMENTS
          $this_year_amount = 0;
          $this_year_payments = $metapress_payments_manager->get_timeframe_payments($current_year_timestamp, $current_time, $token);

          if( ! empty($this_year_payments) ) {
              foreach($this_year_payments as $payment) {
                  $this_year_amount += floatval($payment->token_amount);
              }
          }

          if( ! empty($this_year_amount) ) {
              $this_year_amount = number_format($this_year_amount,6);
          }

          $return_payments = array(
              'last_week'   => $last_week_amount,
              'this_month'  => $this_month_amount,
              'this_year'   => $this_year_amount
          );

          echo wp_json_encode($return_payments);
        } else {
            $wpvs_error_message = __('You are restricted from doing this.', $wp_metapress_textdomain);
            metapress_custom_admin_exit_ajax($wpvs_error_message, 401);
        }
      wp_die();
  }

  public function metapress_admin_update_subscription_price_ajax_request() {
      if( metapress_custom_is_secure_admin_ajax() ) {
          global $wp_metapress_textdomain;

          $subscription_updated = false;
          $subscription_id = intval($_POST["subscription_id"]);
          $subscription_price = sanitize_text_field($_POST["price"]);

          if( empty($subscription_id) ) {
              $wpvs_error_message = __('Missing subscription ID', $wp_metapress_textdomain);
              metapress_custom_admin_exit_ajax($wpvs_error_message, 401);
          }

          $metapress_payments_manager = new METAPRESS_PAYMENTS_MANAGER();
          $subscription_updated = $metapress_payments_manager->update_subscription_price($subscription_id, $subscription_price);
          echo wp_json_encode(array('updated' => $subscription_updated));
      } else {
          $wpvs_error_message = __('You are restricted from doing this.', $wp_metapress_textdomain);
          metapress_custom_admin_exit_ajax($wpvs_error_message, 401);
      }
      wp_die();
  }

  public function metapress_admin_delete_subscription_ajax_request() {
      if( metapress_custom_is_secure_admin_ajax() ) {
          global $wp_metapress_textdomain;

          $subscription_deleted = false;
          $subscription_id = intval($_POST["subscription_id"]);

          if( empty($subscription_id) ) {
              $wpvs_error_message = __('Missing subscription ID', $wp_metapress_textdomain);
              metapress_custom_admin_exit_ajax($wpvs_error_message, 401);
          }

          $metapress_payments_manager = new METAPRESS_PAYMENTS_MANAGER();
          $subscription_deleted = $metapress_payments_manager->delete_subscription($subscription_id);
          echo wp_json_encode(array('deleted' => $subscription_deleted));
      } else {
          $wpvs_error_message = __('You are restricted from doing this.', $wp_metapress_textdomain);
          metapress_custom_admin_exit_ajax($wpvs_error_message, 401);
      }
      wp_die();
  }

}
$metapress_admin_request_ajax_manager = new MetaPress_Admin_Requests_Ajax_Manager();
