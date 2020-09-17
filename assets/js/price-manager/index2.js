$('document').ready(function(){
Vue.component('goods-receipt-item', {
    props: ['idx', 'goods_receipt_item'],    
    data() {
        var app = this;
        return {
            margin: 0,
        }
    },
    watch: {
        margin: function(val) {
            this.$parent.updateMargin(this.idx, val);  
        }
    },    
    methods: {
        moneyFormat(amount) {
            return accounting.formatMoney(amount);
        },   
    },   
    template: `<tr>
                <td>{{idx + 1}}</td>
                <td>{{goods_receipt_item.product_name}}</td>
                <td class="text-right">{{moneyFormat(goods_receipt_item.original_price)}}</td>
                <td>{{goods_receipt_item.qty_unit}}</td>
                <td class="text-right">{{moneyFormat(goods_receipt_item.tax)}}</td>                
                <td style="width:40px;">
                    <span class="pull-right">
                        <input type="number" class="text-right" v-model="margin"/>
                    </span>
                </td>
                <td class="text-right">{{moneyFormat(goods_receipt_item.final_price)}}</td>
            </tr>`
});

	var form = new Vue({
		el:"#form-price-manager",
		data: {
            form_status: {
                alert: false,
                has_errors: false,
                success: false,
                message: 'Undefined message'
            },            
            include_tax: false,
            tax: 10,
			goods_receipt_id: null,
			goods_receipt_no: null,
            supplier_id: null,
            supplier_name: '-',
			received_date: null,
			receiver_name: null,
			sales_person_name: null,
            goods_receipt_items: []
		},
        watch: {
            include_tax: function(val) {
                var app = this;
                if (val) {
                    console.info(val);
                    _.each(app.goods_receipt_items, function(item, key, list) {
                        item.final_price = app.calculateCost(item.original_price, app.tax, item.margin);
                    });                    
                } else {
                    console.info(val);
                    _.each(app.goods_receipt_items, function(item, key, list) {
                        item.final_price = app.calculateCost(item.original_price, 0, item.margin);
                    });                    
                }
            }   
        },
		methods: {
            calculateCostGoodsSold(original_price, tax) {
                var tax_amount = (parseFloat(original_price) * parseFloat(tax)) / 100;
                var costGoodsSold = parseFloat(original_price) + parseFloat(tax_amount);

                return costGoodsSold;
            },
            calculateCost(original_price, tax, margin) {
                var app = this;
                var costGoodsSold = app.calculateCostGoodsSold(original_price, tax);
                console.info('hpp for '+ original_price + ', tax: '+ tax +':'+ costGoodsSold);
                var margin_amount = (parseFloat(original_price) * parseFloat(margin)) / 100;

                return parseFloat(costGoodsSold) + parseFloat(margin_amount);
            },
			openGoodsReceipt() {
				console.info('hallo');
				$('#modal-goods-receipt').modal({show: true});
			},		
            doSave(){
                var app = this;
                var form_data = {
                    'data': app.goods_receipt_items
                };

                axios.post(appConfig.apiUri + '/price_manager', form_data).then(function(response) {
                    app.include_tax = false;
                    app.tax = 10;
                    app.goods_receipt_id = null;
                    app.goods_receipt_no = null;
                    app.received_date = null;
                    app.receiver_name = null;
                    app.sales_person_name = null;
                    app.goods_receipt_items = []

                    app.form_status.alert = true;
                    app.form_status.success = true;
                    app.form_status.has_errors = false;
                    app.form_status.message = 'Data sudah disimpan';
                }).catch(function(error) {
                    $('#btn-save').removeAttr('disabled', 'disabled');

                    var message = error.response.data.message;
                    if (error.response.data.code == 400) {
                        var message = '<ul>';
                        _.each(error.response.data.errors, function(value, key, list){
                            message += `<li>${value}</li>`;
                        });
                        message += '</ul>';
                    }
                    
                    if (error.response.data.code == 500) {
                        message = 'Internal server error';
                    }

                    app.form_status.alert = true;
                    app.form_status.has_errors = true;
                    app.form_status.success = false;
                    app.form_status.message = message;
                }).finally(function(){
                    $('#btn-save').removeAttr('disabled');
                });                
            },
            updateMargin(idx, margin) {
                var app = this;
                var goods_receipt_item = app.goods_receipt_items[idx];
                goods_receipt_item.margin = margin;                
                goods_receipt_item.final_price =  app.calculateCost(goods_receipt_item.original_price, 
                    app.tax, goods_receipt_item.margin);
            }
		},
		created(){
			console.info('form price manager created');
		}
	});	

    var table_goods_receipt = $('#table-goods-receipt').DataTable({  
        "searching": false,
        "order": [[0, 'desc']],
        "processing": true,
        "serverSide": true,
        "ajax" : {
            'url': appConfig.apiUri + "/goods_receipt",  
            'data' : function(d) {
                d.goods_receipt_no = $('#goods_receipt_no').val();
            }
        },
        "columns"     : [  
            { 
                "data": "updated_at",
                "render": function(data, type, row, meta) {
                    return moment(data).format('lll');
                }
            },
            {
                "orderable": false,
                "data": "goods_receipt_no",
            },
            { 
                "orderable": false,
                "data" : "supplier_name",
            },  
            { 
                "orderable": false,
                "data" : "total_qty",
            },            
            { 
                "orderable": false,
                "data" : "status",
            }, 
            { 
                "orderable": false,
                "data" : "goods_receipt_id",
                "render": function(data, type, row, meta) {
                    var nameHtml = `<a href="#" data-id="${data}" title="View"
                    class="btn-choose">[pilih]</a>`;
                    return nameHtml;
                } 
            }                     
        ]
    }); 

    $('#btn-filter').on('click', function(){
        table_goods_receipt.draw();
        return false;
    });    

	$('#table-goods-receipt tbody').on('click', '.btn-choose', function(){
		var goods_receipt_id = $(this).data('id');
        console.info(goods_receipt_id);
	    axios.get(appConfig.apiUri +'/goods_receipt/'+goods_receipt_id).then(function(response){
	    	var data = response.data.data;

	    	form.goods_receipt_id = data.goods_receipt_id;
	    	form.goods_receipt_no = data.goods_receipt_no;
            form.supplier_id = data.supplier_id;
            form.supplier_name = data.supplier_name;
	    	form.received_date = moment(data.received_date).format('L');
	    	form.receiver_name = data.receiver_name;
	    	form.sales_person_name = data.sales_person_name;
	    	
            form.goods_receipt_items = [];
	        _.each(data.goods_receipt_items, function(product, key, list){
	            form.goods_receipt_items.push({
                    goods_receipt_id: data.goods_receipt_id,
                    product_id:product.product_id ,
                    product_name: product.product_name,
                    product_sku: product.product_sku,
                    options: product.options,
                    original_price: product.price,
                    tax: 0,
                    margin: 0,
                    qty_unit: product.qty_unit,
                    final_price: product.price
	            });
	        });
	    });

    	$('#modal-goods-receipt').modal('toggle');		
	});
});
