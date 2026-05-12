<?php
session_start();
if (empty($_SESSION['username']) and empty($_SESSION['passuser'])) {
	echo "<link href='style.css' rel='stylesheet' type='text/css'>
 <center>Untuk mengakses modul, Anda harus login <br>";
	echo "<a href=../../index.php><b>LOGIN</b></a></center>";
} else {
	include "../../../configurasi/koneksi.php";

	$module = $_GET['module'];
	$act = $_GET['act'];

	// Input MESO
	if ($module == 'meso' and $act == 'input') {
		try {
			// Ambil data from POST
			$id_pelanggan = $_POST['id_pelanggan'];
			$kode_sumber_data = $_POST['kode_sumber_data'];
			
			// Data Penderita
			$nama_singkat = $_POST['nama_singkat'];
			$umur = $_POST['umur'];
			$suku = $_POST['suku'];
			$berat_badan = $_POST['berat_badan'];
			$pekerjaan = $_POST['pekerjaan'];
			$jenis_kelamin = $_POST['jenis_kelamin'];
			$status_hamil = $_POST['status_hamil'];
			
			// Penyakit
			$penyakit_utama = $_POST['penyakit_utama'];
			$gangguan_ginjal = isset($_POST['gangguan_ginjal']) ? 1 : 0;
			$gangguan_hati = isset($_POST['gangguan_hati']) ? 1 : 0;
			$alergi = isset($_POST['alergi']) ? 1 : 0;
			$kondisi_medis_lain = isset($_POST['kondisi_medis_lain']) ? 1 : 0;
			$kondisi_medis_lain_ket = $_POST['kondisi_medis_lain_ket'];
			$kesudahan_penyakit = $_POST['kesudahan_penyakit'];
			
			// ESO
			$manifestasi_eso = $_POST['manifestasi_eso'];
			$masalah_mutu_produk = $_POST['masalah_mutu_produk'];
			$tanggal_mula_eso = $_POST['tanggal_mula_eso'];
			$kesudahan_eso = $_POST['kesudahan_eso'];
			$riwayat_eso = $_POST['riwayat_eso'];
			
			// Obat - simpan sebagai JSON
			$data_obat = array();
			if (isset($_POST['obat_nama']) && is_array($_POST['obat_nama'])) {
				for ($i = 0; $i < count($_POST['obat_nama']); $i++) {
					if (!empty($_POST['obat_nama'][$i])) {
						$data_obat[] = array(
							'nama' => $_POST['obat_nama'][$i],
							'bentuk' => $_POST['obat_bentuk'][$i] ?? '',
							'batch' => $_POST['obat_batch'][$i] ?? '',
							'cara' => $_POST['obat_cara'][$i] ?? '',
							'dosis' => $_POST['obat_dosis'][$i] ?? '',
							'indikasi' => $_POST['obat_indikasi'][$i] ?? '',
							'tgl_mula' => $_POST['obat_tgl_mula'][$i] ?? '',
							'tgl_akhir' => $_POST['obat_tgl_akhir'][$i] ?? '',
							'jkn' => isset($_POST['obat_jkn'][$i]) ? 1 : 0,
							'dicurigai' => isset($_POST['obat_dicurigai'][$i]) ? 1 : 0
						);
					}
				}
			}
			$data_obat_json = json_encode($data_obat);
			
			// Keterangan tambahan
			$keterangan_tambahan = $_POST['keterangan_tambahan'];
			$data_laboratorium = $_POST['data_laboratorium'];
			$tanggal_pemeriksaan_lab = !empty($_POST['tanggal_pemeriksaan_lab']) ? $_POST['tanggal_pemeriksaan_lab'] : null;
			
			// Pelapor
			$tanggal_laporan = $_POST['tanggal_laporan'];
			$nama_pelapor = $_POST['nama_pelapor'];
			$created_by = $_SESSION['namalengkap'];
			
			// Insert ke database
			$stmt = $db->prepare("INSERT INTO meso (
				id_pelanggan, kode_sumber_data, nama_singkat, umur, suku, berat_badan, pekerjaan,
				jenis_kelamin, status_hamil, penyakit_utama, gangguan_ginjal, gangguan_hati,
				alergi, kondisi_medis_lain, kondisi_medis_lain_ket, kesudahan_penyakit,
				manifestasi_eso, masalah_mutu_produk, tanggal_mula_eso, kesudahan_eso, riwayat_eso,
				data_obat, keterangan_tambahan, data_laboratorium, tanggal_pemeriksaan_lab,
				tanggal_laporan, nama_pelapor, created_by
			) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
			
			$stmt->execute([
				$id_pelanggan, $kode_sumber_data, $nama_singkat, $umur, $suku, $berat_badan, $pekerjaan,
				$jenis_kelamin, $status_hamil, $penyakit_utama, $gangguan_ginjal, $gangguan_hati,
				$alergi, $kondisi_medis_lain, $kondisi_medis_lain_ket, $kesudahan_penyakit,
				$manifestasi_eso, $masalah_mutu_produk, $tanggal_mula_eso, $kesudahan_eso, $riwayat_eso,
				$data_obat_json, $keterangan_tambahan, $data_laboratorium, $tanggal_pemeriksaan_lab,
				$tanggal_laporan, $nama_pelapor, $created_by
			]);
			
			echo "<script type='text/javascript'>alert('Data MESO berhasil disimpan!');window.location='../../media_admin.php?module=meso'</script>";
			
		} catch (PDOException $e) {
			echo "<script type='text/javascript'>alert('Error: " . $e->getMessage() . "');history.go(-1);</script>";
		}
	}
	
	// Update MESO
	elseif ($module == 'meso' and $act == 'update') {
		try {
			$id_meso = $_POST['id'];
			$id_pelanggan = $_POST['id_pelanggan'];
			$kode_sumber_data = $_POST['kode_sumber_data'];
			
			// Data Penderita
			$nama_singkat = $_POST['nama_singkat'];
			$umur = $_POST['umur'];
			$suku = $_POST['suku'];
			$berat_badan = $_POST['berat_badan'];
			$pekerjaan = $_POST['pekerjaan'];
			$jenis_kelamin = $_POST['jenis_kelamin'];
			$status_hamil = $_POST['status_hamil'];
			
			// Penyakit
			$penyakit_utama = $_POST['penyakit_utama'];
			$gangguan_ginjal = isset($_POST['gangguan_ginjal']) ? 1 : 0;
			$gangguan_hati = isset($_POST['gangguan_hati']) ? 1 : 0;
			$alergi = isset($_POST['alergi']) ? 1 : 0;
			$kondisi_medis_lain = isset($_POST['kondisi_medis_lain']) ? 1 : 0;
			$kondisi_medis_lain_ket = $_POST['kondisi_medis_lain_ket'];
			$kesudahan_penyakit = $_POST['kesudahan_penyakit'];
			
			// ESO
			$manifestasi_eso = $_POST['manifestasi_eso'];
			$masalah_mutu_produk = $_POST['masalah_mutu_produk'];
			$tanggal_mula_eso = $_POST['tanggal_mula_eso'];
			$kesudahan_eso = $_POST['kesudahan_eso'];
			$riwayat_eso = $_POST['riwayat_eso'];
			
			// Obat - simpan sebagai JSON
			$data_obat = array();
			if (isset($_POST['obat_nama']) && is_array($_POST['obat_nama'])) {
				for ($i = 0; $i < count($_POST['obat_nama']); $i++) {
					if (!empty($_POST['obat_nama'][$i])) {
						$data_obat[] = array(
							'nama' => $_POST['obat_nama'][$i],
							'bentuk' => $_POST['obat_bentuk'][$i] ?? '',
							'batch' => $_POST['obat_batch'][$i] ?? '',
							'cara' => $_POST['obat_cara'][$i] ?? '',
							'dosis' => $_POST['obat_dosis'][$i] ?? '',
							'indikasi' => $_POST['obat_indikasi'][$i] ?? '',
							'tgl_mula' => $_POST['obat_tgl_mula'][$i] ?? '',
							'tgl_akhir' => $_POST['obat_tgl_akhir'][$i] ?? '',
							'jkn' => isset($_POST['obat_jkn'][$i]) ? 1 : 0,
							'dicurigai' => isset($_POST['obat_dicurigai'][$i]) ? 1 : 0
						);
					}
				}
			}
			$data_obat_json = json_encode($data_obat);
			
			// Keterangan tambahan
			$keterangan_tambahan = $_POST['keterangan_tambahan'];
			$data_laboratorium = $_POST['data_laboratorium'];
			$tanggal_pemeriksaan_lab = !empty($_POST['tanggal_pemeriksaan_lab']) ? $_POST['tanggal_pemeriksaan_lab'] : null;
			
			// Pelapor
			$tanggal_laporan = $_POST['tanggal_laporan'];
			$nama_pelapor = $_POST['nama_pelapor'];
		
		// Update ke database
		$stmt = $db->prepare("UPDATE meso SET 
			kode_sumber_data = ?, nama_singkat = ?, umur = ?, suku = ?, berat_badan = ?, pekerjaan = ?,
			jenis_kelamin = ?, status_hamil = ?, penyakit_utama = ?, gangguan_ginjal = ?, gangguan_hati = ?,
			alergi = ?, kondisi_medis_lain = ?, kondisi_medis_lain_ket = ?, kesudahan_penyakit = ?,
			manifestasi_eso = ?, masalah_mutu_produk = ?, tanggal_mula_eso = ?, kesudahan_eso = ?, riwayat_eso = ?,
			data_obat = ?, keterangan_tambahan = ?, data_laboratorium = ?, tanggal_pemeriksaan_lab = ?,
			tanggal_laporan = ?, nama_pelapor = ?
			WHERE id_meso = ?");
		
		$stmt->execute([
			$kode_sumber_data, $nama_singkat, $umur, $suku, $berat_badan, $pekerjaan,
			$jenis_kelamin, $status_hamil, $penyakit_utama, $gangguan_ginjal, $gangguan_hati,
			$alergi, $kondisi_medis_lain, $kondisi_medis_lain_ket, $kesudahan_penyakit,
			$manifestasi_eso, $masalah_mutu_produk, $tanggal_mula_eso, $kesudahan_eso, $riwayat_eso,
			$data_obat_json, $keterangan_tambahan, $data_laboratorium, $tanggal_pemeriksaan_lab,
			$tanggal_laporan, $nama_pelapor, $id_meso
		]);
		
		echo "<script type='text/javascript'>alert('Data MESO berhasil diupdate!');window.location='../../media_admin.php?module=meso'</script>";
		
	} catch (PDOException $e) {
			echo "<script type='text/javascript'>alert('Error: " . $e->getMessage() . "');history.go(-1);</script>";
		}
	}
	
	// Hapus MESO
	elseif ($module == 'meso' and $act == 'hapus') {
		$id_meso = $_GET['id'];
		$stmt = $db->prepare("DELETE FROM meso WHERE id_meso = ?");
		$stmt->execute([$id_meso]);
		
		header('location:../../media_admin.php?module=meso');
	}
}
?>
