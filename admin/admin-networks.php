<div class="wrap">
    <?php include('metapress-admin-header.php'); ?>
    <div class="metapress-plugin-settings">
        <form method="post" action="options.php">
            <?php
                global $wp_metapress_textdomain;
              settings_fields( 'metapress-plugin-networks' );
              $metapress_supported_networks = apply_filters('filter_web3_access_networks', get_option('metapress_supported_networks'), 'add');
              $metapress_supported_test_networks = apply_filters('filter_web3_access_test_networks', get_option('metapress_supported_test_networks'), 'add');
              $metapress_wallet_address = get_option('metapress_ethereum_wallet_address');
              $metapress_solana_wallet_address = get_option('metapress_solana_wallet_address');
              $metapress_live_mode = get_option('metapress_live_mode', 0);
              $metapress_admin_page_title = __('Supported Networks', $wp_metapress_textdomain);
              $metapress_admin_page_description = __('Enable or Disable Networks that you would like to support on your site', $wp_metapress_textdomain);
              if( ! empty($metapress_supported_networks) ) {
                  $metapress_supported_networks = array_values($metapress_supported_networks);
              }
              if( ! empty($metapress_supported_test_networks) ) {
                  $metapress_supported_test_networks = array_values($metapress_supported_test_networks);
              }
              if( ! $metapress_live_mode ) {
                  $metapress_admin_page_title = __('Supported TEST Networks', $wp_metapress_textdomain);
                  $metapress_admin_page_description = __('(TEST MODE) Enable or Disable TEST Networks that you would like to support on your site.', $wp_metapress_textdomain);
              }

              ?>
              <div class="metapress-admin-section metapress-border-box">
                <h1><?php echo esc_attr($metapress_admin_page_title); ?></h1>
                <p class="metapress-admin-notice"><?php echo esc_attr($metapress_admin_page_description); ?>.</p>
                <div id="live-metapress-networks" class="metapress-wallet-section metapress-border-box">
                    <?php if( ! empty($metapress_supported_networks) ) {
                        foreach($metapress_supported_networks as $key => $network) {
                            if( empty($network['enabled']) ) {
                                $network['enabled'] = 0;
                            }
                            if( empty($network['receiving_address']) ) {
                                if( $network['slug'] == 'solana' ) {
                                    if( ! empty($metapress_solana_wallet_address) ) {
                                        $network['receiving_address'] = $metapress_solana_wallet_address;
                                    }
                                } else {
                                    if( ! empty($metapress_wallet_address) ) {
                                        $network['receiving_address'] = $metapress_wallet_address;
                                    }
                                }
                            }
                            ?>
                        <div class="metapress-admin-settings metapress-border-box live-network
                        <?php if(! $metapress_live_mode) {
                            echo 'hidden';
                        } ?>
                        ">
                            <div class="metapress-grid metapress-wallet metapress-setting">
                              <div class="metapress-setting-title">
                                <?php _e('Network Name', $wp_metapress_textdomain); ?>
                              </div>
                              <div class="metapress-setting-content">
                                <input class="regular-text" name="metapress_supported_networks[<?php echo esc_attr( $key ); ?>][name]" type="text" value="<?php echo esc_attr( $network['name'] ); ?>" required
                                <?php if($key < 6) {
                                    echo 'readonly';
                                } ?>
                                />
                              </div>
                            </div>
                             <div class="metapress-grid metapress-wallet metapress-setting">
                               <div class="metapress-setting-title">
                                 <?php _e('Slug', $wp_metapress_textdomain); ?>
                               </div>
                               <div class="metapress-setting-content">
                                 <input class="regular-text" name="metapress_supported_networks[<?php echo esc_attr( $key ); ?>][slug]" type="text" value="<?php echo esc_attr( $network['slug'] ); ?>" required
                                 <?php if($key < 6) {
                                     echo 'readonly';
                                 } ?>
                                 />
                               </div>
                             </div>
                             <div class="metapress-grid metapress-wallet metapress-setting">
                               <div class="metapress-setting-title">
                                 <?php _e('Chain ID', $wp_metapress_textdomain); ?>
                               </div>
                               <div class="metapress-setting-content">
                                 <input class="regular-text" name="metapress_supported_networks[<?php echo esc_attr( $key ); ?>][chainid]" type="text" value="<?php echo esc_attr( $network['chainid'] ); ?>" required
                                 <?php if($key < 6) {
                                     echo 'readonly';
                                 } ?>
                                 />
                               </div>
                             </div>
                             <div class="metapress-grid metapress-wallet metapress-setting">
                               <div class="metapress-setting-title">
                                 <?php _e('Symbol', $wp_metapress_textdomain); ?>
                               </div>
                               <div class="metapress-setting-content">
                                 <input class="regular-text" name="metapress_supported_networks[<?php echo esc_attr( $key ); ?>][symbol]" type="text" value="<?php echo esc_attr( $network['symbol'] ); ?>" required
                                 <?php if($key < 6) {
                                     echo 'readonly';
                                 } ?>
                                 />
                               </div>
                             </div>
                             <div class="metapress-grid metapress-wallet metapress-setting">
                               <div class="metapress-setting-title">
                                 <?php _e('Receiving Wallet Address', $wp_metapress_textdomain); ?>
                               </div>
                               <div class="metapress-setting-content">
                                 <input class="regular-text" name="metapress_supported_networks[<?php echo esc_attr( $key ); ?>][receiving_address]" type="text" value="<?php echo esc_attr( $network['receiving_address'] ); ?>" />
                               </div>
                             </div>
                             <div class="metapress-grid metapress-wallet metapress-setting">
                               <div class="metapress-setting-title">
                                 <?php _e('Explorer URL', $wp_metapress_textdomain); ?>
                               </div>
                               <div class="metapress-setting-content">
                                 <input class="regular-text" name="metapress_supported_networks[<?php echo esc_attr( $key ); ?>][explorer]" type="text" value="<?php echo esc_attr( $network['explorer'] ); ?>" required
                                 <?php if($key < 6) {
                                     echo 'readonly';
                                 } ?>
                                 />
                               </div>
                             </div>

                             <div class="metapress-grid metapress-wallet metapress-setting">
                               <div class="metapress-setting-title">
                                 <?php _e('Icon', $wp_metapress_textdomain); ?>
                               </div>
                               <div class="metapress-setting-content">
                                 <input class="regular-text icon-image-url" name="metapress_supported_networks[<?php echo esc_attr( $key ); ?>][icon]" type="text" value="<?php echo esc_attr( $network['icon'] ); ?>" required
                                 <?php if($key < 6) {
                                     echo 'readonly';
                                 } ?>
                                 />
                                 <?php if($key > 6) { ?>
                                     <label class="upload-icon-image button"><?php _e('Upload', $wp_metapress_textdomain); ?></label>
                                     <p><?php _e('Recommended square image (250px by 250px)', $wp_metapress_textdomain); ?></p>
                                 <?php } ?>
                               </div>
                             </div>

                             <div class="metapress-grid metapress-wallet metapress-setting">
                               <div class="metapress-setting-title">
                                 <?php _e('Enabled', $wp_metapress_textdomain); ?>
                               </div>
                               <div class="metapress-setting-content">
                                   <input name="metapress_supported_networks[<?php echo esc_attr( $key ); ?>][enabled]" type="checkbox" value="1" <?php checked(1, $network['enabled']); ?> />
                                   <span><?php _e('Enable or Disable this Network', $wp_metapress_textdomain); ?></span>
                               </div>
                             </div>
                             <?php if($key > 6) { ?>
                                 <div class="metapress-grid metapress-wallet metapress-setting">
                                   <div class="metapress-setting-title">
                                     <?php _e('Remove', $wp_metapress_textdomain); ?>
                                   </div>
                                   <div class="metapress-setting-content">
                                     <label class="remove-custom-network button"><?php _e('Delete Network', $wp_metapress_textdomain); ?></label>
                                   </div>
                                 </div>
                             <?php } ?>
                        </div>
                     <?php } } if( ! empty($metapress_supported_test_networks) ) {
                         foreach($metapress_supported_test_networks as $key => $test_network) {
                             if( empty($test_network['enabled']) ) {
                                 $test_network['enabled'] = 0;
                             }
                             if( empty($test_network['receiving_address']) ) {
                                 if( $test_network['slug'] == 'solanadevnet' ) {
                                     if( ! empty($metapress_solana_wallet_address) ) {
                                         $test_network['receiving_address'] = $metapress_solana_wallet_address;
                                     }
                                 } else {
                                     if( ! empty($metapress_wallet_address) ) {
                                        $test_network['receiving_address'] = $metapress_wallet_address;
                                     }
                                 }
                             }
                             ?>
                         <div class="metapress-admin-settings metapress-border-box test-network <?php if($metapress_live_mode) { echo 'hidden'; } ?>">
                             <div class="metapress-grid metapress-wallet metapress-setting">
                               <div class="metapress-setting-title">
                                 <?php _e('Network Name', $wp_metapress_textdomain); ?>
                               </div>
                               <div class="metapress-setting-content">
                                 <input class="regular-text" name="metapress_supported_test_networks[<?php echo esc_attr( $key ); ?>][name]" type="text" value="<?php echo esc_attr( $test_network['name'] ); ?>" required
                                 <?php if($key < 6) {
                                     echo 'readonly';
                                 } ?>
                                 />
                               </div>
                             </div>
                              <div class="metapress-grid metapress-wallet metapress-setting">
                                <div class="metapress-setting-title">
                                  <?php _e('Slug', $wp_metapress_textdomain); ?>
                                </div>
                                <div class="metapress-setting-content">
                                  <input class="regular-text" name="metapress_supported_test_networks[<?php echo esc_attr( $key ); ?>][slug]" type="text" value="<?php echo esc_attr( $test_network['slug'] ); ?>" required
                                  <?php if($key < 6) {
                                      echo 'readonly';
                                  } ?>
                                  />
                                </div>
                              </div>
                              <div class="metapress-grid metapress-wallet metapress-setting">
                                <div class="metapress-setting-title">
                                  <?php _e('Chain ID', $wp_metapress_textdomain); ?>
                                </div>
                                <div class="metapress-setting-content">
                                  <input class="regular-text" name="metapress_supported_test_networks[<?php echo esc_attr( $key ); ?>][chainid]" type="text" value="<?php echo esc_attr( $test_network['chainid'] ); ?>" required
                                  <?php if($key < 6) {
                                      echo 'readonly';
                                  } ?>
                                  />
                                </div>
                              </div>
                              <div class="metapress-grid metapress-wallet metapress-setting">
                                <div class="metapress-setting-title">
                                  <?php _e('Symbol', $wp_metapress_textdomain); ?>
                                </div>
                                <div class="metapress-setting-content">
                                  <input class="regular-text" name="metapress_supported_test_networks[<?php echo esc_attr( $key ); ?>][symbol]" type="text" value="<?php echo esc_attr( $test_network['symbol'] ); ?>" required
                                  <?php if($key < 6) {
                                      echo 'readonly';
                                  } ?>
                                  />
                                </div>
                              </div>
                              <div class="metapress-grid metapress-wallet metapress-setting">
                                <div class="metapress-setting-title">
                                  <?php _e('Receiving Wallet Address', $wp_metapress_textdomain); ?>
                                </div>
                                <div class="metapress-setting-content">
                                  <input class="regular-text" name="metapress_supported_test_networks[<?php echo esc_attr( $key ); ?>][receiving_address]" type="text" value="<?php echo esc_attr( $test_network['receiving_address'] ); ?>" />
                                </div>
                              </div>
                              <div class="metapress-grid metapress-wallet metapress-setting">
                                <div class="metapress-setting-title">
                                  <?php _e('Explorer URL', $wp_metapress_textdomain); ?>
                                </div>
                                <div class="metapress-setting-content">
                                  <input class="regular-text" name="metapress_supported_test_networks[<?php echo esc_attr( $key ); ?>][explorer]" type="text" value="<?php echo esc_attr( $test_network['explorer'] ); ?>" required
                                  <?php if($key < 6) {
                                      echo 'readonly';
                                  } ?>
                                  />
                                </div>
                              </div>

                              <div class="metapress-grid metapress-wallet metapress-setting">
                                <div class="metapress-setting-title">
                                  <?php _e('Icon', $wp_metapress_textdomain); ?>
                                </div>
                                <div class="metapress-setting-content">
                                  <input class="regular-text icon-image-url" name="metapress_supported_test_networks[<?php echo esc_attr( $key ); ?>][icon]" type="text" value="<?php echo esc_attr( $test_network['icon'] ); ?>" required
                                  <?php if($key < 6) {
                                      echo 'readonly';
                                  } ?>
                                  />
                                  <?php if($key > 6) { ?>
                                      <label class="upload-icon-image button"><?php _e('Upload', $wp_metapress_textdomain); ?></label>
                                      <p><?php _e('Recommended square image (250px by 250px)', $wp_metapress_textdomain); ?></p>
                                  <?php } ?>
                                </div>
                              </div>

                              <div class="metapress-grid metapress-wallet metapress-setting">
                                <div class="metapress-setting-title">
                                  <?php _e('Enabled', $wp_metapress_textdomain); ?>
                                </div>
                                <div class="metapress-setting-content">
                                    <input name="metapress_supported_test_networks[<?php echo esc_attr( $key ); ?>][enabled]" type="checkbox" value="1" <?php checked(1, $test_network['enabled']); ?> />
                                    <span><?php _e('Enable or Disable this Network', $wp_metapress_textdomain); ?></span>
                                </div>
                              </div>
                              <?php if($key > 6) { ?>
                                  <div class="metapress-grid metapress-wallet metapress-setting">
                                    <div class="metapress-setting-title">
                                      <?php _e('Remove', $wp_metapress_textdomain); ?>
                                    </div>
                                    <div class="metapress-setting-content">
                                      <label class="remove-custom-network button"><?php _e('Delete Network', $wp_metapress_textdomain); ?></label>
                                    </div>
                                  </div>
                              <?php } ?>
                         </div>
                      <?php } } ?>
                </div>
            </div>
            <div class="metapress-admin-section metapress-border-box">
                <!--<div id="add-new-network" class="button"><?php _e('Add Network', $wp_metapress_textdomain); ?></div>-->
                <?php submit_button(); ?>
            </div>
        </form>
    </div>
</div>
