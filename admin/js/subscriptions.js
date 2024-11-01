class MetaPress_Admin_Subscriptions_Loading_Manager {
    constructor() {
      this.offset = 0;
      this.status = "";
      this.product_id = "";
      this.from_address = "";
    }

    load_subscriptions() {
      if(this.offset <= 0) {
          this.offset = 0;
          jQuery('.metapress-nav-button.prev .button').hide();
      }
      let metapress_requester = this;
      jQuery('#load-metapress-subscriptions').html('');
      jQuery.ajax({
          url: metapressadminmanagerrequests.ajaxurl,
          data: {
              'action': 'metapress_load_admin_subscriptions_ajax_request',
              'offset': this.offset,
              'status': this.status,
              'product': this.product_id,
              'address': this.from_address,
          },
          success:function(response) {
              let metapress_subscription_data = jQuery.parseJSON(response);
              if(metapress_subscription_data.count > 0 && metapress_subscription_data.subscriptions) {
                  var metapress_subscriptions = metapress_subscription_data.subscriptions;
                  jQuery.each(metapress_subscriptions, function(index, subscription) {

                      let subscription_html = '<div class="metapress-admin-settings metapress-border-box" data-subscription="'+subscription.id+'"><div class="metapress-grid metapress-subscription"><div class="metapress-setting-title mobile-only">'+metapressadminmanagerrequests.payments.product_title+'</div><div class="metapress-setting-content"><a href="'+subscription.product_edit_link+'">'+subscription.product_name+'</a></div><div class="metapress-setting-title mobile-only">'+metapressadminmanagerrequests.payments.from_address+'</div><div class="metapress-setting-content"><input type="text" value="'+subscription.payment_owner+'" readonly onClick="this.select();" /></div><div class="metapress-setting-title mobile-only">'+metapressadminmanagerrequests.subscriptions.price_title+'</div><div class="metapress-setting-content"><div class="metapress-input-update-wrapper"><input type="text" class="metapress-subscription-price" value="'+subscription.price+'" /><div class="metapress-change-subscription-price" data-subscription="'+subscription.id+'"><span class="dashicons dashicons-yes-alt"></span></div></div></div><div class="metapress-setting-title mobile-only">'+metapressadminmanagerrequests.payments.paid_with+'</div><div class="metapress-setting-content">'+subscription.payment_method+'</div><div class="metapress-setting-title mobile-only">'+metapressadminmanagerrequests.subscriptions.expires_title+'</div><div class="metapress-setting-content">'+subscription.expires+'</div><div class="metapress-setting-title mobile-only">'+metapressadminmanagerrequests.payments.status_title+'</div><div class="metapress-setting-content"><div class="metapress-input-update-wrapper"><div class="metapress-subscription-status">'+subscription.status+'</div><div class="metapress-delete-subscription" data-subscription="'+subscription.id+'"><span class="dashicons dashicons-dismiss"></span></div></div></div></div></div>';
                      jQuery('#load-metapress-subscriptions').append(subscription_html);
                  });

                  if(metapress_subscription_data.count < 50) {
                      jQuery('.metapress-nav-button.next .button').hide();
                  } else {
                      jQuery('.metapress-nav-button.next .button').show();
                  }
                  if(metapress_requester.offset > 0) {
                      jQuery('.metapress-nav-button.prev .button').show();
                  }
              } else {
                  jQuery('#load-metapress-subscriptions').html('<div id="metapress-no-payments-found" class="metapress-align-content-center">No More Subscriptions</div>');
                  jQuery('.metapress-nav-button.next .button').hide();
              }
          },
          error: function(response){
                jQuery('#load-metapress-subscriptions').html(response.responseText);
          }
      });
  }

  update_subscription_price(subscription_id, price) {
        let metapress_requester = this;
        jQuery.ajax({
            url: metapressadminmanagerrequests.ajaxurl,
            type: 'POST',
            data: {
                'action': 'metapress_admin_update_subscription_price_ajax_request',
                'subscription_id': subscription_id,
                'price': price,
            },
            success:function(response) {
                let metapress_subscription_data = jQuery.parseJSON(response);
                if(metapress_subscription_data.updated) {

                } else {

                }
            },
            error: function(response){
                  alert(response.responseText);
            }
        });
    }

    delete_subscription(subscription_id, subscription_element) {
        let metapress_requester = this;
        jQuery.ajax({
            url: metapressadminmanagerrequests.ajaxurl,
            type: 'POST',
            data: {
                'action': 'metapress_admin_delete_subscription_ajax_request',
                'subscription_id': subscription_id,
            },
            success:function(response) {
                let metapress_subscription_data = jQuery.parseJSON(response);
                if(metapress_subscription_data.deleted) {
                    subscription_element.remove();
                }
            },
            error: function(response) {
                alert(response.responseText);
            }
        });
    }
}
const metapress_admin_subscriptions_loader = new MetaPress_Admin_Subscriptions_Loading_Manager();
jQuery(document).ready(function() {

    let metapress_search_delay = null;
    let metapress_price_update_delay = null;

    metapress_admin_subscriptions_loader.load_subscriptions();

    jQuery('#metapress-product-filter').keyup( function() {
      if( metapress_search_delay ) {
        clearTimeout(metapress_search_delay);
      }
      metapress_search_delay = setTimeout( function() {
        metapress_admin_subscriptions_loader.product_id = jQuery('#metapress-product-filter').val();
        metapress_admin_subscriptions_loader.offset = 0;
        metapress_admin_subscriptions_loader.load_subscriptions();
      }, 500);
    });

    jQuery('#metapress-address-filter').keyup( function() {
      if( metapress_search_delay ) {
        clearTimeout(metapress_search_delay);
      }
      metapress_search_delay = setTimeout( function() {
        metapress_admin_subscriptions_loader.from_address = jQuery('#metapress-address-filter').val();
        metapress_admin_subscriptions_loader.offset = 0;
        metapress_admin_subscriptions_loader.load_subscriptions();
      }, 500);
    });

    jQuery('.metapress-nav-button.next .button').click( function() {
        metapress_admin_subscriptions_loader.offset += 50;
        metapress_admin_subscriptions_loader.load_subscriptions();
    });

    jQuery('.metapress-nav-button.prev .button').click( function() {
        metapress_admin_subscriptions_loader.offset -= 50;
        metapress_admin_subscriptions_loader.load_subscriptions();
    });

    jQuery('body').delegate('.metapress-change-subscription-price', 'click', function() {
      let price_input = jQuery(this);
      let subscription_id = price_input.data('subscription');
      let new_price = price_input.prev('input.metapress-subscription-price').val();
      metapress_price_update_delay = setTimeout( function() {
          if( window.confirm('Are you sure you want to update this subscription price?' ) ) {
              metapress_admin_subscriptions_loader.update_subscription_price(subscription_id, new_price);
          }

      }, 100);
    });

    jQuery('body').delegate('.metapress-delete-subscription', 'click', function() {
      let price_input = jQuery(this);
      let subscription_id = price_input.data('subscription');
      let subscription_element = price_input.parents('.metapress-admin-settings');
      metapress_price_update_delay = setTimeout( function() {
          if( window.confirm('Are you sure you want to delete this subscription?' ) ) {
              metapress_admin_subscriptions_loader.delete_subscription(subscription_id, subscription_element);
          }
      }, 100);
    });
});
