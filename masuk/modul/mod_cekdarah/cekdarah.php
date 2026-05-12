<?php
session_start();
include "../../../configurasi/koneksi.php";
 if (empty($_SESSION['username']) AND empty($_SESSION['passuser'])){
  echo "<link href=../css/style.css rel=stylesheet type=text/css>";
  echo "<div class='error msg'>Untuk mengakses Modul anda harus login.</div>";
}
else{

$aksi="modul/mod_cekdarah/aksi_cekdarah.php";

switch($_GET['act']){
  // tampil satuan
  default:

  
    //   $tampil_cekdarah = $db->query("SELECT * FROM cekdarah ORDER BY id_cekdarah");

	  ?>
			
			
			<div class="box box-primary box-solid table-responsive">
				<div class="box-header with-border">
					<h3 class="box-title">DAFTAR CEK DARAH -> <a href="https://youtu.be/0auOW5diQIg" target="_blank">TONTON VIDEO</a></h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div><!-- /.box-tools -->
				</div>
				<div class="box-body">
					<a  class ='btn  btn-success btn-flat' href='?module=cekdarah&act=tambah'>Mulai Cek Darah</a>

					<br><br>
					
					
					<table id="cek" class="table table-bordered table-striped" >
						<thead>
							<tr>
								<th>No</th>
								<th>Nama Pasien</th>
                                <th>Petugas</th>
                                <th>Glukosa</th>
                                <th>Asam Urat</th>
                                <th>Asam Kolesterol</th>
                                <th>Tensi</th>
                                <th>Waktu</th>
								<th width="70">Aksi</th>
							</tr>
						</thead>
					</table>
				</div>
			</div>	
             

<?php
    
    break;
	
	case "tambah":
        $petugas = $_SESSION['namalengkap'];
        $tglharini = date('Y-m-d H-i-s');
        echo "
		  <div class='box box-primary box-solid table-responsive'>
				<div class='box-header with-border'>
					<h3 class='box-title'>INPUT HASIL CEK</h3>
					<div class='box-tools pull-right'>
						<button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
                    </div><!-- /.box-tools -->
				</div>
				<div class='box-body'>
				
						<form method=POST action='$aksi?module=cekdarah&act=input_cekdarah' enctype='multipart/form-data' class='form-horizontal'>
						<input type=hidden name='petugas' value='$petugas'>
						<input type=hidden name='waktu' value='$tglharini'>
						
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Nama Pasien</label>        		
									 <div class='col-sm-8'>
										<select name='id_pelanggan' class='form-control' >";
                                        $tampil = $db->query("SELECT * FROM pelanggan ORDER BY nm_pelanggan ASC");
                                        while ($rk = $tampil->fetch(PDO::FETCH_ASSOC)) {
                                            echo "<option value=$rk[id_pelanggan]>$rk[nm_pelanggan]</option>";
                                        }
                                        echo "</select>
									 </div>
							  </div>
							  
							  </div><div class='form-group'>
									<label class='col-sm-2 control-label'>Glukosa</label>        		
									 <div class='col-sm-10'>
										<input type=text name='gula' class='form-control' required='required' autocomplete='off'>
									 </div>
							  </div>							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Asam Urat</label>        		
									 <div class='col-sm-10'>
										<input type=text name='asamurat' class='form-control' required='required' autocomplete='off'>
									 </div>
							  </div>
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Kolesterol</label>        		
									 <div class='col-sm-10'>
										<input type=text name='kolesterol' class='form-control' required='required' autocomplete='off'>
									 </div>
							  </div>
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Tensi</label>        		
									 <div class='col-sm-10'>
										<input type=text name='tensi' class='form-control' required='required' autocomplete='off'>
									 </div>
								
									 <div><p style='text-align: center;'>
											<input class='btn btn-primary' type=submit value=SIMPAN>
											<input class='btn btn-danger' type=button value=BATAL onclick=self.history.back()>
											</p>
										</div>
							  </div>
								
							  </form>
							  
				</div> 
				
			</div>";
					
	
    break;

  case "edit":
    $editan = $db->prepare("SELECT * FROM cekdarah WHERE id_cekdarah = ?");
    $editan->execute([$_GET['id']]);
    $r = $editan->fetch(PDO::FETCH_ASSOC);
    $pelanggan = $db->prepare("SELECT * FROM pelanggan WHERE id_pelanggan = ?");
    $pelanggan->execute([$r['id_pelanggan']]);
    $pasien = $pelanggan->fetch(PDO::FETCH_ASSOC);
			
		echo "
		  <div class='box box-primary box-solid table-responsive'>
				<div class='box-header with-border'>
					<h3 class='box-title'>KOREKSI CEK DARAH</h3>
					<div class='box-tools pull-right'>
						<button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
                    </div><!-- /.box-tools -->
				</div>
				<div class='box-body'>
						<form method=POST method=POST action=$aksi?module=cekdarah&act=update_cekdarah  enctype='multipart/form-data' class='form-horizontal'>
							  <input type=hidden name=id value='$r[id_cekdarah]'>
							  
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Nama Pasien</label>        		
									 <div class='col-sm-6'>
										<input type=text name='id_pelanggan' class='form-control' value='$pasien[nm_pelanggan]' autocomplete='off' disabled>
									 </div>
							  </div>							 
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Glukosa</label>        		
									 <div class='col-sm-6'>
										<input type=text name='gula' class='form-control' value='$r[gula]' autocomplete='off'>
									 </div>
							  </div>
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Asam Urat</label>        		
									 <div class='col-sm-6'>
										<input type=text name='asamurat' class='form-control' value='$r[asamurat]' autocomplete='off'>
									 </div>
							  </div>
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Kolesterol</label>        		
									 <div class='col-sm-6'>
										<input type=text name='kolesterol' class='form-control' value='$r[kolesterol]' autocomplete='off'>
									 </div>
							  </div>
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Tensi</label>        		
									 <div class='col-sm-6'>
										<input type=text name='tensi' class='form-control' value='$r[tensi]' autocomplete='off'>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'></label>       
										<div class='col-sm-5'>
											<input class='btn btn-primary' type=submit value=SIMPAN>
											<input class='btn btn-danger' type=button value=KEMBALI onclick=self.history.back()>
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

<script>
    $(document).ready(function() {
        $("#cek").DataTable({
            serverSide: true,
            ajax: {
                "url": "modul/mod_cekdarah/cek_serverside.php?action=table_data",
                "dataType": "JSON",
                "type": "POST"
            },
            columns: [
                { "data": "no", "className": "text-center" },
                { "data": "nm_pelanggan", "className": "text-left" },
                { "data": "petugas", "className": "text-left" },
                { "data": "gula", "className": "text-right" },
                { "data": "asamurat", "className": "text-right" },
                { "data": "kolesterol", "className": "text-right" },
                { "data": "tensi", "className": "text-center" },
                { "data": "waktu", "className": "text-center" },
                { "data": "aksi", "className": "text-center" }
            ]
        });
    });
</script>