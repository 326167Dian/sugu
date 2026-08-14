<?php
// Hitung ulang SUB TOTAL "Terima Barang dari Pesanan" (transaksi berjalan + item pesanan yang belum diterima)
function hitung_subtotal_order($db, $kd_trbmasuk, $kd_orders)
{
    $subtotal = 0;

    $q1 = $db->prepare("SELECT hrgsat_dtrbmasuk, diskon, qty_grosir FROM trbmasuk_detail WHERE kd_trbmasuk = ?");
    $q1->execute([$kd_trbmasuk]);
    while ($row = $q1->fetch(PDO::FETCH_ASSOC)) {
        $subtotal += round($row['hrgsat_dtrbmasuk'] * (1 - ($row['diskon'] / 100)) * $row['qty_grosir']);
    }

    $q2 = $db->prepare("SELECT hrgsat_dtrbmasuk, diskon, qtygrosir_dtrbmasuk FROM ordersdetail WHERE kd_trbmasuk = ? AND masuk = '1'");
    $q2->execute([$kd_orders]);
    while ($row = $q2->fetch(PDO::FETCH_ASSOC)) {
        $subtotal += round($row['hrgsat_dtrbmasuk'] * (1 - ($row['diskon'] / 100)) * $row['qtygrosir_dtrbmasuk']);
    }

    return $subtotal;
}
