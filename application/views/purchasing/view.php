<section class="content-header">
    <h1>
        Transaksi Pembelian
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?= site_url('purchasing') ?>"><i class="fa fa-dashboard"></i> Transaksi Pembelian</a></li>
        <li class="active"><?= $purchasing->purchasing_no ?></li>
    </ol>
</section>
<div class="content">
    <div class="row">
        <div class="col-lg-12 col-sm-12 col-12">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title"><?= $purchasing->purchasing_no ?></h3>
                </div>
                <form class="form-horizontal">
                    <div class="box-body">
                        <div class="row" style="margin-top: 10px;">
                            <div class="col-lg-6 col-6 col-sm-6">
                                <div class="form-group">
                                    <label class="col-lg-3 col-sm-2 control-label">No. Penerimaan Barang</label>
                                    <div class="col-lg-8 col-sm-8">
                                        <div class="row">
                                            <div class="col-lg-6 col-sm-6 col-6">
                                                <p class="form-control-static"><?= $purchasing->goods_receipt_no ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 col-sm-2 control-label">Nama Supplier</label>
                                    <div class="col-lg-6 col-sm-6">
                                        <p class="form-control-static"><?= $purchasing->supplier_name ?></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 col-sm-2 control-label">Tgl. Invoice</label>
                                    <div class="col-lg-4 col-sm-4">
                                        <p class="form-control-static"><?= format_date($purchasing->invoice_date) ?></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 col-sm-2 control-label">Tgl. Terima</label>
                                    <div class="col-lg-4 col-sm-4">
                                        <p class="form-control-static"><?= format_date($purchasing->receive_date) ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-6 col-sm-6">
                                <div class="form-group">
                                    <label class="col-lg-3 col-sm-2 control-label">Pembayaran</label>
                                    <div class="col-lg-4 col-sm-4">
                                        <p class="form-control-static"><?= $purchasing->payment_method_name ?></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 col-sm-2 control-label">Jatuh Tempo</label>
                                    <div class="col-lg-4 col-sm-4">
                                        <p class="form-control-static"><?= format_date($purchasing->due_date) ?></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 col-sm-2 control-label">Catatan</label>
                                    <div class="col-lg-4 col-sm-4">
                                        <p class="form-control-static"><?= $purchasing->note ?></p>
                                    </div>
                                </div>
                            </div>
                        </div> 
                        <div class="table-responsive" style="overflow-x:auto;">
                            <table class="table table-bordered" id="table_new_receive_order" cellspacing="0" width="100%">
                                <tr>
                                    <th>No</th>
                                    <th>
                                        Produk<br/>
                                        Kode Produk / Barcode<br/>
                                    </th>
                                    <th>Jml</th>
                                    <th>Unit</th>
                                    <th>Harga Awal</th>
                                    <th>Diskon (Rp)</th>
                                    <th>Harga Akhir</th>
                                    <th>Total</th>
                                    <!-- offline -->
                                    <th>Margin <br/>Offline</th>
                                    <th>Harga Jual Offline<br>Pcs</th>
                                    <th>Harga Jual Offline<br>Rasio</th>
                                    <!-- online -->
                                    <th>Margin <br/>Online</th>
                                    <th>Harga Jual Online<br>Pcs</th>
                                    <th>Harga Jual Online<br>Rasio</th>
                                </tr> 
                                <!-- items -->
                                <?php foreach ($purchasing->purchasing_items as $key => $purchasing_item) : ?>
                                <tr>
                                    <td><?= $key + 1?></td>
                                    <td class="col-sm-2 col-lg-2">
                                        <?= $purchasing_item->product_name ?><br/>
                                        <small><?= $purchasing_item->product_model ?></small><br>
                                    </td>
                                    <td class="text-right"><?= $purchasing_item->qty ?></td>
                                    <td><?= $purchasing_item->qty_unit ?></td>
                                    <td class="col-sm-1 col-lg-1">
                                        <!-- harga awal -->
                                        <span class="pull-right">
                                            <?= currency_format($purchasing_item->price) ?>
                                        </span>
                                    </td>
                                    <td class="col-sm-1 col-lg-1">
                                        <!-- discount -->
                                        <span class="pull-right">
                                            <?= currency_format($purchasing_item->discount) ?>
                                        </span>
                                    </td>
                                    <td class="col-sm-1 col-lg-1">
                                        <!-- harga akhir -->
                                        <span class="pull-right">
                                            <?= currency_format($purchasing_item->finalprice) ?>
                                        </span>
                                    </td>
                                    <td class="col-sm-1 col-lg-1">
                                        <!-- harga total -->
                                        <span class="pull-right">
                                            <?= currency_format($purchasing_item->subtotal) ?>
                                        </span>
                                    </td>
                                    <!-- offline -->
                                    <td class="col-sm-1 col-lg-1">
                                        <!-- harga margin -->
                                        <span class="pull-right">
                                            <?= $purchasing_item->offline_margin ?>
                                        </span>
                                    </td>
                                    <td class="col-sm-1 col-lg-1">
                                        <!-- harga satuan -->
                                        <span class="pull-right">
                                            <?= currency_format($purchasing_item->offline_price_pcs) ?>
                                        </span>
                                    </td>
                                    <td class="col-sm-1 col-lg-1">
                                        <!-- harga rasio -->
                                        <span class="pull-right">
                                            <?= currency_format($purchasing_item->offline_price_rasio) ?>
                                        </span>
                                    </td>
                                    <!-- online -->
                                    <td class="col-sm-1 col-lg-1">
                                        <!-- margin online -->
                                        <span class="pull-right">
                                            <?= $purchasing_item->online_margin ?>
                                        </span>
                                    </td>
                                    <td class="col-sm-1 col-lg-1">
                                        <!-- harga satuan online -->
                                        <span class="pull-right">
                                            <?= currency_format($purchasing_item->online_price_pcs) ?>
                                        </span>
                                    </td>
                                    <td class="col-sm-1 col-lg-1">
                                        <!-- harga rasio online -->
                                        <span class="pull-right">
                                            <?= currency_format($purchasing_item->online_price_rasio) ?>
                                        </span>
                                    </td>                
                                </tr>      
                                <?php endforeach; ?>
                                <!-- end items -->                          
                                <tr>
                                    <td colspan="7" class="text-right">
                                        <b>Subtotal</b>
                                    </td>
                                    <td class="text-right">
                                        <span><?= currency_format($purchasing->subtotal) ?></span>
                                    </td>
                                    <td class="text-right" colspan="6">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="7" class="text-right">
                                        <b>Pajak</b>
                                    </td>
                                    <td class="text-right">
                                        <span><?= currency_format($purchasing->tax) ?></span>
                                    </td>
                                    <td class="text-right" colspan="6">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="7" class="text-right">
                                        <b>Diskon</b>
                                    </td>
                                    <td class="text-right">
                                        <span><?= currency_format($purchasing->discount) ?></span>
                                    </td>
                                    <td class="text-right" colspan="6">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="7" class="text-right">
                                        <b>Total</b>
                                    </td>
                                    <td class="text-right">
                                        <span><?= currency_format($purchasing->total) ?></span>
                                    </td>
                                    <td class="text-right" colspan="6">
                                    </td>
                                </tr>
                            </table>                            
                        </div>                   
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>