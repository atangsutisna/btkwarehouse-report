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
        <h2 style="font-size: 14px;">LAPORAN PEMBELIAN</h2>
        <?php foreach ($suppliers as $supplier) : ?>
            <span style="font-size: 9px;"><?= strtoupper($supplier->supplier_name) ?></span>
            <table class="table1">
                <tr>
                    <th>No</th>
                    <th>No. Transaksi</th>
                    <th>No. Faktur</th>
                    <th>Tgl. Terima</th>
                    <th>Tgl. Invoice</th>
                    <th>Termin</th>
                    <th>Jatuh Tempo</th>
                    <th>Status</th>
                    <th>Subtotal</th>
                    <th>Discount</th>
                    <th>Total</th>
                    <th>Ongkir</th>
                    <th>Grand Total</th>
                </tr>
                <?php
                    $criterion = [];
                    if (isset($start_date) && $start_date !== NULL && $start_date !== '') {
                        $criterion['start_date'] = $start_date;
                    }

                    if (isset($end_date) && $end_date !== NULL && $end_date !== '') {
                        $criterion['end_date'] = $end_date;
                    }

                    $all_purchasing = get_purchasing_by_supplier($supplier->supplier_id, $criterion); 
                    $sum_subtotal = 0;
                    $sum_discount = 0;
                    $sum_total = 0;
                ?>
                <?php foreach ($all_purchasing as $key => $purchasing) : ?>
                <tr>
                    <td><?= $key + 1?></td>
                    <td><?= $purchasing->purchasing_no ?></td>
                    <td><?= $purchasing->goods_receipt_no ?></td>
                    <td><?= $purchasing->receive_date ?></td>
                    <td><?= $purchasing->invoice_date ?></td>
                    <td>-</td>
                    <td><?= $purchasing->due_date ?></td>
                    <td>-</td>
                    <td style="text-align: right;"><?= currency_format($purchasing->subtotal) ?></td>
                    <td style="text-align: right;"><?= currency_format($purchasing->discount) ?></td>
                    <td style="text-align: right;"><?= currency_format($purchasing->total) ?></td>
                    <td style="text-align: right;"><?= currency_format(0) ?></td>
                    <td style="text-align: right;"><?= currency_format($purchasing->total) ?></td>
                </tr>
                <?php
                    $sum_subtotal += $purchasing->subtotal;
                    $sum_discount += $purchasing->discount;
                    $sum_total += $purchasing->total; 
                    endforeach; 
                ?>
                <tr>
                    <td colspan="8" style="text-align: right;"><?= strtoupper($supplier->supplier_name) ?></td>
                    <td style="text-align: right;"><?= currency_format($sum_subtotal) ?></td>
                    <td style="text-align: right;"><?= currency_format($sum_discount) ?></td>
                    <td style="text-align: right;"><?= currency_format($sum_total) ?></td>
                    <td style="text-align: right;"><?= currency_format(0) ?></td>
                    <td style="text-align: right;"><?= currency_format($sum_total) ?></td>
                </tr>
            </table>
            <br>
        <?php endforeach; ?>
    </body>
</html>