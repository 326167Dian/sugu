<?php
include "../../../configurasi/koneksi.php";

$exp_date               = date('Y-m-d', strtotime($_POST['exp_date']));
$kd_barang              = $_POST['kd_barang'];
$kd_trbmasuk            = $_POST['kd_trbmasuk'];
$kd_orders              = $_POST['kd_orders'];
$id_dtrbmasuk           = $_POST['id_dtrbmasuk'];

$trbmasuk = $db->prepare("SELECT * FROM trbmasuk_detail 
                            WHERE kd_barang=? 
                            AND kd_trbmasuk=? 
                            AND id_dtrbmasuk= ?");
$trbmasuk->execute([$kd_barang, $kd_trbmasuk, $id_dtrbmasuk]);
$detail = $trbmasuk->fetch(PDO::FETCH_ASSOC);
$cari   = $trbmasuk->rowCount();
// print_r($detail);
// die();
if ($cari > 0) {
    // code...
    // $id_dtrbmasuk   = $detail['id_dtrbmasuk'];
    
    $stmt_update_detail = $db->prepare("UPDATE trbmasuk_detail SET 
										exp_date            = ?
										WHERE id_dtrbmasuk  = ?");
	$stmt_update_detail->execute([$exp_date, $id_dtrbmasuk]);
	// Update batch
	$datetime = date('Y-m-d H:i:s', time());
	$caribatch = $db->prepare("SELECT * FROM batch 
                            WHERE kd_barang=? AND kd_transaksi=?");
    $caribatch->execute([$kd_barang, $kd_trbmasuk]);
    $ketemubatch = $caribatch->rowCount();
    if($ketemubatch > 0){
        $btc = $caribatch->fetch(PDO::FETCH_ASSOC);
        $stmt_update_batch = $db->prepare("UPDATE batch SET
                                                    exp_date    = ?
                                                    WHERE kd_barang     = ?
                                                    AND kd_transaksi    = ?");
        $stmt_update_batch->execute([$exp_date, $kd_barang, $kd_trbmasuk]);
    }									
}
else {
    $order  = $db->prepare("SELECT * FROM ordersdetail 
                            WHERE kd_barang=? AND kd_trbmasuk=?");
    $order->execute([$kd_barang, $kd_orders]);
    $odt    = $order->fetch(PDO::FETCH_ASSOC);
    
    $qty_dtrbmasuk  = $_POST['qtygrosir_dtrbmasuk'] * $odt['konversi'];
    
    // Update stok
    $cekstok        = $db->prepare("SELECT * FROM barang 
                        WHERE id_barang = ?");
    $cekstok->execute([$odt['id_barang']]);
    $rst            = $cekstok->fetch(PDO::FETCH_ASSOC);
    $stok_barang    = $rst['stok_barang'];
    $stokakhir      = $stok_barang + $qty_dtrbmasuk;
    
    // $harga_satuan   = round((($odt['hnasat_dtrbmasuk'] * 1.11) * (1-($odt['diskon']/100))) / $odt['konversi']);
    // $total_harga    = (($odt['hnasat_dtrbmasuk'] * 1.11) * $odt['qtygrosir_dtrbmasuk']) * (1 - ($odt['diskon']/100));
    // $harga_satuan   = round($rst['hna'] / $odt['konversi']);
    $harga_satuan   = round(($rst['hna'] / $odt['konversi']) * (1-($odt['diskon']/100)) * 1.11);
    $total_harga    = round(($rst['hna'] * 1.11) * $_POST['qtygrosir_dtrbmasuk']) * (1 - ($odt['diskon']/100));
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
    // UPDATE STOK ATOMIC - Menambah stok barang (barang baru)
    // Menggunakan single UPDATE statement untuk menghindari race condition
    $stmt_update_barang = $db->prepare("UPDATE barang SET 
                                                stok_barang     = stok_barang + ?,
                                                hrgsat_barang   = ?,
                                                hrgjual_barang  = ?
                                                WHERE id_barang = ?");
    $stmt_update_barang->execute([$qty_dtrbmasuk, $harga_satuan, $hrgjual_barang, $odt['id_barang']]);

    // Update order karena barang sudah masuk
    $stmt_update_orders = $db->prepare("UPDATE ordersdetail SET 
                                                masuk     = '0'
                                                WHERE id_dtrbmasuk = ?");
    $stmt_update_orders->execute([$odt['id_dtrbmasuk']]);
                                                
                                                
    // Insert trbmasuk detail
    $stmt_insert_detail = $db->prepare("INSERT INTO trbmasuk_detail(
                                        kd_trbmasuk,
                                        kd_orders,
										id_barang,
										kd_barang,
										nmbrg_dtrbmasuk,
										qty_dtrbmasuk,
										qty_grosir,
										sat_dtrbmasuk,
										satgrosir_dtrbmasuk,
										konversi,
										hnasat_dtrbmasuk,
										diskon,
										hrgsat_dtrbmasuk,										
										hrgjual_dtrbmasuk,										
										hrgttl_dtrbmasuk,
										no_batch,
										exp_date,
										waktu)
								  VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
	$stmt_insert_detail->execute([$kd_trbmasuk, $kd_orders, $odt['id_barang'], $odt['kd_barang'], $odt['nmbrg_dtrbmasuk'], $qty_dtrbmasuk, $_POST['qtygrosir_dtrbmasuk'], $odt['sat_dtrbmasuk'], $odt['satgrosir_dtrbmasuk'], $odt['konversi'], $rst['hna'], $odt['diskon'], $harga_satuan, $hrgjual_barang, $total_harga, $odt['no_batch'], $exp_date, $waktu]);
										
										
// 	// Insert batch
// 	$datetime = date('Y-m-d H:i:s', time());
//     $stmt_insert_batch = $db->prepare("INSERT INTO batch(
//                                         tgl_transaksi,
//                                         no_batch,
// 										exp_date,
// 										qty,
// 										satuan,
// 										kd_transaksi,
// 										kd_barang,
// 										status)
// 								  VALUES(?, ?, ?, ?, ?, ?, ?, ?)");
//     $stmt_insert_batch->execute([$datetime, $odt['no_batch'], $exp_date, $odt['qty_dtrbmasuk'], $odt['sat_dtrbmasuk'], $kd_trbmasuk, $odt['kd_barang'], 'masuk']);
}
?>