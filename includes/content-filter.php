<?php

class MetaPress_Plugin_Content_Filter_Shortcodes {

    protected $current_time;
    protected $session_manager;
    public function __construct() {
        $metapress_restrict_site_access = get_option('metapress_restrict_site_access', 0);
        $this->current_time = current_time('timestamp');
        $this->session_manager = new WEB3_ACCESS_SESSIONS_MANAGER();
        add_filter('the_content', array($this,'metapress_restrict_post_type_content_with_products'));
        add_filter('the_content', array($this,'metapress_filter_metaproduct_content'));
		add_shortcode('metapress-checkout', array($this, 'metapress_product_checkout_shortcode'));
        add_shortcode('metapress-restricted-content', array($this, 'metapress_restricted_content_shortcode'));
		add_shortcode('metapress-transactions', array($this, 'metapress_user_transactions_shortcode'));
        add_shortcode('metapress-subscriptions', array($this, 'metapress_user_subscriptions_shortcode'));
        if( $metapress_restrict_site_access ) {
            add_action('template_redirect', array($this, 'metapress_restrict_entire_site_access'));
        }

    }

    private function is_admin_viewing_page() {
        if( is_user_logged_in() && current_user_can('manage_options') ) {
            return true;
        } else {
            return false;
        }
    }

    public function metapress_restrict_post_type_content_with_products( $content ) {
		global $wp_metapress_textdomain;
        global $wp_metapress_text_settings;
        global $post;
        $metapress_restricted_content = "";
        $user_has_access = true;
        $access_token = null;
        $is_admin_viewing_page = $this->is_admin_viewing_page();
        $metapress_required_products = get_post_meta($post->ID, 'metapress_required_products', true );

        $metapress_woocommerce_filters_enabled = get_option('metapress_woocommerce_filters_enabled');
        if( $metapress_woocommerce_filters_enabled && $post->post_type == 'product' ) {
            return $content;
        }

        $metapress_restrict_site_access = get_option('metapress_restrict_site_access', 0);
        if( $metapress_restrict_site_access ) {
            $metapress_allow_page_access = get_post_meta($post->ID, 'metapress_allow_page_access', true);
            $metapress_checkout_page = get_option('metapress_checkout_page');
            $metapress_transactions_page = get_option('metapress_transactions_page');
            $metapress_subscriptions_page = get_option('metapress_subscriptions_page');
            $metapress_allowed_pages = array();

            if( $metapress_allow_page_access ) {
                $metapress_allowed_pages[] = $post->ID;
            }

            if( ! empty($metapress_checkout_page) ) {
                $metapress_allowed_pages[] = $metapress_checkout_page;
            }

            if( ! empty($metapress_transactions_page) ) {
                $metapress_allowed_pages[] = $metapress_transactions_page;
            }

            if( ! empty($metapress_subscriptions_page) ) {
                $metapress_allowed_pages[] = $metapress_subscriptions_page;
            }

            if( empty($metapress_required_products) && ! is_page($metapress_allowed_pages) ) {
                $metapress_required_products = get_option('metapress_site_access_required_products');
            }
        }

        if( $is_admin_viewing_page && isset($_GET['metapressadminview']) && $_GET['metapressadminview'] = 'viewing' ) {
            $metapress_required_products = array();
        }

        if ( ! empty($metapress_required_products) ) {
            $user_has_access = false;
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
                            if( isset($access_token->expires) && ($access_token->expires > $this->current_time ) ) {
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

                $metapress_restricted_content .= '<div class="metapress-login-notice"><p><small>*'.__('Accessing this content requires a', $wp_metapress_textdomain).' <a href="https://metamask.io/" target="_blank">MetaMask</a> '.__('account', $wp_metapress_textdomain).', or a browser wallet</small>.</p></div>';

                $metapress_restricted_content .= $metapress_payment_options->generate_wallet_options_html();

                $metapress_restricted_content .= '<div class="metapress-access-buttons">';
                $metapress_restricted_content .= '<p class="metapress-checkout-purchase-text">'.$wp_metapress_text_settings['product_purchase_text'].':</p>';

                if( $is_admin_viewing_page && empty($metapress_checkout_page_url) ) {
                    $metapress_restricted_content .= '<p><strong>(ADMIN) *You need to <a href="'.admin_url('admin.php?page=metapress-settings').'">create a checkout page</a> for buttons to appear*</strong></p>';
                }

                foreach($metapress_required_products as $product_id) {
                    $product_name = get_the_title($product_id);
                    $product_price = get_post_meta($product_id, 'product_price', true);
                    if( ! empty($product_price) && is_float($product_price) ) {
                        $product_price = number_format($product_price, 2, '.', '');
                    }

                    if( ! empty($metapress_checkout_page_url) ) {
                        $product_checkout_page_url = $metapress_checkout_page_url.'?mpp='.$product_id.'&mpred='.$current_page_url;
                        $metapress_restricted_content .= '<a href="'.$product_checkout_page_url.'" class="metapress-checkout-button">'.$product_name;
                        if( ! empty($product_price) ) {
                            $metapress_restricted_content .= ' <span class="metapress-inline-price">($'.$product_price.')</span>';
                        }
                        $metapress_restricted_content .= '</a>';
                    }
                    $metapress_restricted_content .= $metapress_payment_options->generate_product_nft_verification_options($product_id);
                }

                $metapress_restricted_content .= '</div><div class="metapress-notice-box"></div></div>';

                if( $is_admin_viewing_page ) {
                    $admin_viewing_page_url = $current_page_url .= '?metapressadminview=viewing';
                    $metapress_restricted_content .= '<div class="metapress-admin-view-as-link"><a href="'.$admin_viewing_page_url.'">'.__('View Page As Admin', $wp_metapress_textdomain).'</div>';
                }
            }
        }

        if( $user_has_access ) {
            if( ! empty($access_token) ) {
                $content .= '<div class="metapress-verification" data-address="'.esc_attr($access_token->payment_owner).'" data-token="'.esc_attr($access_token->token).'"></div>';
            }
            return $content;
        } else {
            return wp_kses_post($metapress_restricted_content);
        }
	}

    public function metapress_filter_metaproduct_content( $content ) {
        global $wp_metapress_textdomain;
        global $post;
        if ( $post && $post->post_type == 'metapress_product' ) {
            $metapress_checkout_page = get_option('metapress_checkout_page');
            $product_image = get_the_post_thumbnail_url($post->ID, 'medium');
            if( has_excerpt($post->ID) ) {
                $product_description = get_the_excerpt($post->ID);
            } else {
                $product_description = "";
            }
            $metapress_restricted_content = '<div class="single-metapress-product metapress-border-box metapress-align-content-center">';
            $metapress_restricted_content .= '<h3>'.$post->post_title.'</h3>';
            if( ! empty($product_image) ) {
                $metapress_restricted_content .= '<div class="metapress-product-image"><img src="'.$product_image.'" alt="'.$post->post_title.'"></div>';
            }
            if( ! empty($product_description) ) {
                $metapress_restricted_content .= '<div class="metapress-product-description">'.$product_description.'</div>';
            }
            if( ! empty($metapress_checkout_page) ) {
                $metapress_checkout_page_url = get_the_permalink($metapress_checkout_page);
                $current_product_url = get_the_permalink($post->ID);
                if( ! empty($metapress_checkout_page_url) ) {
                    $metapress_checkout_page_url .= '?mpp='.$post->ID;
                    $metapress_restricted_content .= '<a href="'.$metapress_checkout_page_url.'&mpred='.$current_product_url.'" class="metapress-checkout-button">'.__('Buy Now', $wp_metapress_textdomain).'</a>';
                }
            }
            $metapress_restricted_content .= '</div>';
            return wp_kses_post($metapress_restricted_content);
        } else {
            return $content;
        }

	}

	public function metapress_product_checkout_shortcode( $atts ) {
		global $wp_metapress_textdomain;
        global $wp_metapress_text_settings;
		$product_id = null;
		$user_has_access = false;
		  if( isset($_GET['mpp']) && ! empty($_GET['mpp']) ) {
		      $product_id = intval($_GET['mpp']);
		  }

          if( isset($_GET['mpred']) && ! empty($_GET['mpred']) ) {
		      $metapress_redirect_url = esc_url($_GET['mpred']);
		  } else {
              $metapress_redirect_url = "";
          }

		  $metapress_restricted_content = "";
		  if( ! empty($product_id) ) {
		      $access_token = null;
		      $product_name = get_the_title($product_id);
              $product_image = get_the_post_thumbnail_url($product_id, 'medium');
              $product_description = get_the_excerpt($product_id);
              $product_is_subscription = get_post_meta( $product_id, 'product_is_subscription', true );
              if( isset($wp_metapress_text_settings['checkout_purchasing_text']) && ! empty($wp_metapress_text_settings['checkout_purchasing_text']) ) {
                  $checkout_description = sanitize_text_field($wp_metapress_text_settings['checkout_purchasing_text']);
              } else {
                  $checkout_description = __('You are purchasing ', $wp_metapress_textdomain).'{product_title}';
              }
              $checkout_description = str_replace('{product_title}', '<strong>'.$product_name.'</strong>', $checkout_description);

              if( $product_is_subscription && isset($wp_metapress_text_settings['subscription_product_notice']) && ! empty($wp_metapress_text_settings['subscription_product_notice']) ) {
                  $checkout_description .= '<div class="metapress-subscription-notice">'.sanitize_text_field($wp_metapress_text_settings['subscription_product_notice']).'</div>';
              }

		      if( isset($_GET['mpatok']) && ! empty($_GET['mpatok']) ) {
		          $metapress_access_token = sanitize_key($_GET['mpatok']);
		          $metapress_access_token_manager = new METAPRESS_ACCESS_TOKENS_MANAGER();
		          $access_token = $metapress_access_token_manager->access_token($product_id, $metapress_access_token);
		          if( ! empty($access_token) && $this->session_manager->is_valid_wallet($access_token->payment_owner) ) {
		              if( isset($access_token->expires) && ($access_token->expires > $this->current_time ) ) {
		                  $user_has_access = true;
		              } else {
		                  $metapress_access_token_manager->delete_access_token($access_token->id);
		              }
		          }
		      }
		      if( ! $user_has_access ) {
                  $metapress_payment_options = new METAPRESS_PAYMENT_OPTIONS_GEN();
                  $product_price = get_post_meta( $product_id, 'product_price', true );
		          $metapress_restricted_content = '<div class="metapress-checkout-access" data-product-id="'.$product_id.'">';
                  if( ! empty($product_image) ) {
                      $metapress_restricted_content .= '<img class="metapress-checkout-image" src="'.$product_image.'" alt="'.$product_name.'">';
                  }
    		      $metapress_restricted_content .= '<p>'.$checkout_description.'</p>';
                  if( ! empty($product_description) ) {
                      $metapress_restricted_content .= '<p class="metapress-product-description">'.$product_description.'</p>';
                  }

		          $metapress_restricted_content .= '<div class="metapress-login-notice"><p><small>*'.__('Accessing this content requires a', $wp_metapress_textdomain).' <a href="https://metamask.io/" target="_blank">MetaMask</a> '.__('account', $wp_metapress_textdomain).', or a browser wallet</small>.</p></div>';

                  $metapress_restricted_content .= $metapress_payment_options->generate_wallet_options_html();

		          $metapress_restricted_content .= $metapress_payment_options->generate_payment_options_html($product_id);

		          $metapress_restricted_content .= '<div class="metapress-notice-box"></div></div>';
		      } else {
		          $metapress_restricted_content = '<p>'.__('You already have access to this product', $wp_metapress_textdomain).'.</p>';
		      }
		  } else {
              $metapress_restricted_content = '<p>'.__('Missing product ID', $wp_metapress_textdomain).'.</p>';
          }
		  return wp_kses_post($metapress_restricted_content);
		}

        public function metapress_restricted_content_shortcode( $atts, $content ) {
            global $post;
            global $wp_metapress_text_settings;
            global $wp_metapress_textdomain;
            $metapress_restricted_content = "";
            $metapress_required_products = array();
            $user_has_access = true;
            $is_admin_viewing_page = $this->is_admin_viewing_page();
            $metapress_shortcode_atts = shortcode_atts( array(
                'products'    => array(),
            ), $atts );

            if( isset($metapress_shortcode_atts['products']) && ! empty($metapress_shortcode_atts['products']) ) {
                $metapress_required_products = explode(",", $metapress_shortcode_atts['products']);
            }

            if ( ! empty($metapress_required_products) ) {
                $user_has_access = false;
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
                                if( isset($access_token->expires) && ($access_token->expires > $this->current_time ) ) {
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

                    $metapress_restricted_content .= '<div class="metapress-login-notice"><p><small>*'.__('Accessing this content requires a', $wp_metapress_textdomain).' <a href="https://metamask.io/" target="_blank">MetaMask</a> '.__('account', $wp_metapress_textdomain).', or a browser wallet</small>.</p></div>';

                    $metapress_restricted_content .= $metapress_payment_options->generate_wallet_options_html();

                    $metapress_restricted_content .= '<div class="metapress-access-buttons">';
                    $metapress_restricted_content .= '<p class="metapress-checkout-purchase-text">'.$wp_metapress_text_settings['product_purchase_text'].':</p>';

                    if( $is_admin_viewing_page && empty($metapress_checkout_page_url) ) {
                        $metapress_restricted_content .= '<p><strong>(ADMIN) *You need to <a href="'.admin_url('admin.php?page=metapress-settings').'">create a checkout page</a> for buttons to appear*</strong></p>';
                    }

                    foreach($metapress_required_products as $product_id) {
                        $product_name = get_the_title($product_id);
                        $product_price = get_post_meta($product_id, 'product_price', true);
                        if( ! empty($product_price) && is_float($product_price) ) {
                            $product_price = number_format($product_price, 2, '.', '');
                        }

                        if( ! empty($metapress_checkout_page_url) ) {
                            $product_checkout_page_url = $metapress_checkout_page_url.'?mpp='.$product_id.'&mpred='.$current_page_url;
                            $metapress_restricted_content .= '<a href="'.$product_checkout_page_url.'" class="metapress-checkout-button">'.$product_name;
                            if( ! empty($product_price) ) {
                                $metapress_restricted_content .= ' <span class="metapress-inline-price">($'.$product_price.')</span>';
                            }
                            $metapress_restricted_content .= '</a>';
                        }
                    }

                    $metapress_restricted_content .= '</div><div class="metapress-notice-box"></div></div>';
                }
            }

            if( $user_has_access ) {
                if( ! empty($access_token) ) {
                    $content .= '<div class="metapress-verification" data-address="'.esc_attr($access_token->payment_owner).'" data-token="'.esc_attr($access_token->token).'"></div>';
                }
                return $content;
            } else {
                return wp_kses_post($metapress_restricted_content);
            }
        }

		public function metapress_user_transactions_shortcode( $atts ) {
			global $wp_metapress_textdomain;
            $metapress_payment_options = new METAPRESS_PAYMENT_OPTIONS_GEN();
			$metapress_restricted_content = '<div class="metapress-login-notice metapress-align-content-center"><p><small>*'.__('Accessing this content requires a', $wp_metapress_textdomain).' <a href="https://metamask.io/" target="_blank">MetaMask</a> '.__('account', $wp_metapress_textdomain).', or a browser wallet</small>.</p></div>';

            $metapress_restricted_content .= $metapress_payment_options->generate_wallet_options_html();

            $metapress_restricted_content .= '<div class="metapress-transaction metapress-border-box metapress-header"><div class="metapress-grid metapress-payment"><div class="metapress-setting-title">'.__('Product', $wp_metapress_textdomain).'</div><div class="metapress-setting-title">'.__('Paid With', $wp_metapress_textdomain).'</div><div class="metapress-setting-title">'.__('Amount', $wp_metapress_textdomain).'</div><div class="metapress-setting-title">'.__('Network', $wp_metapress_textdomain).'</div>';
            $metapress_restricted_content .= '<div class="metapress-setting-title">'.__('Date', $wp_metapress_textdomain).'</div><div class="metapress-setting-title">'.__('Status', $wp_metapress_textdomain).'</div><div class="metapress-setting-title">'.__('View', $wp_metapress_textdomain).'</div></div></div>';

    	    $metapress_restricted_content .= '<div id="metapress-user-transactions" class="border-box"></div>';

            $metapress_restricted_content .= '<div class="metapress-grid metapress-prev-next-buttons"><div class="metapress-nav-button prev"><div class="metapress-button">'.__('Previous', $wp_metapress_textdomain).'</div></div><div class="metapress-nav-button next"><div class="metapress-button">'.__('Next', $wp_metapress_textdomain).'</div></div></div>';

            // NEEDS TO BE FIXED - REMOVING CONTENT
			return wp_kses_post($metapress_restricted_content);
		}

        public function metapress_user_subscriptions_shortcode( $atts ) {
			global $wp_metapress_textdomain;
            $metapress_payment_options = new METAPRESS_PAYMENT_OPTIONS_GEN();
			$metapress_restricted_content = '<div class="metapress-login-notice metapress-align-content-center"><p><small>*'.__('Accessing this content requires a', $wp_metapress_textdomain).' <a href="https://metamask.io/" target="_blank">MetaMask</a> '.__('account', $wp_metapress_textdomain).', or a browser wallet</small>.</p></div>';

            $metapress_restricted_content .= $metapress_payment_options->generate_wallet_options_html();

    	    $metapress_restricted_content .= '<div id="metapress-user-subscriptions" class="border-box"></div>';

            $metapress_restricted_content .= '<div class="metapress-grid metapress-prev-next-buttons"><div class="metapress-nav-button prev"><div class="metapress-button">'.__('Previous', $wp_metapress_textdomain).'</div></div><div class="metapress-nav-button next"><div class="metapress-button">'.__('Next', $wp_metapress_textdomain).'</div></div></div>';

            // NEEDS TO BE FIXED - REMOVING CONTENT
			return wp_kses_post($metapress_restricted_content);
		}

        public function metapress_restrict_entire_site_access() {
            global $post;
            $allowed_to_view = false;
            $metapress_redirect_link = null;
            if( ! empty($post) ) {
                $metapress_allow_page_access = get_post_meta($post->ID, 'metapress_allow_page_access', true);
                $metapress_required_products = get_post_meta($post->ID, 'metapress_required_products', true);

                // SETTING A PAGE TO REQUIRE SPECIFIC Web3 Products OVERRIDES GLOBAL REQUIRED PRODUCTS
                if( $metapress_allow_page_access || ! empty($metapress_required_products) ) {
                    $allowed_to_view = true;
                }

                if( is_user_logged_in() && current_user_can('manage_options') ) {
                    $allowed_to_view = true;
                }

                if( ! $allowed_to_view ) {
                    $allowed_pages = array();
                    $frontpage_id = get_option( 'page_on_front' );
                    $metapress_checkout_page = get_option('metapress_checkout_page');
              	    $metapress_transactions_page = get_option('metapress_transactions_page');
                    $metapress_subscriptions_page = get_option('metapress_subscriptions_page');

                    $metapress_redirect_type = get_option('metapress_redirect_type');
                    $metapress_redirect_page_id = get_option('metapress_redirect_page_id');

                    if( ! empty($metapress_checkout_page) ) {
                        $allowed_pages[] = $metapress_checkout_page;
                    }

                    if( ! empty($metapress_transactions_page) ) {
                        $allowed_pages[] = $metapress_transactions_page;
                    }

                    if( ! empty($metapress_subscriptions_page) ) {
                        $allowed_pages[] = $metapress_subscriptions_page;
                    }

                    if( ! empty($metapress_redirect_page_id) ) {
                        $allowed_pages[] = $metapress_redirect_page_id;
                    }

                    if( is_page($allowed_pages) ) {
                         $allowed_to_view = true;
                    }

                }

                if( ! $allowed_to_view ) {

                    // SAME PAGE NO REDIRECT ACCESS OPTIONS DISPLAYED BY CONTENT FILTER
                    if( ! empty($metapress_redirect_type) ) {

                        if( $metapress_redirect_type == 'page' ) {
                            $metapress_redirect_link = get_permalink($metapress_redirect_page_id);
                        }
                        if( $metapress_redirect_type == 'custom' ) {
                            $metapress_redirect_link = get_option('metapress_redirect_custom_url');
                        }
                    }

                    if( ! empty($metapress_redirect_link) ) {
                        wp_redirect($metapress_redirect_link);
                        exit;
                    }
                }
            }
        }
}
$metaPress_plugin_content_filter_shortcodes = new MetaPress_Plugin_Content_Filter_Shortcodes();
