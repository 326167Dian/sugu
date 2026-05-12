<?php
include "../../../configurasi/koneksi.php";

$key = $_POST['nm_barang'];

$stmt = $db->prepare("SELECT * FROM barang WHERE nm_barang = ?");
$stmt->execute([$key]);

$json = [];
while($re = $stmt->fetch(PDO::FETCH_ASSOC)){
$json[] = array(
            'id_barang'=> $re['id_barang'],
			'nm_barang'=> $re['nm_barang'],
			'stok_barang'=> $re['stok_barang'],
			'sat_barang'=> $re['sat_barang'],
			'sat_grosir'=> $re['sat_grosir'],
			'indikasi'=> $re['indikasi'],
			'konversi'=> $re['konversi'],
			'hrgjual_barang'=> $re['hrgjual_barang'],
			'hrgsat_barang'=> $re['hrgsat_barang'],
			'hna'=> $re['hna'],
			'kd_barang'=> $re['kd_barang']
			);
}
echo json_encode($json);
?>