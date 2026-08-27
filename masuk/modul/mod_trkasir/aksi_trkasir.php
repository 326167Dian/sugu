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
	include "../../../configurasi/fungsi_perubahan_trkasir.php";
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
            } elseif (empty($_POST['id_user'])) {
                $data['message'] = 'failed';
                $data['error'] = 'Petugas pelayanan belum dipilih.';
                echo json_encode($data);
            } else {
                $db->beginTransaction();


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
                $pelanggan = $stmt_pelanggan->fetch(PDO::FETCH_ASSOC);
                
                $total_poin = 0;
                $poin_awal = 0;
                if($stmt_pelanggan->rowCount() > 0){
                    
                    $poin_awal = (is_null($pelanggan['total_poin']))? 0:$pelanggan['total_poin'];
                    if ($poin['is_kelipatan'] == 'ya') {
                        $total_poin = floor($tk['total_harga'] / $poin['min_penjualan']) * $poin['poin_pelanggan'];
                    } elseif ($poin['is_kelipatan'] == 'no' && floor($tk['total_harga']) >= $poin['min_penjualan']) {
                        $total_poin = $poin['poin_pelanggan'];
                    }
                            
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
                
    			$db->commit();
    			$data['message'] = 'success';
    			echo json_encode($data);
    		} else {
    			$db->rollBack();
    			$data['message'] = 'failed';
    			echo json_encode($data);
    		}

            }
        } catch (Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
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

        if (empty($_POST['id_user'])) {
            echo json_encode([
                'message' => 'failed',
                'error' => 'Petugas pelayanan belum dipilih.'
            ]);
            exit;
        }

        try {
            $db->beginTransaction();

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
            $db->commit();
            $data['message'] = 'success';
    		echo json_encode($data);
    	} else {
    		$db->rollBack();
    		$data['message'] = 'failed';
    		echo json_encode($data);
    	}
        } catch (Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            echo json_encode([
                'message' => 'failed',
                'error' => $e->getMessage()
            ]);
        }
		//echo "<script type='text/javascript'>alert('Transkasi berhasil Ubah !');window.location='../../media_admin.php?module=".$module."'</script>";


	}
	//Hapus Proyek
	elseif ($module == 'trkasir' and $act == 'hapus') {

		if ($_SESSION['level'] != 'pemilik') {
			echo "<script type='text/javascript'>window.location='../../media_admin.php?module=" . $module . "'</script>";
		} else {
			// DDL (ALTER/CREATE TABLE) menyebabkan implicit commit di MySQL, jadi harus
			// dijalankan SEBELUM beginTransaction() supaya tidak menutup transaction secara diam-diam.
			pastikan_skema_perubahan_trkasir($db);

			try {
				$db->beginTransaction();

				$id_trkasir = isset($_GET['id']) ? (int)$_GET['id'] : 0;
				if ($id_trkasir <= 0) {
					throw new Exception('ID transaksi tidak valid.');
				}

				// ambil data induk
				$ambildatainduk = $db->prepare("SELECT * FROM trkasir WHERE id_trkasir=?");
				$ambildatainduk->execute([$id_trkasir]);
				$r1 = $ambildatainduk->fetch(PDO::FETCH_ASSOC);

				if (!$r1) {
					throw new Exception('Data transaksi tidak ditemukan.');
				}

				$kd_trkasir = $r1['kd_trkasir'];
				$tipetx_akhir = isset($r1['tipetx']) ? $r1['tipetx'] : 1;
				$idAdminHapus = isset($_SESSION['idadmin']) ? $_SESSION['idadmin'] : null;

				// loop data detail
				$ambildatadetail = $db->prepare("SELECT * FROM trkasir_detail WHERE kd_trkasir=?");
				$ambildatadetail->execute([$kd_trkasir]);

				$bundleRestore = [];

				while ($r = $ambildatadetail->fetch(PDO::FETCH_ASSOC)) {

					$id_dtrkasir    = $r['id_dtrkasir'];
					$id_barang      = $r['id_barang'];
					$qty_dtrkasir   = $r['qty_dtrkasir'];

					// Kumpulkan qty bundle yang perlu dikembalikan (per kd_bundle, dihitung sekali)
					$kd_bundle = isset($r['kd_bundle']) ? $r['kd_bundle'] : '';
					if (substr($kd_bundle, 0, 4) == 'BUND') {
						$get_bundle_detail = $db->prepare("SELECT qty_barang FROM bundle_detail WHERE kd_bundle = ? AND kd_barang = ?");
						$get_bundle_detail->execute([$kd_bundle, $r['kd_barang']]);
						$rbundle = $get_bundle_detail->fetch(PDO::FETCH_ASSOC);
						if ($rbundle && $rbundle['qty_barang'] > 0) {
							$bundleRestore[$kd_bundle] = $qty_dtrkasir / $rbundle['qty_barang'];
						}
					}

					// update stok
					$cekstok = $db->prepare("SELECT id_barang, stok_barang FROM barang WHERE id_barang=?");
					$cekstok->execute([$id_barang]);
					$rst = $cekstok->fetch(PDO::FETCH_ASSOC);

					$stok_barang = $rst ? $rst['stok_barang'] : 0;
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
						idadmin,
						tipetx_asal,
						tipetx_hapus,
						id_admin_hapus
					) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
					$stmt_insert_hist->execute([
						$r['kd_trkasir'],
						$r['id_barang'],
						$r['kd_barang'],
						$r['nmbrg_dtrkasir'],
						$r['qty_dtrkasir'],
						$r['sat_dtrkasir'],
						$r['hrgjual_dtrkasir'],
						$r['disc'],
						$r['hrgttl_dtrkasir'],
						$r['no_batch'] !== null ? $r['no_batch'] : '',
						$r['exp_date'],
						$r['waktu'],
						$r['tipe'],
						$r['komisi'],
						$r['idadmin'],
						isset($r['tipetx']) ? $r['tipetx'] : 1,
						$tipetx_akhir,
						$idAdminHapus
					]);

					// Snapshot kondisi akhir transaksi (untuk fitur PERUBAHAN TRANSAKSI & UNDO TRANSAKSI TERHAPUS)
					$stmt_insert_restore = $db->prepare("INSERT INTO trkasir_restore (
						kd_trkasir, petugas, shift, tgl_trkasir, nm_pelanggan, tlp_pelanggan, alamat_pelanggan,
						ttl_trkasir, dp_bayar, diskon1, diskon2, sisa_bayar, ket_trkasir, id_carabayar,
						id_dtrkasir, id_barang, kd_barang, nmbrg_dtrkasir, qty_dtrkasir, sat_dtrkasir, hrgjual_dtrkasir, hrgttl_dtrkasir,
						disc, resep, modal, profit, no_batch, exp_date, waktu, tipe, komisi, idadmin,
						kd_bundle, nm_bundle, tipetx, id_admin_hapus,
						id_user, id_pelanggan, kodetx, jenistx, waktu_trx, poin_awal, tambahan_poin, redeem_poin
					) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
					$stmt_insert_restore->execute([
						$r1['kd_trkasir'], $r1['petugas'], $r1['shift'], $r1['tgl_trkasir'], $r1['nm_pelanggan'], $r1['tlp_pelanggan'], $r1['alamat_pelanggan'],
						$r1['ttl_trkasir'], $r1['dp_bayar'], $r1['diskon1'], $r1['diskon2'], $r1['sisa_bayar'], $r1['ket_trkasir'], $r1['id_carabayar'],
						$id_dtrkasir, $r['id_barang'], $r['kd_barang'], $r['nmbrg_dtrkasir'], $r['qty_dtrkasir'], $r['sat_dtrkasir'], $r['hrgjual_dtrkasir'], $r['hrgttl_dtrkasir'],
						$r['disc'], isset($r['resep']) ? $r['resep'] : null, $r['modal'], $r['profit'], $r['no_batch'], $r['exp_date'], $r['waktu'], $r['tipe'], $r['komisi'], $r['idadmin'],
						isset($r['kd_bundle']) ? $r['kd_bundle'] : null, isset($r['nm_bundle']) ? $r['nm_bundle'] : null, $tipetx_akhir, $idAdminHapus,
						$r1['id_user'], $r1['id_pelanggan'], $r1['kodetx'], $r1['jenistx'], $r1['waktu_trx'], $r1['poin_awal'], $r1['tambahan_poin'], $r1['redeem_poin']
					]);

					$stmt_del_detail = $db->prepare("DELETE FROM trkasir_detail WHERE id_dtrkasir = ?");
					$stmt_del_detail->execute([$id_dtrkasir]);

					$stmt_del_komisi = $db->prepare("DELETE FROM komisi_pegawai WHERE id_dtrkasir = ?");
					$stmt_del_komisi->execute([$id_dtrkasir]);

					$stmt_del_batch = $db->prepare("DELETE FROM batch WHERE kd_transaksi = ? AND no_batch=? AND status = 'keluar'");
					$stmt_del_batch->execute([$r['kd_trkasir'], $r['no_batch']]);
				}

				// Kembalikan stok bundle
				foreach ($bundleRestore as $kdBundleRestore => $qtyBundleRestore) {
					$stmt_update_bundle = $db->prepare("UPDATE bundle SET qty_bundle = qty_bundle + :qty_bundle WHERE kd_bundle = :kd_bundle");
					$stmt_update_bundle->execute([
						':qty_bundle' => $qtyBundleRestore,
						':kd_bundle'  => $kdBundleRestore
					]);
				}

				// rollback poin pelanggan
				if (!empty($r1['id_pelanggan'])) {
					$stmt_update_poin = $db->prepare("UPDATE pelanggan SET total_poin = (total_poin - :tambahan_poin) + :redeem_poin WHERE id_pelanggan = :id_pelanggan");
					$stmt_update_poin->execute([
						':tambahan_poin'    => isset($r1['tambahan_poin']) ? $r1['tambahan_poin'] : 0,
						':redeem_poin'      => isset($r1['redeem_poin']) ? $r1['redeem_poin'] : 0,
						':id_pelanggan'     => $r1['id_pelanggan']
					]);
				}

				// Hapus header transaksi: utamakan berdasarkan kd_trkasir agar konsisten
				$stmt_del_trkasir = $db->prepare("DELETE FROM trkasir WHERE kd_trkasir = ?");
				$stmt_del_trkasir->execute([$kd_trkasir]);

				if ($stmt_del_trkasir->rowCount() < 1) {
					// fallback berdasarkan id_trkasir
					$stmt_del_trkasir_fallback = $db->prepare("DELETE FROM trkasir WHERE id_trkasir = ?");
					$stmt_del_trkasir_fallback->execute([$id_trkasir]);

					if ($stmt_del_trkasir_fallback->rowCount() < 1) {
						throw new Exception('Gagal menghapus data header trkasir.');
					}
				}

				$stmt_del_karstok = $db->prepare("DELETE FROM kartu_stok WHERE kode_transaksi = ?");
				$stmt_del_karstok->execute([$kd_trkasir]);

				// Transaksi Market Place berasal dari order_online (kd_trkasir = order_online.kode_pesanan).
				// Ikut dihapus di sini, dalam transaction yang sama, supaya tidak ada data pesanan online
				// yang nyangkut/yatim setelah transaksi kasirnya dihapus. Untuk transaksi reguler (bukan
				// Market Place) query ini tidak menemukan apa-apa, jadi aman dijalankan selalu.
				$cekOrderOnline = $db->prepare("SELECT id FROM order_online WHERE kode_pesanan = ?");
				$cekOrderOnline->execute([$kd_trkasir]);
				$orderOnline = $cekOrderOnline->fetch(PDO::FETCH_ASSOC);
				if ($orderOnline) {
					$db->prepare("DELETE FROM order_online_item WHERE order_id = ?")->execute([$orderOnline['id']]);
					$db->prepare("DELETE FROM order_online WHERE id = ?")->execute([$orderOnline['id']]);
				}

				$db->commit();
				echo "<script type='text/javascript'>alert('Data berhasil dihapus !');window.location='../../media_admin.php?module=" . $module . "'</script>";
			} catch (Throwable $e) {
				if ($db->inTransaction()) {
					$db->rollBack();
				}
				echo "<script type='text/javascript'>alert('Gagal menghapus data: " . addslashes($e->getMessage()) . "');window.location='../../media_admin.php?module=" . $module . "'</script>";
			}
		}
	}
	// Undo transaksi yang sudah dihapus total (kebalikan dari act == 'hapus')
	elseif ($module == 'trkasir' and $act == 'restore_hapus') {

		if ($_SESSION['level'] != 'pemilik') {
			echo "<script type='text/javascript'>alert('Menu ini hanya untuk pemilik.');window.location='../../media_admin.php?module=" . $module . "'</script>";
		} else {
			include_once "../../../configurasi/fungsi_perubahan_trkasir.php";
			// DDL (ALTER/CREATE TABLE) menyebabkan implicit commit di MySQL, jadi harus
			// dijalankan SEBELUM beginTransaction() supaya tidak menutup transaction secara diam-diam.
			pastikan_skema_perubahan_trkasir($db);

			try {
				$kd_trkasir = isset($_GET['kd_trkasir']) ? trim($_GET['kd_trkasir']) : '';
				if ($kd_trkasir === '') {
					throw new Exception('Kode transaksi tidak valid.');
				}

				$cekSudahAda = $db->prepare("SELECT 1 FROM trkasir WHERE kd_trkasir = ? LIMIT 1");
				$cekSudahAda->execute([$kd_trkasir]);
				if ($cekSudahAda->rowCount() > 0) {
					throw new Exception('Transaksi ini sudah aktif, tidak perlu di-restore lagi.');
				}

				$ambilRestore = $db->prepare("SELECT * FROM trkasir_restore WHERE kd_trkasir = ? ORDER BY id_butrkasir ASC");
				$ambilRestore->execute([$kd_trkasir]);
				$itemRestore = $ambilRestore->fetchAll(PDO::FETCH_ASSOC);

				if (count($itemRestore) < 1) {
					throw new Exception('Data transaksi terhapus tidak ditemukan.');
				}

				// Validasi stok dulu SEBELUM menulis apapun -- kalau ada barang yang stoknya
				// tidak cukup, seluruh proses restore dibatalkan (tidak boleh minus).
				// Qty diakumulasi per id_barang dulu (satu barang bisa muncul di beberapa baris
				// kalau qty aslinya kepotong beberapa batch/FIFO), baru dibandingkan sekali ke stok.
				$totalQtyPerBarang = array();
				foreach ($itemRestore as $item) {
					$idBarang = $item['id_barang'];
					if (!isset($totalQtyPerBarang[$idBarang])) {
						$totalQtyPerBarang[$idBarang] = 0;
					}
					$totalQtyPerBarang[$idBarang] += (float) $item['qty_dtrkasir'];
				}

				$kurangStok = array();
				foreach ($totalQtyPerBarang as $idBarang => $qtyDibutuhkan) {
					$cekStok = $db->prepare("SELECT nm_barang, stok_barang FROM barang WHERE id_barang = ?");
					$cekStok->execute([$idBarang]);
					$brg = $cekStok->fetch(PDO::FETCH_ASSOC);

					$stokTersedia = $brg ? (float) $brg['stok_barang'] : 0;
					$namaBarang = $brg ? $brg['nm_barang'] : ('barang id ' . $idBarang);

					if ($stokTersedia < $qtyDibutuhkan) {
						$kurangStok[] = $namaBarang . ' (stok ' . $stokTersedia . ', butuh ' . $qtyDibutuhkan . ')';
					}
				}

				if (count($kurangStok) > 0) {
					throw new Exception('Stok tidak cukup untuk: ' . implode(', ', $kurangStok));
				}

				$db->beginTransaction();

				$header = $itemRestore[0];
				$idUser = !empty($header['id_user']) ? $header['id_user'] : $_SESSION['idadmin'];
				$idPelanggan = isset($header['id_pelanggan']) && $header['id_pelanggan'] !== null ? $header['id_pelanggan'] : 0;
				$kodetx = isset($header['kodetx']) && $header['kodetx'] !== null ? $header['kodetx'] : '';
				$jenistx = !empty($header['jenistx']) ? $header['jenistx'] : 1;
				$waktuTrx = !empty($header['waktu_trx']) ? $header['waktu_trx'] : date('Y-m-d H:i:s');
				$poinAwal = isset($header['poin_awal']) && $header['poin_awal'] !== null ? $header['poin_awal'] : 0;
				$tambahanPoin = isset($header['tambahan_poin']) && $header['tambahan_poin'] !== null ? $header['tambahan_poin'] : 0;
				$redeemPoin = isset($header['redeem_poin']) && $header['redeem_poin'] !== null ? $header['redeem_poin'] : 0;

				$stmt_insert_trkasir = $db->prepare("INSERT INTO trkasir (
						kd_trkasir, id_user, petugas, shift, tgl_trkasir, id_pelanggan, nm_pelanggan, tlp_pelanggan, alamat_pelanggan,
						kodetx, ttl_trkasir, dp_bayar, diskon1, diskon2, sisa_bayar, ket_trkasir, id_carabayar, jenistx, tipetx,
						waktu_trx, poin_awal, tambahan_poin, redeem_poin
					) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
				$stmt_insert_trkasir->execute([
					$header['kd_trkasir'], $idUser, $header['petugas'], $header['shift'], $header['tgl_trkasir'], $idPelanggan,
					$header['nm_pelanggan'], $header['tlp_pelanggan'], $header['alamat_pelanggan'], $kodetx, $header['ttl_trkasir'],
					$header['dp_bayar'], $header['diskon1'], $header['diskon2'], $header['sisa_bayar'], $header['ket_trkasir'],
					$header['id_carabayar'], $jenistx, $header['tipetx'], $waktuTrx, $poinAwal, $tambahanPoin, $redeemPoin
				]);

				foreach ($itemRestore as $item) {
					$disc = isset($item['disc']) ? $item['disc'] : 0;
					$resep = !empty($item['resep']) ? $item['resep'] : 'TIDAK';
					$modal = isset($item['modal']) ? $item['modal'] : 0;
					$profit = isset($item['profit']) ? $item['profit'] : 0;
					$noBatch = isset($item['no_batch']) ? $item['no_batch'] : '';
					$tipe = !empty($item['tipe']) ? $item['tipe'] : 1;
					$komisi = isset($item['komisi']) ? $item['komisi'] : 0;
					$idadmin = !empty($item['idadmin']) ? $item['idadmin'] : $idUser;
					$kdBundle = isset($item['kd_bundle']) ? $item['kd_bundle'] : '';
					$nmBundle = isset($item['nm_bundle']) ? $item['nm_bundle'] : '';

					$stmt_insert_detail = $db->prepare("INSERT INTO trkasir_detail (
							kd_trkasir, id_barang, kd_barang, nmbrg_dtrkasir, qty_dtrkasir, sat_dtrkasir, hrgjual_dtrkasir,
							disc, modal, profit, hrgttl_dtrkasir, no_batch, exp_date, waktu, tipe, komisi, idadmin, tipetx,
							resep, kd_bundle, nm_bundle
						) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
					$stmt_insert_detail->execute([
						$item['kd_trkasir'], $item['id_barang'], $item['kd_barang'], $item['nmbrg_dtrkasir'], $item['qty_dtrkasir'],
						$item['sat_dtrkasir'], $item['hrgjual_dtrkasir'], $disc, $modal, $profit, $item['hrgttl_dtrkasir'], $noBatch,
						$item['exp_date'], $item['waktu'], $tipe, $komisi, $idadmin, $item['tipetx'], $resep, $kdBundle, $nmBundle
					]);
					$idDtrkasirBaru = $db->lastInsertId();

					// UPDATE STOK ATOMIC - transaksi hidup lagi, barang keluar lagi dari rak.
					// Sudah divalidasi cukup di awal, tapi cek ulang di sini sebagai jaga-jaga race condition.
					$stmt_update_barang = $db->prepare("UPDATE barang SET
										stok_barang = stok_barang - :qty_dikurangi
										WHERE id_barang = :id_barang AND stok_barang >= :qty_dikurangi2");
					$stmt_update_barang->execute([
						':qty_dikurangi' => $item['qty_dtrkasir'],
						':id_barang' => $item['id_barang'],
						':qty_dikurangi2' => $item['qty_dtrkasir']
					]);
					if ($stmt_update_barang->rowCount() < 1) {
						throw new Exception('Stok ' . $item['nmbrg_dtrkasir'] . ' berubah menjadi tidak cukup saat proses restore berjalan.');
					}

					if ((float) $komisi != 0) {
						$db->prepare("INSERT INTO komisi_pegawai (kd_trkasir, id_dtrkasir, id_admin, ttl_komisi, tgl_komisi, status_komisi)
										VALUES (?, ?, ?, ?, ?, ?)")
							->execute([$item['kd_trkasir'], $idDtrkasirBaru, $idadmin, $item['qty_dtrkasir'] * $komisi, date('Y-m-d'), 'on']);
					}

					if ($noBatch !== '') {
						$db->prepare("INSERT INTO batch (tgl_transaksi, no_batch, exp_date, qty, satuan, kd_transaksi, kd_barang, status)
										VALUES (?, ?, ?, ?, ?, ?, ?, ?)")
							->execute([date('Y-m-d H:i:s'), $noBatch, $item['exp_date'], $item['qty_dtrkasir'], $item['sat_dtrkasir'], $item['kd_trkasir'], $item['kd_barang'], 'keluar']);
					}
				}

				if ($idPelanggan > 0) {
					$stmt_update_poin = $db->prepare("UPDATE pelanggan SET total_poin = (total_poin + :tambahan_poin) - :redeem_poin WHERE id_pelanggan = :id_pelanggan");
					$stmt_update_poin->execute([
						':tambahan_poin' => $tambahanPoin,
						':redeem_poin' => $redeemPoin,
						':id_pelanggan' => $idPelanggan
					]);
				}

				$db->prepare("INSERT INTO kartu_stok (kode_transaksi, tgl_sekarang) VALUES (?, ?)")
					->execute([$kd_trkasir, date('Y-m-d H:i:s')]);

				$db->prepare("DELETE FROM trkasir_restore WHERE kd_trkasir = ?")->execute([$kd_trkasir]);

				$db->commit();
				echo "<script type='text/javascript'>alert('Transaksi berhasil dikembalikan !');window.location='../../media_admin.php?module=" . $module . "&act=undo_deleted'</script>";
			} catch (Throwable $e) {
				if ($db->inTransaction()) {
					$db->rollBack();
				}
				echo "<script type='text/javascript'>alert('Gagal mengembalikan transaksi: " . addslashes($e->getMessage()) . "');window.location='../../media_admin.php?module=" . $module . "&act=undo_deleted'</script>";
			}
		}
	}
}
