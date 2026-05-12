<!DOCTYPE html>
<html>

<head>
    <title>Export Data Ke Excel Dengan PHP - www.malasngoding.com</title>
</head>

<body>
    <style type="text/css">
        body {
            font-family: sans-serif;
        }

        table {
            margin: 20px auto;
            border-collapse: collapse;
        }

        table th,
        table td {
            border: 1px solid #3c3c3c;
            padding: 3px 8px;

        }

        a {
            background: blue;
            color: #fff;
            padding: 8px 10px;
            text-decoration: none;
            border-radius: 2px;
        }
    </style>

    <?php
    header("Content-type: application/vnd-ms-excel");
    header("Content-Disposition: attachment; filename=Data Barang Macet.xls");
    include_once '../../../configurasi/koneksi.php';

    ?>

    <CENTER>
        <h4>MySIFA TRAFFIC ANALYSIS</h4>
    </CENTER>
    <br>

    <table border="1">
        <thead>
            <tr>
                <th style="text-align: center; ">No</th>
                <th style="text-align: center; ">Kode</th>
                <th style="text-align: center; ">Nama Barang</th>
                <th style="text-align: center; ">Satuan</th>
                <th style="text-align: center; ">Rak</th>
                <th style="text-align: center; ">Stok</th>
                <th style="text-align: center; ">Stok Fisik</th>
                <th style="text-align: center; ">Exp Date.</th>
                <th style="text-align: center; ">Waktu</th>
                <th style="text-align: center; ">ACC Manager</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $query = $db->prepare("SELECT * FROM barang 
            WHERE NOT EXISTS (
                SELECT trkasir_detail.kd_barang FROM trkasir_detail
                JOIN trkasir ON trkasir_detail.kd_trkasir = trkasir.kd_trkasir
                WHERE trkasir_detail.id_barang = barang.id_barang
                AND trkasir.tgl_trkasir BETWEEN '$_GET[start]' AND '$_GET[finish]'
            )
            ORDER BY barang.stok_barang DESC");
            $query->execute();

            while ($value = $query->fetch(PDO::FETCH_ASSOC)) :
                $kodebarang = "'" . $value['kd_barang'];
                if ($value['stok_barang'] > 0) :
            ?>
                    <tr>
                        <td style="text-align: center; "><?= $no; ?></td>
                        <td style="text-align: left; width: 150px;"><?= $kodebarang; ?></td>
                        <td style="text-align: left; width: 300px"><?= $value['nm_barang'] ?></td>
                        <td style="text-align: center; width: 80px;"><?= $value['sat_barang'] ?></td>
                        <td style="text-align: center; width: 80px;"><?= $value['jenisobat'] ?></td>
                        <td style="text-align: center; width: 100px;"><?= strval($value['stok_barang']) ?></td>
                        <td style="text-align: center; width: 100px;"></td>
                        <td style="text-align: center; width: 100px;"></td>
                        <td style="text-align: center; width: 100px;"></td>
                        <td style="text-align: center; width: 100px;"></td>
                    </tr>

            <?php
                    $no++;
                endif;
            endwhile;
            ?>
        </tbody>
    </table>
</body>

</html>