$('document').ready(function(){  
    var table_product = $('#table_product').DataTable({  
        "searching": false,
        "order": [[3, 'desc']],
        "processing": true,
        "serverSide": true,
        "ajax" : {
            'url': appConfig.apiUri + "/product",
            'data': function(d) {
                d.supplier_id = $('#supplier-choices').val();
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
                "data": "image",
                "render": function(data) {
                    return `<img src="${data}" class="img-thumbnail"/>`;
                }
            },
            { 
                "data": "name",
                "render": function(data, type, row, meta) {
                    return `${data}<br/>${row.model}`;
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
                    var nameHtml = `<a href="${appConfig.baseUri}/catalog/product/edit_form/${data}" title="Klik disini untuk edit"
                            class="btn-edit" style=\"cursor:pointer\"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>`;
                    nameHtml += `<a data-id="${data}" class="btn-delete" style=\"cursor:pointer\"><i class="fa fa-trash-o" aria-hidden="true"></i></a>`;

                    return nameHtml;
                } 
            }

        ]
    }); 

    $('#table_product tbody').on('click', '.btn-delete', function(){
        var conf = confirm('are you sure ?');
        if (conf !== false) {
            var val = $(this).data('id');
            console.info('Attempting to disable product with id: '+ val);
            axios.delete(appConfig.apiUri + "/product/" + val)
                .then(function(response){
                    table_product.draw();
                }).error(function(error) {
                    alert('Internal server error');
                });
        } 

    });

    $('#form-filter').submit(function(){
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

    $('#btn-reset').on('click', function(){
        $('#supplier-choices').val(null).trigger('change');
        $('input[name=name]').val(null);
        $('input[name=model]').val(null);
        $('input[name=price]').val(null);
        $('input[name=qty]').val(null);
        $('input[name=minimum]').val(null);
        table_product.draw();

        return false;
    });

});
