$('document').ready(function(){  
    /** 
    var table_product = $('#price-histories').DataTable({  
        "order": [[5, 'desc']],
        "processing": true,
        "serverSide": true,
        "searching": false,
        "ajax" : {
            'url': appConfig.apiUri + "/pricehistories",
            'data': function(d) {
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
                "data": "product_name",
                "render": function(data, type, row, meta) {
                    var drawHtml = `${data}<br/>${row.product_model}<br>`;
                    return drawHtml;
                }
            },
            {
                "orderable": false,
                "data": "old_price",
                "render": function(data, type, row, meta) {
                    var initial_price = accounting.formatMoney(0);
                    return `<span class="pull-right">${initial_price}</span>`;
                }
            },
            {
                "orderable": false,
                "data": "price",
                "render": function(data, type, row, meta) {
                    var price = accounting.formatMoney(data);
                    return `<span class="pull-right">${price}</span>`;
                }
            },
            {
                "orderable": false,
                "data": "qty_unit",
            },
            { 
                "data": "created_at",
                "render": function(data, type, row, meta) {
                    return moment(data).format('lll');
                }
            },
            { 
                "orderable": false,
                "data": "price_adjustment_id",
                "render": function(data, type, row, meta) {
                    return `<input type="checkbox" name="product_ids[${data}] value="${data}"/>`;
                }
            }
        ]
    }); 

    $('#btn-search').on('click', function(){
        table_product.draw();
        return false;
    });
    **/
   $('#price-histories').DataTable({
        "scrollY": "600px",
        "scrollCollapse": true,
        "paging": false,
        "ordering": false,
        "searching": false
    });


});
