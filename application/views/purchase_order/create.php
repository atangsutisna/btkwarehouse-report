<section class="content-header">
    <h1>
        Purchase Order
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?= site_url('purchase_order') ?>">Purchase Order</a></li>
        <li class="active">Buat Baru</li>
    </ol>
</section>
<div class="content">
    <div class="row">
        <div class="col-lg-12 col-sm-12 col-12">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Purchase Order</h3>
                </div>
                <div class="box-body">
                    <form class="form-horizontal" id="form-filter-catalog" v-on:submit.prevent>
                        <div class="form-group">
                            <label class="col-3 col-sm-3 col-lg-3 control-label">Supplier</label>
                            <div class="col-lg-3 col-sm-3">
                                <select class="form-control select2" style="width: 100%;" id="supplier-choices">
                                    <?php if (isset($supplier) && $supplier != NULL): ?>
                                    <option value="<?= $supplier->supplier_id ?>"><?= $supplier->name ?></option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div> 
                        <div class="form-group">
                            <label class="col-3 col-sm-3 col-lg-3 control-label">Jenis Produk</label>
                            <div class="col-lg-2 col-sm-2">
                                <select class="form-control select2" style="width: 100%;" id="moving-status-choices">
                                    <option></option>
                                    <option value="fast">Fast</option>
                                    <option value="slow">Slow</option>
                                    <option value="normal">Normal</option>
                                    <option value="bad">Dead stock</option>
                                </select>
                            </div>
                        </div>                        
                        <div class="form-group">
                            <label class="col-3 col-sm-3 col-lg-3 control-label"></label>
                            <div class="col-lg-3 col-sm-3">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="out_of_stock" v-model="out_of_stock"/> Stok Kosong (Tampilkan stok 0)
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="under_stock_minimum" v-model="under_stock_minimum"/> Tampilkan produk dibawah stok minimum
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="stock_minus" v-model="stock_minus"/> Tampilkan stok minus
                                    </label>
                                </div>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="available_stock" v-model="available_stock"/> Tidak menampilkan stok 0 
                                    </label>
                                </div>                                    
                            </div>
                        </div> 
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-4 col-sm-4 col-lg-4">
                                <button type="submit" class="btn btn-primary" id="btn-search" v-on:click="doFilter()">
                                    <i class="fa fa-filter" aria-hidden="true"></i> Tampilkan Katalog Produk
                                </button>
                            </div>
                        </div>
                    </form>
                    <form method="POST" action="<?= site_url('purchase_order/create') ?>">
                        <div id="form-supplier-product">
                            <?php $this->load->view('purchase_order/supplier_product', $supplier_products) ?>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
