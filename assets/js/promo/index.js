$('document').ready(function(){  
    const table_promo = $('#table-promo').DataTable({  
        "searching": false,
        "order": [[4, 'desc']],
        "processing": true,
        "serverSide": true,
        "ajax" : {
            'url': appConfig.apiUri + "/promo",
            'data': function(d) {
                d.name = $('input[name=name]').val();
                d.status = $('select[name=status]').val();
            }            
        },
        "columns"     : [  
            { 
                "orderable": false,
                "data": "name"
            },
            { 
                "orderable": false,
                "data": "start_from",
                "render": function(data, type, row, meta) {
                    return moment(data).format('ll');
                }                
            },          
                          { 
                "orderable": false,
                "data": "end_on",
                "render": function(data, type, row, meta) {
                    return moment(data).format('ll');
                }                
            },
            { 
                "orderable": false,
                "data": "status",
                "render": function(data, type, row, meta) {
                    return data.toUpperCase();
                }
            },            
            { 
                "data": "updated_at",
                "render": function(data, type, row, meta) {
                    var html = moment(data).format('ll H:mm');
                    return html;
                }
            },            
            { 
                "orderable": false,
                "data" : "id",
                "render": function(data, type, row, meta) {
                    var nameHtml = `<a href="${appConfig.baseUri}/promo/update/${data}" title="Klik disini untuk edit"
                            class="btn-edit" style=\"cursor:pointer\"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>`;
                    return nameHtml;
                } 
            }

        ]
    }); 

    $('#form_filter').submit(function(){
        console.log('submited form filter');
        table_promo.draw();
        return false;
    });

    $('#table-promo tbody').on('click', '.btn-delete', function(){
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

    $('#btn-reset').on('click', function(){
        $('input[name=name').val(null);
        $('select[name=status').val(null);
        table_promo.draw();
        
        return false;
    });

});
