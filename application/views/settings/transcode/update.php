<section class="content-header">
    <h1>
        Pengaturan Kode
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?= site_url('settings/transcode') ?>">Pengaturan Kode</a></li>
        <li class="active">Update Kode</li>
    </ol>
</section>
<div class="content">
    <div class="row">
        <div class="col-lg-12 col-sm-12 col-12">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">Update Kode</h3>
                </div>            	
                <div class="box-body">
                    <?= show_bootstrap_alert() ?>
                	<?= form_open('settings/transcode/update/'. $transcode->seq_name, ['class' => 'form-horizontal']) ?>
                    <div class="form-group">
                        <label class="col-lg-3 col-sm-3 control-label">Kode</label>
                        <div class="col-lg-4 col-sm-4">
                            <input type="text" name="seq_group" class="form-control" 
                            value="<?= set_value('seq_group', $transcode->seq_group) ?>"/>
                            <?= form_error('seq_group') ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-lg-offset-3 col-sm-offset-3" style="margin-left: 26.5%;">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-floppy-o" aria-hidden="true"></i> Simpan
                            </button>
                        </div>
                    </div>
                	<?= form_close() ?>
                </div>
            </div>
        </div>
    </div>
</div>