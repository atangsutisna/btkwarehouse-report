$('document').ready(function(){
    var table_member = $('#table-transcode').DataTable({  
        "searching": true,
        "order": [[0, 'asc']],
        "processing": true,
        "serverSide": true,
        "ajax" : {
            'url': appConfig.apiUri + "/settings/transcode"
        },
        "columns"     : [  
        	{
        		"data": "seq_name"
        	},
            { 
                "data": "seq_group"
            },
            { 
                "orderable": false,
                "data" : "seq_name",
                "render": function(data, type, row, meta) {
                    var nameHtml = `<a href="${appConfig.baseUri}/settings/transcode/update/${data}" title="Klik disini untuk edit"
                            class="btn-edit" style=\"cursor:pointer\"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>`;
                    return nameHtml;
                } 
            }

        ]
    }); 

});