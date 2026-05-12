<?php
include "../../../configurasi/koneksi.php";

$kd_trbmasuk            = $_POST['kd_trbmasuk'];
$id_barang              = $_POST['id_barang'];
$kd_barang              = $_POST['kd_barang'];
$nmbrg_dtrbmasuk        = $_POST['nmbrg_dtrbmasuk'];
$qty_dtrbmasuk          = $_POST['qty_dtrbmasuk'];
$sat_dtrbmasuk          = $_POST['sat_dtrbmasuk'];
$hrgsat_dtrbmasuk       = $_POST['hrgsat_dtrbmasuk'];
$satgrosir_dtrbmasuk    = $_POST['satgrosir_dtrbmasuk'];
$qtygrosir_dtrbmasuk    = $_POST['qtygrosir_dtrbmasuk'];
$konversi               = $_POST['konversi'];

if ($qty_dtrbmasuk == "") {
	$qty_dtrbmasuk = "1";
} else {
}

//cek apakah barang sudah ada
$cekdetail = $db->prepare("SELECT * 
FROM ordersdetail 
WHERE kd_barang=? AND kd_trbmasuk=?");

$cekdetail->execute([$kd_barang, $kd_trbmasuk]);
$ketemucekdetail = $cekdetail->rowCount();
$rcek = $cekdetail->fetch(PDO::FETCH_ASSOC);
if ($ketemucekdetail > 0) {

	$id_dtrbmasuk = $rcek['id_dtrbmasuk'];
	$qtylama = $rcek['qty_dtrbmasuk'];
	$ttlqty = $qtylama + $qty_dtrbmasuk;
	$ttlharga = $ttlqty * $hrgsat_dtrbmasuk;
	$qtygrosirlama =  $rcek['qtygrosir_dtrbmasuk'];
	$qtygrosirbaru = $qtygrosirlama + $qtygrosir_dtrbmasuk ;

	$db->prepare("UPDATE ordersdetail SET qty_dtrbmasuk = ?,
										hrgsat_dtrbmasuk = ?,
										hrgttl_dtrbmasuk = ?,
										satgrosir_dtrbmasuk = ?,
										qtygrosir_dtrbmasuk = ?
										WHERE id_dtrbmasuk = ?")->execute([
											$ttlqty,
											$hrgsat_dtrbmasuk,
											$ttlharga,
											$satgrosir_dtrbmasuk,
											$qtygrosirbaru,
											$id_dtrbmasuk
										]);

	//update stok
	$cekstok = $db->prepare("SELECT id_barang, stok_barang FROM barang 
        WHERE id_barang=?");
	$cekstok->execute([$id_barang]);
	$rst = $cekstok->fetch(PDO::FETCH_ASSOC);

	$stok_barang = $rst['stok_barang'];
	$stokakhir = $stok_barang;

	$stmt_update_barang = $db->prepare("UPDATE barang SET stok_barang = ? WHERE id_barang = ?");
	$stmt_update_barang->execute([$stokakhir, $id_barang]);
} else {

    $cekstok = $db->prepare("SELECT * FROM barang 
        WHERE id_barang=?");
	$cekstok->execute([$id_barang]);
	$rst = $cekstok->fetch(PDO::FETCH_ASSOC);
	
	$ttlharga = $qty_dtrbmasuk * $hrgsat_dtrbmasuk;
// 	$ttlharga = $rst['hna'] * $qtygrosir_dtrbmasuk;

	$stmt_insert_ordersdetail = $db->prepare("INSERT INTO ordersdetail(kd_trbmasuk,
										id_barang,
										kd_barang,
										nmbrg_dtrbmasuk,
										qty_dtrbmasuk,
										sat_dtrbmasuk,
										konversi,
										hrgsat_dtrbmasuk,
										hrgttl_dtrbmasuk,
										hrgjual_dtrbmasuk,
										satgrosir_dtrbmasuk,
										qtygrosir_dtrbmasuk,
										hnasat_dtrbmasuk)
								  VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
	$stmt_insert_ordersdetail->execute([$kd_trbmasuk, $id_barang, $kd_barang, $nmbrg_dtrbmasuk, $qty_dtrbmasuk, $sat_dtrbmasuk, $konversi, $hrgsat_dtrbmasuk, $ttlharga, $rst['hrgjual_barang'], $satgrosir_dtrbmasuk, $qtygrosir_dtrbmasuk, $rst['hna']]);

	//update stok
	$stok_barang = $rst['stok_barang'];
	$stokakhir = $stok_barang;

	$stmt_update_barang2 = $db->prepare("UPDATE barang 
	                                          SET stok_barang = ?,
	                                                 konversi = ?,
	                                                 sat_grosir = ?
	                                          WHERE id_barang = ?");
	$stmt_update_barang2->execute([$stokakhir, $konversi, $satgrosir_dtrbmasuk, $id_barang]);
}
