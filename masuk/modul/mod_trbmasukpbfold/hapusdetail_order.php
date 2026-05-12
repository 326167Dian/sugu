<?php 
include "../../../configurasi/koneksi.php";

$id_dtrbmasuk  = $_POST['id_dtrbmasuk'];

// $trbmasuk = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM trbmasuk_detail WHERE kd_orders='$r[kd_trbmasuk]' AND id_barang='$r[id_barang]'");
$stmt = $db->prepare("SELECT * FROM trbmasuk_detail WHERE id_dtrbmasuk = ?");
$stmt->execute([$id_dtrbmasuk]);
$r1 = $stmt->fetch(PDO::FETCH_ASSOC);
$r1_num = $stmt->rowCount();

if ($r1_num > 0) {
    //update stok
    $stmt_stok = $db->prepare("SELECT id_barang, stok_barang, konversi FROM barang 
                                WHERE id_barang=?");
    $stmt_stok->execute([$r1['id_barang']]);
    $rst = $stmt_stok->fetch(PDO::FETCH_ASSOC);
    
    $stok_barang = $rst['stok_barang'];
    $stokakhir = $stok_barang - $r1['qty_dtrbmasuk'];
    
    $db->prepare("UPDATE barang SET stok_barang = ? WHERE id_barang = ?")->execute([$stokakhir, $r1['id_barang']]);
    $db->prepare("UPDATE ordersdetail SET masuk = '0' WHERE id_barang = ? AND kd_trbmasuk = ?")->execute([$r1['id_barang'], $r1['kd_orders']]);
    // Insert History Deleted
    $db->prepare("INSERT INTO trbmasuk_detail_hist (
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
                                                VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)")->execute([
                                                    $r1['kd_trbmasuk'],
                                                    $r1['kd_orders'],
                                                    $r1['id_barang'],
                                                    $r1['kd_barang'],
                                                    $r1['nmbrg_dtrbmasuk'],
                                                    $r1['qty_dtrbmasuk'],
                                                    $r1['sat_dtrbmasuk'],
                                                    $r1['qty_grosir'],
                                                    $r1['satgrosir_dtrbmasuk'],
                                                    $r1['hnasat_dtrbmasuk'],
                                                    $r1['diskon'],
                                                    $r1['konversi'],
                                                    $r1['hrgsat_dtrbmasuk'],
                                                    $r1['hrgjual_dtrbmasuk'],
                                                    $r1['hrgttl_dtrbmasuk'],
                                                    $r1['no_batch'],
                                                    $r1['exp_date'],
                                                    $r1['waktu'],
                                                    $r1['tipe']
                                                    ]);
    
    $stmt_hapusdetail = $db->prepare("DELETE FROM trbmasuk_detail WHERE id_dtrbmasuk = ?");
    $stmt_hapusdetail->execute([$id_dtrbmasuk]);
} else {
    $stmt_update = $db->prepare("UPDATE ordersdetail SET masuk = '0' WHERE id_dtrbmasuk = ?");
    $stmt_update->execute([$id_dtrbmasuk]);
}
 

?>
