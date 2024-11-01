<?php
$metapress_text_settings_update = get_option('metapress_text_settings');
if( ! empty($metapress_text_settings_update) && ! isset($metapress_text_settings_update['subscription_product_notice']) ) {
    $metapress_text_settings_update['subscription_product_notice'] = __('This is a subscription product and requires a renewal to keep access to its content. Renewals may be done via a Web3 browser wallet or NFT Verification, depending on the content owners settings.', $wp_metapress_textdomain);
    update_option('metapress_text_settings', $metapress_text_settings_update);
}
