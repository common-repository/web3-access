<?php

class METAPRESS_ACCESS_TOKENS_MANAGER {
    protected $table_name;

    public function __construct() {
        $this->table_name = 'metapress_access_tokens';
    }

    public function create_access_token($token_data) {
        $new_access_token = null;
        if( $this->table_name ) {
            global $wpdb;
            $add_to_table = $wpdb->prefix . $this->table_name;
            $new_token = $this->generate_new_access_token(26);
            $metapress_access_tokens_expire = get_option('metapress_access_tokens_expire', '+1 hour');
            if( empty($metapress_access_tokens_expire) ) {
                $metapress_access_tokens_expire = '+1 hour';
            }
            $token_data['token'] = $new_token;
            $token_data['expires'] = strtotime($metapress_access_tokens_expire, current_time('timestamp'));
            $token_added = $wpdb->insert(
                $add_to_table,
                $token_data
            );
            if( ! empty($token_added) ) {
                $new_access_token = $new_token;
            }
        }
        return $new_access_token;
    }

    public function access_token($product_id, $token) {
        global $wpdb;
        $access_token = null;
        $get_table_name = $wpdb->prefix . $this->table_name;
        $db_query = "SELECT * FROM $get_table_name WHERE product_id = '$product_id' AND token = '$token'";
        $access_token = $wpdb->get_results($db_query);
        if( ! empty($access_token) && isset($access_token[0]) && ! empty($access_token[0]) ) {
            $access_token = $access_token[0];
        }
        return $access_token;
    }

    public function confirm_access_token($access_token, $spender_address) {
        global $wpdb;
        $access_token = null;
        $lower_case_address = strtolower($spender_address);
        $get_table_name = $wpdb->prefix . $this->table_name;
        $db_query = "SELECT * FROM $get_table_name WHERE token = '$access_token' AND (payment_owner = '$spender_address' OR payment_owner = '$lower_case_address')";
        $access_token = $wpdb->get_results($db_query);
        if( ! empty($access_token) && isset($access_token[0]) && ! empty($access_token[0]) ) {
            $access_token = $access_token[0];
        }
        return $access_token;
    }

    public function token_exists($product_id, $spender_address) {
        global $wpdb;
        $access_token = null;
        $lower_case_address = strtolower($spender_address);
        $get_table_name = $wpdb->prefix . $this->table_name;
        $db_query = "SELECT * FROM $get_table_name WHERE product_id = '$product_id' AND (payment_owner = '$spender_address' OR payment_owner = '$lower_case_address')";
        $access_tokens = $wpdb->get_results($db_query);
        if( ! empty($access_tokens) && isset($access_tokens[0]) && ! empty($access_tokens[0]) ) {
            $access_token = $access_tokens[0];
            if( count($access_tokens) > 1 ) {
                foreach($access_tokens as $check_token) {
                    if( $check_token->expires <= current_time('timestamp') ) {
                        $this->delete_access_token($check_token->id);
                    }
                }
            }
        }
        return $access_token;
    }

    public function get_access_token($db_id) {
        global $wpdb;
        $access_token = null;
        $get_table_name = $wpdb->prefix . $this->table_name;
        $db_query = "SELECT * FROM $get_table_name WHERE id = '$db_id'";
        $access_token = $wpdb->get_results($db_query);
        if( ! empty($access_token) && isset($access_token[0]) && ! empty($access_token[0]) ) {
            $access_token = $access_token[0];
        }
        return $access_token;
    }

    public function update_access_token($db_id, $access_token_data) {
        global $wpdb;
        $access_token_updated = false;
        $update_table_name = $wpdb->prefix . $this->table_name;

        $access_token_updated = $wpdb->update(
            $update_table_name,
            $access_token_data,
            array('id' => $db_id)
        );

        if( ! empty($access_token_updated) ) {
            $access_token_updated = true;
        }
        return $access_token_updated;
    }

    public function delete_access_token($db_id) {
        global $wpdb;
        $access_token_deleted = false;
        $get_table_name = $wpdb->prefix . $this->table_name;
        $access_token_deleted = $wpdb->delete($get_table_name, array('id' => $db_id));
        if( ! empty($access_token_deleted) ) {
            $access_token_deleted = true;
        }
        return $access_token_deleted;
    }

    private function generate_new_access_token($length) {
        if( function_exists('random_bytes') ) {
            return bin2hex(random_bytes(intval($length)));
        } else {
            $cry_strong = true;
            $random_bytes = openssl_random_pseudo_bytes(intval($length), $cry_strong);
            return bin2hex($random_bytes);
        }
    }

    protected function handle_error($error_message, $error_code) {
        return array(
            'error' => $error_message,
            'code'  => $error_code
        );
    }
}
