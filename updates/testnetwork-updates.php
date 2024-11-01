<?php

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


$metapress_supported_test_networks = get_option('metapress_supported_test_networks');
if( ! empty($metapress_supported_test_networks) ) {
    foreach($metapress_supported_test_networks as $key => &$web3_access_test_network) {
        if( $web3_access_test_network['chainid'] == '0x5' ) {
            $web3_access_test_network['chainid'] = '0xaa36a7';
            $web3_access_test_network['name'] = 'Ethereum Sepolia';
            $web3_access_test_network['slug'] = 'sepolia';
            $web3_access_test_network['explorer'] = 'https://sepolia.etherscan.io/';
        }
    }
    update_option('metapress_supported_test_networks', $metapress_supported_test_networks);
}