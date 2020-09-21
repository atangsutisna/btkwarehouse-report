<!DOCTYPE html>
<html>
    <head>
        <title>Label Harga</title>
        <style type="text/css">
            .barcode {
                vertical-align: top;
                color: #000044;
            }
            .barcodecell {
                text-align: center;
            }       
        </style>
    </head>  
    <body>
        <?php foreach($product as $row){ ?>
        <div style="float: left; width: 33%;margin-bottom:5px;">
            <p style="margin-top: 0px;width:100%;border-top: 1px dashed #000;"></p>
        <table width="100%">
            <tr>
                <td style="width:33%;">
                    <table width="100%">
                        <tr>
                            <td>
                                <p style="font-size:12px;font-weight: bold;"><?= $row->name; ?></p>
                            </td>
                            <td>
                                <p style="font-size:10px;float:right;">EXPIRED <?= $row->expired; ?></p>
                            </td>
                        </tr>
                    </table>
                    <hr/>
                    <table width="100%">
                        <tr>
                            <td rowspan="4" style="text-align: center;">
                                <div class="barcodecell">
                                    <barcode code="<?= $row->model; ?>" type="I25" size="0.5" height="2.4" class="barcode" />
                                </div>
                                <p style="font-size:10px;"><?= $row->model; ?></p>
                            </td>

                            <td>
                                <p style="font-size:12px;font-weight: bold;">Rp.</p>
                                <p style="font-size:14px;font-weight: bold;margin-left:20px;"><?= $row->price; ?></p>
                                 <p style="font-size:12px;">/ Pack</p>
                                <p style="font-size:9px;">Cetak : <?= date('d-m-Y H:i:s'); ?></p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <p style="margin-bottom: 0px;width:100%;border-top: 1px dashed #000;"></p>
        </div>
        <?php } ?>
    </body>
</html>