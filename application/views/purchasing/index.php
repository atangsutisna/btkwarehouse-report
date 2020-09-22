<section class="content-header">
    <h1>
        Transaksi Pembelian
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Transaksi Pembelian</li>
    </ol>
</section>
<div class="content">
    <div class="row">
        <div class="col-lg-12 col-sm-12 col-12">
            <div class="box box-danger">
                <div class="box-body">
                    <p>
                        <a href="<?= site_url('purchasing/create') ?>" class="btn btn-primary">
                            <i class="fa fa-plus" aria-hidden="true"></i> Buat Baru
                        </a>
                        <a href="<?= site_url('purchasing/print') ?>" class="btn btn-info">
                            <i class="fa fa-print" aria-hidden="true"></i> Print
                        </a>
                    </p>
                    <form class="form-horizontal" id="form-filter">
                        <div class="form-group">
                            <label class="col-lg-3 col-sm-3 control-label" for="receiver_name">No. Pembelian</label>
                            <div class="col-lg-9 col-sm-9">
                                <div class="col-lg-3 col-sm-3">
                                    <input type="text" name="purchasing_no" class="form-control" id="purchasing_no"/>
                                </div>                                
                            </div>
                        </div>                        
                        <div class="form-group">
                            <label class="col-lg-3 col-sm-3 control-label">Tgl. Pembuatan</label>
                            <div class="col-lg-9 col-sm-9">
                                <div class="col-lg-3 col-sm-3">
                                    <input type="date" name="start_date" class="form-control"/>
                                </div>
                                <div class="col-lg-3 col-sm-3">
                                    <input type="date" name="end_date" class="form-control"/>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-lg-3 col-sm-3" style="padding-left: 30px;">
                                <button type="submit" class="btn btn-primary" id="btn-search">
                                    <i class="fa fa-filter" aria-hidden="true"></i> Filter
                                </button>
                                <a class="btn btn-default" id="btn-reset">Reset Filter</a>
                            </div>
                        </div>                                                
                    </form>
                    
                    <div class="table-responsive">
                        <table class="table table-condensed" id="table-move-storefront" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>No. Pembelian</th>
                                    <th>No. Penerimaan Barang</th>
                                    <th>Supplier</th>
                                    <th>Tgl. Pembuatan</th>
                                    <th>Total</th>
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