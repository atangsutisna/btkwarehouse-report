$('document').ready(function(){  
    var table_product = $('#table-price-list').DataTable({  
        "order": [[3, 'desc']],
        "processing": true,
        "serverSide": true,
        "ajax" : {
            'url': appConfig.apiUri + "/product",
            'data': function(d) {
                d.supplier_id = $('#supplier-choices').val();
            }
        },
        "createdRow": function(row, data, dataIndex) {
            if (data.qty_unit == null) {
                $(row).addClass('danger');
            }
        },        
        "columns"     : [  
            {
                "orderable": false,
                "data": "image",
                "render": function(data) {
                    return `<img src="${data}" class="img-thumbnail"/>`;
                }
            },
            { 
                "data": "name",
                "render": function(data, type, row, meta) {
                    var drawHtml = `${data}<br/>${row.model}<br>`;
                    return drawHtml;
                }
            },
            { 
                "data": "price",
                "render": function(data) {
                    var base_price = accounting.formatMoney(data);
                    return `<span class="pull-right">${base_price}</span>`;
                }
            },
            { 
                "data": "date_modified",
                "render": function(data, type, row, meta) {
                    var html = moment(data).format('ll H:mm');
                    return html;
                }
            }
        ]
    }); 

    $('#btn-search').on('click', function(){
        table_product.draw();
        return false;
    });

    $('#supplier-choices').select2({
        ajax: {
            url: appConfig.apiUri + '/supplier',
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
                        id: raw.supplier_id,
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

    $('#btn-search').on('click', function(){
        table_product.draw();
        return false;
    });

    $('#btn-reset').on('click', function(){
        $('#supplier-choices').val(null).trigger('change');
        table_product.draw();
        
        return false;
    });

});
