$('document').ready(function(){

    var form = new Vue({
        el: '#form-create',
        data: {
            form_status: {
                alert: false,
                has_errors: false,
                success: false,
                message: 'Undefined message'
            },            
            user_id: null,
            user_group_id: null,
            employee: null,
            firstname: null,
            lastname: '-',
            username: null,
            email: null,
            password: null,
            retype_password: null,
            status: null,
        },
        methods: {
            save() {
                var app = this;
                var form_data = {
                    'user_group_id': app.user_group_id,
                    'firstname': app.firstname,
                    'lastname': app.lastname,
                    'email': app.email,
                    'status': app.status,
                    'username': app.username,
                    'password': app.password,
                    'retype_password': app.retype_password 
                };
                //console.table(form_data);

                axios.post(appConfig.apiUri + '/user', form_data).then(function(response) {
                    var data = response.data.data;
                    app.user_id = data.user_id;

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
            }
        }
    });

    $('#employee-choices').select2().on('select2:select', function(e) {
        var data = e.params.data;
        form.firstname = data.text;
        console.info(form.firstname);
    });
});