/** 
var form_category = new Vue({
    el: '#form-category',
    data: {
        form_status: {
            alert: false,
            has_errors: false,
            success: false,
            message: 'Undefined message'
        },
        category_id: null,
        parent_id: null,
        parent: false,
        name: null,
        description: null,
        meta_title: null,
        meta_description: null,
        meta_keyword: null,
        sort_order: null,
        status: null
    },
    methods: {
        doSave: function() {
            var app = this;
            if ( !$.isEmptyObject(app.category_id) ) {
                app.doUpdate();
            } else {
                app.doInsert();
            }
        },
        doInsert: function() {
            console.info('do insert');
            $('#btn-save').attr('disabled', 'disabled');

            var app = this;
            axios.post(appConfig.apiUri + '/category', {
                'parent_id': app.parent_id,
                'name': app.name,
                'description': app.description,
                'meta_title': app.meta_title,
                'meta_description': app.meta_description,
                'meta_keyword': app.meta_keyword,
                'sort_order': app.sort_order,
                'status': app.status        
            }).then(function(response) {
                var data = response.data.data;
                app.category_id = data.category_id;

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
                
                if (error.response.data.code == 500) {
                    message = 'Internal server error';
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
            axios.put(appConfig.apiUri + '/category', {
                'category_id': app.category_id,
                'parent_id': app.parent_id,
                'name': app.name,
                'description': app.description,
                'meta_title': app.meta_title,
                'meta_description': app.meta_description,
                'meta_keyword': app.meta_keyword,
                'sort_order': app.sort_order,
                'status': app.status        
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

                if (error.response.data.code == 500) {
                    message = 'Internal server error';
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
        app.category_id = $('input[name=category_id]').val();
        if ( !$.isEmptyObject(app.category_id) ) {
            //TODO: check if not found
            var category = await axios.get(appConfig.apiUri + '/category/' + app.category_id);
            app.parent_id = category.data.data.parent_id;
            app.name = category.data.data.name;
            app.description = category.data.data.description;    
            app.meta_title = category.data.data.meta_title; 
            app.meta_description = category.data.data.meta_description;
            app.meta_keyword = category.data.data.meta_keyword;
            app.sort_order = category.data.data.sort_order;
            app.status = category.data.data.status; 
        } else {
            console.warn('Category id doesn exists');
        }
    }
});
**/
$('document').ready(function(){
    if ($('select[name=parent_id]').is(':checked')) {
        console.log('checked');
        $('select[name=parent_id]').val('');
    }

    $("#checkbox_parent").change(function() {
        if(this.checked) {
            $('select[name=parent_id]').attr('disabled', 'disabled');
            $('select[name=parent_id]').val('');
            return true;
        }

        $('select[name=parent_id]').removeAttr('disabled');
        return false;
    });
});