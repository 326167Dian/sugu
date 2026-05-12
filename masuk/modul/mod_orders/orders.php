<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['username']) and empty($_SESSION['passuser'])) {
	echo "<link href=../css/style.css rel=stylesheet type=text/css>";
	echo "<div class='error msg'>Untuk mengakses Modul anda harus login.</div>";
} else {

	require_once __DIR__ . '/../../../configurasi/koneksi.php';

	$aksi = "modul/mod_orders/aksi_orders.php";
	$aksi_trbmasuk = "masuk/modul/mod_orders/aksi_orders.php";
	switch (isset($_GET['act']) ? $_GET['act'] : '') {
			// Tampil barang
		default:
			$tampil_trbmasuk = $db->query("SELECT * FROM orders 
        	  WHERE id_resto = 'pesan'
        	  ORDER BY orders.id_trbmasuk DESC");

?>


			<div class="box box-primary box-solid">
				<div class="box-header with-border">
					<h3 class="box-title">PESAN BARANG -> <a href="https://youtu.be/U4XyHQUffgA" target="_blanks">TONTON VIDEO</a></h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div><!-- /.box-tools -->
				</div>
				<div class="box-body table-responsive">
					<a class='btn  btn-success btn-flat' href='?module=orders&act=tambah'>(F4) TAMBAH</a>
					<div></div>
					<p>
					<p>
						<!--	<a  class ='btn  btn-warning  btn-flat' href='#'></a>
					<small>* Pembayaran belum lunas</small>
					<br><br> -->

                    <hr>
					<table id="tes_table" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>No</th>
								<th>Petugas</th>
								<th>Kode</th>
								<th>Tanggal</th>
								<th>Supplier</th>
								<th>Jenis Pesanan</th>
								<th>Sub Total</th>
								<th>Diskon</th>
								<th>Total Bayar</th>
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
							// 	/*
							// 		if($r['sisa_bayar'] <= "0"){
							// 				echo"
							// 					<td>$no</td>           
							// 					<td>$r[kd_trbmasuk]</td>
							// 				";
							// 			}else{

							// 				echo"
							// 					<td style='background-color:#ffbf00;'>$no</td>           
							// 					<td style='background-color:#ffbf00;'>$r[kd_trbmasuk]</td>
							// 				";

							// 			} */
							// 	echo "
							//                  <td>$no</td>           
							// 				 <td>$r[kd_trbmasuk]</td>
							// 				 <td>$r[tgl_trbmasuk]</td>
							// 				 <td>$r[nm_supplier]</td>
							// 				 <td>$r[tlp_supplier]</td>
							// 				 <td align=right>$ttl_trbmasuknya</td>
							// 				<td align=right>$dp_bayar</td>
							// 				<td align=right>$sisa_bayar</td>											 
							// 				 <td><a href='?module=orders&act=ubah&id=$r[id_trbmasuk]' title='EDIT' class='btn btn-warning btn-xs'>EDIT</a> 
							// 				 <a href=javascript:confirmdelete('$aksi?module=orders&act=hapus&id=$r[id_trbmasuk]') title='HAPUS' class='btn btn-danger btn-xs'>HAPUS</a>

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
            		$("#tes_table").DataTable({
            			processing: true,
            			serverSide: true,
            			ajax: {
            				"url": "modul/mod_orders/orders_serverside.php?action=table_data",
            				"dataType": "JSON",
            				"type": "POST"
            			},
            			"rowCallback": function(row, data, index) {
                            console.log(data);
                        },
            			columns: [{
            				"data": "no",
            				"className": "text-center"
            			},
            			{
            				"data": "petugas",
            				"className": "text-left"
            			},
            			{
            				"data": "kd_trbmasuk",
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
            				"data": "ttl_trbmasuk",
            				"className": "text-right",
            				"render": function(data, type, row) {
            					return formatRupiah(data);
            	    		}
            			},
            			{
            				"data": "dp_bayar",
            				"className": "text-right",
            				"render": function(data, type, row) {
            					return formatRupiah(data);
            				}
            			},
            			{
            				"data": "sisa_bayar",
            				"className": "text-right",
            				"render": function(data, type, row) {
            					return formatRupiah(data);
            				}
            			},
            			{
            				"data": "aksi",
            				"className": "text-center"
            			}]
            		});
            	});
            </script>
<?php

			break;

		case "tambah":
			//cek apakah ada kode transaksi ON berdasarkan user
			$cekkd = $db->prepare("SELECT * FROM kdbm WHERE id_admin=? AND id_resto='pesan' AND stt_kdbm='ON'");
			$cekkd->execute([$_SESSION['idadmin']]);
			$ketemucekkd = $cekkd->rowCount();
			$hcekkd = $cekkd->fetch(PDO::FETCH_ASSOC);
			$petugas = $_SESSION['namalengkap'];

			if ($ketemucekkd > 0) {
				$kdtransaksi = $hcekkd['kd_trbmasuk'];
			} else {
				$kdunik = date('dmyhis');
				$kdtransaksi = "ORD-" . $kdunik;
				$cekkd2 = $db->prepare("SELECT * FROM kdbm WHERE kd_trbmasuk=?");
			    $cekkd2->execute([$kdtransaksi]);
			    $ketemucekkd2 = $cekkd2->rowCount();
			    if ($ketemucekkd2 > 0) {
			        $kdunik2 = date('dmyhis')+1;
				    $kdtransaksi = "ORD-" . $kdunik2;
			    }
				$stmt = $db->prepare("INSERT INTO kdbm(kd_trbmasuk,id_resto,id_admin) VALUES(?,?,?)");
				$stmt->execute([$kdtransaksi, 'pesan', $_SESSION['idadmin']]);
			}

			$tglharini = date('Y-m-d');
			$tgl_akhir = date('Y-m-d');
			$tgl_awal = date('Y-m-d', strtotime('-30 days', strtotime($tgl_awal)));

			// Initialize $re as empty array for new entry
			$re = [];

			echo "
		  <div class='box box-primary box-solid'>
				<div class='box-header with-border'>
					<h3 class='box-title'>TAMBAH PESANAN BARANG</h3>
					<div class='box-tools pull-right'>
						<button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
                    </div><!-- /.box-tools -->
				</div>
				<div class='box-body table-responsive'>
				
						<form onsubmit='return false;' method=POST action='$aksi?module=orders&act=input_trbmasuk' enctype='multipart/form-data' class='form-horizontal'>
						
						        <input type=hidden name='id_trbmasuk' id='id_trbmasuk' value=''>
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
										
									<label class='col-sm-4 control-label'>Jenis Pesanan</label>        		
										<div class='col-sm-6'>
											<select name='ket_trbmasuk' id='ket_trbmasuk' class='form-control' >
												<option value='REGULER'>REGULER </option>
												<option value='PREKURSOR'>PREKURSOR </option>
												<option value='OOT'>OOT</option>
												<option value='ALKES'>ALKES</option>
											</select>
										</div>

									<label class='col-sm-4 control-label'>Tanda Tangan Digital</label>        		
										<div class='col-sm-6'>
										<select name='tandatangan' id='tandatangan' class='form-control' >
											<option value='TIDAK'>TIDAK </option>
											<option value='YA'>YA </option>
										</select>
											</p>
											<div class='buttons'>
												<button type='button' class='btn btn-primary right-block' onclick='simpan_transaksi();'>(F3) SIMPAN TRANSAKSI</button>
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
														<button type=button data-toggle='modal' data-target='#ModalItem' id='kode' href='#'><span class='glyphicon glyphicon-search'></span></button>
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
										
									<label class='col-sm-4 control-label'>Qty Ecer</label>        		
										<div class='col-sm-7'>
											<input type='number' name='qty_dtrbmasuk' id='qty_dtrbmasuk' class='form-control' autocomplete='off'>
										</div>
										
										
									<label class='col-sm-4 control-label'>Satuan Ecer</label>        		
										<div class='col-sm-7'>
											<input type=text name='sat_dtrbmasuk' id='sat_dtrbmasuk' class='form-control' autocomplete='off' readonly>
										</div>
										
									<label class='col-sm-4 control-label'>Konversi</label>        		
										<div class='col-sm-7'>
											<input type=text name='konversi' id='konversi' class='form-control' autocomplete='off'>
										</div>
										
									<label class='col-sm-4 control-label'>Qty Grosir</label>        		
										<div class='col-sm-7'>
											<input type='number' name='qtygrosir_dtrbmasuk' id='qtygrosir_dtrbmasuk' class='form-control' autocomplete='off'>
										</div>

								    <label class='col-sm-4 control-label'>Satuan Grosir</label>        		
									 <div class='col-sm-7'>
										<select name='satgrosir_dtrbmasuk' id='satgrosir_dtrbmasuk' class='form-control' >";
                                        $tampil = $db->query("SELECT * FROM satuan ORDER BY nm_satuan ASC");
                                        while ($rk = $tampil->fetch(PDO::FETCH_ASSOC)) {
                                            echo "<option value=$rk[nm_satuan]>$rk[nm_satuan]</option>";
                                        }
                                        echo "
                                        </select>
									 </div>
							
									
										
									<label class='col-sm-4 control-label'>Harga Beli</label>        		
										<div class='col-sm-7'>
											<input type=text name='hrgsat_dtrbmasuk' id='hrgsat_dtrbmasuk' class='form-control' autocomplete='off'>
											</p>
												<div class='buttons'>
													<button type='button' class='btn btn-success right-block' onclick='simpan_detail();'>(F1)SIMPAN DETAIL</button>
												</div>
										</div>
										
										
								</div>
								
									
						</div>
						</form>
							  
				</div> 
				
				<div id='tabeldata'>
				
			</div>";
			?>
			<script>
				$(document).ready(function() {
					tabel_detail();
				});
			</script>
			<?php

			break;

		case "ubah":
			//cek apakah ada kode transaksi ON berdasarkan user
			$ubah = $db->prepare("SELECT * FROM orders 
	WHERE orders.id_trbmasuk=?");
			$ubah->execute([$_GET['id']]);
			$re = $ubah->fetch(PDO::FETCH_ASSOC);

            $tglharini = date('Y-m-d');
			$tgl_akhir = date('Y-m-d');
			$tgl_awal = date('Y-m-d', strtotime('-30 days', strtotime($tgl_awal)));

			echo "
		  <div class='box box-primary box-solid'>
				<div class='box-header with-border'>
					<h3 class='box-title'>UBAH PESANAN BARANG</h3>
					<div class='box-tools pull-right'>
						<button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
                    </div><!-- /.box-tools -->
				</div>
				<div class='box-body table-responsive'>
				
						<form onsubmit='return false;' method=POST action='$aksi?module=orders&act=ubah_trbmasuk' enctype='multipart/form-data' class='form-horizontal'>
						
						       <input type='hidden' name='id_trbmasuk' id='id_trbmasuk' value='$re[id_trbmasuk]'>
							   <input type='hidden' name='kd_trbmasuk' id='kd_trbmasuk' value='$re[kd_trbmasuk]'>
							   <input type='hidden' name='stt_aksi' id='stt_aksi' value='ubah_trbmasuk'>
							   <input type='hidden' name='id_supplier' id='id_supplier' value='$re[id_supplier]'>
							   <input type='hidden' name='petugas' id='petugas' value='$re[petugas]'>
							 
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
											<input type=text name='tlp_supplier' id='tlp_supplier' value='$re[tlp_supplier]' class='form-control' autocomplete='off'>
										</div>
										
									<label class='col-sm-4 control-label'>Alamat</label>        		
										<div class='col-sm-6'>
											<textarea name='alamat_supplier' id='alamat_supplier' class='form-control' rows='2'>$re[alamat_trbmasuk]</textarea>
										</div>
										
									<label class='col-sm-4 control-label'>Jenis Pesanan</label>        		
										<div class='col-sm-6'>
											<textarea name='ket_trbmasuk' id='ket_trbmasuk' class='form-control' rows='2'>$re[ket_trbmasuk]</textarea>
										</div>
									<label class='col-sm-4 control-label'>Tanda Tangan Digital</label>        		
										<div class='col-sm-6'>											
												<select name='tandatangan' id='tandatangan' class='form-control' >
													<option value=$re[tandatangan]>$re[tandatangan]</option>													
													<option value='TIDAK'>TIDAK </option>
													<option value='YA'>YA </option>
												</select>
											</p>
											<div class='buttons'>
											  <button type='button' class='btn btn-primary right-block' onclick='simpan_transaksi();'>(F3) SIMPAN TRANSAKSI</button>
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
														<button type=button data-toggle='modal' data-target='#ModalItem' id='kode' href='#'><span class='glyphicon glyphicon-search'></span></button>
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
										
									<label class='col-sm-4 control-label'>Qty Ecer</label>        		
										<div class='col-sm-7'>
											<input type='number' name='qty_dtrbmasuk' id='qty_dtrbmasuk' class='form-control' autocomplete='off'>
										</div>
										
										
									<label class='col-sm-4 control-label'>Satuan Ecer</label>        		
										<div class='col-sm-7'>
											<input type=text name='sat_dtrbmasuk' id='sat_dtrbmasuk' class='form-control' autocomplete='off'>
										</div>
									
									<label class='col-sm-4 control-label'>Konversi</label>        		
										<div class='col-sm-7'>
											<input type=text name='konversi' id='konversi' class='form-control' autocomplete='off'>
										</div>
										
									<label class='col-sm-4 control-label'>Qty Grosir</label>        		
										<div class='col-sm-7'>
											<input type='number' name='qtygrosir_dtrbmasuk' id='qtygrosir_dtrbmasuk' class='form-control' autocomplete='off'>
										</div>

									<label class='col-sm-4 control-label'>Satuan Grosir</label>        		
										<div class='col-sm-7'>
											<input type=text name='satgrosir_dtrbmasuk' id='satgrosir_dtrbmasuk' class='form-control' autocomplete='off'>
										</div>
										
									<label class='col-sm-4 control-label'>Harga Beli</label>        		
										<div class='col-sm-7'>
											<input type=text name='hrgsat_dtrbmasuk' id='hrgsat_dtrbmasuk' class='form-control' autocomplete='off'>
											</p>
												<div class='buttons'>
													<button type='button' class='btn btn-success right-block' onclick='simpan_detail();'>(F1) SIMPAN DETAIL</button>
												</div>
										</div>
										
								</div>
								
								
								
								
						
						</div>
						</form>
							  
				</div> 
				
				<div id='tabeldata'>
				
			</div>";
			?>
			<script>
				$(document).ready(function() {
					tabel_detail();
				});
			</script>
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
			<CENTER><strong>MySIFA STATUS TRAFFIC ORDER </strong></CENTER><br>
			<center><button type="button" class="btn btn-info">LAKU</button>
				<button type="button" class="btn btn-success">LANCAR </button>
				<button type="button" class="btn btn-warning">SLOW</button>
				<button type="button" class="btn btn-danger">MACET</button>
			</center>
			<div class="modal-body table-responsive">
				<table id="tes" class="table table-condensed table-bordered table-striped table-hover">

					<thead>
						<tr class="judul-table">
							<th style="vertical-align: middle; background-color: #008000; text-align: center; ">No</th>
							<th style="vertical-align: middle; background-color: #008000; text-align: left; ">Kode</th>
							<th style="vertical-align: middle; background-color: #008000; text-align: left; ">Nama Barang</th>
							<th style="vertical-align: middle; background-color: #008000; text-align: right; ">Qty</th>
							<th style="vertical-align: middle; background-color: #008000; text-align: center; ">Satuan</th>
							<th style="vertical-align: middle; background-color: #008000; text-align: center; ">T30</th>
							<th style="vertical-align: middle; background-color: #008000; text-align: center; ">Q30</th>
							<th style="vertical-align: middle; background-color: #008000; text-align: center; ">SF</th>
							<th style="vertical-align: middle; background-color: #008000; text-align: right; ">Harga Beli</th>
							<th style="vertical-align: middle; background-color: #008000; text-align: center; ">Pilih</th>
						</tr>
					</thead>
					<tbody>
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
				<table id="example1" class="table table-condensed table-bordered table-striped table-hover">

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
						echo "</tbody></table>";
						?>
			</div>
		</div>
	</div>
</div>
<!-- end modul supplier -->


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
		$("#tes_table").DataTable({
			processing: true,
			serverSide: true,
			ajax: {
				"url": "modul/mod_orders/orders_serverside.php?action=table_data",
				"dataType": "JSON",
				"type": "POST"
			},
			"rowCallback": function(row, data, index) {
                console.log(data);
            },
			columns: [{
				"data": "no",
				"className": "text-center"
			},
			{
				"data": "petugas",
				"className": "text-left"
			},
			{
				"data": "kd_trbmasuk",
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
				"data": "ttl_trbmasuk",
				"className": "text-right",
				"render": function(data, type, row) {
					return formatRupiah(data);
	    		}
			},
			{
				"data": "dp_bayar",
				"className": "text-right",
				"render": function(data, type, row) {
					return formatRupiah(data);
				}
			},
			{
				"data": "sisa_bayar",
				"className": "text-right",
				"render": function(data, type, row) {
					return formatRupiah(data);
				}
			},
			{
				"data": "aksi",
				"className": "text-center"
			}]
		});
	});
	
	// item barang per supplie
	$(document).on('click', '#kode', function() {
		$("#tes").DataTable().destroy();
		var start = '<?= $tgl_awal ?>';
		var finish = '<?= $tgl_akhir ?>';
		var idsuplier = document.getElementById('id_supplier').value;

		$('#tes').DataTable({
			processing: true,
			serverSide: true,
			ajax: {
				"url": "modul/mod_orders/barang-serverside.php?action=table_data&start=" + start + "&finish=" + finish + "&id=" + idsuplier,
				"dataType": "JSON",
				"type": "POST"
			},
			"rowCallback": function(row, data, index) {
				// warna for nomor
				if (data['stok_barang'] == 0 && data['q30'] > 0) {
					$(row).find('td:eq(3)').css('background-color', '#ff003f');

				} else if (data['stok_barang'] > 0 && (data['stok_barang'] <= (data['q30'] / 2))) {
					$(row).find('td:eq(3)').css('background-color', '#EDFF00');

				} else if (data['stok_barang'] <= 0 && data['q30'] <= 0) {
					$(row).find('td:eq(3)').css('background-color', '#bcbcbc');

				}

			},
			columns: [{
					"data": "no",
					"className": 'text-center',
				},
				{
					"data": "kd_barang",
					"className": 'text-center',
				},
				{
					"data": "nm_barang"
				},
				{
					"data": "stok_barang",
					"className": 'text-center',
				},
				{
					"data": "satuan",
					"className": 'text-center',
				},
				{
					"data": "t30",
					"className": 'text-center',
				},
				{
					"data": "q30",
					"className": 'text-center',
				},
				{
					"data": "sf",
					"className": 'text-center',
				},
				{
					"data": "harga_beli",
					"className": 'text-right',
					"render": function(data, type, row) {
						return formatRupiah(data);
					}
				},
				{
					"data": "pilih",
					"className": 'text-center',
				},
			],

		});

	});
</script>

<script>
	//auto nama barang
	$('#nmbrg_dtrbmasuk').typeahead({
		source: function(query, process) {
			return $.post('modul/mod_orders/autonamabarang.php', {
				query: query
			}, function(data) {

				data = $.parseJSON(data);
				return process(data);

			});
		}
	});

    // event enter nama obat
	$('#nmbrg_dtrbmasuk').keydown(function(e) {
		if (e.which == 13) {
			//letakan fungsi anda disini

			var nm_barang = $("#nmbrg_dtrbmasuk").val();
			$.ajax({
				url: 'modul/mod_orders/autonamabarang_enter.php',
				type: 'post',
				data: {
					'nm_barang': nm_barang
				},
			}).success(function(data) {

				var json = data;
				//replace array [] menjadi '' 
				var res1 = json.replace("[", "");
				var res2 = res1.replace("]", "");
				//INI CONTOH ARRAY JASON const json = '{"result":true, "count":42}';
				var datab = JSON.parse(res2);
				document.getElementById('id_barang').value = datab.id_barang;
				document.getElementById('kd_barang').value = datab.kd_barang;
				document.getElementById('nmbrg_dtrbmasuk').value = datab.nm_barang;
				document.getElementById('stok_barang').value = datab.stok_barang;
				document.getElementById('qty_dtrbmasuk').value = datab.konversi;
				document.getElementById('sat_dtrbmasuk').value = datab.sat_barang;
				document.getElementById('qtygrosir_dtrbmasuk').value = '1';
				document.getElementById('satgrosir_dtrbmasuk').value = datab.sat_grosir;
				document.getElementById('konversi').value = datab.konversi;
				document.getElementById('hrgsat_dtrbmasuk').value = datab.hrgsat_barang;

			});

		}
	});
	
	// event klik nama obat
	$('#nmbrg_dtrbmasuk_enter').on('click', function() {
        var nm_barang = $("#nmbrg_dtrbmasuk").val();
		$.ajax({
			url: 'modul/mod_orders/autonamabarang_enter.php',
			type: 'post',
			data: {
				'nm_barang': nm_barang
			},
		}).success(function(data) {

			var json = data;
			//replace array [] menjadi '' 
			var res1 = json.replace("[", "");
			var res2 = res1.replace("]", "");
			//INI CONTOH ARRAY JASON const json = '{"result":true, "count":42}';
			var datab = JSON.parse(res2);
			document.getElementById('id_barang').value = datab.id_barang;
			document.getElementById('kd_barang').value = datab.kd_barang;
			document.getElementById('nmbrg_dtrbmasuk').value = datab.nm_barang;
			document.getElementById('stok_barang').value = datab.stok_barang;
			document.getElementById('qty_dtrbmasuk').value = datab.konversi;
			document.getElementById('sat_dtrbmasuk').value = datab.sat_barang;
			document.getElementById('qtygrosir_dtrbmasuk').value = '1';
			document.getElementById('satgrosir_dtrbmasuk').value = datab.sat_grosir;
			document.getElementById('konversi').value = datab.konversi;
			document.getElementById('hrgsat_dtrbmasuk').value = datab.hrgsat_barang;

		});
    });

	$(document).on('click', '#pilihbarang', function() {

// 		var id_barang = $(this).data('id_barang');
// 		var kd_barang = $(this).data('kd_barang');
// 		var nm_barang = $(this).data('nm_barang');
// 		var stok_barang = $(this).data('stok_barang');
// 		var sat_barang = $(this).data('sat_barang');
// 		var hrgsat_barang = $(this).data('hrgsat_barang');
// 		var sf_barang = $(this).data('sf_barang');
// 		var qty_default = "1";

// 		document.getElementById('id_barang').value = id_barang;
// 		document.getElementById('kd_barang').value = kd_barang;
// 		document.getElementById('nmbrg_dtrbmasuk').value = nm_barang;
// 		document.getElementById('stok_barang').value = stok_barang;
// 		document.getElementById('qty_dtrbmasuk').value = sf_barang;
// 		document.getElementById('sat_dtrbmasuk').value = sat_barang;
// 		document.getElementById('hrgsat_dtrbmasuk').value = hrgsat_barang;
// 		//hilangkan modal
// 		$(".close").click();

        var nm_barang = $(this).data('nm_barang');
			$.ajax({
				url: 'modul/mod_orders/autonamabarang_enter.php',
				type: 'post',
				data: {
					'nm_barang': nm_barang
				},
			}).success(function(data) {

				var json = data;
				//replace array [] menjadi '' 
				var res1 = json.replace("[", "");
				var res2 = res1.replace("]", "");
				//INI CONTOH ARRAY JASON const json = '{"result":true, "count":42}';
				var datab = JSON.parse(res2);
				document.getElementById('id_barang').value = datab.id_barang;
				document.getElementById('kd_barang').value = datab.kd_barang;
				document.getElementById('nmbrg_dtrbmasuk').value = datab.nm_barang;
				document.getElementById('stok_barang').value = datab.stok_barang;
				document.getElementById('qty_dtrbmasuk').value = datab.konversi;
				document.getElementById('sat_dtrbmasuk').value = datab.sat_barang;
				document.getElementById('qtygrosir_dtrbmasuk').value = '1';
				document.getElementById('satgrosir_dtrbmasuk').value = datab.sat_grosir;
				document.getElementById('konversi').value = datab.konversi;
				document.getElementById('hrgsat_dtrbmasuk').value = datab.hrgsat_barang;
                $(".close").click();
			});

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

		var kd_trbmasuk         = document.getElementById('kd_trbmasuk').value;
		var id_barang           = document.getElementById('id_barang').value;
		var kd_barang           = document.getElementById('kd_barang').value;
		var nmbrg_dtrbmasuk     = document.getElementById('nmbrg_dtrbmasuk').value;
		var stok_barang         = document.getElementById('stok_barang').value;
		var qty_dtrbmasuk       = document.getElementById('qty_dtrbmasuk').value;
		var sat_dtrbmasuk       = document.getElementById('sat_dtrbmasuk').value;
		var hrgsat_dtrbmasuk    = document.getElementById('hrgsat_dtrbmasuk').value;
		var qtygrosir_dtrbmasuk = document.getElementById('qtygrosir_dtrbmasuk').value;
		var satgrosir_dtrbmasuk = document.getElementById('satgrosir_dtrbmasuk').value;
		var konversi            = document.getElementById('konversi').value;

		if (nmbrg_dtrbmasuk == "") {
			alert('Belum ada Item terpilih');
		} else if (qty_dtrbmasuk == "") {
			alert('Qty tidak boleh kosong');
		} else if (hrgsat_dtrbmasuk == "") {
			alert('Harga tidak boleh kosong');
		} else if (konversi == "0" || konversi == "") {
			alert('Konversi tidak boleh kosong');
		} else {

		

			$.ajax({

				type: 'post',
				url: "modul/mod_orders/simpandetail_tbm.php",
				data: {
					'kd_trbmasuk'           : kd_trbmasuk,
					'id_barang'             : id_barang,
					'kd_barang'             : kd_barang,
					'nmbrg_dtrbmasuk'       : nmbrg_dtrbmasuk,
					'qty_dtrbmasuk'         : qty_dtrbmasuk,
					'sat_dtrbmasuk'         : sat_dtrbmasuk,
					'hrgsat_dtrbmasuk'      : hrgsat_dtrbmasuk,
					'qtygrosir_dtrbmasuk'   : qtygrosir_dtrbmasuk,
					'satgrosir_dtrbmasuk'   : satgrosir_dtrbmasuk,
					'konversi'              : konversi
				},
				success: function(data) {
					//alert('Tambah data detail berhasil');
					document.getElementById("id_barang").value = "";
					document.getElementById("kd_barang").value = "";
					document.getElementById("nmbrg_dtrbmasuk").value = "";
					document.getElementById("qty_dtrbmasuk").value = "";
					document.getElementById("sat_dtrbmasuk").value = "";
					document.getElementById("hrgsat_dtrbmasuk").value = "";
					document.getElementById("qtygrosir_dtrbmasuk").value = "";
					document.getElementById("satgrosir_dtrbmasuk").value = "";
					document.getElementById("konversi").value = "";
					tabel_detail();
				}
			});

		}



	}


	$(document).on('click', '#hapusdetail', function() {

		var id_dtrbmasuk = $(this).data('id_dtrbmasuk');

		$.ajax({
			type: 'post',
			url: "modul/mod_orders/hapusdetail_tbm.php",
			data: {
				id_dtrbmasuk: id_dtrbmasuk
			},

			success: function() {
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
			url: 'modul/mod_orders/tbl_detail.php',
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
		if (e.which == 13) {
			//letakan fungsi anda disini

			var kd_brg = $("#kd_barang").val();
			$.ajax({
				url: 'modul/mod_orders/autobarang.php',
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
				var datab = JSON.parse(res2);
				document.getElementById('id_barang').value = datab.id_barang;
				document.getElementById('kd_barang').value = datab.kd_barang;
				document.getElementById('nmbrg_dtrbmasuk').value = datab.nm_barang;
				document.getElementById('stok_barang').value = datab.stok_barang;
				document.getElementById('qty_dtrbmasuk').value = datab.konversi;
				document.getElementById('sat_dtrbmasuk').value = datab.sat_barang;
				document.getElementById('qtygrosir_dtrbmasuk').value = '1';
				document.getElementById('satgrosir_dtrbmasuk').value = datab.sat_grosir;
				document.getElementById('konversi').value = datab.konversi;
				document.getElementById('hrgsat_dtrbmasuk').value = datab.hrgsat_barang;
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
		var tlp_supplier = document.getElementById('tlp_supplier').value;
		var alamat_trbmasuk = document.getElementById('alamat_supplier').value;
		var ket_trbmasuk = document.getElementById('ket_trbmasuk').value;
		var ttl_trkasir = document.getElementById('ttl_trkasir').value;
		var dp_bayar = document.getElementById('dp_bayar').value;
		var sisa_bayar = document.getElementById('sisa_bayar').value;
		var petugas = document.getElementById('petugas').value;
		var tandatangan = document.getElementById('tandatangan').value;

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
				url: "modul/mod_orders/aksi_orders.php",

				data: {
					'id_trbmasuk': id_trbmasuk,
					'kd_trbmasuk': kd_trbmasuk,
					'tgl_trbmasuk': tgl_trbmasuk,
					'id_supplier': id_supplier,
					'nm_supplier': nm_supplier,
					'tlp_supplier': tlp_supplier,
					'alamat_trbmasuk': alamat_trbmasuk,
					'stt_aksi': stt_aksi,
					'ket_trbmasuk': ket_trbmasuk,
					'ttl_trkasir': ttl_trkasir1x,
					'dp_bayar': dp_bayar1x,
					'sisa_bayar': sisa_bayar1x,
					'petugas': petugas,
					'tandatangan': tandatangan
				},
				success: function(data) {
					alert('Proses berhasil !');
					window.location = 'media_admin.php?module=orders';
				}
			});
		}
	}
	
	$(document).on('keyup', '#qty_dtrbmasuk', function(){
        var qty_dtrbmasuk       = $('#qty_dtrbmasuk').val();
        // var qtygrosir_dtrbmasuk = $('#qtygrosir_dtrbmasuk').val();
        var konversi            = $('#konversi').val();
        
        document.getElementById("qtygrosir_dtrbmasuk").value = (qty_dtrbmasuk/konversi);
	});
	
	$(document).on('keyup', '#qtygrosir_dtrbmasuk', function(){
        // var qty_dtrbmasuk       = $('#qty_dtrbmasuk').val();
        var qtygrosir_dtrbmasuk = $('#qtygrosir_dtrbmasuk').val();
        var konversi            = $('#konversi').val();
        
        document.getElementById("qty_dtrbmasuk").value = (qtygrosir_dtrbmasuk * konversi);
	});
	
	$(document).on('keyup', '#konversi', function(){
        // var qty_dtrbmasuk       = $('#qty_dtrbmasuk').val();
        var qtygrosir_dtrbmasuk = $('#qtygrosir_dtrbmasuk').val();
        var konversi            = $('#konversi').val();
        
        document.getElementById("qty_dtrbmasuk").value = (qtygrosir_dtrbmasuk * konversi);
	});

    document.addEventListener('keydown', function(event) {
        if (event.key === 'F1' || event.keyCode === 112) {
            event.preventDefault();
            simpan_detail();
        }
    });
	
	   
    document.addEventListener('keydown', function(event) {
        if (event.key === 'F3' || event.keyCode === 114) {
            event.preventDefault();
            simpan_transaksi();
        }
    });
    
    document.addEventListener('keydown', function(event) {
        if (event.key === 'F4' || event.keyCode === 115) {
            event.preventDefault();
            window.location = '?module=orders&act=tambah';
        }
    });

</script>