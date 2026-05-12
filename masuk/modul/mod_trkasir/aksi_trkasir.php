<?php
error_reporting(0);
session_start();
if (empty($_SESSION['username']) and empty($_SESSION['passuser'])) {
	echo "<link href='style.css' rel='stylesheet' type='text/css'>
 <center>Untuk mengakses modul, Anda harus login <br>";
	echo "<a href=../../index.php><b>LOGIN</b></a></center>";
} else {
	include "../../../configurasi/koneksi.php";
	include "../../../configurasi/fungsi_thumb.php";
	include "../../../configurasi/library.php";
	$jenistx = $db->prepare("SELECT * FROM trkasir_detail WHERE kd_trkasir='$_POST[kd_trkasir]' GROUP BY kd_trkasir");
	$jenistx->execute();
	$jnstx = $jenistx->fetch(PDO::FETCH_ASSOC);
	$datetime = date('Y-m-d H:i:s', time());
	
	$module = "trkasir";
	$stt_aksi = $_POST['stt_aksi'];
	if ($stt_aksi == "input_trkasir" || $stt_aksi == "ubah_trkasir") {
		$act = $stt_aksi;
	} else {
		$act = $_GET['act'];
	}
    
	// Input admin
	if ($module == 'trkasir' and $act == 'input_trkasir') {
        header('Content-Type: application/json');

        try {
            $cariitem = $db->prepare("SELECT * FROM trkasir_detail WHERE kd_trkasir = ?");
            $cariitem->execute([$_POST['kd_trkasir']]);
            $countItem = $cariitem->rowCount();

            if($countItem <= 0){
                $data['message'] = 'failed';
                $data['error'] = 'Detail transaksi masih kosong.';
			    echo json_encode($data);
            } else {
                
                $stmt_trkasir = $db->prepare("SELECT SUM(hrgttl_dtrkasir) AS total_harga FROM trkasir_detail
                                            WHERE kd_trkasir = :kd_trkasir");
                $stmt_trkasir->execute([
                    ':kd_trkasir'   => $_POST['kd_trkasir']
                ]);
                $tk = $stmt_trkasir->fetch(PDO::FETCH_ASSOC);
                                                
                $stmt_poin = $db->prepare("SELECT * FROM poin_pelanggan");
                $stmt_poin->execute();
                $poin = $stmt_poin->fetch(PDO::FETCH_ASSOC);
                        
                $stmt_pelanggan = $db->prepare("SELECT * FROM pelanggan WHERE id_pelanggan = :id_pelanggan");
                $stmt_pelanggan->execute([
                    ':id_pelanggan' => $_POST['id_pelanggan']
                ]);
                
                $total_poin = 0;
                $poin_awal = 0;
                if($stmt_pelanggan->rowCount() > 0){
                    $pelanggan = $stmt_pelanggan->fetch(PDO::FETCH_ASSOC);
                    $poin_awal = $pelanggan['total_poin'];
                    $total_poin = floor($tk['total_harga'] / $poin['min_penjualan']) * $poin['poin_pelanggan'];
                            
                    $stmt_update_pelanggan = $db->prepare("UPDATE pelanggan SET total_poin = (total_poin + :total_poin) - :redeem_poin
                                                            WHERE id_pelanggan = :id_pelanggan");
                    $stmt_update_pelanggan->execute([
                        ':total_poin'   => $total_poin,
                        ':redeem_poin'    => $_POST['redeem_poin'],
                        ':id_pelanggan' => $_POST['id_pelanggan']
                    ]);
                    
                }
                
    		    $inserttrkasir = $db->prepare("INSERT INTO trkasir(
    										kd_trkasir,	
											id_user,
    										petugas,
    										shift,																				
    										tgl_trkasir,	
    										id_pelanggan,
    										nm_pelanggan,										
    										tlp_pelanggan,
    										alamat_pelanggan,
    										kodetx,
    										ttl_trkasir,
											diskon1,
    										diskon2,
    										dp_bayar,
    										sisa_bayar,
    										ket_trkasir,
    										id_carabayar,
											jenistx,
											waktu_trx,
											poin_awal,
											tambahan_poin,
											redeem_poin
    										)
    									 VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    		        $insert = $inserttrkasir->execute([$_POST['kd_trkasir'], $_POST['id_user'], $_POST['petugas'], $_POST['shift'], $_POST['tgl_trkasir'], $_POST['id_pelanggan'], $_POST['nm_pelanggan'], $_POST['tlp_pelanggan'], $_POST['alamat_pelanggan'], $_POST['kodetx'], $_POST['ttl_trkasir'], $_POST['diskon1'], $_POST['diskon2'], $_POST['dp_bayar'], $_POST['sisa_bayar'], $_POST['ket_trkasir'], $_POST['id_carabayar'], $jnstx['tipe'], $datetime, $poin_awal, $total_poin, $_POST['redeem_poin']]);

                $db->prepare("update trkasir_detail set idadmin = ? where kd_trkasir = ?")->execute([$_POST['id_user'], $_POST['kd_trkasir']]);
    
            $tgl_sekarang = date('Y-m-d H:i:s', time());
            $db->prepare("INSERT INTO kartu_stok(kode_transaksi, tgl_sekarang) VALUES(?,?)")->execute([$_POST['kd_trkasir'], $tgl_sekarang]);
            
                
    		if ($insert) {
    			# code...
    			$db->prepare("UPDATE kdtk SET stt_kdtk = 'OFF' WHERE id_admin = ? AND kd_trkasir = ?")->execute([$_SESSION['idadmin'], $_POST['kd_trkasir']]);
    
    //             $ambildatainduk = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM trkasir WHERE id_trkasir='$_GET[id]'");
                $ambildatainduk = $db->prepare("SELECT * FROM trkasir WHERE kd_trkasir=?");
                $ambildatainduk->execute([$_POST['kd_trkasir']]);
    			$r1 = $ambildatainduk->fetch(PDO::FETCH_ASSOC);
    			$kd_trkasir = $r1['kd_trkasir'];
    			
    			//loop data detail
    // 			$ambildatadetail = $db->prepare("SELECT * FROM trkasir_detail WHERE kd_trkasir=?");
    //             $ambildatadetail->execute([$kd_trkasir]);
    // 			while ($r = $ambildatadetail->fetch(PDO::FETCH_ASSOC)) {
    //                 $db->prepare("INSERT INTO trkasir_restore(
    // 						kd_trkasir, petugas, shift, tgl_trkasir, nm_pelanggan, tlp_pelanggan, alamat_pelanggan,
    // 						ttl_trkasir, dp_bayar, diskon1, diskon2, sisa_bayar, ket_trkasir, id_carabayar, id_barang,
    // 						kd_barang, nmbrg_dtrkasir, qty_dtrkasir, sat_dtrkasir, hrgjual_dtrkasir, hrgttl_dtrkasir)
    // 					VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")->execute([$r1['kd_trkasir'], $r1['petugas'], $r1['shift'], $r1['tgl_trkasir'], $r1['nm_pelanggan'], $r1['tlp_pelanggan'], $r1['alamat_pelanggan'], $r1['ttl_trkasir'], $r1['dp_bayar'], $r1['diskon1'], $r1['diskon2'], $r1['sisa_bayar'], $r1['ket_trkasir'], $r1['id_carabayar'], $r['id_barang'], $r['kd_barang'], $r['nmbrg_dtrkasir'], $r['qty_dtrkasir'], $r['sat_dtrkasir'], $r['hrgjual_dtrkasir'], $r['hrgttl_dtrkasir']]);
    
    //             }
                
    			$data['message'] = 'success';
    			echo json_encode($data);
    		} else {
    			$data['message'] = 'failed';
    			echo json_encode($data);
    		}

            }
        } catch (Throwable $e) {
            echo json_encode([
                'message' => 'failed',
                'error' => $e->getMessage()
            ]);
        }
		//echo "<script type='text/javascript'>alert('Transkasi berhasil ditambahkan !');window.location='../../media_admin.php?module=".$module."'</script>";
	}

	//updata trkasir
	elseif ($module == 'trkasir' and $act == 'ubah_trkasir') {
        header('Content-Type: application/json');

		$stmt_update_trkasir = $db->prepare("UPDATE trkasir SET tgl_trkasir = ?,
									petugas = ?,
									id_user = ?,
									nm_pelanggan = ?,									
									tlp_pelanggan = ?,
									alamat_pelanggan = ?,
									kodetx = ?,
									ttl_trkasir = ?,
									diskon1 = ?,
									diskon2 = ?,
									dp_bayar = ?,
									sisa_bayar = ?,
									ket_trkasir = ?,
									id_carabayar = ?
									WHERE id_trkasir = ?");
			$ubah = $stmt_update_trkasir->execute([
										$_POST['tgl_trkasir'],
										$_POST['petugas'],
										$_POST['id_user'],
										$_POST['nm_pelanggan'],
										$_POST['tlp_pelanggan'],
										$_POST['alamat_pelanggan'],
										$_POST['kodetx'],
										$_POST['ttl_trkasir'],
										$_POST['diskon1'],
										$_POST['diskon2'],
										$_POST['dp_bayar'],
										$_POST['sisa_bayar'],
										$_POST['ket_trkasir'],
										$_POST['id_carabayar'],
										$_POST['id_trkasir']
									]);

        $stmt_update_detail = $db->prepare("update trkasir_detail set idadmin = ? where kd_trkasir = ?");
        $stmt_update_detail->execute([$_POST['id_user'], $_POST['kd_trkasir']]);

        if($ubah){
            $data['message'] = 'success';
    		echo json_encode($data);
    	} else {
    		$data['message'] = 'failed';
    		echo json_encode($data);
    	}
		//echo "<script type='text/javascript'>alert('Transkasi berhasil Ubah !');window.location='../../media_admin.php?module=".$module."'</script>";


	}
	//Hapus Proyek
	elseif ($module == 'trkasir' and $act == 'hapus') {
        
		if ($_SESSION['level'] != 'pemilik') {
			echo "<script type='text/javascript'>window.location='../../media_admin.php?module=" . $module . "'</script>";
		} else {
			//update bagian stok dulu
			//ambil data induk
			$ambildatainduk = $db->prepare("SELECT * FROM trkasir WHERE id_trkasir=?");
			$ambildatainduk->execute([$_GET['id']]);
			$r1 = $ambildatainduk->fetch(PDO::FETCH_ASSOC);
			$kd_trkasir = $r1['kd_trkasir'];
            
			//loop data detail
			$ambildatadetail = $db->prepare("SELECT * FROM trkasir_detail WHERE kd_trkasir=?");
			$ambildatadetail->execute([$kd_trkasir]);
			
			while ($r = $ambildatadetail->fetch(PDO::FETCH_ASSOC)) {

				$id_dtrkasir    = $r['id_dtrkasir'];
				$id_barang      = $r['id_barang'];
				$qty_dtrkasir   = $r['qty_dtrkasir'];

				// $stmt_insert_restore = $db->prepare("INSERT INTO trkasir_restore(
				// 		kd_trkasir, petugas, shift, tgl_trkasir, nm_pelanggan, tlp_pelanggan, alamat_pelanggan,
				// 		ttl_trkasir, dp_bayar, diskon1, diskon2, sisa_bayar, ket_trkasir, id_carabayar, id_barang,
				// 		kd_barang, nmbrg_dtrkasir, qty_dtrkasir, sat_dtrkasir, hrgjual_dtrkasir, hrgttl_dtrkasir)
				// 	VALUES(
				// 		?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
				// $stmt_insert_restore->execute([
				// 		    $r1['kd_trkasir'], $r1['petugas'], $r1['shift'], $r1['tgl_trkasir'], $r1['nm_pelanggan'], $r1['tlp_pelanggan'], $r1['alamat_pelanggan'], $r1['ttl_trkasir'], $r1['dp_bayar'], $r1['diskon1'], $r1['diskon2'], $r1['sisa_bayar'], $r1['ket_trkasir'], $r1['id_carabayar'], $r['id_barang'], $r['kd_barang'], $r['nmbrg_dtrkasir'], $r['qty_dtrkasir'], $r['sat_dtrkasir'], $r['hrgjual_dtrkasir'], $r['hrgttl_dtrkasir']
				// 		]);

				//update stok
				$cekstok = $db->prepare("SELECT id_barang, stok_barang FROM barang 
			                            WHERE id_barang=?");
				$cekstok->execute([$id_barang]);
				$rst = $cekstok->fetch(PDO::FETCH_ASSOC);

				$stok_barang = $rst['stok_barang'];
				$stokakhir = $stok_barang + $qty_dtrkasir;

				$stmt_update_barang = $db->prepare("UPDATE barang SET stok_barang = ? WHERE id_barang = ?");
				$stmt_update_barang->execute([$stokakhir, $id_barang]);

                // Insert into history
                $stmt_insert_hist = $db->prepare("INSERT INTO trkasir_detail_hist (
                                                            kd_trkasir,
                                                            id_barang,
                                                            kd_barang,
                                                            nmbrg_dtrkasir,
                                                            qty_dtrkasir,
                                                            sat_dtrkasir,
                                                            hrgjual_dtrkasir,
                                                            disc,
                                                            hrgttl_dtrkasir,
                                                            no_batch,
                                                            exp_date,
                                                            waktu,
                                                            tipe,
                                                            komisi,
                                                            idadmin
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
                                                            ?
                                                            )");
                $stmt_insert_hist->execute([$r['kd_trkasir'],
											$r['id_barang'],
											$r['kd_barang'],
											$r['nmbrg_dtrkasir'],
											$r['qty_dtrkasir'],
											$r['sat_dtrkasir'],
											$r['hrgjual_dtrkasir'],
											$r['disc'],
											$r['hrgttl_dtrkasir'],
											$r['no_batch'],
											$r['exp_date'],
											$r['waktu'],
											$r['tipe'],
											$r['komisi'],
											$r['idadmin']]);
				$stmt_del_detail = $db->prepare("DELETE FROM trkasir_detail WHERE id_dtrkasir = ?");
				$stmt_del_detail->execute([$id_dtrkasir]);
				
				$stmt_del_komisi = $db->prepare("DELETE FROM komisi_pegawai WHERE id_dtrkasir = ?");
				$stmt_del_komisi->execute([$id_dtrkasir]);
				$stmt_del_batch = $db->prepare("DELETE FROM batch WHERE kd_transaksi = ? AND no_batch=? AND status = 'keluar'");
				$stmt_del_batch->execute([$r['kd_trkasir'], $r['no_batch']]);
	
			}

            $stmt_update_poin = $db->prepare("UPDATE pelanggan SET total_poin = (total_poin - :tambahan_poin) + :redeem_poin WHERE id_pelanggan = :id_pelanggan");
            $stmt_update_poin->execute([
                ':tambahan_poin'    => $r1['tambahan_poin'],
                ':redeem_poin'      => $r1['redeem_poin'],
                ':id_pelanggan'     => $r1['id_pelanggan']
            ]);
            
			$stmt_del_trkasir = $db->prepare("DELETE FROM trkasir WHERE id_trkasir = ?");
			$stmt_del_trkasir->execute([$_GET['id']]);
            $stmt_del_karstok = $db->prepare("DELETE FROM kartu_stok WHERE kode_transaksi = ?");
            $stmt_del_karstok->execute([$kd_trkasir]);

            $module2 = $_GET['module2'];
			echo "<script type='text/javascript'>alert('Data berhasil dihapus !');window.location='../../media_admin.php?module=" . $module . "'</script>";
		}
	}
}
