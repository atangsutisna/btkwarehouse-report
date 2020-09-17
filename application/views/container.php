<!DOCTYPE html>
<html lang="en">
<head>
<title>Btk Mart: Warehouse System</title>
<meta name="description" content="Free Bootstrap 4 Admin Theme | Pike Admin">
<meta name="keywords" content="Bootstrap 4, admin, theme, template, pike admin">
<?= $global_head ?>
</head>
<body class="hold-transition skin-red sidebar-mini">
<div class="wrapper">
    <!-- Main Header -->
    <header class="main-header">
        <!-- Logo -->
        <a href="<?= site_url('home') ?>" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><b>BTK</b></span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><b>BTK</b>Warehouse</span>
        </a>
        <!-- Header Navbar -->
        <?= $navigation ?>
    </header>    
    <!-- Left side column. contains the logo and sidebar -->
    <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <?= $sidebar ?>
        <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <?= $content ?>
    </div>
    <!-- /.content-wrapper -->

  <!-- Main Footer -->
  <?= $footer ?>
  <div class="control-sidebar-bg"></div>
  <?= $global_footer ?>
</div>
<!-- ./wrapper -->
</body>
</html>