<?php
include "../../../configurasi/koneksi.php";
include "../../../configurasi/fungsi_rupiah.php";
include "helper_subtotal_order.php";

$id_dtrbmasuk = $_POST['id_dtrbmasuk'];
$kd_orders    = $_POST['kd_orders'];
$kd_trbmasuk  = $_POST['kd_trbmasuk'];

header('Content-Type: application/json');

try {
    $db->beginTransaction();

    $stmt = $db->prepare("SELECT * FROM trbmasuk_detail WHERE id_dtrbmasuk = ? AND kd_orders = ?");
    $stmt->execute([$id_dtrbmasuk, $kd_orders]);
    $r1 = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($stmt->rowCount() > 0) {
        $db->prepare("UPDATE barang SET stok_barang = stok_barang - ? WHERE id_barang = ?")
            ->execute([$r1['qty_dtrbmasuk'], $r1['id_barang']]);

        $db->prepare("UPDATE ordersdetail SET masuk = '1' WHERE id_barang = ? AND kd_trbmasuk = ?")
            ->execute([$r1['id_barang'], $r1['kd_orders']]);

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
                                    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)")
            ->execute([
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

        $db->prepare("DELETE FROM trbmasuk_detail WHERE id_dtrbmasuk = ?")
            ->execute([$id_dtrbmasuk]);

        $db->prepare("DELETE FROM batch WHERE kd_transaksi = ? AND kd_barang = ? AND no_batch = ?")
            ->execute([$r1['kd_trbmasuk'], $r1['kd_barang'], $r1['no_batch']]);
    } else {
        $db->prepare("UPDATE ordersdetail SET masuk = '0' WHERE id_dtrbmasuk = ? AND kd_trbmasuk = ?")
            ->execute([$id_dtrbmasuk, $kd_orders]);
    }

    $subtotal = hitung_subtotal_order($db, $kd_trbmasuk, $kd_orders);

    $db->commit();

    echo json_encode([
        'status'   => 'ok',
        'subtotal' => format_rupiah($subtotal)
    ]);
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
