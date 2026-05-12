<?php
include "../../../configurasi/koneksi.php";

$key = $_POST['query'];

$ubah = $db->prepare("SELECT * FROM barang WHERE nm_barang LIKE ?");
$ubah->execute(["%$key%"]);

$json = [];
while($re = $ubah->fetch(PDO::FETCH_ASSOC)){
    $stok = (int)$re['stok_barang'];
    $sat  = $re['sat_barang'];
    $json[] = $re['nm_barang'] . ' ( ' . $stok . ' ' . $sat . ' )';
}
echo json_encode($json);
?>