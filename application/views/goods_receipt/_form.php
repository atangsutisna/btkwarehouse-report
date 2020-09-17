<section class="content-header">
    <h1>
        Penerimaan Barang
    </h1>
    <ol class="breadcrumb">
        <li><a href="<?= site_url('dashboard') ?>"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="<?= site_url('goods_receipt') ?>"><i class="fa fa-dashboard"></i> Penerimaan Barang</a></li>
        <li class="active">Form Penerimaan Barang</li>
    </ol>
</section>
<!--/breadcrumb -->
<div>
<div class="content">
    <div class="row">
        <div class="col-lg-12 col-sm-12 col-12">
            <div class="box box-danger">
                <form class="form-horizontal" id="form-goods-receipt" v-on:submit.prevent>
                    <div class="box-body">
                        <alert-component 
                            v-bind:alert="form_status.alert" 
                            v-bind:has_errors="form_status.has_errors"
                            v-bind:success="form_status.success"
                            v-bind:message="form_status.message">
                        </alert-component>
                        <div class="box-header with-border">
                            <h3 class="box-title">Form Penerimaan Barang</h3>
                        </div>
                        <div class="row" style="margin-top: 10px;">
                            <div class="col-lg-8 col-8 col-sm-8">
                                <div class="form-group">
                                    <label class="col-lg-3 col-sm-2 control-label">No. PO</label>
                                    <div class="col-lg-8 col-sm-8">
                                        <div class="row">
                                            <div class="col-lg-6 col-sm-6 col-6">
                                                <input type="text" name="purchase_order_no" v-model="purchase_order_no" class="form-control" readonly="true"/>
                                            </div>
                                            <div class="col-lg-6 col-sm-6 col-6" style="padding-left: 0;">
                                                <a class="btn btn-info" title="Buka Purchase Order" v-on:click="openPo()">
                                                    <i class="fa fa-folder-open-o" aria-hidden="true"></i>
                                                </a>
                                                <button class="btn btn-primary" 
                                                    title="Simpan" 
                                                    v-on:click="save" 
                                                    id="btn-save">
                                                    <i class="fa fa-floppy-o" aria-hidden="true"></i>
                                                </button>    
                                            </div>                                            
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 col-sm-2 control-label">Tgl. Terima</label>
                                    <div class="col-lg-4 col-sm-4">
                                        <input type="date" name="received_date" v-model="received_date" class="form-control"/>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-lg-3 col-sm-2 control-label">Nama Penerima</label>
                                    <div class="col-lg-6 col-sm-6">
                                        <input type="text" 
                                            name="receiver_name" 
                                            class="form-control" 
                                            value="<?= $identity->first_name.' '.$identity->last_name ?>"
                                            disabled="disabled"/>
                                    </div>
                                </div>                                
                                <div class="form-group">
                                    <label class="col-lg-3 col-sm-2 control-label">Nama Pengirim</label>
                                    <div class="col-lg-6 col-sm-6">
                                        <input type="text" name="receiver" v-model="sales_person_name" class="form-control"/>
                                    </div>
                                </div>                                

                            </div>
                            <div class="col-lg-4 col-4 col-sm-4">
                                <b>TANGGAL PO</b>: <br/>
                                <h4>{{purchase_order_date}}</h4>
                                <b>SUPPLIER</b>: <br/>
                                <h4>{{supplier_name}}</h4>                                                                
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-8 col-8 col-sm-8"></div>
                            <div class="col-lg-4 col-4 col-sm-4">
                                <div>
                                    <!--
                                    <a class="btn btn-info" title="Tambah Item" id="btnPo" >
                                        <i class="fa fa-plus-circle" aria-hidden="true"></i>
                                    </a>
                                -->
                                </div>
                            </div>
                        </div><hr/>
                    	<div class="table-responsive">
                            <table class="table table-bordered" id="table_new_receive_order" cellspacing="0" width="100%">
                                <tr>
                                    <th rowspan="2">No</th>
                                    <th rowspan="2">
                                        Produk<br/>
                                        Kode Produk / Barcode<br/>
                                    </th>
                                    <!--<th rowspan="2" class="text-center">Harga</th>-->
                                    <th colspan="2" class="text-center">Dipesan</th>
                                    <th colspan="2" class="text-center">Sudah diterima</th>
                                    <th colspan="2" class="text-center">Dikirim</th>
                                    <th rowspan="2">Catatan</th>
                                    <th rowspan="2">Tgl Kadaluarsa</th>
                                    <!--<th rowspan="2" class="text-center">Subtotal</th>-->
                                </tr>                                    
                                <tr>
                                    <td><b>Jml</b></td>
                                    <td><b>Unit</b></td>
                                    <td><b>Jml</b></td>
                                    <td><b>Unit</b></td>
                                    <td><b>Jml</b></td>
                                    <td><b>Unit</b></td>
                                </tr>                                    
                            	<tr is="order-detail" v-for="(order_detail, idx) in order_details"
                                    :idx="idx"
                                    :order_detail="order_detail">
                            	</tr>
                            </table>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-po-catalog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Purchase Order</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-lg-9 col-9 col-sm-9">
                        <div class="form-inline">
                            <div class="form-group">
                                <label></label>
                                <input type="text" class="form-control" placeholder="Masukan Nomor" 
                                    name="purchase_order_no" id="purchase_order_no">
                            </div>
                            <div class="form-group">
                                <button class="btn btn-primary" id="btn-filter">
                                    <i class="fa fa-search" aria-hidden="true"></i>
                                    Cari
                                </button>
                            </div>                            
                        </div>
                    </div>
                    <div class="col-lg-3 col-3 col-sm-3">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered" id="table-catalog-po" cellspacing="0" width="100%">
                        <thead>
                            <tr>
                                <th>No. PO</th>
                                <th>Suplier</th>
                                <th>Tgl. Pembuatan</th>
                                <th>Status</th>
                                <th class="text-center">#</th>
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