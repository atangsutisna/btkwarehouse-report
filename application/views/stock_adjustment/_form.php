<section class="content-header">
    <h1>
        Penyesuaian Stok
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?= site_url('stock_adjustment') ?>"> Penyesuaian Stok</a></li>
        <li class="active">Penyesuaian Stok Baru</li>
    </ol>
</section>
<!--/breadcrumb -->
<div>
<div class="content">
    <div class="row">
        <div class="col-lg-12 col-sm-12 col-12">
            <div class="box box-danger">
                <form class="form-horizontal" 
                    id="form-stock-adjustment"
                    v-on:submit.prevent>
                    <div class="box-body">
                        <alert-component 
                            v-bind:alert="form_status.alert" 
                            v-bind:has_errors="form_status.has_errors"
                            v-bind:success="form_status.success"
                            v-bind:message="form_status.message">
                        </alert-component>
                        <div class="box-header with-border">
                            <h3 class="box-title">Data Penyesuaian Stok</h3>
                        </div>
                        <div class="row" style="margin-top: 10px;">
                            <div class="col-lg-6 col-6 col-sm-6">
                                
                                <div class="form-group">
                                    <label class="col-lg-4 col-sm-4 control-label">Kode / Barcode</label>
                                    <div class="col-lg-8 col-sm-8">
                                        <div class="row">
                                            <div class="col-lg-6 col-sm-6 col-6">
                                                <input type="text" name="sku" v-model="product_model" class="form-control" disabled="true" />        
                                            </div>
                                            <div class="col-lg-6 col-sm-6 col-6" style="padding-left: 0;">
                                                <a class="btn btn-primary" title="Tambah Item" id="btnStock" v-on:click="openStock()" title="Pilih Produk">
                                                    <i class="fa fa-folder-open-o" aria-hidden="true"></i>
                                                </a>                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-4 col-sm-4 control-label">Nama Produk</label>
                                    <div class="col-lg-6 col-sm-6">
                                        <input type="text" name="description" v-model="product_name" class="form-control" disabled="true"/>
                                    </div>
                                    
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-4 col-sm-4 control-label">Stock Sistem</label>
                                    <div class="col-lg-3 col-sm-3">
                                        <div class="input-group">
                                            <input type="text" name="qty" v-model="qty" class="form-control text-right" disabled="true"/>
                                            <div class="input-group-addon" v-if="qty_unit === null">?</div>
                                            <div class="input-group-addon" v-if="qty_unit !== null">{{ qty_unit }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-4 col-sm-4 control-label">Hitung Fisik</label>
                                    <div class="col-lg-3 col-sm-3">
                                        <div class="input-group">
                                            <input type="text" name="qty" v-model="last_stock" class="form-control text-right"/>
                                            <div class="input-group-addon" v-if="qty_unit === null">?</div>
                                            <div class="input-group-addon" v-if="qty_unit !== null">{{ qty_unit }}</div>
                                        </div>
                                    </div>
                                </div>                                
                                <div class="form-group">
                                    <label class="col-lg-4 col-sm-4 control-label">Selisih</label>
                                    <div class="col-lg-3 col-sm-3">
                                        <div class="input-group">
                                            <input type="number" name="stock_adjust" v-model="stock_adjust" 
                                                class="form-control text-right"
                                                min="0"
                                                disabled="disabled"/>
                                            <div class="input-group-addon" v-if="qty_unit === null">?</div>
                                            <div class="input-group-addon" v-if="qty_unit !== null">{{ qty_unit }}</div>                                            
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-lg-offset-4 col-sm-offset-4" style="margin-left: 35%;">
                                        <button type="submit" class="btn btn-primary" v-on:click="save">
                                            <i class="fa fa-floppy-o" aria-hidden="true"></i> Simpan
                                        </button>
                                    </div>
                                </div>                                
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-stock-catalog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Lookup Stock</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-9 col-9 col-sm-9">
                        <div class="form-inline">
                            <div class="form-group">
                                <label></label>
                                <input type="text" name="term" class="form-control" placeholder="Cari produk">
                            </div>
                            <div class="form-group">
                                <button class="btn btn-primary" id="btn-filter">
                                    <i class="fa fa-search" aria-hidden="true"></i>
                                    Filter
                                </button>
                                <button class="btn btn-default" id="btn-reset-filter-product">
                                    Reset Filter
                                </button>                                                
                            </div>                                                                                        
                        </div>
                    </div>
                    <div class="col-lg-3 col-3 col-sm-3">
                    </div>
                </div>                
                <div class="table-responsive">
                    <table class="table table-bordered" id="table-catalog-stock" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>Tgl. Perubahan</th>
                                <th>Nama</th>
                                <th>Qty/Unit</th>
                                <th>#</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>