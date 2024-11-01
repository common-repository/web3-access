<?php

class METAPRESS_PAYMENTS_MANAGER {
    protected $table_name;

    public function __construct() {
        $metapress_live_mode = get_option('metapress_live_mode', 0);
        if( empty($metapress_live_mode) ) {
          $this->table_name = 'metapress_test_payments';
        } else {
          $this->table_name = 'metapress_payments';
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
              $lower_case_address = strtolower($spender_address);
              if( $filters_added ) {
                  $db_query .= " AND (payment_owner = '$filters->address' OR payment_owner = '$lower_case_address')";
              } else {
                  $db_query .= " WHERE(payment_owner = $filters->address OR payment_owner = $lower_case_address)";
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
        $db_query = "SELECT * FROM $get_table_name WHERE (payment_owner = '$wallet_address' OR payment_owner = '$lower_case_address') = '$wallet_address'";

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

    public function get_timeframe_payments($timeframe, $current_time, $token) {
        global $wpdb;
        $get_table_name = $wpdb->prefix . $this->table_name;
        $payments = $wpdb->get_results("SELECT token_amount FROM $get_table_name WHERE transaction_time <= '$current_time' AND transaction_time >= '$timeframe' AND token = '$token'");
        return $payments;
    }

    public function find_network_explorer_url($network_slug) {
        $network_explorer_url = "";
        $metapress_live_mode = get_option('metapress_live_mode', 0);
        if( empty($metapress_live_mode) ) {
          $metapress_supported_networks = apply_filters('filter_web3_access_networks', get_option('metapress_supported_networks'), 'add');
        } else {
          $metapress_supported_networks = apply_filters('filter_web3_access_test_networks', get_option('metapress_supported_test_networks'), 'add');
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

    protected function handle_error($error_message, $error_code) {
        return array(
            'error' => $error_message,
            'code'  => $error_code
        );
    }
}
