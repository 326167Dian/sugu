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

$module= "trbmasukpbf";
$stt_aksi=$_POST['stt_aksi'];
if($stt_aksi == "input_trbmasuk" || $stt_aksi == "ubah_trbmasuk" || $stt_aksi == "input_order_trbmasuk"){
$act=$stt_aksi;
}else{
$act=$_GET['act'];
}

$timestamp = date('Y-m-d H:i:s', time());

// Input admin
if ($module=='trbmasukpbf' AND $act=='input_trbmasuk'){

    if($_POST['carabayar'] == 'LUNAS'){
        $tgl_lunas      = date('Y-m-d', time());
        $petugas_lunas  = $_POST['petugas'];
    } else {
        $tgl_lunas      = '0000-00-00';
        $petugas_lunas  = '';
    }
    
    
    $db->prepare("INSERT INTO 
										trbmasuk(id_resto,
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
										jatuhtempo,
										carabayar,
										jenis,
										tgl_lunas,
										petugas_lunas)
								 VALUES('pusat',
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
										?,
										?,
										'pbf',
										?,
										?
										)")->execute([
											$_POST['kd_trbmasuk'],
											$_POST['tgl_trbmasuk'],
											$_POST['id_supplier'],
											$_POST['petugas'],
											$_POST['nm_supplier'],
											$_POST['tlp_supplier'],
											$_POST['alamat_trbmasuk'],
											$_POST['ttl_trkasir'],
											$_POST['dp_bayar'],
											$_POST['sisa_bayar'],
											$_POST['ket_trbmasuk'],
											$_POST['jatuhtempo'],
											$_POST['carabayar'],
											$tgl_lunas,
											$petugas_lunas
										]);
										
	$tgl_sekarang = date('Y-m-d H:i:s', time());
	$db->prepare("INSERT INTO kartu_stok(kode_transaksi, tgl_sekarang) VALUES(?,?)")->execute([$_POST['kd_trbmasuk'], $tgl_sekarang]);
	
	$db->prepare("UPDATE kdbm SET stt_kdbm = 'OFF' WHERE id_admin = ? AND id_resto = 'pusat' AND kd_trbmasuk = ?")->execute([$_SESSION['idadmin'], $_POST['kd_trbmasuk']]);
										
										
	//echo "<script type='text/javascript'>alert('Transkasi berhasil ditambahkan !');window.location='../../media_admin.php?module=".$module."'</script>";
	

}
 //updata trbmasukpbf
 elseif ($module=='trbmasukpbf' AND $act=='ubah_trbmasuk'){
 
    if($_POST['carabayar'] == 'LUNAS'){
        $tgl_lunas      = date('Y-m-d', time());
        $petugas_lunas  = $_POST['petugas'];
    } else {
        $tgl_lunas      = '0000-00-00';
        $petugas_lunas  = '';
    }
    
    
    $db->prepare("UPDATE trbmasuk SET tgl_trbmasuk = ?,
									id_supplier     = ?,
									nm_supplier     = ?,
									tlp_supplier    = ?,
									alamat_trbmasuk = ?,
									ttl_trbmasuk    = ?,
									dp_bayar        = ?,
									sisa_bayar      = ?,
									ket_trbmasuk    = ?,
									jatuhtempo      = ?,
									carabayar       = ?,
									tgl_lunas       = ?,
									petugas_lunas   = ?
									WHERE id_trbmasuk = ?")->execute([
										$_POST['tgl_trbmasuk'],
										$_POST['id_supplier'],
										$_POST['nm_supplier'],
										$_POST['tlp_supplier'],
										$_POST['alamat_trbmasuk'],
										$_POST['ttl_trkasir'],
										$_POST['dp_bayar'],
										$_POST['sisa_bayar'],
										$_POST['ket_trbmasuk'],
										$_POST['jatuhtempo'],
										$_POST['carabayar'],
										$tgl_lunas,
										$petugas_lunas,
										$_POST['id_trbmasuk']
									]);
										
										
	//echo "<script type='text/javascript'>alert('Transkasi berhasil Ubah !');window.location='../../media_admin.php?module=".$module."'</script>";
	
}
//Hapus Proyek
elseif ($module=='trbmasukpbf' AND $act=='hapus'){

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

	$db->prepare("UPDATE barang SET stok_barang = ? WHERE id_barang = ?")->execute([$stokakhir, $id_barang]);
	// Insert History Deleted
	$db->prepare("INSERT INTO trbmasuk_detail_hist (
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
                                                VALUES (
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
                                                    ?,
                                                    ?,
                                                    ?,
                                                    ?,
                                                    ?,
                                                    ?,
                                                    ?,
                                                    ?
                                                    )")->execute([
														$r['kd_trbmasuk'],
														$r['kd_orders'],
														$r['id_barang'],
														$r['kd_barang'],
														$r['nmbrg_dtrbmasuk'],
														$r['qty_dtrbmasuk'],
														$r['sat_dtrbmasuk'],
														$r['qty_grosir'],
														$r['satgrosir_dtrbmasuk'],
														$r['hnasat_dtrbmasuk'],
														$r['diskon'],
														$r['konversi'],
														$r['hrgsat_dtrbmasuk'],
														$r['hrgjual_dtrbmasuk'],
														$r['hrgttl_dtrbmasuk'],
														$r['no_batch'],
														$r['exp_date'],
														$r['waktu'],
														$r['tipe']
													]);
	$db->prepare("DELETE FROM trbmasuk_detail WHERE id_dtrbmasuk = ?")->execute([$id_dtrbmasuk]);
	
	}

  $db->prepare("DELETE FROM trbmasuk WHERE id_trbmasuk = ?")->execute([$_GET['id']]);
  $db->prepare("DELETE FROM kartu_stok WHERE kode_transaksi = ?")->execute([$kd_trbmasuk]);

    $module2 = $_GET['module2'];
  echo "<script type='text/javascript'>alert('Data berhasil dihapus !');window.location='../../media_admin.php?module=".$module2."'</script>";
}
//Input order trbmasuk
elseif ($module=='trbmasukpbf' AND $act=='input_order_trbmasuk'){
    
    if($_POST['carabayar'] == 'LUNAS'){
        $tgl_lunas      = date('Y-m-d', time());
        $petugas_lunas  = $_POST['petugas'];
    } else {
        $tgl_lunas      = '0000-00-00';
        $petugas_lunas  = '';
    }
    
    $db->prepare("INSERT INTO 
										trbmasuk(id_resto,
										kd_trbmasuk,
										kd_orders,
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
										jatuhtempo,
										carabayar,
										jenis,
										tgl_lunas,
										petugas_lunas)
								 VALUES('pusat',
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
										?,
										?,
										?,
										'pbf',
										?,
										?
										)")->execute([
											$_POST['kd_trbmasuk1'],
											$_POST['kd_trbmasuk'],
											$_POST['tgl_trbmasuk'],
											$_POST['id_supplier'],
											$_POST['petugas'],
											$_POST['nm_supplier'],
											$_POST['tlp_supplier'],
											$_POST['alamat_trbmasuk'],
											$_POST['ttl_trkasir'],
											$_POST['dp_bayar'],
											$_POST['sisa_bayar'],
											$_POST['ket_trbmasuk'],
											$_POST['jatuhtempo'],
											$_POST['carabayar'],
											$tgl_lunas,
											$petugas_lunas
										]);
	
	
// 	$ambildatadetail=mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM ordersdetail WHERE kd_trbmasuk='$_POST[kd_trbmasuk]'");
// 	while ($r=mysqli_fetch_array($ambildatadetail)){
	    
	   // $cekdetail=mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM barang 
    //         WHERE kd_barang='$r[kd_barang]' AND id_barang='$r[id_barang]'");
    //     $rcek=mysqli_fetch_array($cekdetail);
	    
// 	    mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO trbmasuk_detail(
//                                         kd_trbmasuk,
// 										id_barang,
// 										kd_barang,
// 										nmbrg_dtrbmasuk,
// 										qty_dtrbmasuk,
// 										qty_grosir,
// 										sat_dtrbmasuk,
// 										satgrosir_dtrbmasuk,
// 										konversi,
// 										hnasat_dtrbmasuk,
// 										diskon,
// 										hrgsat_dtrbmasuk,										
// 										hrgjual_dtrbmasuk,										
// 										hrgttl_dtrbmasuk,
// 										no_batch,
// 										exp_date,
// 										waktu)
// 								  VALUES('$_POST[kd_trbmasuk1]',
// 										'$r[id_barang]',
// 										'$r[kd_barang]',
// 										'$r[nmbrg_dtrbmasuk]',
// 										'$r[qty_dtrbmasuk]',
// 										'$r[qtygrosir_dtrbmasuk]',
// 										'$r[sat_dtrbmasuk]',
// 										'$r[satgrosir_dtrbmasuk]',
// 										'$r[konversi]',
// 										'$r[hnasat_dtrbmasuk]',
// 										'$r[diskon]',
// 										'$r[hrgsat_dtrbmasuk]',
// 										'$r[hrgjual_dtrbmasuk]',
// 										'$r[hrgttl_dtrbmasuk]',
// 										'$r[no_batch]',
// 										'$r[exp_date]',
// 										'$timestamp'										
// 										)");
										
		
		//update stok
        // $cekstok=mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM barang 
        //     WHERE id_barang='$r[id_barang]'");
        // $rst=mysqli_fetch_array($cekstok);
    
        // $stok_barang = $rcek['stok_barang'];
        // $stokakhir = $stok_barang + $r['qty_dtrbmasuk'];
    
        // $hrgjual_barang=round($r['hrgjual_dtrbmasuk']) ;
        // $hrgjual_barang1=round($r['hrgjual_dtrbmasuk']*1,0) ;
        // $hrgjual_barang3=round($r['hrgjual_dtrbmasuk']*1.22,0) ;
        
    
        // mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE barang SET 
        //                                       stok_barang = '$stokakhir', 
        //                                       hna = '$rcek[hna]',
        //                                       hrgsat_barang = '$r[sat_dtrbmasuk]',
        //                                       hrgjual_barang='$hrgjual_barang',
        //                                       hrgjual_barang1='$hrgjual_barang',
        //                                       hrgjual_barang3='$hrgjual_barang3'
        //                                       WHERE id_barang = '$r[id_barang]'");
								
// 	}	
	
	
	$db->prepare("INSERT INTO trx_orders(kd_trbmasuk, kd_orders) VALUES(?,?)")->execute([$_POST['kd_trbmasuk1'], $_POST['kd_trbmasuk']]);
	$db->prepare("INSERT INTO kartu_stok(kode_transaksi) VALUES(?)")->execute([$_POST['kd_trbmasuk1']]);
	
	
	$db->prepare("UPDATE kdbm SET stt_kdbm = 'OFF' WHERE id_admin = ? AND id_resto = 'pusat' AND kd_trbmasuk = ?")->execute([$_SESSION['idadmin'], $_POST['kd_trbmasuk1']]);
										
	
}
}
?>
