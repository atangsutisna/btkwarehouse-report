$('document').ready(function(){
    var table_retur = $('#table_retur').DataTable({  
        "searching": false,
        "order": [[2, 'desc']],
        "processing": true,
        "serverSide": true,
        "ajax" : {
            'url': appConfig.apiUri + '/retur',
            data(d) {
                d.retur_no = $('input[name=retur_no]').val();
                d.supplier_id = $('#supplier-choices').val();
            }
        },
        "columns"     : [  
            { 
                "orderable": false,
                "data": "retur_no"
            },
            { 
                "data": "supplier_name",
            },
            { 
                "data": "created_at",
                "render": function(data, type, row, meta) {
                    var html = moment(data).format('ll H:mm');
                    return html;
                }
            },
            { 
                "orderable": false,
                "data": "status",
                "render": function(data) {
                    return data.toUpperCase();
                }
            },
            { 
                "orderable": false,
                "data" : "retur_id",
                "render": function(data, type, row, meta) {
                    var nameHtml = `<a href="${appConfig.baseUri}/retur/view/${data}" title="View"
                    class="btn-edit" style=\"cursor:pointer\" ><i class="fa fa-eye" aria-hidden="true"></i></a>`;

                    return nameHtml;
                } 
            }

        ]
    }); 

    $('#btn-search').on('click', function(){
        table_retur.draw();
        return false;
    });

    $('#btn-reset').on('click', function(){
        $('#supplier-choices').val(null).trigger('change');
        $('input[name=retur_no').val(null);
        table_retur.draw();
        return false;
    });

    $('#supplier-choices').select2({
        ajax: {
            url: appConfig.apiUri + '/supplier',
            dataType: 'json',
            data: function(params) {
                return {
                    name: params.term,
                    page: params.page || 1,
                    length: 25,
                    draw: 1
                }
            },
            processResults: function(response) {
                var data = response.data.map(function(raw) {
                    return {
                        id: raw.supplier_id,
                        text: raw.name
                    }
                });

                return {
                    results: data,
                    pagination: {
                        more: true
                    }
                }
            }                    
        }
    });    
});