$('document').ready(function(){
    var table_po = $('#table_purchase_order').DataTable({  
        "searching": false,
        "order": [[4, 'desc']],
        "processing": true,
        "serverSide": true,
        "ajax" : {
            'url': appConfig.apiUri + '/purchase_order',
            data(d) {
                d.purchase_order_no = $('input[name=purchase_order_no]').val();
                d.supplier_id = $('#supplier-choices').val();
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
            }
        },
        "columns"     : [  
            { 
                "orderable": false,
                "data": "purchase_order_no"
            },
            { 
                "orderable": false,
                "data": "supplier_name",
            },
            { 
                "orderable": false,
                "data": "status",
                "render": function(data, type, row, meta) {
                    return data.toUpperCase();
                }
            },
            { 
                "data": "created_at",
                "render": function(data, type, row, meta) {
                    var html = moment(data).format('ll H:mm');
                    return html;
                }
            },
            { 
                "data": "updated_at",
                "render": function(data, type, row, meta) {
                    var html = moment(data).format('ll H:mm');
                    return html;
                }
            },
            { 
                "orderable": false,
                "data" : "purchase_order_id",
                "render": function(data, type, row, meta) {
                    var nameHtml = `<a href="${appConfig.baseUri}/purchase_order/view/${data}" title="View"
                    class="btn-edit" style=\"cursor:pointer; margin-right: 10px;\"><i class="fa fa-eye" aria-hidden="true"></i> </a>`;

                    if (row.status == 'draft' || row.status == 'ordered') {
                        nameHtml += `<a href="${appConfig.baseUri}/purchase_order/update/${data}" title="Edit"
                        class="btn-edit" style=\"cursor:pointer; margin-right: 10px;\"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>`;

                        nameHtml += `<a data-id="${data}" title="Hapus"
                        class="btn-delete" style=\"cursor:pointer\"><i class="fa fa-trash-o" aria-hidden="true"></i></a>`;
                    }
                    
                    return nameHtml;
                } 
            }

        ]
    }); 

    $('#btn-search').on('click', function(){
        table_po.draw();
        return false;
    });

    $('#btn-reset').on('click', function(){
        $('#supplier-choices').val(null).trigger('change');
        $('input[name=purchase_order_no').val(null);
        $('input[name=start_date').val(null);
        $('input[name=end_date').val(null);
        table_po.draw();
        
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

    $('#table_purchase_order tbody').on('click', '.btn-delete', function(){
        var conf = confirm('Apakah anda yakin ?');
        if (conf !== false) {
            var val = $(this).data('id');
            console.info('Attempting to disable remove purchase order with id: '+ val);
            axios.delete(appConfig.apiUri + "/purchase_order/" + val)
                .then(function(response){
                    table_po.draw();
                }).error(function(error) {
                    alert('Internal server error');
                });
        } 

    });

});