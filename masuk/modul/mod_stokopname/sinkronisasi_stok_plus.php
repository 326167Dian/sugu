<?php
session_start();
 if (empty($_SESSION['username']) AND empty($_SESSION['passuser'])){
  echo "<link href='style.css' rel='stylesheet' type='text/css'>
 <center>Untuk mengakses modul, Anda harus login <br>";
  echo "<a href=../../index.php><b>LOGIN</b></a></center>";
}
else{

include "../../../configurasi/koneksi.php";
include "../../../configurasi/fungsi_thumb.php";
include "../../../configurasi/library.php";

$tampil_barang = $db->query("SELECT * FROM barang ORDER BY barang.id_barang");
$no=1;

while ($r = $tampil_barang->fetch(PDO::FETCH_ASSOC)) {

    $buy_stmt = $db->prepare("SELECT trbmasuk.tgl_trbmasuk, SUM(trbmasuk_detail.qty_dtrbmasuk) AS totalbeli FROM trbmasuk_detail JOIN trbmasuk ON (trbmasuk_detail.kd_trbmasuk = trbmasuk.kd_trbmasuk) WHERE kd_barang = ?");
    $buy_stmt->execute([$r['kd_barang']]);
    $buy2 = $buy_stmt->fetch(PDO::FETCH_ASSOC);

    $jual_stmt = $db->prepare("SELECT trkasir.tgl_trkasir, SUM(trkasir_detail.qty_dtrkasir) AS totaljual FROM trkasir_detail JOIN trkasir ON (trkasir_detail.kd_trkasir = trkasir.kd_trkasir) WHERE kd_barang = ?");
    $jual_stmt->execute([$r['kd_barang']]);
    $sell = $jual_stmt->fetch(PDO::FETCH_ASSOC);

    $selisih = $buy2['totalbeli'] - $sell['totaljual'];
    $stokbarang = $r['stok_barang'];

    if($stokbarang != $selisih)
    {
        $db->prepare("UPDATE barang SET stok_barang = ? WHERE kd_barang = ? AND ? >= 0")->execute([$selisih, $r['kd_barang'], $selisih]);
    }
    else{}

}
        header('location:../../media_admin.php?module=trkasir');
    
}
?>
