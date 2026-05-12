<?php
session_start();
if (empty($_SESSION['username']) and empty($_SESSION['passuser'])) {
	echo "<link href=../css/style.css rel=stylesheet type=text/css>";
	echo "<div class='error msg'>Untuk mengakses Modul anda harus login.</div>";
} else {

	$aksi = "modul/mod_barang/aksi_barang.php";
	$aksi_barang = "masuk/modul/mod_barang/aksi_barang.php";
	switch ($_GET['act']) {
			// Tampil barang
		default:
?>
			<div class="box box-primary box-solid">
				<div class="box-header with-border">
					<h3 class="box-title">STOK KRITIS</h3>
					<!--<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>-->

					<div class="box-tools pull-center">

					</div><!-- /.box-tools -->
				</div>
				<div class="box-body table-responsive">
					<!--<a  class ='btn  btn-success btn-flat' href='?module=barang&act=tambah'>TAMBAH</a>-->
					<CENTER><strong>STOK KRITIS</strong></CENTER><br>
					<hr>
					<form method="POST" action="#" target="_blank" enctype="multipart/form-data" class="form-horizontal">

						<div class="form-group">
							<label class="col-sm-2 control-label">Tanggal Awal</label>
							<div class="col-sm-4">
								<div class="input-group date">
									<div class="input-group-addon">
										<span class="glyphicon glyphicon-th"></span>
									</div>
									<input type="text" class="datepicker" name="tgl_awal" required="required" autocomplete="off" id="awal" value="<?= $_GET['start'] ?>">
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
									<input type="text" class="datepicker" name="tgl_akhir" required="required" autocomplete="off" id="akhir" value="<?= $_GET['finish'] ?>">
								</div>
							</div>
						</div>

						<div class="form-group">
							<label class="col-sm-2 control-label"></label>
							<div class="buttons col-sm-4">
								<input class="btn btn-primary" type="button" id="submit" name="btn" value="SUBMIT">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
								<a class='btn  btn-danger' href='?module=home'>KEMBALI</a>
							</div>
						</div>
					</form>
					<hr />
				</div>
			</div>
			<script>
				$("#submit").on("click", function() {
					var awal = $("#awal").val();
					var akhir = $("#akhir").val();
					location.href = "?module=stok_kritis&act=kritis&start=" + awal + "&finish=" + akhir;
				});
			</script>
		<?php
			break;

		case "kritis":

			$tampil_barang = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM barang where stok_barang <= stok_buffer ORDER BY barang.stok_barang ");

		?>


			<div class="box box-primary box-solid">
				<div class="box-header with-border">
					<h3 class="box-title">STOK KRITIS</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div><!-- /.box-tools -->
				</div>
				<div class="box-body table-responsive">
					<!--<a  class ='btn  btn-success btn-flat' href='?module=barang&act=tambah'>TAMBAH</a>-->
					<br><br>


					<table id="tes" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>No</th>
								<th>Kategori</th>
								<th>Nama Barang</th>
								<th style="text-align: right; ">Qty/Stok</th>
								<th style="text-align: right; ">Stok Buffer</th>
								<th style="text-align: center; ">T30</th>
								<th style="text-align: center; ">Q30</th>

								<th style="text-align: center; ">Satuan</th>

								<th style="text-align: right; ">Harga Beli</th>
								<!-- <th style="text-align: right; ">Aksi</th> -->

							</tr>
						</thead>
						<tbody>

						</tbody>
					</table>
				</div>
			</div>

			<script>
				$(document).ready(function() {
					var start = '<?= $start_date ?>';
					var finish = '<?= $finish_date ?>';

					$('#tes').DataTable({
						processing: true,
						serverSide: true,
						ajax: {
							"url": "modul/mod_lapstok/stokkritis-serverside.php?action=table_data&start=" + start + "&finish=" + finish,
							"dataType": "JSON",
							"type": "POST"
						},
						"rowCallback": function(row, data, index) {
							// warna for nomor
							if (data['t30'] <= 0) {
								$(row).find('td:eq(1)').css('background-color', '#dd4b39');
								$(row).find('td:eq(1)').css('color', '#ffffff');
							} else if (data['t30'] > 0 && data['t30'] <= 5) {
								$(row).find('td:eq(0)').css('background-color', '#f39c12');
								$(row).find('td:eq(0)').css('color', '#ffffff');
							} else if (data['t30'] > 0 && data['t30'] <= 10) {
								$(row).find('td:eq(0)').css('background-color', '#00a65a');
								$(row).find('td:eq(0)').css('color', '#ffffff');
							} else if (data['t30'] > 10) {
								$(row).find('td:eq(0)').css('background-color', '#00c0ef');
							}

						},
						columns: [{
								"data": "no",
								"className": 'text-center',
							},
							{
								"data": "kategori",
								"className": 'text-center',
							},
							{
								"data": "nm_barang"
							},
							{
								"data": "stok_barang",
								"className": 'text-center',
							},
							{
								"data": "stok_buffer",
								"className": 'text-center',
							},
							{
								"data": "t30",
								"className": 'text-center',
							},
							{
								"data": "q30",
								"className": 'text-center',
							},
							{
								"data": "satuan",
								"className": 'text-center',
							},
							{
								"data": "harga_beli",
								"className": 'text-right',
								"render": function(data, type, row) {
									return formatRupiah(data);
								}
							},
						],

					});

				});
			</script>
<?php

			break;

		case "tambah":

			echo "
		  <div class='box box-primary box-solid'>
				<div class='box-header with-border'>
					<h3 class='box-title'>TAMBAH DATA BARANG</h3>
					<div class='box-tools pull-right'>
						<button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
                    </div><!-- /.box-tools -->
				</div>
				<div class='box-body'>
				
						<form method=POST action='$aksi?module=barang&act=input_barang' enctype='multipart/form-data' class='form-horizontal'>
						
						<input type=hidden name='id_supplier' id='id_supplier'>
							  							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Kode Barang</label>        		
									 <div class='col-sm-3'>
										<input type=text name='kd_barang' class='form-control' autocomplete='off'>
									 </div>
							  </div>
							  
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Nama Barang</label>        		
									 <div class='col-sm-4'>
										<input type=text name='nm_barang' class='form-control' required='required' autocomplete='off'>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Qty/Stok</label>        		
									 <div class='col-sm-3'>
										<input type=number name='stok_barang' class='form-control' required='required' autocomplete='off'>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Stok Buffer</label>        		
									 <div class='col-sm-3'>
										<input type=number name='stok_buffer' class='form-control' required='required' autocomplete='off'>
									 </div>
							  </div>
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Satuan</label>        		
									 <div class='col-sm-3'>
										<select name='sat_barang' class='form-control' >";
			$tampil = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM satuan ORDER BY nm_satuan ASC");
			while ($rk = mysqli_fetch_array($tampil)) {
				echo "<option value=$rk[nm_satuan]>$rk[nm_satuan]</option>";
			}
			echo "</select>
									 </div>
							  </div> 
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Jenis Obat</label>        		
									 <div class='col-sm-3'>
										<select name='jenis_obat' class='form-control' >";
			$tampil = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM jenis_obat ORDER BY jenisobat ASC");
			while ($rk = mysqli_fetch_array($tampil)) {
				echo "<option value=$rk[jenisobat]>$rk[jenisobat]</option>";
			}
			echo "</select>
									 </div>
							  </div>
							  
							  

							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Harga Beli</label>        		
									 <div class='col-sm-3'>
										<input type=number name='hrgsat_barang' class='form-control' required='required' autocomplete='off'>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Harga Jual</label>        		
									 <div class='col-sm-3'>
										<input type=number name='hrgjual_barang' class='form-control' required='required' autocomplete='off'>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Expired Date</label>
										<div class='col-sm-4'>
											<div class='input-group date'>
												<div class='input-group-addon'>
													<span class='glyphicon glyphicon-th'></span>
												</div>
													<input type='text' class='datepicker' name='tgl_expired' required='required' autocomplete='off'>
											</div>
										</div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Keterangan Lain</label>        		
									 <div class='col-sm-4'>
										<textarea name='ket_barang' class='form-control' rows='3'></textarea>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'></label>       
										<div class='col-sm-4'>
											<input class='btn btn-primary' type=submit value=SIMPAN>
											<input class='btn btn-danger' type=button value=BATAL onclick=self.history.back()>
										</div>
								</div>
								
							  </form>
							  
				</div> 
				
			</div>";


			break;

		case "edit":
			$edit = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM barang 
	WHERE barang.id_barang='$_GET[id]'");
			$r = mysqli_fetch_array($edit);

			echo "
		  <div class='box box-primary box-solid'>
				<div class='box-header with-border'>
					<h3 class='box-title'>UBAH DATA BARANG</h3>
					<div class='box-tools pull-right'>
						<button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
                    </div><!-- /.box-tools -->
				</div>
				<div class='box-body'>
						<form method=POST method=POST action=$aksi?module=barang&act=update_barang  enctype='multipart/form-data' class='form-horizontal'>
							  <input type=hidden name=id value='$r[id_barang]'>
							  
							 
							 <div class='form-group'>
									<label class='col-sm-2 control-label'>Kode Barang</label>        		
									 <div class='col-sm-3'>
										<input type=text name='kd_barang' class='form-control' required='required' value='$r[kd_barang]' autocomplete='off'>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Nama Barang</label>        		
									 <div class='col-sm-4'>
										<input type=text name='nm_barang' class='form-control' required='required' value='$r[nm_barang]' autocomplete='off'>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Qty/Stok</label>        		
									 <div class='col-sm-3'>
										<input type=number name='stok_barang' class='form-control' required='required' value='$r[stok_barang]' autocomplete='off'>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Stok Buffer</label>        		
									 <div class='col-sm-3'>
										<input type=number name='stok_buffer' class='form-control' required='required' value='$r[stok_buffer]' autocomplete='off'>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Satuan</label>        		
									 <div class='col-sm-3'>
										<select name='sat_barang' class='form-control' >
											 <option  value=$r[sat_barang] selected>$r[sat_barang]</option>";
			$tampil = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM satuan ORDER BY nm_satuan");
			while ($k = mysqli_fetch_array($tampil)) {
				echo "<option value=$k[nm_satuan]>$k[nm_satuan]</option>";
			}
			echo "</select>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Harga Beli</label>        		
									 <div class='col-sm-3'>
										<input type=number name='hrgsat_barang' class='form-control' required='required' value='$r[hrgsat_barang]' autocomplete='off'>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Harga Jual</label>        		
									 <div class='col-sm-3'>
										<input type=number name='hrgjual_barang' class='form-control' required='required' value='$r[hrgjual_barang]' autocomplete='off'>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Expired Date</label>
										<div class='col-sm-4'>
											<div class='input-group date'>
												<div class='input-group-addon'>
													<span class='glyphicon glyphicon-th'></span>
												</div>
													<input type='text' class='datepicker' name='tgl_expired' required='required' value='$r[tgl_expired]' autocomplete='off'>
											</div>
										</div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Keterangan Lain</label>        		
									 <div class='col-sm-4'>
										<textarea name='ket_barang' class='form-control' rows='3'>$r[ket_barang]</textarea>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'></label>       
										<div class='col-sm-4'>
											<input class='btn btn-primary' type=submit value=SIMPAN>
											<input class='btn btn-danger' type=button value=BATAL onclick=self.history.back()>
										</div>
								</div>
								
							  </form>
							  
				</div> 
				
			</div>";




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