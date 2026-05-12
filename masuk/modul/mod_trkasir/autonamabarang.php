<?php
include "../../../configurasi/koneksi.php";

$key = $_POST['query'];

$stmt = $db->prepare("SELECT * FROM barang WHERE nm_barang LIKE ?");
$stmt->execute(['%'.$key.'%']);

$json = [];
while($re = $stmt->fetch(PDO::FETCH_ASSOC)){
    $stok = (int)$re['stok_barang'];
    $sat  = $re['sat_barang'];
    $json[] = $re['nm_barang'] . ' ( ' . $stok . ' ' . $sat . ' )';
}

$stmtbundle = $db->prepare("SELECT * FROM bundle WHERE nm_bundle LIKE ?");
$stmtbundle->execute(['%'.$key.'%']);
while($bnd = $stmtbundle->fetch(PDO::FETCH_ASSOC)){
    $json[] = $bnd['nm_bundle'];
}

echo json_encode($json);
?>