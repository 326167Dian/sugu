<?php
include "../../../configurasi/koneksi.php";
include "../../../configurasi/fungsi_rupiah.php";
include "helper_subtotal.php";

// input ditampilkan dalam format rupiah (mis. "62.500"), jadi titik ribuan harus dibuang dulu
$hnasat_dtrbmasuk       = str_replace(".","",$_POST['hnasat_dtrbmasuk']);
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

        $harga_satuan   = round(($hnasat_dtrbmasuk / $detail['konversi']) * (1-($detail['diskon']/100)) * 1.11);
        $harga_grosir   = round($hnasat_dtrbmasuk);
        $total_harga    = round(($hnasat_dtrbmasuk * 1.11) * $detail['qty_grosir']) * (1 - ($detail['diskon']/100));

        $db->prepare("UPDATE trbmasuk_detail SET
                            hnasat_dtrbmasuk    = ?,
                            hrgsat_dtrbmasuk    = ?,
                            hrgttl_dtrbmasuk    = ?
                            WHERE id_dtrbmasuk  = ?")
            ->execute([$hnasat_dtrbmasuk, $harga_satuan, $total_harga, $id_dtrbmasuk]);

        $db->prepare("UPDATE barang SET
                            hrgsat_barang   = ?,
                            hna             = ?,
                            hrgsat_grosir   = ?
                            WHERE id_barang = ?")
            ->execute([$harga_satuan, $hnasat_dtrbmasuk, $harga_grosir, $detail['id_barang']]);

        $hnasat_final = $hnasat_dtrbmasuk;
        $diskon_final = $detail['diskon'];
        $qtygrosir_final = $detail['qty_grosir'];
    } else {
        $order  = $db->prepare("SELECT * FROM ordersdetail WHERE kd_barang=? AND kd_trbmasuk=?");
        $order->execute([$kd_barang, $kd_orders]);
        $odt    = $order->fetch(PDO::FETCH_ASSOC);

        if (!$odt || $odt['masuk'] != '1') {
            throw new Exception('Item sudah diterima pada transaksi lain. Silakan muat ulang halaman.');
        }

        $qty_dtrbmasuk  = $qtygrosir_dtrbmasuk * $odt['konversi'];
        $harga_satuan   = round(($hnasat_dtrbmasuk / $odt['konversi']) * (1-($odt['diskon']/100)) * 1.11);
        $total_harga    = round(($hnasat_dtrbmasuk * 1.11) * $qtygrosir_dtrbmasuk) * (1 - ($odt['diskon']/100));
        $harga_grosir   = round($hnasat_dtrbmasuk);
        $waktu          = date('Y-m-d H:i:s', time());

        $cekstok        = $db->prepare("SELECT * FROM barang WHERE id_barang = ?");
        $cekstok->execute([$odt['id_barang']]);
        $rst            = $cekstok->fetch(PDO::FETCH_ASSOC);

        $hrgjual_barang     = round($odt['hrgjual_dtrbmasuk']);

        $db->prepare("UPDATE barang SET
                            stok_barang     = stok_barang + ?,
                            hna             = ?,
                            hrgsat_barang   = ?,
                            hrgsat_grosir   = ?,
                            hrgjual_barang  = ?
                            WHERE id_barang = ?")
            ->execute([$qty_dtrbmasuk, $hnasat_dtrbmasuk, $harga_satuan, $harga_grosir, $hrgjual_barang, $odt['id_barang']]);

        $db->prepare("UPDATE ordersdetail SET masuk = '0' WHERE id_dtrbmasuk = ?")->execute([$odt['id_dtrbmasuk']]);

        $db->prepare("INSERT INTO trbmasuk_detail(
                                        kd_trbmasuk, kd_orders, id_barang, kd_barang, nmbrg_dtrbmasuk,
                                        qty_dtrbmasuk, qty_grosir, sat_dtrbmasuk, satgrosir_dtrbmasuk, konversi,
                                        hnasat_dtrbmasuk, diskon, hrgsat_dtrbmasuk, hrgjual_dtrbmasuk, hrgttl_dtrbmasuk,
                                        no_batch, exp_date, waktu)
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")
            ->execute([$kd_trbmasuk, $kd_orders, $odt['id_barang'], $odt['kd_barang'], $odt['nmbrg_dtrbmasuk'], $qty_dtrbmasuk, $qtygrosir_dtrbmasuk, $odt['sat_dtrbmasuk'], $odt['satgrosir_dtrbmasuk'], $odt['konversi'], $hnasat_dtrbmasuk, $odt['diskon'], $harga_satuan, $hrgjual_barang, $total_harga, $odt['no_batch'], $odt['exp_date'], $waktu]);

        $id_dtrbmasuk = $db->lastInsertId();

        $hnasat_final = $hnasat_dtrbmasuk;
        $diskon_final = $odt['diskon'];
        $qtygrosir_final = $qtygrosir_dtrbmasuk;
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
