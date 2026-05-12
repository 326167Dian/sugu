<?php
include "../../../configurasi/koneksi.php";

	$kod = $_POST['kd_brg'];

	$ubah = $db->prepare("SELECT * FROM barang WHERE kd_barang = ?");
	$ubah->execute([$kod]);
	$re = $ubah->fetch(PDO::FETCH_ASSOC);
      //$json[] = $re['nm_barang'];
	  $json[] = array(
					'id_barang'         => $re['id_barang'],
        			'nm_barang'         => $re['nm_barang'],
        			'stok_barang'       => $re['stok_barang'],
        			'sat_barang'        => $re['sat_barang'],
        			'sat_grosir'        => $re['sat_grosir'],
        			'konversi'          => $re['konversi'],
        			'indikasi'          => $re['indikasi'],
        			'hrgjual_barang'    => $re['hrgjual_barang'],
        			'hrgsat_barang'     => $re['hrgsat_barang'],
        			'kd_barang'         => $re['kd_barang']
					);
 
	echo json_encode($json);

?>
