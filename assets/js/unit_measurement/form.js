var unitMeasurement = new Vue({
    el: '#form-unit-measurement',
    data: {
        form_status: {
            alert: false,
            has_errors: false,
            success: false,
            message: 'Undefined message'
        },
        unit_measurement_id: null,
        name: null,
        description: null,
        symbol: null,
        status: null
    },
    methods: {
        doSave: function() {
            var app = this;
            console.info('do save');
            if ( !$.isEmptyObject(app.unit_measurement_id) ) {
                app.doUpdate();
            } else {
                app.doInsert();
            }
        },
        doInsert: function() {
            console.info('do insert');
            $('#btn-save').attr('disabled', 'disabled');

            var app = this;
            axios.post(appConfig.apiUri + '/unit_measurement', {
                'name': app.name,
                'description': app.description,
                'symbol': app.symbol,
                'status': app.status,
            }).then(function(response) {
                var data = response.data.data;
                app.unit_measurement_id = data.unit_measurement_id;
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
            axios.put(appConfig.apiUri + '/unit_measurement', {
                'unit_measurement_id': app.unit_measurement_id,
                'name': app.name,
                'description': app.description,
                'symbol': app.symbol,
                'status': app.status,
            }).then(function(response){
                $('#btn-save').text('Simpan');
                app.form_status.alert = true;
                app.form_status.success = true;
                app.form_status.message = '1 data telah berhasil diperbaharui';
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
        }
    },
    created: async function() {
        var app = this;
        app.unit_measurement_id = $('input[name=unit_measurement_id]').val();
        var unit_measurement = await axios.get(appConfig.apiUri + '/unit_measurement/' + app.unit_measurement_id);
        app.name = unit_measurement.data.data.name;
        app.description = unit_measurement.data.data.description;
        app.symbol = unit_measurement.data.data.symbol;
        app.status = unit_measurement.data.data.status;
    }
});