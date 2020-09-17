<section class="content-header">
    <h1>
        Metode Pembayaran
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?= site_url('payment_method') ?>">Metode Pembayaran</a></li>
        <li class="active">Metode Pembayaran Baru</li>
    </ol>
</section>
<div class="content">
    <div class="row">
        <div class="col-lg-12 col-sm-12 col-12">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">Metode Pembayaran</h3>
                </div>
                <form role="form" 
                    method="post" 
                    class="form-horizontal" 
                    action="<?= site_url('payment_method/update/'. $payment_method->payment_method_id) ?>">   
                    <?= form_hidden('payment_method_id', isset($payment_method) ? $payment_method->payment_method_id : NULL) ?>
                    <div class="box-body">
                        <?php if (count($this->form_validation->error_array()) > 0) : ?>
                            <div class="alert alert-danger" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                <h4><i class="icon fa fa-info"></i> ERRORS!</h4>
                                <ul>
                                    <?php 
                                    $errors = $this->form_validation->error_array();
                                    foreach ($errors as $error) {
                                        echo "<li>$error</li>";
                                    }
                                    ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        <?= show_bootstrap_alert() ?>
                        <div class="form-group">
                            <label class="col-lg-3 col-sm-3 control-label">Nama *</label>
                            <div class="col-lg-4 col-sm-4">
                                <input type="text" 
                                    name="name" 
                                    class="form-control"
                                    value="<?= set_value('name', isset($payment_method) ? $payment_method->payment_method_name : NULL) ?>"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 col-sm-3 control-label">Keterangan</label>
                            <div class="col-lg-4 col-sm-4">
                                <textarea type="text" name="description" rows="3" 
                                    class="form-control"><?= isset($payment_method) ? $payment_method->payment_method_description : NULL ?></textarea>
                            </div>
                        </div>                        
                        <div class="form-group">
                            <label class="col-lg-3 col-sm-3 control-label">Status</label>
                            <div class="col-lg-2 col-sm-2">
                                <?= 
                                form_dropdown('status', ['active' => 'ACTIVE', 'nonactive' => 'NONACTIVE'], 
                                isset($payment_method) ? $payment_method->status : NULL, 
                                ['class' => 'form-control'])
                                ?>
                            </div>
                        </div>                        
                        <div class="form-group">
                            <div class="col-lg-offset-3 col-sm-offset-3" style="margin-left: 26.5%;">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-floppy-o" aria-hidden="true"></i> Simpan
                                </button>
                                <?= anchor('payment_method', 'Kembali', ['class' => 'btn btn-default']) ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>