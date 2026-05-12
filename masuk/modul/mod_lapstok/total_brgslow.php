<?php
include "../../../configurasi/koneksi.php";

$ubah = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT trkasir_detail.kd_barang, COUNT(trkasir_detail.kd_barang) AS t30, SUM(trkasir_detail.qty_dtrkasir) AS q30, SUM(trkasir_detail.hrgttl_dtrkasir) AS om30 FROM trkasir_detail
JOIN trkasir ON trkasir_detail.kd_trkasir = trkasir.kd_trkasir
WHERE trkasir.tgl_trkasir BETWEEN '$_POST[start]' AND '$_POST[finish]'
GROUP BY trkasir_detail.kd_barang;");


$totalOm30 = 0;
$totalL30 = 0;
$totalStok = 0;

while ($re = mysqli_fetch_array($ubah)) {
    if ($re['t30'] > 0 && $re['t30'] < 6) {
        $getbrg = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT SUM(hrgsat_barang * stok_barang) AS nilaibarang, SUM($re[om30]-($re[q30]*hrgsat_barang)) AS l30 FROM barang WHERE kd_barang = '$re[kd_barang]'");
        $brg = mysqli_fetch_array($getbrg);
        // $hargabeli = $brg['hrgsat_barang'];
        // $nilaibarang = $brg['hrgsat_barang'] * $brg['stok_barang'];

        // $l30 = $re['om30'] - ($re['q30'] * $hargabeli);
        $totalOm30 += $re['om30'];
        $totalL30 += $brg['l30'];
        $totalStok += $brg['nilaibarang'];
    }
}

$json_data = [
    "totalOm30"         => $totalOm30,
    "totalL30"          => $totalL30,
    "totalStok"         => $totalStok,
];
echo json_encode($json_data);
