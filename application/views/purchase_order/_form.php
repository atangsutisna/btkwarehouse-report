<section class="content-header">
    <h1>
        Purchase Order
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?= site_url('purchase_order') ?>">Purchase Order</a></li>
        <li class="active">Form Purchase Order</li>
    </ol>
</section>
<div class="content">
    <div class="row">
        <div class="col-lg-12 col-sm-12 col-12">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Purchase Order</h3>
                </div>
                <form role="form" method="post" class="form-horizontal" id="form-purchase-order" v-on:submit.prevent>  
                    <div class="box-body">
                        <alert-component 
                            v-bind:alert="form_status.alert" 
                            v-bind:has_errors="form_status.has_errors"
                            v-bind:success="form_status.success"
                            v-bind:message="form_status.message">
                        </alert-component>
                        <div class="row">
                            <div class="col-lg-8 col-8 col-sm-8">
                                <?php if (isset($purchase_order_id)) : ?>
                                <input type="hidden" name="purchase_order_id" value="<?= $purchase_order_id ?>"/>
                                <?php endif; ?>
                                <div class="form-group">
                                    <label class="col-lg-3 col-sm-3 control-label">No. </label>
                                    <div class="col-lg-5 col-sm-5">
                                        <p class="form-control-static">{{purchase_order_no}}</p>
                                    </div>
                                </div>
                                <div class="form-group" v-bind:class="{'has-error': hasErrors('created_at')}">
                                    <label class="col-lg-3 col-sm-3 control-label">P.O. Date</label>
                                    <div class="col-lg-5 col-sm-5">
                                        <input type="date" name="purchase_order_date" class="form-control" v-model="created_at"/>
                                        <span class="help-block" v-if="hasErrors('created_at')">Tanggal tidak boleh kosong</span>
                                    </div>
                                </div>
                                <div class="form-group" v-bind:class="{'has-error': hasErrors('supplier_id')}">
                                    <label class="col-lg-3 col-sm-3 control-label">Supplier *</label>
                                    <div class="col-lg-5 col-sm-5">
                                        <select class="form-control select2" style="width: 100%;" id="supplier-choices">
                                            <?php if (isset($supplier)) : ?>
                                            <option value="<?= $supplier->supplier_id ?>" selected><?= $supplier->supplier_name ?></option>
                                            <?php endif; ?>
                                        </select>
                                        <input type="hidden" name="supplier_id" v-model="supplier_id"/>
                                        <span class="help-block" v-if="hasErrors('supplier_id')">
                                            Supplier tidak boleh kosong
                                        </span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 col-sm-3 control-label">Status</label>
                                    <div class="col-lg-3 col-sm-3">
                                        <select name="status" class="form-control" v-model="status">
                                            <option value="draft">Draft</option>
                                            <option value="ordered">Ordered</option>
                                            <option value="void">Batal</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-4 col-sm-4">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-8 col-8 col-sm-8"></div>
                            <div class="col-lg-4 col-4 col-sm-4">
                                <div>
                                    <a href="<?= site_url('purchase_order/create?supplier_id='. $supplier->supplier_id) ?>" class="btn btn-info" title="Tambah Item" id="btn-product-catalog">
                                        <i class="fa fa-plus-circle" aria-hidden="true"></i>
                                    </a>
                                    <button class="btn btn-primary" title="Simpan" v-on:click="save"
                                        v-bind:class="{'disabled': order_details.length == 0 && status == 'draft' || status == 'void'}" id="btn-save">
                                        <i class="fa fa-floppy-o" aria-hidden="true"></i>
                                    </button>
                                    <button class="btn btn-danger" title="Batal" v-on:click="cancel" id="btn-delete"
                                        v-bind:class="{'disabled': purchase_order_id == null || (status == 'void' || status == 'complete')}">
                                        <i class="fa fa-remove" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                        </div><hr/>
                        <!-- end header of purchase order -->
                        <!--
                        <div class="row">
                            <div class="col-lg-8 col-8 col-sm-8">
                                <div class="form-group">
                                    <label class="col-lg-3 col-sm-3 control-label">Produk</label>
                                    <div class="col-lg-7 col-sm-7">
                                        <select class="form-control select2" style="width: 100%;" 
                                            id="product-choices">
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group" v-if="form_product.options.length > 0">
                                    <label class="col-lg-3 col-sm-3 control-label">Option</label>
                                    <div class="col-lg-8 col-8 col-sm-8"> 
                                        <option-choices 
                                            v-for="option in form_product.options" 
                                            v-bind:option="option">
                                        </option-choices>                                        
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 col-sm-3 control-label">Qty/Unit</label>
                                    <div class="col-lg-7 col-sm-7">
                                        <div class="row">
                                            <div class="col-5 col-lg-5 col-sm-5">
                                                <input type="number" name="qty" class="form-control" 
                                                v-model="form_product.qty" style="text-align: right;"/>
                                            </div>
                                            <div class="col-5 col-lg-5 col-sm-5" style="padding-left: 0px;">
                                                <input type="text" name="qty_unit" class="form-control" 
                                            v-model="form_product.qty_unit" readonly/>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 col-sm-3 control-label">Harga</label>
                                    <div class="col-lg-7 col-sm-7">
                                        <div class="row">
                                            <div class="col-10 col-lg-10 col-sm-10">
                                                <div class="input-group">
                                                    <span class="input-group-addon">Rp.</span>
                                                    <input type="number" name="price" class="form-control" 
                                                        v-model="form_product.price" style="text-align: right;"/>
                                                </div>
                                            </div>
                                            <div class="col-2 col-lg-2 col-sm-2" style="padding-left: 0; padding-right: 0;">
                                                <button class="btn btn-primary" title="Klik untuk menambah produk" 
                                        v-bind:class="{'disabled': form_product.product_id == null}" v-on:click="addOrderDetails()">+</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-4 col-sm-4">
                            </div>
                        </div> -->
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>No. </th>
                                        <th>Produk</th>
                                        <th>Jml</th>
                                        <th>Unit</th>
                                        <th>Keterangan</th>
                                        <th>#</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr is="order-detail" v-for="(order_detail, idx) in order_details" 
                                    :idx="idx"
                                    :product_name="order_detail.product_name"
                                    :product_model="order_detail.product_model"
                                    :product_image="order_detail.product_image"
                                    :options="order_detail.options"
                                    :default_qty="order_detail.qty"
                                    :qty_unit="order_detail.qty_unit"
                                    :default_note="order_detail.note"
                                    :price="order_detail.price"
                                    :subtotal="order_detail.subtotal"
                                    :status="true">
                                    </tr>
                                </tbody>
                            </table>
                        </div>                        
                    </div>                    
                </form>
                <?php $this->load->view('purchase_order/modal_catalog_product') ?>
            </div> <!-- end box header -->
        </div>
    </div>
</div>