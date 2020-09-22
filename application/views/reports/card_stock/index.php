<section class="content-header">
    <h1>
        Transaksi Kartu Stok
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Transaksi Kartu Stok</li>
    </ol>
</section>
<div class="content">
    <div class="row">
        <div class="col-lg-12 col-sm-12 col-12">
            <div class="box box-danger">
                <div class="box-body">
                    <form class="form-horizontal" 
                        method="get" 
                        action="<?= site_url('reports/card_stock/print') ?>"
                        target="_blank">
                        <div class="form-group <?= form_error('product_id') ? 'has-error' : '' ?>">
                            <label class="col-lg-3 col-sm-3 control-label">Produk</label>
                            <div class="col-lg-9 col-sm-9">
                                <div class="col-lg-6 col-sm-8">
                                    <select name="product_id" id="product-choices" class="form-control">
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group <?= form_error('start_date') || form_error('end_date') ? 'has-error' : ''?>">
                            <label class="col-lg-3 col-sm-3 control-label">Periode</label>
                            <div class="col-lg-9 col-sm-9">
                                <div class="col-lg-3 col-sm-4">
                                    <input type="date" name="start_date" class="form-control"/>
                                </div>
                                <div class="col-lg-3 col-sm-4">
                                    <input type="date" name="end_date" class="form-control"/>
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