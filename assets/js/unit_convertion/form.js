var unitMeasurement = new Vue({
    el: '#form-unit-convertion',
    data: {
        form_status: {
            alert: false,
            has_errors: false,
            success: false,
            message: 'Undefined message'
        },
        unit_convertion_id: null,
        base_unit_measurement_id: null,
        to_unit_measurement_id: null,
        multiply_rate: 0.00,
        divide_rate: 0.00,
    },
    methods: {
        doSave: function() {
            var app = this;
            console.info(app.unit_convertion_id);
            if ( !$.isEmptyObject(app.unit_convertion_id) ) {
                console.info('do update');
                app.doUpdate();
            } else {
                console.info('do insert');
                app.doInsert();
            }
        },
        doInsert: function() {
            $('#btn-save').attr('disabled', 'disabled');

            var app = this;
            axios.post(appConfig.apiUri + '/unit_convertion', {
                'base_unit_measurement_id': app.base_unit_measurement_id,
                'to_unit_measurement_id': app.to_unit_measurement_id,
                'multiply_rate': app.multiply_rate,
                'divide_rate': app.divide_rate,
            }).then(function(response) {
                var data = response.data.data;

                app.unit_convertion_id = data.unit_convertion_id;
                app.base_unit_measurement_id = data.base_unit_measurement_id;
                app.to_unit_measurement_id = data.to_unit_measurement_id;
                app.multiply_rate = data.multiply_rate;
                app.divide_rate = data.divide_rate;

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
            $('#btn-save').attr('disabled', 'disabled');

            var app = this;
            axios.put(appConfig.apiUri + '/unit_convertion', {
                'unit_convertion_id': app.unit_convertion_id,
                'base_unit_measurement_id': app.base_unit_measurement_id,
                'to_unit_measurement_id': app.to_unit_measurement_id,
                'multiply_rate': app.multiply_rate,
                'divide_rate': app.divide_rate,
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
        app.unit_convertion_id = $('input[name=unit_convertion_id]').val();
        if ( !$.isEmptyObject(app.unit_convertion_id) ) {
            var unit_convertion = await axios.get(appConfig.apiUri + '/unit_convertion/' + app.unit_convertion_id);
            console.table(unit_convertion.data.data);
            app.base_unit_measurement_id = unit_convertion.data.data.base_unit_measurement_id;
            app.to_unit_measurement_id = unit_convertion.data.data.to_unit_measurement_id;
            app.multiply_rate = parseFloat(unit_convertion.data.data.multiply_rate);
            app.divide_rate = unit_convertion.data.data.divide_rate;    
        }
    }
});