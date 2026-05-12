<?php
session_start();
if (empty($_SESSION['username']) and empty($_SESSION['passuser'])) {
	echo "<link href='style.css' rel='stylesheet' type='text/css'>
 <center>Untuk mengakses modul, Anda harus login <br>";
	echo "<a href=../../index.php><b>LOGIN</b></a></center>";
} else {

	include "../../../configurasi/koneksi.php";
	include "../../../configurasi/fungsi_thumb.php";
	include "../../../configurasi/library.php";


	$kd_barang = $_POST['kd_barang'];
	$id_barang = $_POST['id_barang'];
	$hrgsat_barang = $_POST['hrgsat_barang'];
	$shift = $_POST['shift'];
	$harini = date('ymdHis');
    
    
	// $stok_sistem = $_POST['stok_sistem'];
	$stok_fisik = $_POST['stok_fisik'];
	$exp_date = (empty($_POST['exp_date'])?'0000-00-00':$_POST['exp_date']);
	$jml = (empty($_POST['jml'])? '0':$_POST['jml']);

    // print_r($exp_date);
    // die();
	$beli = "SELECT trbmasuk.tgl_trbmasuk,                                           
                                       SUM(trbmasuk_detail.qty_dtrbmasuk) AS totalbeli                                            
                                       FROM trbmasuk_detail join trbmasuk 
                                       on (trbmasuk_detail.kd_trbmasuk=trbmasuk.kd_trbmasuk)
                                       WHERE id_barang = ?";

	$stmt = $db->prepare($beli);
	$stmt->execute([$id_barang]);
	$buy2 = $stmt->fetch(PDO::FETCH_ASSOC);
    
    
	$jual = "SELECT trkasir.tgl_trkasir,                                
                                        sum(trkasir_detail.qty_dtrkasir) AS totaljual
                                        FROM trkasir_detail join trkasir 
                                        on (trkasir_detail.kd_trkasir=trkasir.kd_trkasir)
                                        WHERE id_barang = ?";

	$stmt2 = $db->prepare($jual);
	$stmt2->execute([$id_barang]);
	$sell = $stmt2->fetch(PDO::FETCH_ASSOC);
	$selisih = $buy2['totalbeli'] - $sell['totaljual'];
    
	$sisa = $stok_fisik - $selisih;
	$total_sisa = $sisa * $hrgsat_barang;
	$tgl_stok = date('Y-m-d', strtotime($_POST['tgl_awal']));
    
	$stmt3 = $db->prepare("INSERT INTO stok_opname(id_barang,
										 kd_barang,
										 stok_sistem,
										 stok_fisik,
										 exp_date,
										 jml,
										 selisih,
										 hrgsat_barang,
										 ttl_hrgbrg,
										 tgl_current,
										 tgl_stokopname,
										 shift,
										 id_admin)			
								 VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
	$stmt3->execute([$id_barang, $kd_barang, $selisih, $stok_fisik, $exp_date, $jml, $sisa, $hrgsat_barang, $total_sisa, $harini, $tgl_stok, $shift, $_SESSION['idadmin']]);
}
