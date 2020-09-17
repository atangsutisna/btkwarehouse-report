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
        <h2 style="font-size: 14px; text-align: center;">LAPORAN KARTU STOK</h2>
        <p style="text-align: center; font-size: 11px;">Per-tanggal: <?= date('d-m-Y H:i:s') ?></p>
        <table class="table1">
            <tr>
                <td style="width: 55%;">
                    <b>BTK MART</b><br/>
                    <b>Jln, Jendral Sudirman no. 38</b><br/>
                </td>  
                <td style="width: 40%; vertical-align: top;">
                    Gudang: BTK MART<br/>
                    Produk: <?= $product_name ?><br/>
                    Periode: <?= $start_date ?> - <?= $end_date ?>
                </td>  
            </tr>
        </table><br/>
        <table class="table1">
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Jumlah</th>
                <th>Saldo</th>
            </tr>
            <tr>
                <td colspan="3">
                    Saldo Awal
                </td>
                <td style="text-align: right;">Undefined</td>
            </tr>
            <?php foreach ($mutation_stocks as $key => $mutation_stock) : ?>
            <tr>
                <td><?= $key + 1?></td>
                <td><?= $mutation_stock->created_at ?></td>
                <td style="text-align: right;"><?= $mutation_stock->qty ?></td>
                <td style="text-align: right;"><?= $mutation_stock->last_qty ?></td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <td colspan="3">
                    Saldo Akhir
                </td>
                <td style="text-align: right;">0</td>
            </tr>
            <?php if (empty($mutation_stocks)) : ?>
            <tr>
                <td colspan="3">Undefined</td>
            </tr>
            <?php endif; ?>
        </table>
    </body>
</html>