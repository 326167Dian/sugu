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

    try {
        $db->beginTransaction();

        $sinkron = $db->prepare("UPDATE barang b
                                LEFT JOIN (
                                    SELECT kd_barang, SUM(qty_dtrbmasuk) AS totalbeli
                                    FROM trbmasuk_detail
                                    GROUP BY kd_barang
                                ) beli ON beli.kd_barang = b.kd_barang
                                LEFT JOIN (
                                    SELECT kd_barang, SUM(qty_dtrkasir) AS totaljual
                                    FROM trkasir_detail
                                    GROUP BY kd_barang
                                ) jual ON jual.kd_barang = b.kd_barang
                                SET b.stok_barang = (COALESCE(beli.totalbeli, 0) - COALESCE(jual.totaljual, 0))
                                WHERE b.stok_barang <> (COALESCE(beli.totalbeli, 0) - COALESCE(jual.totaljual, 0))
                                AND (COALESCE(beli.totalbeli, 0) - COALESCE(jual.totaljual, 0)) >= 0");
        $sinkron->execute();

        $db->commit();
    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
    }

    header('location:../../media_admin.php?module=trkasir');
}
?>
