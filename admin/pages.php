<?php

class METAPRESS_PLUGIN_SETTINGS_MANAGER {
  public $text_domain;

  public function __construct() {
    global $wp_metapress_textdomain;
    $this->text_domain = $wp_metapress_textdomain;
    add_action( 'admin_init', array($this, 'register_metapress_settings') );
    add_action( 'admin_menu', array($this, 'add_metapress_admin_pages') );
  }

  public function register_metapress_settings() {
    register_setting('metapress-plugin-options', 'metapress_live_mode');
    register_setting('metapress-plugin-options', 'metapress_binance_cron');
    register_setting('metapress-plugin-options', 'metapress_supported_post_types', array(
        'type' => 'array',
        'default' => array('post', 'page')
    ));
    register_setting('metapress-plugin-options', 'metapress_woocommerce_filters_enabled');
    register_setting('metapress-plugin-options', 'metapress_checkout_page');
    register_setting('metapress-plugin-options', 'metapress_transactions_page');
    register_setting('metapress-plugin-options', 'metapress_subscriptions_page');
    register_setting('metapress-plugin-tokens', 'metapress_custom_tokens_list');
    register_setting('metapress-plugin-nft-contracts', 'metapress_nft_contract_list');
    register_setting('metapress-plugin-language', 'metapress_text_settings');

    register_setting('metapress-plugin-networks', 'metapress_supported_networks', array(
        'type' => 'array',
        'default' => array(
            array(
                'name' => __('Ethereum Mainnet', $this->text_domain),
                'slug' => 'mainnet',
                'chainid' => '0x1',
                'symbol' => 'ETH',
                'receiving_address' => '',
                'explorer' => 'https://etherscan.io/',
                'icon' => METAPRESS_PLUGIN_BASE_URL.'images/ethereum.png',
                'enabled' => 1
            ),
            array(
                'name' => __('Polygon (MATIC)', $this->text_domain),
                'slug' => 'maticmainnet',
                'chainid' => '0x89',
                'symbol' => 'MATIC',
                'receiving_address' => '',
                'explorer' => 'https://polygonscan.com/',
                'icon' => METAPRESS_PLUGIN_BASE_URL.'images/polygon.png',
                'enabled' => 1
            ),
            array(
                'name' => __('Binance Smart Chain', $this->text_domain),
                'slug' => 'binancesmartchain',
                'chainid' => '0x38',
                'symbol' => 'BNB',
                'receiving_address' => '',
                'explorer' => 'https://bscscan.com/',
                'icon' => METAPRESS_PLUGIN_BASE_URL.'images/bnb.png',
                'enabled' => 0
            ),
            array(
                'name' => __('Avalanche', $this->text_domain),
                'slug' => 'avaxmainnet',
                'chainid' => '0xa86a',
                'symbol' => 'AVAX',
                'receiving_address' => '',
                'explorer' => 'https://snowtrace.io/',
                'icon' => METAPRESS_PLUGIN_BASE_URL.'images/avax.png',
                'enabled' => 0
            ),
            array(
                'name' => __('Fantom', $this->text_domain),
                'slug' => 'fantomnetwork',
                'chainid' => '0xfa',
                'symbol' => 'FTM',
                'receiving_address' => '',
                'explorer' => 'https://ftmscan.com/',
                'icon' => METAPRESS_PLUGIN_BASE_URL.'images/fantom.png',
                'enabled' => 0
            )
        )
    ));

    register_setting('metapress-plugin-networks', 'metapress_supported_test_networks', array(
        'type' => 'array',
        'default' => array(
            array(
                'name' => __('Ethereum Sepolia', $this->text_domain),
                'slug' => 'sepolia',
                'chainid' => '0xaa36a7',
                'symbol' => 'ETH',
                'receiving_address' => '',
                'explorer' => 'https://sepolia.etherscan.io/',
                'enabled' => 1
            ),
            array(
                'name' => __('Polygon Mumbai', $this->text_domain),
                'slug' => 'matictestnet',
                'chainid' => '0x13881',
                'symbol' => 'MATIC',
                'receiving_address' => '',
                'explorer' => 'https://mumbai.polygonscan.com/',
                'enabled' => 1
            ),
            array(
                'name' => __('Binance Smart Chain', $this->text_domain),
                'slug' => 'binancetestnet',
                'chainid' => '0x61',
                'symbol' => 'BNB',
                'receiving_address' => '',
                'explorer' => 'https://testnet.bscscan.com/',
                'enabled' => 0
            ),
            array(
                'name' => __('Avalanche Fuji', $this->text_domain),
                'slug' => 'avaxtestnet',
                'chainid' => '0xa869',
                'symbol' => 'AVAX',
                'receiving_address' => '',
                'explorer' => 'https://testnet.snowtrace.io/',
                'icon' => METAPRESS_PLUGIN_BASE_URL.'images/avax.png',
                'enabled' => 0
            ),
            array(
                'name' => __('Fantom Testnet', $this->text_domain),
                'slug' => 'fantomtestnet',
                'chainid' => '0xfa2',
                'symbol' => 'FTM',
                'receiving_address' => '',
                'explorer' => 'https://testnet.ftmscan.com/',
                'icon' => METAPRESS_PLUGIN_BASE_URL.'images/fantom.png',
                'enabled' => 0
            )
        )
    ));

    register_setting('metapress-plugin-style', 'metapress_style_settings', array(
        'type' => 'array',
        'default' => array(
            'style' => 'light',
            'accent_color' => '#70b513'
        )
    ));

    register_setting('metapress-access-settings', 'metapress_restrict_site_access');
    register_setting('metapress-access-settings', 'metapress_redirect_type');
    register_setting('metapress-access-settings', 'metapress_redirect_page_id');
    register_setting('metapress-access-settings', 'metapress_redirect_custom_url');
    register_setting('metapress-access-settings', 'metapress_access_tokens_expire');
    register_setting('metapress-access-settings', 'metapress_site_access_required_products');

    register_setting('metapress-api-key-settings', 'metapress_opensea_api_key');
    register_setting('metapress-api-key-settings', 'metapress_moralis_api_key');

    register_setting('metapress-plugin-wallets', 'metapress_ethereum_wallet_address');
    register_setting('metapress-plugin-wallets', 'metapress_allowed_test_address');
    register_setting('metapress-plugin-wallets', 'metapress_solana_wallet_address');
    register_setting('metapress-plugin-wallets', 'metapress_solana_wallet_test_address');
    register_setting('metapress-plugin-wallets', 'metapress_solana_rpc_url');

  }

  public function add_metapress_admin_pages() {
      add_menu_page(
          __( 'Web3 Access', $this->text_domain),
          'Web3 Access',
          'manage_options',
          'metapress-settings',
          array($this, 'load_admin_settings_page'),
          'dashicons-database',
          30
      );
      add_submenu_page( 'metapress-settings', __( 'Web3 Access Settings', $this->text_domain), 'Settings', 'manage_options', 'metapress-settings', array($this, 'load_admin_settings_page') );
      add_submenu_page( 'metapress-settings', 'Wallet Addresses', 'Wallet Addresses', 'manage_options', 'metapress-wallet-addresses', array($this, 'load_admin_wallet_addresses_page') );
      add_submenu_page( 'metapress-settings', 'Access Settings', 'Access Settings', 'manage_options', 'metapress-access-settings', array($this, 'load_admin_access_settings_page') );
      add_submenu_page( 'metapress-settings', 'Payments', 'Payments', 'manage_options', 'metapress-payments', array($this, 'load_admin_payments_page') );
      add_submenu_page( 'metapress-settings', 'Subscriptions', 'Subscriptions', 'manage_options', 'metapress-subscriptions', array($this, 'load_admin_subscriptions_page') );
      add_submenu_page( 'metapress-settings', 'Payment Tokens', 'Payment Tokens', 'manage_options', 'metapress-tokens', array($this, 'load_admin_tokens_page') );
      add_submenu_page( 'metapress-settings', 'Smart Contracts (NFTs)', 'Smart Contracts (NFTs)', 'manage_options', 'metapress-nft-contracts', array($this, 'load_admin_nft_contracts_page') );
      add_submenu_page( 'metapress-settings', 'Networks', 'Networks', 'manage_options', 'metapress-networks', array($this, 'load_admin_networks_page') );
      add_submenu_page( 'metapress-settings', 'API Keys', 'API Keys', 'manage_options', 'metapress-api-keys', array($this, 'load_admin_api_keys_page') );
      add_submenu_page( 'metapress-settings', __( 'Language', $this->text_domain), 'Language', 'manage_options', 'metapress-language-settings', array($this, 'load_admin_language_settings_page') );
      add_submenu_page( 'metapress-settings', __( 'Styling', $this->text_domain), 'Styling', 'manage_options', 'metapress-style-settings', array($this, 'load_admin_style_settings_page') );
  }

  public function load_admin_settings_page() {
      require_once('admin-settings.php');
  }
  public function load_admin_wallet_addresses_page() {
      require_once('admin-wallets.php');
  }
  public function load_admin_payments_page() {
      wp_enqueue_style('metapress-datepicker-admin');
      wp_enqueue_script('jquery-ui-datepicker');
      wp_enqueue_script( 'metapress-admin-payments' );
      require_once('admin-payments.php');
  }
  public function load_admin_subscriptions_page() {
      wp_enqueue_script( 'metapress-admin-subscriptions' );
      require_once('admin-subscriptions.php');
  }
  public function load_admin_tokens_page() {
      $metapress_supported_networks = apply_filters('filter_web3_access_networks', get_option('metapress_supported_networks'), 'remove');
      $metapress_supported_test_networks = apply_filters('filter_web3_access_test_networks', get_option('metapress_supported_test_networks'), 'remove');
      wp_enqueue_media();
      wp_enqueue_script( 'metapress-admin-image-upload' );
      wp_enqueue_script( 'metapress-token-ratio-manager' );
      wp_enqueue_script( 'metapress-admin-tokens' );
      wp_localize_script('metapress-admin-tokens', 'metapressjsdata', array(
          'network_options' => $metapress_supported_networks,
          'test_network_options' => $metapress_supported_test_networks,
      ));
      require_once('admin-tokens.php');
  }
  public function load_admin_nft_contracts_page() {
      $metapress_supported_token_types = get_option('metapress_supported_token_types');
      $metapress_supported_networks = apply_filters('filter_web3_access_networks', get_option('metapress_supported_networks'), 'remove');
      $metapress_supported_test_networks = apply_filters('filter_web3_access_test_networks', get_option('metapress_supported_test_networks'), 'remove');
      wp_enqueue_script( 'metapress-admin-nfts' );
      wp_localize_script('metapress-admin-nfts', 'metapressjsdata', array(
          'network_options' => $metapress_supported_networks,
          'token_options' => $metapress_supported_token_types,
      ));
      require_once('admin-nft-contracts.php');
  }

  public function load_admin_api_keys_page() {
      require_once('admin-api-keys.php');
  }

  public function load_admin_networks_page() {
      // $metapress_live_mode = get_option('metapress_live_mode', 0);
      // wp_enqueue_media();
      // wp_enqueue_script( 'metapress-admin-image-upload' );
      // wp_enqueue_script( 'metapress-admin-networks' );
      // wp_localize_script('metapress-admin-networks', 'metapressjsdata', array(
      //     'live_mode' => $metapress_live_mode,
      // ));
      require_once('admin-networks.php');
  }

  public function load_admin_access_settings_page() {
      require_once('admin-access-settings.php');
  }

  public function load_admin_language_settings_page() {
      require_once('admin-language-settings.php');
  }

  public function load_admin_style_settings_page() {
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script('wp-color-picker' );
    wp_enqueue_script('metapress-admin-style');
    require_once('admin-style-settings.php');
  }
}
$metapress_plugin_settings_manager = new METAPRESS_PLUGIN_SETTINGS_MANAGER();
