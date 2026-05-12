<?php
    include "../../../configurasi/koneksi.php";
    
    $id_detail = $_POST['idbundle_detail'];
	
	$hapus_bundle_detail = $db->prepare("DELETE FROM bundle_detail WHERE idbundle_detail = ?");
	$hapus_bundle_detail->execute([$id_detail]);
	
	$data = array(
	    "status"    => "success",
	    "data"      => "Berhasil hapus data"
	);
	echo json_encode($data);
?>