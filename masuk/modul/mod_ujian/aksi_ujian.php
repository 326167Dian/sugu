<?php
session_start();

if (empty($_SESSION['username']) and empty($_SESSION['passuser'])) {
    echo "<link href='../../css/style.css' rel='stylesheet' type='text/css'>";
    echo "<div class='error msg'>Untuk mengakses modul, Anda harus login.</div>";
    exit;
}

$canAccessUjian = (isset($_SESSION['ujian']) && strtoupper(trim((string) $_SESSION['ujian'])) === 'Y');
$isPemilik = (isset($_SESSION['level']) && $_SESSION['level'] === 'pemilik');

if (!$canAccessUjian || !$isPemilik) {
    echo "<link href='../../css/style.css' rel='stylesheet' type='text/css'>";
    echo "<div class='error msg'>Fitur CRUD soal hanya untuk status pemilik.</div>";
    exit;
}

include "../../../configurasi/koneksi.php";

$act = isset($_GET['act']) ? $_GET['act'] : '';

function setUjianFlash($type, $message)
{
    $_SESSION['ujian_flash'] = array(
        'type' => $type,
        'message' => $message
    );
}

function validJawaban($value)
{
    $v = strtolower(trim((string) $value));
    return in_array($v, array('a', 'b', 'c'), true) ? $v : '';
}

if ($act === 'simpanheader') {
    $nm_ujian = isset($_POST['nm_ujian']) ? trim($_POST['nm_ujian']) : '';
    $durasi = isset($_POST['durasi']) ? (int) $_POST['durasi'] : 0;

    if ($nm_ujian === '' || $durasi <= 0) {
        setUjianFlash('danger', 'Nama ujian dan durasi wajib diisi.');
        header("Location: ../../media_admin.php?module=ujian&act=input_nama_ujian");
        exit;
    }

    $stmt = $db->prepare("INSERT INTO soal_header (nm_ujian, durasi) VALUES (?, ?)");
    $stmt->execute(array($nm_ujian, $durasi));

    $id_soal_aktif = (int) $db->lastInsertId();
    if ($id_soal_aktif > 0) {
        $_SESSION['ujian_aktif_id'] = $id_soal_aktif;
    }

    setUjianFlash('success', 'Nama ujian berhasil disimpan.');
    header("Location: ../../media_admin.php?module=ujian&act=input_nama_ujian");
    exit;
}

if ($act === 'updateheader') {
    $id_soal = isset($_POST['id_soal']) ? (int) $_POST['id_soal'] : 0;
    $nm_ujian = isset($_POST['nm_ujian']) ? trim($_POST['nm_ujian']) : '';
    $durasi = isset($_POST['durasi']) ? (int) $_POST['durasi'] : 0;

    if ($id_soal <= 0 || $nm_ujian === '' || $durasi <= 0) {
        setUjianFlash('danger', 'Data update ujian tidak valid.');
        header("Location: ../../media_admin.php?module=ujian&act=input_nama_ujian");
        exit;
    }

    $stmt = $db->prepare("UPDATE soal_header SET nm_ujian = ?, durasi = ? WHERE id_soal = ?");
    $stmt->execute(array($nm_ujian, $durasi, $id_soal));

    $_SESSION['ujian_aktif_id'] = $id_soal;

    setUjianFlash('success', 'Nama ujian berhasil diupdate.');
    header("Location: ../../media_admin.php?module=ujian&act=input_nama_ujian");
    exit;
}

if ($act === 'hapusheader') {
    $id_soal = isset($_GET['id_soal']) ? (int) $_GET['id_soal'] : 0;

    if ($id_soal <= 0) {
        setUjianFlash('danger', 'Data ujian tidak valid.');
        header("Location: ../../media_admin.php?module=ujian&act=input_nama_ujian");
        exit;
    }

    $stmtCekSoal = $db->prepare("SELECT COUNT(*) FROM soal WHERE id_soal = ?");
    $stmtCekSoal->execute(array($id_soal));
    $jumlahSoal = (int) $stmtCekSoal->fetchColumn();

    if ($jumlahSoal > 0) {
        setUjianFlash('warning', 'Nama ujian tidak dapat dihapus karena masih memiliki soal.');
        header("Location: ../../media_admin.php?module=ujian&act=input_nama_ujian");
        exit;
    }

    $stmt = $db->prepare("DELETE FROM soal_header WHERE id_soal = ?");
    $stmt->execute(array($id_soal));

    if (isset($_SESSION['ujian_aktif_id']) && (int) $_SESSION['ujian_aktif_id'] === $id_soal) {
        unset($_SESSION['ujian_aktif_id']);
    }

    setUjianFlash('success', 'Nama ujian berhasil dihapus.');
    header("Location: ../../media_admin.php?module=ujian&act=input_nama_ujian");
    exit;
}

if ($act === 'simpansoal') {
    $id_soal = isset($_POST['id_soal']) ? (int) $_POST['id_soal'] : 0;
    if ($id_soal <= 0 && isset($_SESSION['ujian_aktif_id'])) {
        $id_soal = (int) $_SESSION['ujian_aktif_id'];
    }
    $pertanyaan = isset($_POST['pertanyaan']) ? trim($_POST['pertanyaan']) : '';
    $opsi_a = isset($_POST['opsi_a']) ? trim($_POST['opsi_a']) : '';
    $opsi_b = isset($_POST['opsi_b']) ? trim($_POST['opsi_b']) : '';
    $opsi_c = isset($_POST['opsi_c']) ? trim($_POST['opsi_c']) : '';
    $jawaban_benar = validJawaban(isset($_POST['jawaban_benar']) ? $_POST['jawaban_benar'] : '');

    if ($id_soal <= 0 || $pertanyaan === '' || $opsi_a === '' || $opsi_b === '' || $opsi_c === '' || $jawaban_benar === '') {
        echo "<script>alert('Data soal belum lengkap.');window.location='../../media_admin.php?module=ujian&act=tambahsoal';</script>";
        exit;
    }

    $stmt = $db->prepare("INSERT INTO soal (id_soal, pertanyaan, opsi_a, opsi_b, opsi_c, jawaban_benar) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute(array($id_soal, $pertanyaan, $opsi_a, $opsi_b, $opsi_c, $jawaban_benar));

    $_SESSION['ujian_aktif_id'] = $id_soal;

    header("Location: ../../media_admin.php?module=ujian&act=tambahsoal&ujian_id=" . $id_soal);
    exit;
}

if ($act === 'updatesoal') {
    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $id_soal = isset($_POST['id_soal']) ? (int) $_POST['id_soal'] : 0;
    $pertanyaan = isset($_POST['pertanyaan']) ? trim($_POST['pertanyaan']) : '';
    $opsi_a = isset($_POST['opsi_a']) ? trim($_POST['opsi_a']) : '';
    $opsi_b = isset($_POST['opsi_b']) ? trim($_POST['opsi_b']) : '';
    $opsi_c = isset($_POST['opsi_c']) ? trim($_POST['opsi_c']) : '';
    $jawaban_benar = validJawaban(isset($_POST['jawaban_benar']) ? $_POST['jawaban_benar'] : '');

    if ($id <= 0 || $id_soal <= 0 || $pertanyaan === '' || $opsi_a === '' || $opsi_b === '' || $opsi_c === '' || $jawaban_benar === '') {
        echo "<script>alert('Data update soal tidak valid.');window.location='../../media_admin.php?module=ujian&act=kelola';</script>";
        exit;
    }

    $stmt = $db->prepare("UPDATE soal SET id_soal = ?, pertanyaan = ?, opsi_a = ?, opsi_b = ?, opsi_c = ?, jawaban_benar = ? WHERE id = ?");
    $stmt->execute(array($id_soal, $pertanyaan, $opsi_a, $opsi_b, $opsi_c, $jawaban_benar, $id));

    $_SESSION['ujian_aktif_id'] = $id_soal;

    header("Location: ../../media_admin.php?module=ujian&act=kelola&ujian_id=" . $id_soal);
    exit;
}

if ($act === 'hapussoal') {
    $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
    $ujian_id = isset($_GET['ujian_id']) ? (int) $_GET['ujian_id'] : 0;
    if ($ujian_id <= 0 && isset($_SESSION['ujian_aktif_id'])) {
        $ujian_id = (int) $_SESSION['ujian_aktif_id'];
    }

    if ($id > 0) {
        $stmt = $db->prepare("DELETE FROM soal WHERE id = ?");
        $stmt->execute(array($id));
    }

    header("Location: ../../media_admin.php?module=ujian&act=kelola&ujian_id=" . $ujian_id);
    exit;
}

header("Location: ../../media_admin.php?module=ujian");
exit;
