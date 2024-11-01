<div class="wrap">
    <?php include('metapress-admin-header.php'); ?>
    <div class="metapress-plugin-settings">
        <form method="post" action="options.php">
            <?php
              global $wp_metapress_textdomain;
              settings_fields( 'metapress-access-settings' );
              $metapress_restrict_site_access = get_option('metapress_restrict_site_access', 0);
              $metapress_site_access_required_products = get_option('metapress_site_access_required_products', array());
              if( empty($metapress_site_access_required_products) ) {
                  $metapress_site_access_required_products = array();
              }
              $metapress_mode_notice = __('Note that restricting access to your entire site may impact your websites SEO (Search Engine Optimization)', $wp_metapress_textdomain);

              $metapress_redirect_type = get_option('metapress_redirect_type');
              $metapress_redirect_page_id = get_option('metapress_redirect_page_id');
              $metapress_redirect_custom_url = get_option('metapress_redirect_custom_url');

              $metapress_access_tokens_expire = get_option('metapress_access_tokens_expire', '+1 hour');
              if( empty($metapress_access_tokens_expire) ) {
                  $metapress_access_tokens_expire = '+1 hour';
              }

              $existing_pages = get_pages();
            $mp_product_args = array(
                'post_type' => 'metapress_product',
                'posts_per_page' => -1,
                'post_status' => 'publish'
            );
            $mp_products = get_posts($mp_product_args);
            ?>
            <div class="metapress-admin-section metapress-border-box">
              <h1><?php _e('Web3 Access Access Settings', $wp_metapress_textdomain); ?></h1>
              <p class="metapress-admin-notice"><?php echo esc_attr($metapress_mode_notice); ?>.</p>
              <div class="metapress-admin-settings metapress-border-box metapress-width-600">
                <div class="metapress-grid metapress-setting">
                  <div class="metapress-setting-title">
                    <?php _e('Restrict Website Access', $wp_metapress_textdomain); ?>
                  </div>
                  <div class="metapress-setting-content metapress-align-right">
                    <input name="metapress_restrict_site_access" type="checkbox" value="1" <?php checked(1, $metapress_restrict_site_access); ?> />
                    <p><?php _e('Enabling this option will lock your entire website. Visitors will need to either purchase one of your Web3 Products or verify they own an NFT to access your website content.', $wp_metapress_textdomain); ?></p>
                  </div>
                </div>
              </div>
            </div>


            <div class="metapress-admin-section metapress-border-box">
                <h3><?php _e('Required Web3 Access Products', $wp_metapress_textdomain); ?></h3>
                <p class="metapress-admin-notice"><?php _e('Set which Web3 Products are required to access website content. Visitors will only need to purchase or verify 1 of these products.', $wp_metapress_textdomain); ?></p>
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
                                  <input type="checkbox" name="metapress_site_access_required_products[]" value="<?php echo esc_attr($product->ID); ?>" <?php checked(in_array($product->ID, $metapress_site_access_required_products)); ?> />
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
              <h3><?php _e('No Access Redirect', $wp_metapress_textdomain); ?></h3>
              <p class="metapress-admin-notice"><?php _e('Choose where visitors without access should be redirected to.', $wp_metapress_textdomain); ?></p>
              <p class="metapress-admin-notice"><strong><?php _e('Note that your Web3 Access Checkout and Transactions page are automatically accessible to allow for purchases and viewing transactions. You can also allow access on specific pages by Editing a Page and enabling the Allow Page Access option.', $wp_metapress_textdomain); ?></strong></p>
              <div class="metapress-admin-settings metapress-border-box metapress-width-600">

                  <div class="metapress-grid metapress-setting">
                    <div class="metapress-setting-title">
                      <?php _e('Redirect Type', $wp_metapress_textdomain); ?>
                    </div>
                    <div class="metapress-setting-content metapress-align-right">
                      <select name="metapress_redirect_type">
                        <option value=""><?php _e('Same Page (No Redirect)', $wp_metapress_textdomain); ?></option>
                        <option value="page" <?php selected('page', $metapress_redirect_type); ?>><?php _e('A Specific Page', $wp_metapress_textdomain); ?></option>
                        <option value="custom" <?php selected('custom', $metapress_redirect_type); ?>><?php _e('A Custom URL', $wp_metapress_textdomain); ?></option>
                      </select>
                    </div>
                  </div>


                <div class="metapress-grid metapress-setting">
                  <div class="metapress-setting-title">
                    <?php _e('Redirect Page', $wp_metapress_textdomain); ?>
                  </div>
                  <div class="metapress-setting-content metapress-align-right">
                    <select name="metapress_redirect_page_id">
                      <option value="0"><?php _e('None', $wp_metapress_textdomain); ?></option>
                      <?php if( ! empty($existing_pages) ) {
                          foreach($existing_pages as $set_page) { ?>
                            <option value="<?php echo esc_attr($set_page->ID); ?>" <?php selected($set_page->ID, $metapress_redirect_page_id); ?>><?php echo esc_attr($set_page->post_title); ?></option>
                      <?php } } ?>
                    </select>
                  </div>
                </div>


               <div class="metapress-grid metapress-setting">
                 <div class="metapress-setting-title">
                   <?php _e('Custom URL', $wp_metapress_textdomain); ?>
                 </div>
                 <div class="metapress-setting-content metapress-align-right">
                   <input class="regular-text" name="metapress_redirect_custom_url" type="url" value="<?php echo esc_attr( $metapress_redirect_custom_url ); ?>" />
                   <p><?php _e('A custom URL to redirect visitors to if they do not have access. This should only be filled in if you set the Redirect Type above to Custom URL', $wp_metapress_textdomain); ?></p>
                 </div>
               </div>



              </div>
            </div>

            <div class="metapress-admin-section metapress-border-box">
              <h3><?php _e('Access Tokens', $wp_metapress_textdomain); ?></h3>
              <p class="metapress-admin-notice"><?php _e('Settings for access tokens after users have purchased access to content or verified ownership', $wp_metapress_textdomain); ?>.</p>
              <div class="metapress-admin-settings metapress-border-box metapress-width-600">
                <div class="metapress-grid metapress-setting">
                  <div class="metapress-setting-title">
                    <?php _e('Access Tokens Expire After', $wp_metapress_textdomain); ?>
                  </div>
                  <div class="metapress-setting-content metapress-align-right">
                    <select name="metapress_access_tokens_expire">
                      <option value="+1 hour" <?php selected('+1 hour', $metapress_access_tokens_expire); ?>><?php _e('1 Hour', $wp_metapress_textdomain); ?></option>
                      <option value="+6 hours" <?php selected('+6 hours', $metapress_access_tokens_expire); ?>><?php _e('6 Hours', $wp_metapress_textdomain); ?></option>
                      <option value="+12 hours" <?php selected('+12 hours', $metapress_access_tokens_expire); ?>><?php _e('12 Hours', $wp_metapress_textdomain); ?></option>
                      <option value="+1 day" <?php selected('+1 day', $metapress_access_tokens_expire); ?>><?php _e('24 Hours', $wp_metapress_textdomain); ?></option>
                      <option value="+2 days" <?php selected('+2 days', $metapress_access_tokens_expire); ?>><?php _e('48 Hours', $wp_metapress_textdomain); ?></option>
                      <option value="+1 week" <?php selected('+1 week', $metapress_access_tokens_expire); ?>><?php _e('1 Week', $wp_metapress_textdomain); ?></option>
                      <option value="+1 month" <?php selected('+1 month', $metapress_access_tokens_expire); ?>><?php _e('1 Month', $wp_metapress_textdomain); ?></option>
                    </select>
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
