<script src="<?php echo base_url("assets/js/moment.min.js") ?>"></script>   
<!-- datatable --> 
<script src="<?php echo base_url("assets/vendor/data-tables/datatables.min.js") ?>"></script>
<script src="https://cdn.datatables.net/scroller/2.0.2/js/dataTables.scroller.min.js"></script>
<!-- select2 -->
<script src="<?php echo base_url("assets/vendor/select2/js/select2.full.min.js") ?>"></script>
<script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>
<script>
const appConfig = {
    baseUri: '<?= base_url(); ?>',
    apiUri: '<?= site_url() ?>api',
    commerceBaseUri: 'http://btk-commerce.localhost',
    commerceBaseImageUri: 'http://btk-commerce.localhost/image/cache',
}
console.log('appConfig: ', appConfig);
$('document').ready(function() {
    const current_url = window.location;

    $('ul.sidebar-menu a').filter(function() {
        return this.href == current_url;
    }).parent().addClass('active');
  
    $('ul.treeview-menu a').filter(function() {
        return this.href == current_url;
    }).closest('.treeview').addClass('active');
});

</script>
<?php if (isset($js_resources)) : ?>
<?php foreach ($js_resources as $file_path) : ?>
<script src="<?php echo base_url($file_path) ?>"></script>
<?php endforeach; ?>
<?php endif; ?>