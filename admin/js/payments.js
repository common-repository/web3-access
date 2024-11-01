class MetaPress_Admin_Payments_Loading_Manager {
    constructor() {
      this.offset = 0;
      this.token = 'ETH';
      this.status = "";
      this.product_id = "";
      this.from_address = "";
      this.from_date = jQuery('#metapress-filter-from-date').val();
      this.to_date = jQuery('#metapress-filter-to-date').val();
    }

    load_payments() {
      if(this.offset <= 0) {
          this.offset = 0;
          jQuery('.metapress-nav-button.prev .button').hide();
      }
      this.from_date = jQuery('#metapress-filter-from-date').val();
      this.to_date = jQuery('#metapress-filter-to-date').val();
      let metapress_requester = this;
      jQuery('#load-metapress-payments').html('');
      jQuery.ajax({
          url: metapressadminmanagerrequests.ajaxurl,
          data: {
              'action': 'metapress_load_admin_payments_ajax_request',
              'offset': this.offset,
              'token': this.token,
              'status': this.status,
              'product': this.product_id,
              'address': this.from_address,
              'from_date': this.from_date,
              'to_date': this.to_date
          },
          success:function(response) {
              let metapress_payment_data = jQuery.parseJSON(response);
              if(metapress_payment_data.count > 0 && metapress_payment_data.payments) {
                  var metapress_payments = metapress_payment_data.payments;
                  jQuery.each(metapress_payments, function(index, payment) {
                      var payment_viewing_url = "N/A"
                      if(payment.view_url != "") {
                          payment_viewing_url = '<a href="'+payment.view_url+'" target="_blank">'+metapressadminmanagerrequests.payments.view_on_network+'</a>';
                      }
                      let payment_html = '<div class="metapress-admin-settings metapress-border-box" data-payment="'+payment.id+'"><div class="metapress-grid metapress-payment"><div class="metapress-setting-title mobile-only">'+metapressadminmanagerrequests.payments.product_title+'</div><div class="metapress-setting-content"><a href="'+payment.product_edit_link+'">'+payment.product_name+'</a></div><div class="metapress-setting-title mobile-only">'+metapressadminmanagerrequests.payments.from_address+'</div><div class="metapress-setting-content"><input type="text" value="'+payment.payment_owner+'" readonly onClick="this.select();" /></div><div class="metapress-setting-title mobile-only">'+metapressadminmanagerrequests.payments.paid_with+'</div><div class="metapress-setting-content">'+payment.token+'</div><div class="metapress-setting-title mobile-only">'+metapressadminmanagerrequests.payments.amount_title+'</div><div class="metapress-setting-content">'+payment.token_amount+'</div><div class="metapress-setting-title mobile-only">'+metapressadminmanagerrequests.payments.network_title+'</div><div class="metapress-setting-content">'+payment.network+'</div><div class="metapress-setting-title mobile-only">'+metapressadminmanagerrequests.payments.date_title+'</div><div class="metapress-setting-content">'+payment.transaction_time+'</div><div class="metapress-setting-title mobile-only">'+metapressadminmanagerrequests.payments.status_title+'</div><div class="metapress-setting-content">'+payment.transaction_status+'</div><div class="metapress-setting-title mobile-only">'+metapressadminmanagerrequests.payments.view_title+'</div><div class="metapress-setting-content"><a href="'+payment.view_url+'" target="_blank">'+metapressadminmanagerrequests.payments.view_on_network+'</a></div></div></div>';
                      jQuery('#load-metapress-payments').append(payment_html);
                  });

                  if(metapress_payment_data.count < 50) {
                      jQuery('.metapress-nav-button.next .button').hide();
                  } else {
                      jQuery('.metapress-nav-button.next .button').show();
                  }
                  if(metapress_requester.offset > 0) {
                      jQuery('.metapress-nav-button.prev .button').show();
                  }
              } else {
                  jQuery('#load-metapress-payments').html('<div id="metapress-no-payments-found" class="metapress-align-content-center">No More Payments</div>');
                  jQuery('.metapress-nav-button.next .button').hide();
              }
              metapress_requester.update_filtering_info();
          },
          error: function(response){
                jQuery('#load-metapress-payments').html(response.responseText);
          }
      });
  }

  load_payments_overview() {
      jQuery.ajax({
          url: metapressadminmanagerrequests.ajaxurl,
          data: {
              'action': 'metapress_load_admin_overview_payments_ajax_request',
              'token': this.token
          },
          success:function(response) {
              var payments_data = jQuery.parseJSON(response);
              jQuery('#metapress-last-week-payments').text(payments_data.last_week);

              jQuery('#metapress-this-month-payments').text(payments_data.this_month);

              jQuery('#metapress-this-year-payments').text(payments_data.this_year);

              jQuery('.metapress-retrieving-payment-data').hide();
              jQuery('.metapress-admin-amount-made').show();
          },
          error: function(response){
              //show_rvs_error(response.responseText);
          }
      });
  }

  update_filtering_info() {
    let new_filtering_text = 'Showing '+this.token+' transactions';
    jQuery('#update-filtering-text').html(new_filtering_text);
  }
}
const metapress_admin_payments_loader = new MetaPress_Admin_Payments_Loading_Manager();
jQuery(document).ready(function() {

    let metapress_search_delay = null;

    metapress_admin_payments_loader.load_payments();
    metapress_admin_payments_loader.load_payments_overview();

    jQuery('#metapress-token-filter').change( function() {
      jQuery('.metapress-admin-amount-made').hide();
      jQuery('.metapress-retrieving-payment-data').show();
      jQuery('.payments-token-label').text(jQuery(this).val());
      metapress_admin_payments_loader.token = jQuery(this).val();
      metapress_admin_payments_loader.offset = 0;
      metapress_admin_payments_loader.load_payments();
      metapress_admin_payments_loader.load_payments_overview();
    });

    jQuery('#metapress-product-filter').keyup( function() {
      if( metapress_search_delay ) {
        clearTimeout(metapress_search_delay);
      }
      metapress_search_delay = setTimeout( function() {
        metapress_admin_payments_loader.product_id = jQuery('#metapress-product-filter').val();
        metapress_admin_payments_loader.offset = 0;
        metapress_admin_payments_loader.load_payments();
      }, 500);
    });

    jQuery('#metapress-address-filter').keyup( function() {
      if( metapress_search_delay ) {
        clearTimeout(metapress_search_delay);
      }
      metapress_search_delay = setTimeout( function() {
        metapress_admin_payments_loader.from_address = jQuery('#metapress-address-filter').val();
        metapress_admin_payments_loader.offset = 0;
        metapress_admin_payments_loader.load_payments();
      }, 500);
    });

    jQuery('.metapress-nav-button.next .button').click( function() {
        metapress_admin_payments_loader.offset += 50;
        metapress_admin_payments_loader.load_payments();
    });

    jQuery('.metapress-nav-button.prev .button').click( function() {
        metapress_admin_payments_loader.offset -= 50;
        metapress_admin_payments_loader.load_payments();
    });

    jQuery('#set-transaction-from-date').datepicker({
        defaultDate: '-1m',
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        dateFormat: 'MM dd, yy',
        onSelect: function(dateText, inst) {
            var set_date = new Date(inst.selectedYear, inst.selectedMonth, inst.selectedDay);
            var transactions_from = set_date.setFullYear(inst.selectedYear);
            jQuery('#set-transaction-to-date').datepicker('option', 'minDate', set_date);
            var transactions_from = transactions_from/1000;
            jQuery('#metapress-filter-from-date').val(transactions_from);
            if( jQuery('#metapress-filter-to-date').val() > transactions_from ) {
              metapress_admin_payments_loader.offset = 0;
              metapress_admin_payments_loader.load_payments();
            }
        }
    });

    jQuery('#set-transaction-to-date').datepicker({
        defaultDate: '+1m',
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,
        dateFormat: 'MM dd, yy',
        onSelect: function(dateText, inst) {
            var set_date = new Date(inst.selectedYear, inst.selectedMonth, inst.selectedDay);
            var transactions_to = set_date.setFullYear(inst.selectedYear);

            jQuery('#set-transaction-from-date').datepicker('option', 'maxDate', set_date);
            var transactions_to = transactions_to/1000;
            jQuery('#metapress-filter-to-date').val(transactions_to);
            if( transactions_to > jQuery('#metapress-filter-from-date').val() ) {
              metapress_admin_payments_loader.offset = 0;
              metapress_admin_payments_loader.load_payments();
            }
        }
    });
});
