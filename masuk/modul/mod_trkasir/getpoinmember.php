<?php
include "../../../configurasi/koneksi.php";

$id_pelanggan = $_POST['id_pelanggan'];
$stmt_get = $db->prepare("SELECT * FROM pelanggan WHERE id_pelanggan = :id_pelanggan");
$stmt_get->execute([
    ':id_pelanggan' => $id_pelanggan
]);

$get = $stmt_get->fetch(PDO::FETCH_ASSOC);
echo $get['total_poin'];
?>