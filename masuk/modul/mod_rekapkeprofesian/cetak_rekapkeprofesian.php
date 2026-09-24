<?php
session_start();
if (empty($_SESSION['username']) and empty($_SESSION['passuser'])) {
	echo "<link href=../css/style.css rel=stylesheet type=text/css>";
	echo "<div class='error msg'>Untuk mengakses Modul anda harus login.</div>";
	exit;
}

include "../../../configurasi/koneksi.php";
include "../../../configurasi/fungsi_indotgl.php";
include "fungsi_rekapkeprofesian.php";

function e($s)
{
	return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function angka($n)
{
	return number_format($n, 0, ',', '.');
}

function tgl_valid($s)
{
	$d = DateTime::createFromFormat('Y-m-d', (string)$s);
	return ($d && $d->format('Y-m-d') === $s) ? $s : '';
}

$tglAwal = tgl_valid(isset($_POST['tgl_awal']) ? $_POST['tgl_awal'] : '');
$tglAkhir = tgl_valid(isset($_POST['tgl_akhir']) ? $_POST['tgl_akhir'] : '');
$tglTtd = tgl_valid(isset($_POST['tgl_ttd']) ? $_POST['tgl_ttd'] : '');
$sipaBerlaku = tgl_valid(isset($_POST['sipa_berlaku']) ? $_POST['sipa_berlaku'] : '');
$idAdmin = intval(isset($_POST['id_admin']) ? $_POST['id_admin'] : 0);
$namaGelar = trim(isset($_POST['nama_gelar']) ? $_POST['nama_gelar'] : '');
$jabatan = trim(isset($_POST['jabatan']) ? $_POST['jabatan'] : '');
$noStra = trim(isset($_POST['no_stra']) ? $_POST['no_stra'] : '');
$noSipa = trim(isset($_POST['no_sipa']) ? $_POST['no_sipa'] : '');
$namaDirektur = trim(isset($_POST['nama_direktur']) ? $_POST['nama_direktur'] : '');

$error = '';
if ($tglAwal == '' || $tglAkhir == '') {
	$error = 'Tanggal awal dan tanggal akhir wajib diisi (format yyyy-mm-dd).';
} elseif ($tglAwal > $tglAkhir) {
	$error = 'Tanggal awal tidak boleh lebih besar dari tanggal akhir.';
} elseif (count(rekap_keprofesian_daftar_bulan($tglAwal, $tglAkhir)) > 12) {
	$error = 'Periode maksimal 12 bulan.';
} elseif ($idAdmin <= 0 || $namaGelar == '') {
	$error = 'Apoteker wajib dipilih.';
} elseif ($namaDirektur == '') {
	$error = 'Nama Direktur Utama wajib diisi.';
}
if ($error != '') {
	echo "<p style='font-family:Arial;color:#c00'>" . e($error) . "</p><p><a href='javascript:window.close()'>Tutup</a></p>";
	exit;
}
if ($tglTtd == '') {
	$tglTtd = date('Y-m-d');
}
if ($jabatan == '') {
	$jabatan = 'Apoteker';
}

// Simpan data keprofesian apoteker supaya terisi otomatis di cetakan berikutnya
pastikan_tabel_apoteker_profesi($db);
$simpan = $db->prepare("INSERT INTO apoteker_profesi (id_admin, nama_gelar, jabatan, no_stra, no_sipa, sipa_berlaku)
	VALUES (?, ?, ?, ?, ?, ?)
	ON DUPLICATE KEY UPDATE nama_gelar = VALUES(nama_gelar), jabatan = VALUES(jabatan),
		no_stra = VALUES(no_stra), no_sipa = VALUES(no_sipa), sipa_berlaku = VALUES(sipa_berlaku)");
$simpan->execute(array($idAdmin, $namaGelar, $jabatan, $noStra, $noSipa, ($sipaBerlaku != '') ? $sipaBerlaku : null));

$rh = $db->query("SELECT * FROM setheader ORDER BY id_setheader LIMIT 1")->fetch(PDO::FETCH_ASSOC);
$namaApotek = $rh['satu'];
$alamatApotek = trim(trim($rh['dua']) . (trim($rh['tiga']) != '' ? ', ' . trim($rh['tiga']) : ''));
$telpApotek = trim($rh['enam']);
$kotaApotek = trim($rh['tigabelas']);

list($bulan, $grup) = rekap_keprofesian_data($db, $tglAwal, $tglAkhir);

$tahunAwal = substr($tglAwal, 0, 4);
$satuTahun = ($tahunAwal == substr($tglAkhir, 0, 4));

// Jumlah jenis kegiatan yang ada (> 0) per bulan
$jenisPerBulan = array_fill_keys($bulan, 0);
foreach ($grup as $kegiatan) {
	foreach ($kegiatan as $k) {
		foreach ($bulan as $b) {
			if ($k['per_bulan'][$b] > 0) {
				$jenisPerBulan[$b]++;
			}
		}
	}
}

$jumlahBulan = count($bulan);
?>
<!DOCTYPE html>
<html lang="id">

<head>
	<meta charset="UTF-8">
	<title>Rekap Administratif Keprofesian <?= e(tgl_indo($tglAwal)) ?> - <?= e(tgl_indo($tglAkhir)) ?></title>
	<style>
		@page {
			size: A4 landscape;
			margin: 8mm 10mm;
		}

		body {
			font-family: Arial, Helvetica, sans-serif;
			font-size: 11px;
			color: #000;
			margin: 0;
		}

		.kop {
			text-align: center;
			border-bottom: 3px double #000;
			padding-bottom: 6px;
		}

		.kop .nama {
			font-size: 17px;
			font-weight: bold;
		}

		.judul {
			text-align: center;
			font-weight: bold;
			font-size: 13px;
			margin: 8px 0 6px;
		}

		.identitas {
			width: 100%;
			margin: 0 0 6px;
			border-collapse: collapse;
		}

		.identitas td {
			padding: 1px 4px;
			vertical-align: top;
		}

		.rekap {
			width: 100%;
			border-collapse: collapse;
		}

		.rekap th,
		.rekap td {
			border: 1px solid #000;
			padding: 2px 3px;
		}

		.rekap th {
			background: #d9d9d9;
			text-align: center;
		}

		.rekap td.n {
			text-align: center;
		}

		.rekap tr.grup td,
		.rekap tr.jumlah td {
			background: #eee;
			font-weight: bold;
		}

		.sumber {
			font-size: 9px;
			margin-top: 4px;
		}

		.ttd {
			width: 100%;
			margin-top: 8px;
		}

		.ttd td {
			width: 50%;
			text-align: center;
			vertical-align: top;
		}

		.ttd .ruang {
			height: 55px;
		}

		.toolbar {
			text-align: right;
			margin-bottom: 8px;
		}

		@media print {
			.toolbar {
				display: none;
			}

			.rekap th,
			.rekap tr.grup td,
			.rekap tr.jumlah td {
				-webkit-print-color-adjust: exact;
				print-color-adjust: exact;
			}
		}
	</style>
</head>

<body>
	<div class="toolbar">
		<button onclick="window.print()">Cetak / Simpan PDF</button>
	</div>

	<div class="kop">
		<div class="nama"><?= e(strtoupper($namaApotek)) ?></div>
		<?php if ($alamatApotek != '') { ?><div><?= e($alamatApotek) ?></div><?php } ?>
		<?php if ($telpApotek != '') { ?><div>Telp/WA: <?= e($telpApotek) ?></div><?php } ?>
	</div>

	<div class="judul">
		REKAPITULASI KEGIATAN PRAKTIK PROFESI<br>
		PELAYANAN ADMINISTRATIF KEPROFESIAN (BIDANG PELAYANAN KEFARMASIAN)
	</div>

	<table class="identitas">
		<tr>
			<td style="width:15%">Nama</td>
			<td style="width:35%">: <?= e($namaGelar) ?></td>
			<td style="width:12%">Jabatan</td>
			<td>: <?= e($jabatan) ?></td>
		</tr>
		<tr>
			<td>No. STRA</td>
			<td>: <?= e($noStra) ?></td>
			<td>Tempat Praktik</td>
			<td>: <?= e($namaApotek) ?></td>
		</tr>
		<tr>
			<td>No. SIPA</td>
			<td>: <?= e($noSipa) ?></td>
			<td>Periode Kegiatan</td>
			<td>: <?= e(tgl_indo($tglAwal)) ?> s/d <?= e(tgl_indo($tglAkhir)) ?> (<?= $jumlahBulan ?> bulan)</td>
		</tr>
		<?php if ($sipaBerlaku != '') { ?>
			<tr>
				<td>SIPA berlaku sampai</td>
				<td>: <?= e(tgl_indo($sipaBerlaku)) ?></td>
				<td></td>
				<td></td>
			</tr>
		<?php } ?>
	</table>

	<table class="rekap">
		<thead>
			<tr>
				<th rowspan="2" style="width:25px">No</th>
				<th rowspan="2" style="width:34%">Kegiatan Administratif</th>
				<th colspan="<?= $jumlahBulan ?>">Jumlah per Bulan<?= $satuTahun ? ' (' . e($tahunAwal) . ')' : '' ?></th>
				<th rowspan="2" style="width:55px">Total</th>
			</tr>
			<tr>
				<?php foreach ($bulan as $b) { ?>
					<th style="width:<?= round(52 / $jumlahBulan, 2) ?>%"><?= rekap_keprofesian_bulan_singkat(substr($b, 5, 2)) . ($satuTahun ? '' : ' ' . substr($b, 2, 2)) ?></th>
				<?php } ?>
			</tr>
		</thead>
		<tbody>
			<?php
			$no = 1;
			foreach ($grup as $namaGrup => $kegiatan) {
			?>
				<tr class="grup">
					<td></td>
					<td colspan="<?= $jumlahBulan + 2 ?>"><?= e($namaGrup) ?></td>
				</tr>
				<?php foreach ($kegiatan as $k) { ?>
					<tr>
						<td class="n"><?= $no++ ?></td>
						<td><?= e($k['nama']) ?></td>
						<?php foreach ($bulan as $b) { ?>
							<td class="n"><?= angka($k['per_bulan'][$b]) ?></td>
						<?php } ?>
						<td class="n"><strong><?= angka(array_sum($k['per_bulan'])) ?></strong></td>
					</tr>
				<?php } ?>
			<?php } ?>
			<tr class="jumlah">
				<td></td>
				<td>Jumlah jenis kegiatan administratif per bulan</td>
				<?php foreach ($bulan as $b) { ?>
					<td class="n"><?= $jenisPerBulan[$b] ?></td>
				<?php } ?>
				<td></td>
			</tr>
		</tbody>
	</table>
	<div class="sumber">
		Sumber data: Sistem informasi manajemen <?= e($namaApotek) ?> (data Surat Pesanan, faktur penerimaan barang, dan transaksi pengeluaran)
		serta Tanda Terima Pelaporan SIPNAP periode <?= e(getBulan(substr($tglAwal, 5, 2)) . ' ' . substr($tglAwal, 0, 4)) ?>
		s/d <?= e(getBulan(substr($tglAkhir, 5, 2)) . ' ' . substr($tglAkhir, 0, 4)) ?>.
	</div>

	<table class="ttd">
		<tr>
			<td>
				<br>
				Yang Membuat,<br>
				<?= e($jabatan) ?>
				<div class="ruang"></div>
				<strong><?= e($namaGelar) ?></strong><br>
				<?php if ($noSipa != '') { ?>No. SIPA: <?= e($noSipa) ?><?php } ?>
			</td>
			<td>
				<?= e(($kotaApotek != '' ? $kotaApotek . ', ' : '') . tgl_indo($tglTtd)) ?><br>
				Mengetahui dan Mengesahkan,<br>
				Direktur Utama
				<div class="ruang"></div>
				<strong><?= e($namaDirektur) ?></strong>
			</td>
		</tr>
	</table>
</body>

</html>
