<?php
session_start();
if (empty($_SESSION['username']) and empty($_SESSION['passuser'])) {
	echo "<link href=../css/style.css rel=stylesheet type=text/css>";
	echo "<div class='error msg'>Untuk mengakses Modul anda harus login.</div>";
} else {

	$aksi = "modul/mod_trbmasuk/aksi_trbmasuk.php";
	$aksi_trbmasuk = "masuk/modul/mod_trbmasuk/aksi_trbmasuk.php";
	switch ($_GET['act']) {
			// Tampil barang
		default:


// 			$tampil_trbmasuk = $db->query("SELECT * FROM trbmasuk 
//         	  WHERE id_resto = 'pusat' and jenis = 'nonpbf'
//         	  ORDER BY trbmasuk.id_trbmasuk DESC");


?>
			<div class="box box-primary box-solid">
				<div class="box-header with-border">
					<h3 class="box-title">TRANSAKSI BARANG MASUK (SUDAH TERMASUK PAJAK) -> <a href="https://youtu.be/SFd8rh0pKec" target="_blanks">TONTON VIDEO</a></h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div><!-- /.box-tools -->
				</div>
				<div class="box-body table-responsive">
					<a class='btn  btn-success btn-flat' href='?module=trbmasuk&act=tambah'>TAMBAH</a>
					<a class='btn  btn-info btn-flat' href='?module=trbmasuk&act=cari'>CARI NOMOR BATCH</a>
					<div></div>
					<p>
					<p>
						<a class='btn  btn-warning  btn-flat' href='#'></a>
						<small>* Pembayaran belum lunas</small>
						<br><br>


					<table id="tes" class="table table-bordered table-striped">
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
							// $no = 1;
							// while ($r = mysqli_fetch_array($tampil_trbmasuk)) {
							// 	$ttl_trbmasuknya = format_rupiah($r['ttl_trbmasuk']);
							// 	$dp_bayar = format_rupiah($r['dp_bayar']);
							// 	$sisa_bayar = format_rupiah($r['sisa_bayar']);

							// 	echo "<tr class='warnabaris' >";

							// 	if ($r['carabayar'] == "LUNAS") {
							// 		echo "
							// 					<td>$no</td>           
							// 					<td>$r[kd_trbmasuk]</td>
							// 				";
							// 	} else {

							// 		echo "
							// 					<td style='background-color:#ffbf00;'>$no</td>           
							// 					<td style='background-color:#ffbf00;'>$r[kd_trbmasuk]</td>
							// 				";
							// 	}
							// 	echo "               
							// 				 <td>$r[petugas]</td>											
							// 				 <td>$r[tgl_trbmasuk]</td>											
							// 				 <td>$r[nm_supplier]</td>
							// 				 <td>$r[ket_trbmasuk]</td>											
							// 				<td align=right>$sisa_bayar</td>											 
							// 				<td align=center>$r[carabayar]</td>											 
							// 				 <td align='center'><a href='?module=trbmasuk&act=ubah&id=$r[id_trbmasuk]' title='EDIT' class='btn btn-warning btn-xs'>TAMPIL</a> 
							// 				 <!-- tidak boleh di hapus
							// 				 <a href=javascript:confirmdelete('$aksi?module=trbmasuk&act=hapus&id=$r[id_trbmasuk]') title='HAPUS' class='btn btn-danger btn-xs'>HAPUS</a>
							// 				 -->
							// 				</td>
							// 			</tr>";
							// 	$no++;
							// }
							// echo "</tbody></table>";
							?>
						</tbody>
					</table>
				</div>
			</div>

			<script>
				$(document).ready(function() {
					$("#tes").DataTable({
						processing: true,
						serverSide: true,
						ajax: {
							"url": "modul/mod_trbmasuk/trbmasuk-serverside.php?action=table_data",
							"dataType": "JSON",
							"type": "POST"
						},
						"rowCallback": function(row, data, index) {
							// warna for nomor
							if (data['carabayar'] != "LUNAS") {
								$(row).find('td:eq(0)').css('background-color', '#ffbf00');
								$(row).find('td:eq(1)').css('background-color', '#ffbf00');
							}

						},
						columns: [{
								"data": "no",
								"className": "text-center"
							},
							{
								"data": "kd_trbmasuk",
								"className": "text-left"
							},
							{
								"data": "petugas",
								"className": "text-left"
							},
							{
								"data": "tgl_trbmasuk",
								"className": "text-center"
							},
							{
								"data": "nm_supplier",
								"className": "text-left"
							},
							{
								"data": "ket_trbmasuk",
								"className": "text-left"
							},
							{
								"data": "sisa_bayar",
								"className": "text-right",
								"render": function(data, type, row) {
									return formatRupiah(data);
								}
							},
							{
								"data": "carabayar",
								"className": "text-center"
							},
							{
								"data": "aksi",
								"className": "text-center"
							},
						]
					});
				});
			</script>
		<?php

			break;

		case "tambah":
			//cek apakah ada kode transaksi ON berdasarkan user
			$cekkd = $db->prepare("SELECT * FROM kdbm WHERE id_admin=? AND id_resto='pusat' AND stt_kdbm='ON'");
			$cekkd->execute([$_SESSION['id_admin']]);
			$ketemucekkd = $cekkd->rowCount();
			$hcekkd = $cekkd->fetch(PDO::FETCH_ASSOC);
			$petugas = $_SESSION['namalengkap'];

			if ($ketemucekkd > 0) {
				$kdtransaksi = $hcekkd['kd_trbmasuk'];
			} else {
				$kdunik = date('dmyhis');
				$kdtransaksi = "BMP-" . $kdunik;
				$cekkd2 = $db->prepare("SELECT * FROM kdbm WHERE kd_trbmasuk=?");
			    $cekkd2->execute([$kdtransaksi]);
			    $ketemucekkd2 = $cekkd2->rowCount();
			    if ($ketemucekkd2 > 0) {
			        $kdunik2 = date('dmyhis')+1;
				    $kdtransaksi = "BMP-" . $kdunik2;
			    }
				$stmt_insert_kdbm = $db->prepare("INSERT INTO kdbm(kd_trbmasuk,id_resto,id_admin) VALUES(?, 'pusat', ?)");
				$stmt_insert_kdbm->execute([$kdtransaksi, $_SESSION['id_admin']]);
			}

			$tglharini = date('Y-m-d');
            
            $stmt_header = $db->prepare("SELECT * FROM setheader");
        	$stmt_header->execute();
        	$rheader = $stmt_header->fetch(PDO::FETCH_ASSOC);
	
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
							    <input type=hidden name='min_exp_date' id='min_exp_date' value='$rheader[empatbelas]'>
							 
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
													<div class='input-group-addon'>
                                                        <button type=button data-toggle='modal' data-target='#ModalScanBarcode' href='#' id='btnScanBarcode'><span class='glyphicon glyphicon-camera'></span></button>
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
										
									<label class='col-sm-4 control-label'>Harga Jual Reguler</label>        		
										<div class='col-sm-7'>
											<input type=text name='hrgjual_dtrbmasuk' id='hrgjual_dtrbmasuk' class='form-control' autocomplete='off'>
											
										</div>
									
									<label class='col-sm-4 control-label'>Harga Jual Resep</label>        		
										<div class='col-sm-7'>
											<input type=text name='hrgjual_dtrbmasuk_resep' id='hrgjual_dtrbmasuk_resep' class='form-control' autocomplete='off'>
											
										</div>
									
									<label class='col-sm-4 control-label'>Harga Jual Nakes</label>        		
										<div class='col-sm-7'>
											<input type=text name='hrgjual_dtrbmasuk_nakes' id='hrgjual_dtrbmasuk_nakes' class='form-control' autocomplete='off'>
											
										</div>
									
									<label class='col-sm-4 control-label'>No. Batch</label>        		
										<div class='col-sm-7'>
											<input type='text' name='no_batch' id='no_batch' class='form-control' autocomplete='off'>
											
										</div>
									
									<label class='col-sm-4 control-label'>Exp. Date</label>        		
										<div class='col-sm-7'>
											<input type='text' class='datepicker' name='exp_date' id='exp_date' required='required' autocomplete='off'>
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

			$ubah = $db->prepare("SELECT * FROM trbmasuk 
	WHERE trbmasuk.id_trbmasuk=?");
			$ubah->execute([$_GET['id']]);
			$re = $ubah->fetch(PDO::FETCH_ASSOC);


			$totalharga = $re['ttl_trbmasuk'];

			$totalharga1 = format_rupiah($totalharga);
			$sisabayar = $re['sisa_bayar'];
			$sisabayar1 = format_rupiah($sisabayar);
			$diskon = $totalharga - $sisabayar;

			$diskon1 = format_rupiah($diskon);
            
            $stmt_header = $db->prepare("SELECT * FROM setheader");
        	$stmt_header->execute();
        	$rheader = $stmt_header->fetch(PDO::FETCH_ASSOC);
        	
			echo "
		  <div class='box box-primary box-solid'>
				<div class='box-header with-border'>
					<h3 class='box-title'>REVIEW TRANSAKSI BARANG MASUK</h3>
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
							   <input type=hidden name='min_exp_date' id='min_exp_date' value='$rheader[empatbelas]'>
							 
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
											<!--
											  <button type='button' class='btn btn-primary right-block' onclick='simpan_transaksi();'>SIMPAN TRANSAKSI</button>
												&nbsp&nbsp&nbsp
											-->
												<input class='btn btn-primary' type='button' value=TUTUP onclick=self.history.back()>
											</div>
								  
										</div>
							  
							</div>  
							  
						</div>
						<!-- BLOK agar karyawan tidak bisa edit
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
											<div class='btn-group btn-group-justified' role='group' aria-label='...'>
                                                <div class='btn-group' role='group'>
											        <input type=text name='nmbrg_dtrbmasuk' id='nmbrg_dtrbmasuk' class='typeahead form-control' autocomplete='off'>
                                                    
                                                </div>
                                                <div class='btn-group' role='group'>
                                                    <button type='button' class='btn btn-primary' id='nmbrg_dtrbmasuk_enter'>Enter</button>
                                                </div>
                                            </div>
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
						-->
	   
						</form>	          
									  
				</div>";
		?>
		    
			<div class='box-body table-responsive'>
				<table id="example1" class="table table-condensed table-bordered table-striped table-hover table-responsive">
					<thead>
						<th>No</th>
						<th>Kode Barang</th>
						<th>Nama Barang</th>
						<th>Qty</th>
						<th>Satuan</th>
						<th>No. Batch</th>
						<th>Exp. Date</th>
						<th>Harga Beli</th>
						<th>Sub Total</th>
					</thead>
					<tbody>
						<?php
						$show = $db->prepare("SELECT * FROM trbmasuk_detail 
                                        WHERE kd_trbmasuk=?");
						$show->execute([$re['kd_trbmasuk']]);
						$no = 1;
						while ($q = $show->fetch(PDO::FETCH_ASSOC)) {

							echo " <tr style='font-size: 14px;'>
                                            <td>$no</td>
                                            <td>$q[kd_barang]</td>
                                            <td>$q[nmbrg_dtrbmasuk]</td>
                                            <td>$q[qty_dtrbmasuk]</td>
                                            <td>$q[sat_dtrbmasuk]</td>
                                            <td>$q[no_batch]</td>
                                            <td>".tgl_indo($q['exp_date'])."</td>
                                            <td align='right'>".format_rupiah($q['hrgsat_dtrbmasuk'])."</td>
                                            <td align='right'>".format_rupiah($q['hrgttl_dtrbmasuk'])."</td>
                                         </tr>";

							$no++;
						}
						?>
					</tbody>

					<tr>
						<td align='center' colspan='5'><strong>TOTAL Rp. <?php echo $totalharga1; ?> </strong> </td>
						<td colspan='2'><strong> DISKON Rp. <?php echo $diskon1;  ?>,- </strong></td>
					</tr>
					<tr>
						<td colspan='5'>
							<h3>
								<center>Total Tagihan</center>
							</h3>
						</td>
						<td colspan='2'>
							<h3><strong> Rp. <?php echo $sisabayar1; ?> ,- </strong></h3>
						</td>
					</tr>

				</table>
			</div>
		</div>
		

<?php
			break;
		
		case "cari":
	

?>

        <div class="box box-primary box-solid">
            <div class='box-header with-border'>
				<h3 class='box-title'>SEACRH BY No. Batch</h3>
				<div class='box-tools pull-right'>
					<button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
                </div><!-- /.box-tools -->
			</div>
			<div class='box-body'>
			    <form method="post" action="?module=trbmasuk&act=carinobatch">
                    <div class="form-group row">
                        <label for="staticEmail" class="col-sm-2 col-form-label">No. Batch</label>
                        <div class="col-sm-4">
                            <input type="text" class="form-control" id="no_batch" name="no_batch">
                        </div>
                    </div>
                    <div class="form-group row justify-contend-end">
                        <label for="inputPassword" class="col-sm-2 col-form-label">&nbsp;</label>
                        <div class="col-sm-10">
                            <button type="submit" class="btn btn-primary">
                                <span class="glyphicon glyphicon-search"></span>
                                Search
                            </button>
                            
                            <button class='btn btn-primary' type='button' onclick=self.history.back()>
                                <span class="glyphicon glyphicon-chevron-left"></span>
                                Kembali
                            </button>
                            
                        </div>
                    </div>
                </form>
			</div>
        </div>
<?php
            break;
        
        case "carinobatch":
            $nobatch = $_POST['no_batch'];
            
            $caridetail = $db->prepare("SELECT * FROM trbmasuk_detail a 
            JOIN trbmasuk b ON a.kd_trbmasuk = b.kd_trbmasuk WHERE a.no_batch=?");
			$caridetail->execute([$nobatch]);
			
			$row = $caridetail->fetch(PDO::FETCH_ASSOC);
?>

        <div class="box box-primary box-solid">
            <div class='box-header with-border'>
				<h3 class='box-title'>SEACRH BY No. Batch</h3>
				<div class='box-tools pull-right'>
					<button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
                </div><!-- /.box-tools -->
			</div>
			<div class='box-body table-responsive'>
			    <div class="form-group row">
                    <label for="staticEmail" class="col-sm-2 col-form-label">Nama Barang</label>
                    <label for="staticEmail" class="col-sm-10 col-form-label">: <?=$row['nmbrg_dtrbmasuk']?></label>
                    
                    <label for="staticEmail" class="col-sm-2 col-form-label">Satuan</label>
                    <label for="staticEmail" class="col-sm-10 col-form-label">: <?=$row['sat_dtrbmasuk']?></label>
                    
                    <label for="staticEmail" class="col-sm-2 col-form-label">No. Batch</label>
                    <label for="staticEmail" class="col-sm-10 col-form-label">: <?=$row['no_batch']?></label>
                </div>
			    
			    <button class='btn btn-primary' type='button' onclick=self.history.back()>
                    <span class="glyphicon glyphicon-chevron-left"></span>
                    Kembali
                </button>
                <hr>
    			    
			    <table id="example1" class="table table-condensed table-bordered table-striped table-hover table-responsive">
    			    <thead>
    					<th class="text-center">No</th>
    					<th class="text-center">Nama Distributor</th>
    					<th class="text-center">Harga Beli</th>
    					<th class="text-center">Tanggal Masuk</th>
    					<th class="text-center">Qty</th>
    					<th class="text-center">Tanggal Exp.</th>
    					<th class="text-center">Petugas Input</th>
    				</thead>
    				<tbody>
    				    <?php
    				        $caridetail1 = $db->prepare("SELECT * FROM trbmasuk_detail a 
    				        JOIN trbmasuk b ON a.kd_trbmasuk = b.kd_trbmasuk WHERE a.no_batch=?");
    				        $caridetail1->execute([$nobatch]);
			
    				        $no=1;
    				        while($dt = mysqli_fetch_array($caridetail1)):
    				    ?>
    				    <tr>
    				        <td class="text-center"><?= $no++?></t>
        					<td class="text-left"><?= $dt['nm_supplier']?></td>
        					<td class="text-center"><?= format_rupiah($dt['hrgsat_dtrbmasuk'])?></td>
        					<td class="text-center"><?= tgl_indo($dt['tgl_trbmasuk'])?></td>
        					<td class="text-center"><?= format_rupiah($dt['qty_dtrbmasuk'])?></td>
        					<td class="text-center"><?= tgl_indo($dt['exp_date'])?></td>
        					<td class="text-center"><?= $dt['petugas']?></td>
    				    </tr>
    				    
    				    <?php endwhile; ?>
    				</tbody>
    			</table>
    			
			</div>
        </div>
        
<?php
            break;
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

			<div class="modal-body table-responsive">
				<table id="example" class="table table-condensed table-bordered table-striped table-hover">

					<thead>
						<tr class="judul-table">
							<th style="vertical-align: middle; background-color: #008000; text-align: center; ">No</th>
							<th style="vertical-align: middle; background-color: #008000; text-align: left; ">Kode</th>
							<th style="vertical-align: middle; background-color: #008000; text-align: left; ">Nama Barang</th>
							<th style="vertical-align: middle; background-color: #008000; text-align: right; ">Qty</th>
							<th style="vertical-align: middle; background-color: #008000; text-align: center; ">Satuan</th>
							<th style="vertical-align: middle; background-color: #008000; text-align: right; ">Harga Beli</th>
							<th style="vertical-align: middle; background-color: #008000; text-align: right; ">Harga Jual</th>
							<th style="vertical-align: middle; background-color: #008000; text-align: center; ">Pilih</th>
						</tr>
					</thead>
					<tbody>
						<?php
						// $no = 1;
						// $tampil_dproyek = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM barang ORDER BY id_barang ASC");
						// while ($rd = mysqli_fetch_array($tampil_dproyek)) {

						// 	$stok1 = format_rupiah($rd['stok_barang']);
						// 	$harga1 = format_rupiah($rd['hrgsat_barang']);

						// 	echo "<tr style='font-size: 13px;'> 
						// 				     <td align=center>$no</td>
						// 					 <td>$rd[kd_barang]</td>
						// 					 <td>$rd[nm_barang]</td>
						// 					 <td align=right>$stok1</td>
						// 					 <td align=center>$rd[sat_barang]</td>
						// 					 <td align=right>$harga1</td>
						// 					 <td align=center>

						//  <button class='btn btn-xs btn-info' id='pilihbarang' 
						// 	 data-id_barang='$rd[id_barang]'
						// 	 data-kd_barang='$rd[kd_barang]'
						// 	 data-nm_barang='$rd[nm_barang]'
						// 	 data-stok_barang='$rd[stok_barang]'
						// 	 data-sat_barang='$rd[sat_barang]'
						// 	 data-hrgsat_barang='$rd[hrgsat_barang]'>
						// 	 <i class='fa fa-check'></i>
						// 	 </button>

						// 					</td>
						// 				</tr>";
						// 	$no++;
						// }
						?>
					</tbody>
				</table>
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

			<div class="modal-body table-responsive">
				<table id="example3" class="table table-condensed table-bordered table-striped table-hover">

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
						<?php $no = 1;
						$tampil_dproyek = $db->query("SELECT * FROM supplier ORDER BY nm_supplier ASC");
						$tampil_dproyek->execute();
						while ($rd = $tampil_dproyek->fetch(PDO::FETCH_ASSOC)) {

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
						?>
					</tbody>
				</table>	
			</div>
		</div>
	</div>
</div>
<!-- end modul supplier -->

<!-- modal scan barcode -->
<div id="ModalScanBarcode" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">SCAN BARCODE (KAMERA HP)</h4>
            </div>
            <div class="modal-body">
                <div id="barcodeScannerReader" style="width:100%; min-height:260px; max-height:320px; background:#000;"></div>
                <video id="barcodeScannerPreview" autoplay playsinline muted style="display:none; width:100%; max-height:320px; background:#000;"></video>
                <p id="barcodeScannerStatus" style="margin-top:10px; margin-bottom:0; font-size:12px; color:#666;">Arahkan kamera ke barcode item.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<!-- end modal scan barcode -->

<script type="text/javascript">
	$(function() {
		$(".datepicker").datepicker({
			format: 'yyyy-mm-dd',
			autoclose: true,
			todayHighlight: true,
		});
	});
</script>

<script>
	$(document).ready(function() {
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
		},
		updater: function(item) {
			// Strip suffix " ( X satuan )" untuk mendapatkan nama asli
			var nm_barang_clean = item.replace(/\s*\(\s*\d+\s+\S+\s*\)\s*$/, '').trim();
			$.ajax({
				url: 'modul/mod_trbmasuk/autonamabarang_enter.php',
				type: 'post',
				data: { 'nm_barang': nm_barang_clean }
			}).success(function(response) {
				var data = $.parseJSON(response);
				var qty_default = '1';
				for (var i = 0; i < data.length; i++) {
					data = data[i];
					document.getElementById('id_barang').value = data.id_barang;
					document.getElementById('kd_barang').value = data.kd_barang;
					document.getElementById('nmbrg_dtrbmasuk').value = data.nm_barang;
					document.getElementById('stok_barang').value = data.stok_barang;
					document.getElementById('qty_dtrbmasuk').value = qty_default;
					document.getElementById('sat_dtrbmasuk').value = data.sat_barang;
					document.getElementById('hrgsat_dtrbmasuk').value = data.hrgsat_barang;
					document.getElementById('hrgjual_dtrbmasuk').value = data.hrgjual_barang;
					document.getElementById('hrgjual_dtrbmasuk_resep').value = data.hrgjual_barang1;
					document.getElementById('hrgjual_dtrbmasuk_nakes').value = data.hrgjual_barang2;
				}
				closeAutocompleteSuggestions('#nmbrg_dtrbmasuk');
			});
			return nm_barang_clean;
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
						document.getElementById('hrgjual_dtrbmasuk').value = data.hrgjual_barang;
						document.getElementById('hrgjual_dtrbmasuk_resep').value = data.hrgjual_barang1;
						document.getElementById('hrgjual_dtrbmasuk_nakes').value = data.hrgjual_barang2;
						document.getElementById('hrgsat_dtrbmasuk').value = data.hrgsat_barang;
				// 		document.getElementById('indikasi').value = data.indikasi;
					}

				});
			}
		})
	});

    $('#nmbrg_dtrbmasuk_enter').on('click', function(){
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
				document.getElementById('hrgjual_dtrbmasuk').value = data.hrgjual_barang;
				document.getElementById('hrgjual_dtrbmasuk_resep').value = data.hrgjual_barang1;
				document.getElementById('hrgjual_dtrbmasuk_nakes').value = data.hrgjual_barang2;
				document.getElementById('hrgsat_dtrbmasuk').value = data.hrgsat_barang;
				// document.getElementById('indikasi').value = data.indikasi;
			}

		});
	})


	$(document).on('click', '#kode', function() {
		$("#example").DataTable().destroy();

		$("#example").DataTable({
			processing: true,
			serverSide: true,
			ajax: {
				"url": "modul/mod_trbmasuk/barang-serverside.php?action=table_data",
				"dataType": "JSON",
				"type": "POST"
			},
			columns: [{
					"data": "no",
					"className": 'text-center',
				},
				{
					"data": "kd_barang"
				},
				{
					"data": "nm_barang"
				},
				{
					"data": "stok_barang",
					"className": 'text-center',
				},
				{
					"data": "sat_barang",
					"className": 'text-center',
				},
				{
					"data": "hrgsat_barang",
					"className": 'text-right',
					"render": function(data, type, row) {
						return formatRupiah(data);
					}
				},
				{
					"data": "hrgjual_barang",
					"className": 'text-right',
					"render": function(data, type, row) {
						return formatRupiah(data);
					}
				},
				{
					"data": "pilih",
					"className": 'text-center'
				},
			],
			"footerCallback": function(row, data, start, end, display) {
				// console.log(row);
			}
		})

	});


	$(document).on('click', '#pilihbarang', function() {

		var id_barang = $(this).data('id_barang');
		var kd_barang = $(this).data('kd_barang');
		var nm_barang = $(this).data('nm_barang');
		var stok_barang = $(this).data('stok_barang');
		var sat_barang = $(this).data('sat_barang');
		var hrgsat_barang = $(this).data('hrgsat_barang');
		var hrgjual_barang = $(this).data('hrgjual_barang');
		var hrgjual_barang_resep = $(this).data('hrgjual_barang1');
		var hrgjual_barang_nakes = $(this).data('hrgjual_barang2');
		var qty_default = "1";

		document.getElementById('id_barang').value = id_barang;
		document.getElementById('kd_barang').value = kd_barang;
		document.getElementById('nmbrg_dtrbmasuk').value = nm_barang;
		document.getElementById('stok_barang').value = stok_barang;
		document.getElementById('qty_dtrbmasuk').value = qty_default;
		document.getElementById('sat_dtrbmasuk').value = sat_barang;
		document.getElementById('hrgsat_dtrbmasuk').value = hrgsat_barang;
		document.getElementById('hrgjual_dtrbmasuk').value = hrgjual_barang;
		document.getElementById('hrgjual_dtrbmasuk_resep').value = hrgjual_barang_resep;
		document.getElementById('hrgjual_dtrbmasuk_nakes').value = hrgjual_barang_nakes;
		//hilangkan modal
		$(".close").click();
		
		console.log(hrgjual_barang + ' - ' + hrgjual_barang_resep + ' - ' + hrgjual_barang_nakes)

	});


	$(document).on('click', '#pilihsupplier', function() {

		var id_supplier = $(this).data('id_supplier');
		var nm_supplier = $(this).data('nm_supplier');
		var tlp_supplier = $(this).data('tlp_supplier');
		var alamat_supplier = $(this).data('alamat_supplier');

		document.getElementById('id_supplier').value = id_supplier;
		document.getElementById('nm_supplier').value = nm_supplier;
		document.getElementById('tlp_supplier').value = tlp_supplier;
		document.getElementById('alamat_supplier').value = alamat_supplier;
		//hilangkan modal
		$(".close").click();
	});

	function simpan_detail() {

		var kd_trbmasuk = document.getElementById('kd_trbmasuk').value;
		var id_barang = document.getElementById('id_barang').value;
		var kd_barang = document.getElementById('kd_barang').value;
		var nmbrg_dtrbmasuk = document.getElementById('nmbrg_dtrbmasuk').value;
		var stok_barang = document.getElementById('stok_barang').value;
		var qty_dtrbmasuk = document.getElementById('qty_dtrbmasuk').value;
		var sat_dtrbmasuk = document.getElementById('sat_dtrbmasuk').value;
		var hrgsat_dtrbmasuk = document.getElementById('hrgsat_dtrbmasuk').value;
		var hrgjual_dtrbmasuk = document.getElementById('hrgjual_dtrbmasuk').value;
		var hrgjual_dtrbmasuk_resep = document.getElementById('hrgjual_dtrbmasuk_resep').value;
		var hrgjual_dtrbmasuk_nakes = document.getElementById('hrgjual_dtrbmasuk_nakes').value;
		
		var no_batch = document.getElementById('no_batch').value;
		var exp_date = document.getElementById('exp_date').value;
		var tgl_trbmasuk = document.getElementById('tgl_trbmasuk').value;
		var min_exp_date = document.getElementById('min_exp_date').value;
        
        const tglAwal = new Date(tgl_trbmasuk);
        const tglAkhir = new Date(exp_date);
        const selisih = hitungSelisihBulan(tglAwal, tglAkhir);
        
        if(parseInt(selisih) < parseInt(min_exp_date)){
            alert('Minimum Expired Date '+min_exp_date+' Hari dari Hari Ini!');
            return false;
        }
		
			
		if (nmbrg_dtrbmasuk == "") {
			alert('Belum ada Item terpilih');
		} else if (qty_dtrbmasuk == "") {
			alert('Qty tidak boleh kosong');
		} else if (hrgsat_dtrbmasuk == "") {
			alert('Harga tidak boleh kosong');
		} else if (no_batch == "") {
			alert('No. Batch tidak boleh kosong');
		} else if (exp_date == "") {
			alert('Exp. date tidak boleh kosong');
		} else {

			//cek stok barang apakah cukup
			// if (stok_barang < qty_dtrbmasuk) {
			// 	alert('Stok barang tidak mencukupi');
			// } else {

			$.ajax({

				type: 'post',
				url: "modul/mod_trbmasuk/simpandetail_tbm.php",
				data: {
					'kd_trbmasuk': kd_trbmasuk,
					'id_barang': id_barang,
					'kd_barang': kd_barang,
					'nmbrg_dtrbmasuk': nmbrg_dtrbmasuk,
					'qty_dtrbmasuk': qty_dtrbmasuk,
					'sat_dtrbmasuk': sat_dtrbmasuk,
					'hrgsat_dtrbmasuk': hrgsat_dtrbmasuk,
					'hrgjual_dtrbmasuk': hrgjual_dtrbmasuk,
					'hrgjual_dtrbmasuk_resep': hrgjual_dtrbmasuk_resep,
					'hrgjual_dtrbmasuk_nakes': hrgjual_dtrbmasuk_nakes,
					'no_batch': no_batch,
					'exp_date': exp_date
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
					document.getElementById("hrgjual_dtrbmasuk_resep").value = "";
					document.getElementById("hrgjual_dtrbmasuk_nakes").value = "";
					
					document.getElementById("no_batch").value = "";
					document.getElementById("exp_date").value = "";
					tabel_detail();
				}
			});
			// }
		}



	}


	$(document).on('click', '#hapusdetail', function() {

		var id_dtrbmasuk = $(this).data('id_dtrbmasuk');
        $.ajax({
			type: 'post',
			url: "modul/mod_trbmasuk/hapusdetail_tbm.php",
			data: {
				id_dtrbmasuk: id_dtrbmasuk
			},
			success: function(data) {
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
			data: {
				'kd_trbmasuk': kd_trbmasuk
			},
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
				data: {
					'kd_brg': kd_brg
				},
			}).success(function(data) {

				var json = data;
				//replace array [] menjadi '' 
				var res1 = json.replace("[", "");
				var res2 = res1.replace("]", "");
				//INI CONTOH ARRAY JASON const json = '{"result":true, "count":42}';
				datab = JSON.parse(res2);
				document.getElementById('id_barang').value = datab.id_barang;
    			document.getElementById('nmbrg_dtrbmasuk').value = datab.nm_barang;
    			document.getElementById('stok_barang').value = datab.stok_barang;
    			document.getElementById('qty_dtrbmasuk').value = "1";
    			document.getElementById('sat_dtrbmasuk').value = datab.sat_barang;
    			document.getElementById('hrgsat_dtrbmasuk').value = datab.hrgsat_barang;
    			
    			document.getElementById('hrgjual_dtrbmasuk').value = datab.hrgjual_barang;
    			document.getElementById('hrgjual_dtrbmasuk_resep').value = datab.hrgjual_barang1;
    			document.getElementById('hrgjual_dtrbmasuk_nakes').value = datab.hrgjual_barang2;
			});

		}
	});


	function simpan_transaksi() {

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

		if (nm_supplier == "") {
			alert('Belum ada data supplier');
		} else {

			$.ajax({

				type: 'post',
				url: "modul/mod_trbmasuk/aksi_trbmasuk.php",

				data: {
					'id_trbmasuk': id_trbmasuk,
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
					'carabayar': carabayar
				},
				success: function(data) {
					alert('Proses berhasil !');
					window.location = 'media_admin.php?module=trbmasuk';
				}
			});
		}
	}
	
	function hitungSelisihBulan(date1, date2) {
        // let tahun1 = date1.getFullYear();
        // let bulan1 = date1.getMonth();
        // let hari1  = date1.getDate();
        // let tahun2 = date2.getFullYear();
        // let bulan2 = date2.getMonth();
        // let hari2  = date2.getDate();
    
        // // return (tahun2 - tahun1) * 12 + (bulan2 - bulan1);
        // return (hari2 - hari1);
        
        // Menghitung selisih milidetik
        const selisihMilidetik = Math.abs(date2 - date1);
        
        // Konversi milidetik ke hari
        const satuHari = 1000 * 60 * 60 * 24;
        return Math.floor(selisihMilidetik / satuHari);
    }
</script>
<script src="assets/js/html5-qrcode.min.js" type="text/javascript"></script>
<script>
    var AUTOCOMPLETE_LIMIT = 8;

    function parseJsonSafe(data) {
        if (typeof data === 'string') {
            try {
                return $.parseJSON(data);
            } catch (e) {
                return [];
            }
        }
        return data || [];
    }

    function normalizeAutocompleteItems(data) {
        var items = parseJsonSafe(data);
        if (!$.isArray(items)) {
            return [];
        }
        return items.slice(0, AUTOCOMPLETE_LIMIT);
    }

    function closeAutocompleteSuggestions(selector) {
        var $input = $(selector);

        if ($.ui && $.ui.autocomplete && $input.data('ui-autocomplete')) {
            $input.autocomplete('close');
        }

        $('.tt-menu, .ui-autocomplete, .dropdown-menu.typeahead, ul.typeahead.dropdown-menu').hide();

        $input.focus();
    }

    var scannerStream = null;
    var scannerInterval = null;
    var barcodeDetectorInstance = null;
    var html5QrScanner = null;
    var html5QrScannerActive = false;
    var barcodeScanLocked = false;
    var scannerMode = null;
    var html5QrScriptPromise = null;

    function setScannerStatus(message, isError) {
        var statusEl = document.getElementById('barcodeScannerStatus');
        if (!statusEl) {
            return;
        }
        statusEl.innerText = message;
        statusEl.style.color = isError ? '#b90000' : '#666';
    }

    function stopBarcodeScanner() {
        barcodeScanLocked = false;

        if (scannerInterval) {
            clearInterval(scannerInterval);
            scannerInterval = null;
        }

        if (html5QrScanner && html5QrScannerActive) {
            try {
                html5QrScanner.stop().then(function() {
                    html5QrScanner.clear();
                }).catch(function() {
                    try {
                        html5QrScanner.clear();
                    } catch (e) {}
                });
            } catch (e) {}
        }
        html5QrScannerActive = false;
        scannerMode = null;

        if (scannerStream) {
            scannerStream.getTracks().forEach(function(track) {
                track.stop();
            });
            scannerStream = null;
        }

        var video = document.getElementById('barcodeScannerPreview');
        if (video) {
            video.srcObject = null;
            video.style.display = 'none';
        }

        var reader = document.getElementById('barcodeScannerReader');
        if (reader) {
            reader.style.display = 'block';
        }
    }

    function triggerBarangByKode(kd_brg) {
        if (!kd_brg) {
            return;
        }

        $('#kd_barang').val(kd_brg);
        
        $.ajax({
			url: 'modul/mod_trbmasuk/autobarang.php',
			type: 'post',
			data: {
				'kd_brg': kd_brg
			},
		}).success(function(data) {

			var json = data;
			//replace array [] menjadi '' 
			var res1 = json.replace("[", "");
			var res2 = res1.replace("]", "");
			//INI CONTOH ARRAY JASON const json = '{"result":true, "count":42}';
			datab = JSON.parse(res2);
			document.getElementById('id_barang').value = datab.id_barang;
			document.getElementById('nmbrg_dtrbmasuk').value = datab.nm_barang;
			document.getElementById('stok_barang').value = datab.stok_barang;
			document.getElementById('qty_dtrbmasuk').value = "1";
			document.getElementById('sat_dtrbmasuk').value = datab.sat_barang;
			document.getElementById('hrgsat_dtrbmasuk').value = datab.hrgsat_barang;
			
			document.getElementById('hrgjual_dtrbmasuk').value = datab.hrgjual_barang;
			document.getElementById('hrgjual_dtrbmasuk_resep').value = datab.hrgjual_barang1;
			document.getElementById('hrgjual_dtrbmasuk_nakes').value = datab.hrgjual_barang2;
		});
    }

    function loadHtml5QrcodeScript() {
        if (window.Html5Qrcode) {
            return Promise.resolve();
        }

        if (html5QrScriptPromise) {
            return html5QrScriptPromise;
        }

        html5QrScriptPromise = new Promise(function(resolve, reject) {
            var script = document.createElement('script');
            script.src = 'assets/js/html5-qrcode.min.js';
            script.async = true;
            script.onload = function() {
                resolve();
            };
            script.onerror = function() {
                reject(new Error('Gagal memuat html5-qrcode'));
            };
            document.head.appendChild(script);
        });

        return html5QrScriptPromise;
    }

    async function startHtml5QrcodeScanner() {
        if (!window.Html5Qrcode) {
            throw new Error('html5-qrcode belum tersedia');
        }

        var video = document.getElementById('barcodeScannerPreview');
        var reader = document.getElementById('barcodeScannerReader');
        if (video) {
            video.style.display = 'none';
        }
        if (reader) {
            reader.style.display = 'block';
        }

        html5QrScanner = new Html5Qrcode('barcodeScannerReader');
        var config = {
            fps: 10,
            qrbox: {
                width: 260,
                height: 120
            },
            aspectRatio: 1.7778,
            formatsToSupport: [
                Html5QrcodeSupportedFormats.CODE_128,
                Html5QrcodeSupportedFormats.EAN_13,
                Html5QrcodeSupportedFormats.EAN_8,
                Html5QrcodeSupportedFormats.UPC_A,
                Html5QrcodeSupportedFormats.UPC_E,
                Html5QrcodeSupportedFormats.CODABAR,
                Html5QrcodeSupportedFormats.CODE_39,
                Html5QrcodeSupportedFormats.CODE_93,
                Html5QrcodeSupportedFormats.ITF,
                Html5QrcodeSupportedFormats.QR_CODE
            ],
            experimentalFeatures: {
                useBarCodeDetectorIfSupported: true
            }
        };

        await html5QrScanner.start({
            facingMode: {
                ideal: 'environment'
            }
        }, config, function(decodedText) {
            if (barcodeScanLocked) {
                return;
            }

            barcodeScanLocked = true;
            var hasilScan = $.trim(decodedText || '');
            if (!hasilScan) {
                barcodeScanLocked = false;
                return;
            }

            setScannerStatus('Barcode terdeteksi: ' + hasilScan, false);
            triggerBarangByKode(hasilScan);
            $('#ModalScanBarcode').modal('hide');
        }, function() {
            // ignore per-frame decode error
        });

        html5QrScannerActive = true;
        scannerMode = 'html5-qrcode';
        setScannerStatus('Scanner aktif. Arahkan barcode ke area kamera.', false);
    }

    async function startBarcodeDetectorScanner() {

        if (!window.BarcodeDetector) {
            setScannerStatus('Browser tidak support BarcodeDetector.', true);
            return;
        }
    
        //var video = document.getElementById('barcodeScannerPreview');
        var video = document.getElementById('barcodeScannerPreview');
        var reader = document.getElementById('barcodeScannerReader');
        if (reader) {
            reader.style.display = 'none';
        }
        if (video) {
            video.style.display = 'block';
        }
        barcodeDetectorInstance = new BarcodeDetector({
            formats: ['code_128','ean_13','ean_8','qr_code']
        });
    
        scannerStream = await navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: { ideal: 'environment' },
                width: { ideal: 720 },
                height: { ideal: 1280 }
            }
        });
    
        video.srcObject = scannerStream;
        await video.play();
    
        scannerInterval = setInterval(async function(){
    
            if (!video || video.readyState !== video.HAVE_ENOUGH_DATA) {
                return;
            }
    
            try {
    
                const detected = await barcodeDetectorInstance.detect(video);
    
                if (detected.length > 0) {
    
                    const hasilScan = detected[0].rawValue;
    
                    barcodeScanLocked = true;
                    clearInterval(scannerInterval);
    
                    setScannerStatus('Barcode: ' + hasilScan, false);
    
                    // $('#ModalScanBarcode').modal('hide');
                    $('#ModalScanBarcode .close').click();
                    triggerBarangByKode(hasilScan);
    
                }
    
            } catch(err) {
                console.log("scan error", err);
            }
    
        }, 600);
    }

    async function startBarcodeScanner() {
        stopBarcodeScanner();
        setScannerStatus('Menyiapkan scanner kamera...', false);

        try {
            await loadHtml5QrcodeScript();
            await startHtml5QrcodeScanner();
        } catch (err) {
            setScannerStatus('Fallback ke mode scanner bawaan browser...', false);
            await startBarcodeDetectorScanner();
        }
    }


    $('#ModalScanBarcode').on('shown.bs.modal', function() {
        startBarcodeScanner();
    });

    $('#ModalScanBarcode').on('hidden.bs.modal', function() {
        stopBarcodeScanner();
    });
</script>