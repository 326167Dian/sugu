<?php
session_start();
if (empty($_SESSION['username']) and empty($_SESSION['passuser'])) {
	echo "<link href='style.css' rel='stylesheet' type='text/css'>
 <center>Untuk mengakses modul, Anda harus login <br>";
	echo "<a href=../../index.php><b>LOGIN</b></a></center>";
} else {
    include "../../../configurasi/koneksi.php";
    include "../../../configurasi/fungsi_generate_kode.php";
    
    
	$module = $_GET['module'];
	$act = $_GET['act'];
	
	// Insert Bundle
	if ($module == 'bundle' and $act == 'input_bundle') {
	    // Bundle
        $nm_bundle      = $_POST['nm_bundle'];
        $sat_bundle     = $_POST['sat_bundle'];
        $qty_bundle     = str_replace(".","",$_POST['qty_bundle']);
        $hrgjual_bundle = str_replace(".","",$_POST['hrgjual_bundle']);
        $created_at     = date('Y-m-d H:i:s', time());
        $petugas        = $_SESSION['namalengkap'];
        $kd_bundle      = get_kode_bundle();
        
        // Bundle Detail
        $kd_barang      = $_POST['obat_kd'];
        $nm_barang      = $_POST['obat_nama'];
        $qty_barang     = $_POST['qty'];
        $sat_barang     = $_POST['sat_barang'];
        $hrgjual_barang = str_replace(".","",$_POST['hrgjual']);
        $subtotal_brg   = str_replace(".","",$_POST['subtotal']);
        $count_item     = count($kd_barang); 
       
    	$stmt_bundle = $db->prepare("SELECT * FROM bundle WHERE nm_bundle = ?");
    	$stmt_bundle->execute([$nm_bundle]);
    	$count = $stmt_bundle->rowCount();
    	    
	    if ($count > 0) {
	        $updatebundle   = $db->prepare("UPDATE bundle SET
    	                                    nm_bundle       = ?,
    	                                    sat_bundle      = ?,
    	                                    qty_bundle      = ?,
    	                                    hrgjual_bundle  = ?,
    	                                    petugas         = ?,
    	                                    update_at       = ?
    	                                WHERE kd_bundle = ?");
        	$updatebundle->execute([$nm_bundle,$sat_bundle,$qty_bundle,$hrgjual_bundle,$petugas,$update_at,$kd_bundle]);
        	
        	$getbundledetail = $db->prepare("SELECT * FROM bundle_detail WHERE kd_bundle = ? AND kd_barang = ?");
        	
        	$updatebundledetail = $db->prepare("UPDATE bundle_detail SET
        	                                                qty_barang      = ?,
        	                                                hrgjual_barang  = ?,
        	                                                subtotal        = ?,
        	                                                update_at       = ?
        	                                           WHERE kd_bundle = ?
        	                                           AND kd_barang = ?");
        	
        	$insertbundledetail = $db->prepare("INSERT INTO bundle_detail(kd_bundle, kd_barang, nm_barang, qty_barang, sat_barang, hrgjual_barang, subtotal, created_at) VALUES (?,?,?,?,?,?,?,?)");
        	
        	for ($i = 0; $i < $count_item; $i++) {
        	    $getbundledetail->execute([$kd_bundle, $kd_barang[$i]]);
        	    
        	    if ($getbundledetail->rowCount() > 0) {
        	        $updatebundledetail->execute([$qty_barang[$i], $hrgjual_barang[$i], $subtotal_brg[$i], $created_at, $kd_bundle, $kd_barang[$i]]);
        	    } else {
        	        
        	        $insertbundledetail->execute([$kd_bundle, $kd_barang[$i], $nm_barang[$i], $qty_barang[$i], $sat_barang[$i], $hrgjual_barang[$i], $subtotal_brg[$i], $created_at]);
        	    }
        	}
    	} else {
    	    $insertbundle = $db->prepare("INSERT INTO bundle(kd_bundle,nm_bundle,sat_bundle,qty_bundle,hrgjual_bundle,petugas,created_at)
    	                                        VALUES (?,?,?,?,?,?,?)");
    	    $insertbundle->execute([$kd_bundle, $nm_bundle, $sat_bundle, $qty_bundle, $hrgjual_bundle, $petugas, $created_at]);
    	    
    	    $insertbundledetail = $db->prepare("INSERT INTO bundle_detail(kd_bundle, kd_barang, nm_barang, qty_barang, sat_barang, hrgjual_barang, subtotal, created_at) VALUES (?,?,?,?,?,?,?,?)");
    	    for ($i = 0; $i < $count_item; $i++) {
    	        $insertbundledetail->execute([$kd_bundle, $kd_barang[$i], $nm_barang[$i], $qty_barang[$i], $sat_barang[$i], $hrgjual_barang[$i], $subtotal_brg[$i], $created_at]);
    	    }
    	}
    	
    	header('location:../../media_admin.php?module=' . $module);
	} 
    // Update Bundle	
	elseif ($module == 'bundle' and $act == 'update_bundle') {
	    // Bundle
        $nm_bundle      = $_POST['nm_bundle'];
        $sat_bundle     = $_POST['sat_bundle'];
        $qty_bundle     = str_replace(".","",$_POST['qty_bundle']);
        $hrgjual_bundle = str_replace(".","",$_POST['hrgjual_bundle']);
        $update_at     = date('Y-m-d H:i:s', time());
        $petugas        = $_SESSION['namalengkap'];
        $kd_bundle      = $_POST['kd_bundle'];
        
        // Bundle Detail
        $kd_barang      = $_POST['obat_kd'];
        $nm_barang      = $_POST['obat_nama'];
        $qty_barang     = $_POST['qty'];
        $sat_barang     = $_POST['sat_barang'];
        $hrgjual_barang = str_replace(".","",$_POST['hrgjual']);
        $subtotal_brg   = str_replace(".","",$_POST['subtotal']);
        $count_item     = count($kd_barang); 
        
    	$updatebundle   = $db->prepare("UPDATE bundle SET
    	                                    nm_bundle       = ?,
    	                                    sat_bundle      = ?,
    	                                    qty_bundle      = ?,
    	                                    hrgjual_bundle  = ?,
    	                                    petugas         = ?,
    	                                    update_at       = ?
    	                                WHERE kd_bundle = ?");
    	$updatebundle->execute([$nm_bundle,$sat_bundle,$qty_bundle,$hrgjual_bundle,$petugas,$update_at,$kd_bundle]);
    	
    	$getbundledetail = $db->prepare("SELECT * FROM bundle_detail WHERE kd_bundle = ? AND kd_barang = ?");
    	
    	$updatebundledetail = $db->prepare("UPDATE bundle_detail SET
    	                                                qty_barang      = ?,
    	                                                hrgjual_barang  = ?,
    	                                                subtotal        = ?,
    	                                                update_at       = ?
    	                                           WHERE kd_bundle = ?
    	                                           AND kd_barang = ?");
    	
    	$insertbundledetail = $db->prepare("INSERT INTO bundle_detail(kd_bundle, kd_barang, nm_barang, qty_barang, sat_barang, hrgjual_barang, subtotal, created_at) VALUES (?,?,?,?,?,?,?,?)");
    	
    	for ($i = 0; $i < $count_item; $i++) {
    	    $getbundledetail->execute([$kd_bundle, $kd_barang[$i]]);
    	   
    	    if ($getbundledetail->rowCount() > 0) {
    	        $updatebundledetail->execute([$qty_barang[$i], $hrgjual_barang[$i], $subtotal_brg[$i], $update_at, $kd_bundle, $kd_barang[$i]]);
    	    } else {
    	        
    	        $insertbundledetail->execute([$kd_bundle, $kd_barang[$i], $nm_barang[$i], $qty_barang[$i], $sat_barang[$i], $hrgjual_barang[$i], $subtotal_brg[$i], $update_at]);
    	    }
    	}	 
    	
    	header('location:../../media_admin.php?module=' . $module);
	}
	// Hapus Bundle
	elseif ($module == 'bundle' and $act == 'hapus_bundle') {
	    $id_bundle = $_GET['id'];
	    $stmt_bundle = $db->prepare("SELECT kd_bundle FROM bundle WHERE id_bundle = :id_bundle");
	    $stmt_bundle->execute([
	        ':id_bundle'    => $id_bundle
	    ]);
	    $rbundle = $stmt_bundle->fetch(PDO::FETCH_ASSOC);
	    
	    $del_bundle_detail = $db->prepare("DELETE FROM bundle_detail WHERE kd_bundle = :kd_bundle");
	    $del_bundle_detail->execute([
	        ':kd_bundle'    => $rbundle['kd_bundle']
	    ]);
	    
	    $del_bundle = $db->prepare("DELETE FROM bundle WHERE id_bundle = :id_bundle");
	    $del_bundle->execute([
	        ':id_bundle' => $id_bundle
	    ]);
	    
	    header('location:../../media_admin.php?module=' . $module);
	}
	
}
?>