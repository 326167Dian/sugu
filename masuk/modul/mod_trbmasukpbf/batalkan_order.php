<?php
include "../../../configurasi/koneksi.php";
include "../../../configurasi/fungsi_rupiah.php";
include "helper_subtotal.php";

// Membatalkan item pesanan yang belum diterima (masuk='1') supaya tidak lagi
// muncul sebagai baris pending yang bisa ke-terima ulang tanpa sengaja saat
// kolom lain di baris itu diedit. Beda dengan hapus di baris yang SUDAH
// diterima (yang mengembalikan status ke pending) -- ini sengaja tidak
// dikembalikan ke pending, karena barangnya memang tidak jadi dikirim.
$kd_barang   = $_POST['kd_barang'];
$kd_orders   = $_POST['kd_orders'];
$kd_trbmasuk = $_POST['kd_trbmasuk'];

header('Content-Type: application/json');

try {
    $db->beginTransaction();

    $stmt_order = $db->prepare("SELECT * FROM ordersdetail WHERE kd_barang = ? AND kd_trbmasuk = ?");
    $stmt_order->execute([$kd_barang, $kd_orders]);
    $odt = $stmt_order->fetch(PDO::FETCH_ASSOC);

    if (!$odt || $odt['masuk'] != '1') {
        throw new Exception('Item ini sudah diterima atau sudah dibatalkan. Silakan muat ulang halaman.');
    }

    $db->prepare("UPDATE ordersdetail SET masuk = '2' WHERE id_dtrbmasuk = ?")
        ->execute([$odt['id_dtrbmasuk']]);

    $subtotal = hitung_subtotal_pbf($db, $kd_trbmasuk);

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
