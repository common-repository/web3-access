class MetaPress_Plugin_User_Transactions_Loader {

    constructor() {
        this.offset = 0;
        this.per_page = 50;
    }

    set_request_param(uri, key, value) {
        var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        var separator = uri.indexOf('?') !== -1 ? "&" : "?";
        if (uri.match(re)) {
            return uri.replace(re, '$1' + key + "=" + value + '$2');
        } else {
            return uri + separator + key + "=" + value;
        }
    }

    request_transactions() {
        if( web3_access_wallet_manager.getWalletAddress() ) {
            if(this.offset <= 0) {
                this.offset = 0;
                jQuery('.metapress-nav-button.prev .metapress-button').removeClass('show');
            }
            metapress_show_ajax_updating('Loading your transactions...');
            var metapress_requester = this;
            jQuery('#metapress-user-transactions').html('');
            const wallet_address =  web3_access_wallet_manager.getWalletAddress();
            var metapress_transactions_request_url = metapressjsdata.api_url;

            metapress_transactions_request_url = this.set_request_param(metapress_transactions_request_url, 'per_page', this.per_page);
            if( this.offset && this.offset > 0 ) {
                metapress_transactions_request_url = this.set_request_param(metapress_transactions_request_url, 'offset', this.offset);
            }
            metapress_transactions_request_url = this.set_request_param(metapress_transactions_request_url, 'mpwalletaddress', wallet_address);
            metapress_transactions_request_url = this.set_request_param(metapress_transactions_request_url, 'request_key', metapressmanagerrequests.api.request_key);
            jQuery.ajax({
                url: metapress_transactions_request_url,
                type: "GET",
                success:function(response) {
                    jQuery('#metapress-updating-box').removeClass('show-overlay-box');
                    let metapress_payment_data = response;
                    if(metapress_payment_data.count > 0 && metapress_payment_data.payments) {
                        var metapress_payments = metapress_payment_data.payments;
                        jQuery.each(metapress_payments, function(index, payment) {
                            var payment_viewing_url = "N/A"
                            if(payment.view_url != "") {
                                payment_viewing_url = '<a href="'+payment.view_url+'" target="_blank">'+metapressmanagerrequests.payments.view_on_network+'</a>';
                            }
                            let payment_html = '<div class="metapress-transaction metapress-border-box" data-payment="'+payment.id+'"><div class="metapress-grid metapress-payment"><div class="metapress-setting-title mobile-only">'+metapressmanagerrequests.payments.product_title+'</div><div class="metapress-setting-content"><div class="metapress-get-product-content" data-product="'+payment.product_id+'">'+payment.product_name+'</div></div><div class="metapress-setting-title mobile-only">'+metapressmanagerrequests.payments.paid_with+'</div><div class="metapress-setting-content">'+payment.token+'</div><div class="metapress-setting-title mobile-only">'+metapressmanagerrequests.payments.amount_title+'</div><div class="metapress-setting-content">'+payment.sent_amount+'</div><div class="metapress-setting-title mobile-only">'+metapressmanagerrequests.payments.network_title+'</div><div class="metapress-setting-content">'+payment.network+'</div><div class="metapress-setting-title mobile-only">'+metapressmanagerrequests.payments.date_title+'</div><div class="metapress-setting-content">'+payment.transaction_time+'</div><div class="metapress-setting-title mobile-only">'+metapressmanagerrequests.payments.status_title+'</div><div class="metapress-setting-content">'+payment.transaction_status+'</div><div class="metapress-setting-title mobile-only">'+metapressmanagerrequests.payments.view_title+'</div><div class="metapress-setting-content"><a href="'+payment.view_url+'" target="_blank">'+metapressmanagerrequests.payments.view_on_network+'</a></div></div><div class="metapress-transaction-content metapress-border-box"><h6>'+metapressmanagerrequests.payments.access_item_list_heading+'</h6><div class="metapress-transaction-content-items metapress-border-box"></div></div></div>';
                            jQuery('#metapress-user-transactions').append(payment_html);
                        });

                        if(metapress_payment_data.count < 50) {
                            jQuery('.metapress-nav-button.next .metapress-button').removeClass('show');
                        } else {
                            jQuery('.metapress-nav-button.next .metapress-button').addClass('show');
                        }
                        if(metapress_requester.offset > 0) {
                            jQuery('.metapress-nav-button.prev .metapress-button').addClass('show');
                        }
                    } else {
                        jQuery('#metapress-user-transactions').append('<div id="metapress-no-payments-found" class="metapress-align-content-center">'+metapressmanagerrequests.payments.no_payments+'</div>');
                        jQuery('.metapress-nav-button.next .metapress-button, .metapress-nav-button.prev .metapress-button').removeClass('show');
                    }
                },
                error: function(error){
                    metapress_show_ajax_error(error.responseText);
                }
            });
        }
    }

    request_product_data(product_id, append_content) {
        if( web3_access_wallet_manager.getWalletAddress() ) {

            const wallet_address =  web3_access_wallet_manager.getWalletAddress();
            metapress_show_ajax_updating('Getting content...');
            var metapress_requester = this;
            var metapress_transactions_request_url = metapressjsdata.product_data_url;

            metapress_transactions_request_url = this.set_request_param(metapress_transactions_request_url, 'productid', product_id);
            metapress_transactions_request_url = this.set_request_param(metapress_transactions_request_url, 'mpwalletaddress', wallet_address);
            metapress_transactions_request_url = this.set_request_param(metapress_transactions_request_url, 'request_key', metapressmanagerrequests.api.request_key);
            jQuery.ajax({
                url: metapress_transactions_request_url,
                type: "GET",
                success:function(response) {
                    jQuery('#metapress-updating-box').removeClass('show-overlay-box');
                    let metapress_product_data = response;
                    let append_items_box = append_content.find('.metapress-transaction-content-items');
                    if(metapress_product_data.has_access && metapress_product_data.content) {
                        if( append_items_box.html() == "" ) {
                          var metapress_items = metapress_product_data.content;
                          jQuery.each(metapress_items, function(index, item) {
                              let metapress_item_html = '<div class="metapress-item metapress-border-box" data-id="'+item.id+'"><a href="'+item.link+'">'+item.name+'</a></div>';
                              append_items_box.append(metapress_item_html);
                          });
                        }
                    } else {
                      append_items_box.html('<div class="metapress-no-access-found metapress-align-content-center">'+metapressmanagerrequests.payments.no_access+'</div>');
                    }
                    append_content.addClass('show');
                },
                error: function(error){
                    metapress_show_ajax_error(error.responseText);
                }
            });
        }
    }
}

metapress_plugin_user_transactions_loader = new MetaPress_Plugin_User_Transactions_Loader();
jQuery(document).on('metapressWalletAccountReady', function() {
    if( web3_access_wallet_manager.getWalletAddress() ) {
        metapress_plugin_user_transactions_loader.request_transactions();

        jQuery('.metapress-nav-button.next .metapress-button').click( function() {
            metapress_plugin_user_transactions_loader.offset += 50;
            metapress_plugin_user_transactions_loader.request_transactions();
        });

        jQuery('.metapress-nav-button.prev .metapress-button').click( function() {
            metapress_plugin_user_transactions_loader.offset -= 50;
            metapress_plugin_user_transactions_loader.request_transactions();
        });

        jQuery('body').delegate('.metapress-get-product-content', 'click', function() {
          var append_content_box = jQuery(this).parents('.metapress-transaction').find('.metapress-transaction-content');
          if( ! append_content_box.hasClass('show') ) {
            metapress_plugin_user_transactions_loader.request_product_data(jQuery(this).data('product'), append_content_box);
          }
        });
    }
});
