<?php

if( defined('METAPRESS_PLUGIN_BASE_URL') ) {
    $web3access_supported_networks = array(
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
    if( ! empty($web3access_supported_networks) ) {
        update_option('metapress_supported_networks', $web3access_supported_networks);
    }

    $web3access_supported_test_networks = array(
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
    if( ! empty($web3access_supported_test_networks) ) {
        update_option('metapress_supported_test_networks', $web3access_supported_test_networks);
    }
}
