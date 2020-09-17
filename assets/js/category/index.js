$('document').ready(function(){  
    const table_category = $('#table-category').DataTable({  
        "searching": true,
        "order": [[0, 'asc']],
        "processing": true,
        "serverSide": true,
        "ajax" : {
            'url': appConfig.apiUri + "/category"
        },
        "columns"     : [  
            { 
                "data": "name"
            },
            { 
                "data": "sort_order",
            },
            { 
                "orderable": false,
                "data" : "category_id",
                "render": function(data, type, row, meta) {
                    var nameHtml = `<a href="${appConfig.baseUri}/catalog/category/update/${data}" title="Klik disini untuk edit"
                            class="btn-edit" style=\"cursor:pointer\"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a> | `;
                    nameHtml += `<a data-id="${data}" class="btn-delete" style=\"cursor:pointer\"><i class="fa fa-trash-o" aria-hidden="true"></i></a>`; 
                    return nameHtml;
                } 
            }

        ]
    }); 

    $('#table-category tbody').on('click', '.btn-delete', function(){
        var conf = confirm('Apakah Anda Yakin ?');
        if (conf !== false) {
            const val = $(this).data('id');
            console.log(`attempting to delete value ${val}`);
            axios.delete(appConfig.apiUri + "/category/" + val)
                .then(function(response){
                    table_category.draw();
                }).catch(function(error) {
                    alert('Internal server error');
                });
        } 

    });

});
