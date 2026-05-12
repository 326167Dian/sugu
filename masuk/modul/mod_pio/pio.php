<?php
session_start();
include "../../../configurasi/koneksi.php";
if (empty($_SESSION['username']) and empty($_SESSION['passuser'])) {
	echo "<link href=../css/style.css rel=stylesheet type=text/css>";
	echo "<div class='error msg'>Untuk mengakses Modul anda harus login.</div>";
} else {

	$aksi = "modul/mod_pio/aksi_pio.php";
	switch ($_GET['act']) {
		// Tampil PIO
		default:
			$tampil_pio = $db->query("SELECT pr.*, p.nm_pelanggan, p.tlp_pelanggan 
				FROM pio pr
				LEFT JOIN pelanggan p ON pr.id_pelanggan = p.id_pelanggan
				ORDER BY pr.id_pio DESC");
?>
			<div class="box box-primary box-solid">
				<div class="box-header with-border">
					<h3 class="box-title">DATA PIO (PELAYANAN INFORMASI OBAT)</h3>
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
								<th>No. PIO</th>
								<th>Tanggal</th>
								<th>Nama Pasien</th>
								<th>Nama Penanya</th>
								<th>Pertanyaan</th>
								<th>Status</th>
								<th>Aksi</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$no = 1;
							while ($r = $tampil_pio->fetch(PDO::FETCH_ASSOC)) {
								$pertanyaan_short = strlen($r['uraian_pertanyaan']) > 50 ? substr($r['uraian_pertanyaan'], 0, 50) . '...' : $r['uraian_pertanyaan'];
								echo "<tr>
									<td>$no</td>
									<td>" . ($r['no_pio'] ?? '-') . "</td>
									<td>" . ($r['tanggal'] ?? '-') . "</td>
									<td>" . ($r['nm_pelanggan'] ?? '-') . "</td>
									<td>" . ($r['nama_penanya'] ?? '-') . "</td>
									<td>" . htmlspecialchars($pertanyaan_short ?? '-') . "</td>
									<td>" . ($r['jawaban'] ? '<span class="label label-success">Terjawab</span>' : '<span class="label label-warning">Belum</span>') . "</td>
									<td>
										<a href='modul/mod_pio/tampil_pio.php?id=" . $r['id_pio'] . "' target='_blank' title='Lihat' class='btn btn-info btn-xs'>LIHAT</a>
										<a href='?module=pio&act=edit&id=" . $r['id_pio'] . "' title='Edit' class='btn btn-warning btn-xs'>EDIT</a>
										<a href=javascript:confirmdelete('$aksi?module=pio&act=hapus&id=" . $r['id_pio'] . "') title='Hapus' class='btn btn-danger btn-xs'>HAPUS</a>
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

		case "input_pio":
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
			
			// Generate auto number PIO
			$stmt_no = $db->query("SELECT MAX(CAST(SUBSTRING(no_pio, 5) AS UNSIGNED)) as max_no FROM pio WHERE no_pio LIKE 'PIO%'");
			$data_no = $stmt_no->fetch(PDO::FETCH_ASSOC);
			$next_no = ($data_no['max_no'] ?? 0) + 1;
			$no_pio = 'PIO' . str_pad($next_no, 4, '0', STR_PAD_LEFT);
			
			echo "
			<div class='box box-primary box-solid'>
				<div class='box-header with-border'>
					<h3 class='box-title'>FORMULIR DOKUMENTASI PIO (PELAYANAN INFORMASI OBAT)</h3>
					<div class='box-tools pull-right'>
						<button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
					</div>
				</div>
				<div class='box-body'>
					<form method='POST' action='$aksi?module=pio&act=input' class='form-horizontal'>
						<input type='hidden' name='id_pelanggan' value='$id_pelanggan'>
						
						<h4><u>INFORMASI DASAR</u></h4>
						<div class='form-group'>
							<label class='col-sm-2 control-label'>No. PIO</label>
							<div class='col-sm-2'>
								<input type='text' name='no_pio' class='form-control' value='$no_pio' readonly>
							</div>
							<label class='col-sm-2 control-label'>Tanggal</label>
							<div class='col-sm-2'>
								<input type='date' name='tanggal' class='form-control' value='" . date('Y-m-d') . "' required>
							</div>
							<label class='col-sm-1 control-label'>Waktu</label>
							<div class='col-sm-2'>
								<input type='time' name='waktu' class='form-control' value='" . date('H:i') . "' required>
							</div>
						</div>
						
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Metode</label>
							<div class='col-sm-10'>
								<label class='radio-inline'>
									<input type='radio' name='metode' value='Lisan' checked> Lisan
								</label>
								<label class='radio-inline'>
									<input type='radio' name='metode' value='Tertulis'> Tertulis
								</label>
								<label class='radio-inline'>
									<input type='radio' name='metode' value='Telepon'> Telepon
								</label>
							</div>
						</div>
						
						<h4><u>IDENTITAS PENANYA</u></h4>
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Nama Penanya</label>
							<div class='col-sm-4'>
								<input type='text' name='nama_penanya' class='form-control' required>
							</div>
							<label class='col-sm-2 control-label'>No. Telp</label>
							<div class='col-sm-4'>
								<input type='text' name='no_telp_penanya' class='form-control'>
							</div>
						</div>
						
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Status</label>
							<div class='col-sm-10'>
								<label class='radio-inline'>
									<input type='radio' name='status_penanya' value='Pasien' checked> Pasien
								</label>
								<label class='radio-inline'>
									<input type='radio' name='status_penanya' value='Keluarga Pasien'> Keluarga Pasien
								</label>
								<label class='radio-inline'>
									<input type='radio' name='status_penanya' value='Petugas Kesehatan'> Petugas Kesehatan
								</label>
								<input type='text' name='status_penanya_ket' class='form-control' placeholder='Instansi/Jabatan (untuk Petugas Kesehatan)' style='margin-top:10px; width:50%;'>
							</div>
						</div>
						
						<h4><u>DATA PASIEN</u></h4>
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Nama Pasien</label>
							<div class='col-sm-4'>
								<input type='text' class='form-control' value='" . ($data_pelanggan['nm_pelanggan'] ?? '') . "' readonly>
							</div>
						</div>
						
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Umur</label>
							<div class='col-sm-2'>
								<input type='number' name='umur_pasien' class='form-control' value='$umur' placeholder='tahun'>
							</div>
							<label class='col-sm-2 control-label'>Tinggi (cm)</label>
							<div class='col-sm-2'>
								<input type='number' name='tinggi_pasien' class='form-control'>
							</div>
							<label class='col-sm-1 control-label'>Berat (kg)</label>
							<div class='col-sm-2'>
								<input type='number' name='berat_pasien' class='form-control'>
							</div>
						</div>
						
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Jenis Kelamin</label>
							<div class='col-sm-4'>
								<label class='radio-inline'>
									<input type='radio' name='jenis_kelamin' value='L' " . (isset($data_pelanggan['jenis_kelamin']) && $data_pelanggan['jenis_kelamin'] == 'L' ? 'checked' : '') . "> Laki-laki
								</label>
								<label class='radio-inline'>
									<input type='radio' name='jenis_kelamin' value='P' " . (isset($data_pelanggan['jenis_kelamin']) && $data_pelanggan['jenis_kelamin'] == 'P' ? 'checked' : '') . "> Perempuan
								</label>
							</div>
						</div>
						
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Kehamilan</label>
							<div class='col-sm-4'>
								<label class='checkbox-inline'>
									<input type='checkbox' name='kehamilan' value='1' onclick='toggleKehamilanMinggu(this)'> Ya
								</label>
								<input type='number' id='kehamilan_minggu' name='kehamilan_minggu' class='form-control' placeholder='Minggu' style='width:80px; display:inline-block; margin-left:10px;' disabled>
							</div>
							<label class='col-sm-2 control-label'>Menyusui</label>
							<div class='col-sm-4'>
								<label class='checkbox-inline'>
									<input type='checkbox' name='menyusui' value='1'> Ya
								</label>
							</div>
						</div>
						
						<h4><u>PERTANYAAN</u></h4>
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Uraian Pertanyaan</label>
							<div class='col-sm-10'>
								<textarea name='uraian_pertanyaan' class='form-control' rows='4' required></textarea>
							</div>
						</div>
						
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Jenis Pertanyaan</label>
							<div class='col-sm-10'>
								<div class='row'>
									<div class='col-sm-4'>
										<label class='checkbox-inline' style='display:block;'>
											<input type='checkbox' name='jenis_pertanyaan[]' value='identifikasi_obat'> Identifikasi Obat
										</label>
										<label class='checkbox-inline' style='display:block;'>
											<input type='checkbox' name='jenis_pertanyaan[]' value='interaksi_obat'> Interaksi Obat
										</label>
										<label class='checkbox-inline' style='display:block;'>
											<input type='checkbox' name='jenis_pertanyaan[]' value='harga_obat'> Harga Obat
										</label>
										<label class='checkbox-inline' style='display:block;'>
											<input type='checkbox' name='jenis_pertanyaan[]' value='kontra_indikasi'> Kontra Indikasi
										</label>
										<label class='checkbox-inline' style='display:block;'>
											<input type='checkbox' name='jenis_pertanyaan[]' value='cara_pemakaian'> Cara Pemakaian
										</label>
									</div>
									<div class='col-sm-4'>
										<label class='checkbox-inline' style='display:block;'>
											<input type='checkbox' name='jenis_pertanyaan[]' value='stabilitas'> Stabilitas
										</label>
										<label class='checkbox-inline' style='display:block;'>
											<input type='checkbox' name='jenis_pertanyaan[]' value='dosis'> Dosis
										</label>
										<label class='checkbox-inline' style='display:block;'>
											<input type='checkbox' name='jenis_pertanyaan[]' value='keracunan'> Keracunan
										</label>
										<label class='checkbox-inline' style='display:block;'>
											<input type='checkbox' name='jenis_pertanyaan[]' value='efek_samping'> Efek Samping Obat
										</label>
										<label class='checkbox-inline' style='display:block;'>
											<input type='checkbox' name='jenis_pertanyaan[]' value='penggunaan_terapeutik'> Penggunaan Terapeutik
										</label>
									</div>
									<div class='col-sm-4'>
										<label class='checkbox-inline' style='display:block;'>
											<input type='checkbox' name='jenis_pertanyaan[]' value='farmakokinetika'> Farmakokinetika
										</label>
										<label class='checkbox-inline' style='display:block;'>
											<input type='checkbox' name='jenis_pertanyaan[]' value='farmakodinamika'> Farmakodinamika
										</label>
										<label class='checkbox-inline' style='display:block;'>
											<input type='checkbox' name='jenis_pertanyaan[]' value='ketersediaan_obat'> Ketersediaan Obat
										</label>
										<label class='checkbox-inline' style='display:block;'>
											<input type='checkbox' name='jenis_pertanyaan[]' value='lain_lain' onclick='toggleLainLain(this)'> Lain-lain
										</label>
										<input type='text' id='jenis_pertanyaan_lain_lain_ket' name='jenis_pertanyaan_lain_lain_ket' class='form-control' placeholder='Sebutkan' style='margin-top:5px;' disabled>
									</div>
								</div>
							</div>
						</div>
						
						<h4><u>JAWABAN & REFERENSI</u></h4>
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Jawaban</label>
							<div class='col-sm-10'>
								<textarea name='jawaban' class='form-control' rows='5'></textarea>
							</div>
						</div>
						
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Referensi</label>
							<div class='col-sm-10'>
								<textarea name='referensi' class='form-control' rows='3'></textarea>
							</div>
						</div>
						
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Penyampaian Jawaban</label>
							<div class='col-sm-10'>
								<label class='radio-inline'>
									<input type='radio' name='penyampaian_jawaban' value='Segera'> Segera
								</label>
								<label class='radio-inline'>
									<input type='radio' name='penyampaian_jawaban' value='Dalam 24 jam'> Dalam 24 jam
								</label>
								<label class='radio-inline'>
									<input type='radio' name='penyampaian_jawaban' value='Lebih dari 24 jam'> Lebih dari 24 jam
								</label>
							</div>
						</div>
						
						<h4><u>APOTEKER PENJAWAB</u></h4>
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Nama Apoteker</label>
							<div class='col-sm-4'>
								<input type='text' name='apoteker_penjawab' class='form-control' value='" . $_SESSION['namalengkap'] . "'>
							</div>
						</div>
						
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Tanggal Jawab</label>
							<div class='col-sm-2'>
								<input type='date' name='tanggal_jawab' class='form-control' value='" . date('Y-m-d') . "'>
							</div>
							<label class='col-sm-2 control-label'>Waktu Jawab</label>
							<div class='col-sm-2'>
								<input type='time' name='waktu_jawab' class='form-control' value='" . date('H:i') . "'>
							</div>
						</div>
						
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Metode Jawaban</label>
							<div class='col-sm-10'>
								<label class='radio-inline'>
									<input type='radio' name='metode_jawab' value='Lisan'> Lisan
								</label>
								<label class='radio-inline'>
									<input type='radio' name='metode_jawab' value='Tertulis'> Tertulis
								</label>
								<label class='radio-inline'>
									<input type='radio' name='metode_jawab' value='Telepon'> Telepon
								</label>
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
			function toggleKehamilanMinggu(checkbox) {
				document.getElementById('kehamilan_minggu').disabled = !checkbox.checked;
			}
			
			function toggleLainLain(checkbox) {
				document.getElementById('jenis_pertanyaan_lain_lain_ket').disabled = !checkbox.checked;
			}
			</script>
			";

			break;

		case "edit":
			$id_pio = isset($_GET['id']) ? intval($_GET['id']) : 0;
			
			// Ambil data pio yang akan diedit
			$stmt = $db->prepare("SELECT pr.*, p.nm_pelanggan 
				FROM pio pr
				LEFT JOIN pelanggan p ON pr.id_pelanggan = p.id_pelanggan
				WHERE pr.id_pio = ?");
			$stmt->execute([$id_pio]);
			$data_pio = $stmt->fetch(PDO::FETCH_ASSOC);
			
			if (!$data_pio) {
				echo "<script>alert('Data tidak ditemukan!'); window.location='?module=pio';</script>";
				exit;
			}
			
			echo "
			<div class='box box-warning box-solid'>
				<div class='box-header with-border'>
					<h3 class='box-title'>EDIT FORMULIR DOKUMENTASI PIO</h3>
					<div class='box-tools pull-right'>
						<button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
					</div>
				</div>
				<div class='box-body'>
					<form method='POST' action='$aksi?module=pio&act=update' class='form-horizontal'>
						<input type='hidden' name='id' value='$id_pio'>
						<input type='hidden' name='id_pelanggan' value='" . $data_pio['id_pelanggan'] . "'>
						
						<h4><u>INFORMASI DASAR</u></h4>
						<div class='form-group'>
							<label class='col-sm-2 control-label'>No. PIO</label>
							<div class='col-sm-2'>
								<input type='text' name='no_pio' class='form-control' value='" . htmlspecialchars($data_pio['no_pio']) . "' readonly>
							</div>
							<label class='col-sm-2 control-label'>Tanggal</label>
							<div class='col-sm-2'>
								<input type='date' name='tanggal' class='form-control' value='" . $data_pio['tanggal'] . "' required>
							</div>
							<label class='col-sm-1 control-label'>Waktu</label>
							<div class='col-sm-2'>
								<input type='time' name='waktu' class='form-control' value='" . $data_pio['waktu'] . "' required>
							</div>
						</div>
						
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Metode</label>
							<div class='col-sm-10'>
								<label class='radio-inline'>
									<input type='radio' name='metode' value='Lisan' " . ($data_pio['metode'] == 'Lisan' ? 'checked' : '') . "> Lisan
								</label>
								<label class='radio-inline'>
									<input type='radio' name='metode' value='Tertulis' " . ($data_pio['metode'] == 'Tertulis' ? 'checked' : '') . "> Tertulis
								</label>
								<label class='radio-inline'>
									<input type='radio' name='metode' value='Telepon' " . ($data_pio['metode'] == 'Telepon' ? 'checked' : '') . "> Telepon
								</label>
							</div>
						</div>
						
						<h4><u>IDENTITAS PENANYA</u></h4>
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Nama Penanya</label>
							<div class='col-sm-4'>
								<input type='text' name='nama_penanya' class='form-control' value='" . htmlspecialchars($data_pio['nama_penanya']) . "' required>
							</div>
							<label class='col-sm-2 control-label'>No. Telp</label>
							<div class='col-sm-4'>
								<input type='text' name='no_telp_penanya' class='form-control' value='" . htmlspecialchars($data_pio['no_telp_penanya']) . "'>
							</div>
						</div>
						
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Status</label>
							<div class='col-sm-10'>
								<label class='radio-inline'>
									<input type='radio' name='status_penanya' value='Pasien' " . ($data_pio['status_penanya'] == 'Pasien' ? 'checked' : '') . "> Pasien
								</label>
								<label class='radio-inline'>
									<input type='radio' name='status_penanya' value='Keluarga Pasien' " . ($data_pio['status_penanya'] == 'Keluarga Pasien' ? 'checked' : '') . "> Keluarga Pasien
								</label>
								<label class='radio-inline'>
									<input type='radio' name='status_penanya' value='Petugas Kesehatan' " . ($data_pio['status_penanya'] == 'Petugas Kesehatan' ? 'checked' : '') . "> Petugas Kesehatan
								</label>
								<input type='text' name='status_penanya_ket' class='form-control' placeholder='Instansi/Jabatan (untuk Petugas Kesehatan)' value='" . htmlspecialchars($data_pio['status_penanya_ket']) . "' style='margin-top:10px; width:50%;'>
							</div>
						</div>
						
						<h4><u>DATA PASIEN</u></h4>
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Nama Pasien</label>
							<div class='col-sm-4'>
								<input type='text' class='form-control' value='" . htmlspecialchars($data_pio['nm_pelanggan']) . "' readonly>
							</div>
						</div>
						
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Umur</label>
							<div class='col-sm-2'>
								<input type='number' name='umur_pasien' class='form-control' value='" . $data_pio['umur_pasien'] . "' placeholder='tahun'>
							</div>
							<label class='col-sm-2 control-label'>Tinggi (cm)</label>
							<div class='col-sm-2'>
								<input type='number' name='tinggi_pasien' class='form-control' value='" . $data_pio['tinggi_pasien'] . "'>
							</div>
							<label class='col-sm-1 control-label'>Berat (kg)</label>
							<div class='col-sm-2'>
								<input type='number' name='berat_pasien' class='form-control' value='" . $data_pio['berat_pasien'] . "'>
							</div>
						</div>
						
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Jenis Kelamin</label>
							<div class='col-sm-4'>
								<label class='radio-inline'>
									<input type='radio' name='jenis_kelamin' value='L' " . ($data_pio['jenis_kelamin'] == 'L' ? 'checked' : '') . "> Laki-laki
								</label>
								<label class='radio-inline'>
									<input type='radio' name='jenis_kelamin' value='P' " . ($data_pio['jenis_kelamin'] == 'P' ? 'checked' : '') . "> Perempuan
								</label>
							</div>
						</div>
						
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Kehamilan</label>
							<div class='col-sm-4'>
								<label class='checkbox-inline'>
									<input type='checkbox' name='kehamilan' value='1' onclick='toggleKehamilanMinggu(this)' " . ($data_pio['kehamilan'] ? 'checked' : '') . "> Ya
								</label>
								<input type='number' id='kehamilan_minggu' name='kehamilan_minggu' class='form-control' placeholder='Minggu' value='" . $data_pio['kehamilan_minggu'] . "' style='width:80px; display:inline-block; margin-left:10px;' " . (!$data_pio['kehamilan'] ? 'disabled' : '') . ">
							</div>
							<label class='col-sm-2 control-label'>Menyusui</label>
							<div class='col-sm-4'>
								<label class='checkbox-inline'>
									<input type='checkbox' name='menyusui' value='1' " . ($data_pio['menyusui'] ? 'checked' : '') . "> Ya
								</label>
							</div>
						</div>
						
						<h4><u>PERTANYAAN</u></h4>
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Uraian Pertanyaan</label>
							<div class='col-sm-10'>
								<textarea name='uraian_pertanyaan' class='form-control' rows='4' required>" . htmlspecialchars($data_pio['uraian_pertanyaan']) . "</textarea>
							</div>
						</div>
						
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Jenis Pertanyaan</label>
							<div class='col-sm-10'>
								<div class='row'>
									<div class='col-sm-4'>
										<label class='checkbox-inline' style='display:block;'>
											<input type='checkbox' name='jenis_pertanyaan[]' value='identifikasi_obat' " . ($data_pio['jenis_pertanyaan_identifikasi_obat'] ? 'checked' : '') . "> Identifikasi Obat
										</label>
										<label class='checkbox-inline' style='display:block;'>
											<input type='checkbox' name='jenis_pertanyaan[]' value='interaksi_obat' " . ($data_pio['jenis_pertanyaan_interaksi_obat'] ? 'checked' : '') . "> Interaksi Obat
										</label>
										<label class='checkbox-inline' style='display:block;'>
											<input type='checkbox' name='jenis_pertanyaan[]' value='harga_obat' " . ($data_pio['jenis_pertanyaan_harga_obat'] ? 'checked' : '') . "> Harga Obat
										</label>
										<label class='checkbox-inline' style='display:block;'>
											<input type='checkbox' name='jenis_pertanyaan[]' value='kontra_indikasi' " . ($data_pio['jenis_pertanyaan_kontra_indikasi'] ? 'checked' : '') . "> Kontra Indikasi
										</label>
										<label class='checkbox-inline' style='display:block;'>
											<input type='checkbox' name='jenis_pertanyaan[]' value='cara_pemakaian' " . ($data_pio['jenis_pertanyaan_cara_pemakaian'] ? 'checked' : '') . "> Cara Pemakaian
										</label>
									</div>
									<div class='col-sm-4'>
										<label class='checkbox-inline' style='display:block;'>
											<input type='checkbox' name='jenis_pertanyaan[]' value='stabilitas' " . ($data_pio['jenis_pertanyaan_stabilitas'] ? 'checked' : '') . "> Stabilitas
										</label>
										<label class='checkbox-inline' style='display:block;'>
											<input type='checkbox' name='jenis_pertanyaan[]' value='dosis' " . ($data_pio['jenis_pertanyaan_dosis'] ? 'checked' : '') . "> Dosis
										</label>
										<label class='checkbox-inline' style='display:block;'>
											<input type='checkbox' name='jenis_pertanyaan[]' value='keracunan' " . ($data_pio['jenis_pertanyaan_keracunan'] ? 'checked' : '') . "> Keracunan
										</label>
										<label class='checkbox-inline' style='display:block;'>
											<input type='checkbox' name='jenis_pertanyaan[]' value='efek_samping' " . ($data_pio['jenis_pertanyaan_efek_samping'] ? 'checked' : '') . "> Efek Samping Obat
										</label>
										<label class='checkbox-inline' style='display:block;'>
											<input type='checkbox' name='jenis_pertanyaan[]' value='penggunaan_terapeutik' " . ($data_pio['jenis_pertanyaan_penggunaan_terapeutik'] ? 'checked' : '') . "> Penggunaan Terapeutik
										</label>
									</div>
									<div class='col-sm-4'>
										<label class='checkbox-inline' style='display:block;'>
											<input type='checkbox' name='jenis_pertanyaan[]' value='farmakokinetika' " . ($data_pio['jenis_pertanyaan_farmakokinetika'] ? 'checked' : '') . "> Farmakokinetika
										</label>
										<label class='checkbox-inline' style='display:block;'>
											<input type='checkbox' name='jenis_pertanyaan[]' value='farmakodinamika' " . ($data_pio['jenis_pertanyaan_farmakodinamika'] ? 'checked' : '') . "> Farmakodinamika
										</label>
										<label class='checkbox-inline' style='display:block;'>
											<input type='checkbox' name='jenis_pertanyaan[]' value='ketersediaan_obat' " . ($data_pio['jenis_pertanyaan_ketersediaan_obat'] ? 'checked' : '') . "> Ketersediaan Obat
										</label>
										<label class='checkbox-inline' style='display:block;'>
											<input type='checkbox' name='jenis_pertanyaan[]' value='lain_lain' onclick='toggleLainLain(this)' " . ($data_pio['jenis_pertanyaan_lain_lain'] ? 'checked' : '') . "> Lain-lain
										</label>
										<input type='text' id='jenis_pertanyaan_lain_lain_ket' name='jenis_pertanyaan_lain_lain_ket' class='form-control' placeholder='Sebutkan' value='" . htmlspecialchars($data_pio['jenis_pertanyaan_lain_lain_ket']) . "' style='margin-top:5px;' " . (!$data_pio['jenis_pertanyaan_lain_lain'] ? 'disabled' : '') . ">
									</div>
								</div>
							</div>
						</div>
						
						<h4><u>JAWABAN & REFERENSI</u></h4>
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Jawaban</label>
							<div class='col-sm-10'>
								<textarea name='jawaban' class='form-control' rows='5'>" . htmlspecialchars($data_pio['jawaban']) . "</textarea>
							</div>
						</div>
						
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Referensi</label>
							<div class='col-sm-10'>
								<textarea name='referensi' class='form-control' rows='3'>" . htmlspecialchars($data_pio['referensi']) . "</textarea>
							</div>
						</div>
						
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Penyampaian Jawaban</label>
							<div class='col-sm-10'>
								<label class='radio-inline'>
									<input type='radio' name='penyampaian_jawaban' value='Segera' " . ($data_pio['penyampaian_jawaban'] == 'Segera' ? 'checked' : '') . "> Segera
								</label>
								<label class='radio-inline'>
									<input type='radio' name='penyampaian_jawaban' value='Dalam 24 jam' " . ($data_pio['penyampaian_jawaban'] == 'Dalam 24 jam' ? 'checked' : '') . "> Dalam 24 jam
								</label>
								<label class='radio-inline'>
									<input type='radio' name='penyampaian_jawaban' value='Lebih dari 24 jam' " . ($data_pio['penyampaian_jawaban'] == 'Lebih dari 24 jam' ? 'checked' : '') . "> Lebih dari 24 jam
								</label>
							</div>
						</div>
						
						<h4><u>APOTEKER PENJAWAB</u></h4>
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Nama Apoteker</label>
							<div class='col-sm-4'>
								<input type='text' name='apoteker_penjawab' class='form-control' value='" . htmlspecialchars($data_pio['apoteker_penjawab']) . "'>
							</div>
						</div>
						
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Tanggal Jawab</label>
							<div class='col-sm-2'>
								<input type='date' name='tanggal_jawab' class='form-control' value='" . $data_pio['tanggal_jawab'] . "'>
							</div>
							<label class='col-sm-2 control-label'>Waktu Jawab</label>
							<div class='col-sm-2'>
								<input type='time' name='waktu_jawab' class='form-control' value='" . $data_pio['waktu_jawab'] . "'>
							</div>
						</div>
						
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Metode Jawaban</label>
							<div class='col-sm-10'>
								<label class='radio-inline'>
									<input type='radio' name='metode_jawab' value='Lisan' " . ($data_pio['metode_jawab'] == 'Lisan' ? 'checked' : '') . "> Lisan
								</label>
								<label class='radio-inline'>
									<input type='radio' name='metode_jawab' value='Tertulis' " . ($data_pio['metode_jawab'] == 'Tertulis' ? 'checked' : '') . "> Tertulis
								</label>
								<label class='radio-inline'>
									<input type='radio' name='metode_jawab' value='Telepon' " . ($data_pio['metode_jawab'] == 'Telepon' ? 'checked' : '') . "> Telepon
								</label>
							</div>
						</div>
						
						<div class='form-group'>
							<div class='col-sm-offset-2 col-sm-10'>
								<button type='submit' class='btn btn-warning'>UPDATE</button>
								<button type='button' class='btn btn-default' onclick=\"window.location='?module=pio'\">BATAL</button>
							</div>
						</div>
					</form>
				</div>
			</div>
			
			<script>
			function toggleKehamilanMinggu(checkbox) {
				document.getElementById('kehamilan_minggu').disabled = !checkbox.checked;
			}
			
			function toggleLainLain(checkbox) {
				document.getElementById('jenis_pertanyaan_lain_lain_ket').disabled = !checkbox.checked;
			}
			</script>
			";
			break;
	}
}
?>
