$('document').ready(function(){
    var table_invbal = $('#table_invbalance').DataTable({  
        "searching": false,
        "order": [[5, 'desc']],
        "processing": true,
        "serverSide": true,
        "ajax" : {
            'url': appConfig.apiUri + "/invbalance",
            data(d) {
                d.product_name = $('input[name=product_name]').val();
                d.supplier_id = $('#supplier-choices').val();
            }
        },
        "columnDefs": [
            {className: "text-right", targets: [3]},
            {className: "text-center", targets: [5]},
        ],
        "columns"     : [  
            { 
                "orderable": false,
                "data": "product_image",
                "render": function(data) {
                    return `<img src="${data}" class="img-thumbnail"/>`;
                }
            },
            { 
                "orderable": false,
                "data": "product_name",
            },
            { 
                "orderable": false,
                "data": "supplier_name",
                "render": function(data, type, row, meta) {
                    return data !== null ? data : ' UNKNOWN';
                }
            },
            { 
                "orderable": false,
                "data": "qty",
                "render": function(data, type, row, meta) {
                    return `${data} ${row.qty_unit}`;
                }
            },
            { 
                "orderable": false,
                "data": "status",
                "render": function(data, type, row, meta) {
                    return data == 1 ? 'ACTIVE' : 'NONACTIVE'
                }
            },
            {
                "data": "updated_at",
                "render": function(data, type, row, meta) {
                    var html = moment(data).format('ll H:mm');
                    return html;
                }
            },
            /** 
            { 
                "orderable": false,
                "data" : "inventory_balance_id",
                "render": function(data, type, row, meta) {
                    var nameHtml = `<a href="${appConfig.baseUri}/invbalance/view/${data}" title="Klik disini untuk detail"
                            class="btn-view" style=\"cursor:pointer\"><i class="fa fa-eye" aria-hidden="true"></i></a>`;
                    return nameHtml;
                } 
            } **/

        ]
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
        table_invbal.draw();
        return false;
    });

    $('#btn-reset').on('click', function(){
        $('#supplier-choices').val(null).trigger('change');
        $('input[name=product_name').val(null);
        table_invbal.draw();
        
        return false;
    });

});