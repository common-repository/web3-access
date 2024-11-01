
class MetaPress_MetaMask_Loading_Manager {

    #address = false;

    constructor() {
        this.metapress_contract = null;
        this.send_to_address = metapressmetamaskjsdata.send_to_address;
        this.#set_abi_contracts();
    }

    setup() {
        this.#address = web3_access_wallet_manager.getWalletAddress();
    }

    #set_abi_contracts() {
        var metapress_manager = this;
        jQuery.getJSON(metapressmetamaskjsdata.abi, function() {}).done(function(abi) {
          metapress_manager.contract_abi = abi;
        }).fail(function() {
          metapress_manager.contract_abi = null;
        });

        jQuery.getJSON(metapressmetamaskjsdata.erc20_abi, function() {}).done(function(abi) {
          metapress_manager.erc20_abi = abi;
        }).fail(function() {
          metapress_manager.erc20_abi = null;
        });

        jQuery.getJSON(metapressmetamaskjsdata.erc721_abi, function() {}).done(function(abi) {
          metapress_manager.erc721_abi = abi;
        }).fail(function() {
          metapress_manager.erc721_abi = null;
        });

        jQuery.getJSON(metapressmetamaskjsdata.erc1155_abi, function() {}).done(function(abi) {
          metapress_manager.erc1155_abi = abi;
        }).fail(function() {
          metapress_manager.erc1155_abi = null;
        });
    }

    set_contract(network) {
        let metapress_contract_address = metapressmetamaskjsdata.contract_address[network];
        this.metapress_contract = new web3_access_wallet_manager.web3.eth.Contract(this.contract_abi, metapress_contract_address);
    }

    set_approval_contract(contract_address) {
        return new web3_access_wallet_manager.web3.eth.Contract(this.erc20_abi, contract_address);
    }

    set_721_nft_contract(contract_address) {
        return new web3_access_wallet_manager.web3.eth.Contract(this.erc721_abi, contract_address);
    }

    set_1155_nft_contract(contract_address) {
        return new web3_access_wallet_manager.web3.eth.Contract(this.erc1155_abi, contract_address);
    }

    async verify_erc20_owner(contract_address, product_id, minimum_balance) {
        let nft_contract = this.set_approval_contract(contract_address);
        const metapress_manager = this;
        await nft_contract.methods.balanceOf(metapress_manager.#address).call({from: metapress_manager.#address}, function(error, balance) {
            if( error ) {
                jQuery('.metapress-access-buttons').addClass('show');
                jQuery('.metapress-notice-box').html('<p>'+error.message+'</p>').show();
                metapress_show_ajax_error(error.message);
                return {
                    'status': 'error',
                    'hash': null
                };
            }
            let from_wei_balance = web3_access_wallet_manager.web3.utils.fromWei(balance, 'ether');
            if( from_wei_balance >= minimum_balance ) {
                web3_access_wallet_manager.create_nft_access_token(product_id);
            } else {
                metapress_show_ajax_error('Verification failed');
            }
        });

    }

    async verify_721_nft_collection_owner(contract_address, product_id, collection_slug, minimum_balance) {
        let nft_contract = this.set_721_nft_contract(contract_address);
        const metapress_manager = this;
        if( collection_slug && collection_slug.length > 0 ) {
            if( typeof(metapress_opensea_api_manager) != 'undefined' ) {
                metapress_opensea_api_manager.get_assets(contract_address, metapress_manager.#address, collection_slug).then( (token_data) => {
                    if( token_data.assets && token_data.assets.length >= minimum_balance ) {
                        web3_access_wallet_manager.create_nft_access_token(product_id);
                    } else {
                        metapress_show_ajax_error('Verification failed');
                    }
                });
            } else {
                metapress_show_ajax_error('Missing OpenSea API Key to check collection balance.');
            }
        } else {
            await nft_contract.methods.balanceOf(metapress_manager.#address).call({from: metapress_manager.#address}, function(error, balance) {
                if( error ) {
                    jQuery('.metapress-access-buttons').addClass('show');
                    jQuery('.metapress-notice-box').html('<p>'+error.message+'</p>').show();
                    metapress_show_ajax_error(error.message);
                    return {
                        'status': 'error',
                        'hash': null
                    };
                }
                if( balance >= minimum_balance ) {
                    web3_access_wallet_manager.create_nft_access_token(product_id);
                } else {
                    metapress_show_ajax_error('Verification failed');
                }
            });
        }
    }

    async verify_721_nft_owner(token_id, contract_address, product_id) {
        let nft_contract = this.set_721_nft_contract(contract_address);
        const metapress_manager = this;
        await nft_contract.methods.ownerOf(token_id).call({from: metapress_manager.#address}, function(error, owner) {
            if( error ) {
                jQuery('.metapress-access-buttons').addClass('show');
                jQuery('.metapress-notice-box').html('<p>'+error.message+'</p>').show();
                metapress_show_ajax_error(error.message);
                return {
                    'status': 'error',
                    'hash': null
                };
            }
            if( owner.toLowerCase() === metapress_manager.#address.toLowerCase() ) {
                web3_access_wallet_manager.create_nft_access_token(product_id);
            } else {
                metapress_show_ajax_error('Verification failed');
            }
        });
    }

    async verify_1155_nft_owner(token_id, contract_address, product_id, collection_slug) {
        let nft_contract = this.set_1155_nft_contract(contract_address);
        const metapress_manager = this;

        if( token_id === "" ) {
            if( typeof(metapress_opensea_api_manager) != 'undefined' ) {
                metapress_opensea_api_manager.get_assets(contract_address, metapress_manager.#address, collection_slug).then( (token_data) => {
                    if( token_data.assets && token_data.assets.length > 1 ) {
                        web3_access_wallet_manager.create_nft_access_token(product_id);
                    } else {
                        metapress_show_ajax_error('Verification failed');
                    }
                });
            } else {
                metapress_show_ajax_error('Missing OpenSea API Key to check ERC-1155 collection balance.');
            }

        } else {
            await nft_contract.methods.balanceOf(metapress_manager.#address, token_id).call({from: metapress_manager.#address}, function(error, balance) {
                if( error ) {
                    jQuery('.metapress-access-buttons').addClass('show');
                    jQuery('.metapress-notice-box').html('<p>'+error.message+'</p>').show();
                    metapress_show_ajax_error(error.message);
                    return {
                        'status': 'error',
                        'hash': null
                    };
                }
                if( balance > 0 ) {
                    web3_access_wallet_manager.create_nft_access_token(product_id);
                } else {
                    metapress_show_ajax_error('Verification failed');
                }
            });
        }
    }

    async makeContractPayment(product_id, product_price, token, contract_address) {
        const metapress_manager = this;
        var token_price = (product_price * web3_access_wallet_manager.token_ratio);
        token_price = token_price.toFixed(18).toString();
        var wei_amount = web3_access_wallet_manager.web3.utils.toWei(token_price, 'ether');
        this.set_contract(web3_access_wallet_manager.provider_slug);
        if( this.send_to_address ) {
            if( metapress_manager.#address && this.metapress_contract && web3_access_wallet_manager.provider_slug && web3_access_wallet_manager.token_ratio > 0 ) {
                // DIRECT TRANSACTION VIA SMART CONTRACT ON NETWORK
                if( token == 'ETH' || token == 'MATIC' || token == 'BNB' || token == 'AVAX' || token == 'FTM' ) {
                    try {
                        await web3_access_wallet_manager.web3.eth.getGasPrice().then(gasPrice => {
                            this.metapress_contract.methods.smartTransfer(this.send_to_address).estimateGas({from: metapress_manager.#address, value: wei_amount}).then(estimatedGas => {
                                this.metapress_contract.methods.smartTransfer(this.send_to_address).send({from: metapress_manager.#address, value: wei_amount, gasPrice: gasPrice, gas: estimatedGas, maxPriorityFeePerGas: null,
            maxFeePerGas: null}).on('transactionHash', (hash) => {
                                    web3_access_wallet_manager.create_transaction(product_id, token, token_price, hash, 'pending', null);
                                }).on('error', (error) => {
                                    jQuery('.metapress-access-buttons').addClass('show');
                                    return {
                                        success: false,
                                        error: error
                                    }
                                });
                            }).catch('error', (error) => {
                                metapress_show_ajax_error(error.message);
                                jQuery('.metapress-access-buttons').addClass('show');
                                return {
                                    success: false,
                                    error: error
                                }
                            });
                        }).catch('error', (error) => {
                            metapress_show_ajax_error(error.message);
                            jQuery('.metapress-access-buttons').addClass('show');
                            return {
                                success: false,
                                error: error
                            }
                        });
                    } catch(error) {
                        metapress_show_ajax_error(error.message);
                        jQuery('.metapress-access-buttons').addClass('show');
                        return {
                            success: false,
                            error: error
                        }
                    }

                } else {
                    // MUST REQUEST SPENDING ALLOWANCE
                    this.get_contract_approval(product_id, token, token_price, contract_address, wei_amount);
                }
            } else {
                jQuery('.metapress-access-buttons').addClass('show');
            }
        } else {
            metapress_show_ajax_error('Missing receiving wallet address.');
            jQuery('.metapress-access-buttons').addClass('show');
        }

     }

     async get_contract_allowance(contract_address) {
         const metapress_manager = this;
         let approval_contract = this.set_approval_contract(contract_address);
         let metapress_contract_address = metapressmetamaskjsdata.contract_address[web3_access_wallet_manager.provider_slug];
         let token_allowance = await approval_contract.methods.allowance(metapress_manager.#address, metapress_contract_address).call({from: metapress_manager.#address}, function(error, result) {
             if( error ) {
                 jQuery('.metapress-access-buttons').addClass('show');
                 jQuery('.metapress-notice-box').html('<p>'+error.message+'</p>').show();
                 metapress_show_ajax_error(error.message);
                 return {
                     'status': 'error',
                     'hash': null
                 };
             }
         });
         return parseInt(token_allowance);
     }

    async get_contract_approval(product_id, token, token_price, contract_address, wei_amount) {
        let approval_contract = this.set_approval_contract(contract_address);
        let metapress_contract_address = metapressmetamaskjsdata.contract_address[web3_access_wallet_manager.provider_slug];
        const metapress_manager = this;
        let token_allowance = await this.get_contract_allowance(contract_address);

        if( token_allowance < wei_amount ) {
            if( token_allowance > 0 ) {
              console.log('need to set allowance to 0');
              await approval_contract.methods.approve(metapress_contract_address, 0).send({from: metapress_manager.#address}).on('error', function(error, receipt) {
                  jQuery('.metapress-access-buttons').addClass('show');
                  jQuery('.metapress-notice-box').html('<p>'+error.message+'</p>').show();
                  metapress_show_ajax_error(error.message);
              }).on('transactionHash', function(hash) {
                  jQuery('.metapress-access-buttons').addClass('show');
                  jQuery('.metapress-notice-box').html('<p>Please try again after your transaction '+hash+' is complete!</p>').show();
              });
            } else {
                  console.log('setting allowance');
                  await web3_access_wallet_manager.web3.eth.getGasPrice().then(gasPrice => {
                      approval_contract.methods.approve(metapress_contract_address, wei_amount).estimateGas({from: metapress_manager.#address}).then(estimatedGas => {
                          approval_contract.methods.approve(metapress_contract_address, wei_amount).send({from: metapress_manager.#address, gasPrice: gasPrice, gas: estimatedGas}).on('error', function(error, receipt) {
                              jQuery('.metapress-access-buttons').addClass('show');
                              jQuery('.metapress-notice-box').html('<p>'+error.message+'</p>').show();
                              metapress_show_ajax_error(error.message);
                          }).on('transactionHash', function(hash){
                              web3_access_wallet_manager.create_transaction(product_id, token, token_price, hash, 'approval', contract_address);
                          });

                      }).catch('error', (error) => {
                          metapress_show_ajax_error(error.message);
                          jQuery('.metapress-access-buttons').addClass('show');
                          return {
                              success: false,
                              error: error
                          }
                      }).catch('error', (error) => {
                          metapress_show_ajax_error(error.message);
                          jQuery('.metapress-access-buttons').addClass('show');
                          return {
                              success: false,
                              error: error
                          }
                      });
                  });

              }
        } else {
            await metapress_manager.confirmContractPayment(product_id, token, token_price, contract_address, null);
        }
    }

    async confirmContractPayment(product_id, token, token_price, contract_address, transaction_id) {
        const metapress_manager = this;
        this.set_contract(web3_access_wallet_manager.provider_slug);
        let token_allowance = await this.get_contract_allowance(contract_address);
        if( token_allowance > 0 ) {
            if( this.send_to_address ) {
                if( metapress_manager.#address && this.metapress_contract && web3_access_wallet_manager.provider_slug ) {

                    var wei_amount = web3_access_wallet_manager.web3.utils.toWei(token_price, 'ether');

                    // DIRECT TRANSACTION VIA SMART CONTRACT ON NETWORK
                    await web3_access_wallet_manager.web3.eth.getGasPrice().then(gasPrice => {
                        this.metapress_contract.methods.smartTokenTransfer(contract_address, this.send_to_address, wei_amount).send({from: metapress_manager.#address, gasPrice: gasPrice}).on('transactionHash', (hash) => {
                            if( transaction_id != null ) {
                                web3_access_wallet_manager.update_approval_transaction(product_id, hash, transaction_id);
                            } else {
                                web3_access_wallet_manager.create_transaction(product_id, token, token_price, hash, 'pending', contract_address);
                            }
                        }).on('error', (error) => {
                            metapress_show_ajax_error(error);
                        }).catch('error', (error) => {
                            metapress_show_ajax_error(error.message);
                            jQuery('.metapress-access-buttons').addClass('show');
                            return {
                                success: false,
                                error: error
                            }
                        });
                    }).catch('error', (error) => {
                        metapress_show_ajax_error(error.message);
                        jQuery('.metapress-access-buttons').addClass('show');
                        return {
                            success: false,
                            error: error
                        }
                    });
                }
            } else {
                metapress_show_ajax_error('Missing receiving wallet address.');
            }
        } else {
            metapress_show_ajax_error('Allowance for this contract is 0');
        }
     }

}

const metapress_metamask_loading_manager = new MetaPress_MetaMask_Loading_Manager();
jQuery(document).on('metapressWalletAccountReady', function() {
    metapress_metamask_loading_manager.setup();
});
