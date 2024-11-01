<?php

class Web3_Access_Solana_Network_Filter {
    private $api_key;

    public function __construct() {
         add_filter('filter_web3_access_networks', array($this, 'add_networks'), 10, 2);
         add_filter('filter_web3_access_test_networks', array($this, 'add_test_networks'), 10, 2);
         //add_filter('filter_web3_access_token_types', array($this, 'add_token_type'));
    }

    public function add_networks( $networks, $action ) {

        if( ! $this->network_exists($networks, 'solana') ) {

            if( $action == 'add' ) {
                $networks[] = array(
                    'name' => __('Solana', 'wp-metapress'),
                    'slug' => 'solana',
                    'chainid' => '0xsolana',
                    'symbol' => 'SOL',
                    'receiving_address' => '',
                    'explorer' => 'https://solscan.io/',
                    'icon' => METAPRESS_PLUGIN_BASE_URL.'images/solana.png',
                    'enabled' => 1
                );
            }

        } else {
            if( $action == 'remove' ) {
                $networks = $this->remove_solana($networks, 'solana');
            }
        }

        return $networks;
    }

    public function add_test_networks( $networks, $action ) {

        if( ! $this->network_exists($networks, 'solanadevnet') ) {
            if( $action == 'add' ) {
                $networks[] = array(
                    'name' => __('Solana Devnet', 'wp-metapress'),
                    'slug' => 'solanadevnet',
                    'chainid' => '0xsolana',
                    'symbol' => 'SOL',
                    'receiving_address' => '',
                    'explorer' => 'https://solscan.io/',
                    'icon' => METAPRESS_PLUGIN_BASE_URL.'images/solana.png',
                    'enabled' => 1
                );
            }
        } else {
            if( $action == 'remove' ) {
                $networks = $this->remove_solana($networks, 'solanadevnet');
            }
        }
        return $networks;
    }

    private function network_exists($networks, $network_slug) {
        $network_added = false;
        if( ! empty($networks) ) {
            foreach($networks as $network) {
                if($network['slug'] == $network_slug) {
                    $network_added = true;
                    break;
                }
            }
        }
        return $network_added;
    }

    private function remove_solana($networks, $network_slug) {
        if( ! empty($networks) ) {
            foreach($networks as $key => &$network) {
                if($network['slug'] == $network_slug) {
                    unset($networks[$key]);
                    break;
                }
            }
        }
        return $networks;
    }

    public function add_token_type( $token_types ) {
        $token_types = (array) $token_types;
        $token_types[] = array(
            'type' => 'slp',
            'name' => 'SLP',
        );
        return $token_types;
    }

}
$web3_access_solana_network_filter = new Web3_Access_Solana_Network_Filter();
