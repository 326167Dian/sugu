<?php
include "../../../configurasi/koneksi.php";

$act = $_GET['act'];

if($act == 'update_order'){
    $id_dtrbmasuk       = $_POST['id_dtrbmasuk'];
    // $qty_dtrbmasuk      = ceil($_POST['qty_dtrbmasuk']/$_POST['konversi']);
    $qty_dtrbmasuk          = $_POST['qty_dtrbmasuk'];
    $qtygrosir_dtrbmasuk    = $_POST['qtygrosir_dtrbmasuk'];
    $konversi               = $_POST['konversi'];
    
    $cekdetail = $db->prepare("SELECT * 
                                FROM ordersdetail 
                                WHERE id_dtrbmasuk=?");
    $cekdetail->execute([$id_dtrbmasuk]);
    $ketemucekdetail = $cekdetail->rowCount();
    $rcek = $cekdetail->fetch(PDO::FETCH_ASSOC);
    
    if ($ketemucekdetail > 0) {
        // $qtygrosir_dtrbmasuk    = $qty_dtrbmasuk / $rcek['konversi'];
        $hrgttl_dtrbmasuk       = $qty_dtrbmasuk * $rcek['hrgsat_dtrbmasuk'];
        $id_barang              = $rcek['id_barang'];
        
        $db->prepare("UPDATE ordersdetail SET
                        qty_dtrbmasuk       = ?,
                        konversi            = ?,
                        qtygrosir_dtrbmasuk = ?,
                        hrgttl_dtrbmasuk    = ?
                    WHERE id_dtrbmasuk  = ?")->execute([$qty_dtrbmasuk, $konversi, $qtygrosir_dtrbmasuk, $hrgttl_dtrbmasuk, $id_dtrbmasuk]);
                                                
        $db->prepare("UPDATE barang SET
                        konversi    = ?
                    WHERE id_barang = ?")->execute([$konversi, $id_barang]);                                        
    }
    
}
elseif($act == 'satgrosir'){
    $id_dtrbmasuk           = $_POST['id_dtrbmasuk'];
    $satgrosir_dtrbmasuk    = $_POST['satgrosir_dtrbmasuk'];
    $id_barang              = $_POST['id_barang'];
    
    $db->prepare("UPDATE ordersdetail SET
                    satgrosir_dtrbmasuk = ?
                WHERE id_dtrbmasuk  = ?")->execute([$satgrosir_dtrbmasuk, $id_dtrbmasuk]);
                                                
    $db->prepare("UPDATE barang SET
                    sat_grosir  = ?
                WHERE id_barang = ?")->execute([$satgrosir_dtrbmasuk, $id_barang]);                                               
}

?>