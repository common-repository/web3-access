class MetaPress_Plugin_User_Subscriptions_Loader {

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

    request_subscriptions() {
        if( web3_access_wallet_manager.getWalletAddress() ) {
            if(this.offset <= 0) {
                this.offset = 0;
                jQuery('.metapress-nav-button.prev .metapress-button').removeClass('show');
            }
            metapress_show_ajax_updating('Loading your subscriptions...');
            var metapress_requester = this;
            jQuery('#metapress-user-subscriptions').html('');
            const wallet_address =  web3_access_wallet_manager.getWalletAddress();
            var metapress_subscriptions_request_url = metapressjsdata.api_url;

            metapress_subscriptions_request_url = this.set_request_param(metapress_subscriptions_request_url, 'per_page', this.per_page);
            if( this.offset && this.offset > 0 ) {
                metapress_subscriptions_request_url = this.set_request_param(metapress_subscriptions_request_url, 'offset', this.offset);
            }
            metapress_subscriptions_request_url = this.set_request_param(metapress_subscriptions_request_url, 'mpwalletaddress', wallet_address);
            metapress_subscriptions_request_url = this.set_request_param(metapress_subscriptions_request_url, 'request_key', metapressmanagerrequests.api.request_key);
            jQuery.ajax({
                url: metapress_subscriptions_request_url,
                type: "GET",
                success:function(response) {
                    jQuery('#metapress-updating-box').removeClass('show-overlay-box');
                    let metapress_subscription_data = response;
                    if(metapress_subscription_data.count > 0 && metapress_subscription_data.subscriptions) {
                        var metapress_subscriptions = metapress_subscription_data.subscriptions;
                        jQuery.each(metapress_subscriptions, function(index, subscription) {
                            let subscription_html = '<div class="metapress-transaction metapress-border-box" data-payment="'+subscription.id+'"><div class="metapress-grid metapress-subscription"><div class="metapress-setting-title">'+metapressmanagerrequests.payments.product_title+'</div><div class="metapress-setting-content">'+subscription.product_name+'</div><div class="metapress-setting-title">'+metapressmanagerrequests.subscriptions.wallet_address+'</div><div class="metapress-setting-content"><input type="text" value="'+subscription.payment_owner+'" readonly onClick="this.select();" /></div><div class="metapress-setting-title">'+metapressmanagerrequests.subscriptions.reminder_email+'</div><div class="metapress-setting-content"><div class="metapress-input-update-wrapper"><input type="text" value="'+subscription.notification_email+'" class="metapress-change-notification-email" /><div class="metapress-update-subscription" data-subscription="'+subscription.id+'"><span class="dashicons dashicons-yes-alt"></span></div></div></div><div class="metapress-setting-title">'+metapressmanagerrequests.subscriptions.price_title+'</div><div class="metapress-setting-content">'+subscription.price+'</div><div class="metapress-setting-title">'+metapressmanagerrequests.payments.paid_with+'</div><div class="metapress-setting-content">'+subscription.payment_method+'</div><div class="metapress-setting-title">'+metapressmanagerrequests.subscriptions.expires_title+'</div><div class="metapress-setting-content">'+subscription.expires+'</div><div class="metapress-setting-title">'+metapressmanagerrequests.payments.status_title+'</div><div class="metapress-setting-content"><div class="metapress-input-update-wrapper">';
                            subscription_html += '<div class="metapress-subscription-status">';
                            if( subscription.status == 'Expired' ) {
                                subscription_html += '<a href="'+subscription.renew_url+'">'+metapressmanagerrequests.subscriptions.renew_now+'</a>';
                            } else {
                                subscription_html += subscription.status;
                            }
                            subscription_html += '</div><div class="metapress-delete-subscription" data-subscription="'+subscription.id+'"><span class="dashicons dashicons-dismiss"></span></div></div></div></div></div>';
                            jQuery('#metapress-user-subscriptions').append(subscription_html);
                        });

                        if(metapress_subscription_data.count < 50) {
                            jQuery('.metapress-nav-button.next .metapress-button').removeClass('show');
                        } else {
                            jQuery('.metapress-nav-button.next .metapress-button').addClass('show');
                        }
                        if(metapress_requester.offset > 0) {
                            jQuery('.metapress-nav-button.prev .metapress-button').addClass('show');
                        }
                    } else {
                        jQuery('#metapress-user-subscriptions').append('<div id="metapress-no-payments-found" class="metapress-align-content-center">'+metapressmanagerrequests.subscriptions.no_subscriptions+'</div>');
                        jQuery('.metapress-nav-button.next .metapress-button, .metapress-nav-button.prev .metapress-button').removeClass('show');
                    }
                },
                error: function(error){
                    metapress_show_ajax_error(error.responseText);
                }
            });
        }
    }

    delete_subscription(subscription_id, subscription_element) {
        if( web3_access_wallet_manager.getWalletAddress() ) {

            const wallet_address =  web3_access_wallet_manager.getWalletAddress();
            metapress_show_ajax_updating('Deleting subscription...');
            var metapress_requester = this;
            var metapress_subscriptions_request_url = metapressjsdata.deletesubscription;

            metapress_subscriptions_request_url = this.set_request_param(metapress_subscriptions_request_url, 'subscription_id', subscription_id);
            metapress_subscriptions_request_url = this.set_request_param(metapress_subscriptions_request_url, 'mpwalletaddress', wallet_address);
            jQuery.ajax({
                url: metapress_subscriptions_request_url,
                type: 'POST',
                beforeSend: function ( xhr ) {
                    xhr.setRequestHeader( 'X-WP-Nonce', metapressmanagerrequests.user.nonce );
                    xhr.setRequestHeader( 'X-METAPRESS', metapressmanagerrequests.api.request_key );
                },
                success:function(response) {
                    jQuery('#metapress-updating-box').removeClass('show-overlay-box');
                    let metapress_subscription_data = response;
                    if(metapress_subscription_data.deleted) {
                        subscription_element.remove();
                    }
                },
                error: function(error){
                    metapress_show_ajax_error(error.responseText);
                }
            });
        }
    }

    update_notification_email(subscription_id, notification_email) {
        if( web3_access_wallet_manager.getWalletAddress() ) {

            const wallet_address =  web3_access_wallet_manager.getWalletAddress();
            metapress_show_ajax_updating('Saving changes...');
            var metapress_requester = this;
            var metapress_subscriptions_request_url = metapressjsdata.notificationemail;

            metapress_subscriptions_request_url = this.set_request_param(metapress_subscriptions_request_url, 'subscription_id', subscription_id);
            metapress_subscriptions_request_url = this.set_request_param(metapress_subscriptions_request_url, 'new_email_address', notification_email);
            metapress_subscriptions_request_url = this.set_request_param(metapress_subscriptions_request_url, 'mpwalletaddress', wallet_address);
            jQuery.ajax({
                url: metapress_subscriptions_request_url,
                type: 'POST',
                beforeSend: function ( xhr ) {
                    xhr.setRequestHeader( 'X-WP-Nonce', metapressmanagerrequests.user.nonce );
                    xhr.setRequestHeader( 'X-METAPRESS', metapressmanagerrequests.api.request_key );
                },
                success:function(response) {
                    jQuery('#metapress-updating-box').removeClass('show-overlay-box');
                    let metapress_subscription_data = response;
                    if(metapress_subscription_data.updated) {
                        jQuery('.metapress-update-subscription').removeClass('show');
                    }
                },
                error: function(error){
                    metapress_show_ajax_error(error.responseText);
                }
            });
        }
    }
}

metapress_plugin_user_subscriptions_loader = new MetaPress_Plugin_User_Subscriptions_Loader();
jQuery(document).on('metapressWalletAccountReady', function() {
    let metapress_email_notification_timeout = null;
    if( web3_access_wallet_manager.getWalletAddress() ) {
        metapress_plugin_user_subscriptions_loader.request_subscriptions();

        jQuery('.metapress-nav-button.next .metapress-button').click( function() {
            metapress_plugin_user_subscriptions_loader.offset += 50;
            metapress_plugin_user_subscriptions_loader.request_subscriptions();
        });

        jQuery('.metapress-nav-button.prev .metapress-button').click( function() {
            metapress_plugin_user_subscriptions_loader.offset -= 50;
            metapress_plugin_user_subscriptions_loader.request_subscriptions();
        });

        jQuery('body').delegate('.metapress-delete-subscription', 'click', function() {
          let price_input = jQuery(this);
          let subscription_id = price_input.data('subscription');
          let subscription_element = price_input.parents('.metapress-transaction');
          metapress_price_update_delay = setTimeout( function() {
              if( window.confirm('Are you sure you want to delete this subscription?' ) ) {
                  metapress_plugin_user_subscriptions_loader.delete_subscription(subscription_id, subscription_element);
              }
          }, 100);
        });

        jQuery('body').delegate('.metapress-change-notification-email', 'keyup', function(e) {
            if( metapress_email_notification_timeout ) {
                clearTimeout(metapress_email_notification_timeout);
            }
            let email_input = jQuery(e.target);
            metapress_email_notification_timeout = setTimeout( function() {
                email_input.next('.metapress-update-subscription').addClass('show');
            }, 500);
        });

        jQuery('body').delegate('.metapress-update-subscription', 'click', function() {
            let update_button = jQuery(this);
            let subscription_id = update_button.data('subscription');
            let notifictaion_email = update_button.prev('input.metapress-change-notification-email').val();
            metapress_plugin_user_subscriptions_loader.update_notification_email(subscription_id, notifictaion_email);
        });
    }
});
