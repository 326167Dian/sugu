<?php
session_start();
header('Content-Type: application/json');

if (empty($_SESSION['username']) and empty($_SESSION['passuser'])) {
    http_response_code(401);
    echo json_encode(array('ok' => false, 'message' => 'Belum login.'));
    exit;
}

$canAccessUjian = (isset($_SESSION['ujian']) && strtoupper(trim((string) $_SESSION['ujian'])) === 'Y');
if (!$canAccessUjian) {
    http_response_code(403);
    echo json_encode(array('ok' => false, 'message' => 'Tidak berhak mengakses ujian.'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['jawaban']) || !is_array($_POST['jawaban'])) {
    http_response_code(400);
    echo json_encode(array('ok' => false, 'message' => 'Data jawaban tidak valid.'));
    exit;
}

include "../../../configurasi/koneksi.php";

$ujian_id = isset($_POST['ujian_id']) ? (int) $_POST['ujian_id'] : 0;
$exam_started_at = isset($_POST['exam_started_at']) ? (int) $_POST['exam_started_at'] : 0;
$id_admin = isset($_SESSION['idadmin']) ? (int) $_SESSION['idadmin'] : 0;

if ($ujian_id <= 0 || $id_admin <= 0) {
    http_response_code(400);
    echo json_encode(array('ok' => false, 'message' => 'Data tidak lengkap.'));
    exit;
}

$jawaban_user = array();
foreach ($_POST['jawaban'] as $soal_id => $nilai) {
    $id = (int) $soal_id;
    if ($id > 0) {
        $jawaban_user[$id] = (string) $nilai;
    }
}

$nama_ujian = '';
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

$waktu_mulai_sql = $exam_started_at > 0 ? date('Y-m-d H:i:s', $exam_started_at) : date('Y-m-d H:i:s');
$waktu_update_sql = date('Y-m-d H:i:s');

try {
    $stmt = $db->prepare("INSERT INTO ujian_progress (
        id_admin, username, nama_lengkap, ujian_id, nama_ujian, jawaban_json, waktu_mulai, waktu_update
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ON DUPLICATE KEY UPDATE
        nama_lengkap = VALUES(nama_lengkap),
        nama_ujian = VALUES(nama_ujian),
        jawaban_json = VALUES(jawaban_json),
        waktu_update = VALUES(waktu_update)");

    $stmt->execute(array(
        $id_admin,
        isset($_SESSION['username']) ? $_SESSION['username'] : null,
        isset($_SESSION['namalengkap']) ? $_SESSION['namalengkap'] : null,
        $ujian_id,
        $nama_ujian !== '' ? $nama_ujian : null,
        json_encode($jawaban_user),
        $waktu_mulai_sql,
        $waktu_update_sql
    ));

    echo json_encode(array('ok' => true, 'terjawab' => count($jawaban_user)));
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(array('ok' => false, 'message' => 'Gagal menyimpan progres.'));
}
