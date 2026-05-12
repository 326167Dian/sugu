<?php
date_default_timezone_set('Asia/jakarta');
$server = "localhost";
$user = "u877780297_ernawati";
$password = "7390091979Dian&&";
$database = "u877780297_kitautama";
set_time_limit(1800);

// Koneksi
$conn = mysqli_connect($server, $user, $password, $database);

if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

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
if (mysqli_query($conn, $sinkronisasi)) {

    $affected = mysqli_affected_rows($conn);

    if ($affected > 0) {
        $text = "Berhasil! Data stok diperbarui sebanyak $affected baris.";
    } else {
        $text = "Query berhasil, tetapi tidak ada data yang berubah.";
    }
    
} else {
    $text = "Gagal menjalankan query: " . mysqli_error($conn);
}
mysqli_query($conn, "INSERT INTO job_sinkronisasi_stok (keterangan, waktu) VALUES ('$text','$datetime')");

// $batch = "
//     UPDATE batch b
//     LEFT JOIN (
//         SELECT kd_trbmasuk,kd_barang,no_batch,qty_dtrbmasuk AS totalbeli
//         FROM trbmasuk_detail
//     ) beli ON beli.kd_barang = b.kd_barang AND beli.kd_trbmasuk = b.kd_transaksi AND beli.no_batch = b.no_batch
//     SET b.qty = COALESCE(beli.totalbeli, 0)
//     WHERE b.status = 'masuk'
//     AND b.kd_transaksi = beli.kd_trbmasuk
//     AND b.kd_barang = beli.kd_barang
//     AND b.no_batch = beli.no_batch
// ";
// mysqli_query($conn, $batch);

// Tutup koneksi
mysqli_close($conn);
?>