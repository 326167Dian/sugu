<?php
session_start();
 if (empty($_SESSION['username']) AND empty($_SESSION['passuser'])){
  echo "<link href=../css/style.css rel=stylesheet type=text/css>";
  echo "<div class='error msg'>Untuk mengakses Modul anda harus login.</div>";
}
else{

$aksi="modul/mod_barang/aksi_barang.php";
$aksi_barang = "masuk/modul/mod_barang/aksi_barang.php";
switch($_GET[act]){
  // Tampil barang
  default:

  
      $tampil_barang = $db->query("SELECT * FROM barang ORDER BY barang.id_barang ");
      
	  ?>
			
			
			<div class="box box-danger box-solid">
				<div class="box-header with-border">
					<h3 class="box-title">NILAI BARANG</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div><!-- /.box-tools -->
				</div>
				<div class="box-body">
					<!--<a  class ='btn  btn-success btn-flat' href='?module=barang&act=tambah'>TAMBAH</a>-->
					<br><br>
					
					
					<table id="example1" class="table table-bordered table-striped" >
						<thead>
							<tr>
								<th>No</th>
								<th>Kode</th>
								<th>Nama Barang</th>
								<th style="text-align: right; ">Qty/Stok</th>

								<th style="text-align: center; ">Satuan</th>

								<th style="text-align: right; ">Harga Beli</th>

								<th style="text-align: center; ">Nilai Barang</th>

							</tr>
						</thead>
						<tbody>
						<?php 
								$no=1;
								while ($r = $tampil_barang->fetch(PDO::FETCH_ASSOC)){
								$hargabeli = format_rupiah($r['hrgsat_barang']);
								$hargajual = format_rupiah($r['hrgjual_barang']);
								$nilaibarang = format_rupiah($r['hrgsat_barang'] * $r[stok_barang]);
								$nilaibarang2 = $r['hrgsat_barang'] * $r[stok_barang];
                                $totalbarang += $nilaibarang2;
                                $tb = format_rupiah($totalbarang);

									echo "<tr class='warnabaris' >
											 <td>$no</td>           
											 <td>$r[kd_barang]</td>
											 <td>$r[nm_barang]</td>
											 <td align=right>$r[stok_barang]</td>
											
											 <td align=center>$r[sat_barang]</td>
											
											 <td align=right>$hargabeli</td>
											 <td align=right> $nilaibarang </td>
											
											 
											</td>
										</tr>";
								$no++;
								}
						echo "</tbody>
                        <tr>
                            <td colspan='5'><h3><center>Total</center></h3>  </td>
                            <td colspan='2'><h3><strong> Rp. $tb  ,- </strong></h3></td> 
                        </tr>
                        </table>";
					?>
				</div>
			</div>	
             

<?php
    
    break;
	
	case "tambah":
       
        echo "
		  <div class='box box-danger box-solid'>
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
											 $tampil = $db->query("SELECT * FROM satuan ORDER BY nm_satuan ASC");
											 while($rk = $tampil->fetch(PDO::FETCH_ASSOC)){
											 echo "<option value=$rk[nm_satuan]>$rk[nm_satuan]</option>";
											 }
										echo "</select>
									 </div>
							  </div> 
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Jenis Obat</label>        		
									 <div class='col-sm-3'>
										<select name='jenis_obat' class='form-control' >";
											 $tampil = $db->query("SELECT * FROM jenis_obat ORDER BY jenisobat ASC");
											 while($rk = $tampil->fetch(PDO::FETCH_ASSOC)){
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
    $edit = $db->prepare("SELECT * FROM barang 
	WHERE barang.id_barang=?");
    $edit->execute([$_GET['id']]);
    $r = $edit->fetch(PDO::FETCH_ASSOC);
			
		echo "
		  <div class='box box-danger box-solid'>
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
											 $tampil = $db->query("SELECT * FROM satuan ORDER BY nm_satuan");
											 while($k = $tampil->fetch(PDO::FETCH_ASSOC)){
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
 $(function(){
  $(".datepicker").datepicker({
      format: 'yyyy-mm-dd',
      autoclose: true,
      todayHighlight: true,
  });
 });
</script>