<?php
include "../configurasi/koneksi.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $username     = $_POST['username'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $no_telp      = $_POST['no_telp'];
    $password_raw = $_POST['password'];

    // Hash password agar sesuai dengan sistem login (password_verify di cek_login.php)
    $password_hashed = password_hash($password_raw, PASSWORD_DEFAULT);

    // Nilai otomatis sesuai instruksi
    $akses_level = 'petugas';
    $blokir      = 'N';
    $ujian       = 'Y';
    $unit        = '1';
    $n           = 'N'; // Nilai default 'N' untuk semua hak akses menu

    try {
        // Query insert menggunakan PDO prepared statement untuk keamanan
        $sql = "INSERT INTO admin (username, 
                                   nama_lengkap, 
                                   no_telp, 
                                   password, 
                                   akses_level, 
                                   blokir, 
                                   ujian,
                                   unit,
                                   mpengguna, mheader, mjenisbayar, mpelanggan, msupplier, 
                                   msatuan, mjenisobat, mbarang, tbm, tbmpbf, tpk, 
                                   lpitem, lpbrgmasuk, lpkasir, lpsupplier, lppelanggan, 
                                   mstok, stok_kritis, orders, penjualansebelum, 
                                   labapenjualan, byrkredit, stokopname, soharian, 
                                   labajenisobat, koreksistok, shiftkerja, neraca, 
                                   komisi, kartustok, catatan, cekdarah, jurnalkas) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 
                        ?, ?, ?, ?, ?, 
                        ?, ?, ?, ?, ?, ?, 
                        ?, ?, ?, ?, ?, 
                        ?, ?, ?, ?, 
                        ?, ?, ?, ?, 
                        ?, ?, ?, ?, 
                        ?, ?, ?, ?, ?)";
        
        $stmt = $db->prepare($sql);
        
        // Parameter utama + 33 parameter hak akses (diisi dengan $n)
        $params = [
            $username, $nama_lengkap, $no_telp, $password_hashed, $akses_level, $blokir, $ujian, $unit,
            $n, $n, $n, $n, $n, $n, $n, $n, $n, $n, $n, $n, $n, $n, $n, $n, $n, $n, $n, $n, $n, $n, $n, $n, $n, $n, $n, $n, $n, $n, $n, $n, $n
        ];

        $stmt->execute($params);

        echo "<script>alert('Registrasi Admin Berhasil!'); window.location='../masuk/index.php';</script>";
    } catch (PDOException $e) {
        // Jika terjadi error (misal username duplikat)
        echo "<script>alert('Gagal registrasi: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
    }
} else {
    // Redirect jika akses file ini secara langsung tanpa POST
    header("Location: index.php");
}
?>
