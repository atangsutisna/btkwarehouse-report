<!DOCTYPE html>
<html>
    <head>
        <title><?= $title; ?></title>
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
            border: 1px solid #000;
            padding: 4px 10px;
        }
        .table1,th{ 
            background-color: #bde9ba;
        }
        .table1,td{ 
            background-color: #fff;
        }
    </style>    
    <body>
        <h3 style="font-size: 14px; text-align: center;margin-bottom:0;"><?=strtoupper($title);?></h3>
        <p style="text-align: center; font-size: 11px;margin-top:0;">Per-tanggal: <?= date('d-m-Y H:i:s') ?></p>
        <table class="table1" style="border: none;">
            <tr>
                <td style="width: 55%; border: none">
                    <b>BTK MART</b><br/>
                    <b>Jln, Jendral Sudirman no. 38</b><br/>
                </td>  
                <td style="width: 40%; vertical-align: top; border: none;">
                    <b style="margin-right: 10px;">Gudang:</b> BTK MART<br/>
                    <b style="margin-right: 10px;">Kategori:</b> <?= $cat_name ?><br/>
                    <b style="margin-right: 10px;">Penilaian:</b> <?= $type ?><br/>
                </td>  
            </tr>
        </table>
        <table class="table1">
            <tr>
                <th>No</th>
                <th>Kode Produk</th>
                <th>Nama Produk</th>
                <th>Satuan</th>
                <th>Jumlah</th>
                <th>Harga</th>
                <th>Total</th>
            </tr>
            <?php 
            $total_all = 0;
            $no=1;
            foreach ($stock as $row) {
             ?>
            <tr>
                <td><?= $no; ?></td>
                <td><?= $row->code; ?></td>
                <td><?= $row->name; ?></td>
                <td><?= $row->satuan; ?></td>
                <td><?= $row->stock; ?></td>
                <td><?= $row->price; ?></td>
                <td><?= number_format($row->total,2); ?></td>
            </tr>
            <?php 
            $no++; 
            $total_all += $row->total;
            } ?>
            <tr>
                <td colspan="6" style="text-align:right;">Total <?= $cat_name ?></td>
                <td><?= number_format($total_all,2); ?></td>
            </tr>
        </table>
</body>
</html>