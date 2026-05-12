<?php
include "../../../configurasi/koneksi.php";

$kd_trbmasuk            = $_POST['kd_trbmasuk'];
$kd_orders              = $_POST['kd_orders'];
$id_barang              = $_POST['id_barang'];
$kd_barang              = $_POST['kd_barang'];
$nmbrg_dtrbmasuk        = $_POST['nmbrg_dtrbmasuk'];
// $qty_dtrbmasuk       = $_POST['qty_dtrbmasuk'];
$qty_dtrbmasuk          = $_POST['qtygrosir_dtrbmasuk'] * $_POST['konversi'];
$qtygrosir_dtrbmasuk    = $_POST['qtygrosir_dtrbmasuk'];
$sat_dtrbmasuk          = $_POST['sat_dtrbmasuk'];
$satgrosir_dtrbmasuk    = $_POST['satgrosir_dtrbmasuk'];
$hnasat_dtrbmasuk       = str_replace(".","",$_POST['hnasat_dtrbmasuk']);
$hrgjual_dtrbmasuk      = str_replace(".","",$_POST['hrgjual_dtrbmasuk']);
$diskon                 = $_POST['diskon'];
$konversi               = $_POST['konversi'];
// $hrgsat_dtrbmasuk = $hnasat_dtrbmasuk * 1.11 ;
$hrgsat_dtrbmasuk       = round($hnasat_dtrbmasuk * (1-($diskon/100)) * 1.11/$konversi);
// $hnappn             = $hnasat_dtrbmasuk * 1.11/$konversi;
// $hrgsat_dtrbmasuk       = round($hnasat_dtrbmasuk * (1-($diskon/100))/$konversi);

$no_batch = $_POST['no_batch'];
$exp_date = date('Y-m-d', strtotime($_POST['exp_date']));


if($qty_dtrbmasuk == ""){
    $qty_dtrbmasuk = "1";
}else{}
if($diskon == ""){
    $diskon = "0";
}else{}

//cek apakah barang sudah ada
$cekdetail = $db->prepare("SELECT * FROM trbmasuk_detail WHERE kd_barang = ? AND kd_trbmasuk = ?");
$cekdetail->execute([$kd_barang, $kd_trbmasuk]);
$ketemucekdetail = $cekdetail->rowCount();
$rcek = $cekdetail->fetch(PDO::FETCH_ASSOC);
if ($ketemucekdetail > 0){

    $id_dtrbmasuk = $rcek['id_dtrbmasuk'];
    $qtylama = $rcek['qty_dtrbmasuk'];
    $qty_grosirlama = $rcek['qty_grosir'];
    $qty_grosir = $qty_grosirlama + $qty_dtrbmasuk;
    // $ttlqty = $qtylama + ($qty_dtrbmasuk*$konversi) ;
    // $ttlqty = $qtylama + ($qty_dtrbmasuk/$konversi) ;
    $ttlqty = ($qty_dtrbmasuk) ;
    // $ttlharga = $ttlqty * $hnasat_dtrbmasuk;
    $ttlharga = $ttlqty * $hrgsat_dtrbmasuk;
    

    $db->prepare("UPDATE trbmasuk_detail SET qty_dtrbmasuk = ?, qty_grosir = ?, hnasat_dtrbmasuk = ?, diskon = ?, hrgsat_dtrbmasuk = ?, hrgjual_dtrbmasuk = ?, hrgttl_dtrbmasuk = ?, no_batch = ?, exp_date = ? WHERE id_dtrbmasuk = ?")->execute([$qty_dtrbmasuk, $qtygrosir_dtrbmasuk, $hnasat_dtrbmasuk, $diskon, $hrgsat_dtrbmasuk, $hrgjual_dtrbmasuk, $ttlharga, $no_batch, $exp_date, $id_dtrbmasuk]);

//update stok
    $cekstok = $db->prepare("SELECT * FROM barang WHERE id_barang = ?");
    $cekstok->execute([$id_barang]);
    $rst = $cekstok->fetch(PDO::FETCH_ASSOC);

    $stok_barang = $rst['stok_barang'];
    $stokakhir = (($stok_barang - $qtylama) + $ttlqty);

    $hrgjual_barang=round($hrgjual_dtrbmasuk) ;
    $hrgjual_barang1=round($hrgjual_barang*1.05) ;
    $hrgjual_barang3=round($hrgsat_dtrbmasuk*1.22,0) ;
    

    // mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE barang SET 
    //                                       stok_barang = '$stokakhir', 
    //                                       hna = '$hnasat_dtrbmasuk',
    //                                       hrgsat_barang = '$hrgsat_dtrbmasuk',
    //                                       hrgjual_barang='$hrgjual_barang',
    //                                       hrgjual_barang1='$hrgjual_barang1',
    //                                       hrgjual_barang3='$hrgjual_barang3'
    //                                       WHERE id_barang = '$id_barang'");
    $db->prepare("UPDATE barang SET stok_barang = ?, hna = ?, hrgsat_barang = ?, hrgjual_barang = ? WHERE id_barang = ?")->execute([$stokakhir, $hnasat_dtrbmasuk, $hrgsat_dtrbmasuk, $hrgjual_barang, $id_barang]);

}else{
    $faktordiskon = (1-($diskon/100));
    // $qty_retail = $qty_dtrbmasuk * $konversi;
    // $ttlharga = $qty_grosir * $hnasat_dtrbmasuk * $faktordiskon ;
    $ttlharga = $qty_dtrbmasuk * $hrgsat_dtrbmasuk * $faktordiskon ;
    // $qtygrosir_dtrbmasuk = $_POST['qtygrosir_dtrbmasuk'];
    $tipe = 1;
    
$cekstok = $db->prepare("SELECT * FROM barang WHERE id_barang = ?");
     $cekstok->execute([$id_barang]);
     $rst = $cekstok->fetch(PDO::FETCH_ASSOC);
    
    $db->prepare("INSERT INTO trbmasuk_detail (kd_trbmasuk, kd_orders, id_barang, kd_barang, nmbrg_dtrbmasuk, qty_dtrbmasuk, qty_grosir, sat_dtrbmasuk, satgrosir_dtrbmasuk, konversi, hnasat_dtrbmasuk, diskon, hrgsat_dtrbmasuk, hrgjual_dtrbmasuk, hrgttl_dtrbmasuk, no_batch, exp_date, tipe) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")->execute([$kd_trbmasuk, $kd_orders, $id_barang, $kd_barang, $nmbrg_dtrbmasuk, $qty_dtrbmasuk, $qtygrosir_dtrbmasuk, $sat_dtrbmasuk, $satgrosir_dtrbmasuk, $konversi, $hnasat_dtrbmasuk, $diskon, $hrgsat_dtrbmasuk, $hrgjual_dtrbmasuk, $ttlharga, $no_batch, $exp_date, $tipe]);

//update stok,hna,hrgbrg+ppn
   

    $stok_barang = $rst['stok_barang'];
    // $stokakhir = $stok_barang + ($qty_dtrbmasuk*$konversi);
    $stokakhir = $stok_barang + ($qty_dtrbmasuk);

    $hrgjual_barang=round($hrgjual_dtrbmasuk) ;
    $hrgjual_barang1=round($hrgjual_barang*1.05) ;
    $hrgjual_barang3=round($hrgsat_dtrbmasuk*1.22,0) ;
    

    // mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE barang SET 
    //                                             stok_barang = '$stokakhir',
    //                                             hna = '$hnasat_dtrbmasuk',
    //                                             konversi = '$konversi',
    //                                             hrgsat_barang = '$hrgsat_dtrbmasuk',
    //                                             hrgjual_barang='$hrgjual_barang',
    //                                             hrgjual_barang1='$hrgjual_barang1',
    //                                             hrgjual_barang3='$hrgjual_barang3'
    //                                             WHERE id_barang = '$id_barang'");
    mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE barang SET 
                                                stok_barang = '$stokakhir',
                                                hna = '$hnasat_dtrbmasuk',
                                                konversi = '$konversi',
                                                hrgsat_barang = '$hrgsat_dtrbmasuk',
                                                hrgjual_barang='$hrgjual_barang'
                                                WHERE id_barang = '$id_barang'");

}

?>
