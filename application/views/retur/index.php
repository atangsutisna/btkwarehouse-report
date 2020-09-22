<section class="content-header">
    <h1>
        Retur Pembelian
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('home') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Retur Pembelian</li>
    </ol>
</section>
<div class="content">
    <div class="row">
        <div class="col-lg-12 col-sm-12 col-12">
            <div class="box box-danger">
                <div class="box-body">
                    <p><a href="<?= site_url('retur/create') ?>" class="btn btn-primary">Buat Baru</a></p>
                    <form class="form-horizontal" id="form_filter">
                        <div class="form-group">
                            <label class="col-3 col-sm-3 col-lg-3 control-label">No.</label>
                            <div class="col-lg-3 col-sm-3">
                                <input type="text" name="retur_no" class="form-control"/>
                            </div>
                        </div>     
                        <div class="form-group">
                            <label class="col-3 col-sm-3 col-lg-3 control-label">Supplier</label>
                            <div class="col-lg-3 col-sm-3">
                                <select class="form-control select2" style="width: 100%;" id="supplier-choices">
                                </select>
                            </div>
                        </div>     
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-4 col-sm-4 col-lg-4">
                                <button type="submit" class="btn btn-primary" id="btn-search">
                                    <i class="fa fa-filter" aria-hidden="true"></i> Filter
                                </button>
                                <button type="submit" class="btn btn-default" id="btn-reset">Reset Filter</button>
                            </div>
                        </div>                      
                    </form>
                    <div class="table-responsive">
                        <table class="table table-striped" id="table_retur" cellspacing="0" width="100%">
                            <thead>
                                <th>No. </th>
                                <th>Supplier</th>
                                <th>Tgl. Pembuatan</th>
                                <th>Status</th>
                                <th>#</th>
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