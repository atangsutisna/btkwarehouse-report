<section class="content-header">
    <h1>
        Daftar Stock Produk Supplier
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Daftar Stock Produk Supplier</li>
    </ol>
</section>
<div class="content">
    <div class="row">
        <div class="col-lg-12 col-sm-12 col-12">
            <div class="box box-danger">
                <div class="box-body">
                    <form class="form-horizontal" 
                        method="get" 
                        action="<?= site_url('reports/stock_supplier/print') ?>"
                        target="_blank">
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