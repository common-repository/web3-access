class MetaPress_Admin_Network_Manager {
    constructor() {
        this.network_count = 0;
    }

    set_mode() {
        if( metapressjsdata.live_mode ) {
            this.network_count = jQuery('.live-network').length;
        } else {
            this.network_count = jQuery('.test-network').length;
        }

    }

    increase_count() {
        this.network_count++;
    }

    decrease_count() {
        this.network_count--;
        if( this.network_count < 0 ) {
            this.network_count = 0;
        }
    }

    add_new_network() {
      let new_network_html = '<div class="metapress-admin-settings metapress-border-box live-network"><div class="metapress-grid metapress-wallet metapress-setting"><div class="metapress-setting-title">Network Name</div><div class="metapress-setting-content"><input class="regular-text" name="metapress_supported_networks['+this.network_count+'][name]" type="text" value="" required /></div></div><div class="metapress-grid metapress-wallet metapress-setting"><div class="metapress-setting-title">Slug</div><div class="metapress-setting-content"><input class="regular-text" name="metapress_supported_networks['+this.network_count+'][slug]" type="text" value="" required /></div></div><div class="metapress-grid metapress-wallet metapress-setting"><div class="metapress-setting-title">Chain ID</div><div class="metapress-setting-content"><input class="regular-text" name="metapress_supported_networks['+this.network_count+'][chainid]" type="text" value="" required placeholder="0x0" /></div></div><div class="metapress-grid metapress-wallet metapress-setting"><div class="metapress-setting-title">Symbol</div><div class="metapress-setting-content"><input class="regular-text" name="metapress_supported_networks['+this.network_count+'][symbol]" type="text" value="" required /></div></div><div class="metapress-grid metapress-wallet metapress-setting"><div class="metapress-setting-title">Receiving Wallet Address</div><div class="metapress-setting-content"><input class="regular-text" name="metapress_supported_networks['+this.network_count+'][receiving_address]" type="text" value="" required/></div></div><div class="metapress-grid metapress-wallet metapress-setting"><div class="metapress-setting-title">Explorer URL</div><div class="metapress-setting-content"><input class="regular-text" name="metapress_supported_networks['+this.network_count+'][explorer]" type="text" value="" required /></div></div><div class="metapress-grid metapress-wallet metapress-setting"><div class="metapress-setting-title">Icon</div><div class="metapress-setting-content"><input class="regular-text icon-image-url" name="metapress_supported_networks['+this.network_count+'][icon]" type="text" value="" required/><label class="upload-icon-image button">Upload</label><p>Recommended square image (250px by 250px)</p></div></div><div class="metapress-grid metapress-wallet metapress-setting"><div class="metapress-setting-title">Enabled</div><div class="metapress-setting-content"><input name="metapress_supported_networks['+this.network_count+'][enabled]" type="checkbox" value="1" /><span>Enable or Disable this Network</span></div></div><div class="metapress-grid metapress-wallet metapress-setting"><div class="metapress-setting-title">Remove</div><div class="metapress-setting-content"><label class="remove-custom-network button">Delete Network</label></div></div>';

      jQuery('#live-metapress-networks').append(new_network_html);
      this.increase_count();
  }

  add_new_test_network() {
    let new_network_html = '<div class="metapress-admin-settings metapress-border-box test-network"><div class="metapress-grid metapress-wallet metapress-setting"><div class="metapress-setting-title">Network Name</div><div class="metapress-setting-content"><input class="regular-text" name="metapress_supported_test_networks['+this.network_count+'][name]" type="text" value="" required /></div></div><div class="metapress-grid metapress-wallet metapress-setting"><div class="metapress-setting-title">Slug</div><div class="metapress-setting-content"><input class="regular-text" name="metapress_supported_test_networks['+this.network_count+'][slug]" type="text" value="" required /></div></div><div class="metapress-grid metapress-wallet metapress-setting"><div class="metapress-setting-title">Chain ID</div><div class="metapress-setting-content"><input class="regular-text" name="metapress_supported_test_networks['+this.network_count+'][chainid]" type="text" value="" required placeholder="0x0" /></div></div><div class="metapress-grid metapress-wallet metapress-setting"><div class="metapress-setting-title">Symbol</div><div class="metapress-setting-content"><input class="regular-text" name="metapress_supported_test_networks['+this.network_count+'][symbol]" type="text" value="" required /></div></div><div class="metapress-grid metapress-wallet metapress-setting"><div class="metapress-setting-title">Receiving Wallet Address</div><div class="metapress-setting-content"><input class="regular-text" name="metapress_supported_test_networks['+this.network_count+'][receiving_address]" type="text" value="" required/></div></div><div class="metapress-grid metapress-wallet metapress-setting"><div class="metapress-setting-title">Explorer URL</div><div class="metapress-setting-content"><input class="regular-text" name="metapress_supported_test_networks['+this.network_count+'][explorer]" type="text" value="" required /></div></div><div class="metapress-grid metapress-wallet metapress-setting"><div class="metapress-setting-title">Icon</div><div class="metapress-setting-content"><input class="regular-text icon-image-url" name="metapress_supported_test_networks['+this.network_count+'][icon]" type="text" value="" required/><label class="upload-icon-image button">Upload</label><p>Recommended square image (250px by 250px)</p></div></div><div class="metapress-grid metapress-wallet metapress-setting"><div class="metapress-setting-title">Enabled</div><div class="metapress-setting-content"><input name="metapress_supported_test_networks['+this.network_count+'][enabled]" type="checkbox" value="1" /><span>Enable or Disable this Network</span></div></div><div class="metapress-grid metapress-wallet metapress-setting"><div class="metapress-setting-title">Remove</div><div class="metapress-setting-content"><label class="remove-custom-network button">Delete Network</label></div></div>';

    jQuery('#live-metapress-networks').append(new_network_html);
    this.increase_count();
}
}
const metapress_admin_network_manager = new MetaPress_Admin_Network_Manager();
jQuery(document).ready(function() {
    metapress_admin_network_manager.set_mode();
    /*jQuery('#add-new-network').click( function() {
        if( metapressjsdata.live_mode ) {
            metapress_admin_network_manager.add_new_network();
        } else {
            metapress_admin_network_manager.add_new_test_network();
        }
    })

    jQuery('body').delegate('.remove-custom-network', 'click', function() {
        if( window.confirm('Are you sure you want to Delete this Network?') ) {
            let network_fields = jQuery(this).parents('.metapress-admin-settings');
            network_fields.remove();
        }
    });*/

});
