<?php
session_start();
include "../../../configurasi/koneksi.php";
include "../../../configurasi/fungsi_indotgl.php";

if (empty($_SESSION['username']) and empty($_SESSION['passuser'])) {
	echo "<link href=../css/style.css rel=stylesheet type=text/css>";
	echo "<div class='error msg'>Untuk mengakses Modul anda harus login.</div>";
} else {

	$aksi = "modul/mod_barang/aksi_barang.php";
	$aksi_barang = "masuk/modul/mod_barang/aksi_barang.php";
	switch ($_GET['act']) {
			// Tampil barang
		default:

			// $tampil_barang = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM barang ORDER BY barang.id_barang ");
        
        
?>


			<div class="box box-primary box-solid">
				<div class="box-header with-border">
					<h3 class="box-title">DATA BARANG</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div><!-- /.box-tools -->
				</div>
				<div class="box-body table-responsive">
					<a class='btn  btn-success btn-flat' href='?module=barang&act=tambah'>TAMBAH</a>
					<?php
					
					$lupa = $_SESSION['level'];
					if ($lupa == 'pemilik') {
                                echo " <a class='btn  btn-primary btn-flat' href='modul/mod_laporan/cetak_barang_excel.php' target='_blank'>EXPORT TO EXCEL</a>     
                                 <a class='btn  btn-warning btn-flat' href='modul/mod_laporan/cetak_batch.php' target='_blank'>EXPORT TO EXCEL BASED ON ACTIVE BATCH</a>
								 <a class='btn  btn-danger btn-flat' href='?module=zataktif'>Zat Aktif/Merk Obat</a>
									        ";
                            }
                            
                    
                    ?>        
					
					<hr>
					<CENTER><strong>MySIFA PROFIT ANALYSIS</strong></CENTER><br>
					<center><button type="button" class="btn btn-info">PROFIT>30%</button>
						<button type="button" class="btn btn-success">PROFIT = 25 - 30 % </button>
						<button type="button" class="btn btn-warning">PROFIT = 20 - 25%"</button>
						<button type="button" class="btn btn-danger">PROFIT < 20% </button>
					</center>
					<br><br>
					


					<table id="tes" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>No</th>
								<th>Nama Barang</th>
								<th style="text-align: center; ">Rak Obat</th>
								<th style="text-align: center; ">Stok</th>
								<th style="text-align: right; ">Harga Jual</th>
								<th style="text-align: right; ">Zat Aktif</th>
								<th style="text-align: center; ">Komposisi dan Indikasi</th>
								<!--<th style="text-align: center; ">Aksi</th>-->
								<?php
								$lupa = $_SESSION['level'];
								if ($lupa == 'pemilik') {
									echo "<th style='white-space:nowrap; width:95px; min-width:95px;'>Aksi</th> ";
								} else {
								}
								?>
							</tr>
						</thead>

					</table>

					<div id="ModalScanBarcodeBarang" class="modal fade" role="dialog" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									<h4 class="modal-title">Scan Barcode Untuk Search Barang</h4>
								</div>
								<div class="modal-body">
									<div id="barcodeScannerReaderBarang" style="width:100%; min-height:260px; max-height:320px; background:#000;"></div>
									<video id="barcodeScannerPreviewBarang" autoplay playsinline muted style="display:none; width:100%; max-height:320px; background:#000;"></video>
									<p id="barcodeScannerStatusBarang" style="margin-top:10px; margin-bottom:0; font-size:12px; color:#666;">Arahkan kamera ke barcode item.</p>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
								</div>
							</div>
						</div>
					</div>

					<div id="indikasiModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
						<div class="modal-dialog modal-fullscreen" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									<h4 class="modal-title">Edit Komposisi dan Indikasi</h4>
								</div>
								<div class="modal-body">
									<textarea id="indikasi_modal_editor" rows="12"></textarea>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
									<button type="button" class="btn btn-primary" id="indikasi_modal_save">Simpan</button>
								</div>
							</div>
						</div>
					</div>

					<div id="zataktifModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
						<div class="modal-dialog modal-fullscreen" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									<h4 class="modal-title">Edit Zat Aktif</h4>
								</div>
								<div class="modal-body">
									<textarea id="zataktif_modal_editor" rows="12"></textarea>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
									<button type="button" class="btn btn-primary" id="zataktif_modal_save">Simpan</button>
								</div>
							</div>
						</div>
					</div>

					<div id="jenisobatModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
						<div class="modal-dialog" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									<h4 class="modal-title">Edit Rak Obat</h4>
								</div>
								<div class="modal-body">
									<select id="jenisobat_modal_select" class="form-control">
										<option value="">- Pilih Rak Obat -</option>
										<?php
										$rak_obat = $db->query("SELECT jenisobat FROM jenis_obat ORDER BY jenisobat ASC");
										while ($rak = $rak_obat->fetch(PDO::FETCH_ASSOC)) {
											$jenisobat_option = htmlspecialchars($rak['jenisobat'], ENT_QUOTES, 'UTF-8');
											echo "<option value='{$jenisobat_option}'>{$jenisobat_option}</option>";
										}
										?>
									</select>
								</div>
								<div class="modal-footer">
									<button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
									<button type="button" class="btn btn-primary" id="jenisobat_modal_save">Simpan</button>
								</div>
							</div>
						</div>
					</div>
					<?php
					$editor = $db->query("select updated_by, count(*) as jumlah from barang GROUP BY updated_by");
					
					while ($row = $editor->fetch(PDO::FETCH_ASSOC)) {
						echo "<p style='color: #666; font-size: 12px;'>Nama Editor <strong>$row[updated_by]</strong> : $row[jumlah] item</p>";
					}
					?>
				</div>
			</div>


<?php

			break;

		case "tambah":
            $kode_brg = get_kode();
            
            echo "
		  <div class='box box-primary box-solid'>
				<div class='box-header with-border'>
					<h3 class='box-title'>TAMBAH DATA BARANG &rarr; <a href='https://www.youtube.com/watch?v=9daG5ZnVVGw' target='_blanks'> (TONTON TUTORIAL)</a> </h3> 
					<div class='box-tools pull-right'>
						<button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
                    </div><!-- /.box-tools -->
				</div>
				<div class='box-body table-responsive'>
				
						<form method=POST action='$aksi?module=barang&act=input_barang' enctype='multipart/form-data' class='form-horizontal'>
						
					<div class='col-md-6'>
							  							  
							  <div class='form-group'>
									<label class='col-sm-4 control-label'>Kode Barang</label>        		
									 <div class='col-sm-8'>
											<div class='input-group'>
												<input type='text' id='kd_barang_tambah' name='kd_barang' class='form-control' autocomplete='off' placeholder='$kode_brg'>
												<span class='input-group-btn'>
													<button type='button' id='btnScanBarcodeKodeBarang' class='btn btn-info btn-flat' data-toggle='modal' data-target='#ModalScanBarcodeBarangForm'><span class='glyphicon glyphicon-camera'></span> Scan</button>
												</span>
											</div>
									 </div>
							  </div>
							  
							  
							  <div class='form-group'>
									<label class='col-sm-4 control-label'>Nama Barang</label>        		
									 <div class='col-sm-8'>
										<input type=text name='nm_barang' class='form-control' required='required' autocomplete='off'>
									 </div>
							  </div>
							  <!-- tidak bisa tambah stok dari sini
							  <div class='form-group'>
									<label class='col-sm-4 control-label'>Qty/Stok</label>        		
									 <div class='col-sm-8'>
										<input type=number name='stok_barang' class='form-control' required='required' autocomplete='off'>
									 </div>
							  </div> -->
							  
							  <div class='form-group'>
									<label class='col-sm-4 control-label'>Stok Buffer</label>        		
									 <div class='col-sm-8'>
										<input type=number name='stok_buffer' class='form-control' required='required' autocomplete='off'>
									 </div>
							  </div>
							  <div class='form-group'>
									<label class='col-sm-4 control-label'>Satuan Retail</label>        		
									 <div class='col-sm-8'>
										<select name='sat_barang' class='form-control' >";
                            			$tampil = $db->query("SELECT * FROM satuan ORDER BY nm_satuan ASC");
                            			while ($rk = $tampil->fetch(PDO::FETCH_ASSOC)) {
                            				echo "<option value=$rk[nm_satuan]>$rk[nm_satuan]</option>";
                            			}
                            			echo "
                            			</select>
									 </div>
							  </div> 
							  <div class='form-group'>
									<label class='col-sm-4 control-label'>Satuan Grosir</label>        		
									 <div class='col-sm-8'>
										<select name='sat_grosir' class='form-control' >";
                            			$tampil = $db->query("SELECT * FROM satuan ORDER BY nm_satuan ASC");
                            			while ($rk = $tampil->fetch(PDO::FETCH_ASSOC)) {
                            				echo "<option value=$rk[nm_satuan]>$rk[nm_satuan]</option>";
                            			}
                            			echo "
                            			</select>
									 </div>
							  </div> 
							  
							  <div class='form-group'>
									<label class='col-sm-4 control-label'>Jenis Obat</label>        		
									 <div class='col-sm-8'>
										<select name='jenisobat' class='form-control' >";
                            			$tampil = $db->query("SELECT * FROM jenis_obat ORDER BY jenisobat ASC");
                            			while ($rk = $tampil->fetch(PDO::FETCH_ASSOC)) {
                            				echo "<option value=$rk[jenisobat]>$rk[jenisobat]</option>";
                            			}
                            			echo "
                            			</select>
									 </div>
							  </div>
							  							  
							   <div class='form-group'>
									<label class='col-sm-4 control-label'>Konversi</label>        		
									 <div class='col-sm-8'>
										<input type='number' min='0' name='konversi' class='form-control' required='required' autocomplete='off'>
									 </div>
							  </div> 
							  
							  <div class='form-group'>
									<label class='col-sm-4 control-label'>Harga Beli Retail</label>        		
									 <div class='col-sm-8'>
										<input type='number' min='0' name='hrgsat_barang' class='form-control' required='required' autocomplete='off'>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-4 control-label'>Harga Beli Grosir</label>        		
									 <div class='col-sm-8'>
										<input type='number' min='0' name='hrgsat_grosir' class='form-control' required='required' autocomplete='off'>
									 </div>
							  </div>
							   <div class='form-group'>
									<label class='col-sm-4 control-label'>Harga Jual Reguler</label>        		
									 <div class='col-sm-8'>
										<input type='number' min='0' name='hrgjual_barang' class='form-control' required='required' autocomplete='off'>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-4 control-label'>Harga Jual Resep</label>        		
									 <div class='col-sm-8'>
										<input type='number' min='0' name='hrgjual_barang1' class='form-control' required='required' autocomplete='off'>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-4 control-label'>Harga Jual Marketplace</label>        		
									 <div class='col-sm-8'>
										<input type='number' min='0' name='hrgjual_barang2' class='form-control' required='required' autocomplete='off'>
									 </div>
							  </div>

							  <div class='form-group'>
									<label class='col-sm-4 control-label'>Zat Aktif</label>        		
									 <div class='col-sm-8'>
										<input type='text' name='zataktif' class='form-control' autocomplete='off'>
									 </div>
						  	  </div>
						  					</div>		  
					<div class='col-md-6'>
							  <div class='form-group'>
									<label class='col-sm-5 control-label'>Komposisi dan Indikasi</label>
										<div class='col-sm-12'>
											<div >	
													<textarea name='indikasi' id='content' rows='3'></textarea>
											</div>
										</div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-4 control-label'>Keterangan Lain</label>        		
									 <div class='col-sm-12'>
										<textarea name='ket_barang' id='content_ket' rows='3'></textarea>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-4 control-label'></label>       
										<div class='col-sm-8'>
											<input class='btn btn-primary' type=submit value=SIMPAN>
											<input class='btn btn-danger' type=button value=BATAL onclick=self.history.back()>
										</div>
								</div>
					</div>			
							  </form>
							  
				</div> 
				
			</div>";


			break;

		case "edit":
			$edit = $db->prepare("SELECT * FROM barang WHERE id_barang = ?");
			$edit->execute([$_GET['id']]);
			$r = $edit->fetch(PDO::FETCH_ASSOC);
			$returnStart = isset($_GET['start']) ? (int)$_GET['start'] : 0;

			echo "
		  <div class='box box-primary box-solid'>
				<div class='box-header with-border'>
					<h3 class='box-title'>UBAH DATA BARANG</h3>
					<div class='box-tools pull-right'>
						<button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
                    </div><!-- /.box-tools -->
				</div>
				<div class='box-body table-responsive'>
						<form method=POST method=POST action=$aksi?module=barang&act=update_barang  enctype='multipart/form-data' class='form-horizontal'>
							  <input type=hidden name=id value='$r[id_barang]'>						  
							  <input type=hidden name=return_start value='$returnStart'>
						<div class='col-md-6'>	 
							  <div class='form-group'>
									<label class='col-sm-4 control-label'>Kode Barang</label>        		
									 <div class='col-sm-8'>
										<input type=text name='kd_barang' class='form-control' required='required' value='$r[kd_barang]' autocomplete='off' readonly>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-4 control-label'>Nama Barang</label>        		
									 <div class='col-sm-8'>
										<input type=text name='nm_barang' class='form-control' required='required' value='$r[nm_barang]' autocomplete='off' >
									 </div>
							  </div>
							  <!-- tidak bisa edit stok dari sini
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Qty/Stok</label>        		
									 <div class='col-sm-3'>
										<input type=number name='stok_barang' class='form-control' required='required' value='$r[stok_barang]' autocomplete='off'>
									 </div>
							  </div> -->
							  
							  <div class='form-group'>
									<label class='col-sm-4 control-label'>Stok Buffer</label>        		
									 <div class='col-sm-8'>
										<input type=number name='stok_buffer' class='form-control' required='required' value='$r[stok_buffer]' autocomplete='off'>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-4 control-label'>Satuan Retail</label>        		
									 <div class='col-sm-8'>
										<select name='sat_barang' class='form-control' >
											 <option  value=$r[sat_barang] selected>$r[sat_barang]</option>";
                                            $tampil = $db->query("SELECT * FROM satuan ORDER BY nm_satuan");
                                            while ($k = $tampil->fetch(PDO::FETCH_ASSOC)) {
                                                echo "<option value=$k[nm_satuan]>$k[nm_satuan]</option>";
                                            }
                                            echo "</select>
									 </div>
							  </div> 
							  
							  <div class='form-group'>
									<label class='col-sm-4 control-label'>Satuan Grosir</label>        		
									 <div class='col-sm-8'>
										<select name='sat_grosir' class='form-control' >
											 <option  value=$r[sat_grosir] selected>$r[sat_grosir]</option>";
                                            $tampil = $db->query("SELECT * FROM satuan ORDER BY nm_satuan");
                                            while ($k = $tampil->fetch(PDO::FETCH_ASSOC)) {
                                                echo "<option value=$k[nm_satuan]>$k[nm_satuan]</option>";
                                            }
                                            echo "</select>
									 </div>
							  </div> 
							  
							  <div class='form-group'>
									<label class='col-sm-4 control-label'>Jenis Obat</label>        		
									 <div class='col-sm-8'>
										<select name='jenisobat' class='form-control' >
											 <option  value=$r[jenisobat] selected>$r[jenisobat]</option>";
                                                $tampil = $db->query("SELECT * FROM jenis_obat ORDER BY idjenis");
                                                while ($k = $tampil->fetch(PDO::FETCH_ASSOC)) {
                                                    echo "<option value=$k[jenisobat]>$k[jenisobat]</option>";
                                                }
                                                echo "</select>
									 </div>
							  </div>
							  
							  
							  <div class='form-group'>
									<label class='col-sm-4 control-label'>Konversi</label>        		
									 <div class='col-sm-8'>
										<input type='number' min='0' name='konversi' class='form-control' required='required' value='$r[konversi]' autocomplete='off'>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-4 control-label'>Harga Beli Retail</label>        		
									 <div class='col-sm-8'>
										<input type='number' min='0' name='hrgsat_barang' class='form-control' required='required' value='$r[hrgsat_barang]' autocomplete='off'>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-4 control-label'>Harga Beli Grosir</label>        		
									 <div class='col-sm-8'>
										<input type='number' min='0' name='hrgsat_grosir' class='form-control' required='required' value='$r[hrgsat_grosir]' autocomplete='off'>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-4 control-label'>Harga Jual Reguler</label>        		
									 <div class='col-sm-8'>
										<input type='number' min='0' name='hrgjual_barang' class='form-control' required='required' value='$r[hrgjual_barang]' autocomplete='off'>
									 </div>
							  </div>
							  
							   <div class='form-group'>
									<label class='col-sm-4 control-label'>Harga Jual Resep</label>        		
									 <div class='col-sm-8'>
										<input type='number' min='0' name='hrgjual_barang1' class='form-control' required='required' value='$r[hrgjual_barang1]' autocomplete='off'>
									 </div>
							  </div>
							  
							   <div class='form-group'>
									<label class='col-sm-4 control-label'>Harga Jual Marketplace</label>        		
									 <div class='col-sm-8'>
										<input type='number' min='0' name='hrgjual_barang2' class='form-control' required='required' value='$r[hrgjual_barang2]' autocomplete='off'>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-4 control-label'>Zat Aktif</label>        		
									 <div class='col-sm-8'>										
									 	<textarea name='zataktif' class='ckeditor' id='content2' rows='3'>$r[zataktif]</textarea>
										</div>
							  </div>
							  
						</div>
						<div class='col-md-6'>	  
							  <div class='form-group'>
									<label class='col-sm-5 control-label'>Komposisi dan Indikasi</label>
										<div class='col-sm-12'>
											<div >	
													<textarea name='indikasi' class='ckeditor' id='content' rows='3'>$r[indikasi]</textarea>
											</div>
										</div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-4 control-label'>Komposisi</label>        		
									 <div class='col-sm-8'>
										<textarea name='ket_barang' class='ckeditor'  rows='3'>$r[ket_barang]</textarea>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-4 control-label'>Dosis / Kekuatan </label>        		
									 <div class='col-sm-8'>
										<textarea name='dosis' class='ckeditor' id='content3' rows='3'>$r[dosis]</textarea>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-4 control-label'></label>       
										<div class='col-sm-8'>
											<input class='btn btn-primary' type=submit value=SIMPAN>
											<input class='btn btn-danger' type=button value=BATAL onclick=self.history.back()>
										</div>
								</div>
						</div>		
							  </form>
							  
				</div> 
				
			</div>";




			break;
        case "detail" :
            $detail = $db->prepare("SELECT * FROM barang WHERE id_barang = ?");
            $detail->execute([$_GET['id']]);
            $s = $detail->fetch(PDO::FETCH_ASSOC);
            $sid = $s['kd_barang'];

        ?>
        <div class="box box-primary box-solid">
            <div class='box-header with-border'>
                <h3 class='box-title'>DETAIL BARANG</h3>
                <div class='box-tools pull-right'>
                    <button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
                </div><!-- /.box-tools -->
            </div>
        <div class='box-body table-responsive'>
        <div class="form-group row">
            <div class="container" style="font-weight: bold">
                <div class="row" style="background-color: #00FFFF" >
                    <div class="col-xs-4">
                        Nama Barang
                    </div>
                    <div class="col-xs-8">
                        : <?=$s['nm_barang']?>
                    </div>
                </div>
                <div class="row" >
                    <div class="col-xs-4">
                        Satuan Retail
                    </div>
                    <div class="col-xs-8">
                        : <?=$s['sat_barang']?>
                    </div>
                </div>
                <div class="row" style="background-color: #00FFFF">
                    <div class="col-xs-4">
                        Satuan Grosir
                    </div>
                    <div class="col-xs-8">
                        : <?=$s['sat_grosir']?>
                    </div>
                </div>
                <div class="row" >
                    <div class="col-xs-4">
                        Stok Retail
                    </div>
                    <div class="col-xs-8">
                        : <?=$s['stok_barang']?>
                    </div>
                </div>
                <div class="row" >
                    <div class="col-xs-4">
                        Stok Grosir
                    </div>
                    <div class="col-xs-8">
                        : <?=round($s['stok_barang'] / $s['konversi'])?>
                    </div>
                </div>
                <div class="row" style="background-color: #00FFFF" >
                    <div class="col-xs-4">
                        Jenis Obat
                    </div>
                    <div class="col-xs-8">
                        : <?=$s['jenisobat']?>
                    </div>
                </div>
                <div class="row" >
                    <div class="col-xs-4">
                        Konversi
                    </div>
                    <div class="col-xs-8">
                        : <?=$s['konversi']?>
                    </div>
                </div>
                <div class="row" style="background-color: #FF1493" >
                    <div class="col-xs-4">
                        Harga Nett Apotek
                    </div>
                    <div class="col-xs-8">
                        : <?= format_rupiah($s['hna'])?>
                    </div>
                </div>
                <div class="row" style="background-color: #00FFFF" >
                    <div class="col-xs-4">
                        Harga Beli Retail
                    </div>
                    <div class="col-xs-8">
                        : <?= format_rupiah($s['hrgsat_barang'])?>
                    </div>
                </div>
                <div class="row" >
                    <div class="col-xs-4">
                        Harga Beli Grosir
                    </div>
                    <div class="col-xs-8">
                        : <?= format_rupiah($s['hrgsat_grosir'])?>
                    </div>
                </div>
                <div class="row" style="background-color: #00FFFF" >
                    <div class="col-xs-4" >
                        Harga Jual Reguler
                    </div>
                    <div class="col-xs-8">
                        : <?= format_rupiah($s['hrgjual_barang'])?>
                    </div>
                </div>
                <div class="row" >
                    <div class="col-xs-4" >
                        Harga Jual Member
                    </div>
                    <div class="col-xs-8">
                        : <?=format_rupiah($s['hrgjual_barang1'])?>
                    </div>
                </div>
                <div class="row" style="background-color: #00FFFF">
                    <div class="col-xs-4">
                       Harga Jual Marketplace
                    </div>
                    <div class="col-xs-8">
                        : <?= format_rupiah($s['hrgjual_barang2'])?>
                    </div>
                </div>
                <div class="row"  >
                    <div class="col-xs-4">
                        Komposisi dan Indikasi
                    </div>
                    <div class="col-xs-8">
                        <?=$s['indikasi']?>
                    </div>
                </div>

            </div>
            <div style="text-align:center;">
                <?php
                   echo" <a href='?module=barang&act=edit&id=$s[id_barang]' title='EDIT' class='btn btn-warning btn-xl'>EDIT</a>
                ";
                ?>
                    <input class='btn btn-success' type='button' value=KEMBALI onclick=self.history.back()>
            </div>
            </div>
        </div>
        <?php
            break ;
	}
}
?>

<script type="text/javascript" src="vendors/ckeditor/ckeditor.js"></script>
<style>
	.modal-fullscreen {
		width: 98%;
		margin: 10px auto;
	}
	.modal-fullscreen .modal-content {
		height: calc(100vh - 20px);
		overflow: hidden;
	}
	.modal-fullscreen .modal-body {
		height: calc(100% - 120px);
		overflow: auto;
	}
	#indikasi_modal_editor {
		width: 100%;
		height: 100%;
	}
	#zataktif_modal_editor {
		width: 100%;
		height: 100%;
	}
	#indikasiModal.is-open {
		display: block;
		position: fixed;
		inset: 0;
		background: rgba(0, 0, 0, 0.5);
		z-index: 1050;
		overflow: auto;
	}
	#indikasiModal.is-open.fade {
		opacity: 1;
	}
	#indikasiModal.is-open .modal-content {
		box-shadow: 0 2px 10px rgba(0, 0, 0, 0.4);
	}
	#indikasiModal.is-open .modal-dialog {
		margin: 10px auto;
	}
	#zataktifModal.is-open {
		display: block;
		position: fixed;
		inset: 0;
		background: rgba(0, 0, 0, 0.5);
		z-index: 1050;
		overflow: auto;
	}
	#zataktifModal.is-open.fade {
		opacity: 1;
	}
	#zataktifModal.is-open .modal-content {
		box-shadow: 0 2px 10px rgba(0, 0, 0, 0.4);
	}
	#zataktifModal.is-open .modal-dialog {
		margin: 10px auto;
	}
	#jenisobatModal.is-open {
		display: block;
		position: fixed;
		inset: 0;
		background: rgba(0, 0, 0, 0.5);
		z-index: 1050;
		overflow: auto;
	}
	#jenisobatModal.is-open.fade {
		opacity: 1;
	}
	#jenisobatModal.is-open .modal-content {
		box-shadow: 0 2px 10px rgba(0, 0, 0, 0.4);
	}
	#jenisobatModal.is-open .modal-dialog {
		margin: 40px auto;
	}
</style>
<script type="text/javascript">
	// Inisialisasi CKEditor untuk form tambah
	if (document.getElementById('content')) {
		CKEDITOR.replace('content', {
			filebrowserBrowseUrl: '',
			filebrowserWindowWidth: 1000,
			filebrowserWindowHeight: 500
		});
	}
	if (document.getElementById('content_ket')) {
		CKEDITOR.replace('content_ket', {
			filebrowserBrowseUrl: '',
			filebrowserWindowWidth: 1000,
			filebrowserWindowHeight: 500
		});
	}
	
	// Inisialisasi CKEditor untuk form edit
	if (document.getElementById('edit_zataktif')) {
		CKEDITOR.replace('edit_zataktif', {
			filebrowserBrowseUrl: '',
			filebrowserWindowWidth: 1000,
			filebrowserWindowHeight: 500
		});
	}
	if (document.getElementById('edit_indikasi')) {
		CKEDITOR.replace('edit_indikasi', {
			filebrowserBrowseUrl: '',
			filebrowserWindowWidth: 1000,
			filebrowserWindowHeight: 500
		});
	}
	if (document.getElementById('edit_ket_barang')) {
		CKEDITOR.replace('edit_ket_barang', {
			filebrowserBrowseUrl: '',
			filebrowserWindowWidth: 1000,
			filebrowserWindowHeight: 500
		});
	}
	if (document.getElementById('edit_dosis')) {
		CKEDITOR.replace('edit_dosis', {
			filebrowserBrowseUrl: '',
			filebrowserWindowWidth: 1000,
			filebrowserWindowHeight: 500
		});
	}
</script>
<script>
var userLevel = '<?= $_SESSION['level']; ?>';
</script>
<?php if (isset($_GET['act']) && $_GET['act'] === 'tambah') { ?>
<div id="ModalScanBarcodeBarangForm" class="modal fade" role="dialog" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">Scan Barcode Kode Barang</h4>
			</div>
			<div class="modal-body">
				<div id="barcodeScannerReaderBarangForm" style="width:100%; min-height:260px; max-height:320px; background:#000;"></div>
				<video id="barcodeScannerPreviewBarangForm" autoplay playsinline muted style="display:none; width:100%; max-height:320px; background:#000;"></video>
				<p id="barcodeScannerStatusBarangForm" style="margin-top:10px; margin-bottom:0; font-size:12px; color:#666;">Arahkan kamera ke barcode item.</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Tutup</button>
			</div>
		</div>
	</div>
</div>
<?php } ?>
<script>
(function() {
	if (!document.getElementById('ModalScanBarcodeBarangForm')) {
		return;
	}

	var scannerStream = null;
	var scannerInterval = null;
	var barcodeDetectorInstance = null;
	var html5QrScanner = null;
	var html5QrScannerActive = false;
	var barcodeScanLocked = false;
	var html5QrScriptPromise = null;

	function hasBootstrapModal() {
		return (typeof $.fn.modal === 'function');
	}

	function showScannerModal() {
		var $modal = $('#ModalScanBarcodeBarangForm');
		if (!$modal.length) {
			return;
		}

		if (hasBootstrapModal()) {
			$modal.modal('show');
			return;
		}

		$('body').addClass('modal-open');
		$modal
			.css('display', 'block')
			.addClass('in')
			.attr('aria-hidden', 'false');
	}

	function hideScannerModal() {
		var $modal = $('#ModalScanBarcodeBarangForm');
		if (!$modal.length) {
			return;
		}

		if (hasBootstrapModal()) {
			$modal.modal('hide');
			return;
		}

		$modal
			.removeClass('in')
			.css('display', 'none')
			.attr('aria-hidden', 'true');
		$('body').removeClass('modal-open');
		stopBarcodeScanner();
	}

	function setScannerStatus(message, isError) {
		var statusEl = document.getElementById('barcodeScannerStatusBarangForm');
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
		html5QrScanner = null;

		if (scannerStream) {
			scannerStream.getTracks().forEach(function(track) {
				track.stop();
			});
			scannerStream = null;
		}

		var video = document.getElementById('barcodeScannerPreviewBarangForm');
		if (video) {
			video.srcObject = null;
			video.style.display = 'none';
		}

		var reader = document.getElementById('barcodeScannerReaderBarangForm');
		if (reader) {
			reader.style.display = 'block';
		}
	}

	function applyBarcodeResultToKodeBarang(hasilScan) {
		var cleanValue = $.trim(hasilScan || '');
		if (!cleanValue) {
			return;
		}

		var $input = $('#kd_barang_tambah');
		if ($input.length) {
			$input.val(cleanValue).trigger('input').trigger('change');
			$input.focus();
		}

		setScannerStatus('Barcode terdeteksi: ' + cleanValue, false);
		hideScannerModal();
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

		var video = document.getElementById('barcodeScannerPreviewBarangForm');
		var reader = document.getElementById('barcodeScannerReaderBarangForm');
		if (video) {
			video.style.display = 'none';
		}
		if (reader) {
			reader.style.display = 'block';
		}

		html5QrScanner = new Html5Qrcode('barcodeScannerReaderBarangForm');
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
			applyBarcodeResultToKodeBarang(decodedText);
		}, function() {
			// ignore per-frame decode error
		});

		html5QrScannerActive = true;
		setScannerStatus('Scanner aktif. Arahkan barcode ke area kamera.', false);
	}

	async function startBarcodeDetectorScanner() {
		if (!window.BarcodeDetector) {
			throw new Error('Browser tidak support BarcodeDetector.');
		}

		var video = document.getElementById('barcodeScannerPreviewBarangForm');
		var reader = document.getElementById('barcodeScannerReaderBarangForm');
		if (reader) {
			reader.style.display = 'none';
		}
		if (video) {
			video.style.display = 'block';
		}

		barcodeDetectorInstance = new BarcodeDetector({
			formats: ['code_128', 'ean_13', 'ean_8', 'qr_code']
		});

		scannerStream = await navigator.mediaDevices.getUserMedia({
			video: {
				facingMode: {
					ideal: 'environment'
				},
				width: {
					ideal: 720
				},
				height: {
					ideal: 1280
				}
			}
		});

		video.srcObject = scannerStream;
		await video.play();

		scannerInterval = setInterval(async function() {
			if (!video || video.readyState !== video.HAVE_ENOUGH_DATA) {
				return;
			}

			try {
				var detected = await barcodeDetectorInstance.detect(video);
				if (detected.length > 0 && !barcodeScanLocked) {
					barcodeScanLocked = true;
					clearInterval(scannerInterval);
					scannerInterval = null;
					applyBarcodeResultToKodeBarang(detected[0].rawValue);
				}
			} catch (err) {
				console.log('scan error', err);
			}
		}, 600);

		setScannerStatus('Scanner aktif. Arahkan barcode ke area kamera.', false);
	}

	async function startBarcodeScanner() {
		stopBarcodeScanner();
		setScannerStatus('Menyiapkan scanner kamera...', false);

		try {
			await loadHtml5QrcodeScript();
			await startHtml5QrcodeScanner();
		} catch (err) {
			try {
				setScannerStatus('Fallback ke mode scanner bawaan browser...', false);
				await startBarcodeDetectorScanner();
			} catch (fallbackErr) {
				setScannerStatus('Scanner tidak dapat dijalankan di browser ini.', true);
			}
		}
	}

	$(document).on('click', '#btnScanBarcodeKodeBarang', function(e) {
		e.preventDefault();
		showScannerModal();

		if (!hasBootstrapModal()) {
			startBarcodeScanner();
		}
	});

	$(document).on('shown.bs.modal', '#ModalScanBarcodeBarangForm', function() {
		startBarcodeScanner();
	});

	$(document).on('hidden.bs.modal', '#ModalScanBarcodeBarangForm', function() {
		stopBarcodeScanner();
	});

	$(document).on('click', '#ModalScanBarcodeBarangForm .close, #ModalScanBarcodeBarangForm [data-dismiss="modal"]', function(e) {
		if (hasBootstrapModal()) {
			return;
		}
		e.preventDefault();
		hideScannerModal();
	});
})();
</script>
<?php
$barang_table_config_path = __DIR__ . '/barang_table_config.js';
$barang_table_config_ver = file_exists($barang_table_config_path) ? filemtime($barang_table_config_path) : time();
?>
<script src="modul/mod_barang/barang_table_config.js?v=<?= $barang_table_config_ver ?>"></script>