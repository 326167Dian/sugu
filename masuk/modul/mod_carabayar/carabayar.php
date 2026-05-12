<?php
session_start();
if (empty($_SESSION['username']) and empty($_SESSION['passuser'])) {
	echo "<link href=../css/style.css rel=stylesheet type=text/css>";
	echo "<div class='error msg'>Untuk mengakses Modul anda harus login.</div>";
} else {

	$aksi = "modul/mod_carabayar/aksi_carabayar.php";
	$aksi_carabayar = "masuk/modul/mod_carabayar/aksi_carabayar.php";
	switch ($_GET['act']) {
			// Tampil Siswa
		default:


			$tampil_carabayar = $db->query("SELECT * FROM carabayar ORDER BY id_carabayar ");

?>


			<div class="box box-primary box-solid">
				<div class="box-header with-border">
					<h3 class="box-title">JENIS PEMBAYARAN KASIR</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div><!-- /.box-tools -->
				</div>
				<div class="box-body">
					<a class='btn  btn-success btn-flat' href='?module=carabayar&act=tambah'>TAMBAH</a>
					<br><br>


					<table id="example1" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>No</th>
								<th>Jenis</th>
								<th width="70">Aksi</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$no = 1;
							while ($r = $tampil_carabayar->fetch(PDO::FETCH_ASSOC)) {
								echo "<tr class='warnabaris' >
											<td>$no</td>           
											 <td>$r[nm_carabayar]</td>
											 <td><a href='?module=carabayar&act=edit&id=$r[id_carabayar]' title='EDIT' class='btn btn-warning btn-xs'>EDIT</a> 
											 <a href=javascript:confirmdelete('$aksi?module=carabayar&act=hapus&id=$r[id_carabayar]') title='HAPUS' class='btn btn-danger btn-xs'>HAPUS</a>
											 
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
					<h3 class='box-title'>TAMBAH</h3>
					<div class='box-tools pull-right'>
						<button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
                    </div><!-- /.box-tools -->
				</div>
				<div class='box-body'>
				
						<form method=POST action='$aksi?module=carabayar&act=input_carabayar' enctype='multipart/form-data' class='form-horizontal'>
						
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Jenis</label>        		
									 <div class='col-sm-6'>
										<input type=text name='nm_carabayar' class='form-control' required='required' autocomplete='off'>
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
			$edit = $db->prepare("SELECT * FROM carabayar WHERE id_carabayar=?");
			$edit->execute([$_GET['id']]);
			$r = $edit->fetch(PDO::FETCH_ASSOC);

			echo "
		  <div class='box box-danger box-solid'>
				<div class='box-header with-border'>
					<h3 class='box-title'>UBAH</h3>
					<div class='box-tools pull-right'>
						<button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
                    </div><!-- /.box-tools -->
				</div>
				<div class='box-body'>
						<form method=POST method=POST action=$aksi?module=carabayar&act=update_carabayar  enctype='multipart/form-data' class='form-horizontal'>
							  <input type=hidden name=id value='$r[id_carabayar]'>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Jenis</label>        		
									 <div class='col-sm-6'>
										<input type=text name='nm_carabayar' class='form-control' value='$r[nm_carabayar]' autocomplete='off'>
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