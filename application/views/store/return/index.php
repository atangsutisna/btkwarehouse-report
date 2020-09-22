<section class="content-header">
    <h1>
        Retur Stok Etalase
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('home') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Retur Stok Etalase</li>
    </ol>
</section>
<div class="content">
    <div class="row">
        <div class="col-lg-12 col-sm-12 col-12">
            <div class="box box-danger">
                <div class="box-body">
                    <p><a href="<?= site_url('store/return_stock/create') ?>" class="btn btn-primary"><i class="fa fa-plus" aria-hidden="true"></i> Buat Baru</a></p>
                    <form class="form-horizontal" id="form-filter">
                        <div class="form-group">
                            <label class="col-lg-3 col-sm-3 control-label" for="receiver_name">Nama Penerima</label>
                            <div class="col-lg-9 col-sm-9">
                                <div class="col-lg-6 col-sm-6">
                                    <input type="text" name="receiver_name" class="form-control" id="receiver_name"/>
                                </div>                                
                            </div>
                        </div>                        
                        <div class="form-group">
                            <label class="col-lg-3 col-sm-3 control-label">Tanggal</label>
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
                                <button class="btn btn-primary" id="btn-filter">Cari</button>
                            </div>
                        </div>                                                
                    </form>
                    
                    <div class="table-responsive">
                        <table class="table table-condensed" id="table-return-stock" cellspacing="0" width="100%">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>No. Dokumen</th>
                                    <th>Penerima</th>
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