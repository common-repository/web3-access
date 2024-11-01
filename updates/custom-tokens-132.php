<?php
$metapress_custom_tokens_list = get_option('metapress_custom_tokens_list', array());

if( ! empty($metapress_custom_tokens_list) ) {
    foreach($metapress_custom_tokens_list as $key => &$custom_token) {
        $custom_token['binance_price_api'] = 1;
        $custom_token['coingecko_price_api'] = 1;
        $custom_token['usd_price'] = 0;
    }
    update_option('metapress_custom_tokens_list', $metapress_custom_tokens_list);
}
