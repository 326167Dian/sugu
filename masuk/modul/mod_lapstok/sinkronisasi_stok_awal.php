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
    
    $tampil_barang = $db->prepare("SELECT * FROM barang ORDER BY barang.id_barang ASC");
    $tampil_barang->execute();
    $no=1;
    while ($r = $tampil_barang->fetch(PDO::FETCH_ASSOC)) {
    
            $r1 = $r['kd_barang'];
            $stokbarang = $r['stok_barang'];
            //tarik waktu awal input dan total quantity input
            $tampilmasuk = $db->prepare("SELECT SUM(qty_dtrbmasuk) AS subtotal, MIN(waktu) AS masukawal FROM trbmasuk_detail
                                        WHERE kd_barang = ?");
            $tampilmasuk->execute([$r1]);
            $masuk = $tampilmasuk->fetch(PDO::FETCH_ASSOC);
            $masuk1 = $masuk['subtotal'];
            $masuk2 = $masuk['masukawal'];
            //tarik waktu awal keluar barang
            $tampilkeluar = $db->prepare("SELECT MIN(waktu) AS keluarawal, MAX(waktu) AS keluarakhir FROM trkasir_detail 
                                        WHERE kd_barang = ?");
            $tampilkeluar->execute([$r1]);
            $keluar  = $tampilkeluar->fetch(PDO::FETCH_ASSOC);
            $keluar1 = $keluar['keluarawal'];
            $keluar2 = $keluar['keluarakhir'];
    
            //tetapkan waktu standar keluar awal dan input awal
            if ($keluar1 < $masuk2) {
                $patokan = $masuk2;
            } else {
                $patokan = $keluar1;
            }
            $transaksi_atas = $db->prepare("SELECT SUM(qty_dtrkasir) AS qty_atas FROM trkasir_detail 
                                            WHERE kd_barang = ? 
                                            AND waktu BETWEEN '$keluar1' AND '$masuk2'");
            $transaksi_atas->execute([$r1]);
            $qty_atas2 = $transaksi_atas->fetch(PDO::FETCH_ASSOC);
            $qty_atas3 = $qty_atas2['qty_atas'];
    
            $transaksi_bawah = $db->prepare("SELECT SUM(qty_dtrkasir) AS qty_bawah FROM trkasir_detail 
                                            WHERE kd_barang = ? 
                                            AND waktu BETWEEN '$masuk2' AND '$keluar2'");
            $transaksi_bawah->execute([$r1]);
            $qty_bawah2 = $transaksi_bawah->fetch(PDO::FETCH_ASSOC);
            $qty_bawah3 = $qty_bawah2['qty_bawah'];
            $stokmasukreal = $qty_atas3 + $masuk1 ;
            $stok_real = $qty_atas3 + $masuk1 - ($qty_atas3+$qty_bawah3);
    
            //tarik stok awal input barang
            $awalinput = $db->prepare("SELECT qty_dtrbmasuk FROM trbmasuk_detail 
                                        WHERE waktu = ?");
            $awalinput->execute([$masuk2]);
            $inputawal = $awalinput->fetch(PDO::FETCH_ASSOC);
            $stokawalinput = $inputawal['qty_dtrbmasuk'];
            $stokbaru = $stokawalinput + $qty_atas3 ;
    
            if($qty_atas3 > 0 ) {
                $db->prepare("UPDATE barang SET stok_barang = ? WHERE kd_barang =?")->execute([$stok_real, $r1]);
                $db->prepare("UPDATE trbmasuk_detail SET qty_dtrbmasuk = ? WHERE waktu =?")->execute([$stokbaru, $masuk2]);
            }
    
    }
    header('location:../../media_admin.php?module=koreksistok');
    
}
?>
