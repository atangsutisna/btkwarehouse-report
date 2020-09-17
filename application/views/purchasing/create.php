<?php
$indentity = get_identity();
?>
<section class="content-header">
    <h1>
        Transaksi Pembelian
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?= site_url('purchasing') ?>">Transaksi Pembelian</a></li>
        <li class="active">Buat Baru</li>
    </ol>
</section>
<div class="content">
    <div class="row">
        <div class="col-lg-12 col-sm-12 col-12">
            <div class="box box-danger">
                <form class="form-horizontal" id="form-purchasing" v-on:submit.prevent>
                    <div class="box-body">
                        <alert-component 
                            v-bind:alert="form_status.alert" 
                            v-bind:has_errors="form_status.has_errors"
                            v-bind:success="form_status.success"
                            v-bind:message="form_status.message">
                        </alert-component>
                        <div class="box-header with-border">
                            <h3 class="box-title">Form Transaksi Pembelian</h3>
                        </div>
                        <div class="row" style="margin-top: 10px;">
                            <div class="col-lg-6 col-6 col-sm-6">
                                <div class="form-group">
                                    <label class="col-lg-3 col-sm-2 control-label">No. Penerimaan Barang</label>
                                    <div class="col-lg-8 col-sm-8">
                                        <div class="row">
                                            <div class="col-lg-6 col-sm-6 col-6">
                                                <input type="text" 
                                                    name="purchase_order_no" 
                                                    class="form-control" 
                                                    v-model="goods_receipt_no"
                                                    readonly="true"/>
                                            </div>
                                            <div class="col-lg-6 col-sm-6 col-6" style="padding-left: 0;">
                                                <a class="btn btn-info" 
                                                    title="Buka Purchase Order"
                                                    v-on:click="openGoodsReceipt()">
                                                    <i class="fa fa-folder-open-o" aria-hidden="true"></i>
                                                </a>
                                                <button class="btn btn-primary" 
                                                    title="Simpan" 
                                                    id="btn-save"
                                                    v-on:click="save">
                                                    <i class="fa fa-floppy-o" aria-hidden="true"></i>
                                                </button>    
                                            </div>                                            
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 col-sm-2 control-label">Nama Supplier</label>
                                    <div class="col-lg-6 col-sm-6">
                                        <input type="hidden" name="supplier_id"/>
                                        <input type="text" 
                                            name="supplier_name" 
                                            v-model="supplier_name"
                                            class="form-control" 
                                            readonly="true"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 col-sm-2 control-label">Tgl. Invoice</label>
                                    <div class="col-lg-4 col-sm-4">
                                        <input type="date" name="received_date" class="form-control" v-model="invoice_date"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 col-sm-2 control-label">Tgl. Terima</label>
                                    <div class="col-lg-4 col-sm-4">
                                        <input type="date" name="received_date" class="form-control" v-model="receive_date"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-6 col-sm-6">
                                <div class="form-group">
                                    <label class="col-lg-3 col-sm-2 control-label">Pembayaran</label>
                                    <div class="col-lg-4 col-sm-4">
                                        <select class="form-control" name="payment_method" id="payment-method-choices" v-model="payment_method">
                                            <option></option>
                                            <?php foreach ($payment_methods as $payment_method) : ?>
                                            <option value="<?= $payment_method->payment_method_id ?>"><?= $payment_method->payment_method_name ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 col-sm-2 control-label">Jatuh Tempo</label>
                                    <div class="col-lg-4 col-sm-4">
                                        <input type="date" name="due_date" class="form-control" v-model="due_date"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 col-sm-2 control-label">Catatan</label>
                                    <div class="col-lg-4 col-sm-4">
                                        <textarea name="note" class="form-control" v-model="note"></textarea>
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
                            	<tr is="purchasing-items" v-for="(purchasing_item, idx) in purchasing_items"
                                    :idx="idx"
                                    :purchasing_item="purchasing_item">
                            	</tr>
                                <tr>
                                    <td colspan="7" class="text-right">
                                        <div class="form-inline">
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="discount_type" value="discount_percentage" disabled="disabled"> Diskon %
                                                </label>
                                            </div>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="discount_type" value="discount_amount" v-model="discount_type"> Diskon Rp
                                                </label>
                                            </div>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" v-model="taxable"> taxable 10%
                                                </label>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-right">
                                    </td>
                                    <td class="text-right" colspan="6">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="7" class="text-right">
                                        <b>Subtotal</b>
                                    </td>
                                    <td class="text-right">
                                        <span>{{ moneyFormat(subtotal) }}</span>
                                    </td>
                                    <td class="text-right" colspan="6">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="7" class="text-right">
                                        <b>Pajak</b>
                                    </td>
                                    <td class="text-right">
                                        <span>{{ moneyFormat(tax) }}</span>
                                    </td>
                                    <td class="text-right" colspan="6">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="7" class="text-right">
                                        <b>Diskon</b>
                                    </td>
                                    <td class="text-right">
                                        <span>{{ moneyFormat(discount) }}</span>
                                    </td>
                                    <td class="text-right" colspan="6">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="7" class="text-right">
                                        <b>Total</b>
                                    </td>
                                    <td class="text-right">
                                        <span>{{ moneyFormat(total) }}</span>
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
<?php $this->load->view('purchasing/modal-goods-receipt') ?>