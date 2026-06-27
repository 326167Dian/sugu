<?php
session_start();
include "../../../configurasi/koneksi.php";

header('Content-Type: application/json');

function ensure_riwayat_foto2_column($db)
{
    static $checked = false;
    if ($checked) {
        return;
    }
    $checked = true;

    $stmt = $db->query("SHOW COLUMNS FROM riwayat_pelanggan LIKE 'foto2'");
    if ($stmt && $stmt->rowCount() > 0) {
        return;
    }

    $db->exec("ALTER TABLE riwayat_pelanggan ADD COLUMN foto2 VARCHAR(255) NULL AFTER foto");
}

function followup_image_upload_dir()
{
    return realpath(__DIR__ . '/../../') . DIRECTORY_SEPARATOR . 'images';
}

function upload_followup_foto($fileInput)
{
    if (!isset($_FILES[$fileInput])) {
        return ['ok' => true, 'filename' => ''];
    }

    $file = $_FILES[$fileInput];
    if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['ok' => true, 'filename' => ''];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'message' => 'Upload foto follow up gagal.'];
    }

    if ($file['size'] > 1024 * 1024) {
        return ['ok' => false, 'message' => 'Ukuran foto maksimal 1MB.'];
    }

    $originalName = isset($file['name']) ? $file['name'] : '';
    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
    $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($ext, $allowedExt, true)) {
        return ['ok' => false, 'message' => 'Format foto tidak didukung.'];
    }

    if (function_exists('random_bytes')) {
        $suffix = bin2hex(random_bytes(4));
    } else {
        $suffix = str_replace('.', '', uniqid('', true));
    }

    $safeName = 'followup_' . date('Ymd_His') . '_' . $suffix . '.' . $ext;
    $targetDir = followup_image_upload_dir();
    if ($targetDir === false) {
        return ['ok' => false, 'message' => 'Folder images tidak ditemukan.'];
    }

    $targetPath = $targetDir . DIRECTORY_SEPARATOR . $safeName;
    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        return ['ok' => false, 'message' => 'Gagal menyimpan foto follow up.'];
    }

    return ['ok' => true, 'filename' => $safeName];
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$datetime = date('Y-m-d H:i:s', time());

if ($id < 1) {
    echo json_encode(['status' => 'error', 'message' => 'ID riwayat tidak valid.']);
    exit;
}

try {
    ensure_riwayat_foto2_column($db);
    $uploadFoto = upload_followup_foto('foto_followup');
    if (!$uploadFoto['ok']) {
        echo json_encode(['status' => 'error', 'message' => $uploadFoto['message']]);
        exit;
    }

    $followupBy = isset($_SESSION['namalengkap']) ? $_SESSION['namalengkap'] : '';
    if ($uploadFoto['filename'] !== '') {
        $stmt = $db->prepare("UPDATE riwayat_pelanggan 
                            SET tgl_followup = ?,
                                followup_by = ?,
                                foto2 = ?
                            WHERE id = ?");
        $stmt->execute([$datetime, $followupBy, $uploadFoto['filename'], $id]);
    } else {
        $stmt = $db->prepare("UPDATE riwayat_pelanggan 
                            SET tgl_followup = ?,
                                followup_by = ?
                            WHERE id = ?");
        $stmt->execute([$datetime, $followupBy, $id]);
    }

    echo json_encode(['status' => 'success']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan follow up.']);
}
?>