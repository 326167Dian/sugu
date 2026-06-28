<?php
session_start();
include "../../../configurasi/koneksi.php";

$kd_bundle  = $_POST['kd_bundle'];
$ambildata=$db->prepare("SELECT * FROM trkasir_detail 
                        WHERE kd_bundle = ?");
$ambildata->execute([$kd_bundle]);

// print_r($ambildata->fetch(PDO::FETCH_ASSOC));
// die
$qty_bundle = 0;
while($r = $ambildata->fetch(PDO::FETCH_ASSOC)){
    $id_barang      = $r['id_barang'];
    $qty_dtrkasir   = $r['qty_dtrkasir'];
    $kd_trbmasuk    = $r['kd_trkasir'];
    $no_batch       = $r['no_batch'];
    $kd_barang      = $r['kd_barang'];
    $kd_bundle      = $r['kd_bundle'];
    
    // Insert History
    $insert_history = $db->prepare("INSERT INTO trkasir_detail_hist(
                                        kd_trkasir,
                                        id_barang,
                                        kd_barang,
                                        nmbrg_dtrkasir,
                                        qty_dtrkasir,
                                        sat_dtrkasir,
                                        hrgjual_dtrkasir,
                                        disc,
                                        modal,
                                        profit,
                                        resep,
                                        hrgttl_dtrkasir,
                                        no_batch,
                                        exp_date,
                                        waktu,
                                        tipe,
                                        komisi,
                                        idadmin,
                                        kd_bundle,
                                        nm_bundle)
                                    VALUE(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
    $insert_history->execute([
        $r['kd_trkasir'],
        $r['id_barang'],
        $r['kd_barang'],
        $r['nmbrg_dtrkasir'],
        $r['qty_dtrkasir'],
        $r['sat_dtrkasir'],
        $r['hrgjual_dtrkasir'],
        $r['disc'],
        $r['modal'],
        $r['profit'],
        $r['resep'],
        $r['hrgttl_dtrkasir'],
        $r['no_batch'],
        $r['exp_date'],
        $r['waktu'],
        $r['tipe'],
        $r['komisi'],
        $r['idadmin'],
        $r['kd_bundle'],
        $r['nm_bundle']
    ]);
    
    $get_bundle_detail = $db->prepare("SELECT * FROM bundle_detail
                                        WHERE kd_bundle = ?
                                        AND id_barang = ?
                                        AND kd_barang = ?");
    $get_bundle_detail->execute([$kd_bundle, $id_barang, $kd_barang]);
    $rbundle = $get_bundle_detail->fetch(PDO::FETCH_ASSOC);
    
    // Update Stok Barang
    $update_stok_barang = $db->prepare("UPDATE barang SET
                                    stok_barang = stok_barang + :qty_dikembalikan
                                    WHERE kd_barang = :kd_barang");
    $update_stok_barang->execute([
        ':qty_dikembalikan' => ($qty_dtrkasir),
        ':kd_barang'        => $rbundle['kd_barang']
    ]);
    
    // Delete Stok Batch
    $delete_batch = $db->prepare("DELETE FROM batch WHERE
                                    kd_barang = :kd_barang  AND
                                    kd_transaksi = :kd_transaksi AND
                                    status = :status");
    $delete_batch->execute([
        ':kd_barang'    => $rbundle['kd_barang'],
        ':kd_transaksi' => $kd_trbmasuk,
        ':status'       => 'keluar'
    ]);
    
    $qty_bundle = $qty_dtrkasir / $rbundle['qty_barang'];
    
    // Delete Trkasir Detail
    $deletetrkasir = $db->prepare("DELETE FROM trkasir_detail WHERE id_dtrkasir = ?");
    $deletetrkasir->execute([$r['id_dtrkasir']]);
}

$updatebundle = $db->prepare("UPDATE bundle SET qty_bundle = qty_bundle + :qty_bundle
                                WHERE kd_bundle = :kd_bundle");
$updatebundle->execute([
    ':qty_bundle'   => $qty_bundle, 
    ':kd_bundle'    => $kd_bundle
    ]);
?>