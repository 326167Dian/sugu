<?php
include "../../../configurasi/koneksi.php";
include "../../../configurasi/fungsi_rupiah.php";

header('Content-Type: application/json');

try {
    $id_barang = $_POST['id_barang'];
    // input ditampilkan dalam format rupiah (mis. "7.000"), jadi titik ribuan harus dibuang dulu
    // sebelum dipakai sebagai angka -- kalau tidak, PHP membaca "7.000" sebagai 7.0 (titik desimal)
    $komisi = isset($_POST['komisi']) ? str_replace('.', '', $_POST['komisi']) : 0;

    if ($komisi === '' || !is_numeric($komisi) || $komisi < 0) {
        throw new Exception('Komisi harus berupa angka dan tidak boleh negatif');
    }

    $stmt = $db->prepare("UPDATE barang SET komisi = ? WHERE id_barang = ?");
    $stmt->execute([$komisi, $id_barang]);

    echo json_encode([
        'status'      => 'ok',
        'komisi_text' => format_rupiah($komisi)
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
