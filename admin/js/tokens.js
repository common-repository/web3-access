class MetaPress_Admin_Token_Manager {
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
      let new_token_html = '<div class="metapress-admin-settings metapress-border-box live-token"><div class="metapress-grid metapress-wallet metapress-setting"><div class="metapress-setting-title">Token Contract Address</div><div class="metapress-setting-content"><input class="regular-text token-contract-address" name="metapress_custom_tokens_list['+this.token_count+'][contract_address]" type="text" value="" required/><label class="fetch-coin-data button">Fetch Coin Info</label></div></div><div class="metapress-grid metapress-wallet metapress-setting"><div class="metapress-setting-title">TEST Token Contract Address</div><div class="metapress-setting-content"><input class="regular-text" name="metapress_custom_tokens_list['+this.token_count+'][test_contract_address]" type="text" value=""/></div></div><div class="metapress-grid metapress-wallet metapress-setting"><div class="metapress-setting-title">Currency Symbol</div><div class="metapress-setting-content"><input class="regular-text token-contract-symbol" name="metapress_custom_tokens_list['+this.token_count+'][currency_symbol]" type="text" value="" required/></div></div><div class="metapress-grid metapress-wallet metapress-setting"><div class="metapress-setting-title">Name</div><div class="metapress-setting-content"><input class="regular-text token-contract-name" name="metapress_custom_tokens_list['+this.token_count+'][name]" type="text" value="" required/></div></div><div class="metapress-grid metapress-wallet metapress-setting"><div class="metapress-setting-title">Network</div><div class="metapress-setting-content"><select class="selected-token-network" name="metapress_custom_tokens_list['+this.token_count+'][network]">';

      jQuery.each(metapressjsdata.network_options, function(index, option) {
          if( option.enabled ) {
              new_token_html += '<option value="'+option.slug+'" data-chainid="'+option.chainid+'" data-explorer="'+option.explorer+'">'+option.name+'</option>';
          }
      });

      new_token_html += '</select><input class="regular-text set-network-chainid" name="metapress_custom_tokens_list['+this.token_count+'][chainid]" type="hidden" value="0x1" required readonly /><input class="regular-text set-network-name" name="metapress_custom_tokens_list['+this.token_count+'][networkname]" type="hidden" value="Ethereum Mainnet" required readonly /><input class="regular-text set-network-explorer" name="metapress_custom_tokens_list['+this.token_count+'][explorer]" type="hidden" value="https://etherscan.io/" required readonly /></div></div><div class="metapress-grid metapress-wallet metapress-setting"><div class="metapress-setting-title">TEST Network</div><div class="metapress-setting-content"><select class="selected-token-test-network" name="metapress_custom_tokens_list['+this.token_count+'][test_network]">';

      jQuery.each(metapressjsdata.test_network_options, function(index, option) {
          if( option.enabled ) {
              new_token_html += '<option value="'+option.slug+'" data-chainid="'+option.chainid+'" data-explorer="'+option.explorer+'">'+option.name+'</option>';
          }
      });

      new_token_html += '<input class="regular-text set-network-chainid" name="metapress_custom_tokens_list['+this.token_count+'][test_chainid]" type="hidden" value="0xaa36a7" required readonly /><input class="regular-text set-network-name" name="metapress_custom_tokens_list['+this.token_count+'][test_networkname]" type="hidden" value="Ethereum Sepolia" required readonly /><input class="regular-text set-network-explorer" name="metapress_custom_tokens_list['+this.token_count+'][test_explorer]" type="hidden" value="https://sepolia.etherscan.io/" required readonly /></div></div>';

      new_token_html += '<div class="metapress-grid metapress-wallet metapress-setting"><div class="metapress-setting-title">Price (USD)</div><div class="metapress-setting-content"><input class="regular-text token-usd-price" name="metapress_custom_tokens_list['+this.token_count+'][usd_price]" type="number" min="0" step="0.01" value="" /><br><br><p class="description"><strong>If the Binance / CoinGecko buttons below cannot find a price, or finds an incorrect price, you need to disable both automatic price conversions and manually set a price in USD.</strong></p></div></div><div class="metapress-grid metapress-wallet metapress-setting"><div class="metapress-setting-title">Binance Price Conversion</div><div class="metapress-setting-content"><input name="metapress_custom_tokens_list['+this.token_count+'][binance_price_api]" type="checkbox" value="1" /><span>Enabling this will automatically convert Web3 Product USD prices to token amounts at the time of transaction using the Binance API.</span><br><br><label class="fetch-coin-binance-price button">Check Binance Price</label></div></div><div class="metapress-grid metapress-wallet metapress-setting"><div class="metapress-setting-title">CoinGecko Price Conversion</div><div class="metapress-setting-content"><input name="metapress_custom_tokens_list['+this.token_count+'][coingecko_price_api]" type="checkbox" value="1" /><span>Enabling this will automatically convert Web3 Product USD prices to token amounts at the time of transaction using the CoinGecko API</span><br><br><label class="fetch-coin-coingecko-price button">Check CoinGecko Price</label></div></div>';

      new_token_html += '<div class="metapress-grid metapress-wallet metapress-setting"><div class="metapress-setting-title">Icon</div><div class="metapress-setting-content"><input class="regular-text icon-image-url" name="metapress_custom_tokens_list['+this.token_count+'][icon]" type="text" value="" required/><label class="upload-icon-image button">Upload</label></div></div><div class="metapress-grid metapress-wallet metapress-setting"><div class="metapress-setting-title">Your Wallet Address</div><div class="metapress-setting-content"><input class="regular-text" name="metapress_custom_tokens_list['+this.token_count+'][address]" type="text" value="" /></div></div><div class="metapress-grid metapress-wallet metapress-setting"><div class="metapress-setting-title">Enabled</div><div class="metapress-setting-content"><input name="metapress_custom_tokens_list['+this.token_count+'][enabled]" type="checkbox" value="1" /><span>Enable or Disable this custom token</span></div></div><div class="metapress-grid metapress-wallet metapress-setting"><div class="metapress-setting-title">Remove</div><div class="metapress-setting-content"><label class="remove-custom-token button">Delete Token</label></div></div></div>';
      jQuery('#live-metapress-tokens').append(new_token_html);
      this.increase_count();
  }

    async fetch_token_data(network, token_address) {
        if( network == "" || token_address == "" ) {
            alert('Please fill in the Token Contract Address field');
            return 0;
        }

        if( network == 'mainnet' || network == 'sepolia' ) {
            let contract_info_url = 'https://api.coingecko.com/api/v3/coins/ethereum/contract/'+token_address;
            let token_data = await jQuery.get(contract_info_url).fail( function() {
                alert(metapressadminmanagerrequests.tokens.contract_not_found);
            });
            if( token_data.id ) {
                return token_data;
            } else {
              return 0;
            }
        }

        if( network == 'maticmainnet' || network == 'matictestnet' ) {
            let contract_info_url = 'https://api.coingecko.com/api/v3/coins/polygon-pos/contract/'+token_address;
            let token_data = await jQuery.get(contract_info_url).fail( function() {
              alert(metapressadminmanagerrequests.tokens.contract_not_found);
            });
            if( token_data.id ) {
              return token_data;
            } else {
            return 0;
            }
        }

        if( network == 'binancesmartchain' || network == 'binancetestnet' ) {
            let contract_info_url = 'https://api.coingecko.com/api/v3/coins/binance-smart-chain/contract/'+token_address;
            let token_data = await jQuery.get(contract_info_url).fail( function() {
              alert(metapressadminmanagerrequests.tokens.contract_not_found);
            });
            if( token_data.id ) {
              return token_data;
            } else {
            return 0;
            }
        }

        if( network == 'avaxmainnet' || network == 'avaxtestnet' ) {
            let contract_info_url = 'https://api.coingecko.com/api/v3/coins/avalanche/contract/'+token_address;
            let token_data = await jQuery.get(contract_info_url).fail( function() {
              alert(metapressadminmanagerrequests.tokens.contract_not_found);
            });
            if( token_data.id ) {
              return token_data;
            } else {
            return 0;
            }
        }

        if( network == 'fantomnetwork' || network == 'fantomtestnet' ) {
            let contract_info_url = 'https://api.coingecko.com/api/v3/coins/fantom/contract/'+token_address;
            let token_data = await jQuery.get(contract_info_url).fail( function() {
              alert(metapressadminmanagerrequests.tokens.contract_not_found);
            });
            if( token_data.id ) {
              return token_data;
            } else {
            return 0;
            }
        }

    }

}
const metapress_admin_token_manager = new MetaPress_Admin_Token_Manager();
jQuery(document).ready(function() {
    metapress_admin_token_manager.set_mode();
    jQuery('#add-new-token').click( function() {
        metapress_admin_token_manager.add_new_token();
    });

    jQuery('body').delegate('.fetch-coin-data', 'click', function() {
        let token_fields = jQuery(this).parents('.metapress-admin-settings');
        let token_address = token_fields.find('input.token-contract-address').val();
        let network = token_fields.find('.selected-token-network').val();
        metapress_admin_token_manager.fetch_token_data(network, token_address).then((token_data) => {
          if( token_data && token_data.id ) {
              token_fields.find('input.token-contract-symbol').val(token_data.symbol.toUpperCase());
              token_fields.find('input.token-contract-name').val(token_data.name);
              if( token_data.image ) {
                  if( token_data.image.thumb ) {
                      token_fields.find('input.icon-image-url').val(token_data.image.thumb);
                  }
                  if( token_data.image.small ) {
                      token_fields.find('input.icon-image-url').val(token_data.image.small);
                  }
                  if( token_data.image.large ) {
                      token_fields.find('input.icon-image-url').val(token_data.image.large);
                  }
              }
          } else {
              alert(metapressadminmanagerrequests.tokens.contract_not_found);
          }
        });
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
        var set_network_explorer = jQuery(this).find(':selected').data('explorer');
        jQuery(this).parent().find('.set-network-name').val(set_network_name);
        jQuery(this).parent().find('.set-network-chainid').val(set_network_chainid);
        jQuery(this).parent().find('.set-network-explorer').val(set_network_explorer);
    });

    jQuery('body').delegate('.selected-token-test-network', 'change', function() {
        var set_network_name = jQuery(this).find(':selected').text();
        var set_network_chainid = jQuery(this).find(':selected').data('chainid');
        var set_network_explorer = jQuery(this).find(':selected').data('explorer');
        jQuery(this).parent().find('.set-network-name').val(set_network_name);
        jQuery(this).parent().find('.set-network-chainid').val(set_network_chainid);
        jQuery(this).parent().find('.set-network-explorer').val(set_network_explorer);
    });

    jQuery('body').delegate('.fetch-coin-binance-price', 'click', function() {
        let token_fields = jQuery(this).parents('.metapress-admin-settings');
        let token_symbol = token_fields.find('input.token-contract-symbol').val();
        if( token_symbol != "" ) {
            metapress_token_ratio_manager.get_binance_token_ratio(token_symbol).then((ratio) => {
                if( ratio > 0 ) {
                    let token_price = 1 / ratio;
                    token_fields.find('input.token-usd-price').val(token_price);
                } else {
                    alert('Token price not found.');
                }
            }).catch((error) => {
                alert('Token price not found.');
            });
        } else {
              alert('Please fill in the Currency Symbol field.');
        }
    });

    jQuery('body').delegate('.fetch-coin-coingecko-price', 'click', function() {
        let token_fields = jQuery(this).parents('.metapress-admin-settings');
        let token_symbol = token_fields.find('input.token-contract-symbol').val();
        let token_address = token_fields.find('input.token-contract-address').val();
        let token_network = token_fields.find('.selected-token-network').val();
        if( token_symbol != "" && token_address != "" && token_network != "" ) {
            metapress_token_ratio_manager.get_coingecko_token(token_network, token_address, token_symbol).then((ratio) => {
                if( ratio > 0 ) {
                    let token_price = 1 / ratio;
                    token_fields.find('input.token-usd-price').val(token_price);
                } else {
                    metapress_token_ratio_manager.search_coingecko_token(token_symbol).then((ratio) => {
                        if( ratio > 0 ) {
                            let token_price = 1 / ratio;
                            token_fields.find('input.token-usd-price').val(token_price);
                        } else {
                            alert('Token price not found.');
                        }
                    });
                }
            });
        } else {
              alert('Please fill in the Contract Address, Network and Currency Symbol fields.');
        }
    });

});
