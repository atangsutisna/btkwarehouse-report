

Vue.component('option-choices', {
    props: ['option'],
    data(){
        return {
            name: this.option.name,
            value: null,
        }
    },
    watch: {
        value(new_value) {
            console.info(`${this.name}, ${new_value}`);
            this.$parent.selectOption({
                name: this.name,
                value: new_value
            });
        }
    },
    template: `<div class="row" style="margin-top: 10px;">
                <div class="col-5 col-lg-5 col-sm-5" style="padding-right: 0;">
                    <p class="form-control-static">{{option.name}}</p>
                </div>
                <div class="col-5 col-lg-5 col-sm-5" style="padding-right: 0;">
                    <select class="form-control" v-model="value">
                        <option v-bind:value="value" v-for="value in option.values">{{value}}</option>
                    </select>
                </div>
            </div>`
});

$('document').ready(function() {
    var catalog = $('#table-catalog-retur').DataTable({  
        "searching": false,
        "lengthChange": false,
        "bInfo" : false,
        "order": [[1, 'asc']],
        "processing": true,
        "serverSide": true,
        "ajax" : {
            'url': appConfig.apiUri + "/product",
            'data': function(d) {
                d.supplier_id = $('input[name=supplier_id]').val();
            }
        },
        "columns"     : [  
            {
                "orderable": false,
                "data": "image",
                "render": function(data) {
                    return `<img src="${data}" class="img-thumbnail"/>`;
                }
            },
            { 
                "data": "name",
                "render": function(data, type, row, meta) {
                    return `${data}<br/><small>${row.sku}</small>`
                }
            },
            { 
                "orderable": false,
                "data": "quantity",
                "render": function(data) {
                    return `<span class="pull-right">${data}</span>`;
                }
            },
            {
                "orderable": false,
                "data": "product_id",
                "render": function(data, type, row, meta) {
                    return `<input type="checkbox" name="checked_product" value="${data}"/>`;
                }
            }

        ]
    }); 

    var form = new Vue({
        el: '#form-retur',
        data: {
            form_status: {
                alert: false,
                has_errors: false,
                success: false,
                message: 'Undefined message'
            },    
            form_errors: [],
            form_product: {
                product_id: null,
                product_sku: null,
                product_name: null,
                price: 0,
                qty: 0,
                qty_unit: 'N/A',
                subtotal: 0,
                options: [],
                selected_options: []
            },
            retur_id: null,
            retur_no: 'N/A',
            status: 'draft',
            supplier_id: null,
            supplier_name: null,
            created_at: null,
            order_details: [],
            total: 0
        },
        watch: {
            order_details: function() {
                var app = this;
                app.total = parseFloat(0);
                _.each(app.order_details, function(elem, index, list){
                    app.total += parseFloat(elem.subtotal);
                });
            },
            supplier_id: function() {
                var app = this;
                if (app.supplier_id == null) {
                    $('#product-choices').prop('disabled', true);
                } else {
                    //console.info('show modal catalog');
                    //$('#modal-product-catalog').modal({show: true});
                }
            }
        },
        methods: {
            moneyFormat(amount) {
                return accounting.formatMoney(amount);
            },
            hasErrors(field) {
                var app = this;
                var exists = _.find(app.form_errors, function(error){
                    return field == error.field;
                });

                return exists == undefined ? false : true;
            },
            selectOption(option) {
                this.form_product.selected_options.push(option);
            },
            doRemove: function(idx) {
                if (confirm('Apakah anda yakin ?')) {
                    this.order_details.splice(idx, 1);
                }  
            }, 
            addOrderDetails() {
                if (this.form_product.product_id !== null && this.form_product.options.length > 0) {
                    if (this.form_product.options.length !== this.form_product.selected_options.length) {   
                        alert('Produk belum lengkap');
                        return false;
                    }
                }

                var order_row = Object.assign({}, this.form_product);
                order_row.subtotal = parseFloat(order_row.price) * parseFloat(order_row.qty);
                this.order_details.push(order_row);
                this.form_product.product_id = null;
                this.form_product.product_sku = null;
                this.form_product.product_name = null;
                this.form_product.price = 0;
                this.form_product.qty = 0;
                this.form_product.qty_unit = 'N/A';
                this.form_product.subtotal = 0;
                this.form_product.options = [];
                this.form_product.selected_options = [];

                $('#product-choices').val(null).trigger('change');
            },
            save() {
                $('#btn-save').attr('disabled', 'disabled');
                var app = this;
                if (app.purchase_order_id == null) {
                    app.insert();
                } else {
                    app.update();
                }
                
            },
            insert(){
                var app = this;
                axios.post(appConfig.apiUri + '/purchase_order', {
                    'supplier_id': app.supplier_id,
                    'supplier_name': app.supplier_name,
                    'order_details': app.order_details,
                    'status': app.status,
                    'created_at': app.created_at,
                    'total': app.total
                }).then(function(response) {
                    console.info('new purchase order has been saved');
                    var data = response.data.data;
                    console.info(data);
                    app.purchase_order_id = data.purchase_order_id;
                    app.purchase_order_no = data.purchase_order_no;

                    app.form_status.alert = true;
                    app.form_status.success = true;
                    app.form_status.message = `Data sudah berhasil disimpan dengan nomor: ${app.purchase_order_no}`;    
                }).catch(function(error) {
                    console.error(error);
                    var message = error.response.data.message;
                    if (error.response.data.code == 400) {
                        var message = '<ul>';
                        _.each(error.response.data.errors, function(value, key, list){
                            message += `<li>${value}</li>`;
                        });
                        message += '</ul>';
                    }

                    if (error.response.status == 500) {
                        message = 'Internal server error';
                    }                    
    
                    app.form_status.alert = true;
                    app.form_status.has_errors = true;
                    app.form_status.success = false;
                    app.form_status.message = message;        
                }).finally(function() {
                    $('#btn-save').removeAttr('disabled');
                });                
            },
            update() {
                var app = this;
                console.info('Requesting to update purchase-order with id: '+ app.purchase_order_id);
                axios.put(appConfig.apiUri + '/purchase_order', {
                    'purchase_order_id': app.purchase_order_id,
                    'supplier_id': app.supplier_id,
                    'supplier_name': app.supplier_name,
                    'created_at': app.created_at,
                    'order_details': app.order_details,
                    'status': app.status,
                    'total': app.total
                }).then(function(response) {
                    console.info('new purchase order has been saved');
                    var data = response.data.data;
                    app.purchase_order_id = data.purchase_order_id;
                    app.purchase_order_no = data.purchase_order_no;

                    app.form_status.alert = true;
                    app.form_status.success = true;
                    app.form_status.has_errors = false;
                    app.form_status.message = `Data sudah berhasil diperbaharui`;    
                }).catch(function(error) {
                    console.error(error);
                    var message = error.response.data.message;
                    if (error.response.data.code == 400) {
                        var message = '<ul>';
                        _.each(error.response.data.errors, function(value, key, list){
                            message += `<li>${value}</li>`;
                        });
                        message += '</ul>';
                    }

                    if (error.response.data.code == 500) {
                        message += 'Internal server error';
                    }                    
    
                    app.form_status.alert = true;
                    app.form_status.has_errors = true;
                    app.form_status.success = false;
                    app.form_status.message = message;        
                }).finally(function() {
                    $('#btn-save').removeAttr('disabled');
                });  
            },
            cancel() {
                $('#btn-delete').attr('disabled', 'disabled');
                var app = this;
                axios.delete(appConfig.apiUri + '/purchase_order/'+ app.purchase_order_id)
                    .then(function(response){
                        app.status = 'void';

                        app.form_status.alert = true;
                        app.form_status.success = true;
                        app.form_status.has_errors = false;
                        app.form_status.message = `Data sudah berhasil diperbaharui`;    
                    }).catch(function(error){
                        var message = error.response.data.message;
                        if (error.response.data.code == 500) {
                            message += 'Internal server error';
                        }                    
                        
                        app.form_status.alert = true;
                        app.form_status.has_errors = true;
                        app.form_status.success = false;
                        app.form_status.message = message;    
                    }).finally(function(){
                        $('#btn-delete').removeAttr('disabled');
                    });
            },
            print() {
                alert('Sory, this feature is undercontruction');
                return false;
            },
            openCatalogRetur() {
                catalog.draw();
                $('#modal-product-catalog-retur').modal({show: true});
            }   
        },
        created() {
            var app = this;
            app.purchase_order_id = $('input[name=purchase_order_id]').val();

            async function get_by_id(id) {
                var purchase_order = await axios.get(appConfig.apiUri + '/purchase_order/' + id);
                app.purchase_order_no = purchase_order.data.data.purchase_order_no;
                app.supplier_id = purchase_order.data.data.supplier_id;
                app.supplier_name = purchase_order.data.data.supplier_name;
                
                _.each(purchase_order.data.data.order_details, function(elem, key, list){
                    app.order_details.push(elem);
                });    
                
                app.status = purchase_order.data.data.status;
                app.created_at = purchase_order.data.data.purchase_order_date;
            }

            if (app.purchase_order_id !== undefined 
                && app.purchase_order_id !== null && app.purchase_order_id !== '') {
                console.info(app.purchase_order_id);
                get_by_id(app.purchase_order_id);
            }

            console.info(app.order_details);
        },
        mounted(){
            $('#product-choices').prop('disabled', true);
            var app = this;
            if (app.purchase_order_id !== undefined 
                && app.purchase_order_id !== null && app.purchase_order_id !== '') {
                $('#supplier-choices').prop('disabled', true);
            }
        },
        updated() {
            var app = this;
            if (app.order_details.length == 0 && app.supplier_id !== null) {
                catalog.draw();
                $('#modal-product-catalog').modal({show: true});
                $('#supplier-choices').prop('disabled', true);
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
        var data = e.params.data;
        form.supplier_id = data.id;
        form.supplier_name = data.text;
        //console.info(form.supplier_id);
        //$('#btn-product-catalog').click();
    });

    $('#product-choices').select2({
        placeholder: 'Pilih Produk',
        ajax: {
            url: appConfig.apiUri + '/product',
            dataType: 'json',
            data: function(params) {
                return {
                    supplier_id: form.supplier_id,
                    page: params.page || 1,
                    count: 25,
                }
            },
            processResults: function(response) {
                var data = response.data.map(function(raw) {
                    return {
                        id: raw.product_id,
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
    }).on('select2:select', function(e) {
        var data = e.params.data;

        async function get_product(product_id) {
            var product = await axios.get(appConfig.apiUri + '/product/' + product_id);

            form.form_product.product_id = product.data.data.product_id;
            form.form_product.product_sku = product.data.data.sku;
            form.form_product.product_name = product.data.data.name;
            form.form_product.price = parseFloat(product.data.data.price);
            form.form_product.qty = 1;
            form.form_product.qty_unit = product.data.data.qty_unit.symbol;
            form.form_product.options = [];

            _.each(product.data.data.product_option, function(elem, key, list){
                var option = {
                    name: elem.name,
                    values: [],
                }
                _.each(elem.values, function(opt_value, opt_key, opt_list){
                    if (opt_value.checked == true) {
                        option.values.push(opt_value.option_value_description);
                    }
                });

                form.form_product.options.push(option);
            });

            console.info(form.form_product);
        }
        
        get_product(data.id);
    })
     
    $("#get-data").click(function(){
        var productids = [];
        $.each($("input[name='checked_product']:checked"), function(){
            productids.push($(this).val());
        });
        axios.get('http://btk-warehouse.localhost/api/product_option_value', {
            params: {
                ids: productids.join()
            }
        }).then(function(response){
            _.each(response.data.data, function(product, key, list){
                var order_item = {
                    product_id: product.product_id,
                    product_sku: product.sku,
                    product_name: product.name,
                    price: product.price,
                    qty: product.minimum_order,
                    qty_unit: 'pc',
                    subtotal: product.price,
                    options: []
                };
                
                _.each(product.options, function(option){
                    order_item.options.push(option);
                });
                console.info(order_item);
                var exists = _.find(form.order_details, function(existing_order_item){
                    return order_item.product_id == existing_order_item.product_id 
                    && _.isEqual(existing_order_item.options, order_item.options);
                    
                });
                console.info('exists? '+ exists);
                if (exists == undefined) {
                    form.order_details.push(order_item);    
                } else {
                    exists.qty = parseFloat(exists.qty) + parseFloat(order_item.qty);
                }
                
            });
            
            $("input[name='checked_product']").prop('checked', false);
            $('#modal-product-catalog-retur').modal('toggle');
        })
        .catch(function(error){
            console.error(error);
            console.error('ada error');
        });
    });    

});