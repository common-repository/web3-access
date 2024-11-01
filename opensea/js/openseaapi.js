
class MetaPress_OpenSea_API_Manager {
    constructor() {
      this.opensea_api_url = 'https://api.opensea.io/api/v1/assets';
      this.api_key = metapressopensea.api_key;
    }

    async get_assets(contract_address, owner_address, collection_slug) {
        let asset_request_url = this.opensea_api_url+'?asset_contract_addresses='+contract_address+'&owner='+owner_address+'&order_direction=desc&offset=0&limit=50';
        if( collection_slug && collection_slug.length > 0 ) {
            asset_request_url += '&collection='+collection_slug;
        }
        let asset_data = await jQuery.get({
            url: asset_request_url,
            headers: {"X-API-KEY": this.api_key},
            error: function(error) {
                metapress_show_ajax_error('Error retrieving OpenSea data');
            }
        });
        return asset_data;
    }
}

const metapress_opensea_api_manager = new MetaPress_OpenSea_API_Manager();
