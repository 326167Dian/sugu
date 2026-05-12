<?php
session_start();
include "../../../configurasi/koneksi.php";

function pto_forbidden($message)
{
    echo "<div style='padding:12px; font-family:Arial,sans-serif;'>";
    echo "<h3>Akses ditolak</h3>";
    echo "<div style='color:#b00;'>" . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . "</div>";
    echo "</div>";
    exit;
}

if (empty($_SESSION['username']) and empty($_SESSION['passuser'])) {
    echo "<link href=../../css/style.css rel=stylesheet type=text/css>";
    echo "<div class='error msg'>Untuk mengakses Modul anda harus login.</div>";
    exit;
}

$act = isset($_GET['act']) ? $_GET['act'] : 'input';

if ($act === 'delete') {
    if (!(isset($_SESSION['level']) && $_SESSION['level'] === 'pemilik')) {
        pto_forbidden('Fitur hapus PTO hanya untuk pemilik.');
    }

    $id_pto = isset($_GET['id_pto']) ? intval($_GET['id_pto']) : 0;
    if ($id_pto <= 0) {
        pto_forbidden('ID PTO tidak valid.');
    }

    try {
        $stmtGet = $db->prepare("SELECT id_pelanggan FROM pto WHERE id_pto = ?");
        $stmtGet->execute([$id_pto]);
        $row = $stmtGet->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            pto_forbidden('Data PTO tidak ditemukan.');
        }

        $stmtDel = $db->prepare("DELETE FROM pto WHERE id_pto = ?");
        $stmtDel->execute([$id_pto]);

        $id_pelanggan = (int)$row['id_pelanggan'];
        header('Location: ../../media_admin.php?module=pto&act=riwayat&id_pelanggan=' . $id_pelanggan);
        exit;
    } catch (PDOException $e) {
        echo "<div style='padding:12px; font-family:Arial,sans-serif;'>";
        echo "<h3>Gagal menghapus data PTO</h3>";
        echo "<div style='color:#b00;'>" . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</div>";
        echo "</div>";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../media_admin.php?module=pelanggan');
    exit;
}

$createTableSql = "CREATE TABLE IF NOT EXISTS pto (
    id_pto INT(11) NOT NULL AUTO_INCREMENT,
    id_pelanggan INT(11) NOT NULL,
    nm_pelanggan VARCHAR(120) DEFAULT NULL,
    jenis_kelamin VARCHAR(30) DEFAULT NULL,
    umur VARCHAR(30) DEFAULT NULL,
    alamat_pelanggan TEXT,
    tlp_pelanggan VARCHAR(30) DEFAULT NULL,
    tanggal_1 DATE DEFAULT NULL,
    catatan_1 TEXT,
    obat_1 TEXT,
    masalah_1 TEXT,
    tindak_1 TEXT,
    tanggal_2 DATE DEFAULT NULL,
    catatan_2 TEXT,
    obat_2 TEXT,
    masalah_2 TEXT,
    tindak_2 TEXT,
    tempat_ttd VARCHAR(120) DEFAULT NULL,
    tanggal_ttd DATE DEFAULT NULL,
    created_by VARCHAR(120) DEFAULT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_pto),
    KEY idx_pto_pelanggan (id_pelanggan)
) ENGINE=InnoDB DEFAULT CHARSET=utf8";

try {
    $db->exec($createTableSql);

    $id_pelanggan = isset($_POST['id_pelanggan']) ? intval($_POST['id_pelanggan']) : 0;
    $nm_pelanggan = isset($_POST['nm_pelanggan']) ? trim($_POST['nm_pelanggan']) : '';
    $jenis_kelamin = isset($_POST['jenis_kelamin']) ? trim($_POST['jenis_kelamin']) : '';
    $umur = isset($_POST['umur']) ? trim($_POST['umur']) : '';
    $alamat_pelanggan = isset($_POST['alamat_pelanggan']) ? trim($_POST['alamat_pelanggan']) : '';
    $tlp_pelanggan = isset($_POST['tlp_pelanggan']) ? trim($_POST['tlp_pelanggan']) : '';

    $tanggal_1 = !empty($_POST['tanggal_1']) ? $_POST['tanggal_1'] : null;
    $catatan_1 = isset($_POST['catatan_1']) ? trim($_POST['catatan_1']) : '';
    $obat_1 = isset($_POST['obat_1']) ? trim($_POST['obat_1']) : '';
    $masalah_1 = isset($_POST['masalah_1']) ? trim($_POST['masalah_1']) : '';
    $tindak_1 = isset($_POST['tindak_1']) ? trim($_POST['tindak_1']) : '';

    $tanggal_2 = !empty($_POST['tanggal_2']) ? $_POST['tanggal_2'] : null;
    $catatan_2 = isset($_POST['catatan_2']) ? trim($_POST['catatan_2']) : '';
    $obat_2 = isset($_POST['obat_2']) ? trim($_POST['obat_2']) : '';
    $masalah_2 = isset($_POST['masalah_2']) ? trim($_POST['masalah_2']) : '';
    $tindak_2 = isset($_POST['tindak_2']) ? trim($_POST['tindak_2']) : '';

    $tempat_ttd = isset($_POST['tempat_ttd']) ? trim($_POST['tempat_ttd']) : '';
    $tanggal_ttd = !empty($_POST['tanggal_ttd']) ? $_POST['tanggal_ttd'] : null;
    $created_by = isset($_SESSION['namalengkap']) ? $_SESSION['namalengkap'] : (isset($_SESSION['username']) ? $_SESSION['username'] : '');

    if ($act === 'update') {
        if (!(isset($_SESSION['level']) && $_SESSION['level'] === 'pemilik')) {
            pto_forbidden('Fitur edit PTO hanya untuk pemilik.');
        }

        $id_pto = isset($_POST['id_pto']) ? intval($_POST['id_pto']) : 0;
        if ($id_pto <= 0) {
            throw new PDOException('ID PTO tidak valid untuk update.');
        }

        $updateSql = "UPDATE pto SET
            id_pelanggan = ?,
            nm_pelanggan = ?,
            jenis_kelamin = ?,
            umur = ?,
            alamat_pelanggan = ?,
            tlp_pelanggan = ?,
            tanggal_1 = ?,
            catatan_1 = ?,
            obat_1 = ?,
            masalah_1 = ?,
            tindak_1 = ?,
            tanggal_2 = ?,
            catatan_2 = ?,
            obat_2 = ?,
            masalah_2 = ?,
            tindak_2 = ?,
            tempat_ttd = ?,
            tanggal_ttd = ?,
            created_by = ?
        WHERE id_pto = ?";

        $stmt = $db->prepare($updateSql);
        $stmt->execute([
            $id_pelanggan, $nm_pelanggan, $jenis_kelamin, $umur, $alamat_pelanggan, $tlp_pelanggan,
            $tanggal_1, $catatan_1, $obat_1, $masalah_1, $tindak_1,
            $tanggal_2, $catatan_2, $obat_2, $masalah_2, $tindak_2,
            $tempat_ttd, $tanggal_ttd, $created_by, $id_pto
        ]);
    } else {
        $insertSql = "INSERT INTO pto (
            id_pelanggan, nm_pelanggan, jenis_kelamin, umur, alamat_pelanggan, tlp_pelanggan,
            tanggal_1, catatan_1, obat_1, masalah_1, tindak_1,
            tanggal_2, catatan_2, obat_2, masalah_2, tindak_2,
            tempat_ttd, tanggal_ttd, created_by
        ) VALUES (
            ?, ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?,
            ?, ?, ?, ?, ?,
            ?, ?, ?
        )";

        $stmt = $db->prepare($insertSql);
        $stmt->execute([
            $id_pelanggan, $nm_pelanggan, $jenis_kelamin, $umur, $alamat_pelanggan, $tlp_pelanggan,
            $tanggal_1, $catatan_1, $obat_1, $masalah_1, $tindak_1,
            $tanggal_2, $catatan_2, $obat_2, $masalah_2, $tindak_2,
            $tempat_ttd, $tanggal_ttd, $created_by
        ]);
    }

    include __DIR__ . '/tampil_pto.php';
} catch (PDOException $e) {
    echo "<div style='padding:12px; font-family:Arial,sans-serif;'>";
    echo "<h3>Gagal menyimpan data PTO</h3>";
    echo "<div style='color:#b00;'>" . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "</div>";
    echo "</div>";
}
