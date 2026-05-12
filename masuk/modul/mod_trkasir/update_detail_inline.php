<?php
session_start();
include "../../../configurasi/koneksi.php";

header('Content-Type: application/json');

$id_dtrkasir = isset($_POST['id_dtrkasir']) ? (int) $_POST['id_dtrkasir'] : 0;
$qty_baru = isset($_POST['qty_dtrkasir']) ? (float) $_POST['qty_dtrkasir'] : 0;
$resep = isset($_POST['resep']) ? strtoupper(trim($_POST['resep'])) : 'TIDAK';

if ($id_dtrkasir <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'ID detail tidak valid']);
    exit;
}

if ($qty_baru < 1) {
    echo json_encode(['status' => 'error', 'message' => 'Qty minimal 1']);
    exit;
}

if ($resep !== 'YA' && $resep !== 'TIDAK') {
    $resep = 'TIDAK';
}

try {
    $db->beginTransaction();

    $detail = $db->prepare("SELECT id_dtrkasir, id_barang, qty_dtrkasir, hrgjual_dtrkasir, disc FROM trkasir_detail WHERE id_dtrkasir = ?");
    $detail->execute([$id_dtrkasir]);
    $row = $detail->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        $db->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Detail tidak ditemukan']);
        exit;
    }

    $qty_lama = (float) $row['qty_dtrkasir'];
    $delta = $qty_baru - $qty_lama;

    if ($delta != 0) {
        $stok = $db->prepare("SELECT stok_barang FROM barang WHERE id_barang = ?");
        $stok->execute([$row['id_barang']]);
        $stokRow = $stok->fetch(PDO::FETCH_ASSOC);

        if (!$stokRow) {
            $db->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'Data stok barang tidak ditemukan']);
            exit;
        }

        $stok_saat_ini = (float) $stokRow['stok_barang'];

        if ($delta > 0 && $stok_saat_ini < $delta) {
            $db->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'Stok tidak mencukupi']);
            exit;
        }

        $stok_baru = $stok_saat_ini - $delta;

        $updStok = $db->prepare("UPDATE barang SET stok_barang = ? WHERE id_barang = ?");
        $updStok->execute([$stok_baru, $row['id_barang']]);
    }

    $hrg_jual = (float) $row['hrgjual_dtrkasir'];
    $disc = (float) $row['disc'];
    $hrg_setelah_disc = $hrg_jual * (1 - ($disc / 100));
    $total_baru = $qty_baru * $hrg_setelah_disc;

    $updDetail = $db->prepare("UPDATE trkasir_detail SET qty_dtrkasir = ?, resep = ?, hrgttl_dtrkasir = ? WHERE id_dtrkasir = ?");
    $updDetail->execute([$qty_baru, $resep, $total_baru, $id_dtrkasir]);

    $db->commit();

    echo json_encode(['status' => 'success', 'message' => 'Detail berhasil diupdate']);
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }

    echo json_encode(['status' => 'error', 'message' => 'Terjadi kesalahan saat update detail']);
}
