<?php
session_start();
if (empty($_SESSION['username']) && empty($_SESSION['passuser'])) {
    echo "<link href='style.css' rel='stylesheet' type='text/css'>
 <center>Untuk mengakses modul, Anda harus login <br>";
    echo "<a href=../../index.php><b>LOGIN</b></a></center>";
} else {
    include "../../../configurasi/koneksi.php";

    $module = isset($_GET['module']) ? $_GET['module'] : '';
    $act = isset($_GET['act']) ? $_GET['act'] : '';

    $id_admin = 0;
    if (isset($_SESSION['id_admin'])) {
        $id_admin = intval($_SESSION['id_admin']);
    } elseif (isset($_SESSION['idadmin'])) {
        $id_admin = intval($_SESSION['idadmin']);
    }
    $nama_lengkap = isset($_SESSION['namalengkap']) ? $_SESSION['namalengkap'] : '';

    if ($module == 'swamedikasi' && $act == 'input_swamedikasi') {
        $id_pelanggan = intval($_POST['id_pelanggan']);
        $tgl_swamedikasi = isset($_POST['tgl_swamedikasi']) ? $_POST['tgl_swamedikasi'] : '';
        $keluhan = trim($_POST['keluhan']);
        $riwayat_penyakit = trim($_POST['riwayat_penyakit']);
        $riwayat_alergi = trim($_POST['riwayat_alergi']);
        $obat_direkomendasikan = trim($_POST['obat_direkomendasikan']);
        $aturan_pakai = trim($_POST['aturan_pakai']);
        $saran = trim($_POST['saran']);

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tgl_swamedikasi)) {
            echo "<script type='text/javascript'>alert('Format tanggal swamedikasi tidak valid.');history.go(-1);</script>";
            exit;
        }

        $pstmt = $db->prepare("SELECT nm_pelanggan FROM pelanggan WHERE id_pelanggan = ?");
        $pstmt->execute([$id_pelanggan]);
        $pelanggan = $pstmt->fetch(PDO::FETCH_ASSOC);
        if (!$pelanggan) {
            echo "<script type='text/javascript'>alert('Pelanggan tidak ditemukan.');history.go(-1);</script>";
            exit;
        }

        $stmt = $db->prepare("INSERT INTO swamedikasi(
            id_pelanggan, nm_pelanggan, tgl_swamedikasi, id_admin, nama_lengkap,
            keluhan, riwayat_penyakit, riwayat_alergi, obat_direkomendasikan, aturan_pakai, saran
        ) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $id_pelanggan,
            $pelanggan['nm_pelanggan'],
            $tgl_swamedikasi,
            $id_admin,
            $nama_lengkap,
            $keluhan,
            $riwayat_penyakit,
            $riwayat_alergi,
            $obat_direkomendasikan,
            $aturan_pakai,
            $saran
        ]);

        header('location:../../media_admin.php?module=' . $module);
    } elseif ($module == 'swamedikasi' && $act == 'update_swamedikasi') {
        $id = intval($_POST['id']);
        $id_pelanggan = intval($_POST['id_pelanggan']);
        $tgl_swamedikasi = isset($_POST['tgl_swamedikasi']) ? $_POST['tgl_swamedikasi'] : '';
        $keluhan = trim($_POST['keluhan']);
        $riwayat_penyakit = trim($_POST['riwayat_penyakit']);
        $riwayat_alergi = trim($_POST['riwayat_alergi']);
        $obat_direkomendasikan = trim($_POST['obat_direkomendasikan']);
        $aturan_pakai = trim($_POST['aturan_pakai']);
        $saran = trim($_POST['saran']);

        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tgl_swamedikasi)) {
            echo "<script type='text/javascript'>alert('Format tanggal swamedikasi tidak valid.');history.go(-1);</script>";
            exit;
        }

        $pstmt = $db->prepare("SELECT nm_pelanggan FROM pelanggan WHERE id_pelanggan = ?");
        $pstmt->execute([$id_pelanggan]);
        $pelanggan = $pstmt->fetch(PDO::FETCH_ASSOC);
        if (!$pelanggan) {
            echo "<script type='text/javascript'>alert('Pelanggan tidak ditemukan.');history.go(-1);</script>";
            exit;
        }

        $stmt = $db->prepare("UPDATE swamedikasi SET
            id_pelanggan = ?,
            nm_pelanggan = ?,
            tgl_swamedikasi = ?,
            id_admin = ?,
            nama_lengkap = ?,
            keluhan = ?,
            riwayat_penyakit = ?,
            riwayat_alergi = ?,
            obat_direkomendasikan = ?,
            aturan_pakai = ?,
            saran = ?
            WHERE id_swamedikasi = ?");
        $stmt->execute([
            $id_pelanggan,
            $pelanggan['nm_pelanggan'],
            $tgl_swamedikasi,
            $id_admin,
            $nama_lengkap,
            $keluhan,
            $riwayat_penyakit,
            $riwayat_alergi,
            $obat_direkomendasikan,
            $aturan_pakai,
            $saran,
            $id
        ]);

        header('location:../../media_admin.php?module=' . $module);
    } elseif ($module == 'swamedikasi' && $act == 'hapus') {
        $stmt = $db->prepare("DELETE FROM swamedikasi WHERE id_swamedikasi = ?");
        $stmt->execute([intval($_GET['id'])]);
        header('location:../../media_admin.php?module=' . $module);
    }
}
?>
