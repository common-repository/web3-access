class MetaPress_Admin_NFT_Manager {
    constructor() {
        this.token_count = 0;
    }

    set_mode() {
        this.token_count = jQuery('.live-token').length;
    }

    increase_count() {
        this.token_count++;
    }

    decrease_count() {
        this.token_count--;
        if( this.token_count < 0 ) {
            this.token_count = 0;
        }
    }

    add_new_token() {
      let new_token_html = '<div class="metapress-admin-settings metapress-border-box live-token"><div class="metapress-grid metapress-wallet metapress-setting"><div class="metapress-setting-title">Token Contract Address</div><div class="metapress-setting-content"><input class="regular-text token-contract-address" name="metapress_nft_contract_list['+this.token_count+'][contract_address]" type="text" value="" required/></div></div><div class="metapress-grid metapress-wallet metapress-setting"><div class="metapress-setting-title">Name</div><div class="metapress-setting-content"><input class="regular-text token-contract-name" name="metapress_nft_contract_list['+this.token_count+'][name]" type="text" value="" required/></div></div><div class="metapress-grid metapress-wallet metapress-setting"><div class="metapress-setting-title">Network</div><div class="metapress-setting-content"><select class="selected-token-network" name="metapress_nft_contract_list['+this.token_count+'][network]">';
      jQuery.each(metapressjsdata.network_options, function(index, option) {
          new_token_html += '<option value="'+option.slug+'" data-chainid="'+option.chainid+'">'+option.name+'</option>';
      });
     new_token_html += '</select><input class="regular-text set-network-chainid" name="metapress_nft_contract_list['+this.token_count+'][chainid]" type="hidden" value="0x1" required readonly /><input class="regular-text set-network-name" name="metapress_nft_contract_list['+this.token_count+'][networkname]" type="hidden" value="Ethereum Mainnet" required readonly /></div></div><div class="metapress-grid metapress-wallet metapress-setting"><div class="metapress-setting-title">Token Type</div><div class="metapress-setting-content"><select name="metapress_nft_contract_list['+this.token_count+'][token_type]">';

     jQuery.each(metapressjsdata.token_options, function(index, option) {
         new_token_html += '<option value="'+option.type+'">'+option.name+'</option>';
     });

     new_token_html += '</select></div></div><div class="metapress-grid metapress-wallet metapress-setting"><div class="metapress-setting-title">Enabled</div><div class="metapress-setting-content"><input name="metapress_nft_contract_list['+this.token_count+'][enabled]" type="checkbox" value="1" /><span>Enable or Disable this smart contract</span></div></div><div class="metapress-grid metapress-wallet metapress-setting"><div class="metapress-setting-title">Remove</div><div class="metapress-setting-content"><label class="remove-custom-token button">Delete Contract</label></div></div></div>';
      jQuery('#live-metapress-tokens').append(new_token_html);
      this.increase_count();
  }
}
const metapress_admin_nft_manager = new MetaPress_Admin_NFT_Manager();
jQuery(document).ready(function() {
    metapress_admin_nft_manager.set_mode();
    jQuery('#add-new-token').click( function() {
        metapress_admin_nft_manager.add_new_token();
    });

    jQuery('body').delegate('.remove-custom-token', 'click', function() {
        if( window.confirm('Are you sure you want to Delete this custom token?') ) {
            let token_fields = jQuery(this).parents('.metapress-admin-settings');
            token_fields.remove();
        }
    });

    jQuery('body').delegate('.selected-token-network', 'change', function() {
        var set_network_name = jQuery(this).find(':selected').text();
        var set_network_chainid = jQuery(this).find(':selected').data('chainid');
        jQuery(this).parent().find('.set-network-name').val(set_network_name);
        jQuery(this).parent().find('.set-network-chainid').val(set_network_chainid);
    });


});
