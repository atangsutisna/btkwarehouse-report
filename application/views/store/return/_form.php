<?php 
$identity = get_identity();
?>
<section class="content-header">
    <h1>
        Pengembalian Stok Etalase
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('home') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?= site_url('store/return_stock') ?>">Retur Stok Etalase</a></li>
        <li class="active">Form Pengembalian Barang</li>
    </ol>
</section>
<!--/breadcrumb -->
<div>
<div class="content">
    <div class="row">
        <div class="col-lg-12 col-sm-12 col-12">
            <div class="box box-danger">
                <form class="form-horizontal" 
                    id="form-return-stock"
                    v-on:submit.prevent>
                    <div class="box-body">
                        <alert-component 
                            v-bind:alert="form_status.alert" 
                            v-bind:has_errors="form_status.has_errors"
                            v-bind:success="form_status.success"
                            v-bind:message="form_status.message">
                        </alert-component>
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-share-alt"></i> Form Pengembalian Barang</h3>
                        </div>
                        <div class="row" style="margin-top: 10px;">
                            <div class="col-lg-8 col-8 col-sm-8">
                                <div class="form-group">
                                    <label class="col-lg-3 col-sm-2 control-label">Tanggal Terima</label>
                                    <div class="col-lg-4 col-sm-4">
                                        <input type="date" name="created_at" v-model="created_at" class="form-control"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 col-sm-2 control-label">Penerima</label>
                                    <div class="col-lg-6 col-sm-6">
                                        <input type="text" name="receiver_name" class="form-control" v-model="receiver_name"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-4 col-sm-4">
                                <b>No. Dokumen</b>: <br/>
                                <h4>{{return_from_storefront_no}}</h4>                                                                
                                <b>OPERATOR</b>: <br/>
                                <h4><?= $identity->first_name ?> <?= $identity->last_name ?></h4>                                                                
                                <b>TOTAL</b>: <br/>
                                <h4>{{moneyFormat(total_amount)}}</h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-8 col-8 col-sm-8"></div>
                            <div class="col-lg-4 col-4 col-sm-4">
                                <div>
                                    <a class="btn btn-info" title="Tambah Item" v-on:click="openCatalog()" >
                                        <i class="fa fa-plus-circle" aria-hidden="true"></i>
                                    </a>
                                    <button class="btn btn-primary" title="Simpan" v-on:click="save"
                                        v-bind:disabled="return_from_storefront_items.length == 0 || return_from_storefront_id !== null" 
                                        id="btn-save">
                                        <i class="fa fa-floppy-o" aria-hidden="true"></i>
                                    </button>
                                    
                                </div>
                            </div>
                        </div><hr/>
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>No. </th>
                                        <th>Produk</th>
                                        <th>Jml</th>
                                        <th>Satuan</th>
                                        <th>Harga</th>
                                        <th>Total</th>
                                        <th>#</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr is="return-from-storefront-item" v-for="(return_from_storefront_item, idx) in return_from_storefront_items" 
                                    :idx="idx"
                                    :product_name="return_from_storefront_item.product_name"
                                    :product_sku="return_from_storefront_item.product_sku"
                                    :options="return_from_storefront_item.options"
                                    :default_qty="return_from_storefront_item.qty"
                                    :qty_unit="return_from_storefront_item.qty_unit"
                                    :price="return_from_storefront_item.price"
                                    :subtotal="return_from_storefront_item.subtotal"
                                    :status="true">
                                    </tr>
                                </tbody>
                            </table>
                        </div>                        
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('store/return/modal_catalog_product') ?>
</div>