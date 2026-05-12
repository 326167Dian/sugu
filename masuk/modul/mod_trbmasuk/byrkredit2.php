<?php
session_start();
 if (empty($_SESSION['username']) AND empty($_SESSION['passuser'])){
  echo "<link href=../css/style.css rel=stylesheet type=text/css>";
  echo "<div class='error msg'>Untuk mengakses Modul anda harus login.</div>";
}
else{

$aksi="modul/mod_trbmasuk/aksi_trbmasuk.php";
$aksi_trbmasuk = "masuk/modul/mod_trbmasuk/aksi_trbmasuk.php";
switch($_GET[act]){
  // Tampil barang
  default:

  
      $tampil_trbmasuk = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM trbmasuk 
	  WHERE id_resto = 'pusat'
	  ORDER BY trbmasuk.id_trbmasuk DESC");


	  ?>
			
			
			<div class="box box-primary box-solid">
				<div class="box-header with-border">
					<h3 class="box-title">TRANSAKSI BARANG MASUK</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div><!-- /.box-tools -->
				</div>
				<div class="box-body table-responsive">
					<a  class ='btn  btn-success btn-flat' href='?module=trbmasuk&act=tambah'>TAMBAH</a><div></div><p><p>
					<a  class ='btn  btn-warning  btn-flat' href='#'></a>
					<small>* Pembayaran belum lunas</small>
					<br><br>
					
					
					<table id="example1" class="table table-bordered table-striped" >
						<thead>
							<tr>
								<th>No</th>
								<th>Kode</th>
								<th>Petugas</th>
								<th>Tanggal</th>
								<th>Supplier</th>
								<th>No Faktur</th>
								<th>Total Tagihan</th>
								<th>Status Pembayaran</th>
								<th width="70">Aksi</th>
							</tr>
						</thead>
						<tbody>
						<?php 
								$no=1;
								while ($r=mysqli_fetch_array($tampil_trbmasuk)){
								$ttl_trbmasuknya = format_rupiah($r['ttl_trbmasuk']);
								$dp_bayar = format_rupiah($r['dp_bayar']);
								$sisa_bayar = format_rupiah($r['sisa_bayar']);

                                $text = "";
                                if($r['jatuhtempo']==''){
                                    $text = 'ubah';
                                } else {
                                    $text = 'ubahpbf';
                                }
									echo "<tr class='warnabaris' >";
									
									if($r['carabayar'] == "LUNAS"){
											echo"
												<td>$no</td>           
												<td>$r[kd_trbmasuk]</td>
											";
										}else{
										
											echo"
												<td style='background-color:#ffbf00;'>$no</td>           
												<td style='background-color:#ffbf00;'>$r[kd_trbmasuk]</td>
											";
										
										}
										echo"               
											 <td>$r[petugas]</td>											
											 <td>$r[tgl_trbmasuk]</td>											
											 <td>$r[nm_supplier]</td>
											 <td>$r[ket_trbmasuk]</td>											
											<td align=right>$sisa_bayar</td>											 
											<td align=center>$r[carabayar]</td>											 
											 <td><a href='?module=byrkredit&act=$text&id=$r[id_trbmasuk]' title='EDIT' class='btn btn-warning btn-xs'>EDIT</a> 
											 <a href=javascript:confirmdelete('$aksi?module=trbmasuk&act=hapus&id=$r[id_trbmasuk]') title='HAPUS' class='btn btn-danger btn-xs'>HAPUS</a>
											 
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
	//cek apakah ada kode transaksi ON berdasarkan user
	$cekkd=mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM kdbm WHERE id_admin='$_SESSION[idadmin]' AND id_resto='pusat' AND stt_kdbm='ON'");
	$ketemucekkd=mysqli_num_rows($cekkd);
	$hcekkd=mysqli_fetch_array($cekkd);
    $petugas= $_SESSION['namalengkap'];

	if ($ketemucekkd > 0){
	$kdtransaksi = $hcekkd['kd_trbmasuk'];
	}else{
	$kdunik = date('dmyhis');
	$kdtransaksi = "BMP-".$kdunik;
	mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO kdbm(kd_trbmasuk,id_resto,id_admin) VALUES('$kdtransaksi','pusat','$_SESSION[idadmin]')");
	}
	
	$tglharini = date('Y-m-d');
       
        echo "
		  <div class='box box-primary box-solid'>
				<div class='box-header with-border'>
					<h3 class='box-title'>TAMBAH TRANSAKSI BARANG MASUK</h3>
					<div class='box-tools pull-right'>
						<button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
                    </div><!-- /.box-tools -->
				</div>
				<div class='box-body table-responsive'>
				
						<form onsubmit='return false;' method=POST action='$aksi?module=trbmasuk&act=input_trbmasuk' enctype='multipart/form-data' class='form-horizontal'>
						
						        <input type=hidden name='id_trbmasuk' id='id_trbmasuk' value='$re[id_trbmasuk]'>
							   <input type=hidden name='kd_trbmasuk' id='kd_trbmasuk' value='$kdtransaksi'>
							   <input type=hidden name='stt_aksi' id='stt_aksi' value='input_trbmasuk'>
							    <input type=hidden name='id_supplier' id='id_supplier'>
							    <input type=hidden name='petugas' id='petugas' value='$petugas'>
							 
						<div class='col-lg-6'>

							  <div class='form-group'>
							  
									<label class='col-sm-4 control-label'>Tanggal</label>
										<div class='col-sm-6'>
											<div class='input-group date'>
												<div class='input-group-addon'>
													<span class='glyphicon glyphicon-th'></span>
												</div>
													<input type='text' class='datepicker' name='tgl_trbmasuk' id='tgl_trbmasuk' required='required' value='$tglharini' autocomplete='off'>
											</div>
										</div>
										
									<label class='col-sm-4 control-label'>Kode Transaksi</label>        		
										<div class='col-sm-6'>
											<input type=text name='kd_hid' id='kd_hid' class='form-control' required='required' value='$kdtransaksi' autocomplete='off' Disabled>
										</div>
										
									<label class='col-sm-4 control-label'>Supplier</label>        		
										<div class='col-sm-6'>
											<div class='input-group'>
												<input type='text' class='form-control' name='nm_supplier' id='nm_supplier' required='required' autocomplete='off' Disabled>
													<div class='input-group-addon'>
														<button type=button data-toggle='modal' data-target='#ModalSupplier' href='#'><span class='glyphicon glyphicon-search'></span></button>
													</div>
											</div>
										</div>
									
									<label class='col-sm-4 control-label'>Telepon</label>        		
										<div class='col-sm-6'>
											<input type=text name='tlp_supplier' id='tlp_supplier' class='form-control' autocomplete='off'>
										</div>
										
									<label class='col-sm-4 control-label'>Alamat</label>        		
										<div class='col-sm-6'>
											<textarea name='alamat_supplier' id='alamat_supplier' class='form-control' rows='2'></textarea>
										</div>
							
                            
									<label class='col-sm-4 control-label'>No Faktur</label>        		
										<div class='col-sm-6'>
											<textarea name='ket_trbmasuk' id='ket_trbmasuk' class='form-control' rows='2'>  </textarea>
											</p>
											<div class='buttons'>
												<button type='button' class='btn btn-primary right-block' onclick='simpan_transaksi();'>SIMPAN TRANSAKSI</button>
												&nbsp&nbsp&nbsp
												<input class='btn btn-danger' type='button' value=KEMBALI onclick=self.history.back()>
												</div>
							  
										</div>
										
							  </div>
							  
						</div>
						
						<div class='col-lg-6'>
						
						
								<input type=hidden name='id_barang' id='id_barang'>
								<input type=hidden name='stok_barang' id='stok_barang'>
								
								<div class='form-group'>
								
									
									<label class='col-sm-4 control-label'>Kode Barang</label>        		
										<div class='col-sm-7'>
											<div class='input-group'>
												<input type='text' class='form-control' name='kd_barang' id='kd_barang' autocomplete='off'>
													<div class='input-group-addon'>
														<button type=button data-toggle='modal' data-target='#ModalItem' href='#'><span class='glyphicon glyphicon-search'></span></button>
													</div>
											</div>
										</div>
									
									<label class='col-sm-4 control-label'>Nama Barang</label>        		
										<div class='col-sm-7'>
											<input type=text name='nmbrg_dtrbmasuk' id='nmbrg_dtrbmasuk' class='typeahead form-control' autocomplete='off'>
										</div>
										
									<label class='col-sm-4 control-label'>Qty</label>        		
										<div class='col-sm-7'>
											<input type='number' name='qty_dtrbmasuk' id='qty_dtrbmasuk' class='form-control' autocomplete='off'>
										</div>
										
									<label class='col-sm-4 control-label'>Satuan</label>        		
										<div class='col-sm-7'>
											<input type=text name='sat_dtrbmasuk' id='sat_dtrbmasuk' class='form-control' autocomplete='off'>
										</div>
									
									<label class='col-sm-4 control-label'>Harga Beli</label>        		
										<div class='col-sm-7'>
											<input type=text name='hrgsat_dtrbmasuk' id='hrgsat_dtrbmasuk' class='form-control' autocomplete='off'>
										</div>	
										
									<label class='col-sm-4 control-label'>Harga Jual</label>        		
										<div class='col-sm-7'>
											<input type=text name='hrgjual_dtrbmasuk' id='hrgjual_dtrbmasuk' class='form-control' autocomplete='off'>
											</p>
												<div class='buttons'>
													<button type='button' class='btn btn-success right-block' onclick='simpan_detail();'>SIMPAN DETAIL</button>
												</div>
										</div>
										
										
								</div>
								
									
						</div>
						</form>
							  
				</div> 
				
				<div id='tabeldata'>
				
			</div>";
					
	
    break;
	
	case "ubah":
	//cek apakah ada kode transaksi ON berdasarkan user
	$ubah=mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM trbmasuk 
	WHERE trbmasuk.id_trbmasuk='$_GET[id]'");
	$re=mysqli_fetch_array($ubah);
       
        echo "
		  <div class='box box-primary box-solid'>
				<div class='box-header with-border'>
					<h3 class='box-title'>UBAH TRANSAKSI BARANG MASUK</h3>
					<div class='box-tools pull-right'>
						<button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
                    </div><!-- /.box-tools -->
				</div>
				<div class='box-body table-responsive'>
				
						<form onsubmit='return false;' method=POST action='$aksi?module=trbmasuk&act=ubah_trbmasuk' enctype='multipart/form-data' class='form-horizontal'>
						
						       <input type=hidden name='id_trbmasuk' id='id_trbmasuk' value='$re[id_trbmasuk]'>
							   <input type=hidden name='kd_trbmasuk' id='kd_trbmasuk' value='$re[kd_trbmasuk]'>
							   <input type=hidden name='stt_aksi' id='stt_aksi' value='ubah_trbmasuk'>
							   <input type=hidden name='id_supplier' id='id_supplier' value='$re[id_supplier]'>
							   <input type=hidden name='petugas' id='petugas' value='$petugas'>
							 
						<div class='col-lg-6'>
						
							<div class='form-group'>
							  
								<label class='col-sm-4 control-label'>Tanggal</label>
										<div class='col-sm-6'>
											<div class='input-group date'>
												<div class='input-group-addon'>
													<span class='glyphicon glyphicon-th'></span>
												</div>
													<input type='text' class='datepicker' name='tgl_trbmasuk' id='tgl_trbmasuk' required='required' value='$re[tgl_trbmasuk]' autocomplete='off'>
											</div>
										</div>
										
									<label class='col-sm-4 control-label'>Kode Transaksi</label>        		
										<div class='col-sm-6'>
											<input type=text name='kd_hid' id='kd_hid' class='form-control' required='required' value='$re[kd_trbmasuk]' autocomplete='off' Disabled>
										</div>
										
									<label class='col-sm-4 control-label'>Supplier</label>        		
										<div class='col-sm-6'>
											<div class='input-group'>
												<input type='text' class='form-control' name='nm_supplier' id='nm_supplier' required='required' value='$re[nm_supplier]' autocomplete='off' Disabled>
													<div class='input-group-addon'>
														<button type=button data-toggle='modal' data-target='#ModalSupplier' href='#'><span class='glyphicon glyphicon-search'></span></button>
													</div>
											</div>
										</div>
									
									<label class='col-sm-4 control-label'>Telepon</label>        		
										<div class='col-sm-6'>
											<input type=text name='tlp_supplier' id='tlp_supplier' class='form-control' value='$re[tlp_supplier]' autocomplete='off'>
										</div>
										
									<label class='col-sm-4 control-label'>Alamat</label>        		
										<div class='col-sm-6'>
											<textarea name='alamat_supplier' id='alamat_supplier' class='form-control' rows='2'>$re[alamat_trbmasuk]</textarea>
										</div>
										
									<label class='col-sm-4 control-label'>No Faktur</label>        		
										<div class='col-sm-6'>
											<textarea name='ket_trbmasuk' id='ket_trbmasuk' class='form-control' rows='2'>$re[ket_trbmasuk]</textarea>
											</p>
											<div class='buttons'>
											  <button type='button' class='btn btn-primary right-block' onclick='simpan_transaksi();'>SIMPAN TRANSAKSI</button>
												&nbsp&nbsp&nbsp
												<input class='btn btn-danger' type='button' value=BATAL onclick=self.history.back()>
											</div>
								  
										</div>
							  
							</div>  
							  
						</div>
						
						<div class='col-lg-6'>
						
						
								<input type=hidden name='id_barang' id='id_barang'>
								<input type=hidden name='stok_barang' id='stok_barang'>
								
								<div class='form-group'>
								
									<label class='col-sm-4 control-label'>Kode Barang</label>        		
										<div class='col-sm-7'>
											<div class='input-group'>
												<input type='text' class='form-control' name='kd_barang' id='kd_barang' autocomplete='off'>
													<div class='input-group-addon'>
														<button type=button data-toggle='modal' data-target='#ModalItem' href='#'><span class='glyphicon glyphicon-search'></span></button>
													</div>
											</div>
										</div>
									
									<label class='col-sm-4 control-label'>Nama Barang</label>        		
										<div class='col-sm-7'>
											<input type=text name='nmbrg_dtrbmasuk' id='nmbrg_dtrbmasuk' class='typeahead form-control' autocomplete='off'>
										</div>
										
									<label class='col-sm-4 control-label'>Qty</label>        		
										<div class='col-sm-7'>
											<input type='number' name='qty_dtrbmasuk' id='qty_dtrbmasuk' class='form-control' autocomplete='off'>
										</div>
										
									<label class='col-sm-4 control-label'>Satuan</label>        		
										<div class='col-sm-7'>
											<input type=text name='sat_dtrbmasuk' id='sat_dtrbmasuk' class='form-control' autocomplete='off'>
										</div>
										
									<label class='col-sm-4 control-label'>Harga Beli</label>        		
										<div class='col-sm-7'>
											<input type=text name='hrgsat_dtrbmasuk' id='hrgsat_dtrbmasuk' class='form-control' autocomplete='off'>
										</div>	
										
									<label class='col-sm-4 control-label'>Harga Jual</label>        		
										<div class='col-sm-7'>
											<input type=text name='hrgjual_dtrbmasuk' id='hrgjual_dtrbmasuk' class='form-control' autocomplete='off'>
											</p>
												<div class='buttons'>
													<button type='button' class='btn btn-success right-block' onclick='simpan_detail();'>SIMPAN DETAIL</button>
												</div>
										</div>
									
										
								</div>
								
								
								
								
						
						</div>
						</form>
							  
				</div> 
				
				<div id='tabeldata'>
				
			</div>";
					
	
    break;

    case "ubahpbf":
        $ubah=mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM trbmasuk 
    	WHERE trbmasuk.id_trbmasuk='$_GET[id]'");
    	$re=mysqli_fetch_array($ubah);
    	
    	echo "
		  <div class='box box-primary box-solid table-responsive'>
				<div class='box-header with-border'>
					<h3 class='box-title'>TAMBAH TRANSAKSI BARANG MASUK DARI PBF</h3>
					<div class='box-tools pull-right'>
						<button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
                    </div><!-- /.box-tools -->
				</div>
				<div class='box-body table-responsive'>
				
						<form onsubmit='return false;' method=POST action='$aksi?module=trbmasukpbf&act=ubah_trbmasuk' enctype='multipart/form-data' class='form-horizontal'>
						
						       <input type=hidden name='id_trbmasuk' id='id_trbmasuk' value='$re[id_trbmasuk]'>
							   <input type=hidden name='kd_trbmasuk' id='kd_trbmasuk' value='$re[kd_trbmasuk]'>
							   <input type=hidden name='stt_aksi' id='stt_aksi' value='ubah_trbmasuk'>
							   <input type=hidden name='id_supplier' id='id_supplier' value='$re[id_supplier]'>
							   <input type=hidden name='petugas' id='petugas' value='$petugas'>
							 
						<div class='col-lg-6'>

							  <div class='form-group'>
							  
									<label class='col-sm-4 control-label'>Tanggal</label>
										<div class='col-sm-6'>
											<div class='input-group date'>
												<div class='input-group-addon'>
													<span class='glyphicon glyphicon-th'></span>
												</div>
													<input type='text' class='datepicker' name='tgl_trbmasuk' id='tgl_trbmasuk' required='required' value='$re[tgl_trbmasuk]' autocomplete='off'>
											</div>
										</div>
										
									<label class='col-sm-4 control-label'>Kode Transaksi</label>        		
										<div class='col-sm-6'>
											<input type=text name='kd_hid' id='kd_hid' class='form-control' required='required' value='$re[kd_trbmasuk]' autocomplete='off' Disabled>
										</div>
										
									<label class='col-sm-4 control-label'>Supplier</label>        		
										<div class='col-sm-6'>
											<div class='input-group'>
												<input type='text' class='form-control' name='nm_supplier' id='nm_supplier' required='required' value='$re[nm_supplier]' autocomplete='off' Disabled>
													<div class='input-group-addon'>
														<button type=button data-toggle='modal' data-target='#ModalSupplier' href='#'><span class='glyphicon glyphicon-search'></span></button>
													</div>
											</div>
										</div>
									
									<label class='col-sm-4 control-label'>Telepon</label>        		
										<div class='col-sm-6'>
											<input type=text name='tlp_supplier' id='tlp_supplier' class='form-control' value='$re[tlp_supplier]' autocomplete='off'>
										</div>
										
									<label class='col-sm-4 control-label'>Alamat</label>        		
										<div class='col-sm-6'>
											<textarea name='alamat_supplier' id='alamat_supplier' class='form-control' rows='2'>$re[alamat_trbmasuk]</textarea>
										</div>
										
									<label class='col-sm-4 control-label'>No Faktur</label>        		
										<div class='col-sm-6'>
											<textarea name='ket_trbmasuk' id='ket_trbmasuk' class='form-control' rows='2'>$re[ket_trbmasuk]</textarea>
										</div>
							  
									<label class='col-sm-4 control-label'>Jatuh Tempo</label>
										<div class='col-sm-6'>
											<div class='input-group date'>
												<div class='input-group-addon'>
													<span class='glyphicon glyphicon-th'></span>
												</div>
													<input type='text' class='datepicker' name='jatuhtempo' id='jatuhtempo' required='required' value='$re[jatuhtempo]'  autocomplete='off'>
											</div>	
											<div class='buttons'>
												<button type='button' class='btn btn-primary right-block' onclick='simpan_transaksi();'>SIMPAN TRANSAKSI</button>
												&nbsp&nbsp&nbsp
												<input class='btn btn-danger' type='button' value=KEMBALI onclick=self.history.back()>
												</div>
										</div>
										
							  </div>
							  
						</div>
						
						<div class='col-lg-6'>
						
						
								<input type=hidden name='id_barang' id='id_barang'>
								<input type=hidden name='stok_barang' id='stok_barang'>
								
								<div class='form-group'>
								
									
									<label class='col-sm-4 control-label'>Kode Barang</label>        		
										<div class='col-sm-7'>
											<div class='input-group'>
												<input type='text' class='form-control' name='kd_barang' id='kd_barang' autocomplete='off'>
													<div class='input-group-addon'>
														<button type=button data-toggle='modal' data-target='#ModalItem' href='#' id='kode'><span class='glyphicon glyphicon-search'></span></button>
													</div>
											</div>
										</div>
									
									<label class='col-sm-4 control-label'>Nama Barang</label>        		
										<div class='col-sm-7'>
											<input type=text name='nmbrg_dtrbmasuk' id='nmbrg_dtrbmasuk' class='typeahead form-control' autocomplete='off'>
										</div>
										
									<label class='col-sm-4 control-label'>Qty</label>        		
										<div class='col-sm-7'>
											<input type='number' name='qty_dtrbmasuk' id='qty_dtrbmasuk' class='form-control' autocomplete='off'>
										</div>
										
									<label class='col-sm-4 control-label'>Satuan</label>        		
										<div class='col-sm-7'>
											<input type=text name='sat_dtrbmasuk' id='sat_dtrbmasuk' class='form-control' autocomplete='off'>
										</div>
										
									<label class='col-sm-4 control-label'>HNA</label>        		
										<div class='col-sm-7'>
											<input type=text name='hnasat_dtrbmasuk' id='hnasat_dtrbmasuk' class='form-control' autocomplete='off'>
											
										</div>
									
									<label class='col-sm-4 control-label'>Harga Jual</label>        		
										<div class='col-sm-7'>
											<input type=text name='hrgjual_dtrbmasuk' id='hrgjual_dtrbmasuk' class='form-control' autocomplete='off'>
											
										</div>
										
									<label class='col-sm-4 control-label'>Diskon Produk (%)</label>        		
										<div class='col-sm-7'>
											<input type=text name='diskon' id='diskon' class='form-control' autocomplete='off'>
											</p>
												<div class='buttons'>
													<button type='button' class='btn btn-success right-block' onclick='simpan_detail();'>SIMPAN DETAIL</button>
												</div>
										</div>
									
									
										
										
								</div>
								
									
						</div>
						</form>
							  
				</div> 
				
				<div id='tabeldata'>
				
			</div>";
    }   
}
?>


<!-- Modal itemmat -->
<div id="ModalItem" class="modal fade" role="dialog">
  <div class="modal-lg modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">PILIH ITEM BARANG</h4>
		
		<div id="box">
		</div>
      </div>
	  
	  
							  
      <div class="modal-body">
		<table id="example1" class="table table-condensed table-bordered table-striped table-hover" >
		
						<thead>
							<tr class="judul-table">
								<th style="vertical-align: middle; background-color: #008000; text-align: center; ">No</th>
								<th style="vertical-align: middle; background-color: #008000; text-align: left; ">Kode</th>
								<th style="vertical-align: middle; background-color: #008000; text-align: left; ">Nama Barang</th>
								<th style="vertical-align: middle; background-color: #008000; text-align: right; ">Qty</th>
								<th style="vertical-align: middle; background-color: #008000; text-align: center; ">Satuan</th>
								<th style="vertical-align: middle; background-color: #008000; text-align: right; ">Harga Beli</th>
								<th style="vertical-align: middle; background-color: #008000; text-align: center; ">Pilih</th>
							</tr>
						</thead>
						<tbody>
						<?php $no=1;
							    $tampil_dproyek=mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM barang ORDER BY id_barang ASC");
								while ($rd=mysqli_fetch_array($tampil_dproyek)){
								
								$stok1 = format_rupiah($rd['stok_barang']);
								$harga1 = format_rupiah($rd['hrgsat_barang']);
								
								echo "<tr style='font-size: 13px;'> 
										     <td align=center>$no</td>
											 <td>$rd[kd_barang]</td>
											 <td>$rd[nm_barang]</td>
											 <td align=right>$stok1</td>
											 <td align=center>$rd[sat_barang]</td>
											 <td align=right>$harga1</td>
											 <td align=center>
											 
											 <button class='btn btn-xs btn-info' id='pilihbarang' 
												 data-id_barang='$rd[id_barang]'
												 data-kd_barang='$rd[kd_barang]'
												 data-nm_barang='$rd[nm_barang]'
												 data-stok_barang='$rd[stok_barang]'
												 data-sat_barang='$rd[sat_barang]'
												 data-hrgsat_barang='$rd[hrgsat_barang]'
												 data-hrgjual_barang='$rd[hrgjual_barang]'>
												 <i class='fa fa-check'></i>
												 </button>
												
											</td>
										</tr>";
									$no++;
								}
						echo "</tbody></table>";
					?>
    </div>
  </div>
</div>
</div>
<!-- end modal item -->


<!-- Modal supplier -->
<div id="ModalSupplier" class="modal fade" role="dialog">
  <div class="modal-lg modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">PILIH SUPPLIER</h4>
		
		<div id="box">
		</div>
      </div>
	  
	  
							  
      <div class="modal-body">
		<table id="example1" class="table table-condensed table-bordered table-striped table-hover" >
		
						<thead>
							<tr class="judul-table">
								<th style="vertical-align: middle; background-color: #008000; text-align: center; ">No</th>
								<th style="vertical-align: middle; background-color: #008000; text-align: left; ">Supplier</th>
								<th style="vertical-align: middle; background-color: #008000; text-align: left; ">Telepon</th>
								<th style="vertical-align: middle; background-color: #008000; text-align: left; ">Alamat</th>
								<th style="vertical-align: middle; background-color: #008000; text-align: center; ">Pilih</th>
							</tr>
						</thead>
						<tbody>
						<?php 
						$no=1;
							    $tampil_dproyek=mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM supplier ORDER BY nm_supplier ASC");
								while ($rd=mysqli_fetch_array($tampil_dproyek)){
								
								echo "<tr style='font-size: 13px;'> 
										     <td align=center>$no</td>
											 <td>$rd[nm_supplier]</td>
											 <td>$rd[tlp_supplier]</td>
											 <td>$rd[alamat_supplier]</td>
											 <td align=center>
											 
											 <button class='btn btn-xs btn-info' id='pilihsupplier' 
												 data-id_supplier='$rd[id_supplier]'
												 data-nm_supplier='$rd[nm_supplier]'
												 data-tlp_supplier='$rd[tlp_supplier]'
												 data-alamat_supplier='$rd[alamat_supplier]'>
												 <i class='fa fa-check'></i>
												 </button>
												
											</td>
										</tr>";
									$no++;
								}
						echo "</tbody></table>";
					?>
    </div>
  </div>
</div>
</div>
<!-- end modul supplier -->


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
        
	$(document).ready(function () {
        tabel_detail();
		
    });
		
    // Autocomplete nama obat
	$('#nmbrg_dtrbmasuk').typeahead({
		source: function(query, process) {
			return $.post('modul/mod_trbmasuk/autonamabarang.php', {
				query: query
			}, function(data) {

				data = $.parseJSON(data);
				return process(data);

			});
		}
	});
	
	// event enter nama obat		
	$(document).ready(function() {
		$('#nmbrg_dtrbmasuk').on('keydown', function(e) {
			if (e.which == 13) {
				let nm_barang = $('#nmbrg_dtrbmasuk').val();
				$.ajax({
					url: 'modul/mod_trbmasuk/autonamabarang_enter.php',
					type: 'post',
					data: {
						'nm_barang': nm_barang
					},
				}).success(function(response) {
					let data = $.parseJSON(response);
					// let data = JSON.parse(response)
					let qty_default = "1";

					for (let i = 0; i < data.length; i++) {
						data = data[i];
						document.getElementById('id_barang').value = data.id_barang;
						document.getElementById('kd_barang').value = data.kd_barang;
						document.getElementById('nmbrg_dtrbmasuk').value = data.nm_barang;
						document.getElementById('stok_barang').value = data.stok_barang;

						document.getElementById('qty_dtrbmasuk').value = qty_default;
						document.getElementById('sat_dtrbmasuk').value = data.sat_barang;
				// 		document.getElementById('hrgjual_dtrbmasuk').value = data.hrgjual_barang;
						document.getElementById('hrgsat_dtrbmasuk').value = data.hrgsat_barang;
					}

				});
			}
		})
	});


    $(document).on('click', '#kd_barang', function(){
        $("#example1").DataTable().destroy();
        $("#example1").DataTable();
        
    });
    
	$(document).on('click', '#pilihbarang', function(){

	 var id_barang = $(this).data('id_barang');
	 var kd_barang = $(this).data('kd_barang');
	  var nm_barang = $(this).data('nm_barang');
	  var stok_barang = $(this).data('stok_barang');
	   var sat_barang = $(this).data('sat_barang');
	    var hrgsat_barang = $(this).data('hrgsat_barang');
	    var hrgjual_barang = $(this).data('hrgjual_barang');
		 var qty_default = "1";
		 
			document.getElementById('id_barang').value= id_barang ;
			document.getElementById('kd_barang').value= kd_barang ;
			document.getElementById('nmbrg_dtrbmasuk').value= nm_barang ;
			document.getElementById('stok_barang').value= stok_barang ;
			document.getElementById('qty_dtrbmasuk').value= qty_default ;
			document.getElementById('sat_dtrbmasuk').value= sat_barang ;
			document.getElementById('hrgsat_dtrbmasuk').value= hrgsat_barang ;
			document.getElementById('hrgjual_dtrbmasuk').value= hrgjual_barang ;
			//hilangkan modal
			$(".close").click();

			});
			
			
	$(document).on('click', '#pilihsupplier', function(){

	 var id_supplier = $(this).data('id_supplier');
	 var nm_supplier = $(this).data('nm_supplier');
	  var tlp_supplier = $(this).data('tlp_supplier');
	  var alamat_supplier = $(this).data('alamat_supplier');
		 
			document.getElementById('id_supplier').value= id_supplier ;
			document.getElementById('nm_supplier').value= nm_supplier ;
			document.getElementById('tlp_supplier').value= tlp_supplier ;
			document.getElementById('alamat_supplier').value= alamat_supplier ;
			//hilangkan modal
			$(".close").click();

			});
	
	
	function simpan_detail(){
	
	var kd_trbmasuk = document.getElementById('kd_trbmasuk').value;
	var id_barang = document.getElementById('id_barang').value;
	var kd_barang = document.getElementById('kd_barang').value;
	var nmbrg_dtrbmasuk = document.getElementById('nmbrg_dtrbmasuk').value;
	var stok_barang = document.getElementById('stok_barang').value;
	var qty_dtrbmasuk = document.getElementById('qty_dtrbmasuk').value;
	var sat_dtrbmasuk = document.getElementById('sat_dtrbmasuk').value;
	var hrgsat_dtrbmasuk = document.getElementById('hrgsat_dtrbmasuk').value;
	var hrgjual_dtrbmasuk = document.getElementById('hrgjual_dtrbmasuk').value;
	
	if(nmbrg_dtrbmasuk == ""){
	alert('Belum ada Item terpilih');
	}else if(qty_dtrbmasuk == ""){
	alert('Qty tidak boleh kosong');
	}else if(hrgsat_dtrbmasuk == ""){
	alert('Harga tidak boleh kosong');
	}else{
	
	//cek stok barang apakah cukup
	//if(stok_barang < qty_dtrbmasuk){
	//alert('Stok barang tidak mencukupi');
	//}else{
	//}
	
	$.ajax({
					
			type: 'post',
			url: "modul/mod_trbmasuk/simpandetail_tbm.php",
			data: {'kd_trbmasuk': kd_trbmasuk,
				   'id_barang': id_barang,
				   'kd_barang': kd_barang,
				   'nmbrg_dtrbmasuk': nmbrg_dtrbmasuk,
				   'qty_dtrbmasuk': qty_dtrbmasuk,
				   'sat_dtrbmasuk': sat_dtrbmasuk,
				   'hrgsat_dtrbmasuk': hrgsat_dtrbmasuk,
				   'hrgjual_dtrbmasuk': hrgjual_dtrbmasuk
			    
			},
                success: function(data) {
                //alert('Tambah data detail berhasil');
				document.getElementById("id_barang").value = "";
				document.getElementById("kd_barang").value = "";
				document.getElementById("nmbrg_dtrbmasuk").value = "";
				document.getElementById("qty_dtrbmasuk").value = "";
				document.getElementById("sat_dtrbmasuk").value = "";
				document.getElementById("hrgsat_dtrbmasuk").value = "";
				document.getElementById("hrgjual_dtrbmasuk").value = "";
				tabel_detail();
            }
		});
		
	}
	
		
	
	}
			
			
	$(document).on('click', '#hapusdetail', function(){

	  var id_dtrbmasuk = $(this).data('id_dtrbmasuk');
				
		$.ajax({
				   type: 'post',
				   url: "modul/mod_trbmasuk/hapusdetail_tbm.php",
				   data: {
						   id_dtrbmasuk: id_dtrbmasuk
						 },
					
					success: function () {
					//setelah simpan data, tabel_detail data terbaru
					 //alert('Hapus data detail berhasil');
					tabel_detail();
					//hilangkan modal
					$(".close").click();
					}
                });

			});
		
        

        //fungsi tabel detail
        function tabel_detail() {
		
		var kd_trbmasuk = document.getElementById('kd_trbmasuk').value;
		
            $.ajax({
                url: 'modul/mod_trbmasuk/tbl_detail.php',
                type: 'post',
				data: {'kd_trbmasuk': kd_trbmasuk },
                success: function(data) {
                    $('#tabeldata').html(data);
                }
				
            });
        }
		
		$('#kd_barang').keydown(function(e) {
		if (e.which == 13) { // e.which == 13 merupakan kode yang mendeteksi ketika anda   // menekan tombol enter di keyboard
			//letakan fungsi anda disini
   
			var kd_brg = $("#kd_barang").val();
                $.ajax({
                    url: 'modul/mod_trbmasuk/autobarang.php',
					type: 'post',
					data: {'kd_brg': kd_brg },
                }).success(function (data) {
				
                    var json = data;
					//replace array [] menjadi '' 
					var res1 = json.replace("[", "");
					var res2 = res1.replace("]", "");
					//INI CONTOH ARRAY JASON const json = '{"result":true, "count":42}';
					datab = JSON.parse(res2);
					document.getElementById('id_barang').value= datab.id_barang ;
					document.getElementById('nmbrg_dtrbmasuk').value= datab.nm_barang ;
					document.getElementById('stok_barang').value= datab.stok_barang ;
					document.getElementById('qty_dtrbmasuk').value= "1" ;
					document.getElementById('sat_dtrbmasuk').value= datab.sat_barang ;
					document.getElementById('hrgsat_dtrbmasuk').value= datab.hrgsat_barang ;
                });
				
		}
		}); 
		
		
		function simpan_transaksi(){
	
		var stt_aksi = document.getElementById('stt_aksi').value;
		var id_trbmasuk = document.getElementById('id_trbmasuk').value;
		var kd_trbmasuk = document.getElementById('kd_trbmasuk').value;
		var tgl_trbmasuk = document.getElementById('tgl_trbmasuk').value;
		var nm_supplier = document.getElementById('nm_supplier').value;
		var id_supplier = document.getElementById('id_supplier').value;
		var petugas = document.getElementById('petugas').value;
		var tlp_supplier = document.getElementById('tlp_supplier').value;
		var alamat_trbmasuk = document.getElementById('alamat_supplier').value;
		var ket_trbmasuk = document.getElementById('ket_trbmasuk').value;
		var ttl_trkasir = document.getElementById('ttl_trkasir').value;
		var dp_bayar = document.getElementById('dp_bayar').value;
		var sisa_bayar = document.getElementById('sisa_bayar').value;
		var carabayar = document.getElementById('carabayar').value;

			var ttl_trkasir1 = ttl_trkasir.replace(".", "");
			var dp_bayar1 = dp_bayar.replace(".", "");
			var sisa_bayar1 = sisa_bayar.replace(".", "");
			
			var ttl_trkasir1x = ttl_trkasir1.replace(".", "");
			var dp_bayar1x = dp_bayar1.replace(".", "");
			var sisa_bayar1x = sisa_bayar1.replace(".", "");
		
		if(nm_supplier == ""){
				alert('Belum ada data supplier');
		}else{
		
			$.ajax({
							
					type: 'post',
					url: "modul/mod_trbmasuk/aksi_trbmasuk.php",
					
					data: {'id_trbmasuk': id_trbmasuk,
						   'kd_trbmasuk': kd_trbmasuk,
						   'tgl_trbmasuk': tgl_trbmasuk,
						   'id_supplier': id_supplier,
						   'petugas': petugas,
						   'nm_supplier': nm_supplier,
						   'tlp_supplier': tlp_supplier,
						   'alamat_trbmasuk': alamat_trbmasuk,
						   'stt_aksi': stt_aksi,
						   'ket_trbmasuk': ket_trbmasuk,
						   'ttl_trkasir': ttl_trkasir1x,
						   'dp_bayar': dp_bayar1x,
						   'sisa_bayar': sisa_bayar1x,
						   'carabayar': carabayar},
						success: function(data) {
						alert('Proses berhasil !');window.location='media_admin.php?module=trbmasuk';
					}
				});
			}
	}
		
</script>