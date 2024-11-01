<?php

class METAPRESS_SCRIPTS_LOADING_MANAGER {
	public function __construct() {
    	add_action('wp_head', array($this, 'load_metapress_header_css_js'));
    	add_action( 'wp_enqueue_scripts', array($this, 'load_metapress_css_scripts') );
		add_action( 'wp_enqueue_scripts', array($this, 'load_metapress_js_scripts') );
		add_action( 'admin_enqueue_scripts', array($this, 'load_metapress_admin_css_scripts') );
		add_action( 'admin_enqueue_scripts', array($this, 'load_metapress_admin_js_scripts') );
		add_action('admin_head', array($this, 'load_metapress_admin_header_vars'));
	}

  public function load_metapress_css_scripts() {
    global $wp_metapress_version;
	$metapress_style_settings = get_option('metapress_style_settings');
	$metapress_style = 'light';
	if( ! empty($metapress_style_settings) && isset($metapress_style_settings['style']) ) {
		$metapress_style = $metapress_style_settings['style'];
	}
	$metapress_style_url = METAPRESS_PLUGIN_BASE_URL . 'css/'.$metapress_style.'.css';
	wp_register_style( 'metapress-style', $metapress_style_url, '', $wp_metapress_version );
    wp_register_style( 'metapress-notifications', METAPRESS_PLUGIN_BASE_URL . 'css/notifications.css', '', $wp_metapress_version );
    wp_register_style( 'metapress-restricted-content', METAPRESS_PLUGIN_BASE_URL . 'css/restricted.css', '', $wp_metapress_version );
	if( ! wp_style_is('dashicons', 'enqueued') ) {
		wp_enqueue_style( 'dashicons' );
	}
	wp_enqueue_style( 'metapress-style' );
    wp_enqueue_style( 'metapress-notifications' );
    wp_enqueue_style( 'metapress-restricted-content' );
  }

	public function load_metapress_js_scripts() {
		global $wp_metapress_version;
		global $post;
    	$metapress_live_mode = get_option('metapress_live_mode', 0);
		$metapress_contract_addresses = get_option('metapress_contract_addresses');
		$fiat_currency = 'USD';
		$metapress_send_to_address = get_option('metapress_ethereum_wallet_address');
		$metapress_allowed_test_address = get_option('metapress_allowed_test_address');
		$metapress_solana_wallet_address = get_option('metapress_solana_wallet_address');
		$metapress_solana_wallet_test_address = get_option('metapress_solana_wallet_test_address');
		$load_metapress_product_js = true;
    	if( empty($metapress_live_mode) ) {
			$metapress_live_mode = 0;
    	}

		$metapress_token_ratios = get_option('metapress_token_ratios', array());
		$token_ratios_last_updated = get_option('metapress_token_ratios_updated_timestamp');
		$metapress_transactions_page = get_option('metapress_transactions_page');
		$metapress_subscriptions_page = get_option('metapress_subscriptions_page');
		$metapress_opensea_api_key = get_option('metapress_opensea_api_key');
		$metapress_moralis_api_key = get_option('metapress_moralis_api_key');

    	wp_register_script('metapress-notifications', METAPRESS_PLUGIN_BASE_URL .'js/notifications.js', array('jquery', 'metamask-account'), $wp_metapress_version, true);
		wp_register_script('web3-access-wallet', METAPRESS_PLUGIN_BASE_URL .'js/wallet.js', array(), false, true);
    	wp_register_script('metamask-detect-provider', METAPRESS_PLUGIN_BASE_URL .'js/detectprovider.js', array(), false, true);
    	wp_register_script('web3-js', METAPRESS_PLUGIN_BASE_URL .'js/web3.js', false, false, true);
		wp_register_script('metapress-token-ratio-manager', METAPRESS_PLUGIN_BASE_URL .'convert/ratios.js', array('jquery'), $wp_metapress_version, true);
		wp_register_script('metamask-account', METAPRESS_PLUGIN_BASE_URL .'js/metamask.js', array('jquery', 'metamask-detect-provider', 'metapress-token-ratio-manager', 'web3-access-wallet'), $wp_metapress_version, true);
    	wp_register_script('metapress-products', METAPRESS_PLUGIN_BASE_URL .'js/products.js', array('jquery', 'metamask-account', 'metapress-token-ratio-manager'), $wp_metapress_version, true);
		wp_register_script('metapress-transactions', METAPRESS_PLUGIN_BASE_URL .'js/transactions.js', array('jquery', 'metamask-account'), $wp_metapress_version, true);
		wp_register_script('metapress-subscriptions', METAPRESS_PLUGIN_BASE_URL .'js/subscriptions.js', array('jquery', 'metamask-account'), $wp_metapress_version, true);
		wp_register_script('metapress-opensea-api', METAPRESS_PLUGIN_BASE_URL .'opensea/js/openseaapi.js', array('jquery'), $wp_metapress_version, true);
		wp_register_script('metapress-moralis-api', METAPRESS_PLUGIN_BASE_URL .'moralis/js/moralisapi.js', array('jquery'), $wp_metapress_version, true);

	  	wp_enqueue_script('metapress-notifications');
    	wp_enqueue_script('web3-js');
		wp_enqueue_script('metapress-token-ratio-manager');
		wp_enqueue_script('web3-access-wallet');
		wp_localize_script('web3-access-wallet', 'webaccesswalletjsdata', array(
			'is_admin' => current_user_can('manage_options'),
			'send_to_address' => esc_attr($metapress_send_to_address),
			'live_mode' => esc_attr($metapress_live_mode),
			'allowed_test_address' => esc_attr($metapress_allowed_test_address),
			'solana_test_address' => esc_attr($metapress_solana_wallet_test_address),
			'createsession' => rest_url('metapress/v2/walletsession'),
			'newtransaction' => rest_url('metapress/v2/newtransaction'),
			'updatetransaction' => rest_url('metapress/v2/updatetransaction'),
			'nfttoken' => rest_url('metapress/v2/nfttoken'),
    	));
    	wp_enqueue_script('metamask-account');
		wp_localize_script('metamask-account', 'metapressmetamaskjsdata', array(
			'abi' => esc_url(METAPRESS_PLUGIN_BASE_URL.'contracts/metapressabi.json'),
			'erc20_abi' => esc_url(METAPRESS_PLUGIN_BASE_URL.'contracts/erc20abi.json'),
			'erc721_abi' => esc_url(METAPRESS_PLUGIN_BASE_URL.'contracts/erc721abi.json'),
			'erc1155_abi' => esc_url(METAPRESS_PLUGIN_BASE_URL.'contracts/erc1155abi.json'),
			'contract_address' => $metapress_contract_addresses,
			'send_to_address' => esc_attr($metapress_send_to_address),
			'live_mode' => esc_attr($metapress_live_mode),
    	));

		if( $post ) {
			if( $post->ID == $metapress_transactions_page ) {
				$load_metapress_product_js = false;
				wp_enqueue_script('metapress-transactions');
				wp_localize_script('metapress-transactions', 'metapressjsdata', array(
					'api_url' => rest_url('metapress/v2/transactions'),
					'product_data_url' => rest_url('metapress/v2/productdata'),
				));
			}

			if( $post->ID == $metapress_subscriptions_page ) {
				$load_metapress_product_js = false;
				wp_enqueue_script('metapress-subscriptions');
				wp_localize_script('metapress-subscriptions', 'metapressjsdata', array(
					'api_url' => rest_url('metapress/v2/subscriptions'),
					'deletesubscription' => rest_url('metapress/v2/deletesubscription'),
					'notificationemail' => rest_url('metapress/v2/notificationemail'),
				));
			}
		}

		if($load_metapress_product_js) {
			wp_enqueue_script('metapress-products');
			wp_localize_script('metapress-products', 'metapressjsdata', array(
		        'live_mode' => esc_attr($metapress_live_mode),
		        'token_ratios' => $metapress_token_ratios,
				'fiat_currency' => esc_attr($fiat_currency),
				'tokens_updated' => esc_attr($token_ratios_last_updated),
				'current_time' => strtotime('-30 seconds', current_time('timestamp', 1)),
				'how_to_add_text' => __('How to add Polygon to MetaMask', 'wp-metapress'),
				'endpoints' => array(
					'getprice' => rest_url('metapress/v2/productprice'),
					'access' => rest_url('metapress/v2/productaccess'),
					'paid' => rest_url('metapress/v2/paytransaction'),
					'deletetx' => rest_url('metapress/v2/deletetransaction'),
				),
		    ));
		}

		if( ! empty($metapress_opensea_api_key) ) {
			wp_enqueue_script('metapress-opensea-api');
			wp_localize_script('metapress-opensea-api', 'metapressopensea', array(
		        'api_key' => esc_attr($metapress_opensea_api_key),
		    ));
		}

		if( ! empty($metapress_moralis_api_key) ) {
			wp_enqueue_script('metapress-moralis-api');
			wp_localize_script('metapress-moralis-api', 'metapressmoralis', array(
		        'endpoints' => array(
					'verifynftowner' => rest_url('metapress/v2/moralisnftlist'),
				),
		    ));
		}

	}

  public function load_metapress_header_css_js() {
    global $post;
	global $wp_metapress_textdomain;
	$metapress_live_mode = get_option('metapress_live_mode', 0);
	if( empty($metapress_live_mode) ) {
		$metapress_live_mode = 0;
	}
    $metapress_plugin_head_script_data = array(
        'ajaxurl' => admin_url( "admin-ajax.php" ),
		'live_mode' => esc_attr($metapress_live_mode),
    );
    if( is_user_logged_in() ) {
        $metapress_plugin_head_script_data['user']['id'] = get_current_user_id();
    }
	$metapress_plugin_head_script_data['user']['nonce'] = wp_create_nonce('wp_rest');
    if( $post && isset($post->ID) ) {
        $metapress_plugin_head_script_data['post']['id'] = $post->ID;
    }

	$metapress_plugin_head_script_data['payments'] = array(
		'view_on_network' => __('View On Network', $wp_metapress_textdomain),
		'product_title' => __('Product', $wp_metapress_textdomain),
		'from_address' => __('From Address', $wp_metapress_textdomain),
		'paid_with' => __('Paid With', $wp_metapress_textdomain),
		'amount_title' => __('Amount', $wp_metapress_textdomain),
		'network_title' => __('Network', $wp_metapress_textdomain),
		'date_title' => __('Date', $wp_metapress_textdomain),
		'status_title' => __('Status', $wp_metapress_textdomain),
		'view_title' => __('View', $wp_metapress_textdomain),
		'no_payments' => __('No More Payments', $wp_metapress_textdomain),
		'access_item_list_heading' => __('Product Access', $wp_metapress_textdomain),
		'no_access' => __('No Access Found', $wp_metapress_textdomain),
	);
	$metapress_plugin_head_script_data['subscriptions'] = array(
		'no_subscriptions' => __('No More Subscriptions', $wp_metapress_textdomain),
		'expires_title' => __('Expires', $wp_metapress_textdomain),
		'price_title' => __('Price', $wp_metapress_textdomain),
		'wallet_address' => __('Wallet Address', $wp_metapress_textdomain),
		'reminder_email' => __('Reminder Email', $wp_metapress_textdomain),
		'renew_now' => __('Renew Now', $wp_metapress_textdomain),
	);
	$metapress_plugin_head_script_data['api'] = array(
		'request_key' => esc_attr(get_option('metapress_api_request_match')),
	);

	$metapress_style_settings = get_option('metapress_style_settings');
	if( empty($metapress_style_settings) ) {
		$metapress_style_settings = array(
            'style' => 'light',
            'accent_color' => '#70b513'
        );
	}

    ?>
    <script type="text/javascript">
        const metapressmanagerrequests = <?php echo wp_json_encode($metapress_plugin_head_script_data); ?>;
    </script>
	<style type="text/css">
		.loadingCircle, .metapress-verify-button span.verify-text, .metapress-notice-box span.metapress-confirm-transaction,
		a.metapress-checkout-button, .metapress-nav-button .metapress-button, .metapress-login-notice button.metamask-connect-wallet {
		   background: <?php echo esc_attr( $metapress_style_settings['accent_color']); ?>;
		}
		.metapress-loading-text, .metapress-payment-button-amount, .metapress-change-wallet-button, .metapress-set-payment-method.active,
		.metapress-update-subscription:hover > .dashicons {
			color: <?php echo esc_attr( $metapress_style_settings['accent_color']); ?>;
		}

    </style>

  <?php }

	public function load_metapress_admin_header_vars() {
    if( is_user_logged_in() && current_user_can('manage_options') ) {
		global $wp_metapress_textdomain;
		$metapress_plugin_admin_head_script_data = array(
			'ajaxurl' => admin_url( "admin-ajax.php" ),
			'payments' => array(
				'view_on_network' => __('View On Network', $wp_metapress_textdomain),
				'product_title' => __('Product ID', $wp_metapress_textdomain),
				'from_address' => __('From Address', $wp_metapress_textdomain),
				'paid_with' => __('Paid With', $wp_metapress_textdomain),
				'amount_title' => __('Amount', $wp_metapress_textdomain),
				'network_title' => __('Network', $wp_metapress_textdomain),
				'date_title' => __('Date', $wp_metapress_textdomain),
				'status_title' => __('Status', $wp_metapress_textdomain),
				'view_title' => __('View', $wp_metapress_textdomain),
			),
			'subscriptions' => array(
				'expires_title' => __('Expires', $wp_metapress_textdomain),
				'price_title' => __('Price', $wp_metapress_textdomain),
			),
			'tokens' => array(
				'contract_not_found' => __('Could not find information for that token address on CoinGecko. Make sure it exists on the Network selected below. The address should still work, but you will need to manually fill in all fields.', $wp_metapress_textdomain)
			),
		); ?>
		<script type="text/javascript">
        var metapressadminmanagerrequests = <?php echo wp_json_encode($metapress_plugin_admin_head_script_data); ?>;
    	</script>
	<?php
    }
 }
	public function load_metapress_admin_css_scripts() {
    global $wp_metapress_version;
		global $post;
    	wp_register_style( 'metapress-admin-settings', METAPRESS_PLUGIN_BASE_URL . 'admin/css/metapress.css', '', $wp_metapress_version );
		wp_register_style( 'metapress-product-edit-admin', METAPRESS_PLUGIN_BASE_URL . 'custom/product/css/admin.css', '', $wp_metapress_version );
		wp_register_style( 'metapress-datepicker-admin', METAPRESS_PLUGIN_BASE_URL . 'admin/css/datepicker.css', '', $wp_metapress_version );
    	wp_enqueue_style( 'metapress-admin-settings' );
		wp_enqueue_style( 'metapress-product-edit-admin' );
  }

	public function load_metapress_admin_js_scripts() {
    global $wp_metapress_version;
	global $post;
    wp_register_script( 'metapress-admin-payments', METAPRESS_PLUGIN_BASE_URL . 'admin/js/payments.js', array('jquery'), $wp_metapress_version, true);
	wp_register_script( 'metapress-admin-subscriptions', METAPRESS_PLUGIN_BASE_URL . 'admin/js/subscriptions.js', array('jquery'), $wp_metapress_version, true);
	wp_register_script( 'metapress-admin-image-upload', METAPRESS_PLUGIN_BASE_URL . 'admin/js/image-upload.js', array('jquery'), $wp_metapress_version, true);
	wp_register_script('metapress-token-ratio-manager', METAPRESS_PLUGIN_BASE_URL .'convert/ratios.js', array('jquery'), $wp_metapress_version, true);
	wp_register_script( 'metapress-admin-tokens', METAPRESS_PLUGIN_BASE_URL . 'admin/js/tokens.js', array('jquery'), $wp_metapress_version, true);
	wp_register_script( 'metapress-admin-nfts', METAPRESS_PLUGIN_BASE_URL . 'admin/js/nfts.js', array('jquery'), $wp_metapress_version, true);
	wp_register_script( 'metapress-admin-product-nfts', METAPRESS_PLUGIN_BASE_URL . 'admin/js/product-nfts.js', array('jquery'), $wp_metapress_version, true);
	wp_register_script( 'metapress-admin-networks', METAPRESS_PLUGIN_BASE_URL . 'admin/js/networks.js', array('jquery'), $wp_metapress_version, true);
	wp_register_script( 'metapress-admin-style', METAPRESS_PLUGIN_BASE_URL . 'admin/js/style.js', array('jquery'), $wp_metapress_version, true);
	if( $post && isset($post->post_type) && $post->post_type == 'metapress_product') {
		wp_enqueue_script( 'metapress-admin-image-upload' );
		wp_enqueue_script( 'metapress-admin-product-nfts' );
	}
  }
}
$metapress_scripts_loading_manager = new METAPRESS_SCRIPTS_LOADING_MANAGER();
