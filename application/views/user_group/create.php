<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        User Group
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?= site_url('user_group') ?>">User Groups</a></li>
        <li class="active"><?= $title ?></li>
    </ol>
</section>
<div class="content">
    <div class="row">
        <div class="col-lg-12 col-sm-12 col-12">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= $title ?></h3>
                </div>
                <div class="box-body">
                    <?php $this->load->view('user_group/_form') ?>
                </div>
            </div>
        </div>
    </div>
</div>