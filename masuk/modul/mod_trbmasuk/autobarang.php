<?php
include "../../../configurasi/koneksi.php";

	$kod = $_POST['kd_brg'];

	$ubah = $db->prepare("SELECT * FROM barang WHERE kd_barang = ?");
	$ubah->execute([$kod]);
	$re = $ubah->fetch(PDO::FETCH_ASSOC);
      
    $json[] = array(
            'id_barang'         => $re['id_barang'],
			'nm_barang'         => $re['nm_barang'],
			'stok_barang'       => $re['stok_barang'],
			'sat_barang'        => $re['sat_barang'],
			'sat_grosir'        => $re['sat_grosir'],
			'indikasi'          => $re['indikasi'],
			'konversi'          => $re['konversi'],
			'hrgjual_barang'    => $re['hrgjual_barang'],
			'hrgjual_barang1'   => $re['hrgjual_barang1'],
			'hrgjual_barang2'   => $re['hrgjual_barang2'],
			'hrgsat_barang'     => $re['hrgsat_barang'],
			'hna'               => $re['hna'],
			'kd_barang'         => (string)$re['kd_barang'],
			);
 
	echo json_encode($json);

?>
