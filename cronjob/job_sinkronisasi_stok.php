<?php
set_time_limit(1800);
// Menggunakan koneksi terpusat
include __DIR__ . "/../configurasi/koneksi.php";

$datetime = date('Y-m-d H:i:s', time());
// Query
$sinkronisasi = "
    UPDATE barang b
    LEFT JOIN (
        SELECT kd_barang, SUM(qty_dtrbmasuk) AS totalbeli
        FROM trbmasuk_detail
        INNER JOIN trbmasuk ON trbmasuk.kd_trbmasuk = trbmasuk_detail.kd_trbmasuk
        GROUP BY kd_barang
    ) beli ON beli.kd_barang = b.kd_barang
    LEFT JOIN (
        SELECT kd_barang, SUM(qty_dtrkasir) AS totaljual
        FROM trkasir_detail
        INNER JOIN trkasir ON trkasir.kd_trkasir = trkasir_detail.kd_trkasir
        GROUP BY kd_barang
    ) jual ON jual.kd_barang = b.kd_barang
    SET b.stok_barang = (COALESCE(beli.totalbeli, 0) - COALESCE(jual.totaljual, 0))
    WHERE b.stok_barang <> (COALESCE(beli.totalbeli, 0) - COALESCE(jual.totaljual, 0))
       OR b.stok_barang IS NULL
";

// Eksekusi + Validasi
$text = "";
try {
    $stmt = $db->prepare($sinkronisasi);
    $stmt->execute();
    $affected = $stmt->rowCount();

    if ($affected > 0) {
        $text = "Berhasil! Data stok diperbarui sebanyak $affected baris.";
    } else {
        $text = "Query berhasil, tetapi tidak ada data yang berubah.";
    }
} catch (PDOException $e) {
    $text = "Gagal menjalankan query: " . $e->getMessage();
}

// Catat log hasil sinkronisasi
$log = $db->prepare("INSERT INTO job_sinkronisasi_stok (keterangan, waktu) VALUES (?, ?)");
$log->execute([$text, $datetime]);

?>