$('document').ready(function(){
    var table_member = $('#table-user-role').DataTable({  
        "searching": false,
        "order": [[0, 'asc']],
        "processing": true,
        "serverSide": true,
        "ajax" : {
            'url': appConfig.apiUri + "/settings/user_role"
        },
        "columns"     : [  
            { 
                "orderable": false,
                "data": "name"
            },
            { 
                "orderable": false,
                "data": "description"
            },
            { 
                "orderable": false,
                "data" : "id",
                "render": function(data, type, row, meta) {
                    var nameHtml = `<a href="${appConfig.baseUri}/settings/user_role/view/${data}" title="Klik disini untuk edit"
                            class="btn-edit" style=\"cursor:pointer\"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>`;
                    return nameHtml;
                } 
            }

        ]
    }); 

});