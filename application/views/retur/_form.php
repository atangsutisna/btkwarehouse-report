<section class="content-header">
    <h1>
        Retur Pembelian
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('home') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?= site_url('retur') ?>">Retur Pembelian</a></li>
        <li class="active">Buat Baru</li>
    </ol>
</section>
<div class="content">
    <div class="row">
        <div class="col-lg-12 col-sm-12 col-12">
            <div class="box box-danger">
                <div class="box-header with-border">
                    <h3 class="box-title">Form Retur Pembelian</h3>
                </div>
                <form role="form" method="post" class="form-horizontal" id="form-retur" v-on:submit.prevent>  
                    <div class="box-body">
                        <alert-component 
                            v-bind:alert="form_status.alert" 
                            v-bind:has_errors="form_status.has_errors"
                            v-bind:success="form_status.success"
                            v-bind:message="form_status.message">
                        </alert-component>
                        <div class="row">
                            <div class="col-lg-8 col-8 col-sm-8">
                                <?php if (isset($retur_id)) : ?>
                                <input type="hidden" name="retur_id" value="<?= $retur_id ?>"/>
                                <?php endif; ?>
                                <div class="form-group">
                                    <label class="col-lg-3 col-sm-3 control-label">Tgl</label>
                                    <div class="col-lg-5 col-sm-5">
                                        <input type="date" name="retur_date" class="form-control" v-model="retur_date"/>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-lg-3 col-sm-3 control-label">Supplier *</label>
                                    <div class="col-lg-6 col-sm-6">
                                        <select class="form-control select2" style="width: 100%;" 
                                            id="supplier-choices">
                                            <?php if (isset($supplier)) : ?>
                                            <option value="<?= $supplier->supplier_id ?>" selected><?= $supplier->supplier_name ?></option>
                                            <?php endif; ?>
                                        </select>
                                        <input type="hidden" name="supplier_id" v-model="supplier_id"/>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4 col-4 col-sm-4">
                                <b>RETUR NO</b>: <br/>
                                <h4>{{retur_no}}</h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-8 col-8 col-sm-8"></div>
                            <div class="col-lg-4 col-4 col-sm-4">
                                <div>
                                    <a class="btn btn-info" title="Tambah Item" id="btn-product-inventory"
                                        v-on:click="openCatalogInventory">
                                        <i class="fa fa-plus-circle" aria-hidden="true"></i>
                                    </a>
                                    <button class="btn btn-primary" title="Simpan" v-on:click="save" id="btn-save">
                                        <i class="fa fa-floppy-o" aria-hidden="true"></i>
                                    </button>
                                    <button class="btn btn-danger" title="Batal" v-on:click="cancel" id="btn-delete">
                                        <i class="fa fa-remove" aria-hidden="true"></i>
                                    </button>
                                </div>
                            </div>
                        </div><hr/>
                        <!-- end header of purchase order -->
                        
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>No. </th>
                                        <th>Nama</th>
                                        <th>Qty</th>
                                        <th>Unit</th>
                                        <th>Keterangan</th>
                                        <th>#</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr is="return-item" v-for="(return_item, idx) in return_items" 
                                    :idx="idx"
                                    :return_item="return_item">
                                </tbody>
                            </table>
                        </div>                        
                    </div>                    
                </form>
                <div class="modal fade" id="modal-product-catalog-retur">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span></button>
                                <h4 class="modal-title">Product</h4>
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
                                                <button class="btn btn-primary" id="btn-filter-product">
                                                    <i class="fa fa-search" aria-hidden="true"></i>Filter
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
                                    <table class="table table-bordered" id="table-catalog-retur" cellspacing="0" width="100%">
                                        <thead>
                                            <tr>
                                                <th>Gambar</th>
                                                <th>Nama</th>
                                                <th class="text-right">Stok Sistem</th>
                                                <th>Tgl. Perubahan</th>
                                                <th>#</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary btn-block" id="get-data-retur">Ok</button>
                            </div>
                        </div>
                    </div>
                </div>                 
            </div> <!-- end box header -->
        </div>
    </div>
</div>