<section class="content-header">
    <h1>
        Penerimaan Barang
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?= site_url('goods_receipt') ?>"> Penerimaan Barang</a></li>
        <li class="active"><?= $goods_receipt->goods_receipt_no ?></li>
    </ol>
</section>
<div class="content">
    <div class="row">
        <div class="col-lg-12 col-sm-12 col-12">
            <div class="box box-danger">
                <form class="form-horizontal" id="form-goods-receipt">
                    <div class="box-body">
                        <div class="box-header with-border">
                            <h3 class="box-title">No. <?= $goods_receipt->goods_receipt_no ?></h3>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 col-12 col-sm-12">
                            </div>
                        </div>                        
                        <div class="row" style="margin-top: 10px;">
                            <div class="col-lg-12 col-12 col-sm-12">
                                <div class="pull-right">
                                    <a class="btn btn-info" title="Cetak" v-on:click="print">
                                        <i class="fa fa-print" aria-hidden="true"></i> Cetak
                                    </a>                                    
                                </div>

                                <div class="form-group">
                                    <label class="col-lg-4 col-sm-2 control-label">Tgl. PO</label>
                                    <div class="col-lg-4 col-sm-4">
                                    	<p class="form-control-static"><?= format_date($goods_receipt->purchase_order_date) ?></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-4 col-sm-2 control-label">No. PO</label>
                                    <div class="col-lg-8 col-sm-8">
                                    	<p class="form-control-static"><?= $goods_receipt->purchase_order_no ?></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-4 col-sm-2 control-label">Nama Supplier</label>
                                    <div class="col-lg-8 col-sm-8">
                                    	<p class="form-control-static"><?= $goods_receipt->supplier_name ?></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-4 col-sm-2 control-label">Tgl. Terima</label>
                                    <div class="col-lg-4 col-sm-4">
                                        <p class="form-control-static"><?= format_date($goods_receipt->received_date) ?></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-4 col-sm-2 control-label">Nama Penerima</label>
                                    <div class="col-lg-6 col-sm-6">
                                    	<p class="form-control-static"><?= $goods_receipt->receiver_name ?></p>
                                    </div>
                                </div>                                
                                <div class="form-group">
                                    <label class="col-lg-4 col-sm-2 control-label">Nama Pengirim</label>
                                    <div class="col-lg-6 col-sm-6">
										<p class="form-control-static"><?= $goods_receipt->sales_person_name ?></p>
                                    </div>
                                </div>                                

                            </div>
                        </div>
                    	<div class="table-responsive">
                            <table class="table table-bordered" id="table_new_receive_order" cellspacing="0" width="100%">
                                <tr>
                                    <th rowspan="2">No</th>
                                    <th rowspan="2">
                                        Produk<br/>
                                        Kode / Barcode<br/>
                                    </th>
                                    <th colspan="2" class="text-center">Dipesan</th>
                                    <th colspan="2" class="text-center">Dikirim</th>
                                    <th rowspan="2">Catatan</th>
                                </tr>                                    
                                <tr>
                                    <td class="text-center"><b>Jml</b></td>
                                    <td class="text-center"><b>Unit</b></td>
                                    <td class="text-center"><b>Jml</b></td>
                                    <td class="text-center"><b>Unit</b></td>
                                </tr>
                                <?php foreach ($goods_receipt_items as $idx => $item) : ?>
                                <tr>
                                    <td><?= $idx + 1 ?></td>
                                    <td>
                                        <?= $item->product_name ?><br/>
                                        <small><?= $item->product_sku ?></small>
                                    </td>
                                    <td class="text-right"><?= $item->qty_order ?></td> 
                                    <td class="text-right"><?= $item->qty_unit ?></td>                                    
                                    <td class="text-right"><?= $item->qty ?></td> 
                                    <td class="text-right"><?= $item->qty_unit ?></td>
                                    <td><?= $item->note ?? 'Tidak ada' ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>