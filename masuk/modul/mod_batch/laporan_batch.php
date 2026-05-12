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
					<h3 class="box-title">LAPORAN DATA BATCH</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div><!-- /.box-tools
					-->
				</div>
				<div class="box-body">

					<form method="POST" action="?module=lapbatch&act=tampil" enctype="multipart/form-data"
						class="form-horizontal">

						</br></br>


						<div class="form-group">
							<label class="col-sm-2 control-label">Tanggal Expired From</label>
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
							<label class="col-sm-2 control-label">Tanggal Expired To</label>
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
			$tgl_awal = date('Y-m-d', strtotime($_POST['tgl_awal']));
			$tgl_akhir = date('Y-m-d', strtotime($_POST['tgl_akhir']));

			$getbatch = $db->prepare("SELECT 
                              no_batch, 
                              exp_date, 
                              kd_barang,
                              MIN(tgl_transaksi) AS tgl_awal, 
                              SUM(
                                CASE WHEN status = 'masuk' THEN qty ELSE 0 END
                              ) AS total_masuk, 
                              SUM(
                                CASE WHEN status = 'keluar' THEN qty ELSE 0 END
                              ) AS total_keluar, 
                              SUM(
                                CASE WHEN status = 'masuk' THEN qty ELSE 0 END
                              ) - SUM(
                                CASE WHEN status = 'keluar' THEN qty ELSE 0 END
                              ) AS sisa_qty 
                            FROM 
                              batch 
                            WHERE 
                              exp_date BETWEEN ? AND ?  
                            GROUP BY 
                              no_batch 
                            HAVING 
                              sisa_qty > 0 
                            ORDER BY 
                              CASE WHEN exp_date = '0000-00-00' THEN '9999-12-31' ELSE exp_date END ASC, 
                              exp_date ASC");
			$getbatch->execute([$tgl_awal, $tgl_akhir]);
// 			$batch = $getbatch->fetch(PDO::FETCH_ASSOC);
			?>
			<div class="box box-primary box-solid">
				<div class="box-header with-border">
					<h3 class="box-title">LAPORAN DATA BATCH Expired Tanggal <?= $tgl_awal ?>
						hingga <?= $tgl_akhir ?> </h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div><!-- /.box-tools -->
				</div>
				<div class="box-body">


					<table id="example1" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th style='text-align:center; width: 5%'>No</th>
								<th style='text-align:center'>Batch</th>
								<th style='text-align:center'>Exp. Date</th>
								<th style='text-align:center'>Kode Barang</th>
								<th style='text-align:center'>Nama Barang</th>
								<th style='text-align:center'>Quantity</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$no = 1;
							while ($bt = $getbatch->fetch(PDO::FETCH_ASSOC)) {
                                $barang = $db->prepare("SELECT nm_barang FROM barang WHERE kd_barang = :kd_barang");
                                $barang->execute([
                                    ':kd_barang'    => $bt['kd_barang']
                                ]);
                                $brg = $barang->fetch(PDO::FETCH_ASSOC);
                                
								echo "<tr class='warnabaris' >
											 <td style='text-align:center'>$no</td>           
											 <td>$bt[no_batch]</td>
											 <td style='text-align:center'>$bt[exp_date]</td>
											 <td>$bt[kd_barang]</td>
											 <td>$brg[nm_barang]</td>
											 <td style='text-align:center'>$bt[sisa_qty]</td>								
											 
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

	}
}
?>