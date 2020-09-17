<?php
$indentity = get_identity();
?>
<section class="content-header">
    <h1>
        Stok Etalase
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?= site_url('move_storefront') ?>">Stok Etalase</a></li>
        <li class="active">Form Pengambilan Barang</li>
    </ol>
</section>
<!--/breadcrumb -->
<div>
<div class="content">
    <div class="row">
        <div class="col-lg-12 col-sm-12 col-12">
            <div class="box box-danger">
                <form id="form-move-storefront" v-on:submit.prevent>
                    <div class="box-body">
                        <alert-component 
                            v-bind:alert="form_status.alert" 
                            v-bind:has_errors="form_status.has_errors"
                            v-bind:success="form_status.success"
                            v-bind:message="form_status.message">
                        </alert-component>
                        <div class="box-header with-border">
                            <h3 class="box-title"><i class="fa fa-share-alt"></i> Form Pengambilan Barang</h3>
                        </div>
                        <div class="row" style="margin-top: 10px;">
                            <div class="col-lg-4 col-4 col-sm-4">
                                <div class="form-group">
                                    <label>Tanggal Terima</label>
                                    <input type="date" name="moved_date" v-model="moved_date" class="form-control"/>
                                </div>
                                <div class="form-group">
                                    <label>Penerima</label>
                                    <input type="text" name="receiver_name" class="form-control" v-model="receiver_name"/>
                                </div>
                            </div>
                            <div class="col-lg-4 col-4 col-sm-4"></div>
                            <div class="col-lg-4 col-4 col-sm-4">
                                <b>No. Dokumen</b>: <br/>
                                <h4>{{move_to_storefront_no}}</h4>                                                                
                                <b>OPERATOR</b>: <br/>
                                <h4><?= $indentity->first_name. ' '.$indentity->last_name ?></h4>                                                                
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
                                        v-bind:class="{'disabled': move_to_storefront_items.length == 0 || move_to_storefront_id !== null}" id="btn-save">
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
                                    <tr is="move-to-storefront-item" v-for="(move_to_storefront_item, idx) in move_to_storefront_items" 
                                    :idx="idx"
                                    :product_name="move_to_storefront_item.product_name"
                                    :product_sku="move_to_storefront_item.product_sku"
                                    :options="move_to_storefront_item.options"
                                    :default_qty="move_to_storefront_item.qty"
                                    :qty_unit="move_to_storefront_item.qty_unit"
                                    :price="move_to_storefront_item.price"
                                    :subtotal="move_to_storefront_item.subtotal"
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
<?php $this->load->view('move-storefront/modal_catalog_product') ?>
</div>