
<?php
session_start();
 if (empty($_SESSION['username']) AND empty($_SESSION['passuser'])){
  echo "<link href=../css/style.css rel=stylesheet type=text/css>";
  echo "<div class='error msg'>Untuk mengakses Modul anda harus login.</div>";
}
else{

$aksi="modul/mod_profil/aksi_profil.php";
switch($_GET['act']){
  // Tampil User
  default:
      $tampil_profil = $db->query("SELECT * FROM admin ORDER BY username");
	  ?>
	   <div class="box box-primary box-solid">
				<div class="box-header with-border">
					<h3 class="box-title">Pengguna</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div><!-- /.box-tools -->
				</div>
				<div class="box-body">
					<a  class ='btn  btn-success btn-flat' href='?module=profil&act=tambahprofil'>Tambah</a>
					<br><br>
					<table id="example1" class="table table-bordered table-striped" >
						<thead>
							<tr>
								<th>No</th>
								<th>Username</th>
								<th>Nama</th>
								<th>Telp/HP</th>
								<th>Level</th>
								<th>Blokir</th>
								<th>Aksi</th>
							</tr>
						</thead>
						<tbody>
						<?php 
							$no=1;
							while ($r = $tampil_profil->fetch(PDO::FETCH_ASSOC)){
							   echo "<tr><td>$no</td>
									 <td>$r[username]</td>
									 <td>$r[nama_lengkap]</td>
									 <td>$r[no_telp]</td>
									 <td>$r[level]</td>  
									 <td>$r[blokir]</td>
									 <td>";
								 if($r['id_admin'] == "1"){
								 echo"<a href='?module=profil&act=editprofil&id=$r[id_admin]' title='Edit' class='btn btn-warning btn-xs'>EDIT</a>
								 ";
								 }else{
								 echo"
								 <a href='?module=profil&act=editprofil&id=$r[id_admin]' title='Edit' class='btn btn-warning btn-xs'>EDIT</a>
								 <a href=javascript:confirmdelete('$aksi?module=profil&act=hapus&id=$r[id_admin]') title='Hapus' class='btn btn-danger btn-xs'>HAPUS</a>
									 ";
									 }
									 echo"
									 </td></tr>";
							  $no++;
							}
						echo "</tbody></table>";
					?>
				</div>
			</div>	
		
	  
	  <?php
    break;

  case "editprofil":
    $id_admin = isset($_GET['id']) ? $_GET['id'] : $_SESSION['id_admin'];
      
    $edit = $db->prepare("SELECT * FROM admin WHERE id_admin=?");
    $edit->execute([$id_admin]);
    $r = $edit->fetch(PDO::FETCH_ASSOC);

    echo "
		  <div class='box box-primary box-solid'>
				<div class='box-header with-border'>
					<h3 class='box-title'>Ubah Data Pengguna</h3>
					<div class='box-tools pull-right'>
						<button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
                    </div><!-- /.box-tools -->
				</div>
				<div class='box-body'>
						<form method=POST action='$aksi?module=profil&act=update_profil' class='form-horizontal'>
							  <input type=hidden name=id value='$r[id_admin]'>
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Username</label>        		
									 <div class='col-sm-4'>
										<input type=text name='username' class='form-control' Placeholder='Username' value='$r[username]'>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Password</label>        		
									 <div class='col-sm-4'>
										<input type=text name='password' class='form-control' Placeholder='Password' >
										<small>Apabila password tidak diubah, dikosongkan saja.</small>
									</div>
							  </div>
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Nama Lengkap</label>        		
									 <div class='col-sm-6'>
										<input type=text name='nama_lengkap' class='form-control' Placeholder='nama_lengkap' value='$r[nama_lengkap]'>
									 </div>
							  </div>
							  
							   <div class='form-group'>
									<label class='col-sm-2 control-label'>Telp/HP</label>        		
									 <div class='col-sm-4'>
										<input type=text name='no_telp' class='form-control' Placeholder='No Telepon' value='$r[no_telp]'>
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
