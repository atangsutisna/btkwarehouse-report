$('document').ready(function(){
    $('#table-supplier-product').DataTable({
        "scrollY": "600px",
        "scrollCollapse": true,
        "paging": false,
        "ordering": false
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
    }).on('select2:select', async function(e) {
        const data = e.params.data;

        $('#form-supplier-product').html(`<div class="alert alert-info alert-dismissible">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        <h4><i class="icon fa fa-info"></i> Loading..</h4>
        Mohon tunggu, sedang mempersiapkan form produk
        </div>`);

        const empty_stock = $('input[name=empty_stock]').is(':checked');
        
        $('input[name=empty_stock]').prop('disabled', true);
        axios.get(appConfig.baseUri + '/purchase_order/load_supplier_product?supplier_id=' + data.id + '&empty_stock=' + empty_stock)
            .then(function(response) {
            //console.log(response);
            $('#form-supplier-product').html(response.data);
        }).catch(function(error) {
            console.error(error);
        }).finally(function(){
            $('#table-supplier-product').DataTable({
                "scrollY": "600px",
                "scrollCollapse": true,
                "paging": false,
                "ordering": false
            });
            $('input[name=empty_stock]').prop('disabled', false);
        });        
    });    

    $('input[name=empty_stock]').change(function() {
        const empty_stock = $(this).is(':checked');
        const supplier_choices = $('#supplier-choices').select2('data');

        $(this).prop('disabled', true);
        if (supplier_choices.length > 0) {
            $('#form-supplier-product').html(`<div class="alert alert-info alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h4><i class="icon fa fa-info"></i> Loading..</h4>
            Mohon tunggu, sedang mempersiapkan form produk
            </div>`);

            const supplier_id = supplier_choices[0].id;
            axios.get(appConfig.baseUri + '/purchase_order/load_supplier_product?supplier_id=' + supplier_id + '&empty_stock=' + empty_stock)
                .then(function(response) {
                //console.log(response);
                $('#form-supplier-product').html(response.data);
            }).catch(function(error) {
                console.error(error);
            }).finally(function(){
                $('#table-supplier-product').DataTable({
                    "scrollY": "600px",
                    "scrollCollapse": true,
                    "paging": false,
                    "ordering": false
                });

                $('input[name=empty_stock]').prop('disabled', false);
            });    
        }
    });     
});