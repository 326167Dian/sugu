<?php
// session_start(); // Sudah aktif di media_admin.php
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
if ($module=='barang' AND $act=='input_barang'){

$cekganda1 = $db->prepare("SELECT kd_barang FROM barang WHERE kd_barang=?");
$cekganda1->execute([$_POST['kd_barang']]);
$ada1 = $cekganda1->rowCount();
if ($ada1 > 0){
echo "<script type='text/javascript'>alert('Kode Barang sudah ada!');history.go(-1);</script>";
}else{

$cekganda = $db->prepare("SELECT nm_barang FROM barang WHERE nm_barang=?");
$cekganda->execute([$_POST['nm_barang']]);
$ada = $cekganda->rowCount();
if ($ada > 0){
echo "<script type='text/javascript'>alert('Nama Barang sudah ada!');history.go(-1);</script>";
}else{

$cekganda3 = $db->prepare("SELECT kd_barang, nm_barang, sat_barang FROM barang WHERE kd_barang=? AND nm_barang=? AND sat_barang=?");
$cekganda3->execute([$_POST['kd_barang'], $_POST['nm_barang'], $_POST['sat_barang']]);
$ada3 = $cekganda3->rowCount();
if ($ada3 > 0){
echo "<script type='text/javascript'>alert('Kode dengan Nama Barang dan Satuan ini sudah ada!');history.go(-1);</script>";
}else{

    $db->prepare("INSERT INTO barang(kd_barang,
										 nm_barang,
										 stok_barang,
										 stok_buffer,
										 sat_barang,
										 jenisobat,
										 hrgsat_barang,
										 hrgjual_barang,
										 tgl_expired,
										 ket_barang)
								 VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")->execute([$_POST['kd_barang'], $_POST['nm_barang'], $_POST['stok_barang'], $_POST['stok_buffer'], $_POST['sat_barang'], $_POST['jenisobat'], $_POST['hrgsat_barang'], $_POST['hrgjual_barang'], $_POST['tgl_expired'], $_POST['ket_barang']]);
										
	//echo "<script type='text/javascript'>alert('Data berhasil ditambahkan !');window.location='../../media_admin.php?module=".$module."'</script>";
	header('location:../../media_admin.php?module='.$module);

}}
}
}
 //update barang
 elseif ($module=='barang' AND $act=='update_barang'){
 
     $db->prepare("UPDATE barang SET kd_barang = ?,
									nm_barang = ?,
									stok_barang = ?,
									stok_buffer = ?,
									sat_barang = ?,
									jenisobat = ?,
									hrgsat_barang = ?,
									hrgjual_barang = ?,
									tgl_expired = ?,
									ket_barang = ?
									WHERE id_barang = ?")->execute([$_POST['kd_barang'], $_POST['nm_barang'], $_POST['stok_barang'], $_POST['stok_buffer'], $_POST['sat_barang'], $_POST['jenisobat'], $_POST['hrgsat_barang'], $_POST['hrgjual_barang'], $_POST['tgl_expired'], $_POST['ket_barang'], $_POST['id']]);
									
	//echo "<script type='text/javascript'>alert('Data berhasil diubah !');window.location='../../media_admin.php?module=".$module."'</script>";
	header('location:../../media_admin.php?module='.$module);
	
}
//Hapus Proyek
elseif ($module=='barang' AND $act=='hapus'){

  $db->prepare("DELETE FROM barang WHERE id_barang = ?")->execute([$_GET['id']]);
  //echo "<script type='text/javascript'>alert('Data berhasil dihapus !');window.location='../../media_admin.php?module=".$module."'</script>";
  header('location:../../media_admin.php?module='.$module);
}

}
?>
