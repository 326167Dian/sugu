<?php
session_start();
include "../../../configurasi/koneksi.php";
include "../../../configurasi/fungsi_perubahan_trkasir.php";

$kd_bundle  = $_POST['kd_bundle'];

// DDL (ALTER/CREATE TABLE) menyebabkan implicit commit di MySQL, jadi harus
// dijalankan SEBELUM beginTransaction() supaya tidak menutup transaction secara diam-diam.
pastikan_skema_perubahan_trkasir($db);

try {
    $db->beginTransaction();

$ambildata=$db->prepare("SELECT * FROM trkasir_detail
                        WHERE kd_bundle = ?");
$ambildata->execute([$kd_bundle]);

$qty_bundle = 0;
$sudahFinal = null;
$tipetxBaru = null;
$idAdminHapus = isset($_SESSION['idadmin']) ? $_SESSION['idadmin'] : null;
while($r = $ambildata->fetch(PDO::FETCH_ASSOC)){
    $id_barang      = $r['id_barang'];
    $qty_dtrkasir   = $r['qty_dtrkasir'];
    $kd_trbmasuk    = $r['kd_trkasir'];
    $no_batch       = $r['no_batch'];
    $kd_barang      = $r['kd_barang'];
    $kd_bundle      = $r['kd_bundle'];

    if ($sudahFinal === null) {
        $sudahFinal = apakah_transaksi_final($db, $kd_trbmasuk);
        if ($sudahFinal) {
            $tipetxBaru = catat_revisi_transaksi($db, $kd_trbmasuk);
        }
    }
    $tipetxAsal = isset($r['tipetx']) ? $r['tipetx'] : 1;
    $tipetxHapus = $sudahFinal ? $tipetxBaru : $tipetxAsal;

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
                                        nm_bundle,
                                        tipetx_asal,
                                        tipetx_hapus,
                                        id_admin_hapus)
                                    VALUE(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
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
        $r['no_batch'] !== null ? $r['no_batch'] : '',
        $r['exp_date'],
        $r['waktu'],
        $r['tipe'],
        $r['komisi'],
        $r['idadmin'],
        $r['kd_bundle'],
        $r['nm_bundle'],
        $tipetxAsal,
        $tipetxHapus,
        $idAdminHapus
    ]);
    
    $get_bundle_detail = $db->prepare("SELECT * FROM bundle_detail
                                        WHERE kd_bundle = ?
                                        AND kd_barang = ?");
    $get_bundle_detail->execute([$kd_bundle, $kd_barang]);
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

    $db->commit();

    echo json_encode(['status' => 'success']);
} catch (Throwable $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
?>