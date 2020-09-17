$('document').ready(function(){
    console.log('viewjs load');
    if ($('#checkbox_parent').is(':checked')) {
        $('select[name=parent_id]').val('');
    }
});