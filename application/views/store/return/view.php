<section class="content-header">
    <h1>
        Retur Stok Etalase
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('home') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?= site_url('store/return_stock') ?>">Retur Stok Etalase</a></li>
        <li class="active">View</li>
    </ol>
</section>
<div class="content">
    <div class="row">
        <div class="col-lg-12 col-sm-12 col-12">
            <div class="box box-danger">
                <form class="form-horizontal" >
                    <div class="box-body">
                        <div class="box-header with-border">
                            <h3 class="box-title">No. <?= $return_stock->return_from_storefront_no ?></h3>
                        </div>
                        <div class="row" style="margin-top: 10px;">
                            <div class="col-lg-8 col-8 col-sm-8">
                                <div class="form-group">
                                    <label class="col-lg-3 col-sm-2 control-label">Tanggal Terima</label>
                                    <div class="col-lg-4 col-sm-4">
                                        <p class="form-control-static"><?= $return_stock->created_at ?></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 col-sm-2 control-label">Penerima</label>
                                    <div class="col-lg-6 col-sm-6">
                                        <p class="form-control-static"><?= $return_stock->receiver_name ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-4 col-sm-4">
                                <b>OPERATOR</b>: <br/>
                                <h4><?= get_operator($return_stock) ?></h4>                                                                
                                <b>TOTAL</b>: <br/>
                                <h4><?= currency_format($return_stock->total_amount) ?></h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-8 col-8 col-sm-8"></div>
                            <div class="col-lg-4 col-4 col-sm-4">
                                <div>
                                    <a class="btn btn-info" title="Cetak" target="_blank"
                                        href="<?= site_url('return_stock/print/'. $return_stock->return_from_storefront_id) ?>">
                                        <i class="fa fa-print" aria-hidden="true"></i>
                                    </a>
                                </div>
                            </div>
                        </div><hr/>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>No. </th>
                                        <th>Produk</th>
                                        <th>Jml/Unit</th>
                                        <th>Harga</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($return_stock->items as $key => $item) : ?>
                                    <tr>
                                        <td><?= $key + 1?></td>
                                        <td><?= $item->product_name ?></td>
                                        <td>
                                            <span class="pull-right"><?= $item->qty ?> <?= $item->qty_unit ?></span>
                                        </td>
                                        <td>
                                            <span class="pull-right"><?= currency_format($item->price) ?></span>
                                        </td>
                                        <td>
                                            <span class="pull-right"><?= currency_format($item->subtotal) ?></span></td>
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