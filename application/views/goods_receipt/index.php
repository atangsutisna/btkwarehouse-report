<section class="content-header">
    <h1>
        Penerimaan Barang
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Barang Masuk</li>
    </ol>
</section>
<div class="content">
    <div class="row">
        <div class="col-lg-12 col-sm-12 col-12">
            <div class="box box-danger">
                <div class="box-body">
                    <p><a href="<?= site_url('goods_receipt/new_form') ?>" class="btn btn-primary"><i class="fa fa-plus" aria-hidden="true"></i> Buat Baru</a></p>
                    <form class="form-horizontal">
                        <div class="form-group">
                            <label class="col-lg-3 col-sm-3 control-label">No</label>
                            <div class="col-lg-9 col-sm-9">
                                <div class="col-lg-3 col-sm-3">
                                    <input type="text" name="goods_receipt_no" class="form-control" id="goods_receipt_no"/>
                                </div>                                
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-3 col-sm-3 col-lg-3 control-label">Supplier</label>
                            <div class="col-lg-9 col-sm-9">
                                <div class="col-lg-3 col-sm-3">
                                    <select class="form-control select2" style="width: 100%;" id="supplier-choices">
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-3 col-sm-3 control-label">Tgl</label>
                            <div class="col-lg-9 col-sm-9">
                                <div class="col-lg-3 col-sm-3">
                                    <input type="date" name="start_date" class="form-control" id="start_date"/>
                                </div>
                                <div class="col-lg-3 col-sm-3">
                                    <input type="date" name="end_date" class="form-control" id="end_date"/>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-lg-3 col-sm-3" style="padding-left: 30px;">
                                <button class="btn btn-primary" id="btn-filter">
                                    <i class="fa fa-filter" aria-hidden="true"></i> Filter
                                </button>
                                <a class="btn btn-default" id="btn-reset">Reset Filter</a>
                            </div>
                        </div>                                                
                    </form>
                    
                    <div class="table-responsive">
                        <table class="table table-condensed" id="table_goods_receipt" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>No. PO</th>
                                    <th>Supplier</th>
                                    <th>Tgl. Pembuatan</th>
                                    <th>Status</th>
                                    <th>#</th>
                                </tr>
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
<!--
<form class="form-horizontal">

</form>
<table class="table table-striped" id="table_goods_receipt">
    <thead>
        <tr>
            <th>Supplier</th>
            <th>Tanggal</th>
            <th>No. Stok masuk</th>
            <th>Status Perubahan Stok</th>
            <th>Unit</th>
            <th>Catatan</th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table> -->