<?php
include "../../../configurasi/koneksi.php";
include "../../../configurasi/fungsi_rupiah.php";
include "helper_subtotal.php";

$no_batch       = $_POST['no_batch'];
$kd_barang      = $_POST['kd_barang'];
$kd_trbmasuk    = $_POST['kd_trbmasuk'];
$kd_orders      = $_POST['kd_orders'];
$id_dtrbmasuk   = $_POST['id_dtrbmasuk'];
$qtygrosir_dtrbmasuk = isset($_POST['qtygrosir_dtrbmasuk']) ? $_POST['qtygrosir_dtrbmasuk'] : 0;
// no_batch SEBELUM diedit -- dipakai untuk mencari baris yang benar (lihat catatan di bawah),
// bukan no_batch yang baru diketik user (yang sudah ada di variabel $no_batch di atas)
$no_batch_asal  = isset($_POST['no_batch_asal']) ? $_POST['no_batch_asal'] : '';

header('Content-Type: application/json');

try {
    $db->beginTransaction();

    // dicocokkan dengan no_batch LAMA (no_batch_asal), bukan kd_barang+kd_trbmasuk saja, supaya kalau
    // barang yang sama sudah punya beberapa baris batch berbeda, edit di satu baris tidak nyasar ke baris lain
    $trbmasuk = $db->prepare("SELECT * FROM trbmasuk_detail WHERE kd_barang=? AND kd_trbmasuk=? AND no_batch = ?");
    $trbmasuk->execute([$kd_barang, $kd_trbmasuk, $no_batch_asal]);
    $detail = $trbmasuk->fetch(PDO::FETCH_ASSOC);

    if ($trbmasuk->rowCount() > 0) {
        $id_dtrbmasuk = $detail['id_dtrbmasuk'];
        $no_batch_lama = $detail['no_batch'];

        $db->prepare("UPDATE trbmasuk_detail SET no_batch = ? WHERE id_dtrbmasuk = ?")->execute([$no_batch, $id_dtrbmasuk]);

        $stmt_batch = $db->prepare("SELECT * FROM batch WHERE kd_barang=? AND kd_transaksi=? AND no_batch=?");
        $stmt_batch->execute([$kd_barang, $kd_trbmasuk, $no_batch_lama]);
        if ($stmt_batch->rowCount() > 0) {
            $db->prepare("UPDATE batch SET no_batch = ? WHERE kd_barang = ? AND kd_transaksi = ? AND no_batch = ?")
                ->execute([$no_batch, $kd_barang, $kd_trbmasuk, $no_batch_lama]);
        }

        $hnasat_final = $detail['hnasat_dtrbmasuk'];
        $diskon_final = $detail['diskon'];
        $qtygrosir_final = $detail['qty_grosir'];
        $no_batch_final = $no_batch;
    } else {
        $stmt_order = $db->prepare("SELECT * FROM ordersdetail WHERE kd_barang=? AND kd_trbmasuk=?");
        $stmt_order->execute([$kd_barang, $kd_orders]);
        $odt = $stmt_order->fetch(PDO::FETCH_ASSOC);

        if (!$odt || $odt['masuk'] != '1') {
            throw new Exception('Item sudah diterima pada transaksi lain. Silakan muat ulang halaman.');
        }

        $qty_dtrbmasuk  = $qtygrosir_dtrbmasuk * $odt['konversi'];

        $stmt_stok = $db->prepare("SELECT * FROM barang WHERE id_barang = ?");
        $stmt_stok->execute([$odt['id_barang']]);
        $rst = $stmt_stok->fetch(PDO::FETCH_ASSOC);
        $stok_barang    = $rst['stok_barang'];
        $stokakhir      = $stok_barang + $qty_dtrbmasuk;

        $harga_satuan   = round(($rst['hna'] / $odt['konversi']) * (1-($odt['diskon']/100)) * 1.11);
        $total_harga    = round(($rst['hna'] * 1.11) * $qtygrosir_dtrbmasuk) * (1 - ($odt['diskon']/100));
        $waktu          = date('Y-m-d H:i:s', time());

        $hrgjual_barang     = round($odt['hrgjual_dtrbmasuk']);

        $db->prepare("UPDATE barang SET
                        stok_barang = stok_barang + ?,
                        hrgsat_barang = ?,
                        hrgjual_barang = ?
                        WHERE id_barang = ?")->execute([$qty_dtrbmasuk, $harga_satuan, $hrgjual_barang, $odt['id_barang']]);

        $db->prepare("UPDATE ordersdetail SET masuk = '0' WHERE id_dtrbmasuk = ?")->execute([$odt['id_dtrbmasuk']]);

        $db->prepare("INSERT INTO trbmasuk_detail(
                                        kd_trbmasuk, kd_orders, id_barang, kd_barang, nmbrg_dtrbmasuk,
                                        qty_dtrbmasuk, qty_grosir, sat_dtrbmasuk, satgrosir_dtrbmasuk, konversi,
                                        hnasat_dtrbmasuk, diskon, hrgsat_dtrbmasuk, hrgjual_dtrbmasuk, hrgttl_dtrbmasuk,
                                        no_batch, exp_date, waktu)
                                    VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)")
            ->execute([$kd_trbmasuk, $kd_orders, $odt['id_barang'], $odt['kd_barang'], $odt['nmbrg_dtrbmasuk'], $qty_dtrbmasuk, $qtygrosir_dtrbmasuk, $odt['sat_dtrbmasuk'], $odt['satgrosir_dtrbmasuk'], $odt['konversi'], $rst['hna'], $odt['diskon'], $harga_satuan, $hrgjual_barang, $total_harga, $no_batch, $odt['exp_date'], $waktu]);

        $id_dtrbmasuk = $db->lastInsertId();

        if (!empty($no_batch)) {
            $datetime = date('Y-m-d H:i:s', time());
            $getbatch = $db->prepare("SELECT * FROM batch WHERE kd_transaksi=? AND no_batch=?");
            $getbatch->execute([$kd_trbmasuk, $no_batch]);
            if ($getbatch->rowCount() > 0) {
                $rowbatch = $getbatch->fetch(PDO::FETCH_ASSOC);
                $ttlqtybatch = $rowbatch['qty'] + $odt['qty_dtrbmasuk'];
                $db->prepare("UPDATE batch SET qty = ? WHERE kd_transaksi = ? AND no_batch = ?")
                    ->execute([$ttlqtybatch, $kd_trbmasuk, $no_batch]);
            } else {
                $db->prepare("INSERT INTO batch(tgl_transaksi, no_batch, exp_date, qty, satuan, kd_transaksi, kd_barang, status) VALUES(?,?,?,?,?,?,?,?)")
                    ->execute([$datetime, $no_batch, $odt['exp_date'], $odt['qty_dtrbmasuk'], $odt['sat_dtrbmasuk'], $kd_trbmasuk, $odt['kd_barang'], 'masuk']);
            }
        }

        $hnasat_final = $rst['hna'];
        $diskon_final = $odt['diskon'];
        $qtygrosir_final = $qtygrosir_dtrbmasuk;
        $no_batch_final = $no_batch;
    }

    $hnadisc = $hnasat_final * (1 - ($diskon_final / 100));
    $baristotal = round($hnadisc * $qtygrosir_final);
    $subtotal = hitung_subtotal_pbf($db, $kd_trbmasuk);

    $db->commit();

    echo json_encode([
        'status'       => 'ok',
        'id_dtrbmasuk' => $id_dtrbmasuk,
        'no_batch'     => $no_batch_final,
        'hnadisc_text' => format_rupiah($hnadisc),
        'total_text'   => format_rupiah($baristotal),
        'subtotal'     => format_rupiah($subtotal)
    ]);
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
