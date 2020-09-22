<section class="content-header">
    <h1>
        Metode Pembayaran
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Metode Pembayaran</li>
    </ol>
</section>
<div class="content">
    <div class="row">
        <div class="col-lg-12 col-sm-12 col-12">
            <div class="box box-danger">
                <div class="box-body">
                    <p><a href="<?= site_url('payment_method/create') ?>" class="btn btn-primary"><i class="fa fa-plus" aria-hidden="true"></i> Buat Baru</a></p>
                    <div class="table-responsive">
                        <table class="table table-condensed" id="table-payment-method" cellspacing="0" width="100%">
                            <thead>
                                <th>Nama</th>
                                <th>Keterangan</th>
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