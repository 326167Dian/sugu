<?php 
include "../../../configurasi/koneksi.php";

$id_dtrbmasuk  = $_POST['id_dtrbmasuk'];

//ambil data
$ambildata = $db->prepare("SELECT * FROM trbmasuk_detail 
                            WHERE id_dtrbmasuk=?");
$ambildata->execute([$id_dtrbmasuk]);
$r = $ambildata->fetch(PDO::FETCH_ASSOC);

$id_barang      = $r['id_barang'];
$qty_dtrbmasuk  = $r['qty_dtrbmasuk'];
$kd_trbmasuk    = $r['kd_trbmasuk'];
$no_batch       = $r['no_batch'];


//update stok
$cekstok = $db->prepare("SELECT id_barang, stok_barang FROM barang 
                            WHERE id_barang=?");
$cekstok->execute([$id_barang]);
$rst = $cekstok->fetch(PDO::FETCH_ASSOC);

$stok_barang = $rst['stok_barang'];
$stokakhir = $stok_barang - $qty_dtrbmasuk;

// Update stok_barang
$stmt_update_barang = $db->prepare("UPDATE barang SET stok_barang = ? 
                                        WHERE id_barang = ?");
$stmt_update_barang->execute([$stokakhir, $id_barang]);

// Insert History Deleted
$stmt_insert_detailhist = $db->prepare("INSERT INTO trbmasuk_detail_hist (
                                                    kd_trbmasuk,
                                                    kd_orders,
                                                    id_barang,
                                                    kd_barang,
                                                    nmbrg_dtrbmasuk,
                                                    qty_dtrbmasuk,
                                                    sat_dtrbmasuk,
                                                    qty_grosir,
                                                    satgrosir_dtrbmasuk,
                                                    hnasat_dtrbmasuk,
                                                    diskon,
                                                    konversi,
                                                    hrgsat_dtrbmasuk,
                                                    hrgjual_dtrbmasuk,
                                                    hrgttl_dtrbmasuk,
                                                    no_batch,
                                                    exp_date,
                                                    waktu,
                                                    tipe
                                                    ) 
                                                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
$stmt_insert_detailhist->execute([$r['kd_trbmasuk'], $r['kd_orders'], $r['id_barang'], $r['kd_barang'], $r['nmbrg_dtrbmasuk'], $r['qty_dtrbmasuk'], $r['sat_dtrbmasuk'],$r['qty_grosir'], $r['satgrosir_dtrbmasuk'], $r['hnasat_dtrbmasuk'], $r['diskon'], $r['konversi'], $r['hrgsat_dtrbmasuk'], $r['hrgjual_dtrbmasuk'], $r['hrgttl_dtrbmasuk'],$r['no_batch'], $r['exp_date'], $r['waktu'], $r['tipe']]); 

// Hapus detail
$delete_trbmasukdetail= $db->prepare("DELETE FROM trbmasuk_detail WHERE id_dtrbmasuk = ?");
$delete_trbmasukdetail->execute([$id_dtrbmasuk]);
$delete_batch = $db->prepare("DELETE FROM batch 
                        WHERE kd_transaksi = ? 
                        AND no_batch = ? 
                        AND status = ?");
$delete_batch->execute([$kd_trbmasuk, $no_batch, 'masuk']);

?>
