<?php
// Reset saldo kas (tabel `kas`) ke 0 setiap kali aplikasi diakses pertama kali
// di bulan baru, supaya perhitungan SALDO di Jurnal Kas mulai dari nol lagi tiap bulan.
// Dibungkus try/catch dan dipanggil sekali per request dari koneksi.php: kalau migrasi
// kolom bulan_saldo belum jalan di suatu environment, fungsi ini diam saja (tidak
// menjatuhkan seluruh aplikasi) dan baru aktif setelah migrasinya dijalankan.
if (!function_exists('reset_saldo_bulanan_jika_perlu')) {
function reset_saldo_bulanan_jika_perlu($db)
{
    try {
        $bulanini = date('Y-m');

        $cek = $db->query("SELECT bulan_saldo FROM kas WHERE id_kas = 1");
        $r = $cek ? $cek->fetch(PDO::FETCH_ASSOC) : false;

        if ($r && $r['bulan_saldo'] !== $bulanini) {
            $db->prepare("UPDATE kas SET saldo = 0, bulan_saldo = ? WHERE id_kas = 1")
                ->execute([$bulanini]);
        }
    } catch (Exception $e) {
        // abaikan: reset saldo bulanan tidak boleh sampai mematikan seluruh aplikasi
    }
}
}
