<?php
  global $wp_metapress_textdomain;
  $metapress_live_mode = get_option('metapress_live_mode', 0);
  $payments_page_title = __('Web3 Access Payments', $wp_metapress_textdomain);
  $metapress_mode_notice = __('You are in Live Mode.', $wp_metapress_textdomain);
  if( ! $metapress_live_mode ) {
    $payments_page_title = __('Web3 Access Test Payments', $wp_metapress_textdomain);
    $metapress_mode_notice = __('You are in Test Mode. The following are test network payments.', $wp_metapress_textdomain);
  }
  $current_time = current_time('timestamp');
  $back_one_month = strtotime('-1 month', $current_time);
  $plus_one_month = strtotime('+1 month', $current_time);
  $transaction_from_date = wp_date('F d, Y', $back_one_month);
  $transaction_to_date = wp_date('F d, Y', $plus_one_month);
  $metapress_custom_tokens_list = get_option('metapress_custom_tokens_list', array());
  if( $metapress_live_mode ) {
      $metapress_supported_networks = apply_filters('filter_web3_access_networks', get_option('metapress_supported_networks'), 'add');
  } else {
      $metapress_supported_networks = apply_filters('filter_web3_access_test_networks', get_option('metapress_supported_test_networks'), 'add');
  }
  $payment_symbol_options = array();
  if(! empty($metapress_supported_networks) ) {
      foreach($metapress_supported_networks as $payment_network) {
          if( isset($payment_network['symbol']) && ! in_array($payment_network['symbol'], $payment_symbol_options) ) {
              $payment_symbol_options[] = $payment_network['symbol'];
          }
      }
  }

  if( ! empty($metapress_custom_tokens_list) ) {
      foreach($metapress_custom_tokens_list as $custom_token) {
          if( isset($custom_token['currency_symbol']) && ! in_array($custom_token['currency_symbol'], $payment_symbol_options) ) {
              $payment_symbol_options[] = $custom_token['currency_symbol'];
          }
      }
  }
?>

<div class="wrap">
    <?php include('metapress-admin-header.php'); ?>
    <div class="metapress-plugin-settings">
        <div class="metapress-admin-section metapress-border-box">
          <h1><?php echo esc_attr($payments_page_title); ?></h1>
          <div class="metapress-payments-overview">
              <div class="metapress-admin-overview-block metapress-border-box">
                  <label class="metapress-overiew-time"><?php _e('Last 7 Days', 'wp-metapress'); ?></label>
                  <label class="metapress-retrieving-payment-data"><span class="dashicons dashicons-update metapress-update-spin"></span> <?php _e('Retrieving data...', 'wp-metapress'); ?></label>
                  <label class="metapress-admin-amount-made"><span class="payments-token-label">ETH</span> <span id="metapress-last-week-payments"></span></label>
              </div>
              <div class="metapress-admin-overview-block metapress-border-box">
                  <label class="metapress-overiew-time"><?php _e('This Month', 'wp-metapress'); ?></label>
                  <label class="metapress-retrieving-payment-data"><span class="dashicons dashicons-update metapress-update-spin"></span> <?php _e('Retrieving data...', 'wp-metapress'); ?></label>
                  <label class="metapress-admin-amount-made"><span class="payments-token-label">ETH</span> <span id="metapress-this-month-payments"></span></label>
              </div>
              <div class="metapress-admin-overview-block metapress-border-box">
                  <label class="metapress-overiew-time"><?php _e('This Year', 'wp-metapress'); ?></label>
                  <label class="metapress-retrieving-payment-data"><span class="dashicons dashicons-update metapress-update-spin"></span> <?php _e('Retrieving data...', 'wp-metapress'); ?></label>
                  <label class="metapress-admin-amount-made"><span class="payments-token-label">ETH</span> <span id="metapress-this-year-payments"></span></label>
              </div>
          </div>

          <p class="metapress-admin-notice"><?php echo esc_attr($metapress_mode_notice); ?></p>
          <div class="metapress-filtering-box">
              <div class="metapress-filter-option filter-icon metapress-border-box">
                  <span class="dashicons dashicons-filter"></span>
              </div>
              <div class="metapress-filter-option metapress-border-box">
                  <label class="metapress-filter-title"><?php _e('Token', 'wp-metapress'); ?></label>
                  <div class="metapress-filter select metapress-border-box">
                    <select id="metapress-token-filter">
                      <?php if( ! empty($payment_symbol_options) ) {
                          foreach($payment_symbol_options as $payment_symbol) { ?>
                              <option value="<?php echo esc_attr($payment_symbol); ?>"><?php echo esc_attr($payment_symbol); ?></option>
                      <?php } } ?>
                    </select>
                  </div>
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
                    <input type="text" id="metapress-address-filter" placeholder="Search from address" />
                  </div>
              </div>
          </div>
          <div class="metapress-filtering-box">
              <div class="metapress-filter-option metapress-border-box"></div>
              <div class="metapress-filter-option metapress-border-box"></div>
              <div class="metapress-filter-option metapress-border-box">
                  <label class="metapress-filter-title"><?php _e('From', 'wp-metapress'); ?></label>
                  <div class="metapress-filter text-input metapress-border-box">
                    <input type="text" id="set-transaction-from-date" class="metapress-select-date-admin" value="<?php echo esc_attr($transaction_from_date); ?>" readonly placeholder="Transactions from">
                    <input type="text" id="metapress-filter-from-date" value="<?php echo esc_attr($back_one_month); ?>" readonly hidden>
                  </div>
              </div>

              <div class="metapress-filter-option metapress-border-box">
                  <label class="metapress-filter-title"><?php _e('To', 'wp-metapress'); ?></label>
                  <div class="metapress-filter text-input metapress-border-box">
                    <input type="text" id="set-transaction-to-date" class="metapress-select-date-admin" value="<?php echo esc_attr($transaction_to_date); ?>" readonly placeholder="Transactions to">
                    <input type="text" id="metapress-filter-to-date" value="<?php echo esc_attr($plus_one_month); ?>" readonly hidden>
                  </div>
              </div>
          </div>

          <p id="update-filtering-text" class="metapress-filter-notice"><?php _e('Showing ETH transactions', 'wp-metapress'); ?></p>
          <div class="metapress-admin-settings metapress-border-box metapress-header">
            <div class="metapress-grid metapress-payment">
              <div class="metapress-setting-title">
                <?php _e('Product', $wp_metapress_textdomain); ?>
              </div>
              <div class="metapress-setting-title">
                <?php _e('From Address', $wp_metapress_textdomain); ?>
              </div>
              <div class="metapress-setting-title">
                <?php _e('Paid With', $wp_metapress_textdomain); ?>
              </div>
              <div class="metapress-setting-title">
                <?php _e('Amount', $wp_metapress_textdomain); ?>
              </div>
              <div class="metapress-setting-title">
                <?php _e('Network', $wp_metapress_textdomain); ?>
              </div>
              <div class="metapress-setting-title">
                <?php _e('Date', $wp_metapress_textdomain); ?>
              </div>

              <div class="metapress-setting-title">
                <?php _e('Status', $wp_metapress_textdomain); ?>
              </div>
              <div class="metapress-setting-title">
                <?php _e('View', $wp_metapress_textdomain); ?>
              </div>
            </div>
          </div>
          <div id="load-metapress-payments"></div>
          <div class="metapress-grid metapress-prev-next-buttons">
              <div class="metapress-nav-button prev"><div class="button button-primary"><span class="dashicons dashicons-arrow-left"></span> <?php _e('Back', $wp_metapress_textdomain); ?></div></div>
              <div class="metapress-nav-button next"><div class="button button-primary"><?php _e('Next', $wp_metapress_textdomain); ?> <span class="dashicons dashicons-arrow-right"></span></div></div>
          </div>
        </div>
    </div>
</div>
