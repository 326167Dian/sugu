<?php 
include "../../../configurasi/koneksi.php";

$id_dtrbmasuk  = $_POST['id_dtrbmasuk'];

//ambil data
$stmt = $db->prepare("SELECT * FROM trbmasuk_detail WHERE id_dtrbmasuk=?");
$stmt->execute([$id_dtrbmasuk]);
$r = $stmt->fetch(PDO::FETCH_ASSOC);

$id_barang = $r['id_barang'];
$qty_dtrbmasuk = $r['qty_dtrbmasuk'];

//update stok
$stmt_stok = $db->prepare("SELECT id_barang, stok_barang FROM barang 
WHERE id_barang=?");
$stmt_stok->execute([$id_barang]);
$rst = $stmt_stok->fetch(PDO::FETCH_ASSOC);

$stok_barang = $rst['stok_barang'];
$stokakhir = $stok_barang - $qty_dtrbmasuk;

$db->prepare("UPDATE barang SET stok_barang = ? WHERE id_barang = ?")->execute([$stokakhir, $id_barang]);
$db->prepare("DELETE FROM trbmasuk_detail WHERE id_dtrbmasuk = ?")->execute([$id_dtrbmasuk]);

?>
