<?php
session_start();

if (empty($_SESSION['username']) and empty($_SESSION['passuser'])) {
    echo "<link href=../../css/style.css rel=stylesheet type=text/css>";
    echo "<div class='error msg'>Untuk mengakses Modul anda harus login.</div>";
    exit;
}

$canAccessUjian = (isset($_SESSION['ujian']) && strtoupper(trim((string) $_SESSION['ujian'])) === 'Y');
if (!$canAccessUjian) {
    echo "<link href=../../css/style.css rel=stylesheet type=text/css>";
    echo "<div class='error msg'>Anda tidak berhak mengakses halaman ini.</div>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['jawaban']) || !is_array($_POST['jawaban'])) {
    echo "<script>alert('Data jawaban tidak valid.');window.location='../../media_admin.php?module=ujian';</script>";
    exit;
}

include "../../../configurasi/koneksi.php";

$jawaban_user = $_POST['jawaban'];
$ids = array();

$exam_started_at = isset($_POST['exam_started_at']) ? (int) $_POST['exam_started_at'] : 0;
$exam_duration_seconds = isset($_POST['exam_duration_seconds']) ? (int) $_POST['exam_duration_seconds'] : 0;
$ujian_id = isset($_POST['ujian_id']) ? (int) $_POST['ujian_id'] : 0;
$nama_ujian = '';

if ($ujian_id > 0) {
    try {
        $stmtNamaUjian = $db->prepare("SELECT nm_ujian FROM soal_header WHERE id_soal = ? LIMIT 1");
        $stmtNamaUjian->execute(array($ujian_id));
        $rowUjian = $stmtNamaUjian->fetch(PDO::FETCH_ASSOC);
        if ($rowUjian) {
            $nama_ujian = (string) $rowUjian['nm_ujian'];
        }
    } catch (Exception $e) {
        $nama_ujian = '';
    }
}

foreach ($jawaban_user as $soal_id => $nilai_user) {
    $id = (int) $soal_id;
    if ($id > 0) {
        $ids[] = $id;
    }
}

$ids = array_values(array_unique($ids));

if (empty($ids)) {
    echo "<script>alert('Jawaban tidak ditemukan.');window.location='../../media_admin.php?module=ujian';</script>";
    exit;
}

$placeholders = implode(',', array_fill(0, count($ids), '?'));
if ($ujian_id > 0) {
    $stmt = $db->prepare("SELECT id, jawaban_benar FROM soal WHERE id_soal = ? AND id IN ($placeholders)");
    $params = $ids;
    array_unshift($params, $ujian_id);
    $stmt->execute($params);
} else {
    $stmt = $db->prepare("SELECT id, jawaban_benar FROM soal WHERE id IN ($placeholders)");
    $stmt->execute($ids);
}
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$kunci_jawaban = array();
foreach ($rows as $row) {
    $kunci_jawaban[(int) $row['id']] = strtolower(trim((string) $row['jawaban_benar']));
}

$benar = 0;
$salah = 0;
$tidak_valid = 0;

foreach ($ids as $id) {
    $jawab = isset($jawaban_user[$id]) ? strtolower(trim((string) $jawaban_user[$id])) : '';
    if (!isset($kunci_jawaban[$id])) {
        $tidak_valid++;
        continue;
    }

    if ($jawab === $kunci_jawaban[$id]) {
        $benar++;
    } else {
        $salah++;
    }
}

$total_dinilai = $benar + $salah;
$total_soal = $total_dinilai;

if ($ujian_id > 0) {
    try {
        $stmtTotal = $db->prepare("SELECT COUNT(*) FROM soal WHERE id_soal = ?");
        $stmtTotal->execute(array($ujian_id));
        $total_soal = (int) $stmtTotal->fetchColumn();
    } catch (Exception $e) {
        $total_soal = $total_dinilai;
    }
} else {
    $total_soal = count($kunci_jawaban);
}

if ($total_soal <= 0) {
    $total_soal = $total_dinilai;
}

$tidak_dijawab = max(0, $total_soal - $total_dinilai);
$nilai_akhir = $total_soal > 0 ? round(($benar / $total_soal) * 100, 2) : 0;

$waktu_selesai_ts = time();
$waktu_mulai_ts = $exam_started_at > 0 ? $exam_started_at : $waktu_selesai_ts;
$durasi_detik = max(0, $waktu_selesai_ts - $waktu_mulai_ts);
$status_waktu = 'on_time';

if ($exam_duration_seconds > 0 && $durasi_detik > $exam_duration_seconds) {
    $status_waktu = 'timeout';
}

$waktu_mulai_sql = date('Y-m-d H:i:s', $waktu_mulai_ts);
$waktu_selesai_sql = date('Y-m-d H:i:s', $waktu_selesai_ts);

try {
    $stmtSimpanHasil = $db->prepare("INSERT INTO hasil_ujian (
        id_admin,
        username,
        nama_lengkap,
        ujian_id,
        nama_ujian,
        total_soal,
        jawaban_benar,
        jawaban_salah,
        tidak_dijawab,
        soal_tidak_valid,
        nilai_akhir,
        waktu_mulai,
        waktu_selesai,
        durasi_detik,
        durasi_batas_detik,
        status_waktu,
        jawaban_json
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    $stmtSimpanHasil->execute(array(
        isset($_SESSION['idadmin']) ? (int) $_SESSION['idadmin'] : null,
        isset($_SESSION['username']) ? $_SESSION['username'] : null,
        isset($_SESSION['namalengkap']) ? $_SESSION['namalengkap'] : null,
        $ujian_id > 0 ? $ujian_id : null,
        $nama_ujian !== '' ? $nama_ujian : null,
        $total_soal,
        $benar,
        $salah,
        $tidak_dijawab,
        $tidak_valid,
        $nilai_akhir,
        $waktu_mulai_sql,
        $waktu_selesai_sql,
        $durasi_detik,
        $exam_duration_seconds > 0 ? $exam_duration_seconds : null,
        $status_waktu,
        json_encode($jawaban_user)
    ));
} catch (Exception $e) {
    // Penyimpanan hasil tidak menghentikan tampilan nilai agar user tetap mendapatkan hasil ujian.
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hasil Ujian</title>
    <link rel="stylesheet" href="../../bootstrap/css/bootstrap.min.css">
</head>
<body style="padding:20px;">
    <div class="container">
        <div class="panel panel-primary">
            <div class="panel-heading"><strong>Hasil Ujian</strong></div>
            <div class="panel-body">
                <?php if ($nama_ujian !== '') { ?>
                    <p>Nama Ujian: <strong><?php echo htmlspecialchars($nama_ujian); ?></strong></p>
                <?php } ?>
                <p>Total Soal: <strong><?php echo $total_soal; ?></strong></p>
                <p>Jawaban Benar: <strong><?php echo $benar; ?></strong></p>
                <p>Jawaban Salah: <strong><?php echo $salah; ?></strong></p>
                <p>Tidak dijawab: <strong><?php echo $tidak_dijawab; ?></strong></p>
                <?php if ($tidak_valid > 0) { ?>
                    <p>Soal Tidak Valid: <strong><?php echo $tidak_valid; ?></strong></p>
                <?php } ?>
                <p>Durasi Pengerjaan: <strong><?php echo $durasi_detik; ?> detik</strong></p>
                <?php if ($exam_duration_seconds > 0) { ?>
                    <p>Status Waktu: <strong><?php echo $status_waktu === 'timeout' ? 'Melebihi Batas Waktu' : 'Tepat Waktu'; ?></strong></p>
                <?php } ?>
                <hr>
                <h4>Nilai Akhir: <strong><?php echo $nilai_akhir; ?></strong> / 100</h4>
                <br>
                <a href="../../media_admin.php?module=ujian" class="btn btn-primary">Kembali ke Ujian</a>
                <a href="../../media_admin.php?module=home" class="btn btn-default">Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>