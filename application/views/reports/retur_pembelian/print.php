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
                            <td style="border: none;padding:0px;"><b>Supplier</b></td>
                            <td style="border: none;padding:0px;"><b>:</b></td>
                            <td style="border: none;padding:0px;"><?= $supplier ?></td>
                        </tr>
                    </table>
                </td>  
            </tr>
        </table>
        <?php 
        $total_all=0;
        foreach ($stock as $row) {
        ?>
            <b><?= $row->name; ?></b>
            <table class="table1">
                <tr>
                    <th>No</th>
                    <th>No Transaksi</th>
                    <th>No Faktur</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Total</th>
                </tr>
                <?php
                $no=1;
                $sql = "SELECT b.retur_no,
                        DATE_FORMAT(b.retur_date,'%d-%m-%Y') as tanggal,
                        '' as no_faktur,
                        b.status,
                        c.qty*d.price_2 as total
                        FROM {PRE}supplier a 
                        INNER JOIN {PRE}retur b ON (a.supplier_id=b.supplier_id) 
                        INNER JOIN {PRE}retur_items c ON (b.retur_id=c.retur_id) 
                        INNER JOIN {PRE}product d ON (c.product_id=d.product_id)                         
                        WHERE a.supplier_id='".$row->supplier_id."'";
                $result = $this->db->query($sql)->result();
                $sub_total = 0;
                foreach ($result as $row2) {
                ?>
                <tr>
                    <td><?= $no; ?></td>
                    <td><?= $row2->retur_no; ?></td>
                    <td><?= $row2->no_faktur; ?></td>
                    <td><?= $row2->tanggal; ?></td>
                    <td><?= strtoupper($row2->status); ?></td>
                    <td style="width:20%;"><?= number_format($row2->total,2); ?></td>
                </tr>
                <?php $no++; $sub_total+=$row2->total; } ?>
                <tr>
                    <th colspan="5" style="text-align:right;">Total <?= $row->name; ?></th>
                    <th style="width:20%;"><?= number_format($sub_total,2); ?></th>
                </tr>
            </table>
        <?php 
        $total_all+=$sub_total;
        } ?>
        <br/>
        <table class="table1" width="100%">
            <tr>
                <th colspan="5" style="text-align:right;">Grand Total</th>
                <th style="width:20%;"><?= number_format($total_all,2); ?></th>
            </tr>
        </table>
    </body>
</html>