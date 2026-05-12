<?php
include "../../../configurasi/koneksi.php";

$kd_trbmasuk = $_POST['kd_trbmasuk'];
$id_barang = $_POST['id_barang'];
$kd_barang = $_POST['kd_barang'];
$nmbrg_dtrbmasuk = $_POST['nmbrg_dtrbmasuk'];
$qty_dtrbmasuk = $_POST['qty_dtrbmasuk'];
$sat_dtrbmasuk = $_POST['sat_dtrbmasuk'];
$hrgsat_dtrbmasuk = round($_POST['hrgsat_dtrbmasuk'],0);
$hrgjual_dtrbmasuk = round($_POST['hrgjual_dtrbmasuk'],0);
$hrgjual_dtrbmasuk_resep = round($_POST['hrgjual_dtrbmasuk_resep'],0);
$hrgjual_dtrbmasuk_nakes = round($_POST['hrgjual_dtrbmasuk_nakes'],0);

$no_batch = $_POST['no_batch'];
$exp_date = date('Y-m-d', strtotime($_POST['exp_date']));

$datetime = date('Y-m-d H:i:s', time());

if ($_POST['exp_date']=='')
{ $tgl_awal = date('Y-m-d');
    $exp_date=date('Y-m-d', strtotime('+720 days', strtotime( $tgl_awal)));
}
else {
    $exp_date = $_POST['exp_date'];
}

if ($qty_dtrbmasuk == "") {
	$qty_dtrbmasuk = "1";
} else {
}

//cek apakah barang sudah ada
$cekdetail = $db->prepare("SELECT * FROM trbmasuk_detail 
                            WHERE kd_barang=? 
                            AND kd_trbmasuk=?
                            AND no_batch=?");
$cekdetail->execute([$kd_barang, $kd_trbmasuk, $no_batch]);
$ketemucekdetail = $cekdetail->rowCount();
$rcek = $cekdetail->fetch(PDO::FETCH_ASSOC);
if ($ketemucekdetail > 0) {

	$id_dtrbmasuk = $rcek['id_dtrbmasuk'];
	$qtylama = $rcek['qty_dtrbmasuk'];
	$ttlqty = $qtylama + $qty_dtrbmasuk;
	$ttlharga = $ttlqty * $hrgsat_dtrbmasuk;

	$stmt_update_trbmasukdetail = $db->prepare("UPDATE trbmasuk_detail SET qty_dtrbmasuk = ?,
            										hrgsat_dtrbmasuk = ?,
            										hrgjual_dtrbmasuk = ?,
            										hrgttl_dtrbmasuk = ?,
            										no_batch = ?,
            										exp_date = ?
            										WHERE id_dtrbmasuk = ?");
    $stmt_update_trbmasukdetail->execute([$ttlqty, $hrgsat_dtrbmasuk, $hrgjual_dtrbmasuk, $ttlharga, $no_batch, $exp_date, $id_dtrbmasuk]);

	// UPDATE STOK ATOMIC - Menambah stok barang (barang sudah ada di detail)
	// Menggunakan single UPDATE statement untuk menghindari race condition
	$stmt_update_barang2 = $db->prepare("UPDATE barang SET 
                                		stok_barang     = stok_barang - :qtylama + :ttlqty,
                                		sat_barang      = :sat_barang,
                                        hrgsat_barang   = :hrgsat_barang
                                		WHERE id_barang = :id_barang");
                                
    $stmt_update_barang2->execute([
        ':qtylama' => $qtylama,
        ':ttlqty' => $ttlqty,
        ':sat_barang' => $sat_dtrbmasuk,
        ':hrgsat_barang' => $hrgsat_dtrbmasuk,
        ':id_barang' => $id_barang
    ]);
    
	//cek apakah barang dengan no batch yang dimaksud sudah ada
    $cekbatchdetail = $db->prepare("SELECT no_batch, kd_transaksi,qty
                                        FROM batch 
                                        WHERE no_batch = ? 
                                        AND kd_transaksi = ? 
                                        AND kd_barang = ?
                                        AND status = ?");
    $cekbatchdetail->execute([$no_batch, $kd_trbmasuk, $kd_barang, 'masuk']);
    $ketemucekbatchdetail = $cekbatchdetail->rowCount();;
    
    if($ketemucekbatchdetail>0)
    {
        //tarikstok dari batch
        $tampung = $cekbatchdetail->fetch(PDO::FETCH_ASSOC);
        $qtybatchlama = $tampung['qty'];
        $qtybatchbaru = $qtybatchlama + $qty_dtrbmasuk;

        $stmt_update_batchdetail = $db->prepare("UPDATE batch SET qty = ? 
                                                    WHERE kd_transaksi = ? 
                                                          AND no_batch = ?
                                                          AND kd_barang = ?
                                                          AND status = ?");
        $stmt_update_batchdetail->execute([$qtybatchbaru, $kd_trbmasuk, $no_batch, $kd_barang, 'masuk']);

    }
} else {

    //Query barang
    $cekstok = $db->prepare("SELECT * FROM barang WHERE id_barang=?");
    $cekstok->execute([$id_barang]);
    $rst = $cekstok->fetch(PDO::FETCH_ASSOC);
    
	$ttlharga = $qty_dtrbmasuk * $hrgsat_dtrbmasuk;
	
	$stmt_insert_trbmasukdetail = $db->prepare("INSERT INTO trbmasuk_detail(kd_trbmasuk,
										id_barang,
										kd_barang,
										nmbrg_dtrbmasuk,
										qty_dtrbmasuk,
										sat_dtrbmasuk,
										hrgsat_dtrbmasuk,
										hrgjual_dtrbmasuk,
										hrgttl_dtrbmasuk,
										hnasat_dtrbmasuk,
										no_batch,
										exp_date)
								  VALUES(?,?,?,?,?,?,?,?,?,?,?,?)");
	
	$stmt_insert_trbmasukdetail->execute([$kd_trbmasuk, $id_barang, $kd_barang, $nmbrg_dtrbmasuk, $qty_dtrbmasuk, $sat_dtrbmasuk,
	                                        $hrgsat_dtrbmasuk, $hrgjual_dtrbmasuk, $ttlharga, $rst['hna'], $no_batch, $exp_date]);									
								
	// UPDATE STOK ATOMIC - Menambah stok barang (barang baru)
	// Menggunakan single UPDATE statement untuk menghindari race condition
    $hrgjual_barang=round($hrgjual_dtrbmasuk) ;
    $hrgjual_barang_resep=round($hrgjual_dtrbmasuk_resep) ;
    $hrgjual_barang_nakes=round($hrgjual_dtrbmasuk_nakes) ;
    

	$stmt_update_barang2 = $db->prepare("UPDATE barang SET 
                                		stok_barang     = stok_barang + :qty_dtrbmasuk,
                                		sat_barang      = :sat_barang,
                                        hrgsat_barang   = :hrgsat_barang,
                                        hrgjual_barang  = :hrgjual_barang,
                                        hrgjual_barang1 = :hrgjual_barang1,
                                        hrgjual_barang2 = :hrgjual_barang2
                                		WHERE id_barang = :id_barang");
                                
    $stmt_update_barang2->execute([
        ':qty_dtrbmasuk' => $qty_dtrbmasuk,
        ':sat_barang' => $sat_dtrbmasuk,
        ':hrgsat_barang' => $hrgsat_dtrbmasuk,
        ':hrgjual_barang' => $hrgjual_barang,
        ':hrgjual_barang1' => $hrgjual_barang_resep,
        ':hrgjual_barang2' => $hrgjual_barang_nakes,
        ':id_barang' => $id_barang
    ]);
		
	$stmt_insert_batch = $db->prepare("INSERT INTO batch(
	                                    tgl_transaksi,
                                        no_batch,
                                        exp_date,
                                        qty,
                                        satuan,
                                        kd_transaksi,										
										kd_barang,
										status
										)
								  VALUES(?,?,?,?,?,?,?,?)");
								// 		VALUES('$datetime',
								//         '$no_batch',
								// 		'$exp_date',
								// 		'$qty_dtrbmasuk',
								// 		'$sat_dtrbmasuk',
								// 		'$kd_trbmasuk',
								// 		'$kd_barang',
								// 		'masuk'
								// 		)");
	$stmt_insert_batch->execute([$datetime, $no_batch, $exp_date, $qty_dtrbmasuk, $sat_dtrbmasuk, $kd_trbmasuk, $kd_barang, 'masuk']);
}
