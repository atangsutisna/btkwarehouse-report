$('document').ready(function(){

    var form = new Vue({
        el: '#form-user-group',
        data: {
            form_status: {
                alert: false,
                has_errors: false,
                success: false,
                message: 'Undefined message'
            },            
            user_group_id: null,
            name: null,
        },
        methods: {
            save() {
                var app = this;
                if (app.user_group_id == null) {
                    console.info('create new user group');
                    app.create();
                } else {
                    app.update();
                }
            },
            create() {
                var app = this;
                var form_data = {
                    'name': app.name
                };
                //console.table(form_data);

                axios.post(appConfig.apiUri + '/user_group', form_data).then(function(response) {
                    var data = response.data.data;
                    app.user_group_id = data.user_group_id;
                    console.info('user group has been saved with id: '+ app.user_group_id);
                    
                    app.form_status.alert = true;
                    app.form_status.success = true;
                    app.form_status.has_errors = false;
                    app.form_status.message = '1 data telah berhasil ditambahkan';
                }).catch(function(error) {
                    $('#btn-save').removeAttr('disabled', 'disabled');

                    if (error.response.data.code == 400) {
                        var message = error.response.data.message;
                        message += '<ul>';
                        _.each(error.response.data.errors, function(value, key, list){
                            message += `<li>${value}</li>`;
                        });
                        message += '</ul>';
                    }
                    
                    if (error.response.status == 500) {
                        var message = 'Internal Server Error';
                    }

                    app.form_status.alert = true;
                    app.form_status.has_errors = true;
                    app.form_status.success = false;
                    app.form_status.message = message;
                }).finally(function(){
                    $('#btn-save').removeAttr('disabled');
                });
            },
            update() {
                var app = this;
                var form_data = {
                    'user_group_id': app.user_group_id,
                    'name': app.name
                };
                //console.table(form_data);

                axios.put(appConfig.apiUri + '/user_group', form_data).then(function(response) {
                    var data = response.data.data;
                    app.user_group_id = data.user_group_id;
                    console.info('user group has been saved with id: '+ app.user_group_id);
                    
                    app.form_status.alert = true;
                    app.form_status.success = true;
                    app.form_status.has_errors = false;
                    app.form_status.message = '1 data telah berhasil diperbaharui';
                }).catch(function(error) {
                    $('#btn-save').removeAttr('disabled', 'disabled');

                    if (error.response.data.code == 400) {
                        var message = error.response.data.message;
                        message += '<ul>';
                        _.each(error.response.data.errors, function(value, key, list){
                            message += `<li>${value}</li>`;
                        });
                        message += '</ul>';
                    }
                    
                    if (error.response.status == 500) {
                        var message = 'Internal Server Error';
                    }

                    app.form_status.alert = true;
                    app.form_status.has_errors = true;
                    app.form_status.success = false;
                    app.form_status.message = message;
                }).finally(function(){
                    $('#btn-save').removeAttr('disabled');
                });
            }
        },
        async created() {
            console.info('form user group was initialize');
            var app = this;
            app.user_group_id = $('input[name=user_group_id]').val();
            console.info('find user group with id: '+ app.user_group_id);
            if ( !$.isEmptyObject(app.user_group_id) ) {
                var user_group = await axios.get(appConfig.apiUri + '/user_group/' + app.user_group_id);
                app.user_group_id = user_group.data.data.user_group_id;
                app.name = user_group.data.data.name;
            } else {
                console.warn('user_group id doesn exists');
            }            
        }
    });

}); 