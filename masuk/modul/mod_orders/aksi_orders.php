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

$module= "orders";
$stt_aksi=$_POST['stt_aksi'];
if($stt_aksi == "input_trbmasuk" || $stt_aksi == "ubah_trbmasuk"){
$act=$stt_aksi;
}else{
$act=$_GET['act'];
}


// Input admin
if ($module=='orders' AND $act=='input_trbmasuk'){

    $stmt = $db->prepare("INSERT INTO 
										orders(id_resto,
										petugas,
										kd_trbmasuk,
										tgl_trbmasuk,
										id_supplier,
										nm_supplier,
										tlp_supplier,
										alamat_trbmasuk,
										ttl_trbmasuk,
										dp_bayar,
										sisa_bayar,
										ket_trbmasuk,
										tandatangan)
								 VALUES('pesan',
										?,
										?,
										?,
										?,
										?,
										?,
										?,
										?,
										?,
										?,
										?,
										?)");
	$stmt->execute([$_POST['petugas'], $_POST['kd_trbmasuk'], $_POST['tgl_trbmasuk'], $_POST['id_supplier'], $_POST['nm_supplier'], $_POST['tlp_supplier'], $_POST['alamat_trbmasuk'], $_POST['ttl_trkasir'], $_POST['dp_bayar'], $_POST['sisa_bayar'], $_POST['ket_trbmasuk'], $_POST['tandatangan']]);
										
	$stmt = $db->prepare("UPDATE kdbm SET stt_kdbm = 'OFF' WHERE id_admin = ? AND id_resto = 'pesan' AND kd_trbmasuk = ?");
	$stmt->execute([$_SESSION['id_admin'], $_POST['kd_trbmasuk']]);
										
										
	//echo "<script type='text/javascript'>alert('Transkasi berhasil ditambahkan !');window.location='../../media_admin.php?module=".$module."'</script>";
	

}
 //updata trbmasuk
 elseif ($module=='orders' AND $act=='ubah_trbmasuk'){
 

    $stmt = $db->prepare("UPDATE orders SET tgl_trbmasuk = ?,
									id_supplier = ?,
									nm_supplier = ?,
									tlp_supplier = ?,
									alamat_trbmasuk = ?,
									ttl_trbmasuk = ?,
									dp_bayar = ?,
									sisa_bayar = ?,
									ket_trbmasuk = ?,
									tandatangan = ?
									WHERE id_trbmasuk = ?");
	$stmt->execute([$_POST['tgl_trbmasuk'], $_POST['id_supplier'], $_POST['nm_supplier'], $_POST['tlp_supplier'], $_POST['alamat_trbmasuk'], $_POST['ttl_trkasir'], $_POST['dp_bayar'], $_POST['sisa_bayar'], $_POST['ket_trbmasuk'], $_POST['tandatangan'], $_POST['id_trbmasuk']]);
										
	$stmt = $db->prepare("UPDATE kdbm SET stt_kdbm = 'OFF' WHERE id_admin = ? AND id_resto = 'pesan' AND kd_trbmasuk = ?");
	$stmt->execute([$_SESSION['id_admin'], $_POST['kd_trbmasuk']]);
										
	//echo "<script type='text/javascript'>alert('Transkasi berhasil Ubah !');window.location='../../media_admin.php?module=".$module."'</script>";
	
}
//Hapus Proyek
elseif ($module=='orders' AND $act=='hapus'){

  //update bagian stok dulu
  //ambil data induk
	$stmt = $db->prepare("SELECT id_trbmasuk, kd_trbmasuk FROM orders WHERE id_trbmasuk=?");
	$stmt->execute([$_GET['id']]);
	$r1 = $stmt->fetch(PDO::FETCH_ASSOC);
	$kd_trbmasuk = $r1['kd_trbmasuk'];
	
	//loop data detail
	//ambil data induk
	$stmt = $db->prepare("SELECT * FROM ordersdetail WHERE kd_trbmasuk=?");
	$stmt->execute([$kd_trbmasuk]);
	while ($r = $stmt->fetch(PDO::FETCH_ASSOC)){
	
	$id_dtrbmasuk = $r['id_dtrbmasuk'];
	$id_barang = $r['id_barang'];
	$qty_dtrbmasuk = $r['qty_dtrbmasuk'];

	//update stok
	$stmt2 = $db->prepare("SELECT id_barang, stok_barang FROM barang WHERE id_barang=?");
	$stmt2->execute([$id_barang]);
	$rst = $stmt2->fetch(PDO::FETCH_ASSOC);

	$stok_barang = $rst['stok_barang'];
	$stokakhir = $stok_barang;

	$stmt3 = $db->prepare("UPDATE barang SET stok_barang = ? WHERE id_barang = ?");
	$stmt3->execute([$stokakhir, $id_barang]);
	// Insert history
    $stmt4 = $db->prepare("INSERT INTO ordersdetail_hist(
                                                kd_trbmasuk,
                                                id_barang,
                                                kd_barang,
                                                nmbrg_dtrbmasuk,
                                                qty_dtrbmasuk,
                                                sat_dtrbmasuk,
                                                hnasat_dtrbmasuk,
                                                diskon,
                                                konversi,
                                                hrgsat_dtrbmasuk,
                                                hrgjual_dtrbmasuk,
                                                hrgttl_dtrbmasuk,
                                                qtygrosir_dtrbmasuk,
                                                satgrosir_dtrbmasuk,
                                                no_batch,
                                                exp_date,
                                                masuk
                                                )
                                            VALUES (
                                                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
                                                )");
    $stmt4->execute([$r['kd_trbmasuk'], $r['id_barang'], $r['kd_barang'], $r['nmbrg_dtrbmasuk'], $r['qty_dtrbmasuk'], $r['sat_dtrbmasuk'], $r['hnasat_dtrbmasuk'], $r['diskon'], $r['konversi'], $r['hrgsat_dtrbmasuk'], $r['hrgjual_dtrbmasuk'], $r['hrgttl_dtrbmasuk'], $r['qtygrosir_dtrbmasuk'], $r['satgrosir_dtrbmasuk'], $r['no_batch'], $r['exp_date'], $r['masuk']]);

	$stmt5 = $db->prepare("DELETE FROM ordersdetail WHERE id_dtrbmasuk = ?");
	$stmt5->execute([$id_dtrbmasuk]);
	
	}

  $stmt6 = $db->prepare("DELETE FROM orders WHERE id_trbmasuk = ?");
  $stmt6->execute([$_GET['id']]);
  
  echo "<script type='text/javascript'>alert('Data berhasil dihapus !');window.location='../../media_admin.php?module=".$module."'</script>";
}

}
?>
