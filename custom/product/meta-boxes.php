<?php

class MetaPress_Custom_Post_Meta_Box_Manager {

    public function __construct() {
        add_action( 'add_meta_boxes', array($this, 'create_product_meta_boxes'));
        add_action( 'save_post', array($this, 'save_product_meta_data'));
    }

    public function create_product_meta_boxes() {
        global $post;
        $metapress_supported_post_types = get_option('metapress_supported_post_types');
        add_meta_box(
            'metapress_product_data',
            __( 'Product Information', 'rogue-themes' ),
            array($this, 'generate_product_meta_boxes'),
            'metapress_product',
            'normal',
            'high'
        );
        add_meta_box(
            'metapress_product_nft_data',
            __( 'Product Access via NFTs and owned tokens', 'rogue-themes' ),
            array($this, 'generate_product_nft_meta_boxes'),
            'metapress_product',
            'normal',
            'high'
        );
        if( ! empty($metapress_supported_post_types) ) {
          add_meta_box(
              'metapress_restrict_post_settings',
              __( 'Web3 Access Restrictions', 'rogue-themes' ),
              array($this, 'generate_post_type_metapress_product_settings'),
              $metapress_supported_post_types,
              'normal',
              'high'
          );
        }
    }

    public function generate_product_meta_boxes( $post ) {
        wp_nonce_field( 'metapress_product_meta_data_save', 'metapress_product_meta_data_save_nonce' );
        $product_price = get_post_meta( $post->ID, 'product_price', true );
        $product_is_subscription = get_post_meta( $post->ID, 'product_is_subscription', true );
        $subscription_interval_count = get_post_meta( $post->ID, 'subscription_interval_count', true );
        $subscription_interval = get_post_meta( $post->ID, 'subscription_interval', true );

        if( empty($product_price) ) {
            $product_price = "";
        }
        if( empty($product_is_subscription) ) {
            $product_is_subscription = 0;
        }
        if( empty($subscription_interval_count) ) {
            $subscription_interval_count = 1;
        }
        if( empty($subscription_interval) ) {
            $subscription_interval = 'days';
        }
        ?>
        <div class="rogue-meta-box">
            <label><?php _e('Product ID', 'rogue-themes'); ?>
            <input type="text" name="metapress_product_id" value="<?php echo esc_attr($post->ID); ?>" readonly />
            </label>
        </div>
        <div class="rogue-meta-box">
            <label><?php _e('Product Price', 'rogue-themes'); ?>
            <input type="text" name="metapress_product_price" value="<?php echo esc_attr($product_price); ?>" placeholder="5.95" />
            <span class="description">All prices should be in USD.</span>
            </label>
        </div>
        <div class="rogue-meta-box">
            <label><?php _e('Is Subscription', 'rogue-themes'); ?>
            <input type="checkbox" name="metapress_product_is_subscription" value="1" <?php checked(1, $product_is_subscription); ?> />
            <span class="description">Check this box if access to this product needs to be renewed after a certain time period.</span>
            </label>
        </div>
        <div class="rogue-meta-box">
            <label><?php _e('Subscription Needs Renewal Every', 'rogue-themes'); ?>
            <input type="number" name="metapress_product_subscription_interval_count" min="1" value="<?php echo esc_attr($subscription_interval_count); ?>" />
            <select name="metapress_product_subscription_interval">
                <option value="days" <?php selected('days', $subscription_interval); ?>><?php _e('Days', 'rogue-themes'); ?></option>
                <option value="weeks" <?php selected('weeks', $subscription_interval); ?>><?php _e('Weeks', 'rogue-themes'); ?></option>
                <option value="months" <?php selected('months', $subscription_interval); ?>><?php _e('Months', 'rogue-themes'); ?></option>
                <option value="years" <?php selected('years', $subscription_interval); ?>><?php _e('Years', 'rogue-themes'); ?></option>
            </select>
            </label>
        </div>
    <?php }

    public function generate_product_nft_meta_boxes( $post ) {
        global $wp_metapress_textdomain;
        $metapress_nft_contract_list = get_option('metapress_nft_contract_list', array());
        $product_nft_access_list = get_post_meta( $post->ID, 'product_nft_access_list', true );

        if( empty($product_nft_access_list) ) {
            $product_nft_access_list = array();
        }

        ?>
        <div class="rogue-meta-box">
            <p class="description"><?php _e('Visitors who any of the following NFTs or tokens will have access to this product along with pages and content that require it', $wp_metapress_textdomain); ?>.</p>
            <p class="description"><?php _e('Ownership verification for the assets below is determined by which type of token they are. (ERC-20, ERC-721, ERC-1155)', $wp_metapress_textdomain); ?></p>
        </div>


        <?php if( ! empty($metapress_nft_contract_list) ) {
            foreach($metapress_nft_contract_list as $nft_contract) {

                if( $nft_contract['token_type'] == 'erc20' ) {
                    $nft_contract_add_text = __('Add ERC-20 Token', $wp_metapress_textdomain);
                    $nft_contract_add_description = '<strong>ERC-20</strong>: '.__('Visitors that verify they have a balance greater than 0 of this token will have access to this product.', $wp_metapress_textdomain);
                }
                if( $nft_contract['token_type'] == 'erc721' ) {
                    $nft_contract_add_text = __('Add Single NFT or Collection', $wp_metapress_textdomain);
                    $nft_contract_add_description = '<strong>ERC-721</strong>: '.__('Visitors that verify they own this specific NFT will have access to this product. Leave the Token ID field blank to allow ownership verification for ANY asset in this Collection.', $wp_metapress_textdomain);
                }
                if( $nft_contract['token_type'] == 'erc1155' ) {
                    $nft_contract_add_text = __('Add ERC-1155 Token', $wp_metapress_textdomain);
                    $nft_contract_add_description = '<strong>ERC-1155</strong>: '.__('Visitors that verify they have a balance greater than 0 of this token will have access to this product. Note that ERC-1155 assets share Token IDs and each one has a balance.', $wp_metapress_textdomain);
                }

                if( $nft_contract['token_type'] == 'slp' ) {
                    $nft_contract_add_text = __('Add SLP Token', $wp_metapress_textdomain);
                    $nft_contract_add_description = '<strong>ERC-1155</strong>: '.__('Visitors that verify they have a balance greater than 0 of this token will have access to this product.', $wp_metapress_textdomain);
                }
            ?>
                <div class="rogue-meta-box metapress-nft-add-box">
                    <label><?php echo esc_attr($nft_contract['name']); ?></label>
                    <div class="button add-new-nft" data-contract="<?php echo esc_attr($nft_contract['contract_address']); ?>"  data-token-type="<?php echo esc_attr($nft_contract['token_type']); ?>" data-name="<?php echo esc_attr($nft_contract['name']); ?>" data-network="<?php echo esc_attr($nft_contract['network']); ?>"
                        data-networkname="<?php echo esc_attr($nft_contract['networkname']); ?>" data-chainid="<?php echo esc_attr($nft_contract['chainid']); ?>">
                    <?php echo wp_kses($nft_contract_add_text, 'post'); ?>
                    </div>
                    <div class="metapress-asset-tooltip">
                        <span class="dashicons dashicons-editor-help metapress-tooltip"></span>
                        <div class="metapress-asset-tooltip-content">
                            <?php echo wp_kses($nft_contract_add_description, 'post'); ?>
                        </div>
                    </div>
                </div>
            <?php }
        } else { ?>
            <div class="rogue-meta-box">
                <p><?php _e('You have not added any NFT contract addresses', $wp_metapress_textdomain); ?>. <a class="button" href="<?php echo admin_url('admin.php?page=metapress-nft-contracts'); ?>"><?php _e('Add NFT Contract', $wp_metapress_textdomain); ?></a></p>
            </div>
        <?php } ?>
        <div id="metapress-nft-token-list">

        <?php if( ! empty($product_nft_access_list) ) {
            $product_nft_access_list = array_values($product_nft_access_list);
            foreach($product_nft_access_list as $t_key => $nft_token) {
                if( ! isset($nft_token['collection_slug']) ) {
                    $nft_token['collection_slug'] = "";
                }
                if( ! isset($nft_token['minimum_balance']) ) {
                    $nft_token['minimum_balance'] = 1;
                } ?>
                <div class="rogue-meta-box metapress-nft-token">
                    <div class="metapress-nft-info-box">
                        <label><?php _e('Contract Name', $wp_metapress_textdomain); ?></label>
                        <input type="text" name="metapress_nft_access_list[<?php echo esc_attr($t_key); ?>][name]" value="<?php echo esc_attr($nft_token['name']); ?>" readonly />
                    </div>
                    <div class="metapress-nft-info-box">
                        <label><?php _e('Contract Address', $wp_metapress_textdomain); ?></label>
                        <input type="text" name="metapress_nft_access_list[<?php echo esc_attr($t_key); ?>][contract_address]" value="<?php echo esc_attr($nft_token['contract_address']); ?>" readonly />
                    </div>
                    <div class="metapress-nft-info-box">
                        <label><?php _e('Token ID', $wp_metapress_textdomain); ?></label>
                        <input type="text" name="metapress_nft_access_list[<?php echo esc_attr($t_key); ?>][token_id]" value="<?php echo esc_attr($nft_token['token_id']); ?>" />
                        <div class="metapress-asset-tooltip">
                            <span class="dashicons dashicons-editor-help metapress-tooltip"></span>
                            <div class="metapress-asset-tooltip-content">
                                <?php _e('Leave this blank for ERC-20 tokens or to allow ownership verification for ANY asset within an ERC-721 Collection. (Required for ERC-1155)', $wp_metapress_textdomain) ?>
                            </div>
                        </div>
                    </div>

                    <div class="metapress-nft-info-box">
                        <label><?php _e('Minimum Balance', $wp_metapress_textdomain); ?></label>
                        <input type="number" min="1" name="metapress_nft_access_list[<?php echo esc_attr($t_key); ?>][minimum_balance]" value="<?php echo esc_attr($nft_token['minimum_balance']); ?>" />
                        <div class="metapress-asset-tooltip">
                            <span class="dashicons dashicons-editor-help metapress-tooltip"></span>
                            <div class="metapress-asset-tooltip-content">
                                <?php _e('Minimum number of tokens required for access to be valid. For example, require the wallet to own at least 2 NFTs OR have a minimum of 5000 coins within a contract.', $wp_metapress_textdomain) ?>
                            </div>
                        </div>
                    </div>

                    <div class="metapress-nft-info-box">
                        <label><?php _e('Token Name', $wp_metapress_textdomain); ?></label>
                        <input type="text" name="metapress_nft_access_list[<?php echo esc_attr($t_key); ?>][token_name]" value="<?php echo esc_attr($nft_token['token_name']); ?>" />
                    </div>
                    <div class="metapress-nft-info-box">
                        <label><?php _e('Token Image URL', $wp_metapress_textdomain); ?></label>
                        <input type="url" class="icon-image-url" name="metapress_nft_access_list[<?php echo esc_attr($t_key); ?>][token_image]" value="<?php echo esc_attr($nft_token['token_image']); ?>" />
                        <span class="upload-icon-image button"><?php _e('Choose Image', $wp_metapress_textdomain); ?></span>
                    </div>
                    <div class="metapress-nft-info-box">
                        <label><?php _e('Purchase URL', $wp_metapress_textdomain); ?></label>
                        <input type="url" name="metapress_nft_access_list[<?php echo esc_attr($t_key); ?>][token_url]" value="<?php echo esc_attr($nft_token['token_url']); ?>" />
                        <div class="metapress-asset-tooltip">
                            <span class="dashicons dashicons-editor-help metapress-tooltip"></span>
                            <div class="metapress-asset-tooltip-content">
                                <?php _e('Where can visitors purchase or view the token(s).', $wp_metapress_textdomain); ?>
                            </div>
                        </div>
                    </div>
                    <div class="metapress-nft-info-box">
                        <label><?php _e('OpenSea Collection Slug', $wp_metapress_textdomain); ?></label>
                        <input type="text" name="metapress_nft_access_list[<?php echo esc_attr($t_key); ?>][collection_slug]" value="<?php echo esc_attr($nft_token['collection_slug']); ?>" />
                        <div class="metapress-asset-tooltip">
                            <span class="dashicons dashicons-editor-help metapress-tooltip"></span>
                            <div class="metapress-asset-tooltip-content">
                                <?php _e('This is only required if you are using a shared OpenSea contract address. Do not fill this in if you do not have an OpenSea API key.', $wp_metapress_textdomain); ?>
                                <strong><?php _e('Case sensitive and must match the collection slug exactly.', $wp_metapress_textdomain); ?></strong>
                            </div>
                        </div>
                    </div>
                    <div class="metapress-nft-info-box">
                        <label><?php _e('Delete', $wp_metapress_textdomain); ?></label>
                        <label class="remove-nft-asset button"><?php _e('Delete Token', $wp_metapress_textdomain); ?></label>
                    </div>
                    <input type="hidden" name="metapress_nft_access_list[<?php echo esc_attr($t_key); ?>][token_type]" value="<?php echo esc_attr($nft_token['token_type']); ?>" readonly/>
                    <input type="hidden" name="metapress_nft_access_list[<?php echo esc_attr($t_key); ?>][network]" value="<?php echo esc_attr($nft_token['network']); ?>" readonly/>
                    <input type="hidden" name="metapress_nft_access_list[<?php echo esc_attr($t_key); ?>][networkname]" value="<?php echo esc_attr($nft_token['networkname']); ?>" readonly/>
                    <input type="hidden" name="metapress_nft_access_list[<?php echo esc_attr($t_key); ?>][chainid]" value="<?php echo esc_attr($nft_token['chainid']); ?>" readonly/>

                </div>
            <?php }
        } else { ?>
            <div id="metapress-no-nfts-added" class="rogue-meta-box">
                <p class="description"><?php _e('You have not added any NFT assets.', $wp_metapress_textdomain); ?>.</p>
            </div>
    <?php } ?>
        </div>
    <?php }

    public function save_product_meta_data( $post_id ) {
        if(metapress_save_custom_metabox_data( $post_id, 'metapress_product_meta_data_save_nonce', 'metapress_product_meta_data_save' )) {
            if( isset($_POST['metapress_product_price']) ) {
                $product_price = sanitize_text_field($_POST['metapress_product_price']);
                $product_price_int = 0;
                if( ! empty($product_price) ) {
                    $price_formats = $this->sanitize_price($product_price);
                    $product_price_int = $price_formats->int_price;
                    update_post_meta( $post_id, 'product_price', $price_formats->formatted_price);
                } else {
                    update_post_meta( $post_id, 'product_price', null);
                }
                update_post_meta( $post_id, 'product_price_amount', intval($product_price_int));
            }

            if( isset($_POST['metapress_product_is_subscription']) ) {
                update_post_meta( $post_id, 'product_is_subscription', 1);
            } else {
                update_post_meta( $post_id, 'product_is_subscription', 0);
            }

            if( isset($_POST['metapress_product_subscription_interval_count']) ) {
                $subscription_interval_count = intval($_POST['metapress_product_subscription_interval_count']);
                if( empty($subscription_interval_count) ) {
                    $subscription_interval_count = 1;
                }
                update_post_meta( $post_id, 'subscription_interval_count', intval($subscription_interval_count));
            }

            if( isset($_POST['metapress_product_subscription_interval']) ) {
                $subscription_interval = sanitize_text_field($_POST['metapress_product_subscription_interval']);
                update_post_meta( $post_id, 'subscription_interval', $subscription_interval);
            }


            if( isset($_POST['metapress_nft_access_list']) && ! empty($_POST['metapress_nft_access_list']) && is_array($_POST['metapress_nft_access_list']) ) {
                $metapress_nft_access_list = $this->sanitize_nft_array($_POST['metapress_nft_access_list']);
                update_post_meta( $post_id, 'product_nft_access_list', $metapress_nft_access_list);
            } else {
                update_post_meta( $post_id, 'product_nft_access_list', array());
            }

        }

        if(metapress_save_custom_metabox_data( $post_id, 'metapress_restrictions_meta_data_save_nonce', 'metapress_restrictions_meta_data_save' )) {
            if( isset($_POST['metapress_required_products']) ) {
                $metapress_required_products = $this->sanitize_int_array($_POST['metapress_required_products']);
                update_post_meta( $post_id, 'metapress_required_products', $metapress_required_products);
            } else {
                update_post_meta( $post_id, 'metapress_required_products', array());
            }

            if( isset($_POST['metapress_allow_page_access']) ) {
                update_post_meta( $post_id, 'metapress_allow_page_access', 1);
            } else {
                update_post_meta( $post_id, 'metapress_allow_page_access', 0);
            }
        }
    }

    public function generate_post_type_metapress_product_settings( $post ) {
        global $wp_metapress_textdomain;
        wp_nonce_field( 'metapress_restrictions_meta_data_save', 'metapress_restrictions_meta_data_save_nonce' );
        $metapress_restrict_site_access = get_option('metapress_restrict_site_access', 0);
        $metapress_required_products = get_post_meta( $post->ID, 'metapress_required_products', true );
        $metapress_allow_page_access = get_post_meta($post->ID, 'metapress_allow_page_access', true);
        if( empty($metapress_required_products) ) {
          $metapress_required_products = array();
        }
        if( empty($metapress_allow_page_access) ) {
            $metapress_allow_page_access = 0;
        }
		$mp_product_args = array(
			'post_type' => 'metapress_product',
			'posts_per_page' => -1,
			'post_status' => 'publish'
		);
		$mp_products = get_posts($mp_product_args);
        ?>
        <div class="metapress-admin-section metapress-border-box">
            <h3><?php _e('Required Web3 Access Products', $wp_metapress_textdomain); ?></h3>
            <?php if($metapress_restrict_site_access) { ?>
                <p class="metapress-admin-notice"><?php echo sprintf(__('<strong>Restrict Website Access is enabled</strong>. Selecting any Web3 Products below will override your global <a href="%s">Access Settings</a> required Products.', $wp_metapress_textdomain), admin_url('admin.php?page=metapress-access-settings')); ?></p>
            <?php } else { ?>
                <p class="metapress-admin-notice"><?php echo sprintf(__('<strong><a href="%s">Restrict Website Access</a> is disabled</strong>. Restrict this entire page by selecting Web3 Products below.', $wp_metapress_textdomain), admin_url('admin.php?page=metapress-access-settings')); ?></p>
            <?php } ?>
          <div class="metapress-admin-settings metapress-border-box metapress-width-600">
            <div class="metapress-grid metapress-setting">
              <div class="metapress-setting-title">
                <?php _e('Required Web3 Products', $wp_metapress_textdomain); ?>
              </div>
              <div class="metapress-setting-content metapress-align-right">
                  <?php if( ! empty($mp_products) ) {
                        foreach($mp_products as $product) { ?>
                        <div class="rogue-meta-box">
                            <label><?php echo esc_attr($product->post_title); ?>
                              <input type="checkbox" name="metapress_required_products[]" value="<?php echo esc_attr($product->ID); ?>" <?php checked(in_array($product->ID, $metapress_required_products)); ?> />
                            </label>
                        </div>
                        <?php }
                  } else { ?>
                      <p><?php _e('You have not created any Web3 Products.', $wp_metapress_textdomain); ?></p>
                  <?php } ?>
              </div>
            </div>
          </div>
        </div>

        <div class="metapress-admin-section metapress-border-box">
            <h3><?php _e('Site Lock Settings', $wp_metapress_textdomain); ?></h3>
            <?php if($metapress_restrict_site_access) { ?>
                <p class="metapress-admin-notice"><?php _e('<strong>Restrict Website Access is enabled</strong>. Optionally allow access for all visitors to this specific page.', $wp_metapress_textdomain); ?></p>
            <?php } else { ?>
                <p class="metapress-admin-notice"><?php echo sprintf(__('<strong><a href="%s">Restrict Website Access</a> is disabled</strong>. This setting is not applicable.', $wp_metapress_textdomain), admin_url('admin.php?page=metapress-access-settings')); ?></p>
            <?php } ?>
          <div class="metapress-admin-settings metapress-border-box metapress-width-600">
            <div class="metapress-grid metapress-setting">
              <div class="metapress-setting-title">
                <?php _e('Allow Page Access', $wp_metapress_textdomain); ?>
              </div>
              <div class="metapress-setting-content metapress-align-right">
                  <input type="checkbox" name="metapress_allow_page_access" value="1" <?php checked(1, $metapress_allow_page_access); ?> />
              </div>
            </div>
          </div>
        </div>
      <?php }

      protected function sanitize_nft_array($array_data) {
          if( ! empty($array_data) && is_array($array_data) ) {
              foreach($array_data as $key => &$nft_data) {
                  $nft_data['name'] = sanitize_text_field($nft_data['name']);
                  $nft_data['contract_address'] = sanitize_text_field($nft_data['contract_address']);
                  $nft_data['token_id'] = sanitize_text_field($nft_data['token_id']);
                  $nft_data['token_name'] = sanitize_text_field($nft_data['token_name']);
                  $nft_data['token_image'] = sanitize_url($nft_data['token_image']);
                  $nft_data['token_url'] = sanitize_url($nft_data['token_url']);
                  $nft_data['collection_slug'] = sanitize_text_field($nft_data['collection_slug']);
                  $nft_data['token_type'] = sanitize_text_field($nft_data['token_type']);
                  $nft_data['network'] = sanitize_text_field($nft_data['network']);
                  $nft_data['networkname'] = sanitize_text_field($nft_data['networkname']);
                  $nft_data['chainid'] = sanitize_key($nft_data['chainid']);
              }
          } else {
              $array_data = array();
          }
          return $array_data;
      }

      protected function sanitize_int_array($array_data) {
          if( ! empty($array_data) && is_array($array_data) ) {
              foreach($array_data as $key => &$int_value) {
                  $int_value = intval($int_value);
              }
          } else {
              $array_data = array();
          }
          return $array_data;
      }

      private function sanitize_price($product_price) {
          if(strpos($product_price, ',')) {
              $product_price = str_replace(",", "", $product_price);
          }
          $product_price = number_format($product_price, 2, '.', '');
          if(strpos($product_price, '.')) {
              $product_price_int = str_replace(".", "", $product_price);
          } else {
              $product_price_int = intval($product_price) * 100;
          }
          return (object) array('formatted_price' => $product_price, 'int_price' => $product_price_int);
      }


}
$metapress_custom_post_meta_box_manager = new MetaPress_Custom_Post_Meta_Box_Manager();
