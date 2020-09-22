<section class="content-header">
    <h1>
        Penyesuaian Stok
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Penyesuaian Stok</li>
    </ol>
</section>
<div class="content">
    <div class="row">
        <div class="col-lg-12 col-sm-12 col-12">
            <div class="box box-danger">
                <form class="form-horizontal">
                <div class="box-body">
                    <p><a href="<?= site_url('stock_adjustment/create') ?>" class="btn btn-primary">
                        <i class="fa fa-plus" aria-hidden="true"></i> Buat Baru</a></p>
                    <form class="form-horizontal" id="form_filter">
                        <div class="form-group">
                            <label class="col-lg-3 col-sm-3 control-label">Tanggal</label>
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
                    <hr/>
                    <div class="table-responsive">
                        <table class="table table-condensed" id="table_stock_adjustment" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Tgl. Pembuatan</th>
                                    <th>Nama</th>
                                    <th>Jns Penyesuaian</th>
                                    <th class="text-right">Stok Sebelumnya</th>
                                    <th class="text-right">Penyesuaian Stok</th>
                                    <th class="text-right">Stok Setelahnya</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>                
                    </div>
                    
                </div>
                </form>
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