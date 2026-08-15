<?php
// Hitung ulang "Total Harga" (tanpa PPN) untuk footer tabel "Terima Barang dari Pesanan" (trbmasukpbf).
// Sesuai tampilan tbl_detail1.php: hanya menjumlahkan item yang SUDAH diterima (trbmasuk_detail),
// item yang masih pending di ordersdetail tidak ikut dijumlahkan ke Total Harga (perilaku aslinya memang begitu).
function hitung_subtotal_pbf($db, $kd_trbmasuk)
{
    $subtotal = 0;

    $q = $db->prepare("SELECT hnasat_dtrbmasuk, diskon, qty_grosir FROM trbmasuk_detail WHERE kd_trbmasuk = ?");
    $q->execute([$kd_trbmasuk]);
    while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
        $subtotal += round($row['hnasat_dtrbmasuk'] * (1 - ($row['diskon'] / 100)) * $row['qty_grosir']);
    }

    return $subtotal;
}
