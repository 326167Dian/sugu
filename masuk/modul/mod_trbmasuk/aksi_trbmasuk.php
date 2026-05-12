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

$module= "trbmasuk";
$stt_aksi=$_POST['stt_aksi'];
if($stt_aksi == "input_trbmasuk" || $stt_aksi == "ubah_trbmasuk"){
$act=$stt_aksi;
}else{
$act=$_GET['act'];
}


// Input admin
if ($module=='trbmasuk' AND $act=='input_trbmasuk'){

    $stmt = $db->prepare("INSERT INTO trbmasuk(id_resto,
										kd_trbmasuk,
										tgl_trbmasuk,
										id_supplier,
										petugas,
										nm_supplier,
										tlp_supplier,
										alamat_trbmasuk,
										ttl_trbmasuk,
										dp_bayar,
										sisa_bayar,
										ket_trbmasuk,
										carabayar,
										jenis)
								 VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute(['pusat', $_POST['kd_trbmasuk'], $_POST['tgl_trbmasuk'], $_POST['id_supplier'], $_POST['petugas'], $_POST['nm_supplier'], $_POST['tlp_supplier'], $_POST['alamat_trbmasuk'], $_POST['ttl_trkasir'], $_POST['dp_bayar'], $_POST['sisa_bayar'], $_POST['ket_trbmasuk'], $_POST['carabayar'], 'nonpbf']);
										
	$tgl_sekarang = date('Y-m-d H:i:s', time());									
	$stmt2 = $db->prepare("INSERT INTO kartu_stok(kode_transaksi, tgl_sekarang) VALUES(?,?)");
	$stmt2->execute([$_POST['kd_trbmasuk'], $tgl_sekarang]);
	
	$stmt3 = $db->prepare("UPDATE kdbm SET stt_kdbm = 'OFF' WHERE id_admin = ? AND id_resto = 'pusat' AND kd_trbmasuk = ?");
	$stmt3->execute([$_SESSION['id_admin'], $_POST['kd_trbmasuk']]);
										
										
	//echo "<script type='text/javascript'>alert('Transkasi berhasil ditambahkan !');window.location='../../media_admin.php?module=".$module."'</script>";
	

}
 //updata trbmasuk
 elseif ($module=='trbmasuk' AND $act=='ubah_trbmasuk'){
 

    $stmt = $db->prepare("UPDATE trbmasuk SET tgl_trbmasuk = ?,
									id_supplier = ?,
									nm_supplier = ?,
									tlp_supplier = ?,
									alamat_trbmasuk = ?,
									ttl_trbmasuk = ?,
									dp_bayar = ?,
									sisa_bayar = ?,
									ket_trbmasuk = ?,
									carabayar = ?
									WHERE id_trbmasuk = ?");
    $stmt->execute([$_POST['tgl_trbmasuk'], $_POST['id_supplier'], $_POST['nm_supplier'], $_POST['tlp_supplier'], $_POST['alamat_trbmasuk'], $_POST['ttl_trkasir'], $_POST['dp_bayar'], $_POST['sisa_bayar'], $_POST['ket_trbmasuk'], $_POST['carabayar'], $_POST['id_trbmasuk']]);
										
										
	//echo "<script type='text/javascript'>alert('Transkasi berhasil Ubah !');window.location='../../media_admin.php?module=".$module."'</script>";
	
}
//Hapus Proyek
elseif ($module=='trbmasuk' AND $act=='hapus'){

  //update bagian stok dulu
  //ambil data induk
	$ambildatainduk = $db->prepare("SELECT id_trbmasuk, kd_trbmasuk FROM trbmasuk 
	WHERE id_trbmasuk=?");
	$ambildatainduk->execute([$_GET['id']]);
	$r1 = $ambildatainduk->fetch(PDO::FETCH_ASSOC);
	$kd_trbmasuk = $r1['kd_trbmasuk'];
	
	//loop data detail
	//ambil data induk
	$ambildatadetail = $db->prepare("SELECT * FROM trbmasuk_detail WHERE kd_trbmasuk=?");
	$ambildatadetail->execute([$kd_trbmasuk]);
	while ($r = $ambildatadetail->fetch(PDO::FETCH_ASSOC)){
	
	$id_dtrbmasuk = $r['id_dtrbmasuk'];
	$id_barang = $r['id_barang'];
	$qty_dtrbmasuk = $r['qty_dtrbmasuk'];

	//update stok
	$cekstok = $db->prepare("SELECT id_barang, stok_barang FROM barang 
	WHERE id_barang=?");
	$cekstok->execute([$id_barang]);
	$rst = $cekstok->fetch(PDO::FETCH_ASSOC);

	$stok_barang = $rst['stok_barang'];
	$stokakhir = $stok_barang - $qty_dtrbmasuk;

	$stmt4 = $db->prepare("UPDATE barang SET stok_barang = ? WHERE id_barang = ?");
	$stmt4->execute([$stokakhir, $id_barang]);
	// Insert History Deleted
    $stmt5 = $db->prepare("INSERT INTO trbmasuk_detail_hist (
                                                    kd_trbmasuk,
                                                    kd_orders,
                                                    id_barang,
                                                    kd_barang,
                                                    nmbrg_dtrbmasuk,
                                                    qty_dtrbmasuk,
                                                    sat_dtrbmasuk,
                                                    qty_grosir,
                                                    satgrosir_dtrbmasuk,
                                                    hnasat_dtrbmasuk,
                                                    diskon,
                                                    konversi,
                                                    hrgsat_dtrbmasuk,
                                                    hrgjual_dtrbmasuk,
                                                    hrgttl_dtrbmasuk,
                                                    no_batch,
                                                    exp_date,
                                                    waktu,
                                                    tipe
                                                    ) 
                                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt5->execute([$r['kd_trbmasuk'], $r['kd_orders'], $r['id_barang'], $r['kd_barang'], $r['nmbrg_dtrbmasuk'], $r['qty_dtrbmasuk'], $r['sat_dtrbmasuk'], $r['qty_grosir'], $r['satgrosir_dtrbmasuk'], $r['hnasat_dtrbmasuk'], $r['diskon'], $r['konversi'], $r['hrgsat_dtrbmasuk'], $r['hrgjual_dtrbmasuk'], $r['hrgttl_dtrbmasuk'], $r['no_batch'], $r['exp_date'], $r['waktu'], $r['tipe']]);
	$stmt6 = $db->prepare("DELETE FROM trbmasuk_detail WHERE id_dtrbmasuk = ?");
	$stmt6->execute([$id_dtrbmasuk]);
	$stmt7 = $db->prepare("DELETE FROM batch WHERE kd_transaksi = ? AND no_batch=? AND status = 'masuk'");
	$stmt7->execute([$r['kd_trbmasuk'], $r['no_batch']]);
	
	}

  $stmt8 = $db->prepare("DELETE FROM trbmasuk WHERE id_trbmasuk = ?");
  $stmt8->execute([$_GET['id']]);
  $stmt9 = $db->prepare("DELETE FROM kartu_stok WHERE kode_transaksi = ?");
  $stmt9->execute([$kd_trbmasuk]);
  
  $module2 = $_GET['module2'];
  echo "<script type='text/javascript'>alert('Data berhasil dihapus !');window.location='../../media_admin.php?module=".$module2."'</script>";
}

}
?>
