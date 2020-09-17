<!DOCTYPE html>
<html>
    <head>
        <title>Laporan Pembelian</title>
    </head>
    <style>
        .table1 {
            font-family: sans-serif;
            color: #232323;
            border-collapse: collapse;
            width: 100%;
            font-size: 9px;
        }
        
        .table1, th, td {
            border: 1px solid #999;
            padding: 8px 20px;
        }
    </style>    
    <body>
        <h2 style="font-size: 14px; text-align: center;">DAFTAR STOK PRODUK</h2>
        <p style="text-align: center; font-size: 11px;">Per-tanggal: <?= date('d-m-Y H:i:s') ?></p>
        <table class="table1" style="border: none;">
            <tr>
                <td style="width: 55%; border: none">
                    <b>BTK MART</b><br/>
                    <b>Jln, Jendral Sudirman no. 38</b><br/>
                </td>  
                <td style="width: 40%; vertical-align: top; border: none;">
                    <b style="margin-right: 10px;">Gudang:</b> BTK MART<br/>
                    <b style="margin-right: 10px;">Kategori:</b> <?= $cat_name ?><br/>
                </td>  
            </tr>
        </table>
        <table class="table1">
            <tr>
                <th>No</th>
                <th>Kode/Barcode</th>
                <th>Nama</th>
                <th>Jumlah Stok</th>
            </tr>
            <?php foreach ($product_stocks as $key => $product_stock) : ?>
            <tr>
                <td><?= $key + 1?></td>
                <td><?= $product_stock->product_model ?></td>
                <td><?= $product_stock->product_name ?></td>
                <td style="text-align: right;"><?= $product_stock->qty.' '. $product_stock->qty_unit ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </body>
</html>