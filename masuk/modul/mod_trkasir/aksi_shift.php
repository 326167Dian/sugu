<?php
error_reporting(0);
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

$module= "trkasir";
$stt_aksi=$_POST['stt_aksi'];
if($stt_aksi == "buka_shift" || $stt_aksi == "tutup_shift"){
$act=$stt_aksi;
}else{
$act=$_GET['act'];
}

// Input admin
if ($module=='trkasir' AND $act=='buka_shift'){


    $db->prepare("INSERT INTO waktukerja(id_shift, petugas, jamkerja, peticash) VALUES (?, ?, ?, ?)")->execute([$_POST['id_shift'], $_POST['petugas'], $_POST['jamkerja'], $_POST['peticash']]);
										
	$db->exec("UPDATE waktukerja SET status = 'ON'");
																			
	//echo "<script type='text/javascript'>alert('Transkasi berhasil ditambahkan !');window.location='../../media_admin.php?module=".$module."'</script>";
}

 //updata trkasir
 elseif ($module=='trkasir' AND $act=='ubah_trkasir'){

    $db->prepare("UPDATE trkasir SET tgl_trkasir = ?, petugas = ?, nm_pelanggan = ?, tlp_pelanggan = ?, alamat_pelanggan = ?, ttl_trkasir = ?, diskon2 = ?, dp_bayar = ?, sisa_bayar = ?, ket_trkasir = ?, id_carabayar = ? WHERE id_trkasir = ?")->execute([$_POST['tgl_trkasir'], $_POST['petugas'], $_POST['nm_pelanggan'], $_POST['tlp_pelanggan'], $_POST['alamat_pelanggan'], $_POST['ttl_trkasir'], $_POST['diskon2'], $_POST['dp_bayar'], $_POST['sisa_bayar'], $_POST['ket_trkasir'], $_POST['id_carabayar'], $_POST['id_trkasir']]);
										
										
	//echo "<script type='text/javascript'>alert('Transkasi berhasil Ubah !');window.location='../../media_admin.php?module=".$module."'</script>";
	
 
}
//Hapus Proyek
elseif ($module=='trkasir' AND $act=='hapus'){

  //update bagian stok dulu
  //ambil data induk
	$ambildatainduk = $db->prepare("SELECT id_trkasir, kd_trkasir FROM trkasir WHERE id_trkasir = ?");
	$ambildatainduk->execute([$_GET['id']]);
	$r1 = $ambildatainduk->fetch(PDO::FETCH_ASSOC);
	$kd_trkasir = $r1['kd_trkasir'];
	
	//loop data detail
	$ambildatadetail = $db->prepare("SELECT id_dtrkasir, kd_trkasir, id_barang, qty_dtrkasir FROM trkasir_detail WHERE kd_trkasir = ?");
	$ambildatadetail->execute([$kd_trkasir]);
	while ($r = $ambildatadetail->fetch(PDO::FETCH_ASSOC)){
	
	$id_dtrkasir = $r['id_dtrkasir'];
	$id_barang = $r['id_barang'];
	$qty_dtrkasir = $r['qty_dtrkasir'];

	//update stok
	
		$cekstok = $db->prepare("SELECT id_barang, stok_barang FROM barang WHERE id_barang = ?");
		$cekstok->execute([$id_barang]);
		$rst = $cekstok->fetch(PDO::FETCH_ASSOC);

		$stok_barang = $rst['stok_barang'];
		$stokakhir = $stok_barang + $qty_dtrkasir;

		$db->prepare("UPDATE barang SET stok_barang = ? WHERE id_barang = ?")->execute([$stokakhir, $id_barang]);
	
	
	$db->prepare("DELETE FROM trkasir_detail WHERE id_dtrkasir = ?")->execute([$id_dtrkasir]);
	
	}

  $db->prepare("DELETE FROM trkasir WHERE id_trkasir = ?")->execute([$_GET['id']]);
  
echo "<script type='text/javascript'>alert('Data berhasil dihapus !');window.location='../../media_admin.php?module=".$module."'</script>";
}

}
?>
