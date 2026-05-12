<!DOCTYPE html>
<html>

<head>
    <title>Laporan Laba Data Penjualan</title>
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
header("Content-Disposition: attachment; filename=Laporan_data_laba_penjualan.xls");
include_once '../../../configurasi/koneksi.php';
include "../../../configurasi/fungsi_rupiah.php";


?>

<CENTER>
    <h4>MySIFA LAPORAN LABA PENJUALAN</h4>
</CENTER>
<br>

<table border="1">

    <thead>
    <tr>
        <th style="text-align: center; ">No</th>
        <th style="text-align: center; ">Kode Barang</th>
        <th style="text-align: center; ">Nama Barang</th>
        <th style="text-align: center; ">Qty</th>
        <th style="text-align: center; ">Satuan</th>
        <th style="text-align: center; ">Total</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $tgl_awal = $_GET['tgl_awal'];
    $tgl_akhir = $_GET['tgl_akhir'];

    $no = 1;
    $query = $db->prepare("SELECT *, 
                    SUM(trkasir_detail.qty_dtrkasir) as q30,
                    SUM(trkasir_detail.hrgttl_dtrkasir) as om30 FROM trkasir_detail
                    JOIN trkasir ON trkasir.kd_trkasir = trkasir_detail.kd_trkasir
                    WHERE trkasir.tgl_trkasir BETWEEN ? AND ?
                    GROUP BY trkasir_detail.kd_barang");
    $query->execute([$tgl_awal, $tgl_akhir]);


    while($value = $query->fetch(PDO::FETCH_ASSOC)):
        ?>
        <tr>
            <td style="text-align: center; "><?= $no; ?></td>
            <td style="text-align: left; width: 150px;"><?= $value['kd_barang']; ?></td>
            <td style="text-align: left; width: 300px"><?= $value['nmbrg_dtrkasir'] ?></td>
            <td style="text-align: center; width: 80px;"><?= $value['q30'] ?></td>
            <td style="text-align: center; width: 100px;"><?= $value['sat_dtrkasir'] ?></td>
            <td style="text-align: right; width: 100px;"><?=format_rupiah($value['om30']) ?></td>
        </tr>
        <?php
        $no++;
    endwhile;
    $ttldet = $db->prepare("select sum(hrgttl_dtrkasir) as totaldet, sum(profit) as untung from trkasir_detail 
                                    JOIN trkasir ON (trkasir.kd_trkasir = trkasir_detail.kd_trkasir)
                                    where tgl_trkasir between '$tgl_awal' AND '$tgl_akhir'");
    $ttldet->execute();
    $tdetail = $ttldet->fetch(PDO::FETCH_ASSOC);

    $ttljual = $db->prepare("select sum(ttl_trkasir) as totaltx from trkasir 
                        where tgl_trkasir between '$tgl_awal' AND '$tgl_akhir' ");
    $ttljual->execute();
    $total = $ttljual->fetch(PDO::FETCH_ASSOC);
    $diskon = $tdetail['totaldet'] - $total['totaltx'];
    $lababersih = $tdetail['untung'] -  $diskon
    ?>
    <tr>
        <td colspan="5" style="text-align:right;">Total Nilai Penjualan</td>  <td><?= format_rupiah($tdetail['totaldet']) ?></td>
    </tr>
    <tr>
        <td colspan="5" style="text-align:right;">Diskon Transaksi</td>  <td><?= format_rupiah($diskon) ?></td>
    </tr>
    <tr>
        <td colspan="5" style="text-align:right;">Total Nilai Transaksi</td>  <td><?= format_rupiah($total['totaltx']) ?></td>
    </tr>
    <tr>
        <td colspan="5" style="text-align:right;">Laba Tanpa Diskon</td>  <td><?= format_rupiah($tdetail['untung']) ?></td>
    </tr>
    <tr>
        <td colspan="5" style="text-align:right;">Laba Setelah Diskon</td>  <td><?= format_rupiah($lababersih) ?></td>
    </tr>
    </tbody>
</table>
</body>

</html>