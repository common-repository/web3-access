<?php

class MetaPress_WooCommerce_Filter_Manager extends MetaPress_Plugin_Content_Filter_Shortcodes {

    public function __construct() {
        $metapress_woocommerce_filters_enabled = get_option('metapress_woocommerce_filters_enabled');
        if( $metapress_woocommerce_filters_enabled ) {
            parent::__construct();
            add_filter('woocommerce_single_product_summary', array($this,'metapress_restrict_woocommerce_product_content'));
		    add_filter('woocommerce_is_purchasable', array($this, 'metapress_set_is_product_purchasable'), 10, 2);
        }
    }

    public function metapress_restrict_woocommerce_product_content( $content ) {
		global $wp_metapress_textdomain;
        global $wp_metapress_text_settings;
        global $post;
        $metapress_restricted_content = $content;
        $metapress_required_products = get_post_meta( $post->ID, 'metapress_required_products', true );

        if ( ! empty($metapress_required_products) ) {
            $user_has_access = false; // get user payments (check for access)
            $metapress_products_list = implode(',', $metapress_required_products);
            foreach($metapress_required_products as $product_id) {
                $metapress_product = get_post($product_id);

                if( ! empty($metapress_product) ) {
                    $access_token = null;
                    $metapress_access_token_manager = new METAPRESS_ACCESS_TOKENS_MANAGER();
                    if( isset($_GET['mpatok']) && ! empty($_GET['mpatok']) ) {
                        $metapress_access_token = sanitize_key($_GET['mpatok']);
                        $access_token = $metapress_access_token_manager->access_token($product_id, $metapress_access_token);
                        if( ! empty($access_token) && $this->session_manager->is_valid_wallet($access_token->payment_owner) ) {
                            if( isset($access_token->expires) && ($access_token->expires > current_time('timestamp') ) ) {
                                $user_has_access = true;
                                break;
                            } else {
                                $metapress_access_token_manager->delete_access_token($access_token->id);
                            }
                        }
                    }
                }
            }

            if( ! $user_has_access ) {
                $metapress_payment_options = new METAPRESS_PAYMENT_OPTIONS_GEN();
                $current_page_url = get_the_permalink($post->ID);
                $metapress_checkout_page = get_option('metapress_checkout_page');
                $metapress_checkout_page_url = null;
                if( ! empty($metapress_checkout_page) ) {
                    $metapress_checkout_page_url = get_the_permalink($metapress_checkout_page);
                }
                $metapress_restricted_content = '<div id="metapress-single-restricted-content" class="metapress-restricted-access has-multiple" data-product-ids="'.$metapress_products_list.'">';
                $metapress_restricted_content .= '<p class="metapress-restricted-text">'.$wp_metapress_text_settings['restricted_text'].'</p>';

                $metapress_restricted_content .= '<div class="metapress-login-notice"><p><small>*'.__('Accessing this content requires a', $wp_metapress_textdomain).' <a href="https://metamask.io/" target="_blank">MetaMask</a> '.__('account', $wp_metapress_textdomain).'</small>.</p></div>';

                $metapress_restricted_content .= $metapress_payment_options->generate_wallet_options_html();

                $metapress_restricted_content .= '<div class="metapress-access-buttons">';
                $metapress_restricted_content .= '<p class="metapress-checkout-purchase-text">'.$wp_metapress_text_settings['product_purchase_text'].':</p>';
                foreach($metapress_required_products as $product_id) {
                    $product_name = get_the_title($product_id);
                    if( ! empty($metapress_checkout_page_url) ) {
                        $product_checkout_page_url = $metapress_checkout_page_url.'?mpp='.$product_id.'&mpred='.$current_page_url;
                        $metapress_restricted_content .= '<a href="'.$product_checkout_page_url.'" class="metapress-checkout-button">'.$product_name.'</a>';
                    }
                }

                $metapress_restricted_content .= '</div><div class="metapress-notice-box"></div></div>';
            } else {
                if( ! empty($access_token) ) {
                	$metapress_restricted_content .= '<div class="metapress-verification" data-address="'.$access_token->payment_owner.'" data-token="'.$access_token->token.'"></div>';
                }
            }
        }
        return $metapress_restricted_content;
	}

    public function metapress_set_is_product_purchasable($is_purchasable, $product) {
        if( class_exists('METAPRESS_ACCESS_TOKENS_MANAGER') ) {
            global $post;
            if( $post && isset($post->ID) ) {
                $metapress_required_products = get_post_meta( $post->ID, 'metapress_required_products', true );

                if ( ! empty($metapress_required_products) ) {
                    $is_purchasable = false;
                    $metapress_products_list = implode(',', $metapress_required_products);
                    $metapress_access_token_manager = new METAPRESS_ACCESS_TOKENS_MANAGER();
                    foreach($metapress_required_products as $product_id) {
                        $metapress_product = get_post($product_id);
                        if( ! empty($metapress_product) ) {
                            $access_token = null;

                            if( isset($_GET['mpatok']) && ! empty($_GET['mpatok']) ) {
                                $metapress_access_token = sanitize_key($_GET['mpatok']);
                                $access_token = $metapress_access_token_manager->access_token($product_id, $metapress_access_token);
                                if( ! empty($access_token) && $this->session_manager->is_valid_wallet($access_token->payment_owner) ) {
                                    if( isset($access_token->expires) && ($access_token->expires > current_time('timestamp') ) ) {
                                        $is_purchasable = true;
                                        break;
                                    } else {
                                        $metapress_access_token_manager->delete_access_token($access_token->id);
                                    }
                                }
                            }
                        }
                    }
                }
        }
        }
        return $is_purchasable;
    }
}
$metapress_woocommerce_filter_manager = new MetaPress_WooCommerce_Filter_Manager();
