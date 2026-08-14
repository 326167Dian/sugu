<?php
include "../../../configurasi/koneksi.php";
include "../../../configurasi/fungsi_rupiah.php";

$id_dtrbmasuk        = isset($_POST['id_dtrbmasuk']) ? $_POST['id_dtrbmasuk'] : '';
$qtygrosir_dtrbmasuk = isset($_POST['qtygrosir_dtrbmasuk']) ? $_POST['qtygrosir_dtrbmasuk'] : '';

header('Content-Type: application/json');

if ($id_dtrbmasuk === '' || !is_numeric($qtygrosir_dtrbmasuk) || $qtygrosir_dtrbmasuk <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Qty Grosir tidak valid']);
    exit;
}

$ambildata = $db->prepare("SELECT kd_trbmasuk, konversi, hrgsat_dtrbmasuk FROM ordersdetail WHERE id_dtrbmasuk = ?");
$ambildata->execute([$id_dtrbmasuk]);
$r = $ambildata->fetch(PDO::FETCH_ASSOC);

if (!$r) {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    exit;
}

$qty_dtrbmasuk    = $r['konversi'] * $qtygrosir_dtrbmasuk;
$hrgttl_dtrbmasuk = $r['hrgsat_dtrbmasuk'] * $qty_dtrbmasuk;

$db->prepare("UPDATE ordersdetail SET
                    qtygrosir_dtrbmasuk = ?,
                    qty_dtrbmasuk = ?,
                    hrgttl_dtrbmasuk = ?
                WHERE id_dtrbmasuk = ?")
    ->execute([$qtygrosir_dtrbmasuk, $qty_dtrbmasuk, $hrgttl_dtrbmasuk, $id_dtrbmasuk]);

$sumstmt = $db->prepare("SELECT SUM(hrgttl_dtrbmasuk) as grandnya FROM ordersdetail WHERE kd_trbmasuk = ?");
$sumstmt->execute([$r['kd_trbmasuk']]);
$rsum = $sumstmt->fetch(PDO::FETCH_ASSOC);
$subtotal = isset($rsum['grandnya']) ? $rsum['grandnya'] : 0;

// tampilkan qty tanpa nol desimal yang tidak perlu, mis. 50 bukan 50.0000
$qty_dtrbmasuk_text = rtrim(rtrim(sprintf('%.4f', $qty_dtrbmasuk), '0'), '.');
if ($qty_dtrbmasuk_text === '') {
    $qty_dtrbmasuk_text = '0';
}

echo json_encode([
    'status'           => 'ok',
    'qty_dtrbmasuk'    => $qty_dtrbmasuk_text,
    'hrgttl_dtrbmasuk' => format_rupiah($hrgttl_dtrbmasuk),
    'subtotal'         => format_rupiah($subtotal)
]);
