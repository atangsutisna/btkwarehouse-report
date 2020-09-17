<section class="content-header">
    <h1>
        Hak Akses
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?= site_url('settings/user_role') ?>">Hak Akses</a></li>
        <li class="active"><?= $user_role->name ?></li>
    </ol>
</section>
<div class="content">
    <div class="row">
        <div class="col-lg-12 col-sm-12 col-12">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">Pengaturan Hak Akses</h3>
                </div>
                <div class="box-body">
                    <div class="row">
                        <div class="col-lg-12 col-12 col-sm-12">
                            <?= show_bootstrap_alert() ?>
                        </div>
                    </div>
                    <div class="row">
                        <form class="form-horizontal" method="POST">
                            <div class="col-lg-12 col-12 col-sm-12">
                                <div class="form-group">
                                    <label class="col-lg-3 col-sm-3 control-label">ID</label>
                                    <div class="col-lg-5 col-sm-5">
                                        <p class="form-control-static"><?= $user_role->name?></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 col-sm-3 control-label">Nama</label>
                                    <div class="col-lg-5 col-sm-5">
                                        <p class="form-control-static"><?= $user_role->description?></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 col-sm-3 control-label">Hak akses</label>
                                    <div class="col-lg-5 col-sm-5">
                                        <select name="permission_id" class="form-control select2">
                                            <option selected>--Select--</option>
                                            <?php foreach ($permissions as $item) : ?>
                                                <option value="<?= $item->ID ?>"><?= $item->Description ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-sm-offset-3 col-4 col-sm-4 col-lg-4">
                                        <button type="submit" class="btn btn-primary" id="btn-search">Tambah Hak Akses</button>
                                    </div>
                                </div>                      
                            </div>
                        </form>
                    </div> <!--/end of header -->
                    <p>&nbsp;</p>
                    <div class="table-responsive">
                        <table class="table table-condensed" id="table-permission">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>ID</th>
                                    <th>Nama</th>
                                    <th>#</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($user_role_permissions as $key => $item) : ?>
                                <tr>
                                    <td><?= $key + 1 ?></td>
                                    <td><?= $item->Title  ?></td>
                                    <td><?= $item->Description  ?></td>
                                    <td>
                                        [<?= anchor('settings/user_role/delete/'.$item->RoleID.'?permission_id='.$item->PermissionID, 'hapus') ?>]
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>