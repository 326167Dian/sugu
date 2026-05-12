<?php
include "../../../configurasi/koneksi.php";

$qtygrosir_dtrbmasuk    = $_POST['qtygrosir_dtrbmasuk'];
$kd_barang              = $_POST['kd_barang'];
$kd_trbmasuk            = $_POST['kd_trbmasuk'];
$kd_orders              = $_POST['kd_orders'];

$trbmasuk = $db->prepare("SELECT * FROM trbmasuk_detail WHERE kd_barang = ? AND kd_trbmasuk = ? AND id_dtrbmasuk = ?");
$trbmasuk->execute([$kd_barang, $kd_trbmasuk, $_POST['id_dtrbmasuk']]);
$detail = $trbmasuk->fetch(PDO::FETCH_ASSOC);
$cari = $trbmasuk->rowCount();

if ($cari > 0) {
    $id_dtrbmasuk   = $detail['id_dtrbmasuk'];
    $cekstok = $db->prepare("SELECT * FROM barang WHERE id_barang = ?");
    $cekstok->execute([$detail['id_barang']]);
    $rsto = $cekstok->fetch(PDO::FETCH_ASSOC);
    $stoko_barang   = $rsto['stok_barang'] - $detail['qty_dtrbmasuk'];
    
    
    $qty_dtrbmasuk  = $qtygrosir_dtrbmasuk * $detail['konversi'];
    // $harga_satuan   = round((($detail['hnasat_dtrbmasuk'] * 1.11) * (1 - ($detail['diskon']/100))) / $detail['konversi']);
    // $harga_satuan   = round($rsto['hna'] / $detail['konversi']);
    $harga_satuan   = round(($rsto['hna'] / $detail['konversi']) * (1-($detail['diskon']/100)) * 1.11);
    // $harga_grosir   = round(($detail['hnasat_dtrbmasuk'] * 1.11) * (1 - ($detail['diskon']/100)));
    $harga_grosir   = round($rsto['hna']);
    // $total_harga    = $harga_satuan * $qty_dtrbmasuk;
    $total_harga    = round(($rsto['hna'] * 1.11) * $qtygrosir_dtrbmasuk) * (1 - ($detail['diskon']/100));
    
	$stokakhir      = $stoko_barang + ($qty_dtrbmasuk);

    // mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE barang SET 
    //                                             stok_barang     = '$stokakhir'
    //                                             WHERE id_barang = '$detail[id_barang]'");
    $db->prepare("UPDATE barang SET hrgsat_barang = ?, stok_barang = ?, hrgsat_grosir = ? WHERE id_barang = ?")->execute([$harga_satuan, $stokakhir, $harga_grosir, $detail['id_barang']]);    
                                                
    $db->prepare("UPDATE trbmasuk_detail SET qty_grosir = ?, qty_dtrbmasuk = ?, hrgttl_dtrbmasuk = ? WHERE id_dtrbmasuk = ?")->execute([$qtygrosir_dtrbmasuk, $qty_dtrbmasuk, $total_harga, $id_dtrbmasuk]);									
}
else {
    $order = $db->prepare("SELECT * FROM ordersdetail WHERE kd_barang = ? AND kd_trbmasuk = ?");
    $order->execute([$kd_barang, $kd_orders]);
    $odt = $order->fetch(PDO::FETCH_ASSOC);
    $odt    = mysqli_fetch_array($order);
    
    // Update stok
    $cekstok = $db->prepare("SELECT * FROM barang WHERE id_barang = ?");
    $cekstok->execute([$odt['id_barang']]);
    $rst = $cekstok->fetch(PDO::FETCH_ASSOC);
    $stok_barang    = $rst['stok_barang'];
    
    $qty_dtrbmasuk  = $qtygrosir_dtrbmasuk * $odt['konversi'];
    $stokakhir      = $stok_barang + $qty_dtrbmasuk;
    // $harga_satuan   = round((($odt['hnasat_dtrbmasuk'] * 1.11) * (1-($odt['diskon']/100))) / $odt['konversi']);
    // $harga_satuan   = round($rst['hna'] / $odt['konversi']);
    $harga_satuan   = round(($rst['hna'] / $odt['konversi']) * (1-($odt['diskon']/100)) * 1.11);
    // $total_harga    = (($odt['hnasat_dtrbmasuk'] * 1.11) * $qtygrosir_dtrbmasuk) * (1 - ($odt['diskon']/100));
    $total_harga    = round(($rst['hna'] * 1.11) * $qtygrosir_dtrbmasuk) * (1 - ($odt['diskon']/100));
    $waktu          = date('Y-m-d H:i:s', time());
    
    
    $hrgjual_barang     = round($odt['hrgjual_dtrbmasuk']);
    $hrgjual_barang1    = round($odt['hrgjual_dtrbmasuk']*1.05);
    $hrgjual_barang3    = round($odt['hrgjual_dtrbmasuk']*1.22);
    
    // mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE barang SET 
    //                                             stok_barang     = '$stokakhir',
    //                                             hrgsat_barang   = '$odt[hrgsat_dtrbmasuk]',
    //                                             hrgjual_barang  = '$hrgjual_barang',
    //                                             hrgjual_barang1 = '$hrgjual_barang1',
    //                                             hrgjual_barang3 = '$hrgjual_barang3'
    //                                             WHERE id_barang = '$odt[id_barang]'");
    $db->prepare("UPDATE barang SET stok_barang = ?, hrgsat_barang = ?, hrgjual_barang = ? WHERE id_barang = ?")->execute([$stokakhir, $harga_satuan, $hrgjual_barang, $odt['id_barang']]);
    
    // Update order karena barang sudah masuk
    $db->prepare("UPDATE ordersdetail SET masuk = '0' WHERE id_dtrbmasuk = ?")->execute([$odt['id_dtrbmasuk']]);
                                                
    // Insert trbmasuk detail
    $db->prepare("INSERT INTO trbmasuk_detail (kd_trbmasuk, kd_orders, id_barang, kd_barang, nmbrg_dtrbmasuk, qty_dtrbmasuk, qty_grosir, sat_dtrbmasuk, satgrosir_dtrbmasuk, konversi, hnasat_dtrbmasuk, diskon, hrgsat_dtrbmasuk, hrgjual_dtrbmasuk, hrgttl_dtrbmasuk, no_batch, exp_date, waktu) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")->execute([$kd_trbmasuk, $kd_orders, $odt['id_barang'], $odt['kd_barang'], $odt['nmbrg_dtrbmasuk'], $qty_dtrbmasuk, $qtygrosir_dtrbmasuk, $odt['sat_dtrbmasuk'], $odt['satgrosir_dtrbmasuk'], $odt['konversi'], $rst['hna'], $odt['diskon'], $harga_satuan, $hrgjual_barang, $total_harga, $odt['no_batch'], $odt['exp_date'], $waktu]);
										
	

}
?>