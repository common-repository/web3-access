class Web3_Access_Wallet_Manager {

    #address = false;
    #wallet_type;
    token_ratio = 0;
    #fetching_address = false;
    constructor() {
        this.provider = null;
        this.chainId = null;
        this.provider_slug = null;
        this.connection = null;
        this.web3 = null;
        this.transaction_viewing_url = '';
        this.address = false;
        this.wallet_type = false;
        this.needs_page_refresh = false;
    }

    getCurrentWalletProvider() {
        let current_wallet_provider = this.#getConnectedWalletStorage();
        if( current_wallet_provider == 'ethereum' ) {
            this.getUserEthereumAccount();
        }
        if( current_wallet_provider == 'solana' ) {
            this.connectSolanaWallet();
        }
    }

    async getEthereumProvider() {
        this.provider = await detectEthereumProvider();
        this.provider.on('accountsChanged', (accounts) => {
            this.#address = false;
            this.remove_token_param();

        });

        this.provider.on('chainChanged', (chainId) => {
            this.remove_token_param();
        });

        this.web3 = new Web3(this.provider);
        this.chainId = await this.provider.request({ method: 'eth_chainId' });
    }

    async handleConnect() {
        this.#setWalletType('ethereum');
        if( this.provider == null || this.provider.isPhantom ) {
            await this.getEthereumProvider();
        }
        const eth_accounts = await this.provider.request({ method: 'eth_requestAccounts' });
        this.#setWalletAddress(eth_accounts[0]);
        this.complete_account_connection();
    }

    async getUserEthereumAccount() {
        this.#setWalletType('ethereum');
        if( this.provider == null || this.provider.isPhantom ) {
            await this.getEthereumProvider();
        }
        if( this.provider && this.provider != null ) {
            if( ! this.#fetching_address  ) {
                this.#fetching_address = true;
                const eth_accounts = await this.provider.request({ method: 'eth_accounts' });
                if( eth_accounts.length > 0 ) {
                    this.#setWalletAddress(eth_accounts[0]);
                    this.complete_account_connection();
                }
            }
        }
    }

    async getSolanaProvider() {
        if ('phantom' in window) {
            const provider = window.phantom?.solana;
            if (provider?.isPhantom) {
                this.provider = provider;
            }
        }
    }

    async connectSolanaWallet() {
        this.#setWalletType('solana');
        if( this.provider == null || ! this.provider.isPhantom ) {
            await this.getSolanaProvider();
        }
        if( this.provider && this.provider?.isPhantom ) {
            const respo = await this.provider.connect();
        	let wallet_address = respo.publicKey.toString();
            this.#setWalletAddress(wallet_address);
            this.provider.chainId = '0xsolana';
            this.chainId = '0xsolana'; 
            this.complete_account_connection();
            this.#checkSolanaWalletChange();
        }
    }

    #checkSolanaWalletChange() {
        if( window.solana && this.#address != false ) {
            let web3SolanaWalletManager = this;
            let solanaCheckInterval = setInterval( function() {
                if( window.solana.publicKey == null ) {
                    clearInterval(solanaCheckInterval);
                    web3SolanaWalletManager.#address = false;
                    window.location.reload();
                } else {
                    if( web3SolanaWalletManager.getWalletAddress() != window.solana.publicKey.toBase58() ) {
                        clearInterval(solanaCheckInterval);
                        web3SolanaWalletManager.#address = false;
                        window.location.reload();
                    }
                }

            }, 500);
        }
    }

    #setWalletAddress(address) {
        let confirm_address = address;
        if( this.#wallet_type == 'ethereum' ) {
            confirm_address = this.web3.utils.toChecksumAddress(address);
        }
        if( webaccesswalletjsdata.live_mode != 1 ) {
            if( this.#wallet_type == 'ethereum' ) {
                const confirm_test_address = this.web3.utils.toChecksumAddress(webaccesswalletjsdata.allowed_test_address);
                if( confirm_test_address == confirm_address ) {
                    this.#address = confirm_address;
                    this.create_wallet_session();
                } else {
                    alert('Incorrect wallet address for Test Mode');
                }
            }
            if( this.#wallet_type == 'solana' ) {
                const confirm_test_address = webaccesswalletjsdata.solana_test_address;
                if( confirm_test_address == confirm_address ) {
                    this.#address = confirm_address;
                    this.create_wallet_session();
                } else {
                    alert('Incorrect wallet address for Test Mode');
                }
            }

        } else {
            this.#address = confirm_address;
            this.create_wallet_session();
        }
    }

    #setWalletType(wallet_type) {
        this.#wallet_type = wallet_type;
        this.#setConnectedWalletStorage();
    }

    getWalletAddress() {
        return this.#address;
    }

    getWalletType() {
        return this.#wallet_type;
    }

    setTokenRatio(ratio) {
        this.token_ratio = ratio;
    }

    set_token_param(token) {
        var metapress_url = new URL(window.location.href);
        var metapress_redirect = metapress_url.searchParams.get('mpred');
        var web3_access_token = metapress_url.searchParams.get('mpatok');
        if( metapress_redirect ) {
            metapress_url.href = metapress_redirect;
        }
        if( ! web3_access_token || web3_access_token != token ) {
            if( token.trim() != "" ) {
                metapress_url.searchParams.set('mpatok',token);
                window.location.href = metapress_url.href;
            }
        }
    }

    remove_token_param() {
        var metapress_url = new URL(window.location.href);
        var metapress_token = metapress_url.searchParams.get('mpatok');
        if( metapress_token ) {
            metapress_url.searchParams.delete('mpatok');
            window.location.href = metapress_url.href;
        } else {
            document.location.reload();
        }
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

    create_wallet_session() {
        if( this.#address ) {
            var metapress_manager = this;
            let metapress_new_session_endpoint = webaccesswalletjsdata.createsession;
            metapress_new_session_endpoint = this.set_request_param(metapress_new_session_endpoint, 'mpwalletaddress', this.#address);
            metapress_new_session_endpoint = this.set_request_param(metapress_new_session_endpoint, 'request_key', metapressmanagerrequests.api.request_key);
            jQuery.ajax({
                url: metapress_new_session_endpoint,
                type: 'POST',
                beforeSend: function ( xhr ) {
                    xhr.setRequestHeader( 'X-WP-Nonce', metapressmanagerrequests.user.nonce );
                    xhr.setRequestHeader( 'X-METAPRESS', metapressmanagerrequests.api.request_key );
                },
                success: function(response) {
                },
                error: function(error) {
                    metapress_show_ajax_error(error.responseText);
                }
            });
        }
    }

    trim_wallet_address() {
        return this.#address.substring(0, 6)+'...';
    }

    complete_account_connection() {
        if( this.#address && this.#address != null ) {
            let trimmed_wallet_address = this.trim_wallet_address();
            if( this.getWalletType() == 'ethereum' ) {
                jQuery('.metamask-connect-wallet').addClass('connected');
                jQuery('.metamask-connect-wallet').find('.metapress-wallet-name').text(trimmed_wallet_address);
                jQuery('.metapress-change-wallet-text').text(trimmed_wallet_address+'(Ethereum)');
                jQuery('.metapress-payment-button').filter('[data-chainid=0xsolana]').remove();
            }

            if( this.getWalletType() == 'solana' ) {
                jQuery('.phantom-connect-wallet').addClass('connected');
                jQuery('.phantom-connect-wallet').find('.metapress-wallet-name').text(trimmed_wallet_address);
                jQuery('.metapress-change-wallet-text').text(trimmed_wallet_address+'(Solana)');
                jQuery('.metapress-payment-button').not('[data-chainid=0xsolana]').remove();

            }
            jQuery('.metapress-access-buttons').addClass('show');
            jQuery('.metapress-login-notice').remove();
            jQuery('#metapress-wallet-options').addClass('hidden');
            jQuery(document).trigger('metapressWalletAccountReady');
        }
    }

    prepare_transaction(network_slug, explorer_url) {
        this.transaction_viewing_url = explorer_url;
        this.provider_slug = network_slug;
    }

    #setConnectedWalletStorage() {
        if( window.sessionStorage ) {
            sessionStorage.setItem('metapresswalletconnect', this.#wallet_type);
        }
    }

    #getConnectedWalletStorage() {
        if( window.sessionStorage ) {
             return sessionStorage.getItem('metapresswalletconnect');
        }
    }

    create_transaction(product_id, token, token_price, hash, status, contract_address) {
        if( product_id && this.#address ) {
            metapress_show_ajax_updating('Creating transaction...');

            let metapress_new_transaction_endpoint = webaccesswalletjsdata.newtransaction;
            metapress_new_transaction_endpoint = this.set_request_param(metapress_new_transaction_endpoint, 'mpwalletaddress', this.#address);
            metapress_new_transaction_endpoint = this.set_request_param(metapress_new_transaction_endpoint, 'productid', product_id);
            metapress_new_transaction_endpoint = this.set_request_param(metapress_new_transaction_endpoint, 'transaction_hash', hash);
            metapress_new_transaction_endpoint = this.set_request_param(metapress_new_transaction_endpoint, 'token', token);
            metapress_new_transaction_endpoint = this.set_request_param(metapress_new_transaction_endpoint, 'token_amount', token_price);
            metapress_new_transaction_endpoint = this.set_request_param(metapress_new_transaction_endpoint, 'network', this.provider_slug);
            metapress_new_transaction_endpoint = this.set_request_param(metapress_new_transaction_endpoint, 'txn_status', status);
            metapress_new_transaction_endpoint = this.set_request_param(metapress_new_transaction_endpoint, 'contract_address', contract_address);
            metapress_new_transaction_endpoint = this.set_request_param(metapress_new_transaction_endpoint, 'request_key', metapressmanagerrequests.api.request_key);

            jQuery.ajax({
                url: metapress_new_transaction_endpoint,
                type: 'POST',
                beforeSend: function ( xhr ) {
                    xhr.setRequestHeader( 'X-WP-Nonce', metapressmanagerrequests.user.nonce );
                    xhr.setRequestHeader( 'X-METAPRESS', metapressmanagerrequests.api.request_key );
                },
                success: function(response) {
                    jQuery('#metapress-updating-box').removeClass('show-overlay-box');
                    var transaction_response = response;
                    if( transaction_response && transaction_response.success ) {
                      var transaction_viewing_url = web3_access_wallet_manager.transaction_viewing_url + 'tx/' + hash;
                      var pending_transaction_notice = '<p>Thank You! Your <a href="'+transaction_viewing_url+'" target="_blank">transaction is currently pending</a>. Please check again once your transaction is complete.</p>';
                      jQuery('.metapress-notice-box').html(pending_transaction_notice).show();
                    }
                },
                error: function(error) {
                    metapress_show_ajax_error(error.responseText);
                }
            });
        }
    }

    update_approval_transaction(product_id, transaction_hash, transaction_id) {
        if( product_id && this.#address ) {
            metapress_show_ajax_updating('Confirming transaction...');

            let metapress_update_transaction_endpoint = webaccesswalletjsdata.updatetransaction;
            metapress_update_transaction_endpoint = this.set_request_param(metapress_update_transaction_endpoint, 'mpwalletaddress', this.#address);
            metapress_update_transaction_endpoint = this.set_request_param(metapress_update_transaction_endpoint, 'productid', product_id);
            metapress_update_transaction_endpoint = this.set_request_param(metapress_update_transaction_endpoint, 'transaction_hash', transaction_hash);
            metapress_update_transaction_endpoint = this.set_request_param(metapress_update_transaction_endpoint, 'transaction_id', transaction_id);
            metapress_update_transaction_endpoint = this.set_request_param(metapress_update_transaction_endpoint, 'request_key', metapressmanagerrequests.api.request_key);

            jQuery.ajax({
                url: metapress_update_transaction_endpoint,
                type: 'POST',
                beforeSend: function ( xhr ) {
                    xhr.setRequestHeader( 'X-WP-Nonce', metapressmanagerrequests.user.nonce );
                    xhr.setRequestHeader( 'X-METAPRESS', metapressmanagerrequests.api.request_key );
                },
                success: function(response) {
                    jQuery('#metapress-updating-box').removeClass('show-overlay-box');
                    var transaction_response = response;
                    if( transaction_response && transaction_response.updated ) {
                      var transaction_viewing_url = web3_access_wallet_manager.transaction_viewing_url + 'tx/' + transaction_hash;
                      var pending_transaction_notice = '<p>Thank You! Your <a href="'+transaction_viewing_url+'" target="_blank">transaction is currently pending</a>. Please check again once your transaction is complete.</p>';
                      jQuery('.metapress-notice-box').html(pending_transaction_notice).show();
                    }
                },
                error: function(error) {
                    metapress_show_ajax_error(error.responseText);
                }
            });
        }
    }

    create_nft_access_token(product_id) {
        if( product_id && this.#address ) {
            metapress_show_ajax_updating('Creating your access token...');

            let metapress_new_nft_access_token = webaccesswalletjsdata.nfttoken;
            metapress_new_nft_access_token = this.set_request_param(metapress_new_nft_access_token, 'mpwalletaddress', this.#address);
            metapress_new_nft_access_token = this.set_request_param(metapress_new_nft_access_token, 'productid', product_id);
            metapress_new_nft_access_token = this.set_request_param(metapress_new_nft_access_token, 'nft_owner_verification_timestamp', jQuery('#metapress-nft-verification-text').data('noncetimestamp'));
            if( jQuery('#metapress-nft-verification-text').data('redirect') ) {
                metapress_new_nft_access_token = this.set_request_param(metapress_new_nft_access_token, 'mpredirect', jQuery('#metapress-nft-verification-text').data('redirect'));
            }
            metapress_new_nft_access_token = this.set_request_param(metapress_new_nft_access_token, 'request_key', metapressmanagerrequests.api.request_key);

            jQuery.ajax({
                url: metapress_new_nft_access_token,
                type: 'POST',
                beforeSend: function ( xhr ) {
                    xhr.setRequestHeader( 'X-WP-Nonce', metapressmanagerrequests.user.nonce );
                    xhr.setRequestHeader( 'X-METAPRESS', metapressmanagerrequests.api.request_key );
                },
                success: function(response) {
                    jQuery('#metapress-updating-box').removeClass('show-overlay-box');
                    var transaction_response = response;
                    if( transaction_response && transaction_response.success && transaction_response.access_token ) {
                        if( transaction_response.redirect ) {
                            window.location.href = transaction_response.redirect;
                        } else {
                            web3_access_wallet_manager.set_token_param(transaction_response.access_token);
                        }
                    }
                },
                error: function(error) {
                    metapress_show_ajax_error(error.responseText);
                }
            });
        }
    }
}
const web3_access_wallet_manager = new Web3_Access_Wallet_Manager();

jQuery(document).ready(function() {
    web3_access_wallet_manager.getCurrentWalletProvider();
    if ( typeof window.ethereum !== 'undefined' ) {
        jQuery('.metamask-connect-wallet').click( function() {
            if( ! jQuery(this).hasClass('connected') ) {
                jQuery('.metapress-connect-wallet-option').removeClass('connected');
                web3_access_wallet_manager.handleConnect();
            }
        });
    }

    jQuery('.phantom-connect-wallet').click( function() {
        if( ! jQuery(this).hasClass('connected') ) {
            jQuery('.metapress-connect-wallet-option').removeClass('connected');
            web3_access_wallet_manager.connectSolanaWallet();
        }
    });

    jQuery('.metapress-change-wallet-button').click( function() {
        jQuery('#metapress-wallet-options').toggleClass('hidden');
    });
});
