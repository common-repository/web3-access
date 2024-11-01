<div class="wrap">
    <?php include('metapress-admin-header.php'); ?>
    <div class="metapress-plugin-settings">
        <form method="post" action="options.php">
            <?php
              global $wp_metapress_textdomain;
              settings_fields( 'metapress-plugin-tokens' );
              $metapress_supported_networks = apply_filters('filter_web3_access_networks', get_option('metapress_supported_networks'), 'remove');
              $metapress_supported_test_networks = apply_filters('filter_web3_access_test_networks', get_option('metapress_supported_test_networks'), 'remove');
              $metapress_custom_tokens_list = get_option('metapress_custom_tokens_list', array());

              if( empty($metapress_custom_tokens_list) ) {
                  $metapress_custom_tokens_list = array();
              } else {
                  $metapress_custom_tokens_list = array_values($metapress_custom_tokens_list);
              }

              $metapress_mode_notice = __('Use this page to add custom tokens you would like to accept for payments', $wp_metapress_textdomain);

              ?>
              <div class="metapress-admin-section metapress-border-box">
                <h1><?php _e('Custom Tokens For Payments', $wp_metapress_textdomain); ?></h1>
                <p class="metapress-admin-notice"><?php echo esc_attr($metapress_mode_notice); ?>.</p>
                <ul class="metapress-notes">
                    <li><?php _e('Tokens must exist on either the Ethereum, Polygon, Binance Smart Chain, Avalanche or Fantom Network', $wp_metapress_textdomain); ?>.</li>
                    <li><?php _e('Tokens must use 18 decimals for values', $wp_metapress_textdomain); ?>. <a href="https://docs.openzeppelin.com/contracts/3.x/api/token/erc20#ERC20-decimals--" target="_blank"><?php _e('Read More Info', $wp_metapress_textdomain); ?></a></li>
                    <li><?php _e('If the Binance API or CoinGecko API cannot find a price for your coin, you will need to manually update your coins price', $wp_metapress_textdomain); ?>.</li>
                </ul>
                <div id="live-metapress-tokens" class="metapress-wallet-section metapress-border-box">
                    <?php if( ! empty($metapress_custom_tokens_list) ) {
                        foreach($metapress_custom_tokens_list as $key => $token) {
                            if( empty($token['enabled']) ) {
                                $token['enabled'] = 0;
                            }
                            if( empty($token['usd_price']) ) {
                                $token['usd_price'] = "";
                            }
                            if( empty($token['binance_price_api']) ) {
                                $token['binance_price_api'] = 0;
                            }
                            if( empty($token['coingecko_price_api']) ) {
                                $token['coingecko_price_api'] = 0;
                            }

                            ?>
                        <div class="metapress-admin-settings metapress-border-box live-token">
                            <div class="metapress-grid metapress-wallet metapress-setting">
                              <div class="metapress-setting-title">
                                <?php _e('Token Contract Address', $wp_metapress_textdomain); ?>
                              </div>
                              <div class="metapress-setting-content">
                                <input class="regular-text token-contract-address" name="metapress_custom_tokens_list[<?php echo esc_attr( $key ); ?>][contract_address]" type="text" value="<?php echo esc_attr( $token['contract_address'] ); ?>" required/>
                                <label class="fetch-coin-data button"><?php _e('Fetch Coin Info', $wp_metapress_textdomain); ?></label>
                              </div>
                            </div>
                            <div class="metapress-grid metapress-wallet metapress-setting">
                              <div class="metapress-setting-title">
                                <?php _e('TEST Token Contract Address', $wp_metapress_textdomain); ?>
                              </div>
                              <div class="metapress-setting-content">
                                <input class="regular-text" name="metapress_custom_tokens_list[<?php echo esc_attr( $key ); ?>][test_contract_address]" type="text" value="<?php echo esc_attr( $token['test_contract_address'] ); ?>"/>
                              </div>
                            </div>
                            <div class="metapress-grid metapress-wallet metapress-setting">
                              <div class="metapress-setting-title">
                                <?php _e('Currency Symbol', $wp_metapress_textdomain); ?>
                              </div>
                              <div class="metapress-setting-content">
                                <input class="regular-text token-contract-symbol" name="metapress_custom_tokens_list[<?php echo esc_attr( $key ); ?>][currency_symbol]" type="text" value="<?php echo esc_attr( $token['currency_symbol'] ); ?>" required/>
                              </div>
                            </div>
                            <div class="metapress-grid metapress-wallet metapress-setting">
                               <div class="metapress-setting-title">
                                 <?php _e('Name', $wp_metapress_textdomain); ?>
                               </div>
                               <div class="metapress-setting-content">
                                 <input class="regular-text token-contract-name" name="metapress_custom_tokens_list[<?php echo esc_attr( $key ); ?>][name]" type="text" value="<?php echo esc_attr( $token['name'] ); ?>" required/>
                               </div>
                            </div>

                            <div class="metapress-grid metapress-wallet metapress-setting">
                               <div class="metapress-setting-title">
                                 <?php _e('Network', $wp_metapress_textdomain); ?>
                               </div>
                               <div class="metapress-setting-content">
                                 <select class="selected-token-network" name="metapress_custom_tokens_list[<?php echo esc_attr( $key ); ?>][network]">
                                     <?php foreach($metapress_supported_networks as $network_option) {
                                         if( isset($network_option['enabled']) ) { ?>
                                         <option value="<?php echo esc_attr($network_option['slug']); ?>" <?php selected($token['network'], esc_attr($network_option['slug']) ); ?> data-chainid="<?php echo esc_attr($network_option['chainid']); ?>" data-explorer="<?php echo esc_attr($network_option['explorer']); ?>"><?php echo esc_attr($network_option['name']); ?></option>
                                     <?php } } ?>
                                 </select>
                                 <input class="regular-text set-network-chainid" name="metapress_custom_tokens_list[<?php echo esc_attr( $key ); ?>][chainid]" type="hidden" value="<?php echo esc_attr( $token['chainid'] ); ?>" required readonly />
                                 <input class="regular-text set-network-name" name="metapress_custom_tokens_list[<?php echo esc_attr( $key ); ?>][networkname]" type="hidden" value="<?php echo esc_attr( $token['networkname'] ); ?>" required readonly />
                                 <input class="regular-text set-network-explorer" name="metapress_custom_tokens_list[<?php echo esc_attr( $key ); ?>][explorer]" type="hidden" value="<?php echo esc_attr( $token['explorer'] ); ?>" required readonly />
                               </div>
                             </div>

                             <div class="metapress-grid metapress-wallet metapress-setting">
                               <div class="metapress-setting-title">
                                 <?php _e('TEST Network', $wp_metapress_textdomain); ?>
                               </div>
                               <div class="metapress-setting-content">
                                 <select class="selected-token-test-network" name="metapress_custom_tokens_list[<?php echo esc_attr( $key ); ?>][test_network]">
                                     <?php foreach($metapress_supported_test_networks as $test_network_option) {
                                          if( isset($test_network_option['enabled']) ) {
                                         ?>
                                         <option value="<?php echo esc_attr($test_network_option['slug']); ?>" <?php selected($token['test_network'], esc_attr($test_network_option['slug']) ); ?> data-chainid="<?php echo esc_attr($test_network_option['chainid']); ?>" data-explorer="<?php echo esc_attr($test_network_option['explorer']); ?>"><?php echo esc_attr($test_network_option['name']); ?></option>
                                     <?php } } ?>
                                 </select>
                                 <input class="regular-text set-network-chainid" name="metapress_custom_tokens_list[<?php echo esc_attr( $key ); ?>][test_chainid]" type="hidden" value="<?php echo esc_attr( $token['test_chainid'] ); ?>" required readonly />
                                 <input class="regular-text set-network-name" name="metapress_custom_tokens_list[<?php echo esc_attr( $key ); ?>][test_networkname]" type="hidden" value="<?php echo esc_attr( $token['test_networkname'] ); ?>" required readonly />
                                 <input class="regular-text set-network-explorer" name="metapress_custom_tokens_list[<?php echo esc_attr( $key ); ?>][test_explorer]" type="hidden" value="<?php echo esc_attr( $token['test_explorer'] ); ?>" required readonly />
                               </div>
                             </div>

                             <div class="metapress-grid metapress-wallet metapress-setting">
                               <div class="metapress-setting-title">
                                 <?php _e('Price (USD)', $wp_metapress_textdomain); ?>
                               </div>
                               <div class="metapress-setting-content">
                                 <input class="regular-text token-usd-price" name="metapress_custom_tokens_list[<?php echo esc_attr( $key ); ?>][usd_price]" type="number" min="0" step="0.000000001" value="<?php echo esc_attr( $token['usd_price'] ); ?>" /><br><br>
                                 <p class="description"><strong><?php _e('If the Binance / CoinGecko buttons below cannot find a price, or finds an incorrect price, you need to disable both automatic price conversions and manually set a price in USD.', $wp_metapress_textdomain); ?></strong></p>
                               </div>
                             </div>

                             <div class="metapress-grid metapress-wallet metapress-setting">
                               <div class="metapress-setting-title">
                                 <?php _e('Binance Price Conversion', $wp_metapress_textdomain); ?>
                               </div>
                               <div class="metapress-setting-content">
                                 <input name="metapress_custom_tokens_list[<?php echo esc_attr( $key ); ?>][binance_price_api]" type="checkbox" value="1" <?php checked(1, $token['binance_price_api']); ?> />
                                 <span><?php _e('Enabling this will automatically convert Web3 Product USD prices to token amounts at the time of transaction using the Binance API.', $wp_metapress_textdomain); ?></span><br><br>
                                 <label class="fetch-coin-binance-price button"><?php _e('Check Binance Price', $wp_metapress_textdomain); ?></label>
                               </div>
                             </div>

                             <div class="metapress-grid metapress-wallet metapress-setting">
                               <div class="metapress-setting-title">
                                 <?php _e('CoinGecko Price Conversion', $wp_metapress_textdomain); ?>
                               </div>
                               <div class="metapress-setting-content">
                                 <input name="metapress_custom_tokens_list[<?php echo esc_attr( $key ); ?>][coingecko_price_api]" type="checkbox" value="1" <?php checked(1, $token['coingecko_price_api']); ?> />
                                 <span><?php _e('Enabling this will automatically convert Web3 Product USD prices to token amounts at the time of transaction using the CoinGecko API', $wp_metapress_textdomain); ?></span><br><br>
                                 <label class="fetch-coin-coingecko-price button"><?php _e('Check CoinGecko Price', $wp_metapress_textdomain); ?></label>
                               </div>
                             </div>

                             <div class="metapress-grid metapress-wallet metapress-setting">
                               <div class="metapress-setting-title">
                                 <?php _e('Icon', $wp_metapress_textdomain); ?>
                               </div>
                               <div class="metapress-setting-content">
                                 <input class="regular-text icon-image-url" name="metapress_custom_tokens_list[<?php echo esc_attr( $key ); ?>][icon]" type="text" value="<?php echo esc_attr( $token['icon'] ); ?>" required/>
                                 <label class="upload-icon-image button"><?php _e('Upload', $wp_metapress_textdomain); ?></label>
                               </div>
                             </div>

                             <div class="metapress-grid metapress-wallet metapress-setting">
                               <div class="metapress-setting-title">
                                 <?php _e('Your Wallet Address', $wp_metapress_textdomain); ?>
                               </div>
                               <div class="metapress-setting-content">
                                 <input class="regular-text" name="metapress_custom_tokens_list[<?php echo esc_attr( $key ); ?>][address]" type="text" value="<?php echo esc_attr( $token['address'] ); ?>" />
                               </div>
                             </div>
                             <div class="metapress-grid metapress-wallet metapress-setting">
                               <div class="metapress-setting-title">
                                 <?php _e('Enabled', $wp_metapress_textdomain); ?>
                               </div>
                               <div class="metapress-setting-content">
                                 <input name="metapress_custom_tokens_list[<?php echo esc_attr( $key ); ?>][enabled]" type="checkbox" value="1" <?php checked(1, $token['enabled']); ?> />
                                 <span><?php _e('Enable or Disable this custom token', $wp_metapress_textdomain); ?></span>
                               </div>
                             </div>
                             <div class="metapress-grid metapress-wallet metapress-setting">
                               <div class="metapress-setting-title">
                                 <?php _e('Remove', $wp_metapress_textdomain); ?>
                               </div>
                               <div class="metapress-setting-content">
                                 <label class="remove-custom-token button"><?php _e('Delete Token', $wp_metapress_textdomain); ?></label>
                               </div>
                             </div>
                        </div>
                     <?php } } ?>
                </div>
            </div>
            <div class="metapress-admin-section metapress-border-box">
                <div id="add-new-token" class="button"><?php _e('Add Token', $wp_metapress_textdomain); ?></div>
                <?php submit_button(); ?>
            </div>
        </form>
    </div>
</div>
