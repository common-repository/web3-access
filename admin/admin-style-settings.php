<div class="wrap">
    <?php include('metapress-admin-header.php'); ?>
    <div class="metapress-plugin-settings">
        <form method="post" action="options.php">
            <?php
              global $wp_metapress_textdomain;
              settings_fields( 'metapress-plugin-style' );
              $metapress_style_settings = get_option('metapress_style_settings');
            ?>
            <div class="metapress-admin-section metapress-border-box">
              <h1><?php _e('Web3 Access Styling', $wp_metapress_textdomain); ?></h1>
            </div>

            <div class="metapress-admin-section metapress-border-box">
              <h3><?php _e('General Style', $wp_metapress_textdomain); ?></h3>
              <p class="metapress-admin-notice"><?php _e('Choose a light or dark theme and change button colour', $wp_metapress_textdomain); ?>.</p>
              <div class="metapress-wallet-section metapress-border-box">
                   <div class="metapress-admin-settings metapress-border-box">
                       <div class="metapress-grid metapress-wallet metapress-setting">
                         <div class="metapress-setting-title">
                           <?php _e('Theme', $wp_metapress_textdomain); ?>
                         </div>
                         <div class="metapress-setting-content">
                             <select name="metapress_style_settings[style]">
                                 <option value="light" <?php selected('light', $metapress_style_settings['style']); ?>><?php _e('Light', $wp_metapress_textdomain); ?></option>
                                 <option value="dark" <?php selected('dark', $metapress_style_settings['style']); ?>><?php _e('Dark', $wp_metapress_textdomain); ?></option>
                             </select>
                         </div>
                       </div>

                       <div class="metapress-grid metapress-wallet metapress-setting">
                         <div class="metapress-setting-title">
                           <?php _e('Accent Colour', $wp_metapress_textdomain); ?>
                         </div>
                         <div class="metapress-setting-content">
                           <input id="metapress-button-color-picker" name="metapress_style_settings[accent_color]" type="text" value="<?php echo esc_attr($metapress_style_settings['accent_color']); ?>" /><br>
                           <span><?php _e('Changes the background colour of primary Web3 Access buttons, loading text and price text', $wp_metapress_textdomain); ?>.</span>
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
