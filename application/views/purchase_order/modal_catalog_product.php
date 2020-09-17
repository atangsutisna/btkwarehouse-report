<div class="modal fade" id="modal-catalog-product">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Lookup Product</h4>
            </div>
            <div class="modal-body">
                <div class="row" style="margin-bottom: 10px;">
                    <div class="col-lg-9 col-9 col-sm-9">
                        <div class="form-inline">
                            <div class="form-group">
                                <label></label>
                                <input type="text" name="term" class="form-control" placeholder="Cari produk">
                            </div>
                            <div class="checkbox" style="margin-right: 10px;">
                                <label>
                                    <input type="checkbox" name="quantity" value="0" id="out_of_stock"/> Stok Kosong
                                </label>
                            </div>         
                            <div class="form-group">
                                <button class="btn btn-primary" id="btn-filter-product">
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
                    <table class="table table-bordered" id="table-catalog-product" width="100%">
                        <thead>
                          <tr>
                            <th rowspan="2">Gambar</th>
                            <th rowspan="2">Tgl. Perubahan</th>
                            <th rowspan="2">Nama / Barcode</th>
                            <th rowspan="2">Satuan</th>
                            <th rowspan="2">Stok</th>
                            <th rowspan="2">Min Stok</th>
                            <th rowspan="2">Max Stok</th>
                            <th rowspan="2">Type Produk</th>
                            <th colspan="4" class="text-center">Penjualan (Qty)</th>
                            <th rowspan="2">#</th>
                          </tr>
                          <tr>
                            <th>1 bln</th>
                            <th>2 bln</td>
                            <th>3 bln</th>
                            <th>6 bln</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>gambar val</td>
                            <td>barcode val</td>
                            <td>nama produk val</td>
                            <td>satuan val</td>
                            <td>stok val</td>
                            <td>min stok val</td>
                            <td>max stok val</td>
                            <td>type produk val</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>checkbox</td>
                          </tr>
                        </tbody>
                    </table>                    
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary btn-block" id="get-data">Ok</button>
            </div>
        </div>
    </div>
</div>