$('document').ready(function(){  
    var table_member = $('#table_unit_convertion').DataTable({  
        "searching": true,
        "order": [[0, 'asc']],
        "processing": true,
        "serverSide": true,
        "ajax" : {
            'url': appConfig.apiUri + "/unit_convertion"
        },
        "columns"     : [  
            { 
                "data": "base_uom"
            },
            { 
                "data": "to_uom"
            },
            { 
                "orderable": false,
                "data": "multiply_rate",
                "render": function(data, type, row, meta) {
                    var value = parseFloat(data);
                    return `<span class="pull-right">${value}</span>`;
                }
            },
            /** 
            { 
                "orderable": false,
                "data": "divide_rate",
            },**/
            { 
                "orderable": false,
                "data" : "unit_convertion_id",
                "render": function(data, type, row, meta) {
                    var nameHtml = `<a href="${appConfig.baseUri}/catalog/unit_convertion/edit_form/${data}" title="Klik disini untuk edit"
                            class="btn-edit" style=\"cursor:pointer\"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>`;
                    return nameHtml;
                } 
            }

        ]
    }); 

});
