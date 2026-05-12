<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// session_start(); // Sudah aktif di media_admin.php
 if (empty($_SESSION['username']) AND empty($_SESSION['passuser'])){
  echo "<link href=../css/style.css rel=stylesheet type=text/css>";
  echo "<div class='error msg'>Untuk mengakses Modul anda harus login.</div>";
}
else{

$aksi="modul/mod_satuan/aksi_satuan.php";

switch(isset($_GET['act']) ? $_GET['act'] : ''){
  // tampil satuan
  default:

  
      $stmt = $db->query("SELECT * FROM satuan ORDER BY id_satuan ");
      $tampil_satuan = $stmt->fetchAll(PDO::FETCH_ASSOC);
      
	  ?>
			
			
			<div class="box box-primary box-solid">
				<div class="box-header with-border">
					<h3 class="box-title">DATA SATUAN</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div><!-- /.box-tools -->
				</div>
				<div class="box-body">
					<a  class ='btn  btn-success btn-flat' href='?module=satuan&act=tambah'>TAMBAH</a>
					<br><br>
					
					
					<table id="example1" class="table table-bordered table-striped" >
						<thead>
							<tr>
								<th>No</th>
								<th>Satuan</th>
                                <th>Deskripsi</th>
								<th width="70">Aksi</th>
							</tr>
						</thead>
						<tbody>
						<?php 
								$no=1;
								foreach ($tampil_satuan as $r){
									echo "<tr class='warnabaris' >
											<td>$no</td>           
											 <td>$r[nm_satuan]</td>
											 <td>$r[deskripsi]</td>
											 <td><a href='?module=satuan&act=edit&id=$r[id_satuan]' title='EDIT' class='btn btn-warning btn-xs'>EDIT</a> 
											 <a href=javascript:confirmdelete('$aksi?module=satuan&act=hapus&id=$r[id_satuan]') title='HAPUS' class='btn btn-danger btn-xs'>HAPUS</a>
											 
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
					<h3 class='box-title'>TAMBAH SATUAN</h3>
					<div class='box-tools pull-right'>
						<button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
                    </div><!-- /.box-tools -->
				</div>
				<div class='box-body'>
				
						<form method=POST action='$aksi?module=satuan&act=input_satuan' enctype='multipart/form-data' class='form-horizontal'>
						
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Satuan</label>        		
									 <div class='col-sm-6'>
										<input type=text name='nm_satuan' class='form-control' required='required' autocomplete='off'>
									 </div>
							  </div>
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Deskripsi</label>        		
									 <div class='col-sm-6'>
										<input type=text name='deskripsi' class='form-control' required='required' autocomplete='off'>
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
    $stmt = $db->prepare("SELECT * FROM satuan WHERE id_satuan = ?");
    $stmt->execute([$_GET['id']]);
    $r = $stmt->fetch(PDO::FETCH_ASSOC);
			
		echo "
		  <div class='box box-primary box-solid'>
				<div class='box-header with-border'>
					<h3 class='box-title'>UBAH SATUAN</h3>
					<div class='box-tools pull-right'>
						<button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
                    </div><!-- /.box-tools -->
				</div>
				<div class='box-body'>
						<form method=POST method=POST action=$aksi?module=satuan&act=update_satuan  enctype='multipart/form-data' class='form-horizontal'>
							  <input type=hidden name=id value='$r[id_satuan]'>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Satuan</label>        		
									 <div class='col-sm-6'>
										<input type=text name='nm_satuan' class='form-control' value='$r[nm_satuan]' autocomplete='off'>
									 </div>
							  </div>
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Deskripsi</label>        		
									 <div class='col-sm-6'>
										<input type=text name='deskripsi' class='form-control' value='$r[deskripsi]' autocomplete='off'>
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
 $(function(){
  $(".datepicker").datepicker({
      format: 'yyyy-mm-dd',
      autoclose: true,
      todayHighlight: true,
  });
 });
</script>