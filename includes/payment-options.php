<?php

class METAPRESS_PAYMENT_OPTIONS_GEN {

	private $supported_networks;
    private $custom_tokens = array();
	protected $live_mode;
	protected $image_folder;
	public $text_domain;
	public $text_settings;


	public function __construct() {
		global $wp_metapress_textdomain;
		global $wp_metapress_text_settings;
		$this->text_domain = $wp_metapress_textdomain;
		$this->text_settings = $wp_metapress_text_settings;
		$metapress_live_mode = get_option('metapress_live_mode', 0);
        if( $metapress_live_mode ) {
			$this->live_mode = true;
			$this->supported_networks = apply_filters('filter_web3_access_networks', get_option('metapress_supported_networks'), 'add');
        } else {
			$this->live_mode = false;
			$this->supported_networks = apply_filters('filter_web3_access_test_networks', get_option('metapress_supported_test_networks'), 'add');
        }
		$this->custom_tokens = get_option('metapress_custom_tokens_list', array());
		$this->image_folder = METAPRESS_PLUGIN_BASE_URL.'images';
	}

	public function generate_payment_options_html($product_id) {
		$metapress_wallet_address = get_option('metapress_ethereum_wallet_address');
		$metapress_solana_wallet_address = get_option('metapress_solana_wallet_address');
		$metapress_payment_options = "";
		if( ! empty($product_id) ) {
			$product_price = get_post_meta( $product_id, 'product_price', true );
			$metapress_payment_options = '<div class="metapress-access-buttons">';

			$metapress_change_payment_options = '<div class="metapress-change-payment-options">';
			$metapress_change_payment_options .= '<div class="metapress-set-payment-method wallet-payment" data-section="#metapress-wallet-payment">'.sanitize_text_field(__('Wallet Payment', $this->text_domain)).'</div>';
			$metapress_change_payment_options .= '<div class="metapress-set-payment-method nft-verification" data-section="#metapress-nft-verification">'.sanitize_text_field(__('NFT Verification', $this->text_domain)).'</div>';
			$metapress_change_payment_options .= '</div>';

			$metapress_payment_options .= $metapress_change_payment_options;

			if( ! empty($product_price) && $product_price > 0 ) {
				$metapress_payment_options .= '<div id="metapress-wallet-payment" class="metapress-payment-method">';
				if( ! empty($this->supported_networks) ) {
					foreach($this->supported_networks as $network_button) {
						if( isset($network_button['enabled']) ) {

							if( empty($network_button['receiving_address']) ) {
								if( $network_button['slug'] == 'solana' ) {
									if( ! empty($metapress_solana_wallet_address) ) {
										$network_button['receiving_address'] = $metapress_solana_wallet_address;
									}
								} else {
									if( ! empty($metapress_wallet_address) ) {
										$network_button['receiving_address'] = $metapress_wallet_address;
									}
								}
							}

							$network_pay_with_text = sanitize_text_field(__('Pay with ', $this->text_domain).$network_button['symbol']);
							$metapress_payment_options .= '<div class="metapress-payment-button" data-product-id="'.$product_id.'" data-token="'.$network_button['symbol'].'" data-network="'.$network_button['slug'].'" data-networkname="'.$network_button['name'].'" data-chainid="'.$network_button['chainid'].'"';

							$metapress_payment_options .= ' data-wallet="'.$network_button['receiving_address'].'" data-explorer="'.$network_button['explorer'].'"><img src="'.$network_button['icon'].'" alt="'.$network_pay_with_text.'" /> <span class="metapress-payment-button-text">'.$network_pay_with_text.'</span><span class="metapress-payment-button-amount"></span></div> ';
						}
					}
				}

				// ADD CUSTOM TOKENS
                if( ! empty($this->custom_tokens) ) {
                    foreach($this->custom_tokens as $custom_token) {
						if( isset($custom_token['enabled']) ) {
                            $pay_with_text = sanitize_text_field(__('Pay with ', $this->text_domain).$custom_token['name']);
							if( $this->live_mode ) {
                                $token_network = sanitize_text_field($custom_token['network']);
                                $token_network_name = sanitize_text_field($custom_token['networkname']);
                                $token_network_chainid = sanitize_text_field($custom_token['chainid']);
								$token_network_explorer = sanitize_url($custom_token['explorer']);
                                $add_test_token_class = "";
                            } else {
                                $token_network = sanitize_text_field($custom_token['test_network']);
                                $token_network_name = sanitize_text_field($custom_token['test_networkname']);
                                $token_network_chainid = sanitize_text_field($custom_token['test_chainid']);
								$token_network_explorer = sanitize_url($custom_token['test_explorer']);
                                $add_test_token_class = " test-token";
                            }
							$metapress_payment_options .= '<div class="metapress-payment-button'.$add_test_token_class.'" data-product-id="'.$product_id.'" data-token="'.$custom_token['currency_symbol'].'" data-network="'.$token_network.'" data-address="'.$custom_token['contract_address'].'"  data-test-address="'.$custom_token['test_contract_address'].'"';

                            $metapress_payment_options .= ' data-explorer="'.$token_network_explorer.'" data-networkname="'.$token_network_name.'" data-chainid="'.$token_network_chainid.'"><img src="'.$custom_token['icon'].'" alt="'.$pay_with_text.'" /> <span class="metapress-payment-button-text">'.$pay_with_text.'</span><span class="metapress-payment-button-amount"></span></div> ';
						}
                    }
                }
				// END CUSTOM TOKENS
				$metapress_payment_options .= '</div>';
			}

			// CHECK FOR NFT ACCESS
			$metapress_payment_options .= $this->generate_product_nft_verification_options($product_id);

			$metapress_payment_options .= '</div>';
		}
		return $metapress_payment_options;
	}

	public function generate_product_nft_verification_options($product_id) {
		// CHECK FOR NFT ACCESS
		$product_nft_access_list = get_post_meta($product_id, 'product_nft_access_list', true );

		$metapress_nft_verify_options = '';
		if( ! empty($product_nft_access_list) ) {
			$metapress_nft_verify_options .= '<div id="metapress-nft-verification" class="metapress-payment-method">';
			$nft_access_nonce = strtotime('+1 hour', current_time('timestamp', 1));
			$metapress_nft_verify_options .= '<div id="metapress-nft-verification-text" data-noncetimestamp="'.$nft_access_nonce.'"><p>'.$this->text_settings['ownership_verification_text'].'.</p></div>';
			foreach($product_nft_access_list as $nft_token) {
				if( ! isset($nft_token['minimum_balance']) ) {
					$nft_token['minimum_balance'] = 1;
				}
				$nft_token_data = $this->sanitize_nft_array($nft_token);
				$nft_collection_slug = "";
				$nft_minimum_balance = 1;
				if( isset($nft_token_data['token_image']) && ! empty($nft_token_data['token_image']) ) {
					$nft_asset_image = $nft_token_data['token_image'];
				} else {
					$nft_asset_image = METAPRESS_PLUGIN_BASE_URL.'images/metapress-logo-icon.png';
				}

				if( isset($nft_token_data['collection_slug']) && ! empty($nft_token_data['collection_slug']) ) {
					$nft_collection_slug = $nft_token_data['collection_slug'];
				}

				if( ! empty($nft_token_data['minimum_balance']) ) {
					$nft_minimum_balance = $nft_token_data['minimum_balance'];
					if( $nft_minimum_balance < 1 ) {
						$nft_minimum_balance = 1;
					}
				}

				$metapress_nft_verify_options .= '<div class="metapress-verify-nft-owner"
				data-token="'.$nft_token_data['token_id'].'"
				data-contract-address="'.$nft_token_data['contract_address'].'"
				data-token-type="'.$nft_token_data['token_type'].'"
				data-network="'.$nft_token_data['network'].'"
				data-networkname="'.$nft_token_data['networkname'].'"
				data-chainid="'.$nft_token_data['chainid'].'"
				data-product-id="'.$product_id.'"
				data-minimum="'.$nft_minimum_balance.'"
				data-collection="'.$nft_collection_slug.'">';

				$metapress_nft_verify_options .= '<img src="'.$nft_asset_image.'" alt="'.$nft_token_data['token_name'].'" /> <span class="nft-name">'.$nft_token_data['token_name'].'</span>';
				$metapress_nft_verify_options .= '<div class="metapress-verify-buttons"><div class="metapress-verify-button"><span class="verify-text">'.__('Confirm Ownership', $this->text_domain).'</span></div><div class="metapress-verify-link"><a class="nft-link" href="'.$nft_token_data['token_url'].'" target="_blank" title="'.__('View NFT', $this->text_domain).'">'.__('View', $this->text_domain).' <span class="dashicons dashicons-external"></span></a></div></div></div>';
			}
			$metapress_nft_verify_options .= '</div>';
		}
		return $metapress_nft_verify_options;
	}

	public function generate_wallet_options_html() {
		global $wp_metapress_textdomain;
		$metapress_wallet_options_content = '<div id="metapress-connect-change-wallet">';

		$metapress_wallet_options_content .= '<div class="metapress-change-wallet-button"><span class="metapress-change-wallet-text">'.__('Not Connected', $this->text_domain).'</span> <span class="dashicons dashicons-arrow-down-alt2"></span></div>';

		$metapress_wallet_options_content .= '</div>';

		$metapress_wallet_options_content .= '<div id="metapress-wallet-options">';
		$metapress_wallet_options_content .= '<div class="metapress-connect-wallet-option metamask-connect-wallet" data-wallettype="ethereum"><div class="metapress-wallet-image"><img src="'.$this->image_folder.'/metamask.png" alt="Connect With Metamask" /></div><div class="metapress-wallet-name">'.__('MetaMask (Ethereum)', $this->text_domain).'</div><div class="wallet-connection-status"><span class="dashicons dashicons-yes-alt"></span></div></div>';

		$metapress_wallet_options_content .= '<div class="metapress-connect-wallet-option metamask-connect-wallet" data-wallettype="ethereum"><div class="metapress-wallet-image"><img src="'.$this->image_folder.'/coinbase.png" alt=Connect with Coinbase" /></div><div class="metapress-wallet-name">'.__('Coinbase (Ethereum)', $this->text_domain).'</div><div class="wallet-connection-status"><span class="dashicons dashicons-yes-alt"></span></div></div>';

		$metapress_wallet_options_content .= '<div class="metapress-connect-wallet-option metamask-connect-wallet" data-wallettype="ethereum"><div class="metapress-wallet-image"><img src="'.$this->image_folder.'/ethereumwallet.png" alt=Connect with Ethereum Wallet" /></div><div class="metapress-wallet-name">'.__('Other Wallet (Ethereum)', $this->text_domain).'</div><div class="wallet-connection-status"><span class="dashicons dashicons-yes-alt"></span></div></div>';

		$metapress_wallet_options_content .= '<div class="metapress-connect-wallet-option phantom-connect-wallet" data-wallettype="solana"><div class="metapress-wallet-image"><img src="'.$this->image_folder.'/phantom.png" alt=Connect with Phantom" /></div><div class="metapress-wallet-name">'.__('Phantom (Solana)', $this->text_domain).'</div><div class="wallet-connection-status"><span class="dashicons dashicons-yes-alt"></span></div></div>';

		$metapress_wallet_options_content .= '</div>';

		return $metapress_wallet_options_content;

	}

	protected function sanitize_nft_array($array_data) {
		if( ! empty($array_data) && is_array($array_data) ) {
			$array_data['name'] = sanitize_text_field($array_data['name']);
			$array_data['contract_address'] = sanitize_text_field($array_data['contract_address']);
			$array_data['token_id'] = sanitize_text_field($array_data['token_id']);
			$array_data['token_name'] = sanitize_text_field($array_data['token_name']);
			$array_data['token_image'] = sanitize_url($array_data['token_image']);
			$array_data['token_url'] = sanitize_url($array_data['token_url']);
			$array_data['collection_slug'] = sanitize_text_field($array_data['collection_slug']);
			$array_data['token_type'] = sanitize_text_field($array_data['token_type']);
			$array_data['network'] = sanitize_text_field($array_data['network']);
			$array_data['networkname'] = sanitize_text_field($array_data['networkname']);
			$array_data['chainid'] = sanitize_key($array_data['chainid']);
			$array_data['minimum_balance'] = intval($array_data['minimum_balance']);
		} else {
			$array_data = array();
		}
		return $array_data;
	}
}
