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
if ($module=='satuan' AND $act=='input_satuan'){

$stmt = $db->prepare("SELECT COUNT(*) FROM satuan WHERE nm_satuan = ?");
$stmt->execute([$_POST['nm_satuan']]);
$ada = $stmt->fetchColumn();
if ($ada > 0){
echo "<script type='text/javascript'>alert('Satuan sudah ada!');history.go(-1);</script>";
}else{

    $stmt = $db->prepare("INSERT INTO satuan(nm_satuan,deskripsi) VALUES(?, ?)");
    $stmt->execute([$_POST['nm_satuan'], $_POST['deskripsi']]);
										
										
	//echo "<script type='text/javascript'>alert('Data berhasil ditambahkan !');window.location='../../media_admin.php?module=".$module."'</script>";
	header('location:../../media_admin.php?module='.$module);

}
}
 //updata satuan
 elseif ($module=='satuan' AND $act=='update_satuan'){
 
     $stmt = $db->prepare("UPDATE satuan SET nm_satuan = ?, deskripsi = ? WHERE id_satuan = ?");
     $stmt->execute([$_POST['nm_satuan'], $_POST['deskripsi'], $_POST['id']]);
									
	//echo "<script type='text/javascript'>alert('Data berhasil diubah !');window.location='../../media_admin.php?module=".$module."'</script>";
	header('location:../../media_admin.php?module='.$module);
	
}
//Hapus Proyek
elseif ($module=='satuan' AND $act=='hapus'){

  $stmt = $db->prepare("DELETE FROM satuan WHERE id_satuan = ?");
  $stmt->execute([$_GET['id']]);
  //echo "<script type='text/javascript'>alert('Data berhasil dihapus !');window.location='../../media_admin.php?module=".$module."'</script>";
  header('location:../../media_admin.php?module='.$module);
}

}
?>
