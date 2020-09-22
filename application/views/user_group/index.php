<section class="content-header">
    <h1>
        User Groups
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">User Groups</li>
    </ol>
</section>
<div class="content">
    <div class="row">
        <div class="col-lg-12 col-sm-12 col-12">
            <?= show_bootstrap_alert() ?>
            <div class="box box-danger">
                <div class="box-body">
                    <p><a href="<?= site_url('user_group/reg_form') ?>" class="btn btn-primary"><i class="fa fa-plus" aria-hidden="true"></i> Buat Baru</a></p>
                    <div class="table-responsive">
                        <table class="table table-striped" id="table-user-group" cellspacing="0" width="100%">
                            <thead>
                                <th>Group Name</th>
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