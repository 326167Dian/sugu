<?php 
ob_start();
session_start();
include "../../../configurasi/koneksi.php";
include "../../../configurasi/fungsi_batch.php";

ob_clean();
header('Content-Type: application/json; charset=utf-8');

try {
    $cekKolomResep = $db->prepare("SHOW COLUMNS FROM trkasir_detail LIKE 'resep'");
    $cekKolomResep->execute();
    if ($cekKolomResep->rowCount() == 0) {
        $db->exec("ALTER TABLE trkasir_detail ADD COLUMN resep ENUM('TIDAK','YA') NOT NULL DEFAULT 'TIDAK' AFTER disc");
    }

    $cekKolomResepHist = $db->prepare("SHOW COLUMNS FROM trkasir_detail_hist LIKE 'resep'");
    $cekKolomResepHist->execute();
    if ($cekKolomResepHist->rowCount() == 0) {
        $db->exec("ALTER TABLE trkasir_detail_hist ADD COLUMN resep ENUM('TIDAK','YA') NOT NULL DEFAULT 'TIDAK' AFTER disc");
    }
} catch (Exception $e) {
}

$kd_trkasir         = isset($_POST['kd_trkasir']) ? $_POST['kd_trkasir'] : '';
$id_dtrkasir        = isset($_POST['id_dtrkasir']) ? $_POST['id_dtrkasir'] : '';
$id_barang          = isset($_POST['id_barang']) ? $_POST['id_barang'] : '';
$kd_barang          = isset($_POST['kd_barang']) ? $_POST['kd_barang'] : '';
$nmbrg_dtrkasir     = isset($_POST['nmbrg_dtrkasir']) ? $_POST['nmbrg_dtrkasir'] : '';
$qty_dtrkasir       = isset($_POST['qty_dtrkasir']) ? $_POST['qty_dtrkasir'] : '';
$sat_dtrkasir       = isset($_POST['sat_dtrkasir']) ? $_POST['sat_dtrkasir'] : '';
$hrgjual_dtrkasir   = isset($_POST['hrgjual_dtrkasir']) ? $_POST['hrgjual_dtrkasir'] : 0;
$indikasi           = isset($_POST['indikasi']) ? $_POST['indikasi'] : '';

if ($_SESSION['komisi'] == 'Y') {
    $tarik = $db->prepare("select komisi from barang where id_barang='$id_barang' ");
    $tarik->execute();
    $kom = $tarik->fetch(PDO::FETCH_ASSOC);

    $komisi = $kom['komisi'] * $_POST['qty_dtrkasir'] ;
}
elseif ($_SESSION['komisi'] == 'N'){
    $komisi = 0;
}

$currentdate = date('Y-m-d',time());
$id_admin = $_POST['id_admin'];

$disc = (empty($_POST['disc']))?0:$_POST['disc'];
$resep = isset($_POST['resep']) && $_POST['resep'] !== '' ? strtoupper($_POST['resep']) : 'TIDAK';
$no_batch = isset($_POST['no_batch']) ? $_POST['no_batch'] : '';
$exp_date = isset($_POST['exp_date']) ? $_POST['exp_date'] : '';
$hrgdisc = $hrgjual_dtrkasir * (1-($disc/100));
$tipe = isset($_POST['tipe']) ? $_POST['tipe'] : 1;
$datetime = date('Y-m-d H:i:s', time());

if($qty_dtrkasir == ""){
    $qty_dtrkasir = "1";
}


if($id_dtrkasir == "" || $id_dtrkasir == null){

    //cek apakah barang sudah ada
    $cekdetail = $db->prepare("SELECT * FROM trkasir_detail 
                                WHERE kd_barang=? AND kd_trkasir=?");
    $cekdetail->execute([$kd_barang, $kd_trkasir]);
    $ketemucekdetail = $cekdetail->rowCount();
    $rcek = $cekdetail->fetch(PDO::FETCH_ASSOC);

    if ($ketemucekdetail > 0){
    
        $id_dtrkasir = $rcek['id_dtrkasir'];
        $qtylama = $rcek['qty_dtrkasir'];
        $ttlqty = $qtylama + $qty_dtrkasir;
        $ttlharga = $ttlqty * $hrgdisc;
    
        $mdl = $db->prepare("select hrgsat_barang from barang where id_barang=?");
        $mdl->execute([$id_barang]);
        $mdl1 = $mdl->fetch(PDO::FETCH_ASSOC);
        $modal = $mdl1['hrgsat_barang'];
        $profit = $ttlharga - ($modal * $ttlqty) ;
    
        $stmt_update = $db->prepare("UPDATE trkasir_detail SET qty_dtrkasir = ?,
    										hrgjual_dtrkasir = ?,
                                            resep = ?,
                                            modal = ?,
    										profit = ?,
    										hrgttl_dtrkasir = ?,
                                            komisi = ?
    										WHERE id_dtrkasir = ? and kd_barang=?");
        $stmt_update->execute([$ttlqty, $hrgjual_dtrkasir, $resep, $modal, $profit, $ttlharga, $komisi, $id_dtrkasir, $kd_barang]);									
            
        // UPDATE STOK ATOMIC - Kurangi stok berdasarkan quantity yang ditambahkan
        // Menggunakan single UPDATE statement untuk menghindari race condition
        $update_barang = $db->prepare("UPDATE barang SET 
                                        stok_barang = stok_barang - :qty_dikurangi
                                        WHERE id_barang = :id_barang");
        $update_barang->execute([
            ':qty_dikurangi' => $qty_dtrkasir, 
            ':id_barang' => $id_barang
        ]);
        
        // Verifikasi update berhasil
        if($update_barang->rowCount() == 0) {
            // Log error jika update gagal
            error_log("Warning: Stock update failed for id_barang: " . $id_barang);
        }
    
        if($_SESSION['komisi']=='Y'){
            if($_SESSION['penjualansebelum']=='Y'){
                $ttlkomisi = $ttlqty * $komisi;
                $stmt_updatekomisi = $db->prepare("UPDATE komisi_pegawai SET ttl_komisi = ? 
                                                    WHERE id_dtrkasir = ?");
                $stmt_updatekomisi->execute([$ttlkomisi, $id_dtrkasir]);
            } else {
                $ttlkomisi = $ttlqty * $komisi;
                $stmt_updatekomisi = $db->prepare("UPDATE komisi_pegawai SET ttl_komisi = ? 
                                                    WHERE id_dtrkasir = ? 
                                                    AND id_admin = ?");		
                $stmt_updatekomisi->execute([$ttlkomisi, $id_dtrkasir, $_SESSION['idadmin']]);
            }
        }
        
        	//cek apakah barang dengan no batch yang dimaksud sudah ada
        $cekbatchdetail = $db->prepare("SELECT no_batch, kd_transaksi,qty
                                        FROM batch 
                                        WHERE no_batch = '$no_batch' 
                                        AND kd_transaksi = '$kd_trkasir' 
                                        AND status = 'keluar'");
                                        
        $ketemucekbatchdetail = $cekbatchdetail->rowCount();
        if($ketemucekbatchdetail>0)
        {
            //tarikstok dari batch
            $tampung = $cekbatchdetail->fetch(PDO::FETCH_ASSOC);
            $qtybatchlama = $tampung['qty'];
            $qtybatchbaru = $qtybatchlama + $qty_dtrkasir;
    
            $stmt_updatebatch = $db->prepare("UPDATE batch SET qty = ?
                                                WHERE kd_transaksi = ? 
                                                      AND no_batch = ?
                                                      AND status = ?");
            $stmt_updatebatch->execute([$qtybatchbaru, $kd_trkasir, $no_batch, 'keluar']);
        }
    }else{
            
        $ttlharga = $qty_dtrkasir * $hrgdisc;
        
        $getkode = substr($kd_barang,0,4);
        if ($getkode != 'BUND') {
            $mdl = $db->prepare("SELECT * FROM barang WHERE id_barang =?");
            $mdl->execute([$id_barang]);
            
            $mdl1 = $mdl->fetch(PDO::FETCH_ASSOC);
            $modal = $mdl1['hrgsat_barang'];
            $profit = $ttlharga - ($modal * $qty_dtrkasir) ;
            $stok_brg_lama = $mdl1['stok_barang'];
            $stok_brg_baru = $stok_brg_lama - $qty_dtrkasir;
            
            //proses input batch jika no batch kosong
            $datetime = date('Y-m-d H:i:s', time());
            if($no_batch != ""){
                // Input batch
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
            	$stmt_insert_batch->execute([$datetime, $no_batch, $exp_date, $qty_dtrkasir, $sat_dtrkasir, $kd_trkasir, $kd_barang, 'keluar']);
            	
                // Input trkasir trkasir_detail
                $stmt_insert_trkasirdetail = $db->prepare("INSERT INTO trkasir_detail(kd_trkasir,
        										id_barang,
        										kd_barang,
        										nmbrg_dtrkasir,
        										qty_dtrkasir,
        										sat_dtrkasir,
        										hrgjual_dtrkasir,
        										disc,
                                                resep,
                                                modal,
        										profit,
        										no_batch,
        										exp_date,
        										waktu,
        										hrgttl_dtrkasir,
        										tipe,
                                                komisi,
                                                idadmin)
    	    							  VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                $stmt_insert_trkasirdetail->execute([$kd_trkasir, $id_barang, $kd_barang, $nmbrg_dtrkasir, $qty_dtrkasir, $sat_dtrkasir, $hrgjual_dtrkasir, 
                                        $disc, $resep, $modal, $profit, $no_batch, $exp_date, $datetime, $ttlharga, $tipe, $komisi, $id_admin]);
            
                $insertid_dtrkasir = $db->lastInsertId();
            } else {
                $data = get_batch_fifo($qty_dtrkasir, $kd_barang);
                $data = json_decode($data, true); // ubah jadi object
                
                for ($i = 0; $i < count($data); $i++) {
                    $no_batch = $data[$i]['no_batch'];
                    $exp_date = $data[$i]['exp_date'];
                    $qty_ambil = $data[$i]['qty_ambil'];
                    
                    // Input Batch    
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
            	    $stmt_insert_batch->execute([$datetime, $no_batch, $exp_date, $qty_ambil, $sat_dtrkasir, $kd_trkasir, $kd_barang, 'keluar']);
            	    
            	    // Input trkasir trkasir_detail
            	    $ttlharga = $qty_ambil * $hrgdisc;
                    $stmt_insert_trkasirdetail = $db->prepare("INSERT INTO trkasir_detail(kd_trkasir,
            										id_barang,
            										kd_barang,
            										nmbrg_dtrkasir,
            										qty_dtrkasir,
            										sat_dtrkasir,
            										hrgjual_dtrkasir,
            										disc,
                                                    resep,
                                                    modal,
            										profit,
            										no_batch,
            										exp_date,
            										waktu,
            										hrgttl_dtrkasir,
            										tipe,
                                                    komisi,
                                                    idadmin)
        	    							  VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                    $stmt_insert_trkasirdetail->execute([$kd_trkasir, $id_barang, $kd_barang, $nmbrg_dtrkasir, $qty_ambil, $sat_dtrkasir, $hrgjual_dtrkasir, $disc, $resep, $modal, $profit, $no_batch, $exp_date, $datetime, $ttlharga, $tipe, $komisi, $id_admin]);
                
                    $insertid_dtrkasir = $db->lastInsertId();
                }
            }
        
        										
            //cek transaksi sukses
            $cekmasuk = $db->prepare("SELECT * FROM trkasir_detail 
                                        WHERE kd_trkasir =?");
            $cekmasuk->execute([$kd_trkasir]);
            $ketemucekmasuk = $cekmasuk->rowCount();
            if($ketemucekmasuk > 0 ) {
                // UPDATE STOK ATOMIC - Kurangi stok langsung di database
                // Menggunakan single UPDATE statement untuk menghindari race condition
                
                //update harga jual hanya sementara hanya untuk pemakai awal, setelah itu harga jual tidak bisa diupdate lewat kasir
                $lst_trx = $db->prepare("SELECT * FROM trkasir 
                                            ORDER BY id_trkasir ASC
                                            LIMIT 1");
                $lst_trx->execute();
                $ketemulst_trx = $lst_trx->rowCount();
                if ($ketemulst_trx > 0) {
                    $trx =  $lst_trx->fetch(PDO::FETCH_ASSOC);
                    $tgl_first = date('Y-m-d', strtotime('+1 months', strtotime($trx['tgl_trkasir'])));
                    $tgl_last  = date('Y-m-d', time());
                
                } else {
                    $tgl_first = date('Y-m-d', strtotime('+1 months', time()));
                    $tgl_last  = date('Y-m-d', time());
                }
                
                
                // Gunakan UPDATE atomic untuk stok
                if($tgl_last > $tgl_first){
                    // Hanya update stok (sudah > 1 bulan)
                    $stmt_updatebarang = $db->prepare("UPDATE barang SET 
                                                        stok_barang = stok_barang - :qty_dikurangi
                                                        WHERE id_barang = :id_barang");
                    $stmt_updatebarang->execute([
                        ':qty_dikurangi' => $qty_dtrkasir, 
                        ':id_barang' => $id_barang
                    ]);
                } else {
                    if($_POST['tipe'] == '1'){
                        $stmt_updatebarang = $db->prepare("UPDATE barang SET 
                                                            hrgjual_barang = :hrgjual,
                                                            stok_barang = stok_barang - :qty_dikurangi
                                                            WHERE id_barang = :id_barang");
                        
                    } 
                    elseif($_POST['tipe'] == '2'){
                        $stmt_updatebarang = $db->prepare("UPDATE barang SET 
                                                            hrgjual_barang1 = :hrgjual,
                                                            stok_barang = stok_barang - :qty_dikurangi
                                                            WHERE id_barang = :id_barang");
                        
                    }
                    elseif($_POST['tipe'] == '3'){
                        $stmt_updatebarang = $db->prepare("UPDATE barang SET 
                                                            hrgjual_barang2 = :hrgjual,
                                                            stok_barang = stok_barang - :qty_dikurangi
                                                            WHERE id_barang = :id_barang");
                        
                    }
                    
                    $stmt_updatebarang->execute([
                        ':qty_dikurangi' => $qty_dtrkasir, 
                        ':id_barang' => $id_barang,
                        ':hrgjual' => $hrgjual_dtrkasir
                    ]);
                }
                
                
                    
                // Verifikasi update berhasil
                if($stmt_updatebarang->rowCount() == 0) {
                    error_log("Warning: Stock update failed for id_barang: " . $id_barang);
                    
                }
                    
                if($_SESSION['komisi']=='Y'){
                    if($_SESSION['penjualansebelum']=='Y'){
                        
                        $ttlkomisi = $qty_dtrkasir * $komisi;
                        $stmt_insert_komisi = $db->prepare("INSERT INTO komisi_pegawai (
                        kd_trkasir, id_dtrkasir, id_admin, ttl_komisi, tgl_komisi, status_komisi)
                        VALUES(?, ?, ?, ?, ?, ?)");
                        $stmt_insert_komisi->execute([$kd_trkasir, $insertid_dtrkasir, $id_admin, $ttlkomisi, $currentdate, 'on']);
                    } else {
                        $ttlkomisi = $qty_dtrkasir * $komisi;
                        $stmt_insert_komisi = $db->prepare("INSERT INTO komisi_pegawai (
                        kd_trkasir, id_dtrkasir, id_admin, ttl_komisi, tgl_komisi, status_komisi)
                        VALUES(?, ?, ?, ?, ?, ?)");
                        $stmt_insert_komisi->execute([$kd_trkasir, $insertid_dtrkasir, $_SESSION['idadmin'], $ttlkomisi, $currentdate, 'on']);
                    }
                }
            }
            
        }
        else {
            $bndl = $db->prepare("SELECT * FROM bundle WHERE id_bundle =?");
            $bndl->execute([$id_barang]);
            $rowbundle = $bndl->rowCount();
            
            if ($rowbundle > 0) {
                
                // if($no_batch != ""){
                //     // Input batch
                //     $stmt_insert_batch = $db->prepare("INSERT INTO batch(
                //                                         tgl_transaksi,
                //                                         no_batch,
                //                                         exp_date,
                //                                         qty,
                //                                         satuan,
                //                                         kd_transaksi,										
                // 										kd_barang,
                // 										status	
                // 										)
                // 								  VALUES(?,?,?,?,?,?,?,?)");    
                // 	$stmt_insert_batch->execute([$datetime, $no_batch, $exp_date, $qty_dtrkasir, $sat_dtrkasir, $kd_trkasir, $kd_barang, 'keluar']);
                // } 
                
                
                $get_bundle_detail = $db->prepare("SELECT * FROM bundle_detail
                                                    WHERE kd_bundle = ?");
                $get_bundle_detail->execute([$kd_barang]);
                $datetime = date('Y-m-d H:i:s', time());
                
                while($rbundle = $get_bundle_detail->fetch(PDO::FETCH_ASSOC)){
                    $qty_dikurangi = $rbundle['qty_barang'] * $qty_dtrkasir;
                    
                    $cekstokbarang = $db->prepare("SELECT stok_barang FROM barang WHERE kd_barang = :kd_barang");
                    $cekstokbarang->execute([
                        ':kd_barang'    => $rbundle['kd_barang']
                    ]);
                    $stokbarang = $cekstokbarang->fetch(PDO::FETCH_ASSOC);
                    
                    if($stokbarang['stok_barang'] < $qty_dikurangi){
                        $data = array(
                            "status"    => "error",
                            "data"      => "Stok barang tidak mencukupi!"
                        );
                        echo json_encode($data);        
                        die();
                    }
                    
                    $update_stok_barang = $db->prepare("UPDATE barang SET stok_barang = stok_barang - :qty_dikurangi
                                                        WHERE kd_barang = :kd_barang");
                    $update_stok_barang->execute([
                            ':qty_dikurangi' => $qty_dikurangi, 
                            ':kd_barang' => $rbundle['kd_barang']
                        ]);
                    
                    $data = get_batch_fifo($qty_dikurangi, $rbundle['kd_barang']);
                    $data = json_decode($data, true); // ubah jadi object
                    
                    for ($i = 0; $i < count($data); $i++) {
                        $no_batch = $data[$i]['no_batch'];
                        $exp_date = $data[$i]['exp_date'];
                        $qty_ambil = $data[$i]['qty_ambil'];
                        
                        // Input Batch    
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
                	    $stmt_insert_batch->execute([$datetime, $no_batch, $exp_date, $qty_ambil, $rbundle['sat_barang'], $kd_trkasir, $rbundle['kd_barang'], 'keluar']);
                	    
                    }
                }
                
                $bundle_detail  = $db->prepare("SELECT SUM(hrgjual_barang) as ttl_hrg_jual FROM bundle_detail WHERE kd_bundle = ?");
                $bundle_detail->execute([$kd_barang]);
                $bndl_jual = $bundle_detail->fetch(PDO::FETCH_ASSOC);
                
                $barang     = $db->prepare("SELECT SUM(barang.hrgsat_barang) as ttl_hrg_modal, MIN(barang.stok_barang) AS min_stok FROM barang
                                            WHERE barang.kd_barang IN (SELECT kd_barang FROM bundle_detail WHERE kd_bundle = ?)");
                $barang->execute([$kd_barang]);
                $brg_modal  = $barang->fetch(PDO::FETCH_ASSOC);
                $min_stok   = $brg_modal['min_stok'];
                $modal      = $brg_modal['ttl_hrg_modal'];
                $profit     = $bndl_jual['ttl_hrg_jual'] - $brg_modal['ttl_hrg_modal'];
                
                if ($min_stok <= 0) {
                    // echo "<script type='text/javascript'>alert('Stok barang tidak mencukupi!');</script>";
                    $data = array(
                            "status"    => "error",
                            "data"      => "Stok barang tidak mencukupi!"
                        );
                    echo json_encode($data);        
                    die();
                }
                
                $stmt_insert_trkasirdetail = $db->prepare("INSERT INTO trkasir_detail(kd_trkasir,
        										id_barang,
        										kd_barang,
        										nmbrg_dtrkasir,
        										qty_dtrkasir,
        										sat_dtrkasir,
        										hrgjual_dtrkasir,
        										disc,
                                                resep,
                                                modal,
        										profit,
        										no_batch,
        										exp_date,										
        										hrgttl_dtrkasir,
        										tipe,
                                                komisi,
                                                idadmin)
    	    							  VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                $stmt_insert_trkasirdetail->execute([$kd_trkasir, $id_barang, $kd_barang, $nmbrg_dtrkasir, $qty_dtrkasir, $sat_dtrkasir, $hrgjual_dtrkasir, 
                                    $disc, $resep, $modal, $profit, $no_batch, $exp_date, $ttlharga, $tipe, $komisi, $id_admin]);
                                    
                
                $insertid_dtrkasir = $db->lastInsertId();
                
                $update_stok_bundle = $db->prepare("UPDATE bundle SET qty_bundle = qty_bundle - :qty_dikurangi
                                                    WHERE id_bundle = :id_barang");
                $update_stok_bundle->execute([
                        ':qty_dikurangi'    => $qty_dtrkasir,
                        ':id_barang'        => $id_barang
                    ]);
                
                
            }
        }
        
    }

}else{
    
    $cekdetail = $db->prepare("SELECT * FROM trkasir_detail 
                                WHERE id_dtrkasir =?");
    $cekdetail->execute([$id_dtrkasir]);
    $rcek = $cekdetail->fetch(PDO::FETCH_ASSOC);
    $id_dtrkasir = $rcek['id_dtrkasir'];
    $qtylama = $rcek['qty_dtrkasir'];
    $qtybaru = $qtylama + $qty_dtrkasir;
    $ttlharga = $qtybaru * $hrgjual_dtrkasir;
    
    $update_trkasir = $db->prepare("UPDATE trkasir_detail SET qty_dtrkasir = '$qtybaru',
    										hrgjual_dtrkasir = '$hrgjual_dtrkasir',
                                        resep = '$resep',
    										hrgttl_dtrkasir = '$ttlharga'
    										WHERE id_dtrkasir = '$id_dtrkasir'");
    $update_trkasir->execute();
										
    // UPDATE STOK ATOMIC - Kurangi stok berdasarkan quantity yang ditambahkan
    // Menggunakan single UPDATE statement untuk menghindari race condition
    $updatebarang = $db->prepare("UPDATE barang SET 
                                    stok_barang = stok_barang - :qty_dikurangi
                                    WHERE id_barang = :id_barang");
    $updatebarang->execute([
        ':qty_dikurangi' => $qty_dtrkasir, 
        ':id_barang' => $id_barang
    ]);
    
    // Verifikasi update berhasil
    if($updatebarang->rowCount() == 0) {
        error_log("Warning: Stock update failed for id_barang: " . $id_barang);
    }
    
    if($_SESSION['komisi']=='Y'){
        if($_SESSION['penjualansebelum']=='Y'){
            $ttlkomisi = $qtybaru * $komisi;
            $updatekomisi = $db->prepare("UPDATE komisi_pegawai SET ttl_komisi = '$ttlkomisi' 
                                        WHERE id_dtrkasir='$id_dtrkasir'");
            $updatekomisi->execute();
        } else {
            $ttlkomisi = $qtybaru * $komisi;
            $updatekomisi = $db->prepare("UPDATE komisi_pegawai SET ttl_komisi = '$ttlkomisi' 
                                        WHERE id_dtrkasir='$id_dtrkasir' AND id_admin='$_SESSION[idadmin]'");
            $updatekomisi->execute();
        }
    }
}

echo json_encode(array(
    "status" => "success"
));

?>
