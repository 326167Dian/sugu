<?php
session_start();
if (empty($_SESSION['username']) and empty($_SESSION['passuser'])) {
	echo "<link href=../css/style.css rel=stylesheet type=text/css>";
	echo "<div class='error msg'>Untuk mengakses Modul anda harus login.</div>";
} else {

	switch ($_GET['act']) {
		default:

?>


			<div class="box box-primary box-solid">
				<div class="box-header with-border">
					<h3 class="box-title">LAPORAN KOMISI PEGAWAI</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div><!-- /.box-tools
					-->
				</div>
				<div class="box-body">

					<form method="POST" action="?module=lapkomisi&act=tampil" target="_blank" enctype="multipart/form-data"
						class="form-horizontal">

						</br></br>


						<div class="form-group">
							<label class="col-sm-2 control-label">Tanggal Awal</label>
							<div class="col-sm-4">
								<div class="input-group date">
									<div class="input-group-addon">
										<span class="glyphicon glyphicon-th"></span>
									</div>
									<input type="text" required="required" class="datepicker" name="tgl_awal" autocomplete="off">
								</div>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label">Tanggal Akhir</label>
							<div class="col-sm-4">
								<div class="input-group date">
									<div class="input-group-addon">
										<span class="glyphicon glyphicon-th"></span>
									</div>
									<input type="text" required="required" class="datepicker" name="tgl_akhir" autocomplete="off">
								</div>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label"></label>
							<div class="buttons col-sm-4">
								<input class="btn btn-primary" type="submit" name="btn"
									value="TAMPIL">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
								<a class='btn  btn-danger' href='?module=home'>KEMBALI</a>
							</div>
						</div>

					</form>
				</div>

			</div>


		<?php

			break;
		case "tampil":

			$tgl_awal = $_POST['tgl_awal'];
			$tgl_akhir = $_POST['tgl_akhir'];
			$admin = $db->prepare("select * from admin where blokir='N' and akses_level='petugas' ");
            $admin->execute();


		?>
			<div class="box box-primary box-solid">
				<div class="box-header with-border">
					<h3 class="box-title">LAPORAN KOMISI PEGAWAI DARI TANGGAL <?php echo $tgl_awal ?> S/D
						<?php echo $tgl_akhir ?>
					</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div><!-- /.box-tools
					-->
				</div>
				<div class="box-body">
					<table id="example1" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>No</th>
								<th>Nama Petugas</th>
								<th>Komisi Produk</th>
								<th>Komisi Global</th>
								<th>SubTotal</th>

							</tr>
						</thead>
						<tbody>
							<?php

							$no = 1;
							while ($min = $admin->fetch(PDO::FETCH_ASSOC)) {
								$kom = $db->prepare("select sum(komisi) as tambahan from trkasir_detail join trkasir 
															on(trkasir_detail.kd_trkasir=trkasir.kd_trkasir) 
															where trkasir_detail.idadmin='$min[id_admin]' and trkasir.tgl_trkasir between '$tgl_awal' and '$tgl_akhir' ");
								$kom->execute();
								$misi = $kom->fetch(PDO::FETCH_ASSOC);
								$pk = $misi['tambahan'];

								$petugas = $min['nama_lengkap'];
								$stmt_global = $db->prepare("select * from komisiglobal where status='ON' ");
								$stmt_global->execute();
								$global1 = $stmt_global->fetch(PDO::FETCH_ASSOC);
								$kogo = $global1['nilai'] / 100;

								$stmt_komisi = $db->prepare("SELECT SUM(ttl_trkasir) as total_komisi FROM trkasir
                            		where tgl_trkasir between ? and ? and petugas=? ");
								$stmt_komisi->execute([$tgl_awal, $tgl_akhir, $petugas]);
								$komisi = $stmt_komisi->fetch(PDO::FETCH_ASSOC);
								$komisipetugas = format_rupiah(round(($komisi['total_komisi'] * $kogo),0));

								$subtotal = format_rupiah(($komisi['total_komisi'] * $kogo)+$pk);


								echo "
								<tr>
									<td width='10px'>$no</td>
									<td>$min[nama_lengkap]</td>
									<td style='text-align:right; font-weight: bold;'><a href='?module=lapkomisi&act=detail&id=$min[id_admin]&tgl_awal=$tgl_awal&tgl_akhir=$tgl_akhir' target='_blank' >Rp. $pk</a></td>									
									<td style='text-align:right;'>Rp. $komisipetugas</td>
									<td style='text-align:right;'>Rp. $subtotal</td>
									</tr>								
								";
								$no++;
								$totalpk[] = $pk;
								$totglobal[] = $komisi['total_komisi']*$kogo;
							}
							?>
						</tbody>
						<tfoot style="font-weight: bold; background-color: aqua; font-size:large;">
							<?php

							$totalpk2 = format_rupiah(array_sum($totalpk));
							$ttlgbl =  format_rupiah(array_sum($totglobal));
							$grttl = format_rupiah(array_sum($totalpk)+array_sum($totglobal));

							echo "
							<tr>
								<td style='text-align:center;' colspan='2'>Grand Total Komisi</td>
								<td style='text-align:right;'>Rp. $totalpk2</td>
								<td style='text-align:right;'>Rp. $ttlgbl</td>
								<td style='text-align:right;'>Rp. $grttl</td>
							</tr>";
							?>
						</tfoot>
					</table>
				</div>
			</div>
		<?php
			break;

		case "detail":
			$admin = $_GET['id'];
			$tgl_awal = $_GET['tgl_awal'];
			$tgl_akhir = $_GET['tgl_akhir'];

			$mince = $db->prepare("select * from admin where id_admin=$admin");
			$mince->execute();
			$mimin = $mince->fetch(PDO::FETCH_ASSOC);
			$petugas = $mimin['nama_lengkap'];


		?>
			<div class="box box-primary box-solid">
				<div class="box-header with-border">
					<h3 class="box-title">Detail Komisi <?php echo $petugas ?> dari tanggal <?php echo $tgl_awal ?> s/d
						<?php echo $tgl_akhir ?>
					</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div><!-- /.box-tools
					-->
				</div>
				<div class="box-body">
					<table id="example1" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th style="text-align:center;" width="30">No</th>
								<th style="text-align:center;">Nama Produk</th>
								<th style="text-align:center;">Jumlah</th>
								<th style="text-align:center;">Nilai Komisi</th>
								<th style="text-align:center;">Subtotal</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$person = $db->prepare("select * from trkasir_detail join trkasir 
															on(trkasir_detail.kd_trkasir=trkasir.kd_trkasir) 
															where trkasir_detail.idadmin='$admin' and trkasir_detail.komisi!=0 and trkasir.tgl_trkasir between '$tgl_awal' and '$tgl_akhir' ");
							$person->execute();
							$no2 = 1;
							while ($per = $person->fetch(PDO::FETCH_ASSOC)) {
								$satuan = format_rupiah($per['komisi'] / $per['qty_dtrkasir']);
								$subttl = format_rupiah($per['komisi']);
								echo "
																<tr>
																<td width='30'>$no2</td>
																<td>$per[nmbrg_dtrkasir]</td>
																<td style='text-align:center;'>$per[qty_dtrkasir]</td>
																<td style='text-align:right;'>Rp. $satuan</td>
																<td style='text-align:right;'>Rp. $subttl</td>
																</tr>
																";
								$no2++;
								$kumulatif[] = $per['komisi'];
							}

							?>
						</tbody>
						<tfoot style="font-weight: bold; background-color: aqua; font-size:large;">
							<?php
							$total = format_rupiah(array_sum($kumulatif));

							echo "
							<tr>
								<td style='text-align:right;' colspan='4'>Total Komisi Produk</td>
								<td style='text-align:right;'>Rp. $total</td>
							</tr>";
							?>
						</tfoot>
					</table>
				</div>
	<?php
			break;
	}
}
	?>

	<script type="text/javascript">
		$(function() {
			$(".datepicker").datepicker({
				format: 'yyyy-mm-dd',
				autoclose: true,
				todayHighlight: true,
			});
		});
	</script>