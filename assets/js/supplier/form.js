var unitMeasurement = new Vue({
    el: '#form-supplier',
    data: {
        form_status: {
            alert: false,
            has_errors: false,
            success: false,
            message: 'Undefined message'
        },
        supplier_id: null,
        name: null,
        description: null,
    },
    methods: {
        doSave: function() {
            var app = this;
            if ( !$.isEmptyObject(app.supplier_id) ) {
                app.doUpdate();
            } else {
                app.doInsert();
            }
        },
        doInsert: function() {
            console.info('do insert');
            $('#btn-save').attr('disabled', 'disabled');

            var app = this;
            axios.post(appConfig.apiUri + '/supplier', {
                'name': app.name,
                'description': app.description,
            }).then(function(response) {
                var data = response.data.data;
                app.supplier_id = data.supplier_id;

                app.form_status.alert = true;
                app.form_status.success = true;
                app.form_status.message = '1 data telah berhasil ditambahkan';
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

                app.form_status.alert = true;
                app.form_status.has_errors = true;
                app.form_status.message = message;
            }).finally(function(){
                $('#btn-save').removeAttr('disabled');
            });
        },
        doUpdate: function() {
            console.info('do update');
            $('#btn-save').attr('disabled', 'disabled');

            var app = this;
            axios.put(appConfig.apiUri + '/supplier', {
                'supplier_id': app.supplier_id,
                'name': app.name,
                'description': app.description,
            }).then(function(response) {
                app.form_status.alert = true;
                app.form_status.success = true;
                app.form_status.message = '1 data telah berhasil diperbaharui';
            }).catch(function(error) {
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
                $('#btn-save').removeAttr('disabled');
            });
        }
    },
    created: async function() {
        var app = this;
        app.supplier_id = $('input[name=supplier_id]').val();
        if ( !$.isEmptyObject(app.supplier_id) ) {
            var supplier = await axios.get(appConfig.apiUri + '/supplier/' + app.supplier_id);
            app.name = supplier.data.data.name;
            app.description = supplier.data.data.description;    
        } else {
            console.warn('Supplier id doesn exists');
        }
    }
});