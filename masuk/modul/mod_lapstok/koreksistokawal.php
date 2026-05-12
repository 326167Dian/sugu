<?php
session_start();
if (empty($_SESSION['username']) and empty($_SESSION['passuser'])) {
	echo "<link href=../css/style.css rel=stylesheet type=text/css>";
	echo "<div class='error msg'>Untuk mengakses Modul anda harus login.</div>";
} else {

	$aksi = "modul/mod_lapstok/aksi_barang.php";
	$aksi_barang = "masuk/modul/mod_lapstok/aksi_barang.php";
	switch ($_GET['act']) {
			// Tampil barang
		default:


			$tampil_barang = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM barang ORDER BY barang.id_barang ");

?>


			<div class="box box-primary box-solid table-responsive">
				<div class="box-header with-border">
					<h3 class="box-title">KOREKSI STOK</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						<a class='btn  btn-success btn-flat' href='modul/mod_lapstok/sinkronisasi_stok.php'>SINKRONISASI</a>
					</div><!-- /.box-tools -->
				</div>
				<div class="box-body">

					<table id="example1" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>No</th>
								<th>Kode</th>
								<th>Nama Barang</th>
								<th style="text-align: center; ">Penjualan lebih cepat</th>
								<th style="text-align: center; ">Stok Masuk</th>
								<th style="text-align: center; ">Penjualan Setelah<br> Stok Masuk</th>
								<th style="text-align: center; ">Stok Barang Real</th>
								<th width="70">Koreksi Stok</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$no = 1;
							while ($r = mysqli_fetch_array($tampil_barang)) {
                                $r1=$r[kd_barang];
                                $tampilmasuk = mysqli_query($GLOBALS["___mysqli_ston"],"select sum(qty_dtrbmasuk) as subtotal,min(waktu) as masukawal from trbmasuk_detail
                                                where kd_barang=$r1 ");
                                $masuk = mysqli_fetch_array($tampilmasuk);
                                $masuk1 = $masuk[subtotal];
                                $masuk2 = $masuk[masukawal];

                                $tampilkeluar = mysqli_query($GLOBALS["___mysqli_ston"],
                                    "select min(waktu) as keluarawal, max(waktu) as keluarakhir from trkasir_detail where kd_barang=$r1");
                                $keluar = mysqli_fetch_array($tampilkeluar);
                                $keluar1= $keluar[keluarawal];
                                $keluar2= $keluar[keluarakhir];

                                if ($keluar1<$masuk2)
                                  { $patokan = $masuk2;}
                                else
                                  {$patokan = $keluar1;}
                                $transaksi_atas = mysqli_query($GLOBALS["___mysqli_ston"],
                                          "select sum(qty_dtrkasir) as qty_atas from trkasir_detail 
                                                where kd_barang=$r1 and waktu between '$keluar1' and '$masuk2'");
                                $qty_atas2= mysqli_fetch_array($transaksi_atas);
                                $qty_atas3= $qty_atas2[qty_atas];

                                $transaksi_bawah = mysqli_query($GLOBALS["___mysqli_ston"],
                                    "select sum(qty_dtrkasir) as qty_bawah from trkasir_detail 
                                                where kd_barang=$r1 and waktu between '$masuk2' and '$keluar2'");
                                $qty_bawah2= mysqli_fetch_array($transaksi_bawah);
                                $qty_bawah3= $qty_bawah2[qty_bawah];
                                $stok_real = $qty_atas3 + $masuk1 - $qty_bawah3;

								echo "<tr class='warnabaris' >
                                             <td>$no</td>                                    										     
											 <td>$r[kd_barang]</td>
											 <td>$r[nm_barang]</td>
											 <td align='center'>$qty_atas3</td>
											 <td align='center'>$masuk1</td>
											 <td align='center'>$qty_bawah3</td>
											 <td align='center'>$stok_real</td>
											 <td style='text-align: center;'><a href='?module=koreksistok&act=edit&id=$r[id_barang]' title='EDIT' class='btn btn-primary btn-xs'>KOREKSI</a> 	
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

		case "edit":
			$edit = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM barang 
	WHERE barang.id_barang='$_GET[id]'");
			$r = mysqli_fetch_array($edit);

			echo "
		  <div class='box box-primary box-solid'>
				<div class='box-header with-border'>
					<h3 class='box-title'>INPUT STOK BARU</h3>
					<div class='box-tools pull-right'>
						<button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
                    </div><!-- /.box-tools -->
				</div>
				
				<div class='box-body'>
						<form method=POST method=POST action=$aksi?module=koreksistok&act=input_koreksi  enctype='multipart/form-data' class='form-horizontal'>
							  <input type=hidden name=id value=''>											  
							  
							 
                             
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Kode Barang</label>        		
									 <div class='col-sm-4'>
										<input type=text name='kd_barang' class='form-control' required='required' value='$r[kd_barang]' autocomplete='off'>
									 </div>
							  </div>
							  						  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Tanggal</label>        		
									 <div class='col-sm-4'>  
										<input type=date name='tgl' class='form-control' required='required' value='' autocomplete='off'>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Nama Barang</label>        		
									 <div class='col-sm-4'>
										<input type=text name='nm_kbarang' class='form-control' required='required' value='$r[nm_barang]' autocomplete='off'>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Stok Barang</label>        		
									 <div class='col-sm-3'>
										<input type=number name='stok_barangawal' class='form-control' required='required' value='$r[stok_barang]' autocomplete='off'>
									 </div>
							  </div>";

			$beli = "SELECT trbmasuk.tgl_trbmasuk,                                           
                                                       SUM(trbmasuk_detail.qty_dtrbmasuk) AS totalbeli                                            
                                                       FROM trbmasuk_detail join trbmasuk 
                                                       on (trbmasuk_detail.kd_trbmasuk=trbmasuk.kd_trbmasuk)
                                                       WHERE kd_barang =$r[kd_barang]";
			$buy = mysqli_query($GLOBALS["___mysqli_ston"], $beli);
			$buy2 = mysqli_fetch_array($buy);

			$jual = "SELECT trkasir.tgl_trkasir,                                
                                                        sum(trkasir_detail.qty_dtrkasir) AS totaljual
                                                        FROM trkasir_detail join trkasir 
                                                        on (trkasir_detail.kd_trkasir=trkasir.kd_trkasir)
                                                        WHERE kd_barang =$r[kd_barang]";
			$jokul = mysqli_query($GLOBALS["___mysqli_ston"], $jual);
			$sell = mysqli_fetch_array($jokul);
			$selisih = $buy2['totalbeli'] - $sell['totaljual'];
			echo "			  <div class='form-group'>
									<label class='col-sm-2 control-label'>Selisih Transaksi Masuk & Keluar</label>        		
									 <div class='col-sm-3'>
									 
										<input type=number name='selisih_tx' class='form-control' required='required' value='$selisih' autocomplete='off'>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Koreksi Stok</label>        		
									 <div class='col-sm-3'>
										<input type=number name='stok_baru' class='form-control' required='required' value='' autocomplete='off'>
									 </div>
							  </div>
							  
							 
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Keterangan Koreksi</label>        		
									 <div class='col-sm-4'>
										<textarea name='ket' class='ckeditor' id='content' rows='3'> </textarea>
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