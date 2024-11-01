<?php
  global $wp_metapress_textdomain;
  $metapress_live_mode = get_option('metapress_live_mode', 0);
  $current_time = current_time('timestamp');
?>

<div class="wrap">
    <?php include('metapress-admin-header.php'); ?>
    <div class="metapress-plugin-settings">
        <div class="metapress-admin-section metapress-border-box">
          <h1><?php echo esc_attr(__('Web3 Access Subscriptions', $wp_metapress_textdomain)); ?></h1>
          <p class="metapress-admin-notice"><?php echo esc_attr(__('Wallet Subscription Access created by Web3 payments or NFT verification', $wp_metapress_textdomain)); ?>.</p>
          <div class="metapress-filtering-box">
              <div class="metapress-filter-option filter-icon metapress-border-box">
                  <span class="dashicons dashicons-filter"></span>
              </div>
              <div class="metapress-filter-option metapress-border-box">

              </div>
              <div class="metapress-filter-option metapress-border-box">
                  <label class="metapress-filter-title"><?php _e('Product', 'wp-metapress'); ?></label>
                  <div class="metapress-filter text-input metapress-border-box">
                    <input type="text" id="metapress-product-filter" placeholder="Search product ID" />
                  </div>
              </div>
              <div class="metapress-filter-option metapress-border-box">
                  <label class="metapress-filter-title"><?php _e('Address', 'wp-metapress'); ?></label>
                  <div class="metapress-filter text-input metapress-border-box">
                    <input type="text" id="metapress-address-filter" placeholder="Search wallet address" />
                  </div>
              </div>
          </div>
          <p id="update-filtering-text" class="metapress-filter-notice"><?php _e('Showing All Subscriptions', 'wp-metapress'); ?></p>
          <div class="metapress-admin-settings metapress-border-box metapress-header">
            <div class="metapress-grid metapress-subscription">
              <div class="metapress-setting-title">
                <?php _e('Product', $wp_metapress_textdomain); ?>
              </div>
              <div class="metapress-setting-title">
                <?php _e('Wallet Address', $wp_metapress_textdomain); ?>
              </div>
              <div class="metapress-setting-title">
                <?php _e('Price', $wp_metapress_textdomain); ?>
              </div>
              <div class="metapress-setting-title">
                <?php _e('Paid With', $wp_metapress_textdomain); ?>
              </div>
              <div class="metapress-setting-title">
                <?php _e('Expires', $wp_metapress_textdomain); ?>
              </div>
              <div class="metapress-setting-title">
                <?php _e('Status', $wp_metapress_textdomain); ?>
              </div>
            </div>
          </div>
          <div id="load-metapress-subscriptions"></div>
          <div class="metapress-grid metapress-prev-next-buttons">
              <div class="metapress-nav-button prev"><div class="button button-primary"><span class="dashicons dashicons-arrow-left"></span> <?php _e('Back', $wp_metapress_textdomain); ?></div></div>
              <div class="metapress-nav-button next"><div class="button button-primary"><?php _e('Next', $wp_metapress_textdomain); ?> <span class="dashicons dashicons-arrow-right"></span></div></div>
          </div>
        </div>
    </div>
</div>
