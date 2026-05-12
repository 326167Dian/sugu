<!DOCTYPE html>
<html>

<head>
    <title>Laporan Data Barang Macet</title>
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
    header("Content-Disposition: attachment; filename=Stok Opname Bulanan.xls");
    include_once '../../../configurasi/koneksi.php';
    include "../../../configurasi/fungsi_rupiah.php";

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
                <th style="text-align: center; ">Stok</th>
                <th style="text-align: center; ">Stok Fisik</th>
                <th style="text-align: center; ">Exp Date.</th>
                <th style="text-align: center; ">Harga Beli.</th>
                <th style="text-align: center; ">Waktu</th>
                <th style="text-align: center; ">ACC Manager</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $query=mysqli_query($GLOBALS["___mysqli_ston"], "select * from barang where jenisobat='$_GET[jenisobat]' ORDER BY barang.nm_barang");

            while($value=mysqli_fetch_array($query)):
                $kodebarang = "'" . $value['kd_barang'];
                // if ($value['stok_barang'] > 0) :
            ?>
                    <tr>
                        <td style="text-align: center; "><?= $no; ?></td>
                        <td style="text-align: left; width: 150px;"><?= $kodebarang; ?></td>
                        <td style="text-align: left; width: 300px"><?= $value['nm_barang'] ?></td>
                        <td style="text-align: center; width: 80px;"><?= $value['sat_barang'] ?></td>
                        <td style="text-align: center; width: 100px;"><?= $value['stok_barang'] ?></td>
                        <td style="text-align: center; width: 100px;"></td>
                        <td style="text-align: center; width: 100px;"></td>
                        <td style="text-align: right; width: 100px;"><?=format_rupiah($value['hrgsat_barang']) ?></td>
                        <td style="text-align: center; width: 100px;"></td>
                        <td style="text-align: center; width: 100px;"></td>
                    </tr>

            <?php
                    $no++;
                // endif;
            endwhile;
            ?>
        </tbody>
    </table>
</body>

</html>