$('document').ready(function(){  
    var table_goods_receipt = $('#table_goods_receipt').DataTable({  
        "searching": false,
        "order": [[3, 'desc']],
        "processing": true,
        "serverSide": true,
        "ajax" : {
            'url': appConfig.apiUri + "/goods_receipt",  
            'data' : function(d) {
                d.supplier_id = $('#supplier-choices').val();
                d.goods_receipt_no = $('#goods_receipt_no').val();
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
            }
        },
        "columns"     : [  
            {
                "orderable": false,
                "data": "goods_receipt_no",
            },
            {
                "orderable": false,
                "data": "purchase_order_no",
            },
            { 
                "orderable": false,
                "data" : "supplier_name",
            },
            { 
                "data": "updated_at",
                "render": function(data, type, row, meta) {
                    return moment(data).format('lll');
                }
            },
            { 
                "orderable": false,
                "data" : "status",
                "render": function(data, type, row, meta) {
                    return data.toUpperCase();
                }
            }, 
            { 
                "orderable": false,
                "data" : "goods_receipt_id",
                "render": function(data, type, row, meta) {
                    var nameHtml = `<a href="${appConfig.baseUri}goods_receipt/view/${data}"
                            class="btn-edit" style=\"cursor:pointer\"><i class="fa fa-eye" aria-hidden="true"></i></a>`;
                    return nameHtml;
                } 
            }                     
        ]
    }); 

    $('#btn-filter').on('click', function(){
        table_goods_receipt.draw();
        return false;
    });

    $('#btn-reset').on('click', function(){
        $('#supplier-choices').val(null).trigger('change');
        $('input[name=goods_receipt_no').val(null);
        $('input[name=start_date').val(null);
        $('input[name=end_date').val(null);
        table_goods_receipt.draw();

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