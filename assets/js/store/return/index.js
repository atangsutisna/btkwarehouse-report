$('document').ready(function(){
    var table_return_stock = $('#table-return-stock').DataTable({  
        "searching": false,
        "order": [[2, 'desc']],
        "processing": true,
        "serverSide": true,
        "ajax" : {
            'url': appConfig.apiUri + '/store/return_stock',
            data(d) {
                d.start_date = $('input[name=start_date]').val();
                d.end_date =  $('input[name=end_date]').val();
                d.receiver_name = $('input[name=receiver_name]').val();
            }
        },
        "columns"     : [  
            { 
                "data": "created_at",
                "render": function(data, type, row, meta) {
                    var html = moment(data).format('ll');
                    return html;
                }                
            },
            { 
                "orderable": false,
                "data": "return_from_storefront_no",
            },
            { 
                "orderable": false,
                "data": "receiver_name",
            },            
            { 
                "orderable": false,
                "data": "total_amount",
                "render": function(data) {
                    var total_amount = accounting.formatMoney(data);
                    return `<span class="pull-right">${total_amount}</span>`;
                }
            },
            { 
                "orderable": false,
                "data" : "return_from_storefront_id",
                "render": function(data, type, row, meta) {
                    var nameHtml = `<a href="${appConfig.baseUri}/store/return_stock/view/${data}" title="View"
                    class="btn-edit" style=\"cursor:pointer\"><i class="fa fa-eye" aria-hidden="true"></i></a>`;

                    return nameHtml;
                } 
            }

        ]
    }); 

    $('#form-filter').submit(function(){
        table_storefront.draw();
        return false;
    });

    $('#btn-reset').on('click', function(){
        $('#supplier-choices').val(null).trigger('change');
        $('input[name=purchase_order_no').val(null);
        table_storefront.draw();
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
});