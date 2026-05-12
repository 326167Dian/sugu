<?php
session_start();
if (empty($_SESSION['username']) && empty($_SESSION['passuser'])) {
    echo "<link href='style.css' rel='stylesheet' type='text/css'>
 <center>Untuk mengakses modul, Anda harus login <br>";
    echo "<a href=../../index.php><b>LOGIN</b></a></center>";
} else {
    include "../../../configurasi/koneksi.php";

    $module = $_GET['module'];
    $act = $_GET['act'];

    $id_admin = 0;
    if (isset($_SESSION['id_admin'])) {
        $id_admin = intval($_SESSION['id_admin']);
    } elseif (isset($_SESSION['idadmin'])) {
        $id_admin = intval($_SESSION['idadmin']);
    }
    $nama_lengkap = isset($_SESSION['namalengkap']) ? $_SESSION['namalengkap'] : '';

    if ($module == 'konseling' && $act == 'input_konseling') {
        $id_pelanggan = intval($_POST['id_pelanggan']);
        $tgl_konseling = isset($_POST['tgl_konseling']) ? $_POST['tgl_konseling'] : '';
        $nama_dokter = trim($_POST['nama_dokter']);
        $diagnosa = trim($_POST['diagnosa']);
        $riwayat_penyakit = trim($_POST['riwayat_penyakit']);
        $riwayat_alergi = trim($_POST['riwayat_alergi']);
        $keluhan = trim($_POST['keluhan']);
        $visite = trim($_POST['visite']);
        $tindakan = trim($_POST['tindakan']);

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tgl_konseling)) {
            echo "<script type='text/javascript'>alert('Format tanggal konseling tidak valid.');history.go(-1);</script>";
            exit;
        }

        $pstmt = $db->prepare("SELECT nm_pelanggan FROM pelanggan WHERE id_pelanggan = ?");
        $pstmt->execute([$id_pelanggan]);
        $pelanggan = $pstmt->fetch(PDO::FETCH_ASSOC);
        if (!$pelanggan) {
            echo "<script type='text/javascript'>alert('Pelanggan tidak ditemukan.');history.go(-1);</script>";
            exit;
        }

        $stmt = $db->prepare("INSERT INTO konseling(
            id_pelanggan, nm_pelanggan, tgl_konseling, id_admin, nama_lengkap,
            nama_dokter, diagnosa, riwayat_penyakit, riwayat_alergi, keluhan, visite, tindakan
        ) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $id_pelanggan,
            $pelanggan['nm_pelanggan'],
            $tgl_konseling,
            $id_admin,
            $nama_lengkap,
            $nama_dokter,
            $diagnosa,
            $riwayat_penyakit,
            $riwayat_alergi,
            $keluhan,
            $visite,
            $tindakan
        ]);

        header('location:../../media_admin.php?module=' . $module);
    } elseif ($module == 'konseling' && $act == 'update_konseling') {
        $id = intval($_POST['id']);
        $id_pelanggan = intval($_POST['id_pelanggan']);
        $tgl_konseling = isset($_POST['tgl_konseling']) ? $_POST['tgl_konseling'] : '';
        $nama_dokter = trim($_POST['nama_dokter']);
        $diagnosa = trim($_POST['diagnosa']);
        $riwayat_penyakit = trim($_POST['riwayat_penyakit']);
        $riwayat_alergi = trim($_POST['riwayat_alergi']);
        $keluhan = trim($_POST['keluhan']);
        $visite = trim($_POST['visite']);
        $tindakan = trim($_POST['tindakan']);

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tgl_konseling)) {
            echo "<script type='text/javascript'>alert('Format tanggal konseling tidak valid.');history.go(-1);</script>";
            exit;
        }

        $pstmt = $db->prepare("SELECT nm_pelanggan FROM pelanggan WHERE id_pelanggan = ?");
        $pstmt->execute([$id_pelanggan]);
        $pelanggan = $pstmt->fetch(PDO::FETCH_ASSOC);
        if (!$pelanggan) {
            echo "<script type='text/javascript'>alert('Pelanggan tidak ditemukan.');history.go(-1);</script>";
            exit;
        }

        $stmt = $db->prepare("UPDATE konseling SET
            id_pelanggan = ?,
            nm_pelanggan = ?,
            tgl_konseling = ?,
            id_admin = ?,
            nama_lengkap = ?,
            nama_dokter = ?,
            diagnosa = ?,
            riwayat_penyakit = ?,
            riwayat_alergi = ?,
            keluhan = ?,
            visite = ?,
            tindakan = ?
            WHERE id_konseling = ?");
        $stmt->execute([
            $id_pelanggan,
            $pelanggan['nm_pelanggan'],
            $tgl_konseling,
            $id_admin,
            $nama_lengkap,
            $nama_dokter,
            $diagnosa,
            $riwayat_penyakit,
            $riwayat_alergi,
            $keluhan,
            $visite,
            $tindakan,
            $id
        ]);

        header('location:../../media_admin.php?module=' . $module);
    } elseif ($module == 'konseling' && $act == 'hapus') {
        $stmt = $db->prepare("DELETE FROM konseling WHERE id_konseling = ?");
        $stmt->execute([$_GET['id']]);
        header('location:../../media_admin.php?module=' . $module);
    }
}
?>
