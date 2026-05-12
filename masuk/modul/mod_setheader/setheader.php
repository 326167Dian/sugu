<?php
// session_start(); // Sudah aktif di media_admin.php
include '../../../configurasi/koneksi.php';
 if (empty($_SESSION['username']) AND empty($_SESSION['passuser'])){
  echo "<link href=../css/style.css rel=stylesheet type=text/css>";
  echo "<div class='error msg'>Untuk mengakses Modul anda harus login.</div>";
}
else{

$aksi="modul/mod_setheader/aksi_setheader.php";
$aksi_setheader = "masuk/modul/mod_setheader/aksi_setheader.php";
switch($_GET['act']){
  // Tampil Siswa
  default:

    
	$stmt = $db->prepare("SELECT * FROM setheader");
	$stmt->execute();
	$r = $stmt->fetch(PDO::FETCH_ASSOC);
			
		echo "
		  <div class='box box-primary box-solid'>
				<div class='box-header with-border'>
					<h3 class='box-title'>SETTING HEADER CETAK STRUK</h3>
					<div class='box-tools pull-right'>
						<button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
                    </div><!-- /.box-tools -->
				</div>
				<div class='box-body'>
						<form method=POST method=POST action=$aksi?module=setheader&act=update_setheader  enctype='multipart/form-data' class='form-horizontal'>
							  <input type=hidden name=id value='$r[id_setheader]'>
							  
							  <h3 class='box-title'>HEADER STRUK</h3>
							  <legend class='scheduler-border'></legend>
							  
							  <div class='form-group'>
									<label class='col-sm-3 control-label'>Baris 1 <br>(Nama Apotek)</label>        		
									 <div class='col-sm-6'>
										<input type=text name='satu' class='form-control' value='$r[satu]' autocomplete='off'>
									 </div>
							  </div>
							  <div class='form-group'>
									<label class='col-sm-3 control-label'>Baris 2 <br> (Alamat Jalan)</label>        		
									 <div class='col-sm-6'>
										<input type=text name='dua' class='form-control' value='$r[dua]' autocomplete='off'>
									 </div>
							  </div>
							  <div class='form-group'>
									<label class='col-sm-3 control-label'>Baris 3 <br>(nama kelurahan kecamatan kota)</label>        		
									 <div class='col-sm-6'>
										<input type=text name='tiga' class='form-control' value='$r[tiga]' autocomplete='off'>
									 </div>
							  </div>
							  <div class='form-group'>
									<label class='col-sm-3 control-label'>Baris 4 <br>(Nama Apoteker) </label>        		
									 <div class='col-sm-6'>
										<input type=text name='empat' class='form-control' value='$r[empat]' autocomplete='off'>
									 </div>
							  </div>
							  <div class='form-group'>
									<label class='col-sm-3 control-label'>Baris 5 <br>( No SIA )</label>        		
									 <div class='col-sm-6'>
										<input type=text name='lima' class='form-control' value='$r[lima]' autocomplete='off'>
									 </div>
							  </div>
							  <div class='form-group'>
									<label class='col-sm-3 control-label'>Baris 6 <br>(No. Telp)</label>        		
									 <div class='col-sm-6'>
										<input type=text name='enam' class='form-control' value='$r[enam]' autocomplete='off'>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-3 control-label'>Baris 7<br> (No SIPA)</label>        		
									 <div class='col-sm-6'>
										<input type=text name='tujuh' class='form-control' value='$r[tujuh]' autocomplete='off'>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-3 control-label'>Alamat Apoteker</label>        		
									 <div class='col-sm-6'>
										<input type=text name='duabelas' class='form-control' value='$r[duabelas]' autocomplete='off'>
									 </div>
							  </div>

							  <div class='form-group'>
									<label class='col-sm-3 control-label'>Kota</label>        		
									 <div class='col-sm-6'>
										<input type=text name='tigabelas' class='form-control' value='$r[tigabelas]' autocomplete='off'>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-3 control-label'>Minimum Exp. Date (Hari)</label>        		
									 <div class='col-sm-6'>
										<input type='number' name='empatbelas' class='form-control' value='$r[empatbelas]' autocomplete='off'>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-3 control-label'>Logo Header</label>        		
									 <div class='col-sm-6'>
										<input type='file' name='fupload1' class='form-control' value='$r[logo]' autocomplete='off'>
									 </div> <br>
                                    	
							  </div>
							  <div class='form-group'>
									<label class='col-sm-3 control-label'>Tanda Tangan</label>        		
									 <div class='col-sm-6'>
										<input type='file' name='fupload2' class='form-control' value='$r[tandatangan]' autocomplete='off'>
									 </div> <br>
							  </div>

							  
							  ";
																			if (!empty($r['logo'])) {
																				echo "
																						<img src='images/$r[logo]'
																								 style='display:block; margin:10px auto; max-width:100px; border:1px solid #ccc;'
																								 alt='Logo Header'>
																				";
																		 }

																			if (!empty($r['tandatangan'])) {
																				echo "
																						<img src='images/$r[tandatangan]'
																								 style='display:block; margin:10px auto; max-width:100px; border:1px solid #ccc;'
																								 alt='Tanda Tangan'>
																				";
																		 }


							    echo"
							  <h3 class='box-title'>FOOTER STRUK</h3>
							  <legend class='scheduler-border'></legend>
							  
							   <div class='form-group'>
									<label class='col-sm-2 control-label'>Baris 1</label>        		
									 <div class='col-sm-6'>
										<input type=text name='delapan' class='form-control' value='$r[delapan]' autocomplete='off'>
									 </div>
							  </div>
							  
							   <div class='form-group'>
									<label class='col-sm-2 control-label'>Baris 2</label>        		
									 <div class='col-sm-6'>
										<input type=text name='sembilan' class='form-control' value='$r[sembilan]' autocomplete='off'>
									 </div>
							  </div>
							  
							   <div class='form-group'>
									<label class='col-sm-2 control-label'>Baris 3</label>        		
									 <div class='col-sm-6'>
										<input type=text name='sepuluh' class='form-control' value='$r[sepuluh]' autocomplete='off'>
									 </div>
							  </div>
							  
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Baris 4</label>        		
									 <div class='col-sm-6'>
										<input type=text name='sebelas' class='form-control' value='$r[sebelas]' autocomplete='off'>
										<p>
										<div class='col-sm-6'>
											<input class='btn btn-primary' type=submit value=SIMPAN>
											<input class='btn btn-danger' type=button value=BATAL onclick=self.history.back()>
										</div>
									 </div>
							  </div>
								
							  </form>
							  
				</div> 
				
			</div>";	

 
    
    break;


}
}
?>