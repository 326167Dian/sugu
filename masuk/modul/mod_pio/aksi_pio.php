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

	// Input PIO
	if ($module == 'pio' and $act == 'input') {
		try {
			// Ambil data from POST
			$id_pelanggan = $_POST['id_pelanggan'];
			$no_pio = $_POST['no_pio'];
			$tanggal = $_POST['tanggal'];
			$waktu = $_POST['waktu'];
			$metode = $_POST['metode'];
			
			// Identitas Penanya
			$nama_penanya = $_POST['nama_penanya'];
			$no_telp_penanya = $_POST['no_telp_penanya'];
			$status_penanya = $_POST['status_penanya'];
			$status_penanya_ket = $_POST['status_penanya_ket'];
			
			// Data Pasien
			$umur_pasien = !empty($_POST['umur_pasien']) ? $_POST['umur_pasien'] : null;
			$tinggi_pasien = !empty($_POST['tinggi_pasien']) ? $_POST['tinggi_pasien'] : null;
			$berat_pasien = !empty($_POST['berat_pasien']) ? $_POST['berat_pasien'] : null;
			$jenis_kelamin = $_POST['jenis_kelamin'] ?? null;
			$kehamilan = isset($_POST['kehamilan']) ? 1 : 0;
			$kehamilan_minggu = !empty($_POST['kehamilan_minggu']) ? $_POST['kehamilan_minggu'] : null;
			$menyusui = isset($_POST['menyusui']) ? 1 : 0;
			
			// Pertanyaan
			$uraian_pertanyaan = $_POST['uraian_pertanyaan'];
			
			// Jenis Pertanyaan - individual checkboxes
			$jenis_pertanyaan = isset($_POST['jenis_pertanyaan']) ? $_POST['jenis_pertanyaan'] : [];
			$jenis_identifikasi_obat = in_array('identifikasi_obat', $jenis_pertanyaan) ? 1 : 0;
			$jenis_stabilitas = in_array('stabilitas', $jenis_pertanyaan) ? 1 : 0;
			$jenis_farmakokinetika = in_array('farmakokinetika', $jenis_pertanyaan) ? 1 : 0;
			$jenis_interaksi_obat = in_array('interaksi_obat', $jenis_pertanyaan) ? 1 : 0;
			$jenis_dosis = in_array('dosis', $jenis_pertanyaan) ? 1 : 0;
			$jenis_farmakodinamika = in_array('farmakodinamika', $jenis_pertanyaan) ? 1 : 0;
			$jenis_harga_obat = in_array('harga_obat', $jenis_pertanyaan) ? 1 : 0;
			$jenis_keracunan = in_array('keracunan', $jenis_pertanyaan) ? 1 : 0;
			$jenis_ketersediaan_obat = in_array('ketersediaan_obat', $jenis_pertanyaan) ? 1 : 0;
			$jenis_kontra_indikasi = in_array('kontra_indikasi', $jenis_pertanyaan) ? 1 : 0;
			$jenis_efek_samping = in_array('efek_samping', $jenis_pertanyaan) ? 1 : 0;
			$jenis_cara_pemakaian = in_array('cara_pemakaian', $jenis_pertanyaan) ? 1 : 0;
			$jenis_penggunaan_terapeutik = in_array('penggunaan_terapeutik', $jenis_pertanyaan) ? 1 : 0;
			$jenis_lain_lain = in_array('lain_lain', $jenis_pertanyaan) ? 1 : 0;
			$jenis_lain_lain_ket = $_POST['jenis_pertanyaan_lain_lain_ket'];
			
			// Jawaban & Referensi
			$jawaban = $_POST['jawaban'];
			$referensi = $_POST['referensi'];
			$penyampaian_jawaban = $_POST['penyampaian_jawaban'];
			
			// Apoteker Penjawab
			$apoteker_penjawab = $_POST['apoteker_penjawab'];
			$tanggal_jawab = !empty($_POST['tanggal_jawab']) ? $_POST['tanggal_jawab'] : null;
			$waktu_jawab = !empty($_POST['waktu_jawab']) ? $_POST['waktu_jawab'] : null;
			$metode_jawab = $_POST['metode_jawab'] ?? null;
			$created_by = $_SESSION['namalengkap'];
			
			// Insert ke database
			$stmt = $db->prepare("INSERT INTO pio (
				id_pelanggan, no_pio, tanggal, waktu, metode,
				nama_penanya, no_telp_penanya, status_penanya, status_penanya_ket,
				umur_pasien, tinggi_pasien, berat_pasien, jenis_kelamin, kehamilan, kehamilan_minggu, menyusui,
				uraian_pertanyaan,
				jenis_pertanyaan_identifikasi_obat, jenis_pertanyaan_stabilitas, jenis_pertanyaan_farmakokinetika,
				jenis_pertanyaan_interaksi_obat, jenis_pertanyaan_dosis, jenis_pertanyaan_farmakodinamika,
				jenis_pertanyaan_harga_obat, jenis_pertanyaan_keracunan, jenis_pertanyaan_ketersediaan_obat,
				jenis_pertanyaan_kontra_indikasi, jenis_pertanyaan_efek_samping, jenis_pertanyaan_cara_pemakaian,
				jenis_pertanyaan_penggunaan_terapeutik, jenis_pertanyaan_lain_lain, jenis_pertanyaan_lain_lain_ket,
				jawaban, referensi, penyampaian_jawaban,
				apoteker_penjawab, tanggal_jawab, waktu_jawab, metode_jawab, created_by
			) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
			
			$stmt->execute([
				$id_pelanggan, $no_pio, $tanggal, $waktu, $metode,
				$nama_penanya, $no_telp_penanya, $status_penanya, $status_penanya_ket,
				$umur_pasien, $tinggi_pasien, $berat_pasien, $jenis_kelamin, $kehamilan, $kehamilan_minggu, $menyusui,
				$uraian_pertanyaan,
				$jenis_identifikasi_obat, $jenis_stabilitas, $jenis_farmakokinetika,
				$jenis_interaksi_obat, $jenis_dosis, $jenis_farmakodinamika,
				$jenis_harga_obat, $jenis_keracunan, $jenis_ketersediaan_obat,
				$jenis_kontra_indikasi, $jenis_efek_samping, $jenis_cara_pemakaian,
				$jenis_penggunaan_terapeutik, $jenis_lain_lain, $jenis_lain_lain_ket,
				$jawaban, $referensi, $penyampaian_jawaban,
				$apoteker_penjawab, $tanggal_jawab, $waktu_jawab, $metode_jawab, $created_by
			]);
			
			echo "<script type='text/javascript'>alert('Data PIO berhasil disimpan!');window.location='../../media_admin.php?module=pio'</script>";
			
		} catch (PDOException $e) {
			echo "<script type='text/javascript'>alert('Error: " . $e->getMessage() . "');history.go(-1);</script>";
		}
	}
	
	// Update PIO
	elseif ($module == 'pio' and $act == 'update') {
		try {
			$id_pio = $_POST['id'];
			$id_pelanggan = $_POST['id_pelanggan'];
			$no_pio = $_POST['no_pio'];
			$tanggal = $_POST['tanggal'];
			$waktu = $_POST['waktu'];
			$metode = $_POST['metode'];
			
			// Identitas Penanya
			$nama_penanya = $_POST['nama_penanya'];
			$no_telp_penanya = $_POST['no_telp_penanya'];
			$status_penanya = $_POST['status_penanya'];
			$status_penanya_ket = $_POST['status_penanya_ket'];
			
			// Data Pasien
			$umur_pasien = !empty($_POST['umur_pasien']) ? $_POST['umur_pasien'] : null;
			$tinggi_pasien = !empty($_POST['tinggi_pasien']) ? $_POST['tinggi_pasien'] : null;
			$berat_pasien = !empty($_POST['berat_pasien']) ? $_POST['berat_pasien'] : null;
			$jenis_kelamin = $_POST['jenis_kelamin'] ?? null;
			$kehamilan = isset($_POST['kehamilan']) ? 1 : 0;
			$kehamilan_minggu = !empty($_POST['kehamilan_minggu']) ? $_POST['kehamilan_minggu'] : null;
			$menyusui = isset($_POST['menyusui']) ? 1 : 0;
			
			// Pertanyaan
			$uraian_pertanyaan = $_POST['uraian_pertanyaan'];
			
			// Jenis Pertanyaan - individual checkboxes
			$jenis_pertanyaan = isset($_POST['jenis_pertanyaan']) ? $_POST['jenis_pertanyaan'] : [];
			$jenis_identifikasi_obat = in_array('identifikasi_obat', $jenis_pertanyaan) ? 1 : 0;
			$jenis_stabilitas = in_array('stabilitas', $jenis_pertanyaan) ? 1 : 0;
			$jenis_farmakokinetika = in_array('farmakokinetika', $jenis_pertanyaan) ? 1 : 0;
			$jenis_interaksi_obat = in_array('interaksi_obat', $jenis_pertanyaan) ? 1 : 0;
			$jenis_dosis = in_array('dosis', $jenis_pertanyaan) ? 1 : 0;
			$jenis_farmakodinamika = in_array('farmakodinamika', $jenis_pertanyaan) ? 1 : 0;
			$jenis_harga_obat = in_array('harga_obat', $jenis_pertanyaan) ? 1 : 0;
			$jenis_keracunan = in_array('keracunan', $jenis_pertanyaan) ? 1 : 0;
			$jenis_ketersediaan_obat = in_array('ketersediaan_obat', $jenis_pertanyaan) ? 1 : 0;
			$jenis_kontra_indikasi = in_array('kontra_indikasi', $jenis_pertanyaan) ? 1 : 0;
			$jenis_efek_samping = in_array('efek_samping', $jenis_pertanyaan) ? 1 : 0;
			$jenis_cara_pemakaian = in_array('cara_pemakaian', $jenis_pertanyaan) ? 1 : 0;
			$jenis_penggunaan_terapeutik = in_array('penggunaan_terapeutik', $jenis_pertanyaan) ? 1 : 0;
			$jenis_lain_lain = in_array('lain_lain', $jenis_pertanyaan) ? 1 : 0;
			$jenis_lain_lain_ket = $_POST['jenis_pertanyaan_lain_lain_ket'];
			
			// Jawaban & Referensi
			$jawaban = $_POST['jawaban'];
			$referensi = $_POST['referensi'];
			$penyampaian_jawaban = $_POST['penyampaian_jawaban'];
			
			// Apoteker Penjawab
			$apoteker_penjawab = $_POST['apoteker_penjawab'];
			$tanggal_jawab = !empty($_POST['tanggal_jawab']) ? $_POST['tanggal_jawab'] : null;
			$waktu_jawab = !empty($_POST['waktu_jawab']) ? $_POST['waktu_jawab'] : null;
			$metode_jawab = $_POST['metode_jawab'] ?? null;
		
		// Update ke database
		$stmt = $db->prepare("UPDATE pio SET 
			no_pio = ?, tanggal = ?, waktu = ?, metode = ?,
			nama_penanya = ?, no_telp_penanya = ?, status_penanya = ?, status_penanya_ket = ?,
			umur_pasien = ?, tinggi_pasien = ?, berat_pasien = ?, jenis_kelamin = ?, 
			kehamilan = ?, kehamilan_minggu = ?, menyusui = ?,
			uraian_pertanyaan = ?,
			jenis_pertanyaan_identifikasi_obat = ?, jenis_pertanyaan_stabilitas = ?, jenis_pertanyaan_farmakokinetika = ?,
			jenis_pertanyaan_interaksi_obat = ?, jenis_pertanyaan_dosis = ?, jenis_pertanyaan_farmakodinamika = ?,
			jenis_pertanyaan_harga_obat = ?, jenis_pertanyaan_keracunan = ?, jenis_pertanyaan_ketersediaan_obat = ?,
			jenis_pertanyaan_kontra_indikasi = ?, jenis_pertanyaan_efek_samping = ?, jenis_pertanyaan_cara_pemakaian = ?,
			jenis_pertanyaan_penggunaan_terapeutik = ?, jenis_pertanyaan_lain_lain = ?, jenis_pertanyaan_lain_lain_ket = ?,
			jawaban = ?, referensi = ?, penyampaian_jawaban = ?,
			apoteker_penjawab = ?, tanggal_jawab = ?, waktu_jawab = ?, metode_jawab = ?
			WHERE id_pio = ?");
		
		$stmt->execute([
			$no_pio, $tanggal, $waktu, $metode,
			$nama_penanya, $no_telp_penanya, $status_penanya, $status_penanya_ket,
			$umur_pasien, $tinggi_pasien, $berat_pasien, $jenis_kelamin, 
			$kehamilan, $kehamilan_minggu, $menyusui,
			$uraian_pertanyaan,
			$jenis_identifikasi_obat, $jenis_stabilitas, $jenis_farmakokinetika,
			$jenis_interaksi_obat, $jenis_dosis, $jenis_farmakodinamika,
			$jenis_harga_obat, $jenis_keracunan, $jenis_ketersediaan_obat,
			$jenis_kontra_indikasi, $jenis_efek_samping, $jenis_cara_pemakaian,
			$jenis_penggunaan_terapeutik, $jenis_lain_lain, $jenis_lain_lain_ket,
			$jawaban, $referensi, $penyampaian_jawaban,
			$apoteker_penjawab, $tanggal_jawab, $waktu_jawab, $metode_jawab,
			$id_pio
		]);
		
		echo "<script type='text/javascript'>alert('Data PIO berhasil diupdate!');window.location='../../media_admin.php?module=pio'</script>";
		
	} catch (PDOException $e) {
			echo "<script type='text/javascript'>alert('Error: " . $e->getMessage() . "');history.go(-1);</script>";
		}
	}
	
	// Hapus PIO
	elseif ($module == 'pio' and $act == 'hapus') {
		$id_pio = $_GET['id'];
		$stmt = $db->prepare("DELETE FROM pio WHERE id_pio = ?");
		$stmt->execute([$id_pio]);
		
		header('location:../../media_admin.php?module=pio');
	}
}
?>
