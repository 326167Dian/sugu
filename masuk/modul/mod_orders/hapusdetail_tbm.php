<?php 
include "../../../configurasi/koneksi.php";

$id_dtrbmasuk  = $_POST['id_dtrbmasuk'];

//ambil data
$ambildata = $db->prepare("SELECT * FROM ordersdetail WHERE id_dtrbmasuk = ?");
$ambildata->execute([$id_dtrbmasuk]);
$r = $ambildata->fetch(PDO::FETCH_ASSOC);

$id_barang = $r['id_barang'];
$qty_dtrbmasuk = $r['qty_dtrbmasuk'];

//update stok
$cekstok = $db->prepare("SELECT id_barang, stok_barang FROM barang WHERE id_barang = ?");
$cekstok->execute([$id_barang]);
$rst = $cekstok->fetch(PDO::FETCH_ASSOC);

$stok_barang = $rst['stok_barang'];
$stokakhir = $stok_barang ;

// Update stok
$db->prepare("UPDATE barang SET stok_barang = ? WHERE id_barang = ?")->execute([$stokakhir, $id_barang]);

// Insert history
$db->prepare("INSERT INTO ordersdetail_hist(
                                            kd_trbmasuk,
                                            id_barang,
                                            kd_barang,
                                            nmbrg_dtrbmasuk,
                                            qty_dtrbmasuk,
                                            sat_dtrbmasuk,
                                            hnasat_dtrbmasuk,
                                            diskon,
                                            konversi,
                                            hrgsat_dtrbmasuk,
                                            hrgjual_dtrbmasuk,
                                            hrgttl_dtrbmasuk,
                                            qtygrosir_dtrbmasuk,
                                            satgrosir_dtrbmasuk,
                                            no_batch,
                                            exp_date,
                                            masuk
                                            )
                                        VALUES (
                                            ?,
                                            ?,
                                            ?,
                                            ?,
                                            ?,
                                            ?,
                                            ?,
                                            ?,
                                            ?,
                                            ?,
                                            ?,
                                            ?,
                                            ?,
                                            ?,
                                            ?,
                                            ?,
                                            ?
                                            )")->execute([$r['kd_trbmasuk'], $r['id_barang'], $r['kd_barang'], $r['nmbrg_dtrbmasuk'], $r['qty_dtrbmasuk'], $r['sat_dtrbmasuk'], $r['hnasat_dtrbmasuk'], $r['diskon'], $r['konversi'], $r['hrgsat_dtrbmasuk'], $r['hrgjual_dtrbmasuk'], $r['hrgttl_dtrbmasuk'], $r['qtygrosir_dtrbmasuk'], $r['satgrosir_dtrbmasuk'], $r['no_batch'], $r['exp_date'], $r['masuk']]);

// Hapus
$db->prepare("DELETE FROM ordersdetail WHERE id_dtrbmasuk = ?")->execute([$id_dtrbmasuk]);

?>
