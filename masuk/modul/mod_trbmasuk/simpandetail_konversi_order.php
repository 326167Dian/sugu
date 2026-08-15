<?php
include "../../../configurasi/koneksi.php";
include "../../../configurasi/fungsi_rupiah.php";
include "helper_subtotal_order.php";

$konversi            = $_POST['konversi'];
$kd_barang           = $_POST['kd_barang'];
$kd_trbmasuk         = $_POST['kd_trbmasuk'];
$kd_orders           = $_POST['kd_orders'];
$id_dtrbmasuk        = $_POST['id_dtrbmasuk'];
$qtygrosir_dtrbmasuk = $_POST['qtygrosir_dtrbmasuk'];

header('Content-Type: application/json');

try {
    $db->beginTransaction();

    // Konversi menentukan qty_dtrbmasuk (qty satuan eceran) yang dipakai untuk stok,
    // jadi bisa berbeda dari konversi saat pesan barang (mis. saat pesan 1 box = 10 strip,
    // tapi barang yang benar-benar datang 1 box = 12 strip).
    $trbmasuk = $db->prepare("SELECT * FROM trbmasuk_detail WHERE kd_barang = ? AND kd_trbmasuk = ?");
    $trbmasuk->execute([$kd_barang, $kd_trbmasuk]);
    $detail = $trbmasuk->fetch(PDO::FETCH_ASSOC);

    if ($trbmasuk->rowCount() > 0) {
        $qty_dtrbmasuk = $konversi * $detail['qty_grosir'];
        $selisih_qty   = $qty_dtrbmasuk - $detail['qty_dtrbmasuk'];

        $db->prepare("UPDATE trbmasuk_detail SET konversi = ?, qty_dtrbmasuk = ? WHERE id_dtrbmasuk = ?")
            ->execute([$konversi, $qty_dtrbmasuk, $detail['id_dtrbmasuk']]);

        $db->prepare("UPDATE barang SET stok_barang = stok_barang + ? WHERE id_barang = ?")
            ->execute([$selisih_qty, $detail['id_barang']]);

        // sinkronkan harga referensi barang (konversi baru mengubah harga per satuan eceran)
        $db->prepare("UPDATE barang SET hrgsat_barang = ?, hrgsat_grosir = ? WHERE kd_barang = ?")
            ->execute([$detail['hrgsat_dtrbmasuk'] / $konversi, $detail['hrgsat_dtrbmasuk'], $kd_barang]);

        $id_dtrbmasuk_final = $detail['id_dtrbmasuk'];
    } else {
        $order = $db->prepare("SELECT * FROM ordersdetail WHERE kd_barang = ? AND kd_trbmasuk = ? AND id_dtrbmasuk = ?");
        $order->execute([$kd_barang, $kd_orders, $id_dtrbmasuk]);
        $odt = $order->fetch(PDO::FETCH_ASSOC);

        if (!$odt || $odt['masuk'] != '1') {
            throw new Exception('Item sudah diterima pada transaksi lain. Silakan muat ulang halaman.');
        }

        $qty_dtrbmasuk = $konversi * $qtygrosir_dtrbmasuk;
        $hrgbelidisc   = $odt['hrgsat_dtrbmasuk'] * (1 - ($odt['diskon'] / 100));
        $hrgttl        = round($hrgbelidisc * $qtygrosir_dtrbmasuk);
        $waktu         = date('Y-m-d H:i:s', time());
        $hnasat_dtrbmasuk = round($odt['hrgsat_dtrbmasuk'] / 1.11);

        $db->prepare("INSERT INTO trbmasuk_detail (kd_trbmasuk, kd_orders, id_barang, kd_barang, nmbrg_dtrbmasuk, qty_dtrbmasuk, qty_grosir, sat_dtrbmasuk, satgrosir_dtrbmasuk, konversi, hnasat_dtrbmasuk, diskon, hrgsat_dtrbmasuk, hrgjual_dtrbmasuk, hrgttl_dtrbmasuk, no_batch, exp_date, waktu) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")
            ->execute([$kd_trbmasuk, $kd_orders, $odt['id_barang'], $odt['kd_barang'], $odt['nmbrg_dtrbmasuk'], $qty_dtrbmasuk, $qtygrosir_dtrbmasuk, $odt['sat_dtrbmasuk'], $odt['satgrosir_dtrbmasuk'], $konversi, $hnasat_dtrbmasuk, $odt['diskon'], $odt['hrgsat_dtrbmasuk'], $odt['hrgjual_dtrbmasuk'], $hrgttl, $odt['no_batch'], $odt['exp_date'], $waktu]);

        $id_dtrbmasuk_final = $db->lastInsertId();

        $db->prepare("UPDATE barang SET stok_barang = stok_barang + ? WHERE id_barang = ?")
            ->execute([$qty_dtrbmasuk, $odt['id_barang']]);

        // sinkronkan harga referensi barang dengan harga beli terima barang ini (pakai konversi baru)
        $db->prepare("UPDATE barang SET hrgsat_barang = ?, hrgsat_grosir = ? WHERE kd_barang = ?")
            ->execute([$odt['hrgsat_dtrbmasuk'] / $konversi, $odt['hrgsat_dtrbmasuk'], $kd_barang]);

        $db->prepare("UPDATE ordersdetail SET masuk = '0' WHERE id_dtrbmasuk = ?")
            ->execute([$odt['id_dtrbmasuk']]);

        if (!empty($odt['no_batch'])) {
            $db->prepare("INSERT INTO batch (tgl_transaksi, no_batch, exp_date, qty, satuan, kd_transaksi, kd_barang, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'masuk')")
                ->execute([$waktu, $odt['no_batch'], $odt['exp_date'], $qtygrosir_dtrbmasuk, $odt['sat_dtrbmasuk'], $kd_trbmasuk, $kd_barang]);
        }
    }

    $subtotal = hitung_subtotal_order($db, $kd_trbmasuk, $kd_orders);

    $db->commit();

    echo json_encode([
        'status'       => 'ok',
        'id_dtrbmasuk' => $id_dtrbmasuk_final,
        'subtotal'     => format_rupiah($subtotal)
    ]);
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
