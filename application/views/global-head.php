<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<title>AdminLTE 2 | APLIKASI ABSENSI</title>
<!-- Tell the browser to be responsive to screen width -->
<meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
<!-- Bootstrap 3.3.6 -->
<link rel="stylesheet" href="<?= base_url("assets/vendor/bootstrap/bootstrap.min.css") ?>">
<!-- Font Awesome -->
<link rel="stylesheet" href="<?= base_url("assets/vendor/adminlte/font-awesome/css/font-awesome.min.css") ?>">
<!-- Ionicons 
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
-->
<!-- Theme style -->
<!--select2 -->
<link rel="stylesheet" href="<?= base_url("assets/vendor/select2/css/select2.min.css") ?>">
<link rel="stylesheet" href="<?= base_url("assets/vendor/adminlte/AdminLTE.min.css") ?>">
<!-- AdminLTE Skins. We have chosen the skin-blue for this starter
    page. However, you can choose any other skin. Make sure you
    apply the skin class to the body tag so the changes take effect.
-->
<link rel="stylesheet" href="<?= base_url("assets/vendor/adminlte/skin/skin-red.css") ?>">
<link rel="stylesheet" href="<?= base_url("assets/vendor/data-tables/datatables.min.css") ?>">
<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
<!--[if lt IE 9]>
<script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
<![endif]-->  
<style type="text/css">
    @media (min-width: 768px) {
      .modal-xl {
        width: 90%;
       max-width:1200px;
      }
    }    

</style>
<script src="<?= base_url("assets/js/jquery-2.2.3.min.js") ?>"></script>
<!-- Bootstrap 3.3.6 -->
<script src="<?= base_url("assets/js/bootstrap.min.js") ?>"></script>
<!-- accounting js -->
<script src="<?php echo base_url("assets/js/accounting.min.js") ?>"></script>
<script type="text/javascript">
    accounting.settings = {
        currency: {
            symbol : "Rp",
            format: "%s%v", // controls output: %s = symbol, %v = value/number (can be object: see below)
            decimal : ",",  // decimal point separator
            thousand: ".",  // thousands separator
            precision : 0   // decimal places
        },
        number: {
            precision : 0,  // default precision on numbers is 0
            thousand: ".",
            decimal : ","
        }
    }
</script>
<!-- AdminLTE App -->
<script src="<?= base_url("assets/js/app.min.js") ?>"></script>
<!-- summernote -->
<link href="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote.css" rel="stylesheet">
<script src="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote.js"></script>
<!-- app config -->
<script src="<?= base_url("assets/js/config.js") ?>"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
<script src="https://unpkg.com/vuex@3.1.2/dist/vuex.js"></script>
<script src="<?= base_url("assets/js/underscore-min.js") ?>"></script>
<script src="<?= base_url("assets/js/alert-components.js") ?>"></script>