<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>
        Profil
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Profil</li>
    </ol>
</section>
<div class="content">
    <?= show_bootstrap_alert() ?>
    <div class="row">
        <div class="col-lg-12 col-sm-12 col-12">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">Informasi Umum</h3>
                </div>
                <form role="form" method="post" id="form-profile"
                    class="form-horizontal">   
                    <div class="box-body">
                        <div class="form-group">
                            <label class="col-lg-3 col-sm-3 control-label">Nama</label>
                            <div class="col-8 col-lg-8 col-sm-8">
                                <div class="col-4 col-lg-4 col-sm-4" style="padding-left: 0;">
                                    <input type="text" name="first_name" placeholder="first name"
                                        class="form-control" value="<?= set_value('first_name', $user->first_name) ?>" required/>
                                    <?= form_error('first_name') ?>
                                </div>
                                <div class="col-4 col-lg-4 col-sm-4" style="padding-left: 5px;">
                                    <input type="text" name="last_name" placeholder="last name"
                                        class="form-control" value="<?= set_value('last_name', $user->last_name) ?>" required/>
                                    <?= form_error('last_name') ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 col-sm-3 control-label">Email</label>
                            <div class="col-4 col-lg-4 col-sm-4">
                                <input type="email" name="email" 
                                    class="form-control" value="<?= set_value('email', $user->email) ?>"
                                    readonly/>
                                <?= form_error('email') ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 col-sm-3 control-label">Username</label>
                            <div class="col-lg-3 col-sm-3">
                                <input type="text" name="username" 
                                    value="<?= set_value('username', $user->username) ?>"
                                    class="form-control" readonly="true" />
                                <?= form_error('username') ?>
                            </div>
                        </div>                        
                        <div class="form-group">
                            <label class="col-lg-3 col-sm-3 control-label">Status</label>
                            <div class="col-lg-3 col-sm-3">
                                <select name="status" class="form-control" disabled>
                                    <option disabled selected></option>
                                    <option value="1" <?= $user->active == 1 ? "selected" : ""?>>Aktif</option>
                                    <option value="0" <?= $user->active == 0 ? "selected" : ""?>>Tidak Aktif</option>
                                </select>                                                                
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-offset-3 col-sm-offset-3" style="margin-left: 27%;">
                                <button type="submit" class="btn btn-primary" id="btn-save" v-on:click="save">Simpan</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="content">
    <div class="row">
        <div class="col-lg-12 col-sm-12 col-12">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">Keamanan</h3>
                </div>            
                <form role="form" method="post" class="form-horizontal"
                    action="<?= site_url('profile/update_passwd') ?>">   
                    <div class="box-body">
                    <div class="form-group">
                            <label class="col-lg-3 col-sm-3 control-label">Password</label>
                            <div class="col-lg-3 col-sm-3">
                                <input type="password" name="password" class="form-control"/>
                                <?= form_error('password') ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 col-sm-3 control-label">Ketik Ulang Password</label>
                            <div class="col-lg-3 col-sm-3">
                                <input type="password" name="password_confirm" class="form-control"/>
                                <?= form_error('password_confirm') ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-offset-3 col-sm-offset-3" style="margin-left: 27%;">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>