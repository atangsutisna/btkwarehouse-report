$('document').ready(function(){  
    var table_stock_adjustment = $('#table_stock_adjustment').DataTable({  
        "searching": false,
        "order": [[0, 'desc']],
        "processing": true,
        "serverSide": true,
        "ajax" : {
            "url": appConfig.apiUri + "/stock_adjustment",  
            "data" : function(d) {
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
            }
        },
        "columns"     : [  
            { 
                "data": "created_at",
                "render": function(data, type, row, meta) {
                    return moment(data).format('ll H:mm');
                }
            },
            { 
                "data": "product_name",
                "render": function(data, type, row, meta) {
                    var drawHtml = `${data}<br/>${row.product_model}<br>`;
                    return drawHtml;
                }
            },
            { 
                "orderable": false,
                "data" : "status_adjust",
                "render": function(data, type, row, meta) {
                    if (data == 'Penambahan') {
                        return `<span class="label label-info">${data}</span>`;
                    }

                    return `<span class="label label-danger">${data}</span>`;
                }
            },  
            { 
                "orderable": false,
                "data" : "original_stock",
                "render": function(data, type, row, meta) {
                    return `<span class="pull-right">${data} ${row.qty_unit}</span>`;
                }
            },
            { 
                "orderable": false,
                "data" : "stock_adjust",
                "render": function(data, type, row, meta) {
                    return `<span class="pull-right">${data} ${row.qty_unit}</span>`;
                }
            },
            { 
                "orderable": false,
                "data" : "last_stock",
                "render": function(data, type, row, meta) {
                    return `<span class="pull-right">${data} ${row.qty_unit}</span>`;
                }
            }    
        ]
    }); 

    $('#btn-filter').on('click', function(){
        table_stock_adjustment.draw();
        return false;
    });

    $('#btn-reset').on('click', function(){
        $('input[name=start_date').val(null);
        $('input[name=end_date').val(null);
        table_stock_adjustment.draw();
        
        return false;
    });

});
