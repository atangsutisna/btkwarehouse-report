$('document').ready(function(){  
    var table_member = $('#table_member').DataTable({  
        "searching": true,
        "order": [[0, 'desc']],
        "processing": true,
        "serverSide": true,
        "ajax" : {
            'url': app_config.api_uri + "/member"
        },
        "columns"     : [  
            { 
                "data": "modification_time",
                "render": function(data, type, row, meta) {
                    return moment(data).format('lll');
                }
            },
            { 
                "orderable": false,
                "data": "nim",
            },
            { 
                "data": "nama"
            },
            { 
                "orderable": false,
                "data": "jk",
            },
            { 
                "orderable": false,
                "render": function(data, type, row, meta) {
                    var tgl_lahir = moment(row.tgl_lahir).format('ll');
                    return `${row.tempat_lahir}<br/><small>${tgl_lahir}</small>`;
                }
            },
            { 
                "orderable": false,
                "data" : "nim",
                "render": function(data, type, row, meta) {
                    var nameHtml = `[
                        <a  href="${app_config.base_uri}/member/view/${data}"
                            class="btn-edit" style=\"cursor:pointer\">Edit</a>]`;
                    return nameHtml;
                } 
            }

        ]
    }); 

    $('#table_member tbody').on('click', '.btn-delete', function(){
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
                    table_member.draw();
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
