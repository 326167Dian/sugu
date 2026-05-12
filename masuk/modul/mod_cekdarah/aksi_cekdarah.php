<?php
session_start();
 if (empty($_SESSION['username']) AND empty($_SESSION['passuser'])){
  echo "<link href='style.css' rel='stylesheet' type='text/css'>
 <center>Untuk mengakses modul, Anda harus login <br>";
  echo "<a href=../../index.php><b>LOGIN</b></a></center>";
}
else{
include "../../../configurasi/koneksi.php";
include "../../../configurasi/fungsi_thumb.php";
include "../../../configurasi/library.php";

$module=$_GET['module'];
$act=$_GET['act'];


// Input admin
if ($module=='cekdarah' AND $act=='input_cekdarah'){

    $stmt = $db->prepare("INSERT INTO cekdarah
                                        (id_pelanggan,
                                        gula,
                                        asamurat,
                                        kolesterol,
                                        tensi,
                                        petugas,
                                        waktu)
								 VALUES(?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$_POST['id_pelanggan'], $_POST['gula'], $_POST['asamurat'], $_POST['kolesterol'], $_POST['tensi'], $_POST['petugas'], $_POST['waktu']]);
										
										
	//echo "<script type='text/javascript'>alert('Data berhasil ditambahkan !');window.location='../../media_admin.php?module=".$module."'</script>";
	header('location:../../media_admin.php?module='.$module);


}
 //updata satuan
 elseif ($module=='cekdarah' AND $act=='update_cekdarah'){
 
     $stmt = $db->prepare("UPDATE cekdarah SET 
                                        gula = ?, 
                                        asamurat = ?,
                                        kolesterol = ?,
                                        tensi = ?
									WHERE id_cekdarah = ?");
    $stmt->execute([$_POST['gula'], $_POST['asamurat'], $_POST['kolesterol'], $_POST['tensi'], $_POST['id']]);
									
	//echo "<script type='text/javascript'>alert('Data berhasil diubah !');window.location='../../media_admin.php?module=".$module."'</script>";
	header('location:../../media_admin.php?module='.$module);
	
}
//Hapus Proyek
elseif ($module=='cekdarah' AND $act=='hapus'){

  $stmt = $db->prepare("DELETE FROM cekdarah WHERE id_cekdarah = ?");
  $stmt->execute([$_GET['id']]);
  //echo "<script type='text/javascript'>alert('Data berhasil dihapus !');window.location='../../media_admin.php?module=".$module."'</script>";
  header('location:../../media_admin.php?module='.$module);
}

}
?>
