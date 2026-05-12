<?php
error_reporting(E_ALL);
include "../../../configurasi/koneksi.php";
$kd_trbmasuk        = $_POST['kd_trbmasuk1'];
$kd_orders          = $_POST['kd_trbmasuk'];
$id_barang          = $_POST['id_barang'];
$kd_barang          = $_POST['kd_barang'];
$nmbrg_dtrbmasuk    = $_POST['nmbrg_dtrbmasuk'];
$qty_dtrbmasuk      = $_POST['qty_dtrbmasuk'];
$sat_dtrbmasuk      = $_POST['sat_dtrbmasuk'];
$hnasat_dtrbmasuk   = isset($_POST['hnasat_dtrbmasuk'])?$_POST['hnasat_dtrbmasuk']:0;
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
}
if($diskon == ""){
    $diskon = "0";
}

// $daray = array(
//     'kode trbmasuk' => $kd_trbmasuk,
//     'kode orders'   => $kd_orders,
//     'id barang'     => $id_barang,
//     'kode barang'   => $kd_barang,
//     'nama barang'   => $nmbrg_dtrbmasuk,
//     'qty masuk'     => $qty_dtrbmasuk,
//     'satuan'        => $sat_dtrbmasuk,
//     'HNA'           => $hnasat_dtrbmasuk,
//     'Harga Satuan'  => $hrgsat_dtrbmasuk,
//     'Harga Jual'    => $hrgjual_dtrbmasuk,
//     'Diskon'        => $diskon,
//     'konversi'      => $konversi,
//     'Batch'         => $no_batch,
//     'Exp Date'      => $exp_date
// );
// print_r($daray); die();

//cek apakah barang sudah ada
// $cekdetail=mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM trbmasuk_detail 
// WHERE kd_barang='$kd_barang' AND kd_trbmasuk='$kd_trbmasuk' AND no_batch='$no_batch'");

// $ketemucekdetail=mysqli_num_rows($cekdetail);
// $rcek=mysqli_fetch_array($cekdetail);

$cekdetail = $db->prepare("SELECT * FROM trbmasuk_detail 
                            WHERE kd_barang = :kd_barang AND kd_trbmasuk = :kd_trbmasuk AND no_batch = :no_batch");
$cekdetail->execute([
    ':kd_barang'    => $kd_barang,
    ':kd_trbmasuk'  => $kd_trbmasuk,
    ':no_batch'     => $no_batch
]);

$ketemucekdetail = $cekdetail->rowCount();

if ($ketemucekdetail > 0){
    $rcek = $cekdetail->fetch(PDO::FETCH_ASSOC);
    
    $faktordiskon = (1-($diskon/100));
    
    $id_dtrbmasuk = $rcek['id_dtrbmasuk'];
    $qtylama = $rcek['qty_dtrbmasuk'];
    $qty_grosirlama = $rcek['qty_grosir'];
    $qty_grosir = $qty_grosirlama + $qty_dtrbmasuk;
    $ttlqty = $qtylama + ($qty_dtrbmasuk*$konversi) ;
    // $ttlharga = $ttlqty * $hnasat_dtrbmasuk;
    $ttlharga = $qty_grosir * $hnasat_dtrbmasuk * $faktordiskon * 1.11;

    // mysqli_query($GLOBALS["___mysqli_ston"], "UPDATE trbmasuk_detail SET 
    //                                     qty_dtrbmasuk = '$ttlqty',
    //                                     qty_grosir = '$qty_grosir',
				// 						hnasat_dtrbmasuk = '$hnasat_dtrbmasuk',
				// 						diskon = '$diskon',
				// 						hrgsat_dtrbmasuk = '$hrgsat_dtrbmasuk',										
				// 						hrgjual_dtrbmasuk = '$hrgjual_dtrbmasuk',										
				// 						hrgttl_dtrbmasuk = '$ttlharga',
				// 						no_batch = '$no_batch',
				// 						exp_date = '$exp_date'
				// 						WHERE id_dtrbmasuk = '$id_dtrbmasuk'");
	$stmt_update_trbmasukdetail = $db->prepare("UPDATE trbmasuk_detail SET
	                qty_dtrbmasuk       = :ttlqty,
	                qty_grosir          = qty_grosir - :qty_grosir,
	                hnasat_dtrbmasuk    = :hnasat_dtrbmasuk,
	                diskon              = :diskon,
	                hrgsat_dtrbmasuk    = :hrgsat_dtrbmasuk,
	                hrgjual_dtrbmasuk   = :hrgjual_dtrbmasuk,
	                hrgttl_dtrbmasuk    = :hrgttl_dtrbmasuk,
	                no_batch            = :no_batch,
	                exp_date            = :exp_date
	                WHERE id_dtrbmasuk  = :id_dtrbmasuk");
	                
	$stmt_update_trbmasukdetail->execute([
	    ':ttlqty'               => $ttlqty, 
	    ':qty_grosir'           => $qty_dtrbmasuk, 
	    ':hnasat_dtrbmasuk'     => $hnasat_dtrbmasuk, 
	    ':diskon'               => $diskon, 
	    ':hrgsat_dtrbmasuk'     => $hrgsat_dtrbmasuk, 
	    ':hrgjual_dtrbmasuk'    => $hrgjual_dtrbmasuk, 
	    ':hrgttl_dtrbmasuk'     => $ttlharga, 
	    ':no_batch'             => $no_batch, 
	    ':exp_date'             => $exp_date, 
	    ':id_dtrbmasuk'         => $id_dtrbmasuk]);

    // UPDATE STOK ATOMIC - Menambah stok barang (barang sudah ada di detail)
    // Menggunakan single UPDATE statement untuk menghindari race condition
    $hrgjual_barang=round($hrgjual_dtrbmasuk) ;
    
    $stmt_update = $db->prepare("UPDATE barang SET 
                                          stok_barang = stok_barang - :qtylama + :ttlqty, 
                                          hna = :hnasat,
                                          hrgsat_barang = :hrgsat,
                                          hrgjual_barang=:hrgjual
                                          WHERE id_barang = :id_barang");
    $stmt_update->execute([
        ':qtylama' => $qtylama,
        ':ttlqty' => $ttlqty,
        ':hnasat' => $hnasat_dtrbmasuk,
        ':hrgsat' => $hrgsat_dtrbmasuk,
        ':hrgjual' => $hrgjual_barang,
        ':id_barang' => $id_barang
    ]);

}else{
    $faktordiskon = (1-($diskon/100));
    $ttlharga = $qty_dtrbmasuk * $hnasat_dtrbmasuk * $faktordiskon * 1.11;
    $qty_retail = $qty_dtrbmasuk * $konversi;
    $tipe = 1;
    
    
    $cekstok = $db->prepare("SELECT * FROM barang WHERE id_barang = :id_barang");
    $cekstok->execute([
        ':id_barang'    => $id_barang
    ]);
    $rst = $cekstok->fetch(PDO::FETCH_ASSOC);
    $stokakhir = ($qty_dtrbmasuk*$konversi);
    $hrgjual_barang=round($hrgjual_dtrbmasuk) ;
    
   
	$stmt_insert_trbmasukdetail = $db->prepare("INSERT INTO trbmasuk_detail(
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
								  VALUES(:kd_trbmasuk,
								        :kd_orders,
										:id_barang,
										:kd_barang,
										:nmbrg_dtrbmasuk,
										:qty_retail,
										:qty_dtrbmasuk,
										:sat_barang,
										:sat_grosir,
										:konversi,
										:hnasat_dtrbmasuk,
										:diskon,
										:hrgsat_dtrbmasuk,
										:hrgjual_dtrbmasuk,
										:ttlharga,
										:no_batch,
										:exp_date,
										:tipe										
										)");

    $stmt_insert_trbmasukdetail->execute([
        ':kd_trbmasuk'          => $kd_trbmasuk,
		':kd_orders'            => $kd_orders,
		':id_barang'            => $id_barang,
		':kd_barang'            => $kd_barang,   
		':nmbrg_dtrbmasuk'      => $nmbrg_dtrbmasuk,
		':qty_retail'           => $qty_retail,
		':qty_dtrbmasuk'        => $qty_dtrbmasuk,
		':sat_barang'           => $rst['sat_barang'],
		':sat_grosir'           => $rst['sat_grosir'],
		':konversi'             => $konversi,
		':hnasat_dtrbmasuk'     => $hnasat_dtrbmasuk,
		':diskon'               => $diskon,
		':hrgsat_dtrbmasuk'     => $hrgsat_dtrbmasuk,
		':hrgjual_dtrbmasuk'    => $hrgjual_dtrbmasuk,
		':ttlharga'             => $ttlharga,
		':no_batch'             => $no_batch,
		':exp_date'             => $exp_date,
		':tipe'                 => $tipe
    ]);
    
    
    $stmt_update = $db->prepare("UPDATE barang SET 
                                                stok_barang = stok_barang + :stokakhir,
                                                hna = :hnasat,
                                                konversi = :konversion,
                                                sat_grosir = :satgrosir,
                                                hrgsat_barang = :hrgsat,
                                                hrgjual_barang = :hrgjual
                                                WHERE id_barang = :id_barangg
                                                AND kd_barang = :kd_barangg");
    $stmt_update->execute([
        ':stokakhir'    => $stokakhir,
        ':hnasat'       => $hnasat_dtrbmasuk,
        ':konversion'   => $konversi,
        ':satgrosir'    => $sat_dtrbmasuk,
        ':hrgsat'       => $hrgsat_dtrbmasuk,
        ':hrgjual'      => $hrgjual_barang,
        ':id_barangg'   => $id_barang,
        ':kd_barangg'   => $kd_barang
    ]);


    if($no_batch != ""){
        // Insert batch
    	$datetime = date('Y-m-d H:i:s', time());
    	$getbatch = $db->prepare("SELECT * FROM batch 
    	                            WHERE kd_transaksi = ?
    	                            AND kd_barang = ?
    	                            AND no_batch = ?");
    	$getbatch->execute([$kd_trbmasuk, $kd_barang, $no_batch]);
    	$countbatch = $getbatch->rowCount();
    	$rowbatch   = $getbatch->fetch(PDO::FETCH_ASSOC);
    	if ($countbatch > 0) {
    	   $qtyoldbatch = $rowbatch['qty'];
    	   $ttlqtybatch = $qtyoldbatch + $odt['qty_dtrbmasuk'];
    	   
    	   $db->prepare("UPDATE batch SET qty = ?
    	                WHERE kd_transaksi = ?
    	                AND kd_barang = ?
    	                AND no_batch = ?")->execute([$stokakhir, $kd_trbmasuk, $kd_barang, $no_batch]);
    	} else {
    	    
            $db->prepare("INSERT INTO batch(
                                            tgl_transaksi,
                                            no_batch,
        									exp_date,
        									qty,
        									satuan,
        									kd_transaksi,
        									kd_barang,
        									status)
        						    VALUES(?,?,?,?,?,?,?,?)")->execute([
        							        $datetime,
        								    $no_batch,
        									$exp_date,
        									$stokakhir,
        									$sat_dtrbmasuk,
        									$kd_trbmasuk,
        									$kd_barang,
        									'masuk'
        									]);
           
    	}
    }
}

?>
