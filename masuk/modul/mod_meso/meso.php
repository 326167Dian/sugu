<?php
session_start();
include "../../../configurasi/koneksi.php";
if (empty($_SESSION['username']) and empty($_SESSION['passuser'])) {
	echo "<link href=../css/style.css rel=stylesheet type=text/css>";
	echo "<div class='error msg'>Untuk mengakses Modul anda harus login.</div>";
} else {

	$aksi = "modul/mod_meso/aksi_meso.php";
	switch ($_GET['act']) {
		// Tampil MESO
		default:
			$tampil_meso = $db->query("SELECT m.*, p.nm_pelanggan, p.tlp_pelanggan 
				FROM meso m
				LEFT JOIN pelanggan p ON m.id_pelanggan = p.id_pelanggan
				ORDER BY m.id_meso DESC");
?>
			<div class="box box-primary box-solid">
				<div class="box-header with-border">
					<h3 class="box-title">DATA MESO (MONITORING EFEK SAMPING OBAT)</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div>
				</div>
				<div class="box-body table-responsive">
                    <a class='btn btn-primary btn-flat' href='?module=pelanggan'>KEMBALI</a>
					<table id="example1" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>No</th>
								<th>Tanggal Laporan</th>
								<th>Nama Pasien</th>
								<th>Penyakit Utama</th>
								<th>Manifestasi ESO</th>
								<th>Kesudahan</th>
								<th>Aksi</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$no = 1;
							while ($r = $tampil_meso->fetch(PDO::FETCH_ASSOC)) {
								echo "<tr>
									<td>$no</td>
									<td>" . ($r['tanggal_laporan'] ?? '-') . "</td>
									<td>" . ($r['nm_pelanggan'] ?? '-') . "</td>
									<td>" . ($r['penyakit_utama'] ?? '-') . "</td>
									<td>" . ($r['manifestasi_eso'] ?? '-') . "</td>
									<td>" . ($r['kesudahan_eso'] ?? '-') . "</td>
									<td>
										<a href='modul/mod_meso/tampil_meso.php?id=" . $r['id_meso'] . "' target='_blank' title='Lihat' class='btn btn-info btn-xs'>LIHAT</a>
										<a href='?module=meso&act=edit&id=" . $r['id_meso'] . "' title='Edit' class='btn btn-warning btn-xs'>EDIT</a>
										<a href=javascript:confirmdelete('$aksi?module=meso&act=hapus&id=" . $r['id_meso'] . "') title='Hapus' class='btn btn-danger btn-xs'>HAPUS</a>
									</td>
								</tr>";
								$no++;
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
<?php
			break;

		case "input_meso":
			$id_pelanggan = isset($_GET['id_pelanggan']) ? intval($_GET['id_pelanggan']) : 0;
			
			// Ambil data pelanggan
			$data_pelanggan = null;
			if ($id_pelanggan > 0) {
				$stmt = $db->prepare("SELECT * FROM pelanggan WHERE id_pelanggan = ?");
				$stmt->execute([$id_pelanggan]);
				$data_pelanggan = $stmt->fetch(PDO::FETCH_ASSOC);
			}
			
			// Hitung umur
			$umur = '';
			if ($data_pelanggan && !empty($data_pelanggan['tanggal_lahir'])) {
				$tanggal_lahir = new DateTime($data_pelanggan['tanggal_lahir']);
				$today = new DateTime('today');
				$umur = $tanggal_lahir->diff($today)->y;
			}
			
			echo "
			<div class='box box-primary box-solid'>
				<div class='box-header with-border'>
					<h3 class='box-title'>FORMULIR PELAPORAN EFEK SAMPING OBAT (ESO)</h3>
					<div class='box-tools pull-right'>
						<button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
					</div>
				</div>
				<div class='box-body'>
					<form method='POST' action='$aksi?module=meso&act=input' class='form-horizontal'>
						<input type='hidden' name='id_pelanggan' value='$id_pelanggan'>
						
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Kode Sumber Data</label>
							<div class='col-sm-4'>
								<input type='text' name='kode_sumber_data' class='form-control'>
							</div>
						</div>
						
						<h4><u>PENDERITA</u></h4>
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Nama Pasien</label>
							<div class='col-sm-4'>
								<input type='text' name='nama_singkat' class='form-control' value='" . ($data_pelanggan['nm_pelanggan'] ?? '') . "' readonly>
							</div>
							<label class='col-sm-2 control-label'>Umur</label>
							<div class='col-sm-2'>
								<input type='text' name='umur' class='form-control' value='$umur tahun' readonly>
							</div>
						</div>
						
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Jenis Kelamin</label>
							<div class='col-sm-4'>
								<label class='radio-inline'>
									<input type='radio' name='jenis_kelamin' value='L' " . (isset($data_pelanggan['jenis_kelamin']) && $data_pelanggan['jenis_kelamin'] == 'L' ? 'checked' : '') . "> Pria
								</label>
								<label class='radio-inline'>
									<input type='radio' name='jenis_kelamin' value='P' " . (isset($data_pelanggan['jenis_kelamin']) && $data_pelanggan['jenis_kelamin'] == 'P' ? 'checked' : '') . "> Wanita
								</label>
							</div>
							<label class='col-sm-2 control-label'>Status Kehamilan</label>
							<div class='col-sm-4'>
								<select name='status_hamil' class='form-control'>
									<option value=''>-- Pilih --</option>
									<option value='hamil'>Hamil</option>
									<option value='tidak_hamil'>Tidak Hamil</option>
									<option value='tidak_tahu'>Tidak Tahu</option>
								</select>
							</div>
						</div>
						
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Suku</label>
							<div class='col-sm-4'>
								<input type='text' name='suku' class='form-control'>
							</div>
							<label class='col-sm-2 control-label'>Berat Badan (kg)</label>
							<div class='col-sm-2'>
								<input type='text' name='berat_badan' class='form-control'>
							</div>
						</div>
						
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Pekerjaan</label>
							<div class='col-sm-4'>
								<input type='text' name='pekerjaan' class='form-control'>
							</div>
						</div>
						
						<h4><u>PENYAKIT</u></h4>
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Penyakit Utama</label>
							<div class='col-sm-10'>
								<textarea name='penyakit_utama' class='form-control' rows='3' required></textarea>
							</div>
						</div>
						
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Kondisi Lain yang Menyertai</label>
							<div class='col-sm-10'>
								<label class='checkbox-inline'>
									<input type='checkbox' name='gangguan_ginjal' value='1'> Gangguan Ginjal
								</label>
								<label class='checkbox-inline'>
									<input type='checkbox' name='gangguan_hati' value='1'> Gangguan Hati
								</label>
								<label class='checkbox-inline'>
									<input type='checkbox' name='alergi' value='1'> Alergi
								</label>
								<label class='checkbox-inline'>
									<input type='checkbox' name='kondisi_medis_lain' value='1'> Kondisi Medis Lainnya
								</label>
								<input type='text' name='kondisi_medis_lain_ket' class='form-control' placeholder='Sebutkan kondisi medis lainnya' style='margin-top:10px;'>
							</div>
						</div>
						
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Kesudahan Penyakit Utama</label>
							<div class='col-sm-10'>
								<label class='radio-inline'>
									<input type='radio' name='kesudahan_penyakit' value='sembuh'> Sembuh
								</label>
								<label class='radio-inline'>
									<input type='radio' name='kesudahan_penyakit' value='sembuh_gejala_sisa'> Sembuh dengan gejala sisa
								</label>
								<label class='radio-inline'>
									<input type='radio' name='kesudahan_penyakit' value='belum_sembuh'> Belum sembuh
								</label>
								<label class='radio-inline'>
									<input type='radio' name='kesudahan_penyakit' value='meninggal'> Meninggal
								</label>
								<label class='radio-inline'>
									<input type='radio' name='kesudahan_penyakit' value='tidak_tahu'> Tidak Tahu
								</label>
							</div>
						</div>
						
						<h4><u>EFEK SAMPING OBAT</u></h4>
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Bentuk / Manifestasi ESO</label>
							<div class='col-sm-10'>
								<textarea name='manifestasi_eso' class='form-control' rows='3' required></textarea>
							</div>
						</div>
						
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Masalah pada Mutu/Kualitas Produk Obat</label>
							<div class='col-sm-10'>
								<textarea name='masalah_mutu_produk' class='form-control' rows='3'></textarea>
							</div>
						</div>
						
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Tanggal Mula Terjadi</label>
							<div class='col-sm-4'>
								<input type='date' name='tanggal_mula_eso' class='form-control' required>
							</div>
						</div>
						
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Kesudahan ESO</label>
							<div class='col-sm-10'>
								<label class='radio-inline'>
									<input type='radio' name='kesudahan_eso' value='sembuh'> Sembuh
								</label>
								<label class='radio-inline'>
									<input type='radio' name='kesudahan_eso' value='sembuh_gejala_sisa'> Sembuh dengan gejala sisa
								</label>
								<label class='radio-inline'>
									<input type='radio' name='kesudahan_eso' value='belum_sembuh'> Belum sembuh
								</label>
								<label class='radio-inline'>
									<input type='radio' name='kesudahan_eso' value='meninggal'> Meninggal
								</label>
								<label class='radio-inline'>
									<input type='radio' name='kesudahan_eso' value='tidak_tahu'> Tidak Tahu
								</label>
							</div>
						</div>
						
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Riwayat ESO yang Pernah Dialami</label>
							<div class='col-sm-10'>
								<textarea name='riwayat_eso' class='form-control' rows='2'></textarea>
							</div>
						</div>
						
						<h4><u>OBAT YANG DIKONSUMSI</u></h4>
						<div id='obat-container'>
							<div class='obat-item' style='border: 1px solid #ddd; padding: 15px; margin-bottom: 10px;'>
								<div class='form-group'>
									<label class='col-sm-2 control-label'>Nama Obat</label>
									<div class='col-sm-4'>
										<input type='text' name='obat_nama[]' class='form-control' placeholder='Nama Dagang/Generik'>
									</div>
									<label class='col-sm-2 control-label'>Bentuk Sediaan</label>
									<div class='col-sm-4'>
										<input type='text' name='obat_bentuk[]' class='form-control'>
									</div>
								</div>
								<div class='form-group'>
									<label class='col-sm-2 control-label'>No. Batch</label>
									<div class='col-sm-4'>
										<input type='text' name='obat_batch[]' class='form-control'>
									</div>
									<label class='col-sm-2 control-label'>Cara Pemberian</label>
									<div class='col-sm-4'>
										<input type='text' name='obat_cara[]' class='form-control'>
									</div>
								</div>
								<div class='form-group'>
									<label class='col-sm-2 control-label'>Dosis</label>
									<div class='col-sm-4'>
										<input type='text' name='obat_dosis[]' class='form-control'>
									</div>
									<label class='col-sm-2 control-label'>Indikasi Penggunaan</label>
									<div class='col-sm-4'>
										<input type='text' name='obat_indikasi[]' class='form-control'>
									</div>
								</div>
								<div class='form-group'>
									<label class='col-sm-2 control-label'>Tanggal Mula</label>
									<div class='col-sm-4'>
										<input type='date' name='obat_tgl_mula[]' class='form-control'>
									</div>
									<label class='col-sm-2 control-label'>Tanggal Akhir</label>
									<div class='col-sm-4'>
										<input type='date' name='obat_tgl_akhir[]' class='form-control'>
									</div>
								</div>
								<div class='form-group'>
									<div class='col-sm-offset-2 col-sm-10'>
										<label class='checkbox-inline'>
											<input type='checkbox' name='obat_jkn[]' value='1'> Obat JKN
										</label>
										<label class='checkbox-inline'>
											<input type='checkbox' name='obat_dicurigai[]' value='1'> Obat yang Dicurigai
										</label>
									</div>
								</div>
							</div>
						</div>
						<div class='form-group'>
							<div class='col-sm-offset-2 col-sm-10'>
								<button type='button' class='btn btn-sm btn-success' onclick='tambahObat()'>+ Tambah Obat</button>
							</div>
						</div>
						
						<h4><u>KETERANGAN TAMBAHAN</u></h4>
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Keterangan Tambahan</label>
							<div class='col-sm-10'>
								<textarea name='keterangan_tambahan' class='form-control' rows='4' placeholder='Contoh: kecepatan timbulnya ESO, reaksi setelah obat dihentikan, dsb'></textarea>
							</div>
						</div>
						
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Data Laboratorium</label>
							<div class='col-sm-10'>
								<textarea name='data_laboratorium' class='form-control' rows='3'></textarea>
							</div>
						</div>
						
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Tanggal Pemeriksaan Lab</label>
							<div class='col-sm-4'>
								<input type='date' name='tanggal_pemeriksaan_lab' class='form-control'>
							</div>
						</div>
						
						<h4><u>PELAPOR</u></h4>
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Tanggal Laporan</label>
							<div class='col-sm-4'>
								<input type='date' name='tanggal_laporan' class='form-control' value='" . date('Y-m-d') . "' required>
							</div>
							<label class='col-sm-2 control-label'>Nama Pelapor</label>
							<div class='col-sm-4'>
								<input type='text' name='nama_pelapor' class='form-control' value='" . $_SESSION['namalengkap'] . "' required>
							</div>
						</div>
						
						<div class='form-group'>
							<div class='col-sm-offset-2 col-sm-10'>
								<button type='submit' class='btn btn-primary'>SIMPAN</button>
								<button type='button' class='btn btn-default' onclick=\"window.location='?module=pelanggan'\">BATAL</button>
							</div>
						</div>
					</form>
				</div>
			</div>
			
			<script>
			function tambahObat() {
				var container = document.getElementById('obat-container');
				var newObat = container.querySelector('.obat-item').cloneNode(true);
				// Reset nilai input
				var inputs = newObat.querySelectorAll('input');
				inputs.forEach(function(input) {
					if (input.type === 'checkbox') {
						input.checked = false;
					} else {
						input.value = '';
					}
				});
				container.appendChild(newObat);
			}
			</script>
			";

			break;

		case "edit":
			$id_meso = isset($_GET['id']) ? intval($_GET['id']) : 0;
			
			// Ambil data meso yang akan diedit
			$stmt = $db->prepare("SELECT m.*, p.nm_pelanggan, p.jenis_kelamin as jk_pelanggan 
				FROM meso m
				LEFT JOIN pelanggan p ON m.id_pelanggan = p.id_pelanggan
				WHERE m.id_meso = ?");
			$stmt->execute([$id_meso]);
			$data_meso = $stmt->fetch(PDO::FETCH_ASSOC);
			
			if (!$data_meso) {
				echo "<script>alert('Data tidak ditemukan!'); window.location='?module=meso';</script>";
				exit;
			}
			
			// Decode JSON data_obat
			$data_obat = json_decode($data_meso['data_obat'], true) ?? [];
			
			echo "
			<div class='box box-warning box-solid'>
				<div class='box-header with-border'>
					<h3 class='box-title'>EDIT FORMULIR PELAPORAN EFEK SAMPING OBAT (ESO)</h3>
					<div class='box-tools pull-right'>
						<button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
					</div>
				</div>
				<div class='box-body'>
					<form method='POST' action='$aksi?module=meso&act=update' class='form-horizontal'>
						<input type='hidden' name='id' value='$id_meso'>
						<input type='hidden' name='id_pelanggan' value='" . $data_meso['id_pelanggan'] . "'>
						
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Kode Sumber Data</label>
							<div class='col-sm-4'>
								<input type='text' name='kode_sumber_data' class='form-control' value='" . htmlspecialchars($data_meso['kode_sumber_data']) . "'>
							</div>
						</div>
						
						<h4><u>PENDERITA</u></h4>
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Nama Pasien</label>
							<div class='col-sm-4'>
								<input type='text' name='nama_singkat' class='form-control' value='" . htmlspecialchars($data_meso['nama_singkat']) . "' readonly>
							</div>
							<label class='col-sm-2 control-label'>Umur</label>
							<div class='col-sm-2'>
								<input type='text' name='umur' class='form-control' value='" . htmlspecialchars($data_meso['umur']) . "' readonly>
							</div>
						</div>
						
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Jenis Kelamin</label>
							<div class='col-sm-4'>
								<label class='radio-inline'>
									<input type='radio' name='jenis_kelamin' value='L' " . ($data_meso['jenis_kelamin'] == 'L' ? 'checked' : '') . "> Pria
								</label>
								<label class='radio-inline'>
									<input type='radio' name='jenis_kelamin' value='P' " . ($data_meso['jenis_kelamin'] == 'P' ? 'checked' : '') . "> Wanita
								</label>
							</div>
							<label class='col-sm-2 control-label'>Status Kehamilan</label>
							<div class='col-sm-4'>
								<select name='status_hamil' class='form-control'>
									<option value=''>-- Pilih --</option>
									<option value='hamil' " . ($data_meso['status_hamil'] == 'hamil' ? 'selected' : '') . ">Hamil</option>
									<option value='tidak_hamil' " . ($data_meso['status_hamil'] == 'tidak_hamil' ? 'selected' : '') . ">Tidak Hamil</option>
									<option value='tidak_tahu' " . ($data_meso['status_hamil'] == 'tidak_tahu' ? 'selected' : '') . ">Tidak Tahu</option>
								</select>
							</div>
						</div>
						
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Suku</label>
							<div class='col-sm-4'>
								<input type='text' name='suku' class='form-control' value='" . htmlspecialchars($data_meso['suku']) . "'>
							</div>
							<label class='col-sm-2 control-label'>Berat Badan (kg)</label>
							<div class='col-sm-2'>
								<input type='text' name='berat_badan' class='form-control' value='" . htmlspecialchars($data_meso['berat_badan']) . "'>
							</div>
						</div>
						
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Pekerjaan</label>
							<div class='col-sm-4'>
								<input type='text' name='pekerjaan' class='form-control' value='" . htmlspecialchars($data_meso['pekerjaan']) . "'>
							</div>
						</div>
						
						<h4><u>PENYAKIT</u></h4>
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Penyakit Utama</label>
							<div class='col-sm-10'>
								<textarea name='penyakit_utama' class='form-control' rows='3' required>" . htmlspecialchars($data_meso['penyakit_utama']) . "</textarea>
							</div>
						</div>
						
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Kondisi Lain yang Menyertai</label>
							<div class='col-sm-10'>
								<label class='checkbox-inline'>
									<input type='checkbox' name='gangguan_ginjal' value='1' " . ($data_meso['gangguan_ginjal'] ? 'checked' : '') . "> Gangguan Ginjal
								</label>
								<label class='checkbox-inline'>
									<input type='checkbox' name='gangguan_hati' value='1' " . ($data_meso['gangguan_hati'] ? 'checked' : '') . "> Gangguan Hati
								</label>
								<label class='checkbox-inline'>
									<input type='checkbox' name='alergi' value='1' " . ($data_meso['alergi'] ? 'checked' : '') . "> Alergi
								</label>
								<label class='checkbox-inline'>
									<input type='checkbox' name='kondisi_medis_lain' value='1' " . ($data_meso['kondisi_medis_lain'] ? 'checked' : '') . "> Kondisi Medis Lainnya
								</label>
								<input type='text' name='kondisi_medis_lain_ket' class='form-control' placeholder='Sebutkan kondisi medis lainnya' value='" . htmlspecialchars($data_meso['kondisi_medis_lain_ket']) . "' style='margin-top:10px;'>
							</div>
						</div>
						
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Kesudahan Penyakit Utama</label>
							<div class='col-sm-10'>
								<label class='radio-inline'>
									<input type='radio' name='kesudahan_penyakit' value='sembuh' " . ($data_meso['kesudahan_penyakit'] == 'sembuh' ? 'checked' : '') . "> Sembuh
								</label>
								<label class='radio-inline'>
									<input type='radio' name='kesudahan_penyakit' value='sembuh_gejala_sisa' " . ($data_meso['kesudahan_penyakit'] == 'sembuh_gejala_sisa' ? 'checked' : '') . "> Sembuh dengan gejala sisa
								</label>
								<label class='radio-inline'>
									<input type='radio' name='kesudahan_penyakit' value='belum_sembuh' " . ($data_meso['kesudahan_penyakit'] == 'belum_sembuh' ? 'checked' : '') . "> Belum sembuh
								</label>
								<label class='radio-inline'>
									<input type='radio' name='kesudahan_penyakit' value='meninggal' " . ($data_meso['kesudahan_penyakit'] == 'meninggal' ? 'checked' : '') . "> Meninggal
								</label>
								<label class='radio-inline'>
									<input type='radio' name='kesudahan_penyakit' value='tidak_tahu' " . ($data_meso['kesudahan_penyakit'] == 'tidak_tahu' ? 'checked' : '') . "> Tidak Tahu
								</label>
							</div>
						</div>
						
						<h4><u>EFEK SAMPING OBAT</u></h4>
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Bentuk / Manifestasi ESO</label>
							<div class='col-sm-10'>
								<textarea name='manifestasi_eso' class='form-control' rows='3' required>" . htmlspecialchars($data_meso['manifestasi_eso']) . "</textarea>
							</div>
						</div>
						
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Masalah pada Mutu/Kualitas Produk Obat</label>
							<div class='col-sm-10'>
								<textarea name='masalah_mutu_produk' class='form-control' rows='3'>" . htmlspecialchars($data_meso['masalah_mutu_produk']) . "</textarea>
							</div>
						</div>
						
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Tanggal Mula Terjadi</label>
							<div class='col-sm-4'>
								<input type='date' name='tanggal_mula_eso' class='form-control' value='" . $data_meso['tanggal_mula_eso'] . "' required>
							</div>
						</div>
						
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Kesudahan ESO</label>
							<div class='col-sm-10'>
								<label class='radio-inline'>
									<input type='radio' name='kesudahan_eso' value='sembuh' " . ($data_meso['kesudahan_eso'] == 'sembuh' ? 'checked' : '') . "> Sembuh
								</label>
								<label class='radio-inline'>
									<input type='radio' name='kesudahan_eso' value='sembuh_gejala_sisa' " . ($data_meso['kesudahan_eso'] == 'sembuh_gejala_sisa' ? 'checked' : '') . "> Sembuh dengan gejala sisa
								</label>
								<label class='radio-inline'>
									<input type='radio' name='kesudahan_eso' value='belum_sembuh' " . ($data_meso['kesudahan_eso'] == 'belum_sembuh' ? 'checked' : '') . "> Belum sembuh
								</label>
								<label class='radio-inline'>
									<input type='radio' name='kesudahan_eso' value='meninggal' " . ($data_meso['kesudahan_eso'] == 'meninggal' ? 'checked' : '') . "> Meninggal
								</label>
								<label class='radio-inline'>
									<input type='radio' name='kesudahan_eso' value='tidak_tahu' " . ($data_meso['kesudahan_eso'] == 'tidak_tahu' ? 'checked' : '') . "> Tidak Tahu
								</label>
							</div>
						</div>
						
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Riwayat ESO yang Pernah Dialami</label>
							<div class='col-sm-10'>
								<textarea name='riwayat_eso' class='form-control' rows='2'>" . htmlspecialchars($data_meso['riwayat_eso']) . "</textarea>
							</div>
						</div>
						
						<h4><u>OBAT YANG DIKONSUMSI</u></h4>
						<div id='obat-container'>";
			
			// Loop untuk menampilkan data obat yang sudah ada
			if (!empty($data_obat)) {
				foreach ($data_obat as $idx => $obat) {
					echo "
					<div class='obat-item' style='border: 1px solid #ddd; padding: 15px; margin-bottom: 10px;'>
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Nama Obat</label>
							<div class='col-sm-4'>
								<input type='text' name='obat_nama[]' class='form-control' placeholder='Nama Dagang/Generik' value='" . htmlspecialchars($obat['nama']) . "'>
							</div>
							<label class='col-sm-2 control-label'>Bentuk Sediaan</label>
							<div class='col-sm-4'>
								<input type='text' name='obat_bentuk[]' class='form-control' value='" . htmlspecialchars($obat['bentuk']) . "'>
							</div>
						</div>
						<div class='form-group'>
							<label class='col-sm-2 control-label'>No. Batch</label>
							<div class='col-sm-4'>
								<input type='text' name='obat_batch[]' class='form-control' value='" . htmlspecialchars($obat['batch']) . "'>
							</div>
							<label class='col-sm-2 control-label'>Cara Pemberian</label>
							<div class='col-sm-4'>
								<input type='text' name='obat_cara[]' class='form-control' value='" . htmlspecialchars($obat['cara']) . "'>
							</div>
						</div>
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Dosis</label>
							<div class='col-sm-4'>
								<input type='text' name='obat_dosis[]' class='form-control' value='" . htmlspecialchars($obat['dosis']) . "'>
							</div>
							<label class='col-sm-2 control-label'>Indikasi Penggunaan</label>
							<div class='col-sm-4'>
								<input type='text' name='obat_indikasi[]' class='form-control' value='" . htmlspecialchars($obat['indikasi']) . "'>
							</div>
						</div>
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Tanggal Mula</label>
							<div class='col-sm-4'>
								<input type='date' name='obat_tgl_mula[]' class='form-control' value='" . $obat['tgl_mula'] . "'>
							</div>
							<label class='col-sm-2 control-label'>Tanggal Akhir</label>
							<div class='col-sm-4'>
								<input type='date' name='obat_tgl_akhir[]' class='form-control' value='" . $obat['tgl_akhir'] . "'>
							</div>
						</div>
						<div class='form-group'>
							<div class='col-sm-offset-2 col-sm-10'>
								<label class='checkbox-inline'>
									<input type='checkbox' name='obat_jkn[]' value='1' " . ($obat['jkn'] ? 'checked' : '') . "> Obat JKN
								</label>
								<label class='checkbox-inline'>
									<input type='checkbox' name='obat_dicurigai[]' value='1' " . ($obat['dicurigai'] ? 'checked' : '') . "> Obat yang Dicurigai
								</label>
							</div>
						</div>
					</div>";
				}
			} else {
				// Jika tidak ada data obat, tampilkan 1 form kosong
				echo "
				<div class='obat-item' style='border: 1px solid #ddd; padding: 15px; margin-bottom: 10px;'>
					<div class='form-group'>
						<label class='col-sm-2 control-label'>Nama Obat</label>
						<div class='col-sm-4'>
							<input type='text' name='obat_nama[]' class='form-control' placeholder='Nama Dagang/Generik'>
						</div>
						<label class='col-sm-2 control-label'>Bentuk Sediaan</label>
						<div class='col-sm-4'>
							<input type='text' name='obat_bentuk[]' class='form-control'>
						</div>
					</div>
					<div class='form-group'>
						<label class='col-sm-2 control-label'>No. Batch</label>
						<div class='col-sm-4'>
							<input type='text' name='obat_batch[]' class='form-control'>
						</div>
						<label class='col-sm-2 control-label'>Cara Pemberian</label>
						<div class='col-sm-4'>
							<input type='text' name='obat_cara[]' class='form-control'>
						</div>
					</div>
					<div class='form-group'>
						<label class='col-sm-2 control-label'>Dosis</label>
						<div class='col-sm-4'>
							<input type='text' name='obat_dosis[]' class='form-control'>
						</div>
						<label class='col-sm-2 control-label'>Indikasi Penggunaan</label>
						<div class='col-sm-4'>
							<input type='text' name='obat_indikasi[]' class='form-control'>
						</div>
					</div>
					<div class='form-group'>
						<label class='col-sm-2 control-label'>Tanggal Mula</label>
						<div class='col-sm-4'>
							<input type='date' name='obat_tgl_mula[]' class='form-control'>
						</div>
						<label class='col-sm-2 control-label'>Tanggal Akhir</label>
						<div class='col-sm-4'>
							<input type='date' name='obat_tgl_akhir[]' class='form-control'>
						</div>
					</div>
					<div class='form-group'>
						<div class='col-sm-offset-2 col-sm-10'>
							<label class='checkbox-inline'>
								<input type='checkbox' name='obat_jkn[]' value='1'> Obat JKN
							</label>
							<label class='checkbox-inline'>
								<input type='checkbox' name='obat_dicurigai[]' value='1'> Obat yang Dicurigai
							</label>
						</div>
					</div>
				</div>";
			}
			
			echo "
					</div>
					<div class='form-group'>
						<div class='col-sm-offset-2 col-sm-10'>
							<button type='button' class='btn btn-sm btn-success' onclick='tambahObat()'>+ Tambah Obat</button>
						</div>
					</div>
					
					<h4><u>KETERANGAN TAMBAHAN</u></h4>
					<div class='form-group'>
						<label class='col-sm-2 control-label'>Keterangan Tambahan</label>
						<div class='col-sm-10'>
							<textarea name='keterangan_tambahan' class='form-control' rows='4' placeholder='Contoh: kecepatan timbulnya ESO, reaksi setelah obat dihentikan, dsb'>" . htmlspecialchars($data_meso['keterangan_tambahan']) . "</textarea>
						</div>
					</div>
					
					<div class='form-group'>
						<label class='col-sm-2 control-label'>Data Laboratorium</label>
						<div class='col-sm-10'>
							<textarea name='data_laboratorium' class='form-control' rows='3'>" . htmlspecialchars($data_meso['data_laboratorium']) . "</textarea>
						</div>
					</div>
					
					<div class='form-group'>
						<label class='col-sm-2 control-label'>Tanggal Pemeriksaan Lab</label>
						<div class='col-sm-4'>
							<input type='date' name='tanggal_pemeriksaan_lab' class='form-control' value='" . $data_meso['tanggal_pemeriksaan_lab'] . "'>
						</div>
					</div>
					
					<h4><u>PELAPOR</u></h4>
					<div class='form-group'>
						<label class='col-sm-2 control-label'>Tanggal Laporan</label>
						<div class='col-sm-4'>
							<input type='date' name='tanggal_laporan' class='form-control' value='" . $data_meso['tanggal_laporan'] . "' required>
						</div>
						<label class='col-sm-2 control-label'>Nama Pelapor</label>
						<div class='col-sm-4'>
							<input type='text' name='nama_pelapor' class='form-control' value='" . htmlspecialchars($data_meso['nama_pelapor']) . "' required>
						</div>
					</div>
					
					<div class='form-group'>
						<div class='col-sm-offset-2 col-sm-10'>
							<button type='submit' class='btn btn-warning'>UPDATE</button>
							<button type='button' class='btn btn-default' onclick=\"window.location='?module=meso'\">BATAL</button>
						</div>
					</div>
				</form>
			</div>
		</div>
		
		<script>
		function tambahObat() {
			var container = document.getElementById('obat-container');
			var newObat = container.querySelector('.obat-item').cloneNode(true);
			// Reset nilai input
			var inputs = newObat.querySelectorAll('input');
			inputs.forEach(function(input) {
				if (input.type === 'checkbox') {
					input.checked = false;
				} else {
					input.value = '';
				}
			});
			var textareas = newObat.querySelectorAll('textarea');
			textareas.forEach(function(textarea) {
				textarea.value = '';
			});
			container.appendChild(newObat);
		}
		</script>
		";
			break;
	}
}
?>
