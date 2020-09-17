$('document').ready(function(){  
    var table_user = $('#table_user').DataTable({  
        "searching": true,
        "order": [[0, 'desc']],
        "processing": true,
        "serverSide": true,
        "ajax" : {
            'url': appConfig.apiUri + "/user"
        },
        "columns"     : [  
            { 
                "data": "created_on",
                "render": function(data, type, row, meta) {
                    return moment(data).format('ll');
                }
            },
            { 
                "orderable": false,
                "data": "username",
            },
            { 
                "orderable": false,
                "data": "id",
                "render": function(data, type, row, meta) {
                    return row.firstname+' '+ row.lastname;
                }
            },
            { 
                "orderable": false,
                "data": "status",
                "render": function(data, type, row, meta) {
                    return data == 1 ? 'Aktif' : 'Tidak Aktif';
                }                
            },
            { 
                "orderable": false,
                "data" : "id",
                "render": function(data, type, row, meta) {
                    var nameHtml = `<a href="${appConfig.baseUri}/user/update/${data}" title="Klik disini untuk edit"
                            class="btn-edit" style=\"cursor:pointer\"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>`;
                    return nameHtml;                                        
                } 
            }

        ]
    }); 

    $('#table_user tbody').on('click', '.btn-delete', function(){
        var conf = confirm('are you sure ?');
        if (conf !== false) {
            var val = $(this).data('id');
            $.ajax({
                url: app_config.api_uri + "/user",
                type: "DELETE",
                dataType: "json", // expected format for response              
                jsonp: false,
                data: {uid: val},
                beforeSend: function() {
                },
                complete: function() {
                },
                success: function(data) {
                    table_user.draw();
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    if (jqXHR.status == 400) {
                        var response = JSON.parse(jqXHR.responseText);
                        alert('Error: '+ response.message);
                    } 

                    if (jqXHR.status == 500) {
                        alert('Internal server error');
                    }
                },
            });
        } 

    });

});
