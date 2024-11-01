<?php
$metapress_wallet_addresses = get_option('metapress_wallet_addresses');
if( ! empty($metapress_wallet_addresses) ) {
    foreach($metapress_wallet_addresses as $wallet_address) {
        if( ! empty($wallet_address['address']) ) {
            update_option('metapress_ethereum_wallet_address', $wallet_address['address']);
        }
    }
}
delete_option('metapress_test_wallet_addresses');
