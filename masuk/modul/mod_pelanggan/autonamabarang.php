<?php
include "../../../configurasi/koneksi.php";

$key = $_POST['query'];

$stmt = $db->prepare("SELECT * FROM barang WHERE nm_barang LIKE ?");
$stmt->execute(['%'.$key.'%']);


$json = [];
while($re = $stmt->fetch(PDO::FETCH_ASSOC)){
    $json[] = array(
                'nm_barang' => $re['nm_barang'],
                'kd_barang' => $re['kd_barang']
                );
}
echo json_encode($json);
?>