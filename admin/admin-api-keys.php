<div class="wrap">
    <?php include('metapress-admin-header.php'); ?>
    <div class="metapress-plugin-settings">
        <form method="post" action="options.php">
            <?php
                global $wp_metapress_textdomain;
                settings_fields( 'metapress-api-key-settings' );
                $metapress_moralis_api_key = get_option('metapress_moralis_api_key');
                $metapress_opensea_api_key = get_option('metapress_opensea_api_key');
                if( empty($metapress_moralis_api_key) ) {
                    $metapress_moralis_api_key = "";
                }
                if( empty($metapress_opensea_api_key) ) {
                    $metapress_opensea_api_key = "";
                }

              ?>
              <div class="metapress-admin-section metapress-border-box">
                <h3><?php _e('OpenSea API Key', $wp_metapress_textdomain); ?></h3>
                <div class="metapress-admin-settings metapress-border-box metapress-width-600">
                  <div class="metapress-grid metapress-setting">
                    <div class="metapress-setting-title">
                      <?php _e('API Key', $wp_metapress_textdomain); ?>
                    </div>
                    <div class="metapress-setting-content metapress-align-right">
                     <input class="regular-text" name="metapress_opensea_api_key" type="password" value="<?php echo esc_html( $metapress_opensea_api_key ); ?>"  />
                    </div>
                  </div>
                  <div class="metapress-grid metapress-setting">
                    <div class="metapress-setting-title">
                      <?php _e('What Is this', $wp_metapress_textdomain); ?>?
                    </div>
                    <div class="metapress-setting-content">
                      <p><?php _e('If you need to verify visitors own at least 1 ERC-1155 token within a smart contract on OpenSea, enter your', $wp_metapress_textdomain); ?> <a href="https://docs.opensea.io/reference/request-an-api-key" target="_blank"><?php _e('OpenSea API Key', $wp_metapress_textdomain); ?></a>.
                          <?php _e('By default, ERC-1155 token balances require a Token ID for each asset you want to verify an address owns', $wp_metapress_textdomain); ?>.</p>
                    </div>
                  </div>
                </div>
              </div>

              <div class="metapress-admin-section metapress-border-box">
                <h3><?php _e('Moralis API', $wp_metapress_textdomain); ?></h3>
                <div class="metapress-admin-settings metapress-border-box metapress-width-600">
                  <div class="metapress-grid metapress-setting">
                    <div class="metapress-setting-title">
                      <?php _e('API Key', $wp_metapress_textdomain); ?>
                    </div>
                    <div class="metapress-setting-content metapress-align-right">
                     <input class="regular-text" name="metapress_moralis_api_key" type="password" value="<?php echo esc_html( $metapress_moralis_api_key ); ?>"  />
                    </div>
                  </div>
                  <div class="metapress-grid metapress-setting">
                    <div class="metapress-setting-title">
                      <?php _e('Get A Key', $wp_metapress_textdomain); ?>
                    </div>
                    <div class="metapress-setting-content">
                      <p><a class="button button-primary" href="https://admin.moralis.io/web3apis" target="_blank"><?php _e('Get API Key', $wp_metapress_textdomain); ?></a><br><br><?php _e('Under development', $wp_metapress_textdomain); ?>.</p>
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
