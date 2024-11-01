<?php

class WEB3_ACCESS_SESSIONS_MANAGER {
    private $wallet_address;
    public function __construct() {
    }

    public function create_metapress_session() {
        if( $this->is_session_started() === FALSE ) {
            session_start([
                'read_and_close' => true,
            ]);
        }
    }

    public function end_metapress_session() {
        if( $this->is_session_started() === TRUE ) {
            session_destroy();
        }
    }

    public function session_wallet_address() {
        $metapress_session_wallet_address = $this->get_session_wallet();
        return $metapress_session_wallet_address;
        if( empty($metapress_session_wallet_address) ) {
            return false;
        } else {
            return $metapress_session_wallet_address;
        }
    }

    private function is_session_started() {
        $metapress_session_started = FALSE;
        if ( php_sapi_name() !== 'cli' ) {
            if ( version_compare(phpversion(), '5.4.0', '>=') ) {
                if( session_status() === PHP_SESSION_ACTIVE ) {
                    $metapress_session_started = TRUE;
                }
            } else {
                if( session_id() === '' ) {
                    $metapress_session_started = FALSE;
                }
            }
        }
        return $metapress_session_started;
    }

    public function set_session_wallet( $wallet_address ) {
        if( $this->is_session_started() === FALSE ) {
            session_start();
        }
        $_SESSION['metapress_wallet_address'] = sanitize_text_field($wallet_address);
        session_write_close();
    }

    private function get_session_wallet() {
        $wallet = "";
        if( ! empty($_SESSION) && isset($_SESSION['metapress_wallet_address']) ) {
            $wallet = sanitize_text_field($_SESSION['metapress_wallet_address']);
        }
        return $wallet;
    }

    public function is_valid_wallet( $wallet_address ) {
        if( sanitize_text_field($wallet_address) == $this->get_session_wallet() ) {
            return true;
        } else {
            return false;
        }
    }
}
