<section class="content-header">
    <h1>
        Daftar Retur Pembelian
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Daftar Retur Pembelian</li>
    </ol>
</section>
<div class="content">
    <div class="row">
        <div class="col-lg-12 col-sm-12 col-12">
            <div class="box box-danger">
                <div class="box-body">
                    <form class="form-horizontal" 
                        method="get" 
                        action="<?= site_url('reports/retur_pembelian/print') ?>"
                        target="_blank">
                        <div class="form-group <?= form_error('supplier_id') ? 'has-error' : '' ?>">
                            <label class="col-lg-3 col-sm-3 control-label">Supplier</label>
                            <div class="col-lg-9 col-sm-9">
                                <div class="col-lg-6 col-sm-8">
                                    <select name="sup_id" class="form-control" id="supplier-choices">
                                        <option selected disabled="disabled">Pilih Supplier</option>
                                        <?php foreach ($supplier as $row) : ?>
                                        <option value="<?= $row->supplier_id ?>"><?= $row->name ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-lg-3 col-sm-3" style="padding-left: 30px;">
                                <button type="submit" class="btn btn-info" id="btn-search">
                                    <i class="fa fa-print" aria-hidden="true"></i> Print
                                </button>
                            </div>
                        </div>    
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>