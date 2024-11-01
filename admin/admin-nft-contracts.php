<div class="wrap">
    <?php include('metapress-admin-header.php'); ?>
    <div class="metapress-plugin-settings">
        <form method="post" action="options.php">
            <?php
              global $wp_metapress_textdomain;
              settings_fields( 'metapress-plugin-nft-contracts' );
              $metapress_supported_networks = apply_filters('filter_web3_access_networks', get_option('metapress_supported_networks'), 'remove');
              $metapress_supported_token_types = get_option('metapress_supported_token_types');
              $metapress_nft_contract_list = get_option('metapress_nft_contract_list', array());

              if( empty($metapress_nft_contract_list) ) {
                  $metapress_nft_contract_list = array();
              } else {
                  $metapress_nft_contract_list = array_values($metapress_nft_contract_list);
              }
              ?>

              <div class="metapress-admin-section metapress-border-box">
                <h1><?php _e('Custom Tokens for Access to Content', $wp_metapress_textdomain); ?></h1>
                <p class="metapress-admin-notice"><?php _e('Add contract addresses for NFT collections or other ERC-20, ERC-721 and ERC-1155 tokens. This will allow you to provide access to content based on which NFTs or tokens visitors own', $wp_metapress_textdomain); ?>.</p>
                <ul class="metapress-notes">
                    <li><?php _e('Contracts must exist on either the Ethereum, Polygon, Binance Smart Chain, Avalanche or Fantom Network', $wp_metapress_textdomain); ?>.</li>
                    <li><strong><?php _e('IMPORTANT', $wp_metapress_textdomain); ?></strong>: <?php _e('The Token Contract Address should be a unique address and not a shared collection address. For example, ', $wp_metapress_textdomain); ?> <a href="https://etherscan.io/address/0x495f947276749ce646f68ac8c248420045cb7b5e" target="_blank"><?php _e('this address', $wp_metapress_textdomain); ?></a> <?php _e('is a shared OpenSea address. Using a shared collection address may result in the NFT verification system returning true if a users address owns ANY asset that belongs to the shared address.', $wp_metapress_textdomain); ?></li>
                    <li><?php _e('If you need to verify visitors own at least 1 ERC-1155 token within a smart contract on OpenSea, setup an', $wp_metapress_textdomain); ?> <a href="<?php echo admin_url('admin.php?page=metapress-api-keys'); ?>"><?php _e('OpenSea API Key', $wp_metapress_textdomain); ?></a>.
                        <?php _e('By default, ERC-1155 token balances require a Token ID for each asset you want to verify an address owns', $wp_metapress_textdomain); ?>.</li>
                </ul>
                <div id="live-metapress-tokens" class="metapress-wallet-section metapress-border-box">
                    <?php if( ! empty($metapress_nft_contract_list) ) {
                        foreach($metapress_nft_contract_list as $key => $token) {
                            if( empty($token['enabled']) ) {
                                $token['enabled'] = 0;
                            }
                            ?>
                        <div class="metapress-admin-settings metapress-border-box live-token">
                            <div class="metapress-grid metapress-wallet metapress-setting">
                              <div class="metapress-setting-title">
                                <?php _e('Token Contract Address', $wp_metapress_textdomain); ?>
                              </div>
                              <div class="metapress-setting-content">
                                <input class="regular-text token-contract-address" name="metapress_nft_contract_list[<?php echo esc_attr( $key ); ?>][contract_address]" type="text" value="<?php echo esc_attr( $token['contract_address'] ); ?>" required/>
                              </div>
                            </div>
                             <div class="metapress-grid metapress-wallet metapress-setting">
                               <div class="metapress-setting-title">
                                 <?php _e('Name', $wp_metapress_textdomain); ?>
                               </div>
                               <div class="metapress-setting-content">
                                 <input class="regular-text token-contract-name" name="metapress_nft_contract_list[<?php echo esc_attr( $key ); ?>][name]" type="text" value="<?php echo esc_attr( $token['name'] ); ?>" required/>
                               </div>
                             </div>

                             <div class="metapress-grid metapress-wallet metapress-setting">
                               <div class="metapress-setting-title">
                                 <?php _e('Network', $wp_metapress_textdomain); ?>
                               </div>
                               <div class="metapress-setting-content">
                                 <select class="selected-token-network" name="metapress_nft_contract_list[<?php echo esc_attr( $key ); ?>][network]">
                                     <?php foreach($metapress_supported_networks as $network_option) { ?>
                                         <option value="<?php echo esc_attr($network_option['slug']); ?>" <?php selected($token['network'], esc_attr($network_option['slug']) ); ?> data-chainid="<?php echo esc_attr($network_option['chainid']); ?>"><?php echo esc_attr($network_option['name']); ?></option>
                                     <?php } ?>
                                 </select>

                                 <input class="regular-text set-network-chainid" name="metapress_nft_contract_list[<?php echo esc_attr( $key ); ?>][chainid]" type="hidden" value="<?php echo esc_attr( $token['chainid'] ); ?>" required readonly />
                                 <input class="regular-text set-network-name" name="metapress_nft_contract_list[<?php echo esc_attr( $key ); ?>][networkname]" type="hidden" value="<?php echo esc_attr( $token['networkname'] ); ?>" required readonly />
                               </div>
                             </div>

                             <div class="metapress-grid metapress-wallet metapress-setting">
                               <div class="metapress-setting-title">
                                 <?php _e('Token Type', $wp_metapress_textdomain); ?>
                               </div>
                               <div class="metapress-setting-content">
                                 <select name="metapress_nft_contract_list[<?php echo esc_attr( $key ); ?>][token_type]">
                                     <?php foreach($metapress_supported_token_types as $token_type) { ?>
                                         <option value="<?php echo esc_attr( $token_type['type'] ); ?>" <?php selected($token['token_type'],  esc_attr( $token_type['type'] ) ); ?>><?php echo esc_attr( $token_type['name'] ); ?></option>
                                     <?php } ?>
                                 </select>
                               </div>
                             </div>

                             <div class="metapress-grid metapress-wallet metapress-setting">
                               <div class="metapress-setting-title">
                                 <?php _e('Enabled', $wp_metapress_textdomain); ?>
                               </div>
                               <div class="metapress-setting-content">
                                 <input name="metapress_nft_contract_list[<?php echo esc_attr( $key ); ?>][enabled]" type="checkbox" value="1" <?php checked(1, $token['enabled']); ?> />
                                 <span><?php _e('Enable or Disable this smart contract', $wp_metapress_textdomain); ?></span>
                               </div>
                             </div>
                             <div class="metapress-grid metapress-wallet metapress-setting">
                               <div class="metapress-setting-title">
                                 <?php _e('Remove', $wp_metapress_textdomain); ?>
                               </div>
                               <div class="metapress-setting-content">
                                 <label class="remove-custom-token button"><?php _e('Delete Contract', $wp_metapress_textdomain); ?></label>
                               </div>
                             </div>
                        </div>
                     <?php } } ?>
                </div>
            </div>
            <div class="metapress-admin-section metapress-border-box">
                <div id="add-new-token" class="button"><?php _e('Add Contract Address', $wp_metapress_textdomain); ?></div>
                <?php submit_button(); ?>
            </div>
        </form>
    </div>
</div>
