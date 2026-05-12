<?php
include "../../../configurasi/koneksi.php";

$diskon                 = $_POST['diskon'];
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

if ($cari > 0) {
    // code...
    $id_dtrbmasuk   = $detail['id_dtrbmasuk'];
    $cekstok        = $db->prepare("SELECT * FROM barang 
                            WHERE id_barang = ?");
    $cekstok->execute([$detail['id_barang']]);
    $rsto           = $cekstok->fetch(PDO::FETCH_ASSOC);
    
    // $harga_satuan   = round((($detail['hnasat_dtrbmasuk'] * 1.11) * (1 - ($diskon/100))) / $detail['konversi']);
    // $total_harga    = $harga_satuan * $detail['qty_dtrbmasuk'];
    // $harga_satuan   = round($rsto['hna'] / $detail['konversi']);
    // $total_harga    = round(($rsto['hna'] * 1.11) * $detail['qty_grosir']) * (1 - ($diskon/100));
    
    // mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE trbmasuk_detail SET 
    //                                     diskon              = '$diskon',
				// 						hrgttl_dtrbmasuk    = '$total_harga'
				// 						WHERE id_dtrbmasuk  = '$id_dtrbmasuk'");
										
    $ceksql = $db->prepare("SELECT * FROM trbmasuk_detail 
                            WHERE kd_barang=? AND kd_trbmasuk=?");
    $ceksql->execute([$kd_barang, $kd_trbmasuk]);
    
    while($sq = $ceksql->fetch(PDO::FETCH_ASSOC)){
        // $harga_satuan   = round($rsto['hna'] / $sq['konversi']);
        $harga_satuan   = round(($rsto['hna'] / $sq['konversi']) * (1-($diskon/100)) * 1.11);
        $harga_grosir   = round($rsto['hna'] );
        $total_harga    = round(($rsto['hna'] * 1.11) * $sq['qty_grosir']) * (1 - ($diskon/100));
        
        $stmt_update_detail = $db->prepare("UPDATE trbmasuk_detail SET 
                                        diskon              = ?,
                                        hrgsat_dtrbmasuk    = ?,
										hrgttl_dtrbmasuk    = ?
										WHERE id_dtrbmasuk  = ?");
		$stmt_update_detail->execute([$diskon, $harga_satuan, $total_harga, $sq['id_dtrbmasuk']]);
		
		$stmt_update_barang = $db->prepare("UPDATE barang SET 
                                                hrgsat_barang   = ?,
                                                hrgsat_grosir   = ?
                                                WHERE id_barang = ?");
		$stmt_update_barang->execute([$harga_satuan, $harga_grosir, $detail['id_barang']]);
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
    $stokakhir      = $stok_barang + ($qty_dtrbmasuk);
    
    // $harga_satuan   = round((($odt['hnasat_dtrbmasuk'] * 1.11) * (1 - ($diskon/100))) / $odt['konversi']);
    // $total_harga    = (($odt['hnasat_dtrbmasuk'] * 1.11) * $odt['qtygrosir_dtrbmasuk']) * (1 - ($diskon/100));
    // $harga_satuan   = round($rst['hna'] / $odt['konversi']);
    $harga_satuan   = round(($rst['hna'] / $odt['konversi']) * (1-($diskon/100)) * 1.11);
    $total_harga    = round(($rst['hna'] * 1.11) * $_POST['qtygrosir_dtrbmasuk']) * (1 - ($odt['diskon']/100));
    
    $waktu          = date('Y-m-d H:i:s', time());
    
    $hrgjual_barang     = round($odt['hrgjual_dtrbmasuk']);
    $hrgjual_barang1    = round($odt['hrgjual_dtrbmasuk']*1.05);
    $hrgjual_barang3    = round($odt['hrgjual_dtrbmasuk']*1.22);
    
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
	$stmt_insert_detail->execute([$kd_trbmasuk, $kd_orders, $odt['id_barang'], $odt['kd_barang'], $odt['nmbrg_dtrbmasuk'], $qty_dtrbmasuk, $_POST['qtygrosir_dtrbmasuk'], $odt['sat_dtrbmasuk'], $odt['satgrosir_dtrbmasuk'], $odt['konversi'], $rst['hna'], $diskon, $harga_satuan, $hrgjual_barang, $total_harga, $odt['no_batch'], $odt['exp_date'], $waktu]);
										
	
}
?>