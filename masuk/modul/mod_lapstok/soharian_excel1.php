<!DOCTYPE html>
<html>

<head>
    <title>STOK OPNAME</title>
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
        header("Content-type: application/xlsx");
        // header("Content-type: application/vnd-ms-excel");
        header("Content-Disposition: attachment; filename=stok_opname.xls");
        include_once '../../../configurasi/koneksi.php';
        include "../../../configurasi/fungsi_rupiah.php";
        include "../../../configurasi/fungsi_indotgl.php";

        $tgl_awal = $_GET['start'];
        $tgl_akhir = $_GET['finish'];
        $shift = $_GET['shift'];
        $shift1 = ($shift == '1')?'PAGI':'SORE';
    ?>

    <CENTER>
        <h4>STOK OPNAME SHIFT <?php echo $shift1;?></h4>
        <h4>Tanggal: <?=tgl_indo($tgl_awal).' s/d '.tgl_indo($tgl_akhir);?></h4>
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
                <th style="text-align: center; ">Harga Jual.</th>
                <th style="text-align: center; ">Waktu</th>
                <th style="text-align: center; ">ACC Manager</th>
            </tr>
        </thead>
        <tbody>
            <?php
            
            $no = 1;
            $query=mysqli_query($GLOBALS["___mysqli_ston"], "SELECT 
                trkasir_detail.kd_barang,
                trkasir_detail.id_dtrkasir,
                trkasir_detail.kd_trkasir,
                trkasir.kd_trkasir,
                trkasir.tgl_trkasir
                FROM trkasir_detail 
                JOIN trkasir ON trkasir_detail.kd_trkasir = trkasir.kd_trkasir
                WHERE trkasir.tgl_trkasir BETWEEN '$tgl_awal' AND '$tgl_akhir' AND shift = '$shift'
                GROUP BY trkasir_detail.kd_barang");

            while($lihat = mysqli_fetch_array($query)):
                
                $query2=mysqli_query($GLOBALS["___mysqli_ston"], "SELECT 
                    trkasir_detail.kd_barang,
                    trkasir_detail.id_dtrkasir,
                    trkasir_detail.kd_trkasir,
                    trkasir_detail.kd_barang,
                    barang.stok_barang,
                    SUM(trkasir_detail.qty_dtrkasir) as ttlqty,
                    SUM(trkasir_detail.hrgttl_dtrkasir) as ttlhrg,
                    trkasir.kd_trkasir,
                    trkasir.tgl_trkasir
                    FROM trkasir_detail 
                    JOIN trkasir ON trkasir_detail.kd_trkasir = trkasir.kd_trkasir
                    join barang on barang.kd_barang = trkasir_detail.kd_barang
                    WHERE trkasir_detail.kd_barang='$lihat[kd_barang]'
                    AND trkasir.tgl_trkasir BETWEEN '$tgl_awal' AND '$tgl_akhir' AND shift = '$shift'
                    ORDER BY trkasir_detail.id_dtrkasir ASC");
                    
                    $r2=mysqli_fetch_array($query2);
                    $ttlqty = $r2['ttlqty'];
                    $ttlhrg = $r2['ttlhrg'];
                    $stok = $r2['stok_barang'];
                    
                    $query3=mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM trkasir_detail 
                    WHERE id_dtrkasir='$lihat[id_dtrkasir]'");
                    $r3=mysqli_fetch_array($query3);
                    $kd_barang = $r3['kd_barang'];
                    $nmbrg_dtrkasir = $r3['nmbrg_dtrkasir'];
                    $sat_dtrkasir = $r3['sat_dtrkasir'];
                    $hrgjual_dtrkasir = $r3['hrgjual_dtrkasir'];
            ?>
                    <tr>
                        <td style="text-align: center; "><?= $no; ?></td>
                        <td style="text-align: left; width: 150px;"><?= $kd_barang; ?></td>
                        <td style="text-align: left; width: 300px"><?= $nmbrg_dtrkasir ?></td>
                        <td style="text-align: left; width: 80px;"><?= $sat_dtrkasir ?></td>
                        <td style="text-align: center; width: 100px;"><?= $stok ?></td>
                        <td style="text-align: center; width: 100px;"></td>
                        <td style="text-align: center; width: 100px;"></td>
                        <td style="text-align: right; width: 100px;"><?=format_rupiah($hrgjual_dtrkasir) ?></td>
                        <td style="text-align: center; width: 100px;"></td>
                        <td style="text-align: center; width: 100px;"></td>
                    </tr>

            <?php
                    $no++;
            endwhile;
            ?>
        </tbody>
    </table>
</body>

</html>