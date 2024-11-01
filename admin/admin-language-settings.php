<div class="wrap">
    <?php include('metapress-admin-header.php'); ?>
    <div class="metapress-plugin-settings">
        <form method="post" action="options.php">
            <?php
              global $wp_metapress_textdomain;
              global $wp_metapress_text_settings;
              settings_fields( 'metapress-plugin-language' );
            ?>
            <div class="metapress-admin-section metapress-border-box">
              <h1><?php _e('Web3 Access Language & Text', $wp_metapress_textdomain); ?></h1>
            </div>

            <div class="metapress-admin-section metapress-border-box">
              <h3><?php _e('No Access Text Options', $wp_metapress_textdomain); ?></h3>
              <p class="metapress-admin-notice"><?php _e('Change the text that visitors see when they do not have access to a product or content.', $wp_metapress_textdomain); ?>.</p>
              <div class="metapress-wallet-section metapress-border-box">
                   <div class="metapress-admin-settings metapress-border-box">
                       <div class="metapress-grid metapress-wallet metapress-setting">
                         <div class="metapress-setting-title">
                           <?php _e('General No Access', $wp_metapress_textdomain); ?>
                         </div>
                         <div class="metapress-setting-content">
                           <input class="regular-text" name="metapress_text_settings[restricted_text]" type="text" value="<?php echo esc_attr( $wp_metapress_text_settings['restricted_text'] ); ?>" />
                         </div>
                       </div>

                       <div class="metapress-grid metapress-wallet metapress-setting">
                         <div class="metapress-setting-title">
                           <?php _e('Product Purchase', $wp_metapress_textdomain); ?>
                         </div>
                         <div class="metapress-setting-content">
                           <textarea class="large-text" name="metapress_text_settings[product_purchase_text]"><?php echo esc_attr( $wp_metapress_text_settings['product_purchase_text'] ); ?></textarea><br>
                           <span><?php _e('This notice lets the visitor know they need to purchase a product to access content', $wp_metapress_textdomain); ?></span>
                         </div>
                       </div>

                       <div class="metapress-grid metapress-wallet metapress-setting">
                         <div class="metapress-setting-title">
                           <?php _e('Ownership Verification', $wp_metapress_textdomain); ?>
                         </div>
                         <div class="metapress-setting-content">
                           <textarea class="large-text" name="metapress_text_settings[ownership_verification_text]"><?php echo esc_attr( $wp_metapress_text_settings['ownership_verification_text'] ); ?></textarea>
                           <span><?php _e('Displayed on content that can be accessed by verifying ownership of an NFT or token', $wp_metapress_textdomain); ?></span>
                         </div>
                       </div>

                       <div class="metapress-grid metapress-wallet metapress-setting">
                         <div class="metapress-setting-title">
                           <?php _e('Checkout Product Title', $wp_metapress_textdomain); ?>
                         </div>
                         <div class="metapress-setting-content">
                           <textarea class="large-text" name="metapress_text_settings[checkout_purchasing_text]"><?php echo esc_attr( $wp_metapress_text_settings['checkout_purchasing_text'] ); ?></textarea>
                           <span><?php _e('Displayed on the checkout page when purchasing a product.', $wp_metapress_textdomain); ?>. <strong>{product_title} <?php _e('must be included in the text', $wp_metapress_textdomain); ?></strong></span>
                         </div>
                       </div>

                       <div class="metapress-grid metapress-wallet metapress-setting">
                         <div class="metapress-setting-title">
                           <?php _e('Checkout Token Notice', $wp_metapress_textdomain); ?>
                         </div>
                         <div class="metapress-setting-content">
                           <textarea class="large-text" name="metapress_text_settings[checkout_product_access_text]"><?php echo esc_attr( $wp_metapress_text_settings['checkout_product_access_text'] ); ?></textarea>
                           <span><?php _e('This notice lets visitors on the checkout page know they may already have access to a product via ownership verification', $wp_metapress_textdomain); ?></span>
                         </div>
                       </div>

                       <div class="metapress-grid metapress-wallet metapress-setting">
                         <div class="metapress-setting-title">
                           <?php _e('Subscription Notice', $wp_metapress_textdomain); ?>
                         </div>
                         <div class="metapress-setting-content">
                           <textarea class="large-text" name="metapress_text_settings[subscription_product_notice]"><?php echo esc_attr( $wp_metapress_text_settings['subscription_product_notice'] ); ?></textarea>
                           <span><?php _e('This notice lets visitors know they are paying for a subscription product that will require a renewal payment or verification', $wp_metapress_textdomain); ?></span>
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
