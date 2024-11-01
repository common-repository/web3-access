
class Web3_Access_Moralis_API_Manager {
    constructor() {

    }

    async verify_nft_owner(owner_address, contract_address, network, chainid) {

        let moralis_nft_list_endpoint = metapressmoralis.endpoints.verifynftowner;
        moralis_nft_list_endpoint = web3_access_wallet_manager.set_request_param(moralis_nft_list_endpoint, 'mpwalletaddress', web3_access_wallet_manager.getWalletAddress());
        moralis_nft_list_endpoint = web3_access_wallet_manager.set_request_param(moralis_nft_list_endpoint, 'contract_address', contract_address);
        moralis_nft_list_endpoint = web3_access_wallet_manager.set_request_param(moralis_nft_list_endpoint, 'request_key', metapressmanagerrequests.api.request_key);
        moralis_nft_list_endpoint = web3_access_wallet_manager.set_request_param(moralis_nft_list_endpoint, 'network', network);
        moralis_nft_list_endpoint = web3_access_wallet_manager.set_request_param(moralis_nft_list_endpoint, 'chainid', chainid);

        jQuery.ajax({
            url: moralis_nft_list_endpoint,
            type: 'GET',
            success: function(response) {
                jQuery('#metapress-updating-box').removeClass('show-overlay-box');
            },
            error: function(error) {
                metapress_show_ajax_error(error.responseText);
            }
        });
    }
}

const web3_access_moralis_api_manager = new Web3_Access_Moralis_API_Manager();
