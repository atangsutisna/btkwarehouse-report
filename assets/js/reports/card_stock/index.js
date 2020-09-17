$('#product-choices').select2({
    minimumInputLength: 3,
    ajax: {
        url: appConfig.apiUri + '/product',
        dataType: 'json',
        data: function(params) {
            return {
                name: params.term,
                page: params.page || 1,
                length: 25,
                draw: 1
            }
        },
        processResults: function(response) {
            var data = response.data.map(function(raw) {
                return {
                    id: raw.product_id,
                    text: raw.name
                }
            });

            return {
                results: data,
                pagination: {
                    more: true
                }
            }
        }                    
    }
}); 
