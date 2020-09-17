Vue.component('purchasing-items', {
    props: ['idx', 'purchasing_item'],   
    watch: {
        'purchasing_item.discount': function(val) {
            var app = this;
            app.purchasing_item.finalprice = app.purchasing_item.price - val;
            app.purchasing_item.subtotal = app.purchasing_item.finalprice * app.purchasing_item.qty;
            app.$parent.calculate();
        },
        'purchasing_item.price': function(val) {
            var app = this;
            app.purchasing_item.finalprice = val;
            app.purchasing_item.subtotal = app.purchasing_item.finalprice * app.purchasing_item.qty;
            app.$parent.calculate();
        }
    },
    methods: {
        formatRupiah(amount) {
            return accounting.formatMoney(amount);
        },   
        calculate() {
            console.info('calculate subtotal');
            var app = this;
            this.$parent.updatePrice(app.idx, app.updated_price);

            app.updated_price = app.updated_price.toString().replace(/(\d)(?=(\d{3})+(?:\.\d+)?$)/g, "$1\.");
        },
        checkDiscValue() {
            var app = this;
            if (app.purchasing_item.discount == '') {
                app.purchasing_item.discount = 0;
            }
        }
    },   
    template: `<tr>
                <td>{{idx + 1}}</td>
                <td class="col-sm-2 col-lg-2">
                    {{ purchasing_item.product_name }}<br/>
                    <small>{{ purchasing_item.product_model }}</small><br>
                </td>
                <td class="text-right">{{ purchasing_item.qty }}</td>
                <td>{{ purchasing_item.qty_unit }}</td>
                <td class="col-sm-1 col-lg-1">
                    <!-- harga awal -->
                    <span class="pull-right">
                        <input type="number" class="form-control text-right" v-model="purchasing_item.price"/>
                    </span>
                </td>
                <td class="col-sm-1 col-lg-1">
                    <!-- discount -->
                    <span class="pull-right">
                        <input type="number" class="form-control text-right" v-model="purchasing_item.discount" v-on:blur="checkDiscValue()"/>
                    </span>
                </td>
                <td class="col-sm-1 col-lg-1">
                    <!-- harga akhir -->
                    <span class="pull-right">
                        <input type="number" class="form-control text-right" v-model="purchasing_item.finalprice" disabled/>
                    </span>
                </td>
                <td class="col-sm-1 col-lg-1">
                    <!-- harga total -->
                    <span class="pull-right">
                        <input type="number" class="form-control text-right" v-model="purchasing_item.subtotal" disabled/>
                    </span>
                </td>
                <!-- offline -->
                <td class="col-sm-1 col-lg-1">
                    <!-- harga margin -->
                    <span class="pull-right">
                        <input type="number" class="form-control text-right" v-model="purchasing_item.offline_margin"/>
                    </span>
                </td>
                <td class="col-sm-1 col-lg-1">
                    <!-- harga satuan -->
                    <span class="pull-right">
                        <input type="number" class="form-control text-right" v-model="purchasing_item.offline_price_pcs"/>
                    </span>
                </td>
                <td class="col-sm-1 col-lg-1">
                    <!-- harga rasio -->
                    <span class="pull-right">
                        <input type="number" class="form-control text-right" v-model="purchasing_item.offline_price_rasio"/>
                    </span>
                </td>
                <!-- online -->
                <td class="col-sm-1 col-lg-1">
                    <!-- margin online -->
                    <span class="pull-right">
                        <input type="number" class="form-control text-right" v-model="purchasing_item.online_margin"/>
                    </span>
                </td>
                <td class="col-sm-1 col-lg-1">
                    <!-- harga satuan online -->
                    <span class="pull-right">
                        <input type="number" class="form-control text-right" v-model="purchasing_item.online_price_pcs"/>
                    </span>
                </td>
                <td class="col-sm-1 col-lg-1">
                    <!-- harga rasio online -->
                    <span class="pull-right">
                        <input type="number" class="form-control text-right" v-model="purchasing_item.online_price_rasio"/>
                    </span>
                </td>                
            </tr>`
});

$('document').ready(function() {
    var form = new Vue({
        el: '#form-purchasing',
        data: {
            form_status: {
                alert: false,
                has_errors: false,
                success: false,
                message: 'Undefined message'
            },
            goods_receipt_id: null,
            goods_receipt_no: null,
            supplier_id: null,
            supplier_name: null,
            payment_method: null,
            due_date: null,
            invoice_date: null,
            receive_date: null,
            taxable: false,
            discount_type: 'discount_amount',
            subtotal: 0,
            tax: 0,
            discount: 0,
            total: 0,
            note: null,
            purchasing_items: [],
        },
        watch: {
            purchasing_items: function(val) {
                var app = this;
                app.calculate();
            },
            taxable: function(val) {
                var app = this;
                app.calculate();
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
            doRemove: function(idx) {
                if (confirm('Apakah anda yakin ?')) {
                    this.move_to_storefront_items.splice(idx, 1);
                }  
            }, 
            save() {
                $('#btn-save').attr('disabled', 'disabled');
                this.insert();
            },
            insert(){
                var app = this;
                console.log(`appconfig api uri: ${appConfig.apiUri}`);
                axios.post(appConfig.apiUri + '/purchasing', {
                    goods_receipt_id: app.goods_receipt_id,
                    goods_receipt_no: app.goods_receipt_no,
                    supplier_id: app.supplier_id,
                    supplier_name: app.supplier_name,
                    payment_method: app.payment_method,
                    due_date: app.due_date,
                    invoice_date: app.invoice_date,
                    receive_date: app.receive_date,
                    taxable: app.taxable,
                    discount_type: 'discount_amount',
                    subtotal: app.subtotal,
                    tax: app.tax,
                    discount: app.discount,
                    total: app.total,
                    note: app.note,
                    purchasing_items: app.purchasing_items,        
                }).then(function(response) {
                    console.info('move to storefront has been saved');
                    const data = response.data.data;
                    
                    app.form_status.alert = true;
                    app.form_status.success = true;
                    app.form_status.has_errors = false;
                    app.form_status.message = `Data sudah berhasil disimpan dengan nomor: ${app.move_to_storefront_no}`;    

                    setTimeout(function(){
                        window.location.replace(appConfig.baseUri + '/purchasing');
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
            print() {
                alert('Sory, this feature is undercontruction');
                return false;
            },
            openGoodsReceipt() {
                $('#modal-goods-receipt').modal({show: true});
            },
            calculate() {
                var app = this;
                app.subtotal = parseFloat(0);
                app.discount = parseFloat(0);
                _.each(app.purchasing_items, function(elem, index, list){
                    app.subtotal += parseFloat(elem.subtotal);
                    app.discount += parseFloat(elem.discount);
                });     

                app.tax = 0;
                if (true == app.taxable) {
                    app.tax = 0.1 * app.subtotal;
                }
                
                app.total = app.subtotal + app.tax;
                app.total = app.total - app.discount;
            }            
        },
        created() {
            var app = this;
            console.log('form purchasing created');        
            app.invoice_date = moment().format('YYYY-MM-DD');
            app.receive_date = moment().format('YYYY-MM-DD');
            /** 
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
            **/
        },
    });

    const table_goods_receipt = $('#table-goods-receipt').DataTable({  
        "searching": true,
        "order": [[3, 'desc']],
        "processing": true,
        "serverSide": true,
        "bInfo" : false,
        "lengthChange": false,
        "ajax" : {
            'url': appConfig.apiUri + "/goods_receipt",  
            'data' : function(d) {
                d.status = 'draft';
                d.purchase_order_no = $('#purchase_order_no').val();
            }
        },
        "columns"     : [  
            {
                "orderable": false,
                "data": "goods_receipt_no",
            },
            {
                "orderable": false,
                "data": "purchase_order_no",
            },
            { 
                "orderable": false,
                "data" : "supplier_name",
            },
            { 
                "data": "updated_at",
                "render": function(data, type, row, meta) {
                    return moment(data).format('lll');
                }
            },
            { 
                "orderable": false,
                "data" : "status",
                "render": function(data, type, row, meta) {
                    return data.toUpperCase();
                }
            }, 
            { 
                "orderable": false,
                "data" : "goods_receipt_id",
                "render": function(data, type, row, meta) {
                    var nameHtml = `<a href="#"
                            class="btn-choose"
                            data-id="${data}"
                            style=\"cursor:pointer\">
                            [ PILIH ]
                            </a>`;
                    return nameHtml;
                } 
            }                     
        ]
    });

    $('#table-goods-receipt tbody').on('click', '.btn-choose', function(){
        var goods_receipt_id = $(this).data('id');
        axios.get(appConfig.apiUri +'/goods_receipt/' + goods_receipt_id).then(function(response){
            console.log('it work');
            form.goods_receipt_id = response.data.data.goods_receipt_id;
            form.goods_receipt_no = response.data.data.goods_receipt_no;
            form.supplier_id = response.data.data.supplier_id;
            form.supplier_name = response.data.data.supplier_name;
            
            form.purchasing_items = [];
            _.each(response.data.data.goods_receipt_items, function(product, key, list){
                form.purchasing_items.push({
                    product_id:product.product_id ,
                    product_name: product.product_name,
                    product_model: product.product_model,
                    price: product.cost_of_goods_sold,
                    discount: 0,
                    qty: product.qty,
                    finalprice: product.cost_of_goods_sold,
                    qty_unit_id: product.qty_unit_id,
                    qty_unit: product.qty_unit,
                    qty_rasio: product.qty_rasio,
                    subtotal: product.cost_of_goods_sold * product.qty,
                    offline_margin: 0,
                    offline_price_pcs: 0,
                    offline_price_rasio: 0,
                    online_margin: 0,
                    online_price_pcs: 0,
                    online_price_rasio: 0,
                });
            });

            form.calculate();
        });
    
        $('#modal-goods-receipt').modal('toggle');    
    });


});