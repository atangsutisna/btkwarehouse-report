$('document').ready(function(){  
    var table_product = $('#table-price-label').DataTable({  
        "order": [[1, 'asc']],
        "processing": true,
        "serverSide": true,
        "ajax" : {
            'url': appConfig.apiUri + "/product",
            'data': function(d) {
                d.name = $('input[name=name]').val();
                d.model = $('input[name=model]').val();
                d.price = $('input[name=price]').val();
                d.qty = $('input[name=qty]').val();
                d.minimum = $('input[name=minimum]').val();
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
                "data": "product_id",
                "render": function ( data, type, row, meta ) {
                    return `<input type="checkbox" name="printed">`;
                }
            },
            { 
                "orderable": false,                
                "data": "model",
            },
            { 
                "data": "name",
                "render": function ( data, type, row, meta ) {
                    if (row.qty_unit == null) {
                        return `${data}<br/><small class="text-danger">Invalid product</small>`;
                    }
                    
                    return data;
                }
            },
            { 
                "data": "price",
                "render": function(data) {
                    var total_amount = accounting.formatMoney(data);
                    return `<span class="pull-right">${total_amount}</span>`;
                }
            },
            { 
                "orderable": false,                
                "data": "expiry_date",
            },            
            {
                "orderable": false,                
                "data": "product_id",
                "render": function(data) {
                    var nameHtml = `<a href="${appConfig.baseUri}price-label/print/${data}"
                            style=\"cursor:pointer\"><i class="fa fa-print" aria-hidden="true"></i></a>`;
                    return nameHtml;
                }
            }
        ]
    }); 

    $('#btn-search').on('click', function(){
        table_product.draw();
        return false;
    });


});
