/** 
{
    product_id: 713,
    product_sku: '899799898990',
    product_name: 'Baju Adat Sunda',
    price: 110000,
    qty: 1,
    qty_unit: 'pc',
    subtotal: 110000,
    options: [
        {
            name: 'Apprrel Size',
            value: 'XL',
        },
        {
            name: 'Colors',
            value: 'Hitam',
        }
    ]
}
**/
Vue.component('return-from-storefront-item', {
    props: ['idx', 'product_name', 'product_sku', 
        'options', 'default_qty', 'qty_unit', 'price', 'subtotal', 'status'],
    data() {
        var app = this;
        return {
            qty: app.default_qty,
        }
    },
    watch: {
        qty: function(val) {
            var app = this;
            console.info('value: '+ val);
            if (app.qty >= 0) {
                this.$parent.sumSubtotal(app.idx, val);    
            }
            
        }
    },
    methods: {
        moneyFormat(amount) {
            return accounting.formatMoney(amount);
        },   
        calculate() {
            this.$parent.sumSubtotal(idx);
        },
        doRemove(idx) {
          this.$parent.doRemove(idx);  
        }     
    },
    template: `<tr>
                <td>{{ idx + 1}}</td>
                <td>
                    <span v-html="product_name"></span><br/>
                    <small>{{product_sku}}</small>
                    <ul style="margin-left: -24px;">
                        <li v-for="option in options">
                            <b>{{option.name}}: </b> {{option.value}}
                        </li>
                    </ul>
                </td>
                <td><input type="number" v-model="qty" min="0" class="text-right" size="5"/></td> 
                <td>{{qty_unit}}</td>
                <td>{{moneyFormat(price)}}</td>
                <td>{{moneyFormat(subtotal)}}</td>
                <td><a class="btn btn-danger" v-on:click="doRemove(idx)" v-bind:class="{'disabled' : status == 'void'}">-</a></td>
            </tr>`
});
/**
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
});**/

$('document').ready(function() {
    var catalog = $('#table-catalog-product').DataTable({  
        "searching": false,
        "lengthChange": true,
        "bInfo" : false,
        "order": [[1, 'asc']],
        "processing": true,
        "serverSide": true,
        "ajax" : {
            'url': appConfig.apiUri + "/product",
            'data': function(d) {
                d.name = $('input[name=term]').val();
            }            
        },
        "createdRow": function(row, data, dataIndex) {
            if (data.qty_unit == null) {
                $(row).addClass('danger');
            }
        },
        "columnDefs": [
            {className: "text-right", targets: [4]},
            {className: "text-right", targets: [5]},
            {className: "text-right", targets: [8]},
            {className: "text-right", targets: [9]},
            {className: "text-right", targets: [10]},
            {className: "text-right", targets: [11]},
        ],
        "columns"     : [  
            {
                "orderable": false,
                "data": "image",
                "render": function(data) {
                    return `<img src="${data}" class="img-thumbnail"/>`;
                }
            },
            {
                "orderable": false,
                "data": "model"
            },
            { 
                "data": "name",
                "render": function(data, type, row, meta) {
                    if (row.qty_unit == null) {
                        return `${data}<br/><small class="text-danger">empty</small>`
                    }

                    return `${data}<br/><small>${row.sku}</small>`
                }
            },
            {
                "orderable": false,
                "data": "qty_unit",
                "render": function(data, type, row, meta) {
                    if (data == null) {
                        return 'not set'
                    }

                    return data.symbol
                }                
            },            
            {
                "orderable": false,
                "data": "stocks.storagebin2"
            },   
            {
                "orderable": false,
                "data": "minimum",
                "render": function(data, type, row, meta) {
                    if (data == null) {
                        return 'not set'
                    }

                    return data
                }                
            },       
            {
                "orderable": false,
                "data": "maximum",
                "render": function(data, type, row, meta) {
                    if (data == null) {
                        return 'not set'
                    }

                    return data
                }                            
            },    
            {
                "orderable": false,
                "data": "moving_product_status",
                "render": function(data, type, row, meta) {
                    if (data == null) {
                        return 'not set'
                    }

                    return data
                }                                
            },               
            {
                "orderable": false,
                "data": "product_id",
                "render": function(data, type, row, meta) {
                    return 0;
                }                       
            }, 
            {
                "orderable": false,
                "data": "product_id",
                "render": function(data, type, row, meta) {
                    return 0;
                }                       
            },
            {
                "orderable": false,
                "data": "product_id",
                "render": function(data, type, row, meta) {
                    return 0;
                }                       
            },             
            {
                "orderable": false,
                "data": "product_id",
                "render": function(data, type, row, meta) {
                    return 0;
                }                       
            },
            {
                "orderable": false,
                "data": "product_id",
                "render": function(data, type, row, meta) {
                    var disabled = row.qty_unit == null ? "disabled" : "";
                    return `<input type="checkbox" name="checked_product" value="${data}" ${disabled}/>`;
                }
            }

        ]
    }); 

    $('#btn-filter-product').click(function(){
        console.info('redraw catalog');
        catalog.draw();
    });

    $('#btn-reset-filter-product').click(function(){
        $('input[name=term]').val('');
        $('input[name=minimum]').val('');        
        catalog.draw();
    });

    var form = new Vue({
        el: '#form-return-stock',
        data: {
            form_status: {
                alert: false,
                has_errors: false,
                success: false,
                message: 'Undefined message'
            },    
            return_from_storefront_id: null,
            return_from_storefront_no: 'N/A',
            receiver_name: null,
            return_from_storefront_items: [],
            total_qty: 0,
            total_amount: 0,
            created_at: null
        },
        watch: {
            return_from_storefront_items: function() {
                var app = this;
                app.total = parseFloat(0);
                _.each(app.return_from_storefront_items, function(elem, index, list){
                    app.total += parseFloat(elem.subtotal);
                });
            },
        },
        methods: {
            moneyFormat(amount) {
                return accounting.formatMoney(amount);
            },
            doRemove: function(idx) {
                if (confirm('Apakah anda yakin ?')) {
                    this.return_from_storefront_items.splice(idx, 1);
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
                /**
                var app = this;
                if (app.move_to_storefront_id == null) {
                    app.insert();
                } else {
                    app.update();
                }**/
                this.insert();
                
            },
            insert(){
                console.info('create return stock from store');
                var app = this;
                axios.post(appConfig.apiUri + '/store/return_stock', {
                    'created_at': app.created_at,
                    'receiver_name': app.receiver_name,
                    'return_from_storefront_items': app.return_from_storefront_items,
                    'total_amount': app.total_amount
                }).then(function(response) {
                    console.info('move to storefront has been saved');
                    var data = response.data.data;

                    app.return_from_storefront_id = data.return_from_storefront_id;
                    app.return_from_storefront_no = data.return_from_storefront_no;
                    
                    app.form_status.alert = true;
                    app.form_status.success = true;
                    app.form_status.has_errors = false;
                    app.form_status.message = `Data sudah berhasil disimpan dengan nomor: ${app.return_from_storefront_no}`;   
                    
                    setTimeout(function(){
                        window.location.replace(appConfig.baseUri + 'store/return_stock');
                    }, 3000);                    
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
            openCatalog() {
                $('#modal-catalog-product').modal({show: true});
            },
            sumSubtotal(idx, qty) {
                var app = this;
                var return_from_storefront_item = app.return_from_storefront_items[idx];
                return_from_storefront_item.qty = qty;
                return_from_storefront_item.subtotal = return_from_storefront_item.qty * return_from_storefront_item.price;

                app.total_amount = parseFloat(0);
                _.each(app.return_from_storefront_items, function(elem, index, list){
                    app.total_amount += parseFloat(elem.subtotal);
                });

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
        }
    });
    
    function refreshCatalog(callback) {
        $('input[name=term]').val('');
        catalog.draw();
        callback();
    }
     
    $("#get-data").click(function(){
        var productids = [];
        $.each($("input[name='checked_product']:checked"), function(){
            productids.push($(this).val());
        });
        axios.get(appConfig.apiUri + '/product_option_value', {
            params: {
                ids: productids.join()
            }
        }).then(function(response){
            _.each(response.data.data, function(product, key, list){
                var return_from_storefront_item = {
                    product_id: product.product_id,
                    product_sku: product.sku,
                    product_name: product.name,
                    price: product.price,
                    qty: 0,
                    qty_unit: product.qty_unit.symbol,
                    subtotal: product.price,
                    options: []
                };
                
                _.each(product.options, function(option){
                    return_from_storefront_item.options.push(option);
                });
                console.info(return_from_storefront_item);
                var exists = _.find(form.return_from_storefront_items, function(existing_return_item){
                    return return_from_storefront_item.product_id == existing_return_item.product_id 
                    && _.isEqual(existing_return_item.options, return_from_storefront_item.options);
                });
                console.info('exists? '+ exists);
                if (exists == undefined) {
                    form.return_from_storefront_items.push(return_from_storefront_item);    
                } else {
                    exists.qty = parseFloat(exists.qty) + parseFloat(return_from_storefront_item.qty);
                }
                
            });            
        })
        .catch(function(error){
            console.error(error);
            var message = error.response.data.message;
            if (error.response.data.code == 400) {
                message += '<ul>';
                _.each(error.response.data.errors, function(value, key, list){
                    message += `<li>${value}</li>`;
                });
                message += '</ul>';
            }

            if (error.response.data.code == 500) {
                message += 'Internal server error';
            }                    

            form.form_status.alert = true;
            form.form_status.has_errors = true;
            form.form_status.success = false;
            form.form_status.message = message;
        }).finally(function(){
            $("input[name='checked_product']").prop('checked', false);
            $('#modal-catalog-product').modal('toggle');
        });
    });    

});