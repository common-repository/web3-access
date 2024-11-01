<?php

class Web3_Access_Plugin_Moralis_Api_Manager extends WP_REST_Controller {
    public $current_time;
    public function __construct() {
        $this->current_time = current_time('timestamp', 1);
        $this->request_key_match = get_option('metapress_api_request_match');
        add_action( 'rest_api_init', array($this, 'register_web3_access_moralis_api_endpoint_routes') );
    }

    public function register_web3_access_moralis_api_endpoint_routes() {

        register_rest_route( 'metapress/v2', '/moralisnftlist/', array(
                'methods'  => 'GET',
                'callback' => array($this, 'list_wallet_nfts'),
                'permission_callback' => array($this, 'metapress_permissions_check' ),
            )
        );

    }

    private function verify_request_key($mp_request_key) {
        if( $mp_request_key != $this->request_key_match ) {
            return false;
        } else {
            return true;
        }
    }

    public function metapress_permissions_check( $request ) {
        $api_key = $request->get_param('request_key');
        return $this->verify_request_key($api_key);
    }

    public function list_wallet_nfts($request) {
        $wallet_address = $request->get_param('mpwalletaddress');
        $network = $request->get_param('network');

        $token_addresses = array();
        $wallet_nfts = array();
        if( empty($wallet_address) || empty($network) ) {
            return $this->handle_error( __('Invalid request', 'rogue-themes') );
        }

        if( $request->has_param('contract_address') ) {
            $token_addresses = $request->get_param('contract_address');
        }

        $web3_access_moralis_nft_manager = new Web3_Access_Moralis_NFT_Manager();
        if( $network == 'solana' ) {
            $wallet_nfts = $web3_access_moralis_nft_manager->get_wallet_solana_nfts($wallet_address, 'mainnet', $token_addresses); // mainnet or devnet
        } else {
            $chain = $request->get_param('chain');
            if( empty($chain) ) {
                return $this->handle_error( __('Missing chain id', 'rogue-themes') );
            }
            $wallet_nfts = $web3_access_moralis_nft_manager->get_wallet_nfts($wallet_address, $chain, $token_addresses);
        }

        if( empty($wallet_nfts) ) {
            $wallet_nfts = array();
        }

        return new WP_REST_Response($wallet_nfts, 200);
    }

    public function handle_error( $error_message ) {
        return new WP_REST_Response(array(
            'error' => $error_message,
        ), 400);
    }

}
$web3_access_moralis_rest_api_manager = new Web3_Access_Plugin_Moralis_Api_Manager();
