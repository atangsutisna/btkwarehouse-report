Vue.component('btk-wysiwyg', {
    props: ['description'],
    template: `<textarea type="text" name="description" rows="5" class="form-control" 
        v-html="description" id="product_description"></textarea>`,
    mounted: function() {
        $(this.$el).summernote({
            toolbar: [
                // [groupName, [list of button]]
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['font', ['strikethrough', 'superscript', 'subscript']],
                ['fontsize', ['fontsize']],
                ['fontNames', ['Verdana']],            
                ['para', ['ul', 'ol', 'paragraph']],
            ],
        });
    },
    updated() {
        console.info('child run on updated');
        console.info(`child-desc after update: ${this.description}`);
        $(this.$el).summernote('code', this.description);

        //this.description = $('#product_description').summernote('code');
    }
});
var formProduct = new Vue({
    el: '#form-product',
    data: {
        form_status: {
            alert: false,
            has_errors: false,
            success: false,
            message: 'Undefined message'
        },
        form_price_variant: {
            platform: null,
            price: 0,
        },
        /** 
        form_invbalance: {
            invbalance_id: null,
            sku: null,
            description: null,
            qty_unit_id: null,
            form_status: {
                alert: false,
                has_errors: false,
                success: false,
                message: 'Undefined message'
            }                
        }, **/
        form_product_option: {
            product_options: []
        },
        form_product_image: {
            images: [],
            selected_images: [],
        },
        multiple_uom: false,
        product_variant: [{
            product_id: null,
            model: null,
            price: null,
            qty_unit_id: null,
            qty_rasio: null,
            default: false
        }],
        product_rasio_choices: [],
        invbalance_id: null, // delete me
        qty_unit_id: null,
        target_qty_unit: null, // delete me
        rasio_product_id: null,
        product_id: null,
        supplier_id: null,
        product_category: [],
        product_related: [],
        sku: null,
        isbn: null,
        name: null,
        model: null,
        price: null,
        price_variants: [],
        description: null,
        meta_title: null,
        meta_description: null,
        meta_keyword: null,
        supplier_id: null,
        tax_class_id: null,
        quantity: null,
        minimum: null,
        maximum: null,
        minimum_order: null,
        maximum_order: null,
        moving_product_status: null,
        subtract: 1,
        stock_status_id: null,
        shipping: 1,
        weight: null,
        weight_class_id: null,
        length: null,
        length_class_id: null,
        width: null,
        height: null,
        status: 1,
        sort_order: null,
        product_options: [],
        primary_image: null,
        image: null
    },    
    watch: {
        "form_product_image.selected_images": function(val) {
            var app = this;
            var primary = _.find(app.form_product_image.selected_images, function(image){
                return image.name === app.primary_image;
            });

            console.info(primary);
            if (primary === undefined) {
                if (app.form_product_image.selected_images.length > 0) {
                    app.primary_image = app.form_product_image.selected_images[0].name;
                }        
            }
        },
        name: function() {
            var app = this;
            var sku = app.name.replace(/\s/g, "-");
            app.sku = sku.toLowerCase();
        },
        rasio_product_id: function(val) {
            var app = this;
            app.form_subproduct = [];
            app.form_subproduct.push({
                sku: null,
                barcode: null,
                sku: null,
                qty_unit: null
            });
            console.log(`count subproduct: ${app.form_subproduct.length}`);
        },
        multiple_uom: function(val) {
            var app = this;
            console.log(`multiple uom: ${val}`);
            if (val && app.product_variant.length == 0) {
                app.product_variant.push({
                    product_id: null,
                    model: null,
                    price: null,
                    qty_unit_id: null,
                    qty_rasio: null,
                    default: false
                });
            }   
        }        
    },
    methods: {
        formatCurrency: function(event) {
            var value = event.target.value;
            var formatedValue = value.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ".")
            console.info('format number: '+ formatedValue);
            $('#foreignCurrency').val(formatedValue);
            this.price = formatedValue;
            console.info(this.price);
        },
        addPriceVariant: function() {
            console.info('add new price variant');
            var app = this;
            if (app.form_price_variant.platform == null || 
                (app.form_price_variant.price == null || app.form_price_variant.price == 0)) {
                console.warn('empty value');
                return;
            }

            var exist_price_platform = _.find(app.price_variants, function(value, index, list) {
                return value.platform == app.form_price_variant.platform;
            });

            if (exist_price_platform) {
                console.warn('Duplicate value');
                return;
            }

            var default_price = _.size(app.price_variants) == 0 ? true : false;
            app.price_variants.push({
                'product_price_id': null,
                'platform': app.form_price_variant.platform,
                'price': app.form_price_variant.price,
                'display_value_price': accounting.formatMoney(app.form_price_variant.price),
                'default': default_price
            });

            if (default_price == true) {
                app.price = app.form_price_variant.price;
            }

            app.form_price_variant.platform = null;
            app.form_price_variant.price = null;
        },
        deletePriceVariant: function(idx) {
            var app = this;
            if (confirm('Anda yakin ?')) {
                app.price_variants.splice(idx, 1);
            }  
        },
        updateDefaultPrice: function() {
            var app = this;
            if (_.size(app.price_variants) > 0) {
                var price_item = _.find(app.price_variants, function(price_item){
                    return price_item.default === true;
                });
                console.info('found default price variant');
                console.table(price_item);
                price_item.price = app.price;
                price_item.display_value_price = accounting.formatMoney(app.price);
            }
        },
        setDefaultPrice: function(curr_idx) {
            var app = this;
            console.info('set default price');
            _.each(app.price_variants, function(price_item, index, list){
                price_item.default = false;
            });
            
            var price_item = app.price_variants[curr_idx];
            price_item.default = true;

            app.price = price_item.price;
            console.info('set default price to: '+ app.price);
        },
        doRemove: function(idx) {
            var identifier = this.identifiers[idx];
            if (identifier.sku == null && identifier.barcode == null) {
                this.identifiers.splice(idx, 1);
                return;
            }

            if (confirm('Anda yakin ?')) {
                this.identifiers.splice(idx, 1);
            }  
        },
        doSave: function() {
            var app = this;
            /** 
            app.form_status.alert = false;
            app.form_status.success = false;
            app.form_status.has_errors = false;
            **/
           console.log('qtu unit id: ${app.qty_unit_id}');
            if (app.qty_unit_id === null) {
                app.form_status.alert = true;
                app.form_status.success = false;
                app.form_status.has_errors = true;    
                app.form_status.message = 'Sku gudang dan qty unit tidak boleh kosong';
                return false;
            }
            if (!$.isEmptyObject(app.product_id) ) {
                console.info('do update');
                app.doUpdate();
            } else {
                console.info('do insert');
                app.doInsert();
            }
        },
        doInsert: function() {
            var app = this;
            app.description = $('#product_description').summernote('code');
            console.info(app.description);
            
            $('#btn-save').attr('disabled', 'disabled');
            var product_images = _.map(app.form_product_image.selected_images, function(elem, key){
                return {
                    'product_id': app.product_id,
                    'image': elem.name
                };
            });
            axios.post(appConfig.apiUri + '/product', {
                'supplier_id': app.supplier_id,
                'qty_unit_id': app.qty_unit_id,
                'sku': app.sku,
                'isbn': app.isbn,
                'name': app.name,
                'model': app.model,
                'price': app.price,
                'description': app.description,
                'meta_title': app.meta_title,
                'meta_description': app.meta_description,
                'meta_keyword': app.meta_keyword,
                'supplier_id': app.supplier_id,
                'tax_class_id': app.tax_class_id,
                'quantity': app.quantity,
                'minimum': app.minimum,
                'maximum': app.maximum,
                'minimum_order': app.minimum_order,
                'maximum_order': app.maximum_order,
                'moving_product_status': app.moving_product_status,
                'substract': app.subtract,
                'stock_status_id': app.stock_status_id,
                'shipping': app.shipping,
                'weight': app.weight,
                'weight_class_id': app.weight_class_id,
                'length': app.length,
                'length_class_id': app.length_class_id,
                'width': app.width,
                'height': app.height,
                'status': app.status,
                'sort_order': app.sort_order,
                'product_category': app.product_category,
                'product_related': app.product_related,
                'product_option': app.product_options,
                'product_images': product_images,
                'multiple_uom': app.multiple_uom,
                'product_variant': app.product_variant
            }).then(function(response){
                console.info('success');
                var data = response.data.data;
                app.product_id = data.product_id;

                app.form_status.alert = true;
                app.form_status.success = true;
                app.form_status.message = '1 data telah berhasil ditambahkan';
            }).catch(function(error) {
                console.error('error');
                var message = error.response.data.message;
                if (error.response.data.code == 400) {
                    var message = '<ul>';
                    _.each(error.response.data.errors, function(value, key, list){
                        message += `<li>${value}</li>`;
                    });
                    message += '</ul>';
                }

                app.form_status.alert = true;
                app.form_status.has_errors = true;
                app.form_status.message = message;
            }).finally(function(){
                $('#btn-save').removeAttr('disabled', 'disabled');                
            });
        },
        doUpdate: function() {
            var app = this;
            app.description = $('#product_description').summernote('code');

            $('#btn-save').attr('disabled', 'disabled');
            var product_images = _.map(app.form_product_image.selected_images, function(elem, key){
                return {
                    'product_id': app.product_id,
                    'image': elem.name
                };
            });
            axios.put(appConfig.apiUri + '/product', {
                'invbalance_id': app.invbalance_id,
                'product_id': app.product_id,
                'supplier_id': app.supplier_id,
                'qty_unit_id': app.qty_unit_id,
                'sku': app.sku,
                'isbn': app.isbn,
                'name': app.name,
                'model': app.model,
                'price': app.price,
                'description': app.description,
                'meta_title': app.meta_title,
                'meta_description': app.meta_description,
                'meta_keyword': app.meta_keyword,
                'supplier_id': app.supplier_id,
                'tax_class_id': app.tax_class_id,
                'quantity': app.quantity,
                'minimum': app.minimum,
                'maximum': app.maximum,
                'minimum_order': app.minimum_order,
                'maximum_order': app.maximum_order,
                'moving_product_status': app.moving_product_status,
                'subtract': app.subtract,
                'stock_status_id': app.stock_status_id,
                'shipping': app.shipping,
                'weight': app.weight,
                'weight_class_id': app.weight_class_id,
                'length': app.length,
                'length_class_id': app.length_class_id,
                'width': app.width,
                'height': app.height,
                'status': app.status,
                'sort_order': app.sort_order,
                'product_category': app.product_category,
                'product_related': app.product_related,
                'product_option': app.product_options,
                'moving_product_status': app.moving_product_status,
                'product_images': product_images,
                'multiple_uom': app.multiple_uom,
                'product_variant': app.product_variant
            }).then(function(response){
                var data = response.data.data;
                app.product_id = data.product_id;
                console.info('Product id with id '+ app.product_id + ' was updated');
                app.form_status.alert = true;
                app.form_status.success = true;
                app.form_status.has_errors = false;
                app.form_status.message = '1 data telah berhasil diperbaharui';
            }).catch(function(error) {
                var message = error.response.data.message;
                if (error.response.data.code == 400) {
                    var message = '<ul>';
                    _.each(error.response.data.errors, function(value, key, list){
                        message += `<li>${value}</li>`;
                    });
                    message += '</ul>';
                } else {
                    message = 'Internal server error. Please call the administrator';
                }
                
                app.form_status.alert = true;
                app.form_status.success = false;
                app.form_status.has_errors = true;
                app.form_status.message = message;
            }).finally(function() {
                $('#btn-save').removeAttr('disabled', 'disabled');                
            });
        },
        removeCategory: function(idx) {
            var app = this;
            app.product_category.splice(idx, 1);
        },
        removeProductRelated: function(idx) {
            var app = this;
            app.product_related.splice(idx, 1);
        },
        addInvbalance: function() {
            var app = this;
            var invbalance = app.form_invbalance;
            console.info(invbalance.sku);   
            console.info(invbalance.description);   
            console.info(invbalance.qty_unit_id);   

            $('#btn-save-invbalance').attr('disabled', 'disabled');
            axios.post(appConfig.apiUri + '/invbalance', {
                'sku': invbalance.sku,
                'description': invbalance.description,
                'qty_unit_id': invbalance.qty_unit_id,
            }).then(function(response){
                var data = response.data.data;
                app.form_invbalance.invbalance_id = null;
                app.form_invbalance.sku = null;
                app.form_invbalance.description = null;
                app.form_invbalance.qty_unit_id = null;
                $('#product_rasio_choices').val(null).trigger('change');

                app.form_invbalance.form_status.alert = true;
                app.form_invbalance.form_status.success = true;
                app.form_invbalance.form_status.message = '1 data telah berhasil ditambahkan';
            }).catch(function(error){
                var message = error.response.data.message;
                if (error.response.data.code == 400) {
                    var message = '<ul>';
                    _.each(error.response.data.errors, function(value, key, list){
                        message += `<li>${value}</li>`;
                    });
                    message += '</ul>';
                }

                app.form_invbalance.form_status.alert = true;
                app.form_invbalance.form_status.has_errors = true;
                app.form_invbalance.form_status.message = message;
            }).finally(function(){
                $('#btn-save-invbalance').removeAttr('disabled');
            });
        },
        removeProductOption: function(idx) {
            var app = this;
            app.product_options.splice(idx, 1);
        },
        selectImages: function() {
            var app = this;

            async function select(images) {
                var checked_images = _.filter(images, function(image) {
                    return image.checked == true;
                });

                _.each(checked_images, function(elem, index, list){
                    app.form_product_image.selected_images.push(elem);
                });
                    
                if (checked_images.length > 0) {
                    app.primary_image = checked_images[0];
                }

                $('#modal-img-manager').modal('hide');
            } 
            
            select(app.form_product_image.images);
        },
        removeSelectedImage: function(idx) {
            var app = this;
            if (confirm('Anda yakin ?')) {
                app.form_product_image.selected_images.splice(idx, 1);
            }
            
        }
    },   
    created: async function() {
        console.info('parent first');
        var app = this;
        app.product_id = $('input[name=product_id]').val();
        
        var raw_images = await axios.get(appConfig.apiUri + '/img_manager');
        _.each(raw_images.data.images, function(elem, index, list){
            app.form_product_image.images.push(elem);
        });
        console.info('total image: '+ app.form_product_image.images.length);

        if ( !$.isEmptyObject(app.product_id) ) {
            console.info('Product id: '+ app.product_id);
            var product = await axios.get(appConfig.apiUri + '/product/' + app.product_id);

            app.invbalance_id = product.data.data.invbalance_id;
            app.sku = product.data.data.sku;
            app.isbn = product.data.data.isbn;
            app.supplier_id = product.data.data.supplier_id;
            app.name = product.data.data.name;
            app.model = product.data.data.model;
            app.price = parseFloat(product.data.data.price);
            app.description = product.data.data.description;
            app.meta_title = product.data.data.meta_title;
            app.meta_description = product.data.data.meta_description;
            app.meta_keyword = product.data.data.meta_keyword;
            app.tax_class_id = product.data.data.tax_class_id;
            app.quantity = product.data.data.quantity;
            app.minimum = product.data.data.minimum;
            app.maximum = product.data.data.maximum;
            app.minimum_order = product.data.data.minimum_order;
            app.maximum_order = product.data.data.maximum_order;
            app.moving_product_status = product.data.data.moving_product_status;
    
            app.subtract = product.data.data.subtract;
            app.stock_status_id = product.data.data.stock_status_id;
            app.shipping = product.data.data.shipping;
            app.weight = parseFloat(product.data.data.weight),
            app.weight_class_id = product.data.data.weight_class_id;
            app.length = parseFloat(product.data.data.length);
            app.length_class_id = product.data.data.length_class_id;
            app.width = parseFloat(product.data.data.width);
            app.height = parseFloat(product.data.data.height);
            app.status = product.data.data.status;
            app.sort_order = product.data.data.sort_order;
            app.product_options = product.data.data.product_option;
            app.qty_unit_id = product.data.data.qty_unit_id;
            console.log(`multiple uom: ${product.data.data.multiple_uom}`);
            app.multiple_uom = product.data.data.multiple_uom == 1 ? true : false;
            app.product_variant = product.data.data.product_variant;
            /** 
            if (product.data.data.qty_unit_id != null) {
                app.qty_unit_id = {
                    id: product.data.data.qty_unit_id.unit_measurement_id,
                    name: product.data.data.qty_unit_id.name,
                    symbol: product.data.data.qty_unit_id.symbol,
                }
                console.info(app.qty_unit_id);
            }

            if (product.data.data.target_qty_unit != null) {
                app.target_qty_unit = product.data.data.target_qty_unit;
                var unit_measurements = await axios.get(appConfig.apiUri + '/unit_measurement/target/' + product.data.data.target_qty_unit.unit_measurement_id);
                
                _.each(unit_measurements.data.data, function(elem, index, list){
                    app.product_rasio_choices.push({
                        id: elem.unit_measurement_id,
                        name: elem.name,
                        symbol: elem.symbol,
                    });
                });
    
            } **/
            var product_rasio = await axios.get(appConfig.apiUri + '/unit_measurement/rasio/' + app.qty_unit_id);
            app.product_rasio_choices = [];
            _.each(product_rasio.data.data, function(elem, index, list){
                app.product_rasio_choices.push({
                    id: elem.unit_convertion_id,
                    display_value: elem.display_value,
                });
            });     
            
            _.each(product.data.data.product_category, function(elem, index, list){
                app.product_category.push({
                    id: elem.category_id,
                    name: elem.name
                });
            });

            _.each(product.data.data.product_related, function(elem, index, list){
                app.product_related.push({
                    related_id: elem.related_id,
                    name: elem.name
                });
            });

            _.each(product.data.data.price_variants, function(elem, index, list){
                app.price_variants.push(elem);
            });

            _.each(product.data.data.product_images, function(elem, index, list){
                app.form_product_image.selected_images.push(elem);
            });
            app.image = product.data.data.image;
        } else {
            var default_weightclass = await axios.get(appConfig.apiUri + '/weight_class?default=1');
            var default_taxclass = await axios.get(appConfig.apiUri + '/tax_class?default=1');
            app.weight_class_id = default_weightclass.data.data.weight_class_id;
            app.tax_class_id = default_taxclass.data.data.tax_class_id;
        }       

        //console.info(app.price_variants);
        //$('#product_description').summernote("code", app.description); 
        $('#supplier-choices').val(app.supplier_id).trigger('change');
        $('#unit_measurement').val(app.qty_unit_id).trigger('change');
    },
    updated: function() {
        var app = this;
        console.info(`the desc after mounted: ${app.description}`);
    }, 
    mounted: function() {
        var app = this;
        $('#supplier-choices').select2().on('select2:select', function(e) {
            var data = e.params.data;
            app.supplier_id = data.id;
        });   

        $('#warehouse-sku-choices').select2().on('select2:select', function(e) {
            var data = e.params.data;
            app.invbalance_id = data.id;
        });   
    }
});

$(document).ready(function() {
    $('#category-choices').select2({
        escapeMarkup: function(markup) {
            return markup;
        },
        templateResult: function(data) {
            return data.html;
        },
        templateSelection: function(data) {
            return data.html;
        },    
        ajax: {
            url: appConfig.apiUri + '/category',
            dataType: 'json',
            data: function(params) {
                return {
                    search: {
                        value: params.term
                    },
                    page: params.page || 1,
                    length: 25,
                    draw: 1
                }
            },
            processResults: function(response) {
                var data = response.data.map(function(raw) {
                    return {
                        id: raw.category_id,
                        text: raw.name,
                        html: raw.name
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
        formProduct.product_category.push({
            id: data.id,
            name: data.html
        });
        $(this).val(null).trigger('change');
    });    

    $('#product-related-choices').select2({
        ajax: {
            url: appConfig.apiUri + '/product',
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
        formProduct.product_related.push({
            related_id: data.id,
            name: data.text
        });
        $(this).val(null).trigger('change');
    }); 

    /** 
    $('#unit_measurement').select2({
        ajax: {
            url: appConfig.apiUri + '/unit_measurement',
            dataType: 'json',
            data: function(params) {
                return {
                    search: {
                        value: params.term
                    },
                    page: params.page || 1,
                    length: 25,
                    draw: 1
                }
            },
            processResults: function(response) {
                var data = response.data.map(function(raw) {
                    return {
                        id: raw.unit_measurement_id,
                        text: raw.name +'- ('+ raw.symbol +')'
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
        console.log(`data: `, data);
        formProduct.qty_unit_id = data.id;
        async function get_measurement(unit_measurement_id) {
            formProduct.product_rasio_choices = [];

            var unit_measurements = await axios.get(appConfig.apiUri + '/unit_measurement/rasio/' + unit_measurement_id);
            _.each(unit_measurements.data.data, function(elem, index, list){
                formProduct.product_rasio_choices.push({
                    id: elem.unit_convertion_id,
                    display_value: elem.display_value,
                });
            });     
            console.log(formProduct.product_rasio_choices);   
        }
        
        get_measurement(data.id);
    }); **/

    $('#options-choices').select2({
        ajax: {
            url: appConfig.apiUri + '/option',
            dataType: 'json',
            data: function(params) {
                return {
                    search: {
                        value: params.term
                    },
                    page: params.page || 1,
                    length: 25,
                    draw: 1
                }
            },
            processResults: function(response) {
                var data = response.data.map(function(raw) {
                    return {
                        id: raw.option_id,
                        text: raw.name +'- ('+ raw.type +')'
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

        async function get_option(option_id) {
            var option = await axios.get(appConfig.apiUri + '/option/' + option_id);
            var exists = _.find(formProduct.product_options, function(product_option){
                return product_option.option_id === option.data.data.option_id;
            });

            console.info(exists);
            if (exists == undefined) {
                _.each(option.data.data.values, function(value, key, list){
                    value.checked = true;
                });
    
                formProduct.product_options.push(option.data.data);
                console.info(option.data.data);    
            } else {
                console.info('duplicate option id')
            }

        }
        
        get_option(data.id);

        $(this).val(null).trigger('change');
    });    

   $('#button-upload').on('click', function() {
	    $('#form-upload').remove();

	    $('body').prepend('<form enctype="multipart/form-data" id="form-upload" style="display: none;"><input type="file" name="file[]" value="" multiple="multiple" /></form>');

	    $('#form-upload input[name=\'file[]\']').trigger('click');

        if (typeof timer != 'undefined') {
            clearInterval(timer);
        }
        console.info('attempting to upload new file');
        timer = setInterval(function() {
            //console.info($('#form-upload input[name=\'file[]\']').val());
            if ($('#form-upload input[name=\'file[]\']').val() != '') {
                clearInterval(timer);
    
                $.ajax({
                    url: appConfig.apiUri + '/img_manager/upload',
                    type: 'POST',
                    dataType: 'json',
                    data: new FormData($('#form-upload')[0]),
                    cache: false,
                    contentType: false,
                    processData: false,
                    beforeSend: function() {
                        $('#button-upload i').replaceWith('<i class="fa fa-circle-o-notch fa-spin"></i>');
                        $('#button-upload').prop('disabled', true);
                    },
                    complete: function() {
                        $('#button-upload i').replaceWith('<i class="fa fa-upload"></i>');
                        $('#button-upload').prop('disabled', false);
                    },
                    success: function(json) {
                        if (json['error']) {
                            alert(json['error']);
                        }
                        
                        if (formProduct.image == null) {
                            console.log('primary image is empty');
                            formProduct.image = json.data.href;
                        } else {
                            var image_exists = _.find(formProduct.form_product_image.selected_images, function(image){
                                return json.data.name == image.name;
                            });
    
                            if (image_exists === undefined) {
                                formProduct.form_product_image.selected_images.push(json.data);
                            } else {
                                alert('Image already exists');
                            }
                        }
                        
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                    }
                });
            }
        }, 500);      
   })   ;

});