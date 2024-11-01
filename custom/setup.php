<?php
require_once('custom-meta-functions.php');
require_once('product/config.php');

class METAPRESS_PLUGIN_RUN_SETUP_MANAGER {

    public function __construct() {
        $this->run_setup();
    }

    public function run_setup() {
        $metapress_contract_addresses = get_option('metapress_contract_addresses');
        $metapress_api_request_match = get_option('metapress_api_request_match');
        $metapress_supported_networks = get_option('metapress_supported_networks');
        $metapress_supported_test_networks = get_option('metapress_supported_test_networks');
        $metapress_supported_token_types = get_option('metapress_supported_token_types');

        if( empty($metapress_contract_addresses) ) {
          $set_metapress_contact_address = (object) array(
              'sepolia' => '0x61fF69Db8D37F579BE0E0b8e84E9Ab1879d30470',
              'mainnet' => '0x00c5A679d3Ae7261e021FF80DF210De513b38042',
              'matictestnet' => '0xaddBF54A1E826436257f05E785881133f9895141',
              'maticmainnet' => '0x61fF69Db8D37F579BE0E0b8e84E9Ab1879d30470',
              'binancetestnet' => '0x9bC293b22c74a8b2eEd677e77A7c90c7aE34ace4',
              'binancesmartchain' => '0x9bC293b22c74a8b2eEd677e77A7c90c7aE34ace4',
              'avaxtestnet' => '0x61fF69Db8D37F579BE0E0b8e84E9Ab1879d30470',
          	  'avaxmainnet' => '0x61fF69Db8D37F579BE0E0b8e84E9Ab1879d30470',
              'fantomtestnet' => '0x61fF69Db8D37F579BE0E0b8e84E9Ab1879d30470',
              'fantomnetwork' => '0x61fF69Db8D37F579BE0E0b8e84E9Ab1879d30470'
          );
          update_option('metapress_contract_addresses', $set_metapress_contact_address);
        }

        if( empty($metapress_api_request_match) ) {
            $metapress_api_request_match = $this->generate_new_api_key(26);
            update_option('metapress_api_request_match', $metapress_api_request_match);
        }

        if( empty($metapress_supported_networks) ) {
            $metapress_supported_networks = array(
                array(
                    'name' => 'Ethereum Mainnet',
                    'slug' => 'mainnet',
                    'chainid' => '0x1',
                    'symbol' => 'ETH',
                    'receiving_address' => '',
                    'explorer' => 'https://etherscan.io/',
                    'icon' => METAPRESS_PLUGIN_BASE_URL.'images/ethereum.png',
                    'enabled' => 1
                ),
                array(
                    'name' => 'Polygon (MATIC)',
                    'slug' => 'maticmainnet',
                    'chainid' => '0x89',
                    'symbol' => 'MATIC',
                    'receiving_address' => '',
                    'explorer' => 'https://polygonscan.com/',
                    'icon' => METAPRESS_PLUGIN_BASE_URL.'images/polygon.png',
                    'enabled' => 1
                ),
                array(
                    'name' => 'Binance Smart Chain',
                    'slug' => 'binancesmartchain',
                    'chainid' => '0x38',
                    'symbol' => 'BNB',
                    'receiving_address' => '',
                    'explorer' => 'https://bscscan.com/',
                    'icon' => METAPRESS_PLUGIN_BASE_URL.'images/bnb.png',
                    'enabled' => 1
                ),
                array(
                    'name' => 'Avalanche',
                    'slug' => 'avaxmainnet',
                    'chainid' => '0xa86a',
                    'symbol' => 'AVAX',
                    'receiving_address' => '',
                    'explorer' => 'https://snowtrace.io/',
                    'icon' => METAPRESS_PLUGIN_BASE_URL.'images/avax.png',
                    'enabled' => 1
                ),
                array(
                    'name' => __('Fantom', 'wp-metapress'),
                    'slug' => 'fantomnetwork',
                    'chainid' => '0xfa',
                    'symbol' => 'FTM',
                    'receiving_address' => '',
                    'explorer' => 'https://ftmscan.com/',
                    'icon' => METAPRESS_PLUGIN_BASE_URL.'images/fantom.png',
                    'enabled' => 1
                )
            );
            update_option('metapress_supported_networks', $metapress_supported_networks);
        }

        if( empty($metapress_supported_test_networks) ) {
            $metapress_supported_test_networks = array(
                array(
                    'name' => 'Ethereum Sepolia',
                    'slug' => 'sepolia',
                    'chainid' => '0xaa36a7',
                    'symbol' => 'ETH',
                    'receiving_address' => '',
                    'explorer' => 'https://sepolia.etherscan.io/',
                    'icon' => METAPRESS_PLUGIN_BASE_URL.'images/ethereum.png',
                    'enabled' => 1
                ),
                array(
                    'name' => 'Polygon Mumbai',
                    'slug' => 'matictestnet',
                    'chainid' => '0x13881',
                    'symbol' => 'MATIC',
                    'receiving_address' => '',
                    'explorer' => 'https://mumbai.polygonscan.com/',
                    'icon' => METAPRESS_PLUGIN_BASE_URL.'images/polygon.png',
                    'enabled' => 1
                ),
                array(
                    'name' => 'Binance Smart Chain Testnet',
                    'slug' => 'binancetestnet',
                    'chainid' => '0x61',
                    'symbol' => 'BNB',
                    'receiving_address' => '',
                    'explorer' => 'https://testnet.bscscan.com/',
                    'icon' => METAPRESS_PLUGIN_BASE_URL.'images/bnb.png',
                    'enabled' => 1
                ),
                array(
                    'name' => 'Avalanche Fuji',
                    'slug' => 'avaxtestnet',
                    'chainid' => '0xa869',
                    'symbol' => 'AVAX',
                    'receiving_address' => '',
                    'explorer' => 'https://testnet.snowtrace.io/',
                    'icon' => METAPRESS_PLUGIN_BASE_URL.'images/avax.png',
                    'enabled' => 1
                ),
                array(
                    'name' => __('Fantom Testnet', 'wp-metapress'),
                    'slug' => 'fantomtestnet',
                    'chainid' => '0xfa2',
                    'symbol' => 'FTM',
                    'receiving_address' => '',
                    'explorer' => 'https://testnet.ftmscan.com/',
                    'icon' => METAPRESS_PLUGIN_BASE_URL.'images/fantom.png',
                    'enabled' => 1
                )
            );
            update_option('metapress_supported_test_networks', $metapress_supported_test_networks);
        }

        if( empty($metapress_supported_token_types) ) {
          $metapress_supported_token_types = array(
              array(
                  'type' => 'erc20',
                  'name' => 'ERC-20',
              ),
              array(
                  'type' => 'erc721',
                  'name' => 'ERC-721',
              ),
              array(
                  'type' => 'erc1155',
                  'name' => 'ERC-1155',
              ),
          );
          update_option('metapress_supported_token_types', $metapress_supported_token_types);
        }
    }

    private function generate_new_api_key($length) {
        if( function_exists('random_bytes') ) {
            return bin2hex(random_bytes(intval($length)));
        } else {
            $cry_strong = true;
            $random_bytes = openssl_random_pseudo_bytes(intval($length), $cry_strong);
            return bin2hex($random_bytes);
        }
    }
}
$metapress_plugin_run_setup_manager = new METAPRESS_PLUGIN_RUN_SETUP_MANAGER();
