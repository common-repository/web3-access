<?php

class METAPRESS_PAYMENTS_MANAGER {
    protected $table_name;
    protected $subscription_table_name;
    public function __construct() {
        $metapress_live_mode = get_option('metapress_live_mode', 0);
        if( empty($metapress_live_mode) ) {
          $this->table_name = 'metapress_test_payments';
          $this->subscription_table_name = 'metapress_test_subscriptions';
        } else {
          $this->table_name = 'metapress_payments';
          $this->subscription_table_name = 'metapress_subscriptions';
        }
    }

    public function data_stripeslashes($payment_data) {
        foreach($payment_data as &$value) {
            if( ! empty($value) ) {
                $value = stripslashes($value);
                $value =  wp_unslash($value);
            }
        }
        return $payment_data;
    }

    public function create_payment($payment_data) {
        $new_payment_id = null;
        if( $this->table_name ) {
            global $wpdb;
            $add_to_table = $wpdb->prefix . $this->table_name;
            $payment_data = $this->data_stripeslashes($payment_data);
            $payment_added = $wpdb->insert(
                $add_to_table,
                $payment_data
            );
            if( ! empty($payment_added) ) {
                $new_payment_id = $wpdb->insert_id;
            }
        }
        return $new_payment_id;
    }

    public function find_pending_payment_for_product($product_id, $spender_address) {
        global $wpdb;
        $payment = null;
        $lower_case_address = strtolower($spender_address);
        $get_table_name = $wpdb->prefix . $this->table_name;
        $db_query = "SELECT * FROM $get_table_name WHERE product_id = '$product_id' AND (payment_owner = '$spender_address' OR payment_owner = '$lower_case_address') AND transaction_status != 'paid'";
        $payment = $wpdb->get_results($db_query);
        if( ! empty($payment) && isset($payment[0]) && ! empty($payment[0]) ) {
            $payment = $payment[0];
        }
        return $payment;
    }

    public function find_payment_for_product($product_id, $spender_address) {
        global $wpdb;
        $payment = null;
        $lower_case_address = strtolower($spender_address);
        $get_table_name = $wpdb->prefix . $this->table_name;
        $db_query = "SELECT * FROM $get_table_name WHERE product_id = '$product_id' AND (payment_owner = '$spender_address' OR payment_owner = '$lower_case_address') AND transaction_status = 'paid'";
        $payment = $wpdb->get_results($db_query);
        if( ! empty($payment) && isset($payment[0]) && ! empty($payment[0]) ) {
            $payment = $payment[0];
        }
        return $payment;
    }

    public function find_payment_with_hash($product_id, $spender_address, $hash) {
        global $wpdb;
        $payment = null;
        $lower_case_address = strtolower($spender_address);
        $get_table_name = $wpdb->prefix . $this->table_name;
        $db_query = "SELECT * FROM $get_table_name WHERE product_id = '$product_id' AND (payment_owner = '$spender_address' OR payment_owner = '$lower_case_address') AND transaction_hash = '$hash'";
        $payment = $wpdb->get_results($db_query);
        if( ! empty($payment) && isset($payment[0]) && ! empty($payment[0]) ) {
            $payment = $payment[0];
        }
        return $payment;
    }

    public function find_payment($payment_id, $parcel) {
        global $wpdb;
        $payment = null;
        $get_table_name = $wpdb->prefix . $this->table_name;
        $db_query = "SELECT * FROM $get_table_name WHERE payment_id = '$payment_id'";
        $payment = $wpdb->get_results($db_query);
        if( ! empty($payment) && isset($payment[0]) && ! empty($payment[0]) ) {
            $payment = $payment[0];
        }
        return $payment;
    }

    public function get_payment($db_id) {
        global $wpdb;
        $payment = null;
        $get_table_name = $wpdb->prefix . $this->table_name;
        $db_query = "SELECT * FROM $get_table_name WHERE id = '$db_id'";
        $payment = $wpdb->get_results($db_query);
        if( ! empty($payment) && isset($payment[0]) && ! empty($payment[0]) ) {
            $payment = $payment[0];
        }
        return $payment;
    }

    public function update_payment($db_id, $payment_data) {
        global $wpdb;
        $payment_updated = false;
        $update_table_name = $wpdb->prefix . $this->table_name;

        $payment_data = $this->data_stripeslashes($payment_data);
        $payment_updated = $wpdb->update(
            $update_table_name,
            $payment_data,
            array('id' => $db_id)
        );

        if( ! empty($payment_updated) ) {
            $payment_updated = true;
        }
        return $payment_updated;
    }

    public function delete_payment($db_id) {
        global $wpdb;
        $payment_deleted = false;
        $get_table_name = $wpdb->prefix . $this->table_name;
        $payment_deleted = $wpdb->delete($get_table_name, array('id' => $db_id));
        if( ! empty($payment_deleted) ) {
            $payment_deleted = true;
        }
        return $payment_deleted;
    }

    public function get_all_payments($offset, $filters) {
        global $wpdb;
        $get_table_name = $wpdb->prefix . $this->table_name;

        $db_query = "SELECT * FROM $get_table_name";

        if( ! empty($filters) ) {
            $filters = (object) $filters;
            $filters_added = false;
            if( isset($filters->token) ) {
                $db_query .= " WHERE token = '$filters->token'";
                $filters_added = true;
            }
            if( isset($filters->status) ) {
              if( $filters_added ) {
                  $db_query .= " AND transaction_status = '$filters->status'";
              } else {
                  $db_query .= " WHERE transaction_status = '$filters->status'";
              }
              $filters_added = true;
            }
            if( isset($filters->product) ) {
              if( $filters_added ) {
                  $db_query .= " AND product_id = '$filters->product'";
              } else {
                  $db_query .= " WHERE product_id = '$filters->product'";
              }
              $filters_added = true;
            }
            if( isset($filters->address) ) {
                $lower_case_address = strtolower($filters->address);
              if( $filters_added ) {
                  $db_query .= " AND (payment_owner = '$filters->address' OR payment_owner = '$lower_case_address')";
              } else {
                  $db_query .= " WHERE (payment_owner = '$filters->address' OR payment_owner = '$lower_case_address')";
              }
              $filters_added = true;
            }
            if( isset($filters->from_date) ) {
              if( $filters_added ) {
                  $db_query .= " AND transaction_time >= '$filters->from_date'";
              } else {
                  $db_query .= " WHERE transaction_time >= '$filters->from_date'";
              }
              $filters_added = true;
            }
            if( isset($filters->to_date) ) {
              if( $filters_added ) {
                  $db_query .= " AND transaction_time <= '$filters->to_date'";
              } else {
                  $db_query .= " WHERE transaction_time <= '$filters->to_date'";
              }
              $filters_added = true;
            }
        }

        $db_query .= " ORDER BY transaction_time DESC LIMIT 50 OFFSET $offset";

        $all_payment_blocks = $wpdb->get_results($db_query);
        return $all_payment_blocks;
    }

    public function get_wallet_address_payments($wallet_address, $offset, $filters) {
        global $wpdb;
        $get_table_name = $wpdb->prefix . $this->table_name;
        $lower_case_address = strtolower($wallet_address);
        $db_query = "SELECT * FROM $get_table_name WHERE (payment_owner = '$wallet_address' OR payment_owner = '$lower_case_address')";

        if( ! empty($filters) ) {
            $filters = (object) $filters;
            $filters_added = false;
            if( isset($filters->token) ) {
                $db_query .= " AND token = '$filters->token'";
                $filters_added = true;
            }
            if( isset($filters->status) ) {
                $db_query .= " AND transaction_status = '$filters->status'";
            }
        }

        $db_query .= " ORDER BY transaction_time DESC LIMIT 50 OFFSET $offset";

        $all_payment_blocks = $wpdb->get_results($db_query);
        return $all_payment_blocks;
    }

    function get_timeframe_payments($timeframe, $current_time, $token) {
        global $wpdb;
        $get_table_name = $wpdb->prefix . $this->table_name;
        $payments = $wpdb->get_results("SELECT token_amount FROM $get_table_name WHERE transaction_time <= '$current_time' AND transaction_time >= '$timeframe' AND token = '$token'");
        return $payments;
    }

    function find_network_explorer_url($network_slug) {
        $network_explorer_url = "";
        $metapress_live_mode = get_option('metapress_live_mode', 0);
        if( empty($metapress_live_mode) ) {
          $metapress_supported_networks = apply_filters('filter_web3_access_test_networks', get_option('metapress_supported_test_networks'), 'add');
        } else {
          $metapress_supported_networks = apply_filters('filter_web3_access_networks', get_option('metapress_supported_networks'), 'add');
        }
        if( ! empty($metapress_supported_networks) ) {
            foreach($metapress_supported_networks as $network) {
                if($network['slug'] == $network_slug) {
                    $network_explorer_url = $network['explorer'];
                    break;
                }
            }
        }
        return $network_explorer_url;
    }

    public function create_subscription($product_id, $subscription_data) {
        $new_subscription_id = null;
        $subscription_expires = $this->set_subscription_expiration($product_id, null);
        if( ! empty($subscription_expires) ) {
            global $wpdb;
            $add_to_table = $wpdb->prefix . $this->subscription_table_name;
            $subscription_data = $this->data_stripeslashes($subscription_data);
            $subscription_data['expires'] = intval($subscription_expires);
            $subscription_data['created_time'] = current_time('timestamp');
            $subscription_data['notice_sent'] = false;
            if( ! isset($subscription_data['price']) ) {
                $subscription_data['price'] = $this->sanitize_price(get_post_meta( $product_id, 'product_price', true ));
            }
            $subscription_added = $wpdb->insert(
                $add_to_table,
                $subscription_data
            );
            if( ! empty($subscription_added) ) {
                $new_subscription_id = $wpdb->insert_id;
            }
        }
        return $new_subscription_id;
    }

    public function get_subscription($product_id, $spender_address) {
        global $wpdb;
        $subscription = null;
        $lower_case_address = strtolower($spender_address);
        $get_table_name = $wpdb->prefix . $this->subscription_table_name;
        $db_query = "SELECT * FROM $get_table_name WHERE product_id = '$product_id' AND (payment_owner = '$spender_address' OR payment_owner = '$lower_case_address')";
        $subscription = $wpdb->get_results($db_query);
        if( ! empty($subscription) && isset($subscription[0]) && ! empty($subscription[0]) ) {
            $subscription = $subscription[0];
            if( empty($subscription->price) ) {
                $subscription->formatted_price = '0.00';
            } else {
                $subscription->formatted_price = number_format($subscription->price/100, 2, '.', '');
            }

        }
        return $subscription;
    }

    public function update_subscription($subscription_id, $subscription_data) {
        global $wpdb;
        $subscription_updated = false;
        $update_table_name = $wpdb->prefix . $this->subscription_table_name;
        $subscription_data = $this->data_stripeslashes($subscription_data);
        $subscription_updated = $wpdb->update(
            $update_table_name,
            $subscription_data,
            array('id' => $subscription_id)
        );

        if( ! empty($subscription_updated) ) {
            $subscription_updated = true;
        }
        return $subscription_updated;
    }

    public function update_subscription_time($subscription) {
        global $wpdb;
        $subscription_updated = false;
        $update_table_name = $wpdb->prefix . $this->subscription_table_name;
        $subscription_expires = $this->set_subscription_expiration($subscription->product_id, $subscription);
        $subscription_updated = $wpdb->update(
            $update_table_name,
            array('expires' => $subscription_expires),
            array('id' => $subscription->id)
        );

        if( ! empty($subscription_updated) ) {
            $subscription_updated = true;
        }
        return $subscription_updated;
    }

    public function update_subscription_price($subscription_id, $price) {
        global $wpdb;
        $subscription_updated = false;
        $update_table_name = $wpdb->prefix . $this->subscription_table_name;
        $subscription_updated = $wpdb->update(
            $update_table_name,
            array('price' => $this->sanitize_price($price)),
            array('id' => $subscription_id)
        );

        if( ! empty($subscription_updated) ) {
            $subscription_updated = true;
        }
        return $subscription_updated;
    }

    public function delete_subscription($subscription_id) {
        global $wpdb;
        $subscription_deleted = false;
        $get_table_name = $wpdb->prefix . $this->subscription_table_name;
        $subscription_deleted = $wpdb->delete($get_table_name, array('id' => $subscription_id));
        if( ! empty($subscription_deleted) ) {
            $subscription_deleted = true;
        }
        return $subscription_deleted;
    }

    private function sanitize_price($price) {
        if( ! empty($price) ) {
            if(strpos($price, ',')) {
                $price = str_replace(",", "", $price);
            }
            if(strpos($price, '.')) {
                $price = str_replace(".", "", $price);
            } else {
                $price = intval($price) * 100;
            }
        } else {
            $price = 0;
        }
        return intval($price);
    }

    private function set_subscription_expiration($product_id, $subscription) {
        $current_wp_time = current_time('timestamp');
        $subscription_expires = $current_wp_time;
        $subscription_interval_count = get_post_meta( $product_id, 'subscription_interval_count', true );
        $subscription_interval = get_post_meta( $product_id, 'subscription_interval', true );
        $subscription_expiration_text = '+'.$subscription_interval_count.' '.$subscription_interval;
        if( ! empty($subscription) && isset($subscription->expires) && $subscription->expires > $current_wp_time ) {
            $subscription_expires = strtotime($subscription_expiration_text, $subscription->expires);
        } else {
            $subscription_expires = strtotime($subscription_expiration_text, $current_wp_time);
        }
        return $subscription_expires;
    }

    public function get_all_subscriptions($offset, $filters) {
        global $wpdb;
        $get_table_name = $wpdb->prefix . $this->subscription_table_name;

        $db_query = "SELECT * FROM $get_table_name";

        if( ! empty($filters) ) {
            $filters = (object) $filters;
            $filters_added = false;
            if( isset($filters->product) ) {
                $db_query .= " WHERE product_id = '$filters->product'";
                $filters_added = true;
            }
            if( isset($filters->address) ) {
                $lower_case_address = strtolower($filters->address);
              if( $filters_added ) {
                  $db_query .= " AND (payment_owner = '$filters->address' OR payment_owner = '$lower_case_address')";
              } else {
                  $db_query .= " WHERE (payment_owner = '$filters->address' OR payment_owner = '$lower_case_address')";
              }
              $filters_added = true;
            }
        }

        $db_query .= " ORDER BY id DESC LIMIT 50 OFFSET $offset";

        $all_found_subscriptions = $wpdb->get_results($db_query);
        return $all_found_subscriptions;
    }

    public function get_wallet_subscriptions($wallet_address, $offset) {
        global $wpdb;
        $get_table_name = $wpdb->prefix . $this->subscription_table_name;
        $lower_case_address = strtolower($wallet_address);
        $db_query = "SELECT * FROM $get_table_name WHERE (payment_owner = '$wallet_address' OR payment_owner = '$lower_case_address')";
        $db_query .= " ORDER BY created_time DESC LIMIT 50 OFFSET $offset";
        $all_found_subscriptions = $wpdb->get_results($db_query);
        return $all_found_subscriptions;
    }

    public function get_renewal_reminder_subscriptions($timestamp) {
        global $wpdb;
        $get_table_name = $wpdb->prefix . $this->subscription_table_name;
        $db_query = "SELECT * FROM $get_table_name WHERE notice_sent != '1' AND expires <= '$timestamp' ORDER BY id DESC LIMIT 50";
        $all_found_subscriptions = $wpdb->get_results($db_query);
        return $all_found_subscriptions;
    }

    protected function handle_error($error_message, $error_code) {
        return array(
            'error' => $error_message,
            'code'  => $error_code
        );
    }
}
