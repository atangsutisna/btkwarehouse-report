$('document').ready(function(){  
    var table_product = $('#table_product').DataTable({  
        "searching": true,
        "order": [[9, 'desc']],
        "processing": true,
        "serverSide": true,
        "ajax" : {
            'url': appConfig.apiUri + "/product_variant",
            data(d) {
                d.price = $('input[name=price]').val();
                d.supplier_id = $('#supplier-choices').val();
                d.zero_price = $('#zero_price').is(':checked');
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
                    if (row.variants.length > 0) {
                        var variant = row.variants[0];
                        drawHtml += `( rasio: ${variant.qty_unit} ${variant.qty_rasio} )`;
                    }

                    return drawHtml;
                }
            },
            {
                /**harga pokok */
                "orderable": false,
                "data": "cost_of_goods_sold",
                "render": function(data, type, row, meta) {
                    var cost_of_goods_sold = accounting.formatMoney(data);
                    return `<span class="pull-right">${cost_of_goods_sold}</span>`;
                }
            },
            {
                "orderable": false,
                "data": "qty_on_hand",
                "render": function(data, type, row, meta) {
                    return `<div class="text-right">${data} ${row.qty_unit}</div>`;
                }
            },
            {
                /**harga pcs offline */
                "orderable": false,
                "data": "offline_price",
                "render": function(data, type, row, meta) {
                    var offline_price = accounting.formatMoney(data);
                    return `<span class="pull-right">${offline_price}</span>`;
                }
            },
            {
                /**harga rasio offline */
                "orderable": false,
                "data": "product_id",
                "render": function(data, type, row, meta) {
                    if (row.variants.length > 0) {
                        var variant = row.variants[0];
                        var variant_offline_price = accounting.formatMoney(variant.offline_price);
                        return `<span class="pull-right">${variant_offline_price}</span>`;    
                    }

                    var offline_price = accounting.formatMoney(0);
                    return `<span class="pull-right">${offline_price}</span>`;
                }
            },
            {
                /**harga pcs online */
                "orderable": false,
                "data": "online_price",
                "render": function(data, type, row, meta) {
                    var online_price = accounting.formatMoney(data);
                    return `<span class="pull-right">${online_price}</span>`;
                }
            },
            {
                /**harga rasio online */
                "orderable": false,
                "data": "product_id",
                "render": function(data, type, row, meta) {
                    if (row.variants.length > 0) {
                        var variant = row.variants[0];
                        var variant_online_price = accounting.formatMoney(variant.online_price);
                        return `<span class="pull-right">${variant_online_price}</span>`;    
                    }

                    var online_price = accounting.formatMoney(0);
                    return `<span class="pull-right">${online_price}</span>`;
                }
            },
            { 
                "orderable": false,
                "data": "status",
                "render": function(data) {
                    return data == 1 ? 'Enabled' : 'Disabled';
                }
            },
            { 
                "data": "date_modified",
                "render": function(data, type, row, meta) {
                    var html = moment(data).format('ll H:mm');
                    return html;
                }
            },
            { 
                "orderable": false,
                "data" : "product_id",
                "render": function(data, type, row, meta) {
                    var nameHtml = `<a href="#" title="Klik disini untuk edit harga" data-id="${data}" 
                        class="btn-edit" style="cursor:pointer"
                        data-toggle="modal" data-target="#form-product">
                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>`;

                    return nameHtml;
                } 
            }

        ]
    }); 

    $('#table_product tbody').on('click', '.btn-edit', function(event){
        const id = $(this).data('id');
        const $current = $(this);
        $(this).text('wait..');

        $.get(appConfig.apiUri + "/product_variant/" + id, function(data){
            $('input[name=product_id]').val(data.data.product_id);
            $('input[name=name]').val(data.data.name);
            $('input[name=model]').val(data.data.model);
            
            if (data.data.offline_price !== undefined) {
                $('input[name=offline_price_pcs]').val(parseFloat(data.data.offline_price));
            }

            if (data.data.online_price !== undefined) {
                $('input[name=online_price_pcs]').val(parseFloat(data.data.online_price));
            }

            if (data.data.qty_unit !== undefined && data.data.qty_unit !== null) {
                $('.base_uom').text(data.data.qty_unit);
            } else if (data.data.qty_unit == null) {
                $('.base_uom').text('?');
            }
            
            if (data.data.variants.length > 0) {
                $('.price-product-rasio').show();
                $('input[name=offline_price_rasio]').val(parseFloat(data.data.variants[0].offline_price));
                $('input[name=online_price_rasio]').val(parseFloat(data.data.variants[0].online_price));
                $('.rasio_uom').text(data.data.variants[0].qty_unit);
            } else {
                $('.price-product-rasio').hide();
                $('.rasio_uom').text('?');
            }
            
            $('#form-product').modal('show');
            $current.text('');
            $current.append('<i class="fa fa-pencil-square-o" aria-hidden="true"></i>');
        });
        
        return false;
    });

    $('#btn-update').click(function(){
        const productId = $('input[name=product_id]').val();
        const offline_price_pcs = $('input[name=offline_price_pcs]').val();
        const online_price_pcs = $('input[name=online_price_pcs]').val();

        const offline_price_rasio = $('input[name=offline_price_rasio]').val();
        const online_price_rasio = $('input[name=online_price_rasio]').val();

        $('#btn-update').text('Waitt..');
        $('#btn-update').attr('disabled', 'disabled');
        $.post(appConfig.apiUri + "/price_manager", {
            'product_id': productId,
            'offline_price_pcs': offline_price_pcs,
            'online_price_pcs': online_price_pcs,
            'offline_price_rasio': offline_price_rasio,
            'online_price_rasio': online_price_rasio,
        }, (success) => {
            table_product.draw();
            setTimeout(() => {
                $('#btn-update').text('Simpan');
                $('#btn-update').removeAttr('disabled');
                $('#form-product').modal('toggle');                
            }, 2000);
        });
    });
    /**
    $('#form-filter').submit(function(){
        table_product.draw();
        return false;
    }); **/
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
        $('input[name=price').val(null);
        $('#zero_price').prop('checked', false);
        table_product.draw();
        
        return false;
    });
    
});
$('#form-product').modal({show: false});
