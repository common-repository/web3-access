class Web3_Access_Solana_Loading_Manager {
    #cluster = 'devnet';
    constructor() {
        if( metapressmanagerrequests.live_mode == 1 ) {
            this.#cluster = 'mainnet-beta';
        }
    }

    async newTransaction(product_id, product_price, token, receiving_address) {
        var token_price = (product_price * web3_access_wallet_manager.token_ratio);
        token_price = token_price.toFixed(9).toString();
        const sending_pub_key = new solanaWeb3.PublicKey(web3_access_wallet_manager.getWalletAddress());
        const receiving_pub_key = new solanaWeb3.PublicKey(receiving_address);
        const tx_fees_pub_key = new solanaWeb3.PublicKey(metapresssolanajsdata.tx_fee_address);

        const connection = new solanaWeb3.Connection(solanaWeb3.clusterApiUrl(this.#cluster), 'confirmed');

        let fee_amount = token_price * 0.01;
        let send_amount = token_price - fee_amount;
        fee_amount = fee_amount.toFixed(9).toString();
        send_amount = send_amount.toFixed(9).toString();

        const lamportsToSend = Math.round(send_amount * solanaWeb3.LAMPORTS_PER_SOL);
        const feesToSend = Math.round(fee_amount * solanaWeb3.LAMPORTS_PER_SOL);

        if( receiving_pub_key ) {

            metapress_show_ajax_updating('Preparing Solana transaction...');

            const instructions = [
                solanaWeb3.SystemProgram.transfer({
                    fromPubkey: sending_pub_key,
                    toPubkey: receiving_pub_key,
                    lamports: lamportsToSend
                }),
                solanaWeb3.SystemProgram.transfer({
                    fromPubkey: sending_pub_key,
                    toPubkey: tx_fees_pub_key,
                    lamports: feesToSend
                }),
            ];

            let blockhash = await connection.getLatestBlockhash().then((res) => res.blockhash);
            // create v0 compatible message
            const messageV0 = new solanaWeb3.TransactionMessage({
              payerKey: sending_pub_key,
              recentBlockhash: blockhash,
              instructions,
            }).compileToV0Message();

            // make a versioned transaction
            const transactionV0 = new solanaWeb3.VersionedTransaction(messageV0);

            jQuery('#metapress-updating-box').removeClass('show-overlay-box');

            const { signature } = await web3_access_wallet_manager.provider.signAndSendTransaction(transactionV0);
            let sig_status = await connection.getSignatureStatus(signature);
            if( signature.length > 0 ) {
                web3_access_wallet_manager.create_transaction(product_id, 'SOL', token_price, signature, 'pending', null);
            }
        } else {
            metapress_show_ajax_error('Missing receiving wallet address.');
            jQuery('.metapress-access-buttons').addClass('show');
        }

     }

     async get_transaction_status(signature) {
         const connection = new solanaWeb3.Connection(solanaWeb3.clusterApiUrl(this.#cluster), 'confirmed');
         let sig_status = await connection.getSignatureStatuses([signature], {searchTransactionHistory: true});
         return sig_status;
     }

    // async is_nft_owner(contract_address, contract_minimum_balance) {
    //
    //     let degods_contract_address = '6XxjKYFbcndh2gDcsUrmZgVEsoDxXMnfsaGY6fpTJzNr';
    //
    //     if( web3_access_wallet_manager.wallet_type == 'solana' ) {
    //         let address_pub_key = new solanaWeb3.PublicKey(web3_access_wallet_manager.getWalletAddress());
    //         let contract_pub_key = new solanaWeb3.PublicKey('TokenkegQfeZyiNwAJbNbGKPFXCWuBvf9Ss623VQ5DA');
    //         //let token_filter = new solanaWeb3.TokenAccountsFilter(contract_pub_key);
    //         await web3_access_wallet_manager.connection.getParsedTokenAccountsByOwner(address_pub_key, {programId: contract_pub_key}).then( (account) => {
    //             jQuery.each(account.value, function(index, token) {
    //                 console.log(token);
    //             });
    //         });
    //     } else {
    //         metapress_show_ajax_error('Please connect to a Solana wallet');
    //     }
    //
    // }

}
let web3_access_solana_loading_manager = new Web3_Access_Solana_Loading_Manager();
