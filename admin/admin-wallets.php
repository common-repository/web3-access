<div class="wrap">
    <?php include('metapress-admin-header.php'); ?>
    <div class="metapress-plugin-settings">
        <form method="post" action="options.php">
            <?php
              global $wp_metapress_textdomain;
              settings_fields( 'metapress-plugin-wallets' );
              $metapress_wallet_address = get_option('metapress_ethereum_wallet_address');
              $metapress_test_wallet_address = get_option('metapress_allowed_test_address');
              $metapress_solana_wallet_address = get_option('metapress_solana_wallet_address');
              $metapress_solana_wallet_test_address = get_option('metapress_solana_wallet_test_address');
              $metapress_solana_rpc_url = get_option('metapress_solana_rpc_url', 'https://api.mainnet-beta.solana.com/');
            ?>
            <div class="metapress-admin-section metapress-border-box">
              <h1><?php _e('Web3 Access Wallet Addresses', $wp_metapress_textdomain); ?></h1>
            </div>

            <div class="metapress-admin-section metapress-border-box">
              <h3><?php _e('Ethereum Receiving Wallet Address', $wp_metapress_textdomain); ?></h3>
              <p class="metapress-admin-notice"><?php echo esc_attr(__('This is the ethereum wallet address you will receive payments at', $wp_metapress_textdomain)); ?>.</p>
              <div class="metapress-wallet-section metapress-border-box metapress-width-600">
                <div class="metapress-admin-settings metapress-border-box">
                    <div class="metapress-grid metapress-wallet metapress-setting">
                        <div class="metapress-setting-title">
                            <?php _e('Address', $wp_metapress_textdomain); ?>
                        </div>
                        <div class="metapress-setting-content">
                            <input class="regular-text" name="metapress_ethereum_wallet_address" type="text" value="<?php echo esc_attr( $metapress_wallet_address ); ?>" />
                        </div>
                    </div>
                </div>
              </div>
            </div>

            <div class="metapress-admin-section metapress-border-box">
              <h3><?php _e('Allowed Test Mode Ethereum Wallet Address', $wp_metapress_textdomain); ?></h3>
              <p class="metapress-admin-notice"><?php echo esc_attr(__('An address for testing when in Test Mode. No other addresses will be allowed to connect when in Test Mode', $wp_metapress_textdomain)); ?>.</p>
              <div class="metapress-wallet-section metapress-border-box metapress-width-600">
                <div class="metapress-admin-settings metapress-border-box">
                    <div class="metapress-grid metapress-wallet metapress-setting">
                        <div class="metapress-setting-title">
                            <?php _e('Address', $wp_metapress_textdomain); ?>
                        </div>
                        <div class="metapress-setting-content">
                            <input class="regular-text" name="metapress_allowed_test_address" type="text" value="<?php echo esc_attr( $metapress_test_wallet_address ); ?>" />
                        </div>
                    </div>
                </div>
              </div>
            </div>

            <div class="metapress-admin-section metapress-border-box">
              <h3><?php _e('Solana Receiving Wallet Address', $wp_metapress_textdomain); ?></h3>
              <p class="metapress-admin-notice"><?php echo esc_attr(__('This is the Solana wallet address you will receive payments at in Live Mode', $wp_metapress_textdomain)); ?>.</p>
              <div class="metapress-wallet-section metapress-border-box metapress-width-600">
                <div class="metapress-admin-settings metapress-border-box">
                    <div class="metapress-grid metapress-wallet metapress-setting">
                        <div class="metapress-setting-title">
                            <?php _e('Address', $wp_metapress_textdomain); ?>
                        </div>
                        <div class="metapress-setting-content">
                            <input class="regular-text" name="metapress_solana_wallet_address" type="text" value="<?php echo esc_attr( $metapress_solana_wallet_address ); ?>"/>
                        </div>
                    </div>
                </div>
              </div>
            </div>

            <div class="metapress-admin-section metapress-border-box">
              <h3><?php _e('Allowed Test Mode Solana Wallet Address', $wp_metapress_textdomain); ?></h3>
              <p class="metapress-admin-notice"><?php echo esc_attr(__('An address for testing when in Test Mode. No other addresses will be allowed to connect when in Test Mode', $wp_metapress_textdomain)); ?>.</p>
              <div class="metapress-wallet-section metapress-border-box metapress-width-600">
                <div class="metapress-admin-settings metapress-border-box">
                    <div class="metapress-grid metapress-wallet metapress-setting">
                        <div class="metapress-setting-title">
                            <?php _e('Address', $wp_metapress_textdomain); ?>
                        </div>
                        <div class="metapress-setting-content">
                            <input class="regular-text" name="metapress_solana_wallet_test_address" type="text" value="<?php echo esc_attr( $metapress_solana_wallet_test_address ); ?>" />
                        </div>
                    </div>
                </div>
              </div>
            </div>

            <div class="metapress-admin-section metapress-border-box">
              <h3><?php _e('Solana RPC URL', $wp_metapress_textdomain); ?></h3>
              <p class="metapress-admin-notice"><?php echo esc_attr(__('Use a custom rpc URL for Solana if default mainnet-beta is returning 403 errors', $wp_metapress_textdomain)); ?>. <?php echo esc_attr(__('Use the following links to find RPC URLs for Solana', $wp_metapress_textdomain)); ?>.</p>
              <p><a href="https://www.alchemy.com/chain-connect/endpoints/alchemy-solana?r=a620b83e-22c3-4f3e-8c03-82a40f149b90" target="_blank">Alchemy Solana RPC Endpoint</a> | <a href="https://solana.com/rpc" target="_blank">More Solana RPC Endpoints</a></p>
              <div class="metapress-wallet-section metapress-border-box metapress-width-600">
                <div class="metapress-admin-settings metapress-border-box">
                    <div class="metapress-grid metapress-wallet metapress-setting">
                        <div class="metapress-setting-title">
                            <?php _e('Solana RPC Endpoint', $wp_metapress_textdomain); ?>
                        </div>
                        <div class="metapress-setting-content">
                            <input class="regular-text" name="metapress_solana_rpc_url" type="text" value="<?php echo esc_attr( $metapress_solana_rpc_url ); ?>" placeholder="https://api.mainnet-beta.solana.com/"/>
                        </div>
                    </div>
                </div>
              </div>
            </div>

            <div class="metapress-admin-section metapress-border-box">
                <?php submit_button(); ?>
            </div>
        </form>
    </div>
</div>
