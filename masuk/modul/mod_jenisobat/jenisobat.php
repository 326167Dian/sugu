<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// session_start(); // Sudah aktif di media_admin.php
if (empty($_SESSION['username']) and empty($_SESSION['passuser'])) {
	echo "<link href=../css/style.css rel=stylesheet type=text/css>";
	echo "<div class='error msg'>Untuk mengakses Modul anda harus login.</div>";
} else {

	$aksi = "modul/mod_jenisobat/aksi_jenisobat.php";

	switch (isset($_GET['act']) ? $_GET['act'] : '') {
			// tampil jenis obat
		default:


			$stmt = $db->query("SELECT * FROM jenis_obat ORDER BY idjenis ");
			$tampil_jenisobat = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>


			<div class="box box-primary box-solid">
				<div class="box-header with-border">
					<h3 class="box-title">DATA JENIS OBAT dan RAK OBAT</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div><!-- /.box-tools -->
				</div>
				<div class="box-body">
					<a class='btn  btn-success btn-flat' href='?module=jenisobat&act=tambah'>TAMBAH</a>
					<br><br>


					<table id="example1" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>No</th>
								<th>Jenis & Rak Obat</th>
								<th>Deskripsi</th>
								<th width="70">Aksi</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$no = 1;
							foreach ($tampil_jenisobat as $r) {
								echo "<tr class='warnabaris' >
											<td>$no</td>           
											 <td>$r[jenisobat]</td>
											 <td>$r[ket]</td>
											 <td><a href='?module=jenisobat&act=edit&id=$r[idjenis]' title='EDIT' class='btn btn-warning btn-xs'>EDIT</a> 
											 <a href=javascript:confirmdelete('$aksi?module=jenisobat&act=hapus&id=$r[idjenis]') title='HAPUS' class='btn btn-danger btn-xs'>HAPUS</a>
											 
											</td>
										</tr>";
								$no++;
							}
							echo "</tbody></table>";
							?>
				</div>
			</div>


<?php

			break;

		case "tambah":

			echo "
		  <div class='box box-primary box-solid'>
				<div class='box-header with-border'>
					<h3 class='box-title'>TAMBAH JENIS OBAT</h3>
					<div class='box-tools pull-right'>
						<button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
                    </div><!-- /.box-tools -->
				</div>
				<div class='box-body'>
				
						<form method=POST action='$aksi?module=jenisobat&act=input_jenisobat' enctype='multipart/form-data' class='form-horizontal'>
						
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Jenis Obat & Rak Obat</label>        		
									 <div class='col-sm-6'>
										<input type=text name='jenisobat' class='form-control' required='required' autocomplete='off'>
									 </div>
							  </div>
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Deskripsi</label>        		
									 <div class='col-sm-6'>
										<input type=text name='ket' class='form-control' required='required' autocomplete='off'>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'></label>       
										<div class='col-sm-5'>
											<input class='btn btn-primary' type=submit value=SIMPAN>
											<input class='btn btn-danger' type=button value=BATAL onclick=self.history.back()>
										</div>
								</div>
								
							  </form>
							  
				</div> 
				
			</div>";


			break;

		case "edit":
			$stmt = $db->prepare("SELECT * FROM jenis_obat WHERE idjenis = ?");
			$stmt->execute([$_GET['id']]);
			$r = $stmt->fetch(PDO::FETCH_ASSOC);

			echo "
		  <div class='box box-primary box-solid'>
				<div class='box-header with-border'>
					<h3 class='box-title'>UBAH Jenis Obat</h3>
					<div class='box-tools pull-right'>
						<button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
                    </div><!-- /.box-tools -->
				</div>
				<div class='box-body'>
						<form method=POST action=$aksi?module=jenisobat&act=edit  enctype='multipart/form-data' class='form-horizontal'>
							  <input type=hidden name=idjenis value='$r[idjenis]'>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Jenis Obat</label>        		
									 <div class='col-sm-6'>
										<input type=text name='jenisobat' class='form-control' value='$r[jenisobat]' autocomplete='off'>
									 </div>
							  </div>
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Deskripsi</label>        		
									 <div class='col-sm-6'>
										<input type=text name='ket' class='form-control' value='$r[ket]' autocomplete='off'>
									 </div>
							  </div>
							  <div class='form-group'>
									<label class='col-sm-2 control-label'></label>       
										<div class='col-sm-5'>
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