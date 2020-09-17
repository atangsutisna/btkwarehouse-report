$('document').ready(function(){  
    var table_unit_measurement = $('#table_unit_measurement').DataTable({  
        "searching": true,
        "order": [[0, 'asc']],
        "processing": true,
        "serverSide": true,
        "ajax" : {
            'url': appConfig.apiUri + "/unit_measurement"
        },
        "columns"     : [  
            { 
                "data": "name"
            },
            { 
                "orderable": false,
                "data": "description",
            },
            { 
                "orderable": false,
                "data": "symbol",
            },
            { 
                "orderable": false,
                "data": "status",
                "render": function(data, type, row, meta) {
                    return data == 1 ? 'ACTIVE' : 'NONACTIVE';
                }
            },
            { 
                "orderable": false,
                "data" : "unit_measurement_id",
                "render": function(data, type, row, meta) {
                    var nameHtml = `<a href="${appConfig.baseUri}/catalog/unit_measurement/edit_form/${data}" title="Klik disini untuk edit"
                            class="btn-edit" style=\"cursor:pointer\"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a> | `;
                    nameHtml += `<a data-id="${data}" class="btn-delete" style=\"cursor:pointer\"><i class="fa fa-trash-o" aria-hidden="true"></i></a>`;                            
                    return nameHtml;
                } 
            }

        ]
    }); 

    $('#table_unit_measurement tbody').on('click', '.btn-delete', function(){
        var conf = confirm('Apakah Anda Yakin?');
        if (conf !== false) {
            var val = $(this).data('id');
            console.info('Attempting to disable product with id: '+ val);
            axios.delete(appConfig.apiUri + "/unit_measurement/" + val)
                .then(function(response){
                    table_unit_measurement.draw();
                }).error(function(error) {
                    alert('Internal server error');
                });
        } 

    });


});
