<?php
include "../../../configurasi/koneksi.php";

$id_dtrbmasuk        = isset($_POST['id_dtrbmasuk']) ? $_POST['id_dtrbmasuk'] : '';
$qtygrosir_dtrbmasuk = isset($_POST['qtygrosir_dtrbmasuk']) ? $_POST['qtygrosir_dtrbmasuk'] : '';

header('Content-Type: application/json');

if ($id_dtrbmasuk === '' || !is_numeric($qtygrosir_dtrbmasuk) || $qtygrosir_dtrbmasuk <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Qty Grosir tidak valid']);
    exit;
}

$ambildata = $db->prepare("SELECT konversi, hrgsat_dtrbmasuk FROM ordersdetail WHERE id_dtrbmasuk = ?");
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

echo json_encode(['status' => 'ok']);
