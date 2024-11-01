<?php

class WEB3_ACCESS_SOLANA_SCRIPTS_LOADING_MANAGER {
	public function __construct() {

		add_action( 'wp_enqueue_scripts', array($this, 'load_metapress_js_scripts') );

	}

  public function load_metapress_css_scripts() {
    global $wp_metapress_version;

  }

	public function load_metapress_js_scripts() {
		global $wp_metapress_version;
		global $post;
    	$metapress_live_mode = get_option('metapress_live_mode', 0);
		$metapress_solana_rpc_url = get_option('metapress_solana_rpc_url', 'https://api.mainnet-beta.solana.com/');

    	//wp_register_script('web3-access-solana-web3', 'https://unpkg.com/@solana/web3.js@latest/lib/index.iife.min.js', array(), $wp_metapress_version, true);

		wp_register_script('web3-access-solana-web3', METAPRESS_PLUGIN_BASE_URL .'solana/js/solanaWeb3.browser.js', array(), $wp_metapress_version, true);
		wp_register_script('web3-access-solana', METAPRESS_PLUGIN_BASE_URL .'solana/js/solana.js', array('jquery', 'web3-access-solana-web3'), $wp_metapress_version, true);

		wp_enqueue_script('web3-access-solana');
		wp_localize_script('web3-access-solana-web3', 'metapresssolanawebsjsdata', array(
			'rpc_url' => $metapress_solana_rpc_url,
    	));
		wp_localize_script('web3-access-solana', 'metapresssolanajsdata', array(
			'live_mode' => esc_attr($metapress_live_mode),
			'tx_fee_address' => 'ByLpCkADbHnFW3YWsHQn4fruFz2WXZDBD3qxxP3suMXK',
    	));


	}

  public function load_metapress_header_css_js() {

  }

  public function load_metapress_admin_header_vars() {

 }
  public function load_metapress_admin_css_scripts() {

  }

  public function load_metapress_admin_js_scripts() {

  }
}
$web3_access_solana_scripts_loading_manager = new WEB3_ACCESS_SOLANA_SCRIPTS_LOADING_MANAGER();
