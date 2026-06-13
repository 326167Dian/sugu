<?php
require_once __DIR__ . '/../../../configurasi/koneksi.php';
$mysqli = $GLOBALS["___mysqli_ston"];

$is_kelipatan = $_POST['is_kelipatan'];
$stmt = $db->prepare("UPDATE poin_pelanggan SET is_kelipatan = :is_kelipatan");
$stmt->execute([
        ':is_kelipatan' => $is_kelipatan
    ]);
?>