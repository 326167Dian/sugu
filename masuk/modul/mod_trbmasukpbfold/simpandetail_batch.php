<?php
include "../../../configurasi/koneksi.php";

$no_batch       = $_POST['no_batch'];
$kd_barang      = $_POST['kd_barang'];
$kd_trbmasuk    = $_POST['kd_trbmasuk'];
$kd_orders      = $_POST['kd_orders'];

$stmt = $db->prepare("SELECT * FROM trbmasuk_detail 
                            WHERE kd_barang=? AND kd_trbmasuk=? AND id_dtrbmasuk=?");
$stmt->execute([$kd_barang, $kd_trbmasuk, $_POST['id_dtrbmasuk']]);
$detail = $stmt->fetch(PDO::FETCH_ASSOC);
$cari = $stmt->rowCount();

// echo "Kode Barang = ".$kd_barang."\nKode Orders = ".$kd_orders."\nKode Barang Masuk = ".$kd_trbmasuk."\nBatch = ".$no_batch;
// die();

if ($cari > 0) {
    // code...
    $id_dtrbmasuk   = $detail['id_dtrbmasuk'];
    $db->prepare("UPDATE trbmasuk_detail SET 
										no_batch = ?
										WHERE id_dtrbmasuk = ?")->execute([$no_batch, $id_dtrbmasuk]);
										
	// Update batch
	$datetime = date('Y-m-d H:i:s', time());
	$stmt_batch = $db->prepare("SELECT * FROM batch 
                            WHERE kd_barang=? AND kd_transaksi=?");
    $stmt_batch->execute([$kd_barang, $kd_trbmasuk]);
    $ketemubatch = $stmt_batch->rowCount();
    if($ketemubatch > 0){
        $btc = $stmt_batch->fetch(PDO::FETCH_ASSOC);
        $db->prepare("UPDATE batch SET
                                                    no_batch = ?
                                                    WHERE kd_barang = ?
                                                    AND kd_transaksi = ?")->execute([$no_batch, $kd_barang, $kd_trbmasuk]);
    }
    									
}
else {
    $stmt_order = $db->prepare("SELECT * FROM ordersdetail 
                            WHERE kd_barang=? AND kd_trbmasuk=?");
    $stmt_order->execute([$kd_barang, $kd_orders]);
    $odt = $stmt_order->fetch(PDO::FETCH_ASSOC);
    
    $qty_dtrbmasuk  = $_POST['qtygrosir_dtrbmasuk'] * $odt['konversi'];
    
    // Update stok
    $stmt_stok = $db->prepare("SELECT * FROM barang 
                        WHERE id_barang = ?");
    $stmt_stok->execute([$odt['id_barang']]);
    $rst = $stmt_stok->fetch(PDO::FETCH_ASSOC);
    $stok_barang    = $rst['stok_barang'];
    // $stokakhir      = $stok_barang + $odt['qty_dtrbmasuk'];
    $stokakhir      = $stok_barang + $qty_dtrbmasuk;
    
    // $harga_satuan   = round((($rst['hna'] * 1.11) * (1-($odt['diskon']/100))) / $odt['konversi']);
    $harga_satuan   = round(($rst['hna'] / $odt['konversi']) * (1-($odt['diskon']/100)) * 1.11);
    // $total_harga    = round(($rst['hna'] * 1.11) * $odt['qtygrosir_dtrbmasuk']) * (1 - ($odt['diskon']/100));
    $total_harga    = round(($rst['hna'] * 1.11) * $_POST['qtygrosir_dtrbmasuk']) * (1 - ($odt['diskon']/100));
    $waktu          = date('Y-m-d H:i:s', time());
    
    
    $hrgjual_barang     = round($odt['hrgjual_dtrbmasuk']);
    $hrgjual_barang1    = round($odt['hrgjual_dtrbmasuk']*1.05);
    $hrgjual_barang3    = round($odt['hrgjual_dtrbmasuk']*1.22);
    
    
    $db->prepare("UPDATE barang SET 
                                                stok_barang = ?,
                                                hrgsat_barang = ?,
                                                hrgjual_barang = ?
                                                WHERE id_barang = ?")->execute([$stokakhir, $harga_satuan, $hrgjual_barang, $odt['id_barang']]);
    
    // Update order karena barang sudah masuk
    $db->prepare("UPDATE ordersdetail SET 
                                                masuk = '0'
                                                WHERE id_dtrbmasuk = ?")->execute([$odt['id_dtrbmasuk']]);
                                                
    // Insert trbmasuk detail
    $db->prepare("INSERT INTO trbmasuk_detail(
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
								  VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)")->execute([
								        $kd_trbmasuk,
								        $kd_orders,
										$odt['id_barang'],
										$odt['kd_barang'],
										$odt['nmbrg_dtrbmasuk'],
										$odt['qty_dtrbmasuk'],
										$_POST['qtygrosir_dtrbmasuk'],
										$odt['sat_dtrbmasuk'],
										$odt['satgrosir_dtrbmasuk'],
										$odt['konversi'],
										$rst['hna'],
										$odt['diskon'],
										$harga_satuan,
										$hrgjual_barang,
										$total_harga,
										$no_batch,
										$odt['exp_date'],
										$waktu
										]);
										
	// Insert batch
	$datetime = date('Y-m-d H:i:s', time());
	$getbatch = $db->prepare("SELECT * FROM batch 
	                            WHERE kd_transaksi =?
	                            AND no_batch =?")->execute([$kd_trbmasuk, $no_batch]);
	$countbatch = $getbatch->rowCount();
	$rowbatch   = $getbatch->fetch(PDO::FETCH_ASSOC);
	if ($countbatch > 0) {
	   $qtyoldbatch = $rowbatch['qty'];
	   $ttlqtybatch = $qtyoldbatch + $odt['qty_dtrbmasuk'];
	   
	   $db->prepare("UPDATE batch SET qty =?
	                WHERE kd_transaksi =?
	                AND no_batch =?")->execute([$ttlqtybatch, $kd_trbmasuk, $no_batch]);
	} else {
	    
        $db->prepare("INSERT INTO batch(
                                            tgl_transaksi,
                                            no_batch,
    										exp_date,
    										qty,
    										satuan,
    										kd_transaksi,
    										kd_barang,
    										status)
    								  VALUES(?,?,?,?,?,?,?,?)")->execute([
    								        $datetime,
    								        $no_batch,
    										$odt['exp_date'],
    										$odt['qty_dtrbmasuk'],
    										$odt['sat_dtrbmasuk'],
    										$kd_trbmasuk,
    										$odt['kd_barang'],
    										'masuk'
    										]);
       
	}
}
?>