<?php
session_start();
if (empty($_SESSION['username']) and empty($_SESSION['passuser'])) {
	echo "<link href=../css/style.css rel=stylesheet type=text/css>";
	echo "<div class='error msg'>Untuk mengakses Modul anda harus login.</div>";
} else {
	include_once "modul/mod_rekapkeprofesian/fungsi_rekapkeprofesian.php";
	pastikan_tabel_apoteker_profesi($db);

	$admins = $db->query("SELECT a.id_admin, a.nama_lengkap,
			p.nama_gelar, p.jabatan, p.no_stra, p.no_sipa, p.sipa_berlaku
		FROM admin a
		LEFT JOIN apoteker_profesi p ON p.id_admin = a.id_admin
		WHERE a.blokir = 'N'
		ORDER BY a.nama_lengkap")->fetchAll(PDO::FETCH_ASSOC);

	// Data keprofesian per admin untuk mengisi form otomatis saat apoteker dipilih
	$profesi = array();
	foreach ($admins as $a) {
		$profesi[$a['id_admin']] = array(
			'nama_gelar' => ($a['nama_gelar'] != '') ? $a['nama_gelar'] : $a['nama_lengkap'],
			'jabatan' => ($a['jabatan'] != '') ? $a['jabatan'] : 'Apoteker',
			'no_stra' => (string)$a['no_stra'],
			'no_sipa' => (string)$a['no_sipa'],
			'sipa_berlaku' => (string)$a['sipa_berlaku'],
		);
	}

	$tahunLalu = date('Y') - 1;
?>
	<div class="box box-primary box-solid">
		<div class="box-header with-border">
			<h3 class="box-title">REKAP ADMINISTRATIF KEPROFESIAN</h3>
			<div class="box-tools pull-right">
				<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
			</div>
		</div>
		<div class="box-body">

			<form method="POST" action="modul/mod_rekapkeprofesian/cetak_rekapkeprofesian.php" target="_blank" class="form-horizontal" id="formRekap">
				<br>
				<h4 class="col-sm-offset-2">Periode Kegiatan</h4>
				<div class="form-group">
					<label class="col-sm-2 control-label">Tanggal Awal</label>
					<div class="col-sm-4">
						<div class="input-group date">
							<div class="input-group-addon"><span class="glyphicon glyphicon-th"></span></div>
							<input type="text" required="required" class="datepicker form-control" name="tgl_awal" value="<?= $tahunLalu ?>-01-01" autocomplete="off">
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">Tanggal Akhir</label>
					<div class="col-sm-4">
						<div class="input-group date">
							<div class="input-group-addon"><span class="glyphicon glyphicon-th"></span></div>
							<input type="text" required="required" class="datepicker form-control" name="tgl_akhir" value="<?= $tahunLalu ?>-12-31" autocomplete="off">
						</div>
						<small class="text-muted">Maksimal 12 bulan.</small>
					</div>
				</div>

				<h4 class="col-sm-offset-2">Apoteker</h4>
				<div class="form-group">
					<label class="col-sm-2 control-label">Pilih Admin</label>
					<div class="col-sm-4">
						<select name="id_admin" id="id_admin" class="form-control" required="required">
							<option value="">- Pilih Apoteker -</option>
							<?php foreach ($admins as $a) { ?>
								<option value="<?= $a['id_admin'] ?>" <?= ($a['id_admin'] == $_SESSION['idadmin']) ? 'selected' : '' ?>><?= htmlspecialchars($a['nama_lengkap']) ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">Nama + Gelar</label>
					<div class="col-sm-4">
						<input type="text" class="form-control" name="nama_gelar" id="nama_gelar" required="required" placeholder="apt. Nama Lengkap, S.Si">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">Jabatan</label>
					<div class="col-sm-4">
						<input type="text" class="form-control" name="jabatan" id="jabatan" required="required">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">No. STRA</label>
					<div class="col-sm-4">
						<input type="text" class="form-control" name="no_stra" id="no_stra">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">No. SIPA</label>
					<div class="col-sm-4">
						<input type="text" class="form-control" name="no_sipa" id="no_sipa">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">SIPA Berlaku Sampai</label>
					<div class="col-sm-4">
						<div class="input-group date">
							<div class="input-group-addon"><span class="glyphicon glyphicon-th"></span></div>
							<input type="text" class="datepicker form-control" name="sipa_berlaku" id="sipa_berlaku" autocomplete="off">
						</div>
						<small class="text-muted">Data apoteker di atas otomatis tersimpan saat laporan dicetak.</small>
					</div>
				</div>

				<h4 class="col-sm-offset-2">Pengesahan</h4>
				<div class="form-group">
					<label class="col-sm-2 control-label">Nama Direktur Utama</label>
					<div class="col-sm-4">
						<input type="text" class="form-control" name="nama_direktur" id="nama_direktur" required="required">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label">Tanggal Tanda Tangan</label>
					<div class="col-sm-4">
						<div class="input-group date">
							<div class="input-group-addon"><span class="glyphicon glyphicon-th"></span></div>
							<input type="text" required="required" class="datepicker form-control" name="tgl_ttd" value="<?= date('Y-m-d') ?>" autocomplete="off">
						</div>
					</div>
				</div>

				<div class="form-group">
					<label class="col-sm-2 control-label"></label>
					<div class="buttons col-sm-4">
						<input class="btn btn-primary" type="submit" value="TAMPIL">&nbsp;&nbsp;
						<a class='btn btn-danger' href='?module=home'>KEMBALI</a>
					</div>
				</div>
			</form>
		</div>
	</div>

	<script type="text/javascript">
		$(function() {
			$(".datepicker").datepicker({
				format: 'yyyy-mm-dd',
				autoclose: true,
				todayHighlight: true,
			});

			var profesi = <?= json_encode($profesi) ?>;

			function isiApoteker() {
				var p = profesi[$('#id_admin').val()] || {nama_gelar: '', jabatan: 'Apoteker', no_stra: '', no_sipa: '', sipa_berlaku: ''};
				$('#nama_gelar').val(p.nama_gelar);
				$('#jabatan').val(p.jabatan);
				$('#no_stra').val(p.no_stra);
				$('#no_sipa').val(p.no_sipa);
				$('#sipa_berlaku').val(p.sipa_berlaku);
			}
			$('#id_admin').on('change', isiApoteker);
			isiApoteker();

			// Nama direktur diingat di browser ini
			try {
				$('#nama_direktur').val(localStorage.getItem('rekapkeprofesian_direktur') || '');
			} catch (e) {}
			$('#formRekap').on('submit', function() {
				try {
					localStorage.setItem('rekapkeprofesian_direktur', $('#nama_direktur').val());
				} catch (e) {}
			});
		});
	</script>
<?php
}
?>
