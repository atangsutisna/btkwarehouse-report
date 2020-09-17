<section class="content-header">
    <h1>
        Retur
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('home') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?= site_url('retur') ?>">Retur</a></li>
        <li class="active">Retur</li>
    </ol>
</section>
<div class="content">
    <div class="row">
        <div class="col-lg-12 col-sm-12 col-12">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= $retur_data->retur_no ?></h3>
                </div>
                <form role="form" method="post" class="form-horizontal" id="form-retur">  
                    <div class="box-body">
                        <div class="row">
                            <div class="col-lg-12 col-12 col-sm-12">
                                <div class="form-group">
                                    <label class="col-lg-3 col-sm-3 control-label">Tgl. Pembuatan:</label>
                                    <div class="col-lg-5 col-sm-5">
                                        <p class="form-control-static"><?= format_date($retur_data->created_at) ?></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 col-sm-3 control-label">Supplier</label>
                                    <div class="col-lg-7 col-sm-7">
                                        <p class="form-control-static"><?= $retur_data->supplier_name ?></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 col-sm-3 control-label">Status:</label>
                                    <div class="col-lg-5 col-sm-5">
                                        <p class="form-control-static"><?= strtoupper($retur_data->status) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end header of purchase order -->
                        <div class="table-responsive">
                            <table class="table table-condensed">
                                <thead>
                                    <tr>
                                        <th>No. </th>
                                        <th>Nama</th>
                                        <th class="text-right">Qty</th>
                                        <th>Unit</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($retur_data->return_items as $key => $value) : ?>
                                    <tr>
                                        <td><?= $key + 1?></td>
                                        <td>
                                            <?= $value->product_name ?><br/>
                                            <?= $value->product_model ?>
                                        </td>
                                        <td class="col-sm-1 col-lg-1 text-right"><?= $value->qty ?? 0?></td>
                                        <td class="col-sm-1 col-lg-1"><?= $value->qty_unit ?? '?' ?></td>
                                        <td><?= $value->note ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>                        
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>