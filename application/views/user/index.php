<section class="content-header">
    <h1>
        Users
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Users</li>
    </ol>
</section>
<div class="content">
    <div class="row">
        <div class="col-lg-12 col-sm-12 col-12">
            <?= show_bootstrap_alert() ?>
            <div class="box box-danger">
                <div class="box-body">
                    <p><a href="<?= site_url('user/create') ?>" class="btn btn-primary"><i class="fa fa-plus" aria-hidden="true"></i> Buat Baru</a></p>
                    <div class="table-responsive">
                        <table class="table table-condensed" id="table_user" cellspacing="0" width="100%">
                            <thead>
                                <th>Tanggal</th>
                                <th>Username</th>
                                <th>Nama Lengkap</th>
                                <th>Status</th>
                                <th>Action</th>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>