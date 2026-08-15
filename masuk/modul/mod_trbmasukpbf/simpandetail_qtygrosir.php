<?php
include "../../../configurasi/koneksi.php";
include "../../../configurasi/fungsi_rupiah.php";
include "helper_subtotal.php";

$qtygrosir_dtrbmasuk    = $_POST['qtygrosir_dtrbmasuk'];
$kd_barang              = $_POST['kd_barang'];
$kd_trbmasuk            = $_POST['kd_trbmasuk'];
$kd_orders              = $_POST['kd_orders'];
$id_dtrbmasuk           = $_POST['id_dtrbmasuk'];

header('Content-Type: application/json');

try {
    $db->beginTransaction();

    // dicari cukup dengan kd_barang + kd_trbmasuk (bukan id_dtrbmasuk) karena setelah baris pertama kali
    // dipindah dari ordersdetail ke trbmasuk_detail, id_dtrbmasuk yang dikirim browser masih milik ordersdetail
    $trbmasuk = $db->prepare("SELECT * FROM trbmasuk_detail WHERE kd_barang=? AND kd_trbmasuk=?");
    $trbmasuk->execute([$kd_barang, $kd_trbmasuk]);
    $detail = $trbmasuk->fetch(PDO::FETCH_ASSOC);

    if ($trbmasuk->rowCount() > 0) {
        $id_dtrbmasuk   = $detail['id_dtrbmasuk'];
        $cekstok = $db->prepare("SELECT * FROM barang WHERE id_barang = ?");
        $cekstok->execute([$detail['id_barang']]);
        $rsto = $cekstok->fetch(PDO::FETCH_ASSOC);
        $stoko_barang   = $rsto['stok_barang'] - $detail['qty_dtrbmasuk'];

        $qty_dtrbmasuk  = $qtygrosir_dtrbmasuk * $detail['konversi'];
        $harga_satuan   = round(($rsto['hna'] / $detail['konversi']) * (1-($detail['diskon']/100)) * 1.11);
        $harga_grosir   = round($rsto['hna']);
        $total_harga    = round(($rsto['hna'] * 1.11) * $qtygrosir_dtrbmasuk) * (1 - ($detail['diskon']/100));

        $stokakhir      = $stoko_barang + ($qty_dtrbmasuk);

        $db->prepare("UPDATE barang SET hrgsat_barang = ?, stok_barang = ?, hrgsat_grosir = ? WHERE id_barang = ?")->execute([$harga_satuan, $stokakhir, $harga_grosir, $detail['id_barang']]);

        $db->prepare("UPDATE trbmasuk_detail SET qty_grosir = ?, qty_dtrbmasuk = ?, hrgttl_dtrbmasuk = ? WHERE id_dtrbmasuk = ?")->execute([$qtygrosir_dtrbmasuk, $qty_dtrbmasuk, $total_harga, $id_dtrbmasuk]);

        if(!empty($detail['no_batch'])){
            $stmt_batch = $db->prepare("SELECT * FROM batch
                                    WHERE no_batch=? AND kd_barang=? AND kd_transaksi=?");
            $stmt_batch->execute([$detail['no_batch'],$kd_barang, $kd_trbmasuk]);
            if($stmt_batch->rowCount() > 0){
                $db->prepare("UPDATE batch SET qty = ?
                                            WHERE no_batch = ? AND kd_barang = ?
                                            AND kd_transaksi = ?")->execute([$qtygrosir_dtrbmasuk,$detail['no_batch'], $kd_barang, $kd_trbmasuk]);
            }
        }

        $hnasat_final = $rsto['hna'];
        $diskon_final = $detail['diskon'];
        $qtygrosir_final = $qtygrosir_dtrbmasuk;
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

        $qty_dtrbmasuk  = $qtygrosir_dtrbmasuk * $odt['konversi'];
        $stokakhir      = $stok_barang + $qty_dtrbmasuk;
        $harga_satuan   = round(($rst['hna'] / $odt['konversi']) * (1-($odt['diskon']/100)) * 1.11);
        $total_harga    = round(($rst['hna'] * 1.11) * $qtygrosir_dtrbmasuk) * (1 - ($odt['diskon']/100));
        $waktu          = date('Y-m-d H:i:s', time());

        $hrgjual_barang     = round($odt['hrgjual_dtrbmasuk']);

        $db->prepare("UPDATE barang SET stok_barang = ?, hrgsat_barang = ?, hrgjual_barang = ? WHERE id_barang = ?")->execute([$stokakhir, $harga_satuan, $hrgjual_barang, $odt['id_barang']]);

        $db->prepare("UPDATE ordersdetail SET masuk = '0' WHERE id_dtrbmasuk = ?")->execute([$odt['id_dtrbmasuk']]);

        $db->prepare("INSERT INTO trbmasuk_detail (kd_trbmasuk, kd_orders, id_barang, kd_barang, nmbrg_dtrbmasuk, qty_dtrbmasuk, qty_grosir, sat_dtrbmasuk, satgrosir_dtrbmasuk, konversi, hnasat_dtrbmasuk, diskon, hrgsat_dtrbmasuk, hrgjual_dtrbmasuk, hrgttl_dtrbmasuk, no_batch, exp_date, waktu) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")->execute([$kd_trbmasuk, $kd_orders, $odt['id_barang'], $odt['kd_barang'], $odt['nmbrg_dtrbmasuk'], $qty_dtrbmasuk, $qtygrosir_dtrbmasuk, $odt['sat_dtrbmasuk'], $odt['satgrosir_dtrbmasuk'], $odt['konversi'], $rst['hna'], $odt['diskon'], $harga_satuan, $hrgjual_barang, $total_harga, $odt['no_batch'], $odt['exp_date'], $waktu]);

        $id_dtrbmasuk_new = $db->lastInsertId();
        $id_dtrbmasuk = $id_dtrbmasuk_new;

        $hnasat_final = $rst['hna'];
        $diskon_final = $odt['diskon'];
        $qtygrosir_final = $qtygrosir_dtrbmasuk;
    }

    // Nilai tampilan HNA+Disc & Total per baris (tanpa PPN, sesuai tampilan tbl_detail1.php)
    $hnadisc = $hnasat_final * (1 - ($diskon_final / 100));
    $baristotal = round($hnadisc * $qtygrosir_final);
    $subtotal = hitung_subtotal_pbf($db, $kd_trbmasuk);

    $db->commit();

    echo json_encode([
        'status'       => 'ok',
        'id_dtrbmasuk' => $id_dtrbmasuk,
        'qty_grosir'   => $qtygrosir_final,
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
