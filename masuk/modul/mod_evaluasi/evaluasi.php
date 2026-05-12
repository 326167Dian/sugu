<?php
session_start();
if (empty($_SESSION['username']) and empty($_SESSION['passuser'])) {
	echo "<link href=../css/style.css rel=stylesheet type=text/css>";
	echo "<div class='error msg'>Untuk mengakses Modul anda harus login.</div>";
} else {

	$aksi = "modul/mod_komisi/aksi_komisi.php";
	
	switch ($_GET['act']) {
		// tampil satuan
		default:


			$tampil_komisi = $db->query("SELECT * FROM barang WHERE komisi != 0");

			?>


			<div class="box box-primary box-solid">
				<div class="box-header with-border">
					<h3 class="box-title">TAMBAH DAN TUTUP KOMISI</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div><!-- /.box-tools -->
				</div>
				<div class="box-body table-responsive">
					<?php if ($_SESSION['level'] == 'pemilik'): ?>
						<a class='btn  btn-success btn-flat' href='?module=komisi&act=tambah'>ATUR KOMISI</a>
						<a class='btn  btn-primary btn-flat' href='?module=komisi&act=global'>KOMISI GLOBAL</a>
						<a class='btn  btn-warning btn-flat' href='<?= $aksi . "?module=komisi&act=hapus&id=all" ?>'>HAPUS SEMUA
							KOMISI</a>
						<!--<a class='btn  btn-danger btn-flat' href='?module=komisi&act=tutupkomisi'>TUTUP KOMISI</a>-->
					<?php endif; ?>

					<br><br>


					<table id="example1" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>No</th>
								<th>Kode</th>
								<th>Nama Barang</th>
								<th style="text-align: right; ">Qty/Stok</th>
								<th style="text-align: right; ">Satuan</th>
								<th style="text-align: center; ">Jenis Obat</th>
								<th style="text-align: right; ">Harga Jual</th>
								<th style="text-align: right; ">Komisi Pegawai</th>
								<?php
								$lupa = $_SESSION['level'];
								if ($lupa == 'pemilik') {
									echo "<th>Aksi</th> ";
								} else {
								}
								?>
							</tr>
						</thead>
						<tbody>
							<?php
							$no = 1;
							while ($r = $tampil_komisi->fetch(PDO::FETCH_ASSOC)):
								$hargajual = format_rupiah($r['hrgjual_barang']);
								$komisi = format_rupiah($r['komisi']);
								?>
								<tr>
									<td><?= $no++ ?></td>
									<td><?= $r['kd_barang'] ?></td>
									<td><?= $r['nm_barang'] ?></th>
									<td style="text-align: center; "><?= $r['stok_barang'] ?></td>
									<td style="text-align: center; "><?= $r['sat_barang'] ?></td>
									<td style="text-align: center; "><?= $r['jenisobat'] ?></td>
									<td style="text-align: right; "><?= $hargajual ?></td>
									<td style="text-align: right; "><?= $komisi ?></td>
									<?php
									$lupa = $_SESSION['level'];
									if ($lupa == 'pemilik'):
										?>
										<td style="width: 80px; text-align: center">
											<a href="?module=komisi&act=editkomisi&id=<?= $r['id_barang'] ?>" title="EDIT"
												class="glyphicon glyphicon-pencil">&nbsp</a>
											<a href="javascript:confirmdelete('<?= $aksi . "?module=komisi&act=hapus&id=" . $r['id_barang'] ?>')"
												title="HAPUS" class="glyphicon glyphicon-remove">&nbsp</a>
										</td>
									<?php endif; ?>
								</tr>
								<?php
							endwhile;
							?>
						</tbody>
					</table>
				</div>
			</div>


			<?php

			break;

		case "tambah":

			?>
			<div class='box box-primary box-solid'>
				<div class='box-header with-border'>
					<h3 class='box-title'>TAMBAH KOMISI</h3>
					<div class='box-tools pull-right'>
						<button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
					</div><!-- /.box-tools -->
				</div>
				<div class='box-body'>

					<form method="POST" action="<?= $aksi ?>?module=komisi&act=input_komisi" enctype="multipart/form-data"
						class="form-horizontal">

						<div class='form-group'>
							<label class='col-sm-2 control-label'>Nama Barang</label>
							<div class='col-sm-6'>
								<input type="text" id="nm_barang" name="nm_barang" class="form-control" required="required" autocomplete="off">
							</div>
						</div>
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Harga Beli</label>
							<div class='col-sm-6'>
								<input type="number" name="hrgsat_barang" id="hrgsat_barang" class="form-control" required="required" autocomplete="off">
							</div>
						</div>
						<div class='form-group'>
							<label class='col-sm-2 control-label'>Harga Jual</label>
							<div class='col-sm-6'>
								<input type="number" name="hrgjual_barang" id="hrgjual_barang" class="form-control" required="required" autocomplete="off">
							</div>
						</div>

						<div class='form-group'>
							<label class="col-sm-2 control-label">Metode Pemberian</label>
							<div class="col-sm-5">
								<select name="metode" class="form-control">
									<option value="nominal">Nominal</option>
									<option value="persentase">Persentase</option>
								</select>
							</div>
						</div>

						<div class='form-group'>
							<label class='col-sm-2 control-label'>JUMLAH KOMISI</label>
							<div class='col-sm-6'>
								<input type="number" name="komisi" class="form-control" required="required" autocomplete="off">
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label"></label>
							<div class="col-sm-5">
								<input class="btn btn-primary" type="submit" value="SIMPAN">
								<input class="btn btn-danger" type="button" value="BATAL" onclick="self.history.back()">
							</div>
						</div>

					</form>

				</div>

			</div>

			<script>
				//auto_namabarang
				$('#nm_barang').typeahead({
					source: function (query, process) {
						return $.post('modul/mod_komisi/autonamabarang.php', {
							query: query
						}, function (data) {

							data = $.parseJSON(data);
							return process(data);

						});
					}
				});

				//enter barang
				$('#nm_barang').keydown(function (e) {
					if (e.which == 13) { // e.which == 13 merupakan kode yang mendeteksi ketika anda   // menekan tombol enter di keyboard
						//letakan fungsi anda disini

						var nm_barang = $("#nm_barang").val();
						$.ajax({
							url: 'modul/mod_komisi/autonamabarang_enter.php',
							type: 'post',
							data: {
								'nm_barang': nm_barang
							},
						}).success(function (data) {
							var json = data;
							//replace array [] menjadi ''
							var res1 = json.replace("[", "");
							var res2 = res1.replace("]", "");
							//INI CONTOH ARRAY JASON const json = '{"result":true, "count":42}';
							datab = JSON.parse(res2);
							document.getElementById('nm_barang').value = datab.nm_barang;
							document.getElementById('hrgsat_barang').value = datab.hrgsat_barang;
							document.getElementById('hrgjual_barang').value = datab.hrgjual_barang;
						});

					}
				});

			</script>


			<?php
			break;
		case "editkomisi":

			$edit = $db->prepare("SELECT * FROM barang WHERE id_barang=?");
			$edit->execute([$_GET['id']]);
			$r = $edit->fetch(PDO::FETCH_ASSOC);

			?>


			<div class='box box-primary box-solid'>
				<div class='box-header with-border'>
					<h3 class='box-title'>EDIT KOMISI</h3>
					<div class='box-tools pull-right'>
						<button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
					</div><!-- /.box-tools -->
				</div>
				<div class='box-body'>

					<form method="POST" action="<?= $aksi ?>?module=komisi&act=update_komisi" enctype="multipart/form-data"
						class="form-horizontal">

						<div class='form-group'>
							<label class="col-sm-2 control-label">NAMA BARANG</label>
							<div class="col-sm-3">
								<select name="barang" class="form-control js-example-basic-single">
									<option value="<?= $r['id_barang'] ?>"><?= $r['nm_barang'] ?></option>
								</select>
							</div>
						</div>

						<div class='form-group'>
							<label class="col-sm-2 control-label">Harga Beli</label>
							<div class="col-sm-3">
								<select name="barang" class="form-control js-example-basic-single">
									<option value="<?= $r['id_barang'] ?>"><?= $r['hrgsat_barang'] ?></option>
								</select>
							</div>
						</div>
						<div class='form-group'>
							<label class="col-sm-2 control-label">Harga Jual</label>
							<div class="col-sm-3">
								<select name="barang" class="form-control js-example-basic-single">
									<option value="<?= $r['id_barang'] ?>"><?= format_rupiah($r['hrgjual_barang']) ?></option>
								</select>
							</div>
						</div>


						<div class='form-group'>
							<label class="col-sm-2 control-label">Metode Diskon</label>
							<div class="col-sm-3">
								<select name="metode" class="form-control">
									<option value="nominal">Nominal</option>
									<option value="persentase">Persentase</option>
								</select>
							</div>
						</div>

						<div class='form-group'>
							<label class='col-sm-2 control-label'>Jumlah Komisi</label>
							<div class='col-sm-6'>
								<input type="number" name="komisi" class="form-control" required="required"
									value="<?= $r['komisi'] ?>" autocomplete="off">
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label"></label>
							<div class="col-sm-5">
								<input class="btn btn-primary" type="submit" value="SIMPAN">
								<input class="btn btn-danger" type="button" value="BATAL" onclick="self.history.back()">
							</div>
						</div>

					</form>

				</div>

			</div>

			<?php
			break;

		case "tutupkomisi":
			$staff = $db->query("SELECT * FROM admin WHERE akses_level = 'petugas' ORDER BY id_admin ASC");

			?>

			<div class="box box-primary box-solid">
				<div class="box-header with-border">
					<h3 class="box-title">TUTUP KOMISI</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div><!-- /.box-tools -->
				</div>
				<div class="box-body table-responsive">

					<table id="example1" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>No</th>
								<th>Nama</th>
								<th>Telp/HP</th>
								<th>Start Date</th>
								<th>Finish Date</th>
								<th style="text-align: right; ">Total Komisi</th>
								<th>Aksi</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$no = 1;
							while ($r = $staff->fetch(PDO::FETCH_ASSOC)):

								$query = $db->prepare("SELECT SUM(ttl_komisi) as total_komisi, MIN(tgl_komisi) as min_date, MAX(tgl_komisi) as max_date
						                FROM komisi_pegawai WHERE id_admin = ? AND status_komisi = 'on'");
								$query->execute([$r['id_admin']]);

								$kms = $query->fetch(PDO::FETCH_ASSOC);
								?>
								<tr>
									<td><?= $no++ ?></td>
									<td><?= $r['nama_lengkap'] ?></td>
									<td><?= $r['no_telp'] ?></th>
									<td style="text-align: center; "><?= $kms['min_date'] ?></td>
									<td style="text-align: center; "><?= $kms['max_date'] ?></td>
									<td style="text-align: right; "><?= format_rupiah($kms['total_komisi']) ?></td>
									<td style="width: 80px; text-align: center">
										<?php if ($kms['total_komisi'] > 0): ?>
											<a href="<?= $aksi ?>?module=komisi&act=close&id=<?= $r['id_admin'] ?>" title="closed"
												class="btn btn-primary">CLOSED</a>
										<?php else: ?>
											<a href="#" title="closed" class="btn btn-primary" disabled>CLOSED</a>
										<?php endif; ?>
									</td>

								</tr>
								<?php
							endwhile;
							?>
						</tbody>
					</table>
				</div>
			</div>

			<?php

		//    break;
//    case "bulan" :
//    break
		case "global":
			?>
			<div class="box box-primary box-solid">
				<div class="box-header with-border">
					<h3 class="box-title">INPUT KOMISI</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div><!-- /.box-tools -->
				</div>
				<div class="box-body table-responsive">
					<form method="POST" action="<?= $aksi ?>?module=komisi&act=input_komisiglobal" enctype="multipart/form-data"
						class="form-horizontal">

						<div class='col-sm-3'>
							<label>INPUT NILAI KOMISI GLOBAL (%)</label>
							<input type="number" name="nilai" id='nilai' class="form-control" required="required"
								autocomplete="off">
						</div>
						<div class='col-sm-3'>
							<label>STATUS PEMBERIAN KOMISI</label>
							<select name='status' id='status' class='form-control'>
								<?php
								$stat = $db->query("select * from komisiglobal where id_komisiglobal=1");
								$status = $stat->fetch(PDO::FETCH_ASSOC);
								echo "<option value='$status[status]'>$status[status]</option>
                                <option value='ON'>ON</option>
                                <option value='OFF'>OFF</option>
                                ";
								?>
							</select>

							<div>
								<input class="btn btn-primary" type="submit" value="SIMPAN">
							</div>

						</div>
					</form>

					<div>
						<div>
							<?php
							$global = mysqli_query($GLOBALS["___mysqli_ston"], "select * from komisiglobal where status='ON' ");
							$global1 = mysqli_fetch_array($global);
							$kogo = $global1['nilai'];

							echo "
                            <div col-sm-3>
                            Nilai komisi yang aktif saat ini = $kogo % 
                        </div><br><br><br>";
							?>
						</div>
						<table id="example1" class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>No</th>
									<th>Nama Admin</th>
									<th style="text-align: center; ">Bulan</th>
									<th style="text-align: center; ">Komisi</th>

								</tr>
							</thead>
							<tbody>
								<?php
								$admin1 = $db->query("select * from admin where akses_level='petugas' and blokir='N' ");

								$no = 1;
								$tgl_awal = (date('Y-m-d'));
								$bulan = substr($tgl_awal, 5, 2);
								$namabulan = getBulan($bulan);


								while ($r = mysqli_fetch_array($admin1)) {
									$petugas = $r['nama_lengkap'];
									$tanpatgl = substr($tgl_awal, 0, 8);
									$awalbulan = $tanpatgl . '01';
									$querykomisi = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT SUM(ttl_trkasir) as total_komisi FROM trkasir
                                where tgl_trkasir between '$awalbulan' and '$tgl_awal' and petugas='$petugas' ");
									$komisi = mysqli_fetch_array($querykomisi);
									$nilai = $kogo / 100;
									$komisi1 = $komisi['total_komisi'];
									$komisipetugas = format_rupiah($komisi1 * $nilai);
									$totalkomisi[] = $komisi1 * $nilai;

									echo "<tr class='warnabaris' >
											<td>$no</td>           
											 <td>$r[nama_lengkap]</td>
											 <td style='text-align:center;'>$namabulan</td>	
											 <td style='text-align:right'>Rp. $komisipetugas</td>
										</tr>";
									$no++;
								}
								$total_komisi = format_rupiah(array_sum($totalkomisi));


								echo "
                            </tbody>
                            <tfoot>
                            <tr>
                                <td colspan='3'>
                                    <h3>
                                        <center>Total</center>
                                    </h3>
                                </td>
                                <td colspan='2'>
                                    <h3><strong> Rp.  $total_komisi,- </strong></h3>
                                </td>
                            </tr>
                            </tfoot>";
								?>
						</table>
					</div>
				</div>
				<div style='text-align: center'><a class='btn  btn-success btn-flat' href='?module=komisi&act=history'>History</a>
				</div>

			</div>
			<?php
			break;
		case "history":
			?>
			<div class="box box-primary box-solid">
				<div class="box-header with-border">
					<h3 class="box-title">INPUT BULAN</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div><!-- /.box-tools -->
				</div>
				<div class="box-body table-responsive">
					<form method="POST" action="?module=komisi&act=bulan" enctype="multipart/form-data" class="form-horizontal">
						<label>Bulan</label>
						<select name='bulan' id='bulan' class='form-control'>
							<?php
							echo "
                                <option value='1'>Januari</option>
                                <option value='2'>Febuari</option>
                                <option value='3'>Maret</option>
                                <option value='4'>April</option>
                                <option value='5'>Mei</option>
                                <option value='6'>Juni</option>
                                <option value='7'>Juli</option>
                                <option value='8'>Agustus</option>
                                <option value='9'>September</option>
                                <option value='10'>Oktober</option>
                                <option value='11'>November</option>
                                <option value='12'>Desember</option>                               
                                ";
							?>
						</select>
						<input class="btn btn-primary" type="submit" value="SIMPAN">
						<input class="btn btn-danger" type="button" value="KEMBALI" onclick="self.history.back()">
					</form>
				</div>
			</div>
			<?php
			break;
		case "bulan":
			$bulan = $_POST['bulan'];
			$we = getBulan($bulan);
			$tglhariini = date('Y-m-d');
			$tahun = substr($tglhariini, 0, 4);




			$admin1 = $db->query("select * from admin where akses_level='petugas' ");
			?>
			<table id="example1" class="table table-bordered table-striped">
				<thead>
					<tr>
						<th>No</th>
						<th>Nama Admin</th>
						<th style="text-align: center; ">Bulan</th>
						<th style="text-align: center; ">Komisi</th>
					</tr>
				</thead>
				<tbody>
					<?php
					$no = 1;
					while ($t = mysqli_fetch_array($admin1)) {
						$querykomisi = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT SUM(ttl_trkasir) as total_komisi FROM trkasir
                                where month (tgl_trkasir)='$bulan' and year (tgl_trkasir) ='2025' and petugas='$t[nama_lengkap]' ");
						$komisipetugas1 = mysqli_fetch_array($querykomisi);
						$kp = $komisipetugas1['total_komisi'];

						$kg1 = $db->query("select * from komisiglobal where month (tgl)='$bulan' ");
						$kg2 = $kg1->fetch_array();
						$angka = $kg2['nilai'];

						$ksh = format_rupiah($kp * $angka / 100);

						echo "<tr class='warnabaris' >
											<td>$no</td>           
											 <td>$t[nama_lengkap]</td>
											 <td style='text-align:center;'>$we</td>	
											 <td style='text-align:right'>Rp. $ksh </td>
										</tr>";
						$no++;
					}

					?>
				</tbody>
			</table>
			<input class="btn btn-danger" type="button" value="KEMBALI" onclick="self.history.back()">
			<?php
			break;


	}
}
?>

<script type="text/javascript">


	$(function () {
		$(".datepicker").datepicker({
			format: 'yyyy-mm-dd',
			autoclose: true,
			todayHighlight: true,
		});
	});

	$(document).ready(function () {
		$('.js-example-basic-single').select2();
	});
</script>