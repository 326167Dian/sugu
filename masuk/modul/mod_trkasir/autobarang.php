<?php
include "../../../configurasi/koneksi.php";

	$kod = $_POST['kd_brg'];

	$stmt = $db->prepare("SELECT * FROM barang WHERE kd_barang = ?");
	$stmt->execute([$kod]);
	$re = $stmt->fetch(PDO::FETCH_ASSOC);
      //$json[] = $re['nm_barang'];
	  $json[] = array('id_barang'=> $re['id_barang'],
					'nm_barang'=> $re['nm_barang'],
					'stok_barang'=> $re['stok_barang'],
					'sat_barang'=> $re['sat_barang'],
				// 	'indikasi'=> $re['indikasi'],
					'hrgjual_barang'=> $re['hrgjual_barang']);
 
	echo json_encode($json);

?>
