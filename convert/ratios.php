<?php

class MetaPress_Crypto_Price_Converter {
    protected $binance_api_url;
    protected $coingecko_api_url;
    protected $token_ratios;
    protected $live_mode;
    public function __construct() {
         $this->live_mode = get_option('metapress_live_mode', 0);
         $this->binance_api_url = 'https://api.binance.com/api/v3/ticker/price?symbol=';
         $this->coingecko_api_url = 'https://api.coingecko.com/api/v3/simple/price';
         $this->token_ratios = get_option('metapress_token_ratios', array());
         $metapress_binance_cron = get_option('metapress_binance_cron', 1);
         if( $metapress_binance_cron && extension_loaded('curl') ) {
           $this->set_all_token_pairs();
         }
    }

    public function set_all_token_pairs() {
        $token_ratios_last_updated = get_option('metapress_token_ratios_updated_timestamp');
        $mp_current_time = strtotime('-30 seconds', current_time('timestamp', 1)); // USES GMT time
        if( empty($token_ratios_last_updated) || $token_ratios_last_updated < $mp_current_time ) {
            $this->check_custom_network_ratios();
            $this->check_custom_token_ratios();
        }
    }

    public function get_binance_price($token_pair) {
        $request_url = $this->binance_api_url . $token_pair;
        $binance_results = wp_remote_retrieve_body( wp_remote_get($request_url) );
        $binance_results = json_decode($binance_results);
        return $binance_results;
    }

    public function get_coingecko_price($token_pair) {
        $request_url = null;
        $coingecko_price = (object) array();
        if( $token_pair == 'ETHUSDT' ) {
          $request_url = $this->coingecko_api_url . '?ids=ethereum&vs_currencies=usd';
        }
        if( $token_pair == 'MATICUSDT' ) {
          $request_url = $this->coingecko_api_url . '?ids=matic-network&vs_currencies=usd';
        }
        if( $token_pair == 'BNBUSDT' ) {
          $request_url = $this->coingecko_api_url . '?ids=binancecoin&vs_currencies=usd';
        }
        if( $token_pair == 'AVAXUSDT' ) {
          $request_url = $this->coingecko_api_url . '?ids=avalanche-2&vs_currencies=usd';
        }
        if( $token_pair == 'FTMUSDT' ) {
          $request_url = $this->coingecko_api_url . '?ids=fantom&vs_currencies=usd';
        }
        if( $token_pair == 'SOLUSDT' ) {
          $request_url = $this->coingecko_api_url . '?ids=solana&vs_currencies=usd';
        }
        if( ! empty($request_url) ) {
          $coingecko_data = wp_remote_retrieve_body( wp_remote_get($request_url) );
          $coingecko_data = json_decode($coingecko_data, true);
          if( $token_pair == 'ETHUSDT' ) {
            $coingecko_price->price = $coingecko_data['ethereum']['usd'];
          }
          if( $token_pair == 'MATICUSDT' ) {
            $coingecko_price->price = $coingecko_data['matic-network']['usd'];
          }
          if( $token_pair == 'BNBUSDT' ) {
            $coingecko_price->price = $coingecko_data['binancecoin']['usd'];
          }
          if( $token_pair == 'AVAXUSDT' ) {
            $coingecko_price->price = $coingecko_data['avalanche-2']['usd'];
          }
          if( $token_pair == 'FTMUSDT' ) {
            $coingecko_price->price = $coingecko_data['fantom']['usd'];
          }
          if( $token_pair == 'SOLUSDT' ) {
            $coingecko_price->price = $coingecko_data['solana']['usd'];
          }
          return $coingecko_price;
        }
    }

    public function search_coingecko_price($token_symbol) {
        $token_symbol = strtolower($token_symbol);
        $coingecko_price = (object) array();
        $coingecko_coin_id = null;

        $coingecko_data = wp_remote_retrieve_body( wp_remote_get('https://api.coingecko.com/api/v3/coins/list') );
        $coingecko_data = json_decode($coingecko_data, true);
        if( ! empty($coingecko_data) ) {
            foreach($coingecko_data as $coingecko_coin) {
                if( isset($coingecko_coin['symbol']) && $coingecko_coin['symbol'] == $token_symbol ) {
                    $coingecko_coin_id = $coingecko_coin['id'];
                    break;
                }
            }
        }
        if( ! empty($coingecko_coin_id) ) {
            $coin_id_request_url = 'https://api.coingecko.com/api/v3/simple/price?ids='.$coingecko_coin_id.'&vs_currencies=usd';
            $coin_price_data = wp_remote_retrieve_body( wp_remote_get($coin_id_request_url) );
            $coin_price_data = json_decode($coin_price_data, true);
            $coingecko_price->price = $coin_price_data[$coingecko_coin_id]['usd'];
        }
        return $coingecko_price;

    }

    public function check_custom_network_ratios() {
        $metapress_live_mode = get_option('metapress_live_mode', 0);
        $fiat_currency = 'usd';
        $metapress_supported_networks = apply_filters('filter_web3_access_networks', get_option('metapress_supported_networks'), 'add');
        if( ! empty($metapress_supported_networks) ) {
            foreach($metapress_supported_networks as $custom_network) {
                $this->set_ratio(esc_attr($custom_network['symbol']));
            }
        }
    }

    public function check_custom_token_ratios() {
        $metapress_live_mode = get_option('metapress_live_mode', 0);
        $fiat_currency = 'usd';
        $metapress_custom_tokens_list = get_option('metapress_custom_tokens_list', array());
        if( ! empty($metapress_custom_tokens_list) ) {
            foreach($metapress_custom_tokens_list as $custom_token) {
                $token_ratio_data = (object) array('price' => 0);

                if( isset($custom_token['binance_price_api']) && ! empty($custom_token['binance_price_api']) ) {
                    $token_pair = $custom_token['currency_symbol'].'USDT';
                    $token_ratio_data = $this->get_binance_price($token_pair);
                }

                if( ! isset($token_ratio_data->price) || $token_ratio_data->price <= 0 ) {
                    if( isset($custom_token['coingecko_price_api']) && ! empty($custom_token['coingecko_price_api']) ) {
                        $token_ratio_data = $this->get_coingecko_custom_token_price($custom_token, $fiat_currency);
                        if( ! isset($token_ratio_data->price) || $token_ratio_data->price <= 0 ) {
                            $token_ratio_data = $this->search_coingecko_price($custom_token['currency_symbol']);
                        }
                    }
                }

                if( ! isset($token_ratio_data->price) || $token_ratio_data->price <= 0 ) {
                    if( isset($custom_token['usd_price']) && ! empty($custom_token['usd_price']) ) {
                        $token_ratio_data = (object) array('price' => $custom_token['usd_price']);
                    }
                }

                $token_pair = $custom_token['currency_symbol'].$fiat_currency; // SET PAIR TO USD FOR PLUGIN CONSISTENCY
                $token_pair = strtoupper($token_pair);
                if( isset($token_ratio_data->price) && $token_ratio_data->price > 0 ) {
                    $token_ratio = 1 / $token_ratio_data->price;
                    if( $this->token_pair_exists($token_pair) ) {
                        $this->set_token_pair_ratio($token_pair, $token_ratio);
                    } else {
                        $this->token_ratios[$token_pair] = $token_ratio;
                        update_option('metapress_token_ratios', $this->token_ratios);
                    }
                }
            }
            update_option('metapress_token_ratios_updated_timestamp', current_time('timestamp', 1));
        }
    }

    public function get_coingecko_custom_token_price($token, $fiat_currency = 'usd') {
        $request_url = null;
        $coingecko_price = (object) array();
        if( isset($token['contract_address']) && ! empty($token['contract_address']) ) {
            $contract_address = $token['contract_address'];

            if( $token['network'] == 'mainnet' || $token['network'] == 'sepolia' ) {
                $request_url = 'https://api.coingecko.com/api/v3/simple/token_price/ethereum?contract_addresses='.$contract_address.'&vs_currencies='.$fiat_currency;
            }

            if( $token['network'] == 'maticmainnet' ||  $token['network'] == 'matictestnet' ) {
                $request_url = 'https://api.coingecko.com/api/v3/simple/token_price/polygon-pos?contract_addresses='.$contract_address.'&vs_currencies='.$fiat_currency;
            }

            if( $token['network'] == 'binancesmartchain' ||  $token['network'] == 'binancetestnet' ) {
                $request_url = 'https://api.coingecko.com/api/v3/simple/token_price/binance-smart-chain?contract_addresses='.$contract_address.'&vs_currencies='.$fiat_currency;
            }

            if( $token['network'] == 'avaxmainnet' ||  $token['network'] == 'avaxtestnet' ) {
                $request_url = 'https://api.coingecko.com/api/v3/simple/token_price/avalanche?contract_addresses='.$contract_address.'&vs_currencies='.$fiat_currency;
            }

            if( $token['network'] == 'fantomnetwork' ||  $token['network'] == 'fantomtestnet' ) {
                $request_url = 'https://api.coingecko.com/api/v3/simple/token_price/fantom?contract_addresses='.$contract_address.'&vs_currencies='.$fiat_currency;
            }

            if( ! empty($request_url) ) {
                $coingecko_data = wp_remote_retrieve_body( wp_remote_get($request_url) );
                $coingecko_data = json_decode($coingecko_data, true);
                if( isset($coingecko_data[$contract_address]['usd']) ) {
                  $coingecko_price->price = $coingecko_data[$contract_address]['usd'];
                }
            }
        }
        return $coingecko_price;
    }

    public function set_ratio($token_symbol) {
        $token_pair = $token_symbol.'USDT';
        $token_ratio_data = $this->get_binance_price($token_pair);
        if( ! isset($token_ratio_data->price) || $token_ratio_data->price <= 0 ) {
            $token_ratio_data = $this->get_coingecko_price($token_pair);
        }

        if( ! isset($token_ratio_data->price) || $token_ratio_data->price <= 0 ) {
            $token_ratio_data = $this->search_coingecko_price($token_symbol);
        }

        if( isset($token_ratio_data->price) && $token_ratio_data->price > 0 ) {
            $token_ratio = 1 / $token_ratio_data->price;
            if( $this->token_pair_exists($token_pair) ) {
                $this->set_token_pair_ratio($token_pair, $token_ratio);
            } else {
                $this->token_ratios[$token_pair] = $token_ratio;
                update_option('metapress_token_ratios', $this->token_ratios);
            }
            update_option('metapress_token_ratios_updated_timestamp', current_time('timestamp', 1));
        }
    }

    private function set_token_pair_ratio($token_pair, $ratio) {
        if( ! empty($this->token_ratios) ) {
            $this->token_ratios[$token_pair] = $ratio;
        }
        update_option('metapress_token_ratios', $this->token_ratios);
    }

    private function token_pair_exists($token_pair) {
        $token_pair_exists = false;
        if( ! empty($this->token_ratios) ) {
            if( isset($this->token_ratios[$token_pair]) && ! empty($this->token_ratios[$token_pair]) ) {
                $token_pair_exists = true;
            }
        }
        return $token_pair_exists;
    }

    public function get_ratio($token_pair) {
      $token_ratio = null;
      if( $this->token_pair_exists($token_pair)) {
          $token_ratio = $this->token_ratios[$token_pair];
      }
      return $token_ratio;
    }

    private function handle_error($error_message, $error_code) {
        return array(
            'error' => $error_message,
            'code'  => $error_code
        );
    }
}
$metapress_crypto_price_converter = new MetaPress_Crypto_Price_Converter();
