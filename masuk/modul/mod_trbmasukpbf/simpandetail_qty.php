<?php
include "../../../configurasi/koneksi.php";

$id_dtrbmasuk = isset($_POST['id_dtrbmasuk']) ? (int) $_POST['id_dtrbmasuk'] : 0;
$qty_input = isset($_POST['qty_dtrbmasuk']) ? str_replace(',', '.', trim($_POST['qty_dtrbmasuk'])) : '';

if ($id_dtrbmasuk <= 0 || $qty_input === '' || !is_numeric($qty_input) || (float) $qty_input <= 0) {
    http_response_code(400);
    exit('Qty tidak valid');
}

$qty_dtrbmasuk = (float) $qty_input;

try {
    $db->beginTransaction();

    $stmt_detail = $db->prepare("SELECT id_dtrbmasuk, id_barang, kd_barang, kd_trbmasuk, qty_dtrbmasuk, konversi, qty_grosir, hnasat_dtrbmasuk, diskon, no_batch
                                 FROM trbmasuk_detail
                                 WHERE id_dtrbmasuk = ?");
    $stmt_detail->execute([$id_dtrbmasuk]);
    $detail = $stmt_detail->fetch(PDO::FETCH_ASSOC);

    if (!$detail) {
        throw new Exception('Data detail tidak ditemukan');
    }

    $qty_lama = (float) $detail['qty_dtrbmasuk'];
    $konversi = (float) $detail['konversi'];
    $qty_grosir = ($konversi > 0) ? ($qty_dtrbmasuk / $konversi) : $qty_dtrbmasuk;
    $faktordiskon = 1 - (((float) $detail['diskon']) / 100);
    $hrgttl_dtrbmasuk = round($qty_grosir * (float) $detail['hnasat_dtrbmasuk'] * $faktordiskon * 1.11, 0);
    $selisih_qty = $qty_dtrbmasuk - $qty_lama;

    $stmt_update_detail = $db->prepare("UPDATE trbmasuk_detail
                                        SET qty_dtrbmasuk = ?,
                                            qty_grosir = ?,
                                            hrgttl_dtrbmasuk = ?
                                        WHERE id_dtrbmasuk = ?");
    $stmt_update_detail->execute([$qty_dtrbmasuk, $qty_grosir, $hrgttl_dtrbmasuk, $id_dtrbmasuk]);

    $stmt_update_barang = $db->prepare("UPDATE barang
                                        SET stok_barang = stok_barang + ?
                                        WHERE id_barang = ?");
    $stmt_update_barang->execute([$selisih_qty, $detail['id_barang']]);

    if (!empty($detail['no_batch'])) {
        $stmt_update_batch = $db->prepare("UPDATE batch
                                           SET qty = ?
                                           WHERE kd_transaksi = ?
                                             AND kd_barang = ?
                                             AND no_batch = ?
                                             AND status = ?");
        $stmt_update_batch->execute([
            $qty_dtrbmasuk,
            $detail['kd_trbmasuk'],
            $detail['kd_barang'],
            $detail['no_batch'],
            'masuk'
        ]);
    }

    $db->commit();
    echo 'OK';
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }

    http_response_code(500);
    echo $e->getMessage();
}
