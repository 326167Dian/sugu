<?php
include "../../../configurasi/koneksi.php";

$key = $_POST['nm_barang'];

$ubah = $db->prepare("SELECT * FROM barang WHERE nm_barang = ?");
$ubah->execute([$key]);

$json = [];
while($re = $ubah->fetch(PDO::FETCH_ASSOC)){
$json[] = array(
            'id_barang'         => $re['id_barang'],
			'nm_barang'         => $re['nm_barang'],
			'stok_barang'       => $re['stok_barang'],
			'sat_barang'        => $re['sat_barang'],
			'hrgjual_barang'    => $re['hrgjual_barang'],
			'hrgjual_barang1'   => $re['hrgjual_barang1'],
			'hrgjual_barang2'   => $re['hrgjual_barang2'],
			'hrgsat_barang'     => $re['hrgsat_barang'],
			'kd_barang'         => (string)$re['kd_barang']
			);
}
echo json_encode($json);
?>