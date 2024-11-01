
class MetaPress_Token_Ratio_Manager {
    constructor() {
      this.binance_api_url = 'https://api.binance.com/api/v3/ticker/price';
      this.coingeck_api_url ='https://api.coingecko.com/api/v3/simple/price';
    }

    async get_binance_token_ratio(token) {
        let convertAPIURL = this.binance_api_url+'?symbol='+token+'USDT';
        let token_data = await jQuery.get(convertAPIURL);
        if( token_data.price ) {
            let new_token_ratio = 1 / token_data.price;
            return new_token_ratio;
        } else {
            if( token == 'ETH' || token == 'MATIC' || token == 'BNB' || token == 'AVAX' || token == 'FTM' || token == 'SOL' ) {
                let new_token_ratio = await this.get_coingecko_token_ratio(token);
                return new_token_ratio;
            } else {
                return 0;
            }
        }
    }

    async get_coingecko_token_ratio(token) {
        if( token == 'ETH' ) {
            let convertAPIURL = this.coingeck_api_url+'?ids=ethereum&vs_currencies=usd';
            let token_data = await jQuery.get(convertAPIURL);
            if( token_data.ethereum.usd ) {
                let new_token_ratio = 1 / token_data.ethereum.usd;
                return new_token_ratio;
            } else {
              return 0;
            }
        }
        if( token == 'MATIC' ) {
            let convertAPIURL = this.coingeck_api_url+'?ids=matic-network&vs_currencies=usd';
            let token_data = await jQuery.get(convertAPIURL);
            if( token_data['matic-network']['usd'] ) {
                let new_token_ratio = 1 / token_data['matic-network']['usd'];
                return new_token_ratio;
            } else {
              return 0;
            }
        }
        if( token == 'BNB' ) {
            let convertAPIURL = this.coingeck_api_url+'?ids=binancecoin&vs_currencies=usd';
            let token_data = await jQuery.get(convertAPIURL);
            if( token_data['binancecoin']['usd'] ) {
                let new_token_ratio = 1 / token_data['binancecoin']['usd'];
                return new_token_ratio;
            } else {
              return 0;
            }
        }
        if( token == 'AVAX' ) {
            let convertAPIURL = this.coingeck_api_url+'?ids=avalanche-2&vs_currencies=usd';
            let token_data = await jQuery.get(convertAPIURL);
            if( token_data['avalanche-2']['usd'] ) {
                let new_token_ratio = 1 / token_data['avalanche-2']['usd'];
                return new_token_ratio;
            } else {
              return 0;
            }
        }
        if( token == 'FTM' ) {
            let convertAPIURL = this.coingeck_api_url+'?ids=fantom&vs_currencies=usd';
            let token_data = await jQuery.get(convertAPIURL);
            if( token_data['fantom']['usd'] ) {
                let new_token_ratio = 1 / token_data['fantom']['usd'];
                return new_token_ratio;
            } else {
              return 0;
            }
        }
        if( token == 'SOL' ) {
            let convertAPIURL = this.coingeck_api_url+'?ids=solana&vs_currencies=usd';
            let token_data = await jQuery.get(convertAPIURL);
            if( token_data['solana']['usd'] ) {
                let new_token_ratio = 1 / token_data['solana']['usd'];
                return new_token_ratio;
            } else {
              return 0;
            }
        }
    }

    async get_coingecko_token(network, token_address, token) {
        if( network == 'mainnet' || network == 'sepolia' ) {
            let contract_info_url = 'https://api.coingecko.com/api/v3/simple/token_price/ethereum?contract_addresses='+token_address+'&vs_currencies=usd';
            let token_data = await jQuery.get(contract_info_url);

            if( token_data && token_data[token_address] && token_data[token_address].usd ) {
                let new_token_ratio = 1 / token_data[token_address].usd;
                return new_token_ratio;
            }
        }

        if( network == 'maticmainnet' || network == 'matictestnet' ) {
            let contract_info_url = 'https://api.coingecko.com/api/v3/simple/token_price/polygon-pos?contract_addresses='+token_address+'&vs_currencies=usd';
            let token_data = await jQuery.get(contract_info_url);

            if( token_data && token_data[token_address] && token_data[token_address].usd ) {
              let new_token_ratio = 1 / token_data[token_address].usd;
              return new_token_ratio;
            }
        }

        if( network == 'binancesmartchain' || network == 'binancetestnet' ) {
            let contract_info_url = 'https://api.coingecko.com/api/v3/simple/token_price/binance-smart-chain?contract_addresses='+token_address+'&vs_currencies=usd';
            let token_data = await jQuery.get(contract_info_url);

            if( token_data && token_data[token_address] && token_data[token_address].usd ) {
              let new_token_ratio = 1 / token_data[token_address].usd;
              return new_token_ratio;
            }
        }

        if( network == 'avaxmainnet' || network == 'avaxtestnet' ) {
            let contract_info_url = 'https://api.coingecko.com/api/v3/simple/token_price/avalanche?contract_addresses='+token_address+'&vs_currencies=usd';
            let token_data = await jQuery.get(contract_info_url);

            if( token_data && token_data[token_address] && token_data[token_address].usd ) {
              let new_token_ratio = 1 / token_data[token_address].usd;
              return new_token_ratio;
            }
        }

        if( network == 'fantomnetwork' || network == 'fantomtestnet' ) {
            let contract_info_url = 'https://api.coingecko.com/api/v3/simple/token_price/fantom?contract_addresses='+token_address+'&vs_currencies=usd';
            let token_data = await jQuery.get(contract_info_url);

            if( token_data && token_data[token_address] && token_data[token_address].usd ) {
              let new_token_ratio = 1 / token_data[token_address].usd;
              return new_token_ratio;
            }
        }
        return 0;
    }

    async search_coingecko_token(symbol) {
        symbol = symbol.toLowerCase();
        let coingecko_coinslist_url = 'https://api.coingecko.com/api/v3/coins/list';
        let coinlist_data = await jQuery.get(coingecko_coinslist_url);
        let found_coin_id = null;
        if( coinlist_data.length > 1 ) {
            jQuery.each(coinlist_data, function(index, coin) {
                if(coin.symbol == symbol) {
                    found_coin_id = coin.id;
                    return false;
                }
            });
        }

        if(found_coin_id != null) {
            coingecko_coinslist_url = 'https://api.coingecko.com/api/v3/simple/price?ids='+found_coin_id+'&vs_currencies=usd';
            let token_data = await jQuery.get(coingecko_coinslist_url);
            if( token_data && token_data[found_coin_id] && token_data[found_coin_id].usd ) {
              let new_token_ratio = 1 / token_data[found_coin_id].usd;
              return new_token_ratio;
            }
        }
        return 0;

    }
}
const metapress_token_ratio_manager = new MetaPress_Token_Ratio_Manager();
