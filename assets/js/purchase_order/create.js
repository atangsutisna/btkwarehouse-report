$('document').ready(function(){
    const form = new Vue({
        el: '#form-filter-catalog',
        data: {
            supplier_id: null,
            product_type: null,
            under_stock_minimum: false,
            stock_minus: false,
            available_stock: false,
            out_of_stock: false,
        },
        methods: {
            doFilter: function() {
                console.log('do filter');
                let app = this;
                let params = {};

                if (app.supplier_id !== null) {
                    params.supplier_id = app.supplier_id;
                }
                
                if (app.product_type !== null) {
                    params.product_type = app.product_type;
                }

                if (app.out_of_stock) {
                    params.out_of_stock = app.out_of_stock;
                }

                if (app.stock_minus) {
                    params.stock_minus = app.stock_minus;
                }

                if (app.available_stock) {
                    params.available_stock = app.available_stock;
                }

                if (app.under_stock_minimum) {
                    params.under_stock_minimum = app.under_stock_minimum;
                }

                if (Object.entries(params).length === 0) {
                    console.log('empty params');
                    alert('Silahkan pilih supplier');
                    return false;
                }

                $('#form-supplier-product').html(`<div class="alert alert-info alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-info"></i> Loading..</h4>
                Mohon tunggu, sedang mempersiapkan form produk
                </div>`);

                params = $.param(params);                
                axios.get(appConfig.baseUri + '/purchase_order/load_supplier_product?' + params)
                    .then(function(response) {
                    console.log('ready for render catalog');
                    $('#form-supplier-product').html(response.data);
                }).catch(function(error) {
                    console.error(error);
                }).finally(function(){
                    $('#table-supplier-product').DataTable({
                        "scrollY": "600px",
                        "scrollCollapse": true,
                        "paging": false,
                        "ordering": false,
                        "searching": false
                    });
                    //$('input[name=empty_stock]').prop('disabled', false);
                });                
            }
        }
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
        form.supplier_id = data.id;
    });    

    /** 
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
    }); **/  
    
    $('#moving-status-choices').select2().on('select2:select', async function(e) {
        const data = e.params.data;
        form.product_type = data.id;
    });    
});