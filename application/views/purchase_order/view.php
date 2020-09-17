<section class="content-header">
    <h1>
        Purchase Order
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?= site_url('purchase_order') ?>">Purchase Order</a></li>
        <li class="active"><?php echo $purchase_order_id ?></li>
    </ol>
</section>
<div class="content" id="form-purchase-order">
    <div class="row">
        <div class="col-lg-12 col-sm-12 col-12">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">P.O. {{purchase_order_no}}</h3>
                </div>
                <form role="form" method="post" class="form-horizontal">  
                    <div class="box-body">
                        <div class="row">
                            <div class="col-lg-12 col-12 col-sm-12">
                                <?php if (isset($purchase_order_id)) : ?>
                                <input type="hidden" name="purchase_order_id" value="<?= $purchase_order_id ?>"/>
                                <?php endif; ?>
                                <div class="form-group">
                                    <label class="col-lg-3 col-sm-3 control-label">Tgl. Pembuatan</label>
                                    <div class="col-lg-5 col-sm-5">
                                        <p class="form-control-static">{{created_at}}</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 col-sm-3 control-label">Supplier</label>
                                    <div class="col-lg-7 col-sm-7">
                                        <p class="form-control-static">{{supplier_name}}</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 col-sm-3 control-label">Status</label>
                                    <div class="col-lg-7 col-sm-7">
                                        <p class="form-control-static">{{status.toUpperCase()}}</p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 col-sm-3 control-label">Tgl. Perubahan</label>
                                    <div class="col-lg-5 col-sm-5">
                                        <p class="form-control-static">{{updated_at}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-bottom: 20px; margin-top: 20px;">
                            <div class="col-lg-8 col-8 col-sm-8"></div>
                            <div class="col-lg-4 col-4 col-sm-4">
                                <div>
                                    <a class="btn btn-primary" href="<?= site_url('purchase_order/edit_form/'. $purchase_order_id) ?>"
                                        id="btn-save" title="Edit" v-if="status == 'draft'">
                                        <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
                                    </a>
                                    <a class="btn btn-danger" title="Batal" v-on:click="cancel" 
                                        v-bind:class="{'disabled': purchase_order_id == null}" v-if="status == 'draft'">
                                        <i class="fa fa-remove" aria-hidden="true"></i>
                                    </a>
                                    <a class="btn btn-info" title="Cetak" target="_blank"
                                        href="<?= site_url('purchase_order/print/'. $purchase_order_id) ?>">
                                        <i class="fa fa-print" aria-hidden="true"></i> Cetak
                                    </a>
                                </div>
                            </div>
                        </div>
                        <!-- end header of purchase order -->
                        <div class="table-responsive">
                            <table class="table table-condensed">
                                <thead>
                                    <tr>
                                        <th>Gambar</th>
                                        <th>Nama/ Barcode</th>
                                        <th>Qty</th>
                                        <th>Unit</th>
                                        <th>Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(order_detail, idx) in order_details">
                                        <td><img v-bind:src="order_detail.product_image" class="img-thumbnail"/></td>
                                        <td>
                                            {{order_detail.product_name}}<br/>
                                            <small>{{order_detail.product_model}}</small>
                                        </td>
                                        <td>{{order_detail.qty}}</td>
                                        <td>{{order_detail.qty_unit}}</td>
                                        <td>{{order_detail.note}}</td>
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