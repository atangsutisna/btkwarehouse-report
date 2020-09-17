<?php
use DusanKasan\Knapsack\Collection;
?>
<input type="hidden" name="supplier_id" value="<?= $supplier_id ?? 0 ?>">
<div class="row">
    <div class="col-lg-12">
        <div class="pull-right" style="margin-bottom: 20px;">
            <button class="btn btn-primary"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</button>
        </div>
    </div>
</div>
<div class="table-responsive">
    <table class="table table-bordered" id="table-supplier-product" width="100%">
        <thead>
            <tr>
                <th rowspan="2">Gambar</th>
                <th rowspan="2">Nama / Barcode</th>
                <th rowspan="2">Qty</th>
                <th rowspan="2">Satuan</th>
                <th rowspan="2">Stok Ready</th>
                <th rowspan="2">
                    Min Stok 
                    <a title="Klik disini untuk edit min stok" class="btn-edit-min-stock"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> </a>
                </th>
                <th rowspan="2">
                    Max Stok
                    <a title="Klik disini untuk edit max stok" class="btn-edit-max-stock"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> </a>
                </th>
                <th rowspan="2">
                    Jenis Produk
                    <a title="Klik disini untuk edit jenis produk" class="btn-edit-product-type"><i class="fa fa-pencil-square-o" aria-hidden="true"></i> </a>
                </th>
                <th colspan="4" class="text-center">Penjualan (Qty)</th>
            </tr>
            <tr>
                <th>1 bln</th>
                <th>2 bln</td>
                <th>3 bln</th>
                <th>6 bln</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($supplier_products as $product) : ?>
            <tr class="<?= $product->qty_unit ?? "danger" ?>">
                <td>
                    <img src="<?= $product->image ?>" class="img-thumbnail"/>
                </td>
                <td class="col-sm-3 col-lg-3">
                    <?= $product->name ?><br>
                    <?= $product->model ?>
                </td>
                <td class="col-sm-2 col-lg-2">
                    <input type="number" name="product_qtys[<?= $product->product_id ?>]" class="form-control text-right"/>
                </td>
                <td class="col-sm-1 col-lg-1">
                    <select name="qty_unit_ids[<?= $product->product_id ?>]" class="form-control" style="width: 100%;">
                        <option value="<?= $product->qty_unit_id ?>"><?= $product->qty_unit ?></option>
                        <?php 
                            $product_variant = Collection::from($product_variants)->find(function($value) use ($product){
                                return $value->product_id == $product->product_id;
                            });

                            if ($product_variant != NULL) {
                                echo "<option value=\"{$product_variant->qty_unit_id}\">{$product_variant->qty_unit}</option>";
                            }
                        ?>
                    </select>
                </td>
                <td class="text-right"><?= $product->qty ?? 0?></td>
                <td class="col-sm-1 col-lg-1 text-right">
                    <span class="input-stock-minimum">
                        <input type="number" 
                            name="product_minimums[<?= $product->product_id ?>]" 
                            class="form-control text-right"
                            value="<?= $product->minimum ?? 0?>"/>
                    </span>
                </td>
                <td class="col-sm-1 col-lg-1 text-right">
                    <input type="number" 
                        name="product_minimums[<?= $product->product_id ?>]" 
                        class="form-control text-right"
                        value="<?= $product->maximum ?? 0?>"/>
                </td>
                <td>
                    <select name="product_types[<?= $product->product_id ?>]" class="form-control">
                        <option value="fast" <?= $product->moving_product_status === 'fast' ? 'selected' : ''?>>Fast</option>
                        <option value="slow" <?= $product->moving_product_status === 'slow' ? 'selected' : ''?>>Slow</option>
                        <option value="normal" <?= $product->moving_product_status === 'normal' ? 'selected' : ''?>>Normal</option>
                        <option value="bad" <?= $product->moving_product_status === 'bad' ? 'selected' : ''?>>Dead stock</option>
                    </select>
                </td>
                <td class="text-right">0</td>
                <td class="text-right">0</td>
                <td class="text-right">0</td>
                <td class="text-right">0</td>
            </tr>
            <?php endforeach; ?>

            <?php if (count($supplier_products) == 0): ?>
            <tr>
                <td colspan="13">
                    <p>Produk tidak ditemukan</p>
                </td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>                    
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="pull-right" style="margin-bottom: 20px;">
            <button class="btn btn-primary"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</button>
        </div>
    </div>
</div>