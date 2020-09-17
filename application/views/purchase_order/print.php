<!DOCTYPE html>
<html>
    <head>
        <title>Purchase Order</title>
    </head>
    <style>
      /**
      .bordered table, th, td {
      padding: 10px;
      border: 1px solid black; 
      border-collapse: collapse;
      }
      **/
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
        <h2 style="font-size: 11px;">PURCHASING ORDER</h2>
        <table class="table1">
            <tr>
                <td style="width: 50%;">
                    <b>Tanggal:</b><br/>
                    <?= format_date($purchase_order->purchase_order_date) ?><br/><br/>
                    <b>No. Transaksi: </b><br/>
                    <?= $purchase_order->purchase_order_no ?><br/>
                </td>  
                <td style="width: 50%; vertical-align: top;">
                    <b>Nama Supplier:</b><br/>
                    <?= $purchase_order->supplier_name ?>                    
                </td>  
            </tr>
        </table><br/>
        <table class="table1">
            <tr>
                <th>No</th>
                <th>Gambar</th>
                <th>Produk <br> Kode / Barcode Produk</th>
                <th style="text-align: center;">Jumlah</th>
                <th style="text-align: center;">Unit</th>
            </tr>
            <?php foreach ($purchase_order_items as $key => $value) : ?>
            <tr>
                <td><?= $key + 1?></td>
                <td>
                    <img src="<?= $value['product_image'] ?>">
                </td>
                <td style="width: 62%;">
                    <?= $value['product_name'] ?><br>
                    <?= $value['product_model'] ?>
                </td>
                <td style="text-align: right;"><?= $value['qty'] ?></td>
                <td><?= $value['qty_unit'] ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
        <br/>
        <p style="font-size: 8px; font-family: sans-serif;">
            <span>Disetujui Oleh:</span>
        </p>
        <img src="<?= base_url('assets/img/small-logo.png') ?>" width="50">
        <p style="font-size: 8px; font-family: sans-serif;">
            <span>Mr. Dedi</span>
        </p>
        <p>&nbsp;</p>
        <p style="font-size: 8px; font-family: sans-serif;">
            PERHATIAN:
            <ol style="font-size: 8px; font-family: sans-serif;">
              <li>SETIAP PENGIRIMAN WAJIB MELAMPIRKAN PURCHASING ODER DARI BTK MART</li>
              <li>PRODUK YANG DIKIRIM DALAM KEADAAN BAIK (LAYAK) DAN CUKUP</li>
              <li>UNTUK PRODUK MAKANAN MEMILIKI EXPIRED DATE PADA RANGE MINIMAL 6 BULAN S/D 1 TAHUN KEDEPAN </li>
              <li>UNTUK PRODUK NON MAKANAN & MINUMAN MEMILIKI EXPIRED DATE LEBIH DARI / MINIMAL 1 TAHUN</li>
              <li>PIHAK SUPPLIER DILARANG UNTUK MENAMBAHKAN PRODUK-PRODUK LAIN TANPA PERSETUJUAN DARI ADMIN BTK MART</li>
              <li>BATAS MAKSIMAL WAKTU PENERIMAAN BARANG ADALAH S/D PUKUL 16.00 WIB (MINGGU LIBUR)</li>
              <li>BTK MART BERHAK MENOLAK PENGIRIMAN YANG MELANGGAR KETENTUAN DI ATAS</li>
              <li>PURCHASING ORDER HANYA BERLAKU SELAMA 7 HARI TERSEBUT DITERBITKAN</li>
            </ol>
        </p>
    </body>
</html>