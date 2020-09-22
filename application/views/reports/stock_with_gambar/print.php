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
                    <table width="100%" padding="0">
                        <tr>
                            <td style="border: none; width:25%;padding:0px;"><b>Gudang</b></td>
                            <td style="border: none; width:10%;padding:0px;"><b>:</b></td>
                            <td style="border: none;padding:0px;">BTK MART</td>
                        </tr>
                        <tr>
                            <td style="border: none;padding:0px;"><b>Kategori</b></td>
                            <td style="border: none;padding:0px;"><b>:</b></td>
                            <td style="border: none;padding:0px;"><?= $cat_name ?></td>
                        </tr>
                    </table>
                </td>  
            </tr>
        </table>
        <table class="table1">
            <tr>
                <th>No</th>
                <th>Gambar</th>
                <th>Kode Produk</th>
                <th>Nama Produk</th>
                <th>Satuan</th>
                <th>Minimal Stock</th>
                <th>Jumlah Stock</th>
            </tr>
            <?php 
            $no=1;
            foreach ($stock as $row) {
             ?>
            <tr>
                <td><?= $no; ?></td>
                <td><?= $row->image; ?></td>
                <td><?= $row->code; ?></td>
                <td><?= $row->name; ?></td>
                <td><?= $row->satuan; ?></td>
                <td><?= $row->min; ?></td>
                <td><?= $row->stock.' '.$row->satuan; ?></td>
            </tr>
            <?php 
            $no++; 
            } ?>
        </table>
    </body>
</html>