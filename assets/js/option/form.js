var optionTypes = new Vue({
    el: '#form-option',
    data: {
        form_status: {
            alert: false,
            has_errors: false,
            success: false,
            message: 'Undefined message'
        },
        option_id: null,
        name: null,
        type: null,
        sort_order: null,
        values: [],
        status: 'draft'
    },
    methods: {
        addChoice: function() {
            this.values.push({
                option_value_id: null,
                option_value_description: null,
                image: null,
                sort_order: 0
            });
        },
        doRemove: function(idx) {
            if (confirm('Are you sure ?')) {
                this.values.splice(idx, 1);
            }  
        },
        doSave: function() {
            var app = this;
            app.values = _.filter(app.values, function(option_value){
                return option_value.option_value_description !== null;
            }); 
            if ( !$.isEmptyObject(app.option_id) ) {
                app.doUpdate();
            } else {
                app.doInsert();
            }
        },
        doInsert: function() {
            console.info('do insert');
            $('#btn-save').attr('disabled', 'disabled');

            var app = this;
            axios.post(appConfig.apiUri + '/option', {
                'option_id': app.option_id,
                'name': app.name,
                'type': app.type,
                'sort_order': app.sort_order,
                'option_values': app.values
            }).then(function(response){
                var data = response.data.data;
                app.option_id = data.option_id;

                app.form_status.alert = true;
                app.form_status.success = true;
                app.form_status.message = '1 data telah berhasil ditambahkan';
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
        },
        doUpdate: function() {
            $('#btn-save').attr('disabled', 'disabled');

            var app = this;
            axios.put(appConfig.apiUri + '/option', {
                'option_id': app.option_id,
                'name': app.name,
                'type': app.type,
                'sort_order': app.sort_order,
                'option_values': app.values
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
            });;
        },
        notifyMe: function() {
            var alertInfo = new AlertDanger({
                propsData: {
                  message: 'Data cannot be saved. Internal server error'
                }
            }).$mount('#alert-container');    
        }
    },
    created: async function() {
        var app = this;
        app.option_id = $('input[name=option_id]').val();
        console.info('created');
        if ( !$.isEmptyObject(app.option_id) ) {
            var option = await axios.get(appConfig.apiUri + '/option/' + app.option_id);
            app.name = option.data.data.name;
            app.type = option.data.data.type;
            app.description = option.data.data.description;
            app.sort_order = option.data.data.sort_order;

            option.data.data.values.forEach(function(item, idx) {
                app.values.push(item);        
            });    
        }
        
        
    }
})