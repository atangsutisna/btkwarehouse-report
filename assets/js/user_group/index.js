$('document').ready(function(){  
    var table_user = $('#table-user-group').DataTable({  
        "searching": true,
        "order": [[0, 'desc']],
        "processing": true,
        "serverSide": true,
        "ajax" : {
            'url': appConfig.apiUri + "/user_group"
        },
        "columns"     : [  
            { 
                "orderable": false,
                "data": "name",
            },
            { 
                "orderable": false,
                "data" : "user_group_id",
                "render": function(data, type, row, meta) {
                    var nameHtml = `<a href="${appConfig.baseUri}/user_group/edit_form/${data}" title="Klik disini untuk edit"
                            class="btn-edit" style=\"cursor:pointer\"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>`;
                    return nameHtml;                                        
                } 
            }

        ]
    }); 

});
