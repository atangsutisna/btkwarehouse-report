Vue.component('return-item', {
    props: ['idx', 'return_item'],
    methods: {
        doRemove(idx) {
          this.$parent.doRemove(idx);  
        }     
    },
    template: `<tr>
                <td>{{ idx + 1}}</td>
                <td>
                    <span v-html="return_item.product_name"></span><br/>
                    <small>{{return_item.product_model}}</small>
                </td>
                <td class="col-sm-1 col-lg-1"><input type="number" v-model="return_item.qty" min="0" class="form-control text-right" size="5"/></td> 
                <td class="col-sm-1 col-lg-1">{{return_item.qty_unit}}</td>
                <td class="col-sm-2 col-lg-2"><textarea rows="2" class="form-control" style="min-width: 100%;" v-model="return_item.note"></textarea></td>
                <td><a class="btn btn-danger" v-on:click="doRemove(idx)">-</a></td>
            </tr>`
});

$('document').ready(function(){  
    var catalog_retur = $('#table-catalog-retur').DataTable({  
        "searching": false,
        "order": [[3, 'desc']],
        "processing": true,
        "serverSide": true,
        "lengthChange": false,
        "ajax" : {
            'url': appConfig.apiUri + "/product_variant",  
            'data' : function(d) {
                d.supplier_id = $("#supplier-choices option:selected").val();
                d.term = $('input[name=term]').val();
            }
        },
        "createdRow": function(row, data, dataIndex) {
            if (data.qty_unit_id == null) {
                $(row).addClass('danger');
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
                "orderable": false,
                "data": "name",
                "render": function(data, type, row, meta) {
                    return `${data}<br/><small>${row.model}</small>`
                }
            },
            {
                "orderable": false,
                "data": "qty_on_hand",
                "render": function(data, type, row, meta) {
                    return `<span class="pull-right">${data} ${row.qty_unit}</span>`;
                }
            },   
            { 
                "data": "date_modified",
                "render": function(data, type, row, meta) {
                    var html = moment(data).format('ll H:mm');
                    return html;
                }
            },
            {
                "orderable": false,
                "data": "product_id",
                "render": function(data, type, row, meta) {
                    var disabled = row.qty_unit == null ? "disabled" : "";
                    return `<input type="checkbox" name="checked_product" value="${data}" ${disabled}/>`;
                    /**return `<input type="checkbox" name="checked_product" value="${data}"/>`;**/
                }
            }
        ]
    }); 

    $('#btn-filter-product').click(function(){
        console.info('redraw catalog retur');
        catalog_retur.draw();
    });

    $('#btn-reset-filter-product').click(function(){
        $('input[name=term]').val('');        
        catalog_retur.draw();
    });
    var form = new Vue({
        el:"#form-retur",
        data: {
            form_status: {
                alert: false,
                has_errors: false,
                success: false,
                message: 'Undefined message'
            },
            retur_no:'N/A',
            retur_id:null,
            supplier_name:null,
            supplier_id:null,
            purchase_order_id: null,
            retur_date: null,
            product_id:null,
            product_name:null,
            product_sku:null,
            price:null,
            qty:null,
            qty_unit:null,
            subtotal:null,
            created_at: null,
            return_items: [],
            total: 0
        },
        watch: {
            return_items: function() {
                var app = this;
                app.total = parseFloat(0);
                _.each(app.return_items, function(elem, index, list){
                    app.total += parseFloat(elem.subtotal);
                });
            },
            supplier_id: function() {
                var app = this;
                //console.info(`selected supplier id ${app.supplier_id}`);
                catalog_retur.draw();
                $('#modal-product-catalog-retur').modal({show: true});
                app.return_items = [];
                /**
                if (app.supplier_id == null) {
                    $('#product-choices').prop('disabled', true);
                } else {
                }**/
            }
        },
        methods:{
            moneyFormat(amount) {
                return accounting.formatMoney(amount);
            },
            openCatalogInventory: function(){
                var app = this;
                var supplier=form.supplier_id;
                if(supplier){
                    catalog_retur.draw();
                    $('#modal-product-catalog-retur').modal({show: true});
                }else{
                    app.form_status.has_errors = 'supplier Tidak Boleh Kosong';
                    alert('supplier Tidak Boleh Kosong');
                }
                
            },
            doRemove: function(idx) {
                if (confirm('Apakah anda yakin ?')) {
                    this.return_items.splice(idx, 1);
                }  
            }, 
            save() {
                    $('#btn-save').attr('disabled', 'disabled');
                    var app = this;
                    if (app.retur_id == null) {
                        app.insert();
                    } else {
                        app.update();
                    }
                    
            },
            insert(){
                var app = this;
                axios.post(appConfig.apiUri + '/Retur', {
                    'supplier_id': app.supplier_id,
                    'supplier_name': app.supplier_name,
                    'retur_date': app.retur_date,
                    'return_items': app.return_items,
                }).then(function(response) {
                    console.info('new retur has been saved');
                    var data = response.data.data;
                    console.info(data);
                    app.retur_id = data.retur_id;
                    app.retur_no = data.retur_no;

                    app.form_status.alert = true;
                    app.form_status.success = true;
                    app.form_status.message = `Data sudah berhasil disimpan dengan nomor: ${app.retur_no}`;  
                    
                    /** 
                    setTimeout(function(){
                        window.location.replace(appConfig.baseUri + '/retur');
                    }, 3000); **/
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
                console.info('Requesting to update retur with id: '+ app.retur_id);
                axios.put(appConfig.apiUri + '/retur', {
                    'retur_id': app.retur_id,
                    'supplier_id': app.supplier_id,
                    'supplier_name': app.supplier_name,
                    'purchase_order_id': app.purchase_order_id,
                    'created_at': app.created_at,
                    'return_items': app.return_items,
                    'total': app.total
                }).then(function(response) {
                    console.info('new purchase order has been saved');
                    var data = response.data.data;
                    app.retur_id = data.retur_id;
                    app.retur_no = data.retur_no;

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
                var app = this;
            },
            print() {
                alert('Sory, this feature is undercontruction');
                return false;
            },
        },
        created() {
            var app = this;
            app.retur_date = moment().format('YYYY-MM-DD');
            console.log(app.retur_date);
            /** 
            app.retur_id = $('input[name=retur_id]').val();
            async function get_by_id(id) {
                var retur = await axios.get(appConfig.apiUri + '/retur/' + id);
                app.retur_no = retur.data.data.retur_no;
                app.supplier_id = retur.data.data.supplier_id;
                app.supplier_name = retur.data.data.supplier_name;
                
                _.each(retur.data.data.return_items, function(elem, key, list){
                    app.return_items.push(elem);
                });    
                
                //app.status = retur.data.data.status;
                app.created_at = retur.data.data.retur_date;
            }

            if (app.retur_id !== undefined 
                && app.retur_id !== null && app.retur_id !== '') {
                console.info(app.retur_id);
                get_by_id(app.retur_id);
            }

            console.info(app.return_items); **/
        },
        updated() {
            /**
            var app = this;
            console.info('pasti kesini');
            if (app.return_items.length == 0 && app.supplier_id !== null) {
                catalog_retur.draw();
                $('#modal-product-catalog-retur').modal({show: true});
                $('#supplier-choices').prop('disabled', true);
            } **/
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
    });

    $("#get-data-retur").click(function(){
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
                var return_item = {
                    product_id: product.product_id,
                    product_model: product.model,
                    product_name: product.name,
                    qty: 0,
                    qty_unit: product.qty_unit,
                    qty_unit_id: product.qty_unit_id,
                    note: null,
                };

                form.return_items.push(return_item);    
            });
            
        }).catch(function(error){
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
            $('#modal-product-catalog-retur').modal('toggle');
        });
    }); 

    $('#purchase-order-choices').select2({
        ajax: {
            url: appConfig.apiUri + '/purchase_order?status=complete',
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
                        id: raw.purchase_order_id,
                        text: `${raw.purchase_order_no} (${raw.supplier_name})` 
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
        form.purchase_order_id = data.id;
    });

});

