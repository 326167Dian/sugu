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
					<h3 class="box-title">LAPORAN BARANG MASUK</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div><!-- /.box-tools
					-->
				</div>
				<div class="box-body">

					<form method="POST" action="modul/mod_laporan/cetak_brgmasuk.php" enctype="multipart/form-data"
						class="form-horizontal" target='_blank'>

						</br></br>


						<div class="form-group">
							<label class="col-sm-2 control-label">Tanggal</label>
							<div class="col-sm-4">
								<div class="input-group date">
									<div class="input-group-addon">
										<span class="glyphicon glyphicon-th"></span>
									</div>
									<input type="text" required="required" class="datepicker" name="tgl_awal" id="tgl_awal"
										autocomplete="off">
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
									<input type="text" required="required" class="datepicker" name="tgl_akhir" id="tgl_akhir"
										autocomplete="off">
								</div>
							</div>
						</div>

						<!-- <div class='form-group'>
							<label class='col-sm-2 control-label'>Supplier</label>
							<div class='col-sm-3'>
								<select name='supplier' class='form-control' id='supplier'>
									<?php
									$sup = $db->query("SELECT * FROM supplier ORDER BY id_supplier ASC");
									while ($sup1 = $sup->fetch(PDO::FETCH_ASSOC)) {
										echo "<option value='$sup1[id_supplier]'>$sup1[nm_supplier]</option>";
									}
									?>
								</select>

							</div>
						</div> -->


						<div class="form-group">
							<label class="col-sm-2 control-label"></label>
							<div class="buttons col-sm-4">
								<input class="btn btn-primary" type="submit" name="btn"
									value="TAMPIL">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
								<!-- <a class='btn  btn-primary' onclick='javascript:tampil()' target='_blank'>
									TAMPIL
								</a>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp -->

								<!-- <a class='btn  btn-success' onclick='javascript:exportExcel()' target='_blank'>
									<i class='fa fa-fw fa-file-excel-o'></i>EXPORT EXCEL
								</a>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp -->

								<a class='btn  btn-danger' href='?module=home'>KEMBALI</a>
							</div>
						</div>

					</form>
				</div>

			</div>

			<script>
				function exportExcel() {
					let tgl_awal = $('#tgl_awal').val();
					let tgl_akhir = $('#tgl_akhir').val();
					let supplier = $('#supplier').val();
					// window.location = 'modul/mod_lapstok/stokopname_excel.php?jenisobat='+jenisobat
					// window.open('modul/mod_lapstok/stokopname_excel.php?jenisobat='+jenisobat+'&start='+tgl_awal+'&finish='+tgl_akhir, '_blank');
					window.open('modul/mod_lapbrgmasuk/barangmasuk_excel.php?supplier='+supplier+'+&tgl_awal='+tgl_awal+'&tgl_akhir='+tgl_akhir, '_blank');
				}

			</script>
			<script type="text/javascript">
				$(function () {
					$(".datepicker").datepicker({
						format: 'yyyy-mm-dd',
						autoclose: true,
						todayHighlight: true,
					});
				});
			</script>


			<?php

			break;
		case "tampil":
			$tgl_awal = $_POST['tgl_awal'];
			$tgl_akhir = $_POST['tgl_akhir'];
			$supplier = $_POST['supplier'];

			$totalbeli = $db->prepare("select * from trbmasuk where id_supplier=? and tgl_trbmasuk between ? and ? ");
			$totalbeli->execute([$supplier, $tgl_awal, $tgl_akhir]);
			$dist = $totalbeli->fetch(PDO::FETCH_ASSOC);
			?>
			<div class="box box-primary box-solid">
				<div class="box-header with-border">
					<h3 class="box-title">Rekap Pembelian dari distributor <?= $dist['nm_supplier'] ?> Tanggal <?= $tgl_awal ?>
						hingga <?= $tgl_akhir ?> </h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div><!-- /.box-tools -->
				</div>
				<div class="box-body">

					<br><br>


					<table id="example1" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>No</th>
								<th>tanggal Pembelian</th>
								<th>Kode Transaksi</th>
								<th>Distributor</th>
								<th>Status Pembayaran</th>
								<th>Nilai Faktur</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$no = 1;
							while ($te = $totalbeli->fetch_array()) {

								$ttl = format_rupiah($te['ttl_trbmasuk']);
								echo "<tr class='warnabaris' >
											<td>$no</td>           
											 <td>$te[tgl_trbmasuk]</td>
											 <td>$te[kd_trbmasuk]</td>
											 <td>$te[nm_supplier]</td>
											 <td>$te[carabayar]</td>
											 <td style='text-align:right;'>Rp.  $ttl</td>								
											 
										</tr>";

								$total[] = $te['ttl_trbmasuk'];
								$no++;
							}
							echo "
						</tbody>
						<tfoot>
						";
							$tus = format_rupiah(array_sum($total));
							$totallunas = $db->query("select sum(ttl_trbmasuk) as lunas from trbmasuk where id_supplier=$supplier and tgl_trbmasuk between '$tgl_awal' and '$tgl_akhir' and carabayar='LUNAS' ");
							$lns = $totallunas->fetch_array();
							$lunas = format_rupiah($lns['lunas']);
							$belumlunas = $db->query("select sum(ttl_trbmasuk) as belum from trbmasuk where id_supplier=$supplier and tgl_trbmasuk between '$tgl_awal' and '$tgl_akhir' and carabayar='KREDIT'");
							$blm = $belumlunas->fetch_array();
							$belum = format_rupiah($blm['belum']);
							echo "
						            <tr style='background: #00fafa; font-size: 4vh;'>
                                        <td colspan='4'>Lunas = Rp. $lunas , Belum Lunas = Rp. $belum  => Total</td>
                                        <td style='text-align:right;' colspan='2'>Rp.  $tus</td>
                                    </tr>
						</tfoot>
						</table>";

							?>
				</div>

			</div>
			<?php

			break;

	}
}
?>