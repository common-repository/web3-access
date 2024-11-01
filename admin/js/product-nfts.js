class MetaPress_Admin_Product_NFT_Manager {
    constructor() {
        this.token_count = 0;
    }

    set_mode() {
        this.token_count = jQuery('.metapress-nft-token').length;
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

    add_new_token(contract_name, contract_address, token_type, network, networkname, chainid) {
      let new_token_html = '<div class="rogue-meta-box metapress-nft-token"><div class="metapress-nft-info-box"><label>Contract Name</label><input type="text" name="metapress_nft_access_list['+this.token_count+'][name]" value="'+contract_name+'" readonly /></div><div class="metapress-nft-info-box"><label>Contract Address</label><input type="text" name="metapress_nft_access_list['+this.token_count+'][contract_address]" value="'+contract_address+'" readonly /></div><div class="metapress-nft-info-box"><label>Token ID</label><input type="text" name="metapress_nft_access_list['+this.token_count+'][token_id]" value="" /><div class="metapress-asset-tooltip"><span class="dashicons dashicons-editor-help metapress-tooltip"></span><div class="metapress-asset-tooltip-content">Leave this blank for ERC-20 tokens or to allow ownership verification for ANY asset within an ERC-721 Collection. (Required for ERC-1155)</div></div></div>';


      new_token_html += '<div class="metapress-nft-info-box"><label>Minimum Balance</label><input type="number" min="1" name="metapress_nft_access_list['+this.token_count+'][minimum_balance]" value="1" /><div class="metapress-asset-tooltip"><span class="dashicons dashicons-editor-help metapress-tooltip"></span><div class="metapress-asset-tooltip-content">Minimum number of tokens required for access to be valid. For example, require the wallet to own at least 2 NFTs OR have a minimum of 5000 coins within a contract.</div></div></div>';

      new_token_html += '<div class="metapress-nft-info-box"><label>Token Name</label><input type="text" name="metapress_nft_access_list['+this.token_count+'][token_name]" value="" /></div><div class="metapress-nft-info-box"><label>Token Image URL</label><input type="url" class="icon-image-url" name="metapress_nft_access_list['+this.token_count+'][token_image]" value="" /> <span class="upload-icon-image button">Choose Image</span></div><div class="metapress-nft-info-box"><label>Purchase URL</label><input type="url" class="icon-image-url" name="metapress_nft_access_list['+this.token_count+'][token_url]" value="" /><div class="metapress-asset-tooltip"><span class="dashicons dashicons-editor-help metapress-tooltip"></span><div class="metapress-asset-tooltip-content">Where can visitors purchase or view the token(s).</div></div></div><div class="metapress-nft-info-box"><label>OpenSea Collection Slug</label><input type="text" name="metapress_nft_access_list['+this.token_count+'][collection_slug]" value="" /><div class="metapress-asset-tooltip"><span class="dashicons dashicons-editor-help metapress-tooltip"></span><div class="metapress-asset-tooltip-content">This is only required if you are using a shared OpenSea contract address. Do not fill this in if you do not have an OpenSea API key. <strong>Case sensitive and must match the collection slug exactly.</strong></div></div></div>';

      new_token_html += '<div class="metapress-nft-info-box"><label>Delete</label><label class="remove-nft-asset button">Delete Token</label></div><input type="hidden" name="metapress_nft_access_list['+this.token_count+'][token_type]" value="'+token_type+'" readonly/><input type="hidden" name="metapress_nft_access_list['+this.token_count+'][network]" value="'+network+'" readonly/><input type="hidden" name="metapress_nft_access_list['+this.token_count+'][networkname]" value="'+networkname+'" readonly/><input type="hidden" name="metapress_nft_access_list['+this.token_count+'][chainid]" value="'+chainid+'" readonly/></div>';

      jQuery('#metapress-nft-token-list').append(new_token_html);
      this.increase_count();
  }
}
const metapress_admin_product_nft_manager = new MetaPress_Admin_Product_NFT_Manager();
jQuery(document).ready(function() {
    metapress_admin_product_nft_manager.set_mode();
    jQuery('.add-new-nft').click( function() {
        let contract_name = jQuery(this).data('name');
        let contract_address = jQuery(this).data('contract');
        let token_type = jQuery(this).data('token-type');
        let network = jQuery(this).data('network');
        let networkname = jQuery(this).data('networkname');
        let chainid = jQuery(this).data('chainid');
        metapress_admin_product_nft_manager.add_new_token(contract_name, contract_address, token_type, network, networkname, chainid);
        jQuery('#metapress-no-nfts-added').hide();
    });

    jQuery('body').delegate('.remove-nft-asset', 'click', function() {
        if( window.confirm('Are you sure you want to Delete this NFT asset from the list?') ) {
            let token_fields = jQuery(this).parents('.metapress-nft-token');
            token_fields.remove();
        }

    });

});
