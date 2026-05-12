<?php
include "../../../configurasi/koneksi.php";

	$stmt = $db->prepare("SELECT * FROM pelanggan
	   WHERE nm_pelanggan LIKE ?
	   OR tlp_pelanggan LIKE ?
	   LIMIT 10");
	$stmt->execute(['%'.$_GET['query'].'%', '%'.$_GET['query'].'%']);
	  
	 $json = [];
	// $json2 = [];
	 while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
		  $json[] = $row['nm_pelanggan']."/".$row['tlp_pelanggan'];
		  //$json2[] = array('nm_pelanggan'=> $row['nm_pelanggan'],
				//	'tlp_pelanggan'=> $row['tlp_pelanggan'],
				//	'alamat_pelanggan'=> $row['alamat_pelanggan']);
	 }

	 echo json_encode($json);

?>
