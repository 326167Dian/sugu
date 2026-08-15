<?php
include "../../../configurasi/koneksi.php";
include "../../../configurasi/fungsi_rupiah.php";
include "helper_subtotal.php";

$konversi               = $_POST['konversi'];
$kd_barang              = $_POST['kd_barang'];
$kd_trbmasuk            = $_POST['kd_trbmasuk'];
$kd_orders              = $_POST['kd_orders'];
$id_dtrbmasuk           = $_POST['id_dtrbmasuk'];
$qtygrosir_dtrbmasuk    = isset($_POST['qtygrosir_dtrbmasuk']) ? $_POST['qtygrosir_dtrbmasuk'] : 0;

header('Content-Type: application/json');

try {
    $db->beginTransaction();

    $trbmasuk = $db->prepare("SELECT * FROM trbmasuk_detail WHERE kd_barang=? AND kd_trbmasuk=?");
    $trbmasuk->execute([$kd_barang, $kd_trbmasuk]);
    $detail = $trbmasuk->fetch(PDO::FETCH_ASSOC);

    if ($trbmasuk->rowCount() > 0) {
        $id_dtrbmasuk = $detail['id_dtrbmasuk'];

        $cekstok = $db->prepare("SELECT * FROM barang WHERE id_barang = ?");
        $cekstok->execute([$detail['id_barang']]);
        $rsto = $cekstok->fetch(PDO::FETCH_ASSOC);
        $stoko_barang   = $rsto['stok_barang'] - $detail['qty_dtrbmasuk'];

        $qty_dtrbmasuk  = $konversi * $detail['qty_grosir'];
        $harga_satuan   = round(($rsto['hna'] / $konversi) * (1-($detail['diskon']/100)) * 1.11);
        $harga_grosir   = round($rsto['hna']);
        $total_harga    = round(($rsto['hna'] * 1.11) * $detail['qty_grosir']) * (1 - ($detail['diskon']/100));

        $stokakhir      = $stoko_barang + $qty_dtrbmasuk;

        $db->prepare("UPDATE barang SET hrgsat_barang = ?, stok_barang = ?, hrgsat_grosir = ? WHERE id_barang = ?")
            ->execute([$harga_satuan, $stokakhir, $harga_grosir, $detail['id_barang']]);

        $db->prepare("UPDATE trbmasuk_detail SET konversi = ?, qty_dtrbmasuk = ?, hrgttl_dtrbmasuk = ? WHERE id_dtrbmasuk = ?")
            ->execute([$konversi, $qty_dtrbmasuk, $total_harga, $id_dtrbmasuk]);

        $hnasat_final = $detail['hnasat_dtrbmasuk'];
        $diskon_final = $detail['diskon'];
        $qtygrosir_final = $detail['qty_grosir'];
    } else {
        $order = $db->prepare("SELECT * FROM ordersdetail WHERE kd_barang = ? AND kd_trbmasuk = ?");
        $order->execute([$kd_barang, $kd_orders]);
        $odt = $order->fetch(PDO::FETCH_ASSOC);

        if (!$odt || $odt['masuk'] != '1') {
            throw new Exception('Item sudah diterima pada transaksi lain. Silakan muat ulang halaman.');
        }

        $cekstok = $db->prepare("SELECT * FROM barang WHERE id_barang = ?");
        $cekstok->execute([$odt['id_barang']]);
        $rst = $cekstok->fetch(PDO::FETCH_ASSOC);
        $stok_barang    = $rst['stok_barang'];

        $qty_dtrbmasuk  = $konversi * $odt['qtygrosir_dtrbmasuk'];
        $stokakhir      = $stok_barang + $qty_dtrbmasuk;
        $harga_satuan   = round(($rst['hna'] / $konversi) * (1-($odt['diskon']/100)) * 1.11);
        $total_harga    = round(($rst['hna'] * 1.11) * $odt['qtygrosir_dtrbmasuk']) * (1 - ($odt['diskon']/100));
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
            ->execute([$kd_trbmasuk, $kd_orders, $odt['id_barang'], $odt['kd_barang'], $odt['nmbrg_dtrbmasuk'], $qty_dtrbmasuk, $odt['qtygrosir_dtrbmasuk'], $odt['sat_dtrbmasuk'], $odt['satgrosir_dtrbmasuk'], $konversi, $rst['hna'], $odt['diskon'], $harga_satuan, $hrgjual_barang, $total_harga, $odt['no_batch'], $odt['exp_date'], $waktu]);

        $id_dtrbmasuk = $db->lastInsertId();

        if (!empty($odt['no_batch'])) {
            $datetime = date('Y-m-d H:i:s', time());
            $getbatch = $db->prepare("SELECT * FROM batch WHERE kd_transaksi =? AND no_batch =?");
            $getbatch->execute([$kd_trbmasuk, $odt['no_batch']]);
            if ($getbatch->rowCount() > 0) {
                $rowbatch = $getbatch->fetch(PDO::FETCH_ASSOC);
                $ttlqtybatch = $rowbatch['qty'] + $odt['qtygrosir_dtrbmasuk'];
                $db->prepare("UPDATE batch SET qty = ? WHERE kd_transaksi = ? AND no_batch = ?")
                    ->execute([$ttlqtybatch, $kd_trbmasuk, $odt['no_batch']]);
            } else {
                $db->prepare("INSERT INTO batch(tgl_transaksi, no_batch, exp_date, qty, satuan, kd_transaksi, kd_barang, status) VALUES(?,?,?,?,?,?,?,?)")
                    ->execute([$datetime, $odt['no_batch'], $odt['exp_date'], $odt['qtygrosir_dtrbmasuk'], $odt['sat_dtrbmasuk'], $kd_trbmasuk, $kd_barang, 'masuk']);
            }
        }

        $hnasat_final = $rst['hna'];
        $diskon_final = $odt['diskon'];
        $qtygrosir_final = $odt['qtygrosir_dtrbmasuk'];
    }

    $hnadisc = $hnasat_final * (1 - ($diskon_final / 100));
    $baristotal = round($hnadisc * $qtygrosir_final);
    $subtotal = hitung_subtotal_pbf($db, $kd_trbmasuk);

    $db->commit();

    echo json_encode([
        'status'       => 'ok',
        'id_dtrbmasuk' => $id_dtrbmasuk,
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
