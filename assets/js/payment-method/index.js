$('document').ready(function() {
    var table_payment_method = $('#table-payment-method').DataTable({  
        "searching": true,
        "order": [[0, 'asc']],
        "processing": true,
        "serverSide": true,
        "ajax" : {
            'url': appConfig.apiUri + "/payment_method"
        },
        "columns"     : [  
            { 
                "orderable": true,
                "data": "payment_method_name"
            },
            { 
                "orderable": false,
                "data": "payment_method_description"
            },

            { 
                "orderable": false,
                "data": "status",
                "render": function(data, type, row, meta) {
                    return data.toUpperCase();
                }
            },
            { 
                "orderable": false,
                "data" : "payment_method_id",
                "render": function(data, type, row, meta) {
                    var nameHtml = `<a href="${appConfig.baseUri}payment_method/update/${data}" title="Klik disini untuk edit"
                            class="btn-edit" style=\"cursor:pointer\"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a> | `;
                    nameHtml += `<a data-id="${data}" class="btn-delete" style=\"cursor:pointer\"><i class="fa fa-trash-o" aria-hidden="true"></i></a>`;
                    return nameHtml;
                } 
            }

        ]
    }); 

    $('#table-payment-method tbody').on('click', '.btn-delete', function(){
        var conf = confirm('Apakah Anda Yakin?');
        if (conf !== false) {
            var val = $(this).data('id');
            console.info('Attempting to disable product with id: '+ val);
            axios.delete(appConfig.apiUri + "/payment_method/" + val)
                .then(function(response){
                    table_payment_method.draw();
                }).error(function(error) {
                    alert('Internal server error');
                });
        } 

    });

});