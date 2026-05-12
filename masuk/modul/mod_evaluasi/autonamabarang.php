<?php
include "../../../configurasi/koneksi.php";

$key = $_POST['query'];

$ubah = $db->prepare("SELECT * FROM barang WHERE nm_barang LIKE ?");
$ubah->execute(["%$key%"]);

$json = [];
while($re = $ubah->fetch(PDO::FETCH_ASSOC)){
    $json[] = $re['nm_barang'];
}
echo json_encode($json);
?>