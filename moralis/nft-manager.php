<?php

class Web3_Access_Moralis_NFT_Manager {
    protected $api_url;
    protected $solana_api_url;
    protected $live_mode;
    private $api_key;

    public function __construct() {
         $this->live_mode = get_option('metapress_live_mode', 0);
         $this->api_key = get_option('metapress_moralis_api_key');
         $this->api_url = 'https://deep-index.moralis.io/api/v2/';
         $this->solana_api_url = 'https://solana-gateway.moralis.io/';
    }

    public function get_wallet_nfts($wallet_address, $chain, $token_addresses) {
        $request_url = $this->api_url . $wallet_address . '/nft';
        $request_params = array(
            'chain' => $chain,
        );
        if( ! empty($token_addresses) ) {
            $request_params['token_addresses'] = $token_addresses;
        }
        $request_url .= '?' . http_build_query($request_params);

        $request_headers = $this->set_request_headers();
        $moralis_request = wp_remote_get($request_url, array('headers' => $request_headers) );
        $moralis_request_code = wp_remote_retrieve_response_code( $moralis_request );
        $moralis_results = wp_remote_retrieve_body( $moralis_request );
        $moralis_results = json_decode($moralis_results);
        if( $moralis_request_code != 200 ) {
            if( isset($moralis_results->message) ) {
                $error_message = $moralis_results->message;
            } else {
                $error_message = wp_remote_retrieve_response_message($moralis_request);
            }
            return $this->handle_error($error_message, $moralis_request_code);
        }
        return $moralis_results;
    }

    public function get_wallet_solana_nfts($wallet_address, $network, $token_addresses) {
        $request_url = $this->solana_api_url . 'account/' . $network . '/' . $wallet_address . '/nft';

        $request_headers = $this->set_request_headers();
        $moralis_request = wp_remote_get($request_url, array('headers' => $request_headers) );
        $moralis_request_code = wp_remote_retrieve_response_code( $moralis_request );
        $moralis_results = wp_remote_retrieve_body( $moralis_request );
        $moralis_results = json_decode($moralis_results);
        if( $moralis_request_code != 200 ) {
            if( isset($moralis_results->message) ) {
                $error_message = $moralis_results->message;
            } else {
                $error_message = wp_remote_retrieve_response_message($moralis_request);
            }
            return $this->handle_error($error_message, $moralis_request_code);
        }
        return $moralis_results;
    }

    private function set_request_headers() {
        return array(
            'accept' => 'application/json',
            'X-API-KEY' => $this->api_key
        );
    }

    private function handle_error($error_message, $error_code) {
        return array(
            'error' => $error_message,
            'code'  => $error_code
        );
    }
}
