<?php

if( ! class_exists('METAPRESS_DB_TABLES_MANAGER') ) {
class METAPRESS_DB_TABLES_MANAGER {
    private $db_table_names;
    private $charset_collate;
    public function __construct() {
        global $wpdb;
        $this->db_table_names = array(
            'metapress_payments',
            'metapress_test_payments',
            'metapress_access_tokens',
            'metapress_subscriptions',
            'metapress_test_subscriptions'
        );
        $this->charset_collate = $wpdb->get_charset_collate();
        $this->load_tables();
    }

    private function load_tables() {
        if( ! empty($this->db_table_names) && is_array($this->db_table_names) ) {
            foreach($this->db_table_names as $metapress_table_name) {
                if( ! $this->table_exists($metapress_table_name) ) {
                    $this->create_table($metapress_table_name);
                }
            }
        }
    }

    private function table_exists($table_name) {
        global $wpdb;
        $db_table_name = $wpdb->prefix . $table_name;
        $table_exists = true;
        if($wpdb->get_var("SHOW TABLES LIKE '$db_table_name'") != $db_table_name) {
             $table_exists = false;
        }
        return $table_exists;
    }

    private function create_table($table_name) {
        global $wpdb;
        $add_new_tables = "";
        if( $table_name == 'metapress_payments' || $table_name == 'metapress_test_payments' ) {
            $db_table_name = $wpdb->prefix . $table_name;
            $add_new_tables = "CREATE TABLE $db_table_name (
              id int NOT NULL AUTO_INCREMENT,
              product_id int NOT NULL,
              payment_owner tinytext NOT NULL,
              token tinytext NOT NULL,
              token_amount DECIMAL(36,18) NOT NULL,
              sent_amount DECIMAL(36,18) NOT NULL,
              network tinytext NOT NULL,
              transaction_time int NOT NULL,
              transaction_hash tinytext NOT NULL,
              transaction_status tinytext NOT NULL,
              contract_address tinytext,
              PRIMARY KEY  (id)
            ) $this->charset_collate;";
        }

        if( $table_name == 'metapress_access_tokens' ) {
            $db_table_name = $wpdb->prefix . $table_name;
            $add_new_tables = "CREATE TABLE $db_table_name (
              id int NOT NULL AUTO_INCREMENT,
              product_id int NOT NULL,
              payment_owner tinytext NOT NULL,
              token tinytext NOT NULL,
              expires int NOT NULL,
              PRIMARY KEY  (id)
            ) $this->charset_collate;";
        }

        if( $table_name == 'metapress_subscriptions' || $table_name == 'metapress_test_subscriptions' ) {
            $db_table_name = $wpdb->prefix . $table_name;
            $add_new_tables = "CREATE TABLE $db_table_name (
              id int NOT NULL AUTO_INCREMENT,
              product_id int NOT NULL,
              payment_owner tinytext NOT NULL,
              expires int NOT NULL,
              price tinytext,
              created_time int NOT NULL,
              payment_method tinytext,
              user_id int,
              notification_email varchar(320),
              notice_sent BOOLEAN,
              custom_data mediumtext,
              PRIMARY KEY  (id)
            ) $this->charset_collate;";
        }

        if( ! empty($add_new_tables) ) {
            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $add_new_tables );
        }
        $this->update_table_data();
    }

    protected function update_table_data() {
        global $wpdb;
        global $wp_metapress_version;
        if( ! empty($wp_metapress_version) ) {
            $current_version_number = intval(str_replace(".","",$wp_metapress_version));
            if($current_version_number < 102) {
                if( $this->table_exists('metapress_payments') ) {
                    $table_name = $wpdb->prefix . 'metapress_payments';
                    $wpdb->query("ALTER TABLE $table_name ADD contract_address tinytext AFTER transaction_status");
                }

                if( $this->table_exists('metapress_test_payments') ) {
                    $test_table_name = $wpdb->prefix . 'metapress_test_payments';
                    $wpdb->query("ALTER TABLE $test_table_name ADD contract_address tinytext AFTER transaction_status");
                }
            }
        }
    }
}
$metapress_db_tables_manager = new METAPRESS_DB_TABLES_MANAGER();
}
