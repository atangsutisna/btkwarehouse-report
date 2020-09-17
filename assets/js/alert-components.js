const AlertInfo = Vue.extend({
    props: ['message'],
    template: `<div class="alert alert-info alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-info"></i>Info!</h4>
                    {{message}}
                </div>`
});

const AlertDanger = Vue.extend({
    props: ['message'],
    template: `<div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                    <h4><i class="icon fa fa-ban"></i>Errors!</h4>
                    <span v-html="message"></span>
                </div>`
});

Vue.component('alert-component', {
    props: ['alert', 'has_errors', 'success', 'message'],
    methods: {
        hide() {
            var form_status = this.$parent.form_status;
            console.info('hide');
            form_status.alert = false;
            form_status.has_errors = false;
            form_status.success = false;
        },
    },
    template: `<div v-bind:class="{'alert': alert, 'alert-danger': has_errors, 'alert-info': success}" v-if="alert">
                    <button type="button" class="close" aria-hidden="true" v-on:click="hide">&times;</button>
                    <h4>
                        <i class="icon fa fa-ban" v-bind:class="{'icon': alert, 'fa-ban': has_errors, 'fa-info': success}"></i>
                        <span v-if="has_errors">ERROR!</span><span v-if="success">INFO!</span>
                    </h4>
                    <span v-html="message"></span>
                </div>`
});