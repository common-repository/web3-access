<?php

class METAPRESS_GUTENBERG_BLOCKS_MANAGER {
	protected $assets_path;
	protected $session_manager;
	public function __construct() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}
		$this->assets_path = plugin_dir_url(__FILE__);
		$this->session_manager = new WEB3_ACCESS_SESSIONS_MANAGER();
		add_action( 'init', array($this, 'create_metapress_restricted_content_block') );
		add_filter( 'render_block', array($this, 'metapress_block_filter'), 10, 2 );
	}

	public function create_metapress_restricted_content_block() {
		global $wp_metapress_version;

		wp_register_script('metapress-restricted-content-block-js', $this->assets_path .'/block.js', array( 'wp-blocks', 'wp-i18n', 'wp-element', 'wp-editor', 'underscore' ), $wp_metapress_version);

	  	wp_localize_script( 'metapress-restricted-content-block-js', 'metapressblockcontent',
	      	array(
				'product_list' => $this->create_editor_product_list(),
			)
		);

		wp_register_style(
			'metapress-gutenberg-block-editor-css',
			$this->assets_path .'/editor.css',
			array( 'wp-edit-blocks' ),
			$wp_metapress_version
		);

		wp_register_style(
			'metapress-restricted-content-block-css',
			$this->assets_path .'style.css',
			array(),
			$wp_metapress_version
		);

		register_block_type( 'metapress-blocks/restricted-content-block', array(
			'style' => 'metapress-restricted-content-block-css',
			'editor_style' => 'metapress-gutenberg-block-editor-css',
			'editor_script' => 'metapress-restricted-content-block-js',
	        'attributes'      => array(
	            'product_id'    => array(
	                'type'      => 'number',
	                'default'   => 0,
	            ),
	        ),
		) );
	}

	public function metapress_block_filter( $block_content, $block ) {
		global $wp_metapress_textdomain;
		global $wp_metapress_text_settings;
		if ( $block['blockName'] === 'metapress-blocks/restricted-content-block' ) {
			$metapress_restricted_content = $block_content;
			$product_id = null;
			if( isset($block['attrs']['product_id']) && ! empty($block['attrs']['product_id']) ) {
				$product_id = $block['attrs']['product_id'];
			}

			if( ! empty($product_id) ) {
				$access_token = null;
				$user_has_access = false; // get user payments (check for access)
				if( isset($_GET['mpatok']) && ! empty($_GET['mpatok']) ) {
					$metapress_access_token = sanitize_key($_GET['mpatok']);
					$metapress_access_token_manager = new METAPRESS_ACCESS_TOKENS_MANAGER();
          			$access_token = $metapress_access_token_manager->access_token($product_id, $metapress_access_token);

					if( ! empty($access_token) && $this->session_manager->is_valid_wallet($access_token->payment_owner) ) {
						if( isset($access_token->expires) && ($access_token->expires > current_time('timestamp') ) ) {
								$user_has_access = true;
						} else {
								$metapress_access_token_manager->delete_access_token($access_token->id);
						}
					}
				}
				if( ! $user_has_access ) {
					$metapress_payment_options = new METAPRESS_PAYMENT_OPTIONS_GEN();
					$product_price = get_post_meta( $product_id, 'product_price', true );
					$product_is_subscription = get_post_meta( $product_id, 'product_is_subscription', true );
					$metapress_restricted_content = '<div class="metapress-restricted-access" data-product-id="'.$product_id.'">';
					$metapress_restricted_content .= '<p class="metapress-restricted-text">'.$wp_metapress_text_settings['restricted_text'].'</p>';

					if( $product_is_subscription && isset($wp_metapress_text_settings['subscription_product_notice']) && ! empty($wp_metapress_text_settings['subscription_product_notice']) ) {
	                    $metapress_restricted_content .= '<div class="metapress-subscription-notice">'.sanitize_text_field($wp_metapress_text_settings['subscription_product_notice']).'</div>';
	                }

					$metapress_restricted_content .= '<div class="metapress-login-notice"><p><small>*'.__('Accessing this content requires a', $wp_metapress_textdomain).' <a href="https://metamask.io/" target="_blank">MetaMask</a> '.__('account', $wp_metapress_textdomain).', or a browser wallet</small>.</p></div>';

					$metapress_restricted_content .= $metapress_payment_options->generate_wallet_options_html();

					$metapress_restricted_content .= $metapress_payment_options->generate_payment_options_html($product_id);

					$metapress_restricted_content .= '<div class="metapress-notice-box"></div></div>';
				} else {
					if( ! empty($access_token) ) {
						$metapress_restricted_content .= '<div class="metapress-verification" data-address="'.$access_token->payment_owner.'" data-token="'.$access_token->token.'"></div>';
					}
				}
			}
			return wp_kses_post($metapress_restricted_content);
    	} else {
			return $block_content;
		}

	}

	protected function create_editor_product_list() {
		$product_list = array(array(
			'product_id' => 0,
			'product_name' => 'None'
		));
		$metapress_product_args = array(
				'post_type' => 'metapress_product',
				'posts_per_page' => -1,
				'post_status' => 'publish'
		);
		$metapress_products = get_posts($metapress_product_args);
		if( ! empty($metapress_products) ) {
			foreach($metapress_products as $product) {
				$product_list[] = array(
					'product_id' => $product->ID,
					'product_name' => $product->post_title
				);
			}
		}
		return $product_list;
	}
}
$metapress_gutenberg_blocks_manager = new METAPRESS_GUTENBERG_BLOCKS_MANAGER();
