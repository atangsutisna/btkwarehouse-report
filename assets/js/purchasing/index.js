$('document').ready(function(){
    var table_storefront = $('#table-move-storefront').DataTable({  
        "searching": false,
        "order": [[3, 'desc']],
        "processing": true,
        "serverSide": true,
        "ajax" : {
            'url': appConfig.apiUri + '/purchasing',
            data(d) {
                d.start_date = $('input[name=start_date]').val();
                d.end_date =  $('input[name=end_date]').val();
                d.receiver_name = $('input[name=receiver_name]').val();
            }
        },
        "columns"     : [  
            { 
                "orderable": false,
                "data": "purchasing_no",
            },
            { 
                "orderable": false,
                "data": "goods_receipt_no",
            },
            { 
                "orderable": false,
                "data": "supplier_name",
            },
            { 
                "data": "created_at",
                "render": function(data, type, row, meta) {
                    var html = moment(data).format('lll');
                    return html;
                }                
            },
            { 
                "orderable": false,
                "data": "total",
                "render": function(data) {
                    var total = accounting.formatMoney(data);
                    return `<span class="pull-right">${total}</span>`;
                }
            },
            { 
                "orderable": false,
                "data" : "purchasing_id",
                "render": function(data, type, row, meta) {
                    var nameHtml = `<a href="${appConfig.baseUri}/purchasing/view/${data}" title="View"
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