<?php
session_start();
include "../../../configurasi/koneksi.php";

header('Content-Type: application/json');

if (empty($_SESSION['username']) && empty($_SESSION['passuser'])) {
    echo json_encode(array(
        "status" => "error",
        "message" => "Sesi login sudah berakhir."
    ));
    exit;
}

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
if ($id <= 0) {
    echo json_encode(array(
        "status" => "error",
        "message" => "ID follow up tidak valid."
    ));
    exit;
}

$datetime = date('Y-m-d H:i:s');
$followupBy = isset($_SESSION['namalengkap']) && $_SESSION['namalengkap'] !== '' ? $_SESSION['namalengkap'] : 'System';

try {
    $stmt = $db->prepare("UPDATE riwayat_pelanggan 
                            SET tgl_followup = ?,
                                followup_by = ?
                            WHERE id = ?");
    $stmt->execute(array($datetime, $followupBy, $id));

    echo json_encode(array(
        "status" => "success",
        "message" => "Follow up berhasil disimpan.",
        "tgl_followup" => $datetime,
        "followup_by" => $followupBy
    ));
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(array(
        "status" => "error",
        "message" => "Gagal menyimpan follow up: " . $e->getMessage()
    ));
}
?>