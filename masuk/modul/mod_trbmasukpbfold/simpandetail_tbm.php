<?php
include "../../../configurasi/koneksi.php";

$kd_trbmasuk        = $_POST['kd_trbmasuk1'];
$kd_orders          = $_POST['kd_trbmasuk'];
$id_barang          = $_POST['id_barang'];
$kd_barang          = $_POST['kd_barang'];
$nmbrg_dtrbmasuk    = $_POST['nmbrg_dtrbmasuk'];
$qty_dtrbmasuk      = $_POST['qty_dtrbmasuk'];
$sat_dtrbmasuk      = $_POST['sat_dtrbmasuk'];
$hnasat_dtrbmasuk   = $_POST['hnasat_dtrbmasuk'];
$hrgjual_dtrbmasuk  = $_POST['hrgjual_dtrbmasuk'];
$diskon             = $_POST['diskon'];
$konversi           = $_POST['konversi'];
// $hrgsat_dtrbmasuk = $hnasat_dtrbmasuk * 1.11 ;
$hrgsat_dtrbmasuk   = round($hnasat_dtrbmasuk * (1-($diskon/100))/$konversi);
// $hrgsat_dtrbmasuk   = round($hnasat_dtrbmasuk * (1-($diskon/100)) * 1.11/$konversi,0);
// $hnappn             = $hnasat_dtrbmasuk * 1.11/$konversi;

$no_batch           = $_POST['no_batch'];
//$exp_date = date('Y-m-d', strtotime($_POST['exp_date']));

if ($_POST['exp_date']=='')
{ $tgl_awal = date('Y-m-d');
    $exp_date=date('Y-m-d', strtotime('+720 days', strtotime( $tgl_awal)));
}
else {
    $exp_date = $_POST['exp_date'];
}

if($qty_dtrbmasuk == ""){
    $qty_dtrbmasuk = "1";
}else{}
if($diskon == ""){
    $diskon = "0";
}else{}


//cek apakah barang sudah ada
$cekdetail=mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM trbmasuk_detail 
WHERE kd_barang='$kd_barang' AND kd_trbmasuk='$kd_trbmasuk' AND no_batch='$no_batch'");

$ketemucekdetail=mysqli_num_rows($cekdetail);
$rcek=mysqli_fetch_array($cekdetail);
if ($ketemucekdetail > 0){
    
    $faktordiskon = (1-($diskon/100));
    
    $id_dtrbmasuk = $rcek['id_dtrbmasuk'];
    $qtylama = $rcek['qty_dtrbmasuk'];
    $qty_grosirlama = $rcek['qty_grosir'];
    $qty_grosir = $qty_grosirlama + $qty_dtrbmasuk;
    $ttlqty = $qtylama + ($qty_dtrbmasuk*$konversi) ;
    // $ttlharga = $ttlqty * $hnasat_dtrbmasuk;
    $ttlharga = $qty_grosir * $hnasat_dtrbmasuk * $faktordiskon * 1.11;

    mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE trbmasuk_detail SET 
                                        qty_dtrbmasuk = '$ttlqty',
                                        qty_grosir = '$qty_grosir',
										hnasat_dtrbmasuk = '$hnasat_dtrbmasuk',
										diskon = '$diskon',
										hrgsat_dtrbmasuk = '$hrgsat_dtrbmasuk',										
										hrgjual_dtrbmasuk = '$hrgjual_dtrbmasuk',										
										hrgttl_dtrbmasuk = '$ttlharga',
										no_batch = '$no_batch',
										exp_date = '$exp_date'
										WHERE id_dtrbmasuk = '$id_dtrbmasuk'");

    //update stok
    $cekstok=mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM barang 
        WHERE id_barang='$id_barang'");
    $rst=mysqli_fetch_array($cekstok);

    $stok_barang = $rst['stok_barang'];
    $stokakhir = (($stok_barang - $qtylama) + $ttlqty);

    $hrgjual_barang=round($hrgjual_dtrbmasuk) ;
    
    

    mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE barang SET 
                                          stok_barang = '$stokakhir', 
                                          hna = '$hnasat_dtrbmasuk',
                                          hrgsat_barang = '$hrgsat_dtrbmasuk',
                                          hrgjual_barang='$hrgjual_barang'
                                          WHERE id_barang = '$id_barang'");

}else{
    $faktordiskon = (1-($diskon/100));
    $ttlharga = $qty_dtrbmasuk * $hnasat_dtrbmasuk * $faktordiskon * 1.11;
    $qty_retail = $qty_dtrbmasuk * $konversi;
    $tipe = 1;
    
    $cekstok=mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM barang 
        WHERE id_barang='$id_barang'");
    $rst=mysqli_fetch_array($cekstok);
    
    mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO trbmasuk_detail(
                                        kd_trbmasuk,
                                        kd_orders,
										id_barang,
										kd_barang,
										nmbrg_dtrbmasuk,
										qty_dtrbmasuk,
										qty_grosir,
										sat_dtrbmasuk,
										satgrosir_dtrbmasuk,
										konversi,
										hnasat_dtrbmasuk,
										diskon,
										hrgsat_dtrbmasuk,										
										hrgjual_dtrbmasuk,										
										hrgttl_dtrbmasuk,
										no_batch,
										exp_date,
										tipe)
								  VALUES('$kd_trbmasuk',
								        '$kd_orders',
										'$id_barang',
										'$kd_barang',
										'$nmbrg_dtrbmasuk',
										'$qty_retail',
										'$qty_dtrbmasuk',
										'$rst[sat_barang]',
										'$rst[sat_grosir]',
										'$konversi',
										'$hnasat_dtrbmasuk',
										'$diskon',
										'$hrgsat_dtrbmasuk',
										'$hrgjual_dtrbmasuk',
										'$ttlharga',
										'$no_batch',
										'$exp_date',
										'$tipe'										
										)");

    // mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO ordersdetail(
    //                                     kd_trbmasuk,
				// 						id_barang,
				// 						kd_barang,
				// 						nmbrg_dtrbmasuk,
				// 						qty_dtrbmasuk,
				// 						qtygrosir_dtrbmasuk,
				// 						sat_dtrbmasuk,
				// 						satgrosir_dtrbmasuk,
				// 						konversi,
				// 						hnasat_dtrbmasuk,
				// 						diskon,
				// 						hrgsat_dtrbmasuk,										
				// 						hrgjual_dtrbmasuk,										
				// 						hrgttl_dtrbmasuk,
				// 						no_batch,
				// 						exp_date)
				// 				  VALUES('$kd_orders',
				// 						'$id_barang',
				// 						'$kd_barang',
				// 						'$nmbrg_dtrbmasuk',
				// 						'$qty_retail',
				// 						'$qty_dtrbmasuk',
				// 						'$rst[sat_barang]',
				// 						'$rst[sat_grosir]',
				// 						'$konversi',
				// 						'$hnasat_dtrbmasuk',
				// 						'$diskon',
				// 						'$hrgsat_dtrbmasuk',
				// 						'$hrgjual_dtrbmasuk',
				// 						'$ttlharga',
				// 						'$no_batch',
				// 						'$exp_date'									
				// 						)");
//update stok,hna,hrgbrg+ppn
   

    $stok_barang = $rst['stok_barang'];
    $stokakhir = $stok_barang + ($qty_dtrbmasuk*$konversi);
    $hrgjual_barang=round($hrgjual_dtrbmasuk) ;
    

    mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE barang SET 
                                                stok_barang = '$stokakhir',
                                                hna = '$hnasat_dtrbmasuk',
                                                konversi = '$konversi',
                                                sat_grosir = '$sat_dtrbmasuk',
                                                hrgsat_barang = '$hrgsat_dtrbmasuk',
                                                hrgjual_barang='$hrgjual_barang'
                                                WHERE id_barang = '$id_barang'");

}

?>
