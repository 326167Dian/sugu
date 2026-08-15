<?php
include "../../../configurasi/koneksi.php";
include "../../../configurasi/fungsi_rupiah.php";
include "helper_subtotal_order.php";

$qtygrosir_dtrbmasuk = $_POST['qtygrosir_dtrbmasuk'];
$kd_barang           = $_POST['kd_barang'];
$kd_trbmasuk         = $_POST['kd_trbmasuk'];
$kd_orders           = $_POST['kd_orders'];
$id_dtrbmasuk        = $_POST['id_dtrbmasuk'];

header('Content-Type: application/json');

try {
    $db->beginTransaction();

    // dicari cukup dengan kd_barang + kd_trbmasuk (bukan id_dtrbmasuk) karena setelah baris pertama kali
    // dipindah dari ordersdetail ke trbmasuk_detail, id_dtrbmasuk yang dikirim browser masih milik ordersdetail
    $trbmasuk = $db->prepare("SELECT * FROM trbmasuk_detail WHERE kd_barang = ? AND kd_trbmasuk = ?");
    $trbmasuk->execute([$kd_barang, $kd_trbmasuk]);
    $detail = $trbmasuk->fetch(PDO::FETCH_ASSOC);

    if ($trbmasuk->rowCount() > 0) {
        $qty_dtrbmasuk = $qtygrosir_dtrbmasuk * $detail['konversi'];
        $hrgbelidisc   = $detail['hrgsat_dtrbmasuk'] * (1 - ($detail['diskon'] / 100));
        $hrgttl        = round($hrgbelidisc * $qtygrosir_dtrbmasuk);
        $selisih_qty   = $qty_dtrbmasuk - $detail['qty_dtrbmasuk'];

        $db->prepare("UPDATE trbmasuk_detail SET qty_grosir = ?, qty_dtrbmasuk = ?, hrgttl_dtrbmasuk = ? WHERE id_dtrbmasuk = ?")
            ->execute([$qtygrosir_dtrbmasuk, $qty_dtrbmasuk, $hrgttl, $detail['id_dtrbmasuk']]);

        $db->prepare("UPDATE barang SET stok_barang = stok_barang + ? WHERE id_barang = ?")
            ->execute([$selisih_qty, $detail['id_barang']]);

        if (!empty($detail['no_batch'])) {
            $db->prepare("UPDATE batch SET qty = ?
                            WHERE kd_transaksi = ? AND kd_barang = ? AND no_batch = ? AND status = 'masuk'")
                ->execute([$qtygrosir_dtrbmasuk, $kd_trbmasuk, $kd_barang, $detail['no_batch']]);
        }

        $id_dtrbmasuk_final = $detail['id_dtrbmasuk'];
    } else {
        $order = $db->prepare("SELECT * FROM ordersdetail WHERE kd_barang = ? AND kd_trbmasuk = ? AND id_dtrbmasuk = ?");
        $order->execute([$kd_barang, $kd_orders, $id_dtrbmasuk]);
        $odt = $order->fetch(PDO::FETCH_ASSOC);

        if (!$odt || $odt['masuk'] != '1') {
            throw new Exception('Item sudah diterima pada transaksi lain. Silakan muat ulang halaman.');
        }

        $qty_dtrbmasuk = $qtygrosir_dtrbmasuk * $odt['konversi'];
        $hrgbelidisc   = $odt['hrgsat_dtrbmasuk'] * (1 - ($odt['diskon'] / 100));
        $hrgttl        = round($hrgbelidisc * $qtygrosir_dtrbmasuk);
        $waktu         = date('Y-m-d H:i:s', time());
        $hnasat_dtrbmasuk = round($odt['hrgsat_dtrbmasuk'] / 1.11);

        $db->prepare("INSERT INTO trbmasuk_detail (kd_trbmasuk, kd_orders, id_barang, kd_barang, nmbrg_dtrbmasuk, qty_dtrbmasuk, qty_grosir, sat_dtrbmasuk, satgrosir_dtrbmasuk, konversi, hnasat_dtrbmasuk, diskon, hrgsat_dtrbmasuk, hrgjual_dtrbmasuk, hrgttl_dtrbmasuk, no_batch, exp_date, waktu) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")
            ->execute([$kd_trbmasuk, $kd_orders, $odt['id_barang'], $odt['kd_barang'], $odt['nmbrg_dtrbmasuk'], $qty_dtrbmasuk, $qtygrosir_dtrbmasuk, $odt['sat_dtrbmasuk'], $odt['satgrosir_dtrbmasuk'], $odt['konversi'], $hnasat_dtrbmasuk, $odt['diskon'], $odt['hrgsat_dtrbmasuk'], $odt['hrgjual_dtrbmasuk'], $hrgttl, $odt['no_batch'], $odt['exp_date'], $waktu]);

        $id_dtrbmasuk_final = $db->lastInsertId();

        $db->prepare("UPDATE barang SET stok_barang = stok_barang + ? WHERE id_barang = ?")
            ->execute([$qty_dtrbmasuk, $odt['id_barang']]);

        // sinkronkan harga referensi barang dengan harga beli terima barang ini
        $db->prepare("UPDATE barang SET hrgsat_barang = ?, hrgsat_grosir = ? WHERE kd_barang = ?")
            ->execute([$odt['hrgsat_dtrbmasuk'] / $odt['konversi'], $odt['hrgsat_dtrbmasuk'], $kd_barang]);

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
        'status'           => 'ok',
        'id_dtrbmasuk'     => $id_dtrbmasuk_final,
        'qty_grosir'       => $qtygrosir_dtrbmasuk,
        'hrgbelidisc_text' => format_rupiah($hrgbelidisc),
        'total_text'       => format_rupiah($hrgttl),
        'subtotal'         => format_rupiah($subtotal)
    ]);
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
