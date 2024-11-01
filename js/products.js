class MetaPress_Product_Payments_Manager {
    constructor() {}

    async get_token_ratio(token, token_address, pay_network) {
        let usd_token_pair = token+metapressjsdata.fiat_currency; // default to USD
        let usdt_token_pair = token+'USDT';
        let found_ratio = 0;
        let mp_current_timestamp = (Date.now() - 30000) / 1000;
        mp_current_timestamp = parseInt(mp_current_timestamp);

        if( metapressjsdata.token_ratios ) {
            if( metapressjsdata.token_ratios[usd_token_pair] ) {
                found_ratio = metapressjsdata.token_ratios[usd_token_pair];
            }
            if( metapressjsdata.token_ratios[usdt_token_pair] ) {
                found_ratio = metapressjsdata.token_ratios[usdt_token_pair];
            }
        }


        if( ! metapressjsdata.tokens_updated || (metapressjsdata.tokens_updated < mp_current_timestamp) ) {
            metapress_token_ratio_manager.get_binance_token_ratio(token).then((ratio) => {
                if( ratio > 0 ) {
                    found_ratio = ratio;
                } else {
                    if( token_address ) {
                        metapress_token_ratio_manager.get_coingecko_token(pay_network, token_address, token).then((ratio) => {
                            if( ratio > 0 ) {
                                found_ratio = ratio;
                            } else {
                                metapress_token_ratio_manager.search_coingecko_token(token).then((ratio) => {
                                    found_ratio = ratio;
                                });
                            }
                        });
                    } else {
                        metapress_token_ratio_manager.search_coingecko_token(token).then((ratio) => {
                            found_ratio = ratio;
                        });
                    }
                }
            }).catch((error) => {

            });
        }
        return found_ratio;
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

    confirm_correct_network(token, chainid, network_name) {
        if( chainid != web3_access_wallet_manager.chainId ) {
            var incorrect_network_message = '<p>Incorrect Network. Please change to the '+network_name+' network.';
            if( chainid == '0x89' || chainid == '0x13881' ) {
                incorrect_network_message += '<br><a href="https://docs.matic.network/docs/develop/metamask/config-matic/" target="_blank">'+metapressjsdata.how_to_add_text+'</a>';
            }
            incorrect_network_message += '</p>';
            metapress_show_ajax_error(incorrect_network_message);
            jQuery('.metapress-notice-box').html(incorrect_network_message).show();
            return false;
        } else {
            this.payment_token = token;
            return true;
        }
    }

    verify_product_price(product_id, access_box, token_address, receiving_address) {
        jQuery('.metapress-notice-box').html('').hide();
        jQuery('.metapress-access-buttons').removeClass('show');
        if( product_id && web3_access_wallet_manager.getWalletAddress() ) {
            metapress_show_ajax_updating('Requesting access...');
            let metapress_product_data = this;

            let metapress_verify_price_url = metapressjsdata.endpoints.getprice;
            metapress_verify_price_url = this.set_request_param(metapress_verify_price_url, 'mpwalletaddress', web3_access_wallet_manager.getWalletAddress());
            metapress_verify_price_url = this.set_request_param(metapress_verify_price_url, 'productid', product_id);
            metapress_verify_price_url = this.set_request_param(metapress_verify_price_url, 'request_key', metapressmanagerrequests.api.request_key);

            jQuery.ajax({
                url: metapress_verify_price_url,
                type: 'GET',
                beforeSend: function ( xhr ) {
                    xhr.setRequestHeader( 'X-WP-Nonce', metapressmanagerrequests.user.nonce );
                    xhr.setRequestHeader( 'X-METAPRESS', metapressmanagerrequests.api.request_key );
                },
                success: function(response) {
                    jQuery('#metapress-updating-box').removeClass('show-overlay-box');
                    var product_response = response;
                    if( product_response ) {
                        if( product_response.has_access && product_response.access_token ) {
                            web3_access_wallet_manager.set_token_param(product_response.access_token);
                        } else if( product_response.transaction_hash ) {
                            metapress_product_data.check_transaction_hash_receipt(product_id, access_box, product_response);
                        } else {
                            if( web3_access_wallet_manager.getWalletType() == 'solana' ) {
                                web3_access_solana_loading_manager.newTransaction(product_id, product_response.price, metapress_product_data.payment_token, receiving_address);
                            } else {
                                metapress_metamask_loading_manager.makeContractPayment(product_id, product_response.price, metapress_product_data.payment_token, token_address);
                            }

                        }
                    } else {
                      jQuery('.metapress-access-buttons').addClass('show');
                    }
                },
                error: function(error) {
                    metapress_show_ajax_error(error.responseText);
                    jQuery('.metapress-access-buttons').addClass('show');
                }
            });
        }
    }

    check_current_address_access(product_id, access_box) {
        if( product_id && web3_access_wallet_manager.getWalletAddress() ) {
            metapress_show_ajax_updating('Checking access...');
            let metapress_product_data = this;

            let metapress_verify_price_url = metapressjsdata.endpoints.getprice;

            metapress_verify_price_url = this.set_request_param(metapress_verify_price_url, 'mpwalletaddress', web3_access_wallet_manager.getWalletAddress());
            metapress_verify_price_url = this.set_request_param(metapress_verify_price_url, 'productid', product_id);
            metapress_verify_price_url = this.set_request_param(metapress_verify_price_url, 'request_key', metapressmanagerrequests.api.request_key);

            jQuery.ajax({
                url: metapress_verify_price_url,
                type: 'GET',
                success: function(response) {
                    jQuery('#metapress-updating-box').removeClass('show-overlay-box');
                    var product_response = response;
                    if( product_response ) {
                        if( product_response.has_access && product_response.access_token ) {
                            web3_access_wallet_manager.set_token_param(product_response.access_token);
                        } else {
                            if( product_response.transaction_hash ) {
                                metapress_product_data.check_transaction_hash_receipt(product_id, access_box, product_response);
                            }
                            if( product_response.price ) {
                                metapress_product_data.set_token_price_labels(product_response.price);
                            }
                        }
                    }
                },
                error: function(error) {
                    metapress_show_ajax_error(error.responseText);
                    jQuery('.metapress-access-buttons').addClass('show');
                }
            });
        }
    }

    set_token_price_labels(price) {
        const metapress_product_manager = this;
        jQuery('.metapress-payment-button').each( function() {
            let payment_button = jQuery(this);
            let token = payment_button.data('token');
            let token_address = payment_button.data('address');
            let token_network = payment_button.data('network');
            let token_amount_label = payment_button.find('.metapress-payment-button-amount');
            let network_chainid = payment_button.data('chainid');
            if( network_chainid == web3_access_wallet_manager.chainId ) {
                web3_access_wallet_manager.transaction_viewing_url = payment_button.data('explorer');
            }
            metapress_product_manager.get_token_ratio(token, token_address, token_network).then((ratio) => {
              if( ratio > 0 ) {
                  let token_price = (price * ratio);
                  token_price = token_price.toFixed(6).toString();
                  payment_button.find('.metapress-payment-button-amount').html(token_price+' '+token);
              }
            });
        });
    }

    check_current_address_products_access(products) {
        if( products && jQuery.isArray(products) && web3_access_wallet_manager.getWalletAddress() ) {
            let metapress_product_data = this;

            let metapress_check_access = metapressjsdata.endpoints.access;

            metapress_check_access = this.set_request_param(metapress_check_access, 'mpwalletaddress', web3_access_wallet_manager.getWalletAddress());
            metapress_check_access = this.set_request_param(metapress_check_access, 'products', encodeURIComponent(products));
            metapress_check_access = this.set_request_param(metapress_check_access, 'request_key', metapressmanagerrequests.api.request_key);

            jQuery.ajax({
                url: metapress_check_access,
                type: 'GET',
                success: function(response) {
                    jQuery('#metapress-updating-box').removeClass('show-overlay-box');
                    var product_response = response;
                    if( product_response ) {
                        if( product_response.has_access && product_response.access_token ) {
                            web3_access_wallet_manager.set_token_param(product_response.access_token);
                        } else {
                            if( product_response.transaction_hash ) {
                                let access_box = jQuery('#metapress-single-restricted-content');
                                metapress_product_data.check_transaction_hash_receipt(product_response.product_id, access_box, product_response);
                            }
                        }
                    }
                },
                error: function(error) {
                    metapress_show_ajax_error(error.responseText);
                    jQuery('.metapress-access-buttons').addClass('show');
                }
            });
        }
    }

    check_transaction_hash_receipt(product_id, access_box, transaction) {
        let metapress_box_buttons = access_box.find('.metapress-access-buttons');
        let metapress_notice_box = access_box.find('.metapress-notice-box');
        metapress_show_ajax_updating('Checking transaction status...');

        if( transaction.token == 'SOL' ) {
            if( transaction.transaction_status == 'pending' ) {
                web3_access_solana_loading_manager.get_transaction_status(transaction.transaction_hash).then((txdetails) => {
                    if( txdetails.value.length > 0 ) {
                        if(txdetails.value[0].confirmationStatus == 'confirmed' || txdetails.value[0].confirmationStatus == 'finalized') {
                            this.mark_transaction_as_paid(product_id, transaction.transaction_hash);
                        }
                    }
                });
            }

        } else {
            web3_access_wallet_manager.web3.eth.getTransactionReceipt(transaction.transaction_hash, (err, txReceipt) => {
                jQuery('#metapress-updating-box').removeClass('show-overlay-box');
                if( err ) {
                    metapress_notice_box.html('Error: '+err).show();
                }
                if( web3_access_wallet_manager.transaction_viewing_url == "" && transaction.transaction_url ) {
                    web3_access_wallet_manager.transaction_viewing_url = transaction.transaction_url;
                }
                if( txReceipt && txReceipt.status === true ) {
                    if( transaction.transaction_status == 'pending' ) {
                        this.mark_transaction_as_paid(product_id, transaction.transaction_hash);
                    }

                    if( transaction.transaction_status == 'approval' ) {
                        web3_access_wallet_manager.provider_slug = transaction.network;
                        metapress_box_buttons.removeClass('show');
                        let transaction_amount = transaction.sent_amount.substring(0,8);

                        let transaction_viewing_url = web3_access_wallet_manager.transaction_viewing_url + 'tx/' + transaction.transaction_hash;
                        let pending_transaction_notice = '<p>Your spend approval for '+transaction_amount+' '+transaction.token+' is complete!</p>';
                        pending_transaction_notice += '<span class="metapress-confirm-transaction" data-transaction="'+transaction.id+'" data-product-id="'+product_id+'" data-amount="'+transaction.sent_amount+'" data-token="'+transaction.token+'" data-contract-address="'+transaction.contract_address+'">Confirm Payment</span> <span class="metapress-remove-transaction" data-transaction="'+transaction.transaction_hash+'" data-product-id="'+product_id+'">I need to make a new transaction</span>';
                        metapress_notice_box.html(pending_transaction_notice).show();
                    }

                } else {

                    metapress_box_buttons.removeClass('show');
                    let transaction_viewing_url = web3_access_wallet_manager.transaction_viewing_url + 'tx/' + transaction.transaction_hash;
                    let pending_transaction_notice = '<p>You have a <a href="'+transaction_viewing_url+'" target="_blank">pending transaction</a>! Please check again once your transaction is complete.</p><span class="metapress-remove-transaction" data-transaction="'+transaction.transaction_hash+'" data-product-id="'+product_id+'">I need to make a new transaction</span>';
                    metapress_notice_box.html(pending_transaction_notice).show();
                }
          });
        }

    }

    mark_transaction_as_paid(product_id, transaction_hash) {
        if( product_id && web3_access_wallet_manager.getWalletAddress() ) {
            metapress_show_ajax_updating('Confirming transaction...');

            let metapress_mark_as_paid = metapressjsdata.endpoints.paid;

            metapress_mark_as_paid = this.set_request_param(metapress_mark_as_paid, 'mpwalletaddress', web3_access_wallet_manager.getWalletAddress());
            metapress_mark_as_paid = this.set_request_param(metapress_mark_as_paid, 'transaction_hash', transaction_hash);
            metapress_mark_as_paid = this.set_request_param(metapress_mark_as_paid, 'productid', product_id);
            metapress_mark_as_paid = this.set_request_param(metapress_mark_as_paid, 'request_key', metapressmanagerrequests.api.request_key);

            jQuery.ajax({
                url: metapress_mark_as_paid,
                type: 'POST',
                beforeSend: function ( xhr ) {
                    xhr.setRequestHeader( 'X-WP-Nonce', metapressmanagerrequests.user.nonce );
                    xhr.setRequestHeader( 'X-METAPRESS', metapressmanagerrequests.api.request_key );
                },
                success: function(response) {
                    jQuery('#metapress-updating-box').removeClass('show-overlay-box');
                    var transaction_response = response;
                    if( transaction_response && transaction_response.success && transaction_response.access_token ) {
                        web3_access_wallet_manager.set_token_param(transaction_response.access_token);
                    }
                },
                error: function(error) {
                    metapress_show_ajax_error(error.responseText);
                }
            });
        }
    }

    remove_pending_transaction(product_id, transaction_hash) {
        jQuery('.metapress-notice-box').hide();
        if( product_id && web3_access_wallet_manager.getWalletAddress() ) {
            metapress_show_ajax_updating('removing transaction...');

            let metapress_delete_tx = metapressjsdata.endpoints.deletetx;

            metapress_delete_tx = this.set_request_param(metapress_delete_tx, 'mpwalletaddress', web3_access_wallet_manager.getWalletAddress());
            metapress_delete_tx = this.set_request_param(metapress_delete_tx, 'transaction_hash', transaction_hash);
            metapress_delete_tx = this.set_request_param(metapress_delete_tx, 'productid', product_id);
            metapress_delete_tx = this.set_request_param(metapress_delete_tx, 'request_key', metapressmanagerrequests.api.request_key);

            jQuery.ajax({
                url: metapress_delete_tx,
                type: 'POST',
                success: function(response) {
                    jQuery('#metapress-updating-box').removeClass('show-overlay-box');
                    var transaction_response = response;
                    if( transaction_response && transaction_response.success ) {
                        window.location.reload();
                    } else {
                      jQuery('.metapress-notice-box').show();
                    }
                },
                error: function(error) {
                    metapress_show_ajax_error(error.responseText);
                    jQuery('.metapress-access-buttons').addClass('show');
                }
            });
        }
    }
}

jQuery(document).on('metapressWalletAccountReady', function() {
    if( web3_access_wallet_manager.getWalletAddress() ) {
        const metapress_product_payments_manager = new MetaPress_Product_Payments_Manager();

        if( jQuery('.metapress-restricted-access').length > 0 ) {
            if( jQuery('#metapress-single-restricted-content').length > 0 ) {
                let page_product_list = jQuery('#metapress-single-restricted-content').data('product-ids').toString();
                if( page_product_list.indexOf(',') === -1 ) {
                    page_product_list = [page_product_list];
                } else {
                    page_product_list = page_product_list.split(',');
                }
                metapress_product_payments_manager.check_current_address_products_access(page_product_list);
            } else {
                metapress_product_payments_manager.check_current_address_access(jQuery('.metapress-restricted-access').data('product-id'), jQuery('.metapress-restricted-access'));
            }
        }

        if( jQuery('.metapress-checkout-access').length > 0 ) {
            metapress_product_payments_manager.check_current_address_access(jQuery('.metapress-checkout-access').data('product-id'), jQuery('.metapress-checkout-access'));
        }

        jQuery('.metapress-payment-button').click( function() {
            let pay_with_token = jQuery(this).data('token');
            let pay_network = jQuery(this).data('network');
            let pay_network_name = jQuery(this).data('networkname');
            let pay_chainid = jQuery(this).data('chainid');
            let token_address = jQuery(this).data('address');
            let network_explorer = jQuery(this).data('explorer');
            let receiving_address = jQuery(this).data('wallet');
            if( jQuery(this).hasClass('test-token') ) {
                token_address = jQuery(this).data('test-address');
            }

            let product_id = jQuery(this).data('product-id');
            let access_box = jQuery(this).parents('.metapress-restricted-access');

            let is_correct_network = metapress_product_payments_manager.confirm_correct_network(pay_with_token, pay_chainid, pay_network_name);
            if( is_correct_network ) {
                web3_access_wallet_manager.prepare_transaction(pay_network, network_explorer);
                metapress_product_payments_manager.get_token_ratio(metapress_product_payments_manager.payment_token, token_address, pay_network).then((ratio) => {
                  if( ratio > 0 ) {
                      web3_access_wallet_manager.setTokenRatio(ratio);
                      metapress_product_payments_manager.verify_product_price(product_id, access_box, token_address, receiving_address);
                  }
                });
            }

        });

        jQuery('body').delegate('.metapress-remove-transaction', 'click', function() {
            if( window.confirm('Are you sure you want to create a new transaction? This will delete any pending transactions for access to this content.') ) {
                metapress_product_payments_manager.remove_pending_transaction(jQuery(this).data('product-id'), jQuery(this).data('transaction'));
            }
        });

        jQuery('body').delegate('.metapress-confirm-transaction', 'click', function() {
            let transaction_id = jQuery(this).data('transaction');
            let approval_amount = jQuery(this).data('amount').toString();
            let contract_address = jQuery(this).data('contract-address');
            let product_id = jQuery(this).data('product-id');
            metapress_metamask_loading_manager.confirmContractPayment(product_id, null, approval_amount, contract_address, transaction_id);
        });

        // NEW NFT VERIFICATION

        jQuery('body').delegate('.metapress-verify-button', 'click', function() {
            let nft_token_data = jQuery(this).parents('.metapress-verify-nft-owner');
            let product_id = nft_token_data.data('product-id');
            let token_id = nft_token_data.data('token');
            let token_type = nft_token_data.data('token-type');
            let contract_address = nft_token_data.data('contract-address');
            let contract_network = nft_token_data.data('network');
            let contract_network_name = nft_token_data.data('networkname');
            let contract_network_chainid = nft_token_data.data('chainid');
            let contract_collection_slug = nft_token_data.data('collection');
            let contract_minimum_balance = nft_token_data.data('minimum');

            let is_correct_network = metapress_product_payments_manager.confirm_correct_network(null, contract_network_chainid, contract_network_name);
            if( is_correct_network ) {
                if( token_type == 'erc20' ) {
                    metapress_metamask_loading_manager.verify_erc20_owner(contract_address, product_id, contract_minimum_balance);
                }
                if( token_type == 'erc721' ) {
                    if( token_id && token_id != "" ) {
                        metapress_metamask_loading_manager.verify_721_nft_owner(token_id, contract_address, product_id);
                    } else {
                        metapress_metamask_loading_manager.verify_721_nft_collection_owner(contract_address, product_id, contract_collection_slug, contract_minimum_balance);
                    }

                }
                if( token_type == 'erc1155' ) {
                    metapress_metamask_loading_manager.verify_1155_nft_owner(token_id, contract_address, product_id, contract_collection_slug);
                }
            }
        });



        if( jQuery('#metapress-wallet-payment').length < 1 || jQuery('#metapress-nft-verification').length < 1) {
            jQuery('.metapress-change-payment-options').remove();
        } else {
            jQuery('.metapress-set-payment-method').first().addClass('active');
        }

        jQuery('.metapress-payment-method').first().addClass('active');

        jQuery('.metapress-set-payment-method').click( function() {
            let payment_method_button = jQuery(this);
            let payment_method_section = payment_method_button.data('section');
            jQuery('.metapress-set-payment-method, .metapress-payment-method').removeClass('active');
            payment_method_button.addClass('active');
            jQuery(payment_method_section).addClass('active');
        });
    }
});
