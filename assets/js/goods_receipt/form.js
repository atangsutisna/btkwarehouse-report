Vue.component('order-detail', {
    props: ['idx', 'order_detail'],    
    data() {
        var app = this;
        return {
            received_qty: app.order_detail.received_qty,
            updated_price: app.order_detail.price
        }
    },
    watch: {
        received_qty: function(val) {
            var app = this;
            console.info('value: '+ val);
            console.info('qty_receipt: '+ app.order_detail.qty_receipt);
            if (app.order_detail.qty_receipt > 0 &&
                    parseInt(app.received_qty) > parseInt(app.order_detail.qty_receipt)) {
                alert('Qty greather than qty order');
                app.received_qty = 0;
                return false;
            }

            if (app.received_qty >= 0) {
                this.$parent.sumSubtotal(app.idx, val);    
            }
            
        }
    },    
    methods: {
        formatRupiah(amount) {
            return accounting.formatMoney(amount);
        },   
        doRemove(idx) {
          this.$parent.doRemove(idx);  
        },
        calculate() {
            console.info('calculate subtotal');
            var app = this;
            this.$parent.updatePrice(app.idx, app.updated_price);

            app.updated_price = app.updated_price.toString().replace(/(\d)(?=(\d{3})+(?:\.\d+)?$)/g, "$1\.");
        }
    },   
    template: `<tr>
                <td>{{idx + 1}}</td>
                <td>{{order_detail.product_name}}<br/>
                    <small>{{order_detail.product_model}}</small>
                    <input type="text" v-model="order_detail.product_id" hidden="true" />
                </td>
                <!--
                <td class="text-right">
                    <input type="text" class="text-right" v-model="updated_price" v-on:change="calculate()"/>
                </td>
                -->
                <td class="text-right">{{order_detail.qty}}</td>
                <td>{{order_detail.qty_unit}}</td>
                <td class="text-right">{{order_detail.qty_receipt}}</td>
                <td>{{order_detail.qty_unit}}</td>
                <td class="col-lg-1 col-sm-1">
                    <span class="pull-right">
                        <input type="number" class="text-right" v-model="received_qty" class="form-control text-right"/>
                    </span>
                </td>
                <td>{{order_detail.qty_unit}}</td>
                <td><textarea class="form-control" v-model="order_detail.note"></textarea></td>
                <td><input type="date" v-model="order_detail.expiry_date" class="form-control"/></td>
                <!--
                <td class="text-right">{{formatRupiah(order_detail.subtotal)}}</td>
                -->
            </tr>`
});

var form = new Vue({
	el:"#form-goods-receipt",
	data: {
        form_valid: false,
        form_status: {
            alert: false,
            has_errors: false,
            success: false,
            message: 'Undefined message'
        },
        goods_receipt_id: null,
        purchase_order_id: null,
        purchase_order_no: null,
        purchase_order_date: 'N/A',        
        goods_receipt_no:'N/A',
        goods_receipt_id: null,
        receiver_id: null,
        receiver_name: null,
        received_date: null,
        sales_person_name: null,
        supplier_id:null,
        supplier_name: '-',
        status:'draft',
        total: 0,
        order_details: [],
        options: [],
        product_id:null,
        product_name:null,
        product_sku:null,
        price:null,
        qty:null,
        qty_unit:null,
        subtotal:null,
        total: 0
	},
    watch: {
        receiver_name: function(new_val) {
            console.log(new_val);
            this.form_valid = this.isFormValid();
        },
        received_date: function(new_val) {
            console.log(new_val);
            this.form_valid = this.isFormValid();  
        },
        sales_person_name: function(new_val) {
            console.log(new_val);
            this.form_valid = this.isFormValid();
        },
        order_details: function() {
            console.log('ada value baru gak ya');
            this.form_valid = this.isFormValid();
        }
    },
	methods:{
        moneyFormat(amount) {
            return accounting.formatMoney(amount);
        },
        sumSubtotal(idx, qty) {
            var app = this;
            var order_detail = app.order_details[idx];
            order_detail.received_qty = qty;
            order_detail.subtotal = order_detail.received_qty * order_detail.price;

            app.total = parseFloat(0);
            _.each(app.order_details, function(elem, index, list){
                app.total += parseFloat(elem.subtotal);
            });

            this.form_valid = this.isFormValid();
        },  
        updatePrice(idx, price) {
            var app = this;
            var order_detail = app.order_details[idx];
            order_detail.price = price;
            order_detail.subtotal = parseFloat(order_detail.received_qty) * parseFloat(order_detail.price);
            console.info('subtotal: '+ order_detail.subtotal);

            app.total = parseFloat(0);
            _.each(app.order_details, function(elem, index, list){
                app.total += parseFloat(elem.subtotal);
            });            
        },      
        set_total: function() {
            var app = this;
            app.total = parseFloat(0);
            _.each(app.order_details, function(elem, index, list){
                app.total += parseFloat(elem.subtotal);
            });
        },
		openPo: function(){
			$('#modal-po-catalog').modal({show: true});
		},
        doRemove: function(idx) {
            var app = this;
            if (confirm('Apakah anda yakin ?')) {
                app.order_details.splice(idx, 1);
                app.set_total();
            }  
        },
        save() {
                $('#btn-save').attr('disabled', 'disabled');
                var app = this;
                //if (app.purchase_order_id == null) {
                    app.insert();
                //} else {
                   // app.update();
                //}
                
        },
        insert(){
            var app = this;
            console.log(`goods receipt items: `, app.order_details);
            axios.post(appConfig.apiUri + '/goods_receipt', {
                'purchase_order_id': app.purchase_order_id,
                'supplier_id': app.supplier_id,
                'supplier_name': app.supplier_name,
                'receiver_id': app.receiver_id,
                'receiver_name': app.receiver_name,
                'received_date': app.received_date,
                'sales_person_name': app.sales_person_name,
                'goods_receipt_items': app.order_details,
                'status': app.status,
                'options': app.options,
                'total': app.total
            }).then(function(response) {
                console.info('new purchase order has been saved');
                var data = response.data.data;
                console.info(data);
                app.goods_receipt_id = data.goods_receipt_id;
                app.goods_receipt_no = data.goods_receipt_no;

                app.form_status.alert = true;
                app.form_status.success = true;
                app.form_status.has_errors = false;
                app.form_status.message = `Data sudah berhasil disimpan dengan nomor: ${app.goods_receipt_no}`;  

                setTimeout(function(){
                    window.location.replace(appConfig.baseUri + '/goods_receipt');
                }, 2000);                                
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
        isFormValid() {
            var total = parseInt(0);
            this.order_details.forEach(function(item){
                total += parseInt(item.received_qty);
            });
            console.log(`total qty: ${total}`);
            return (this.received_date !== null && this.received_date !== '') && 
                   (this.receiver_name !== null && this.receiver_name !== '') && 
                   (this.sales_person_name !== null && this.sales_person_name !== '')
                   && this.order_details.length !== 0 && total > 0;
        }
    },
    created() {
        var app = this;
        app.received_date = moment().format('YYYY-MM-DD');
        app.receiver_id = -1;
        app.receiver_name = $('input[name=receiver_name]').val();
    }
});

function get_po(id)
{
    axios.get(appConfig.apiUri +'/purchase_order/'+id).then(function(response){
        form.purchase_order_id = response.data.data.purchase_order_id;
         form.purchase_order_no = response.data.data.purchase_order_no;
         form.purchase_order_date = response.data.data.purchase_order_date;        
         form.supplier_name = response.data.data.supplier_name;
         form.supplier_id = response.data.data.supplier_id;
         form.status = response.data.data.status;
         form.order_details = [];
        _.each(response.data.data.order_details, function(product, key, list){
            form.order_details.push({
                product_id:product.product_id ,
                product_name: product.product_name,
                product_sku: product.product_sku,
                product_model: product.product_model,
                price: product.price,
                qty: product.qty,
                qty_unit_id: product.qty_unit_id,
                qty_receipt: product.qty_receipt,
                qty_balance: product.qty_balance,
                qty_unit: product.qty_unit,
                qty_rasio: product.qty_rasio,
                received_qty: 0, /**qty yang diinput user */
                note: null,
                expiry_date: null,
                subtotal: product.subtotal,
            });
        });
        form.set_total();
    })
    $('#modal-po-catalog').modal('toggle');
}


$('document').ready(function(){  
    var table_goods_receipt = $('#table-catalog-po').DataTable({  
        "searching": false,
        "order": [[2, 'desc']],
        "processing": true,
        "serverSide": true,
        "bInfo" : false,
        "lengthChange": false,
        "ajax" : {
            'url': appConfig.apiUri + "/purchase_order",  
            'data' : function(d) {
                d.status = 'ordered,partial';
                d.purchase_order_no = $('#purchase_order_no').val();
            }
        },
        "columns"     : [  
            {
                "orderable": false,
                "data": "purchase_order_no",
            },
            { 
                "orderable": false,
                "data" : "supplier_name",
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
                "data" : "status",
                "render": function(data, type, row, meta) {
                    return data.toUpperCase();
                }
            },
            { 
                "orderable": false,
                "data" : "purchase_order_id",
                "render": function(data, type, row, meta) {
                    var nameHtml = `<a href="#" data-id="${data}" title="View"
                    class="btn-edit" onclick="get_po(${data});">[PILIH]</a>`;
                    return nameHtml;
                }
            }  
        ]
    }); 

    $('#btn-filter').on('click', function(){
        table_goods_receipt.draw();
        return true;
    });

    
});

