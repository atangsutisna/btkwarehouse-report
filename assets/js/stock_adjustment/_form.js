var form = new Vue({
    el:"#form-stock-adjustment",
	data: {
        form_status: {
            alert: false,
            has_errors: false,
            success: false,
            message: 'Undefined message'
        },
        product_id:null,
        product_name:null,
        product_model:null,
        qty:null,
        qty_unit: null,
        qty_unit_id: null,
        status_adjust: 1,
        stock_adjust:null,
        last_stock:0
    },
    watch: {
        last_stock: function(val) {
            var app = this;

            app.status_adjust = 1;
            app.stock_adjust = 0;

            if (val == '' || val === undefined) {
                return false;
            }

            if (val > 0) {
                app.stock_adjust = val - app.qty;
            }
            
            if (app.stock_adjust <= 0) {
                app.status_adjust = 2;
            }

            console.log(`status adjust: ${app.status_adjust}`);
            //app.calculate();
        }
    },
	methods:{
		openStock: function(){
			$('#modal-stock-catalog').modal({show: true});
		},
        clear_calculate: function(){
            var app=this;
            app.stock_adjust=null;
            app.last_stock=0;
        },
        calculate: function(){
            var app=this;
            var operation=app.status_adjust;

            if(operation==1){
                var new_stock=parseFloat(app.qty)+parseFloat(app.stock_adjust);
            }else{
                var new_stock=parseFloat(app.qty)-parseFloat(app.stock_adjust);
                if(new_stock<0){
                    app.form_status.alert = true;
                    app.form_status.has_errors = true;
                    app.form_status.success = false;
                    app.form_status.message = 'Error Calculate';
                    $('#btn-save').attr('disabled', 'disabled');
                }
            }
            app.last_stock=new_stock;
        },
        save() {
            $('#btn-save').attr('disabled', 'disabled');
            var app = this;
            app.insert();                
        },
        insert(){
            var app = this;
            axios.post(appConfig.apiUri + '/stock_adjustment', {
                'product_id': app.product_id,
                'qty': app.qty,
                'qty_unit_id': app.qty_unit_id,
                'stock_adjust': app.stock_adjust,
                'status_adjust': app.status_adjust,
                'last_stock': app.last_stock
            }).then(function(response) {
                console.info('new stock adjustment has been saved');
                var data = response.data.data;
                console.info(data);
                app.stock_adjustment_id = data.stock_adjustment_id;

                app.form_status.alert = true;
                app.form_status.success = true;
                app.form_status.message = `Data sudah berhasil disimpan`;  

                setTimeout(function(){
                    window.location.replace(appConfig.baseUri + '/stock_adjustment');
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
	}
});

function get_stock(id) {
    axios.get(appConfig.apiUri + '/invbalance/'+id)
        .then(function(response){
            console.log(`response: `, response.data);

            form.product_id = response.data.data.product_id;
            form.product_name = response.data.data.product_name;
            form.product_model = response.data.data.product_model;
            form.qty = response.data.data.qty;
            form.qty_unit = response.data.data.qty_unit;
            form.qty_unit_id = response.data.data.qty_unit_id;
    })
    $('#modal-stock-catalog').modal('toggle');
}


$('document').ready(function(){  
    var table_stock = $('#table-catalog-stock').DataTable({  
        "searching": false,
        "order": [[0, 'desc']],
        "processing": true,
        "serverSide": true,
        "bInfo" : false,
        "lengthChange": false,
        "ajax" : {
            'url': appConfig.apiUri + "/invbalance",  
            'data' : function(d) {
                d.search = {
                    'value': $('input[name=term]').val()
                }
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
                "data": "product_name",
                "render": function(data, type, row, meta) {
                    var drawHtml = `${data}<br/>${row.product_model}<br>`;
                    return drawHtml;
                }
            },
            { 
                "orderable": false,
                "data" : "qty",
                "render": function(data, type, row, meta) {
                    return `<span class="pull-right">${data} ${row.qty_unit}</span>`;
                }
            },
            { 
                "orderable": false,
                "data" : "inventory_balance_id",
                "render": function(data, type, row, meta) {
                    var nameHtml = `<a href="#" data-id="${data}" title="View"
                    class="btn-edit text-center" onclick="get_stock(${data});">[ pilih ]</a>`;
                    return nameHtml;
                }
            }  
        ]
    }); 

    $('#btn-filter').on('click', function(){
        table_stock.draw();
        return false;
    });

    
});

