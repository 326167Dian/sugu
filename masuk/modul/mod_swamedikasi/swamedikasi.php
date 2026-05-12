<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// session_start(); // Sudah aktif di media_admin.php
if (empty($_SESSION['username']) and empty($_SESSION['passuser'])) {
	echo "<link href=../css/style.css rel=stylesheet type=text/css>";
	echo "<div class='error msg'>Untuk mengakses Modul anda harus login.</div>";
} else {

	$aksi = "modul/mod_swamedikasi/aksi_pelanggan.php";
// 	$aksi_pelanggan = "masuk/modul/mod_pelanggan/aksi_pelanggan.php";
	switch (isset($_GET['act']) ? $_GET['act'] : '') {
			// Tampil Siswa
		default:

			//$stmt = $db->query("SELECT * FROM swamedikasi ORDER BY id_pelanggan ASC");
			//$tampil_pelanggan = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>


			<div class="box box-primary box-solid">
				<div class="box-header with-border">
					<h3 class="box-title">DATA SWAMEDIKASI</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div><!-- /.box-tools -->
				</div>
				<div class="box-body table-responsive">
					<!--<a class='btn  btn-success btn-flat' href='?module=pelanggan&act=tambah'>TAMBAH</a>-->
					<!--<a class='btn btn-primary btn-flat' href='?module=konseling'>KONSELING</a>-->
					<!--<a class='btn btn-warning btn-flat' href='?module=meso'>MESO</a>-->
					<!--<a class='btn btn-danger btn-flat' href='?module=pio'>PIO</a>-->
					<!--<a class='btn btn-default btn-flat' href='?module=pto'>PTO</a>-->
					<!--<a class='btn btn-success btn-flat' href='?module=cpp'>CATATAN PENGOBATAN PASIEN (CPP)</a>-->
					<!--<a class='btn btn-info btn-flat' href='?module=homecare'>HOME CARE </a>-->
					<br><br>


					<table id="example1" class="table table-bordered table-striped">
						<thead>
							<tr>
        						<th>No</th>
        						<th>Pelanggan</th>
        						<th>Tanggal</th>
        						<th>Diagnosa</th>
        						<th>Tindakan</th>
        						<th>Saran Konsultasi</th>
        						<th>Tgl Follow Up</th>
        						<th>Follow Up oleh</th>
        						<th>Created</th>
        						<th>Aksi</th>
        					</tr>
						</thead>
						<tbody>
						<?php
						$has_riwayat_obat_table = ($db->query("SHOW TABLES LIKE 'riwayat_pelanggan_obat'")->rowCount() > 0);
						// Generate CSRF token for riwayat actions if not set
            			if (!isset($_SESSION['csrf_pelanggan']) || empty($_SESSION['csrf_pelanggan'])) {
            				if (function_exists('random_bytes')) {
            					$_SESSION['csrf_pelanggan'] = bin2hex(random_bytes(16));
            				} else {
            					$_SESSION['csrf_pelanggan'] = bin2hex(openssl_random_pseudo_bytes(16));
            				}
            			}
            			$token = $_SESSION['csrf_pelanggan'];
			
						$stmt = $db->prepare("SELECT rp.*, p.nm_pelanggan
							FROM riwayat_pelanggan rp
							LEFT JOIN pelanggan p ON p.id_pelanggan = rp.id_pelanggan
							ORDER BY rp.tgl DESC");
            			$stmt->execute();
            			$riwayat = $stmt->fetchAll(PDO::FETCH_ASSOC);
            			$riwayat_ids = array_map(function($x){ return (int)$x['id']; }, $riwayat);
            			$obat_map = [];
            			if ($has_riwayat_obat_table && count($riwayat_ids) > 0) {
            				$in_placeholders = implode(',', array_fill(0, count($riwayat_ids), '?'));
            				$obat_stmt = $db->prepare("SELECT id_riwayat, kd_barang, nm_barang, aturan_pakai FROM riwayat_pelanggan_obat WHERE id_riwayat IN ($in_placeholders) ORDER BY id ASC");
            				$obat_stmt->execute($riwayat_ids);
            				while ($ob = $obat_stmt->fetch(PDO::FETCH_ASSOC)) {
            					$txt = htmlspecialchars($ob['nm_barang']);
            				// 	if (!empty($ob['kd_barang'])) {
            				// 		$txt .= " (" . htmlspecialchars($ob['kd_barang']) . ")";
            				// 	}
            					if (!empty($ob['aturan_pakai'])) {
            						$txt .= " - " . htmlspecialchars($ob['aturan_pakai']);
            					}
            					$obat_map[$ob['id_riwayat']][] = $txt;
            				}
            			}
            			$no = 1;
            			foreach($riwayat as $rw){
					$nama_pelanggan = isset($rw['nm_pelanggan']) && $rw['nm_pelanggan'] !== '' ? htmlspecialchars($rw['nm_pelanggan']) : '-';
            				$edit_link = "?module=swamedikasi&act=edit_riwayat&idr=".$rw['id'];
            				$delete_link = $aksi."?module=swamedikasi&act=hapus_riwayat&id=".$rw['id']."&token=".$token;
            				$obat_tindakan = isset($obat_map[$rw['id']]) ? implode("<br>", $obat_map[$rw['id']]) : htmlspecialchars($rw['tindakan']);
            				$tgl_followup = (isset($rw['tgl_followup']))? $rw['tgl_followup']:'<button type="button" data-id="'.$rw['id'].'" class="tgl_followup btn btn-danger">Klik untuk followup</button>';
            				
            				echo "<tr>
            					<td>$no</td>
					<td>$nama_pelanggan</td>
            					<td>$rw[tgl]</td>
            					<td>$rw[diagnosa]</td>
            					<td>$obat_tindakan</td>
            					<td>$rw[followup]</td>
            					<td>$tgl_followup</td>
            					<td>$rw[followup_by]</td>
            					<td>$rw[created_at]</td>
            					<td>
            						<a href='".$edit_link."' title='EDIT' class='btn btn-warning btn-xs'>EDIT</a>
            						<a href=javascript:confirmdelete('".$delete_link."') title='HAPUS' class='btn btn-danger btn-xs'>HAPUS</a>
            					</td>
            				</tr>";
            				$no++;
            			}
						?>
    				    </tbody>
    				</table>

			
				</div>
			</div>


<?php

			break;

		case "tambah":

			echo "
		  <div class='box box-primary box-solid'>
				<div class='box-header with-border'>
					<h3 class='box-title'>TAMBAH</h3>
					<div class='box-tools pull-right'>
						<button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
                    </div><!-- /.box-tools -->
				</div>
				<div class='box-body table-responsive'>
				
						<form method=POST action='$aksi?module=pelanggan&act=input_pelanggan' enctype='multipart/form-data' class='form-horizontal'>
						
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Nama Pelanggan</label>        		
									 <div class='col-sm-4'>
										<input type=text name='nm_pelanggan' class='form-control' required='required' autocomplete='off'>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Jenis Kelamin</label>        		
									 <div class='col-sm-4'>	
										<select name='jenis_kelamin' id='jenis_kelamin' class='form-control' required='required'>
											<option value='' selected>- Pilih -</option>
											<option value='PRIA'>PRIA</option>
											<option value='WANITA'>WANITA</option>
										</select>
									 </div>									 
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Tanggal lahir</label>        		
									 <div class='col-sm-4'>
										<input type='date' name='tanggal_lahir' class='form-control' required='required' autocomplete='off'>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Telepon</label>        		
									 <div class='col-sm-4'>
										<input type=text name='tlp_pelanggan' class='form-control' autocomplete='off'>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Alamat</label>        		
									 <div class='col-sm-4'>
										<textarea name='alamat_pelanggan' class='form-control' rows='3'></textarea>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Keterangan</label>        		
									 <div class='col-sm-4'>
										<textarea name='ket_pelanggan' class='form-control' rows='3'></textarea>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'></label>       
										<div class='col-sm-5'>
											<input class='btn btn-info' type=submit value=SIMPAN>
									<input class='btn btn-primary' type=button value=BATAL onclick='self.history.back()'>
							  
				</div> 
				
			</div>";


			break;

		case "riwayat":
			$stmt = $db->prepare("SELECT * FROM pelanggan WHERE id_pelanggan = ?");
			$stmt->execute([$_GET['id']]);
			$p = $stmt->fetch(PDO::FETCH_ASSOC);
			$has_riwayat_obat_table = ($db->query("SHOW TABLES LIKE 'riwayat_pelanggan_obat'")->rowCount() > 0);
			// Generate CSRF token for riwayat actions if not set
			if (!isset($_SESSION['csrf_pelanggan']) || empty($_SESSION['csrf_pelanggan'])) {
				if (function_exists('random_bytes')) {
					$_SESSION['csrf_pelanggan'] = bin2hex(random_bytes(16));
				} else {
					$_SESSION['csrf_pelanggan'] = bin2hex(openssl_random_pseudo_bytes(16));
				}
			}
			$token = $_SESSION['csrf_pelanggan'];
			echo "
		  <div class='box box-primary box-solid'>
				<div class='box-header with-border'>
					<h3 class='box-title'>SWAMEDIKASI DAN RIWAYAT PELANGGAN : $p[nm_pelanggan]</h3>
					<div class='box-tools pull-right'>
						<a href='modul/mod_pelanggan/cetak_riwayat_pdf.php?id=$_GET[id]' target='_blank' class='btn btn-danger btn-sm' title='Cetak PDF'>
							<i class='fa fa-file-pdf-o'></i> CETAK PDF
						</a>
						<button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
					</div><!-- /.box-tools -->
				</div>
				<div class='box-body table-responsive'>";
			// flash message display if exists
			if (isset($_SESSION['flash'])){
				echo $_SESSION['flash'];
				unset($_SESSION['flash']);
			}
			if (!$has_riwayat_obat_table) {
				echo "<div class='alert alert-warning'>Tabel detail obat belum tersedia. Jalankan migration <b>20260313_add_table_riwayat_pelanggan_obat.sql</b> terlebih dahulu.</div>";
			}
			echo "
			<form method=POST action='$aksi?module=pelanggan&act=input_riwayat' enctype='multipart/form-data' class='form-horizontal'>
				<input type=hidden name='id_pelanggan' value='$_GET[id]'>
				<input type=hidden name='token' value='$token'>
				<div class='form-group'>
					<label class='col-sm-2 control-label'>Tanggal</label>
					<div class='col-sm-4'>
						<input type='date' name='tgl' class='form-control' required='required' value='".date('Y-m-d')."'>
					</div>
				</div>
				<div class='form-group'>
					<label class='col-sm-2 control-label'>Diagnosa</label>
					<div class='col-sm-4'>
						<textarea name='diagnosa' class='form-control' rows='3'></textarea>
					</div>
				</div>
				<div class='form-group'>
					<label class='col-sm-2 control-label'>Tindakan</label>
					<div class='col-sm-4'>
						<div id='obat-wrap'>
							<div class='obat-row' style='margin-bottom:8px;'>
								<div class='col-sm-7' style='padding-left:0;'>
								    <div id='containerObat'>
                                        <div class='row-obat'>
                                            <div class='autocomplete-wrapper'>
                                                <input type='hidden' name='obat_kd[]' class='obat-kd'>
                                                <div class='input-group'>
                                                    <input type='text' name='obat_nama[]' class='form-control obat-nama' placeholder='Nama obat (ketik lalu Enter)' id='item_1'>
                                                    <div class='input-group-addon'>
                                                        <button type='button' data-toggle='modal' data-target='#ModalItem' href='#' class='item' data-rowi='1'><span class='glyphicon glyphicon-search'></span></button>
                                                    </div>
                                                </div>
							                    
                                                <div class='autocomplete-panel'></div>
                                            </div>
                                        </div>
                                    </div>
								</div>
								<div class='col-sm-5' style='padding-right:0;'>
									<div class='input-group'>
										<input type='text' name='aturan_pakai[]' class='form-control' placeholder='Aturan pakai'>
										<span class='input-group-btn'>
											<button type='button' class='btn btn-danger btn-remove-obat'>x</button>
										</span>
									</div>
								</div>
							</div>
						</div>
						<button type='button' id='btn-tambah-obat' class='btn btn-default btn-sm'>+Tambah Obat</button>
					</div>
				</div>
				<div class='form-group'>
					<label class='col-sm-2 control-label'>Saran konsultasi</label>
					<div class='col-sm-4'>
						<textarea name='followup' class='form-control' rows='3'></textarea>
					</div>
				</div>
				<div class='form-group'>
					<label class='col-sm-2 control-label'></label>
					<div class='col-sm-5'>
						<input class='btn btn-info' type=submit value=SIMPAN>
							<input class='btn btn-primary' type=button value=KEMBALI onclick='self.history.back()'>
					</div>
				</div>
			</form>
			<hr>
			<form method='GET'  enctype='multipart/form-data' class='form-horizontal'>
			    <input type='hidden' name='module' value='pelanggan'>
			    <input type='hidden' name='act' value='riwayat'>
			    <input type='hidden' name='id' value='$_GET[id]'>
			    <div class='form-group'>
    				<label class='col-sm-2 control-label'>Tanggal Start</label>
    				<div class='col-sm-3'>
    					<input type='date' name='tgl_from' class='form-control'>
    				</div>
    			</div>
    			<div class='form-group'>
    				<label class='col-sm-2 control-label'>Tanggal Finish</label>
    				<div class='col-sm-3'>
    					<input type='date' name='tgl_to' class='form-control'>
    				</div>
    			</div>
    			<div class='form-group'>
					<label class='col-sm-2 control-label'></label>
					<div class='col-sm-5'>
						<button class='btn btn-info'>
						    <i class='glyphicon glyphicon-search'></i>
						    Cari Data
						</button>
					</div>
				</div>
			</form>
			<hr>
			
			<h4>Riwayat Sebelumnya</h4>
			<table class='table table-bordered table-striped'>
				<thead>
					<tr>
						<th>No</th>
						<th>Tanggal</th>
						<th>Diagnosa</th>
						<th>Tindakan</th>
						<th>Saran Konsultasi</th>
						<th>Tgl Follow Up</th>
						<th>Follow Up oleh</th>
						<th>Created</th>
						<th>Aksi</th>
					</tr>
				</thead>
				<tbody>";
			$date = date('Y-m-d', time());
			$tgl_from = isset($_GET['tgl_from'])? $_GET['tgl_from']:'';
			$tgl_to = isset($_GET['tgl_to'])? $_GET['tgl_to']:date('Y-m-d',time());
			
			$stmt = $db->prepare("SELECT * FROM riwayat_pelanggan WHERE id_pelanggan = ? AND tgl BETWEEN ? AND ? ORDER BY tgl DESC");
			$stmt->execute([$_GET['id'], $tgl_from, $tgl_to]);
			$riwayat = $stmt->fetchAll(PDO::FETCH_ASSOC);
			$riwayat_ids = array_map(function($x){ return (int)$x['id']; }, $riwayat);
			$obat_map = [];
			if ($has_riwayat_obat_table && count($riwayat_ids) > 0) {
				$in_placeholders = implode(',', array_fill(0, count($riwayat_ids), '?'));
				$obat_stmt = $db->prepare("SELECT id_riwayat, kd_barang, nm_barang, aturan_pakai FROM riwayat_pelanggan_obat WHERE id_riwayat IN ($in_placeholders) ORDER BY id ASC");
				$obat_stmt->execute($riwayat_ids);
				while ($ob = $obat_stmt->fetch(PDO::FETCH_ASSOC)) {
					$txt = htmlspecialchars($ob['nm_barang']);
				// 	if (!empty($ob['kd_barang'])) {
				// 		$txt .= " (" . htmlspecialchars($ob['kd_barang']) . ")";
				// 	}
					if (!empty($ob['aturan_pakai'])) {
						$txt .= " - " . htmlspecialchars($ob['aturan_pakai']);
					}
					$obat_map[$ob['id_riwayat']][] = $txt;
				}
			}
			$no = 1;
			foreach($riwayat as $rw){
				$edit_link = "?module=pelanggan&act=edit_riwayat&id=$_GET[id]&idr=".$rw['id'];
				$delete_link = $aksi."?module=pelanggan&act=hapus_riwayat&id=".$rw['id']."&token=".$token;
				$obat_tindakan = isset($obat_map[$rw['id']]) ? implode("<br>", $obat_map[$rw['id']]) : htmlspecialchars($rw['tindakan']);
				$tgl_followup = (isset($rw['tgl_followup']))? $rw['tgl_followup']:'<button type="button" data-id="'.$rw['id'].'" class="tgl_followup btn btn-danger">Klik untuk followup</button>';
				echo "<tr>
					<td>$no</td>
					<td>$rw[tgl]</td>
					<td>$rw[diagnosa]</td>
					<td>$obat_tindakan</td>
					<td>$rw[followup]</td>
					<td>$tgl_followup</td>
					<td>$rw[followup_by]</td>
					<td>$rw[created_at]</td>
					<td>
						<a href='".$edit_link."' title='EDIT' class='btn btn-warning btn-xs'>EDIT</a>
						<a href=javascript:confirmdelete('".$delete_link."') title='HAPUS' class='btn btn-danger btn-xs'>HAPUS</a>
					</td>
				</tr>";
				$no++;
			}
			echo "</tbody></table>
			</div>
		</div>";
			break;
		case "edit_riwayat":
			$idr = isset($_GET['idr']) ? intval($_GET['idr']) : 0;
			$id_pelanggan = isset($_GET['id']) ? intval($_GET['id']) : 0;
			$redirect_to = function ($url) {
				if (!headers_sent()) {
					header('location:'.$url);
				} else {
					echo "<script>window.location='".htmlspecialchars($url, ENT_QUOTES, 'UTF-8')."';</script>";
				}
				exit;
			};
			if ($idr <= 0) {
				$_SESSION['flash'] = "<div class='alert alert-danger'>Data riwayat tidak valid.</div>";
				$redirect_to('../../media_admin.php?module=swamedikasi');
			}

			if ($id_pelanggan > 0) {
				$stmt = $db->prepare("SELECT * FROM riwayat_pelanggan WHERE id = ? AND id_pelanggan = ?");
				$stmt->execute([$idr, $id_pelanggan]);
			} else {
				$stmt = $db->prepare("SELECT * FROM riwayat_pelanggan WHERE id = ?");
				$stmt->execute([$idr]);
			}
			if ($stmt->rowCount() < 1) {
				$_SESSION['flash'] = "<div class='alert alert-danger'>Riwayat tidak ditemukan.</div>";
				if ($id_pelanggan > 0) {
					$redirect_to('../../media_admin.php?module=pelanggan&act=riwayat&id='.$id_pelanggan);
				} else {
					$redirect_to('../../media_admin.php?module=swamedikasi');
				}
			}
			$rw = $stmt->fetch(PDO::FETCH_ASSOC);
			$id_pelanggan = isset($rw['id_pelanggan']) ? intval($rw['id_pelanggan']) : $id_pelanggan;
			$token = isset($_SESSION['csrf_pelanggan']) ? $_SESSION['csrf_pelanggan'] : '';
			$has_riwayat_obat_table = ($db->query("SHOW TABLES LIKE 'riwayat_pelanggan_obat'")->rowCount() > 0);
			$riwayat_obat = [];
			if ($has_riwayat_obat_table) {
				$riwayat_obat_stmt = $db->prepare("SELECT kd_barang, nm_barang, aturan_pakai FROM riwayat_pelanggan_obat WHERE id_riwayat = ? ORDER BY id ASC");
				$riwayat_obat_stmt->execute([$rw['id']]);
				$riwayat_obat = $riwayat_obat_stmt->fetchAll(PDO::FETCH_ASSOC);
			}
			echo "
		  <div class='box box-primary box-solid'>
			<div class='box-header with-border'>
				<h3 class='box-title'>UBAH RIWAYAT PELANGGAN</h3>
				<div class='box-tools pull-right'>
					<button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
				</div>
			</div>
			<div class='box-body table-responsive'>
			";
			if (!$has_riwayat_obat_table) {
				echo "<div class='alert alert-warning'>Tabel detail obat belum tersedia. Jalankan migration <b>20260313_add_table_riwayat_pelanggan_obat.sql</b> terlebih dahulu.</div>";
			}
			echo "
			<form method=POST action='$aksi?module=pelanggan&act=update_riwayat' enctype='multipart/form-data' class='form-horizontal'>
				<input type=hidden name='id_pelanggan' value='".$id_pelanggan."'>
				<input type=hidden name='id_riwayat' value='".$rw['id']."'>
				<input type=hidden name='token' value='".$token."'>
				<div class='form-group'>
					<label class='col-sm-2 control-label'>Tanggal</label>
					<div class='col-sm-4'>
						<input type='date' name='tgl' class='form-control' required='required' value='".$rw['tgl']."'>
					</div>
				</div>
				<div class='form-group'>
					<label class='col-sm-2 control-label'>Diagnosa</label>
					<div class='col-sm-4'>
						<textarea name='diagnosa' class='form-control' rows='3'>".htmlspecialchars($rw['diagnosa'])."</textarea>
					</div>
				</div>
				<div class='form-group'>
					<label class='col-sm-2 control-label'>Tindakan</label>
					<div class='col-sm-4'>
						<div id='obat-wrap-edit'>";
			if (count($riwayat_obat) > 0) {
				foreach ($riwayat_obat as $item) {
					echo "<div class='obat-row' style='margin-bottom:8px;'>
						<div class='col-sm-7' style='padding-left:0;'>
						    <div id='containerObat'>
                                <div class='row-obat'>
                                    <div class='autocomplete-wrapper'>
                                        <input type='hidden' name='obat_kd[]' class='obat-kd' value='".htmlspecialchars($item['kd_barang'])."'>
							            <input type='text' name='obat_nama[]' class='form-control obat-nama' placeholder='Nama obat (ketik lalu Enter)' value='".htmlspecialchars($item['nm_barang'])."'>
                                        <div class='autocomplete-panel'></div>
                                    </div>
                                </div>
                            </div>
							
						</div>
						<div class='col-sm-5' style='padding-right:0;'>
							<div class='input-group'>
								<input type='text' name='aturan_pakai[]' class='form-control' placeholder='Aturan pakai' value='".htmlspecialchars($item['aturan_pakai'])."'>
								<span class='input-group-btn'>
									<button type='button' class='btn btn-danger btn-remove-obat'>x</button>
								</span>
							</div>
						</div>
					</div>";
				}
			} else {
				echo "<div class='obat-row' style='margin-bottom:8px;'>
					<div class='col-sm-7' style='padding-left:0;'>
					    <div id='containerObat'>
                            <div class='row-obat'>
                                <div class='autocomplete-wrapper'>
                                    <input type='hidden' name='obat_kd[]' class='obat-kd' value=''>
						            <input type='text' name='obat_nama[]' class='form-control obat-nama' placeholder='Nama obat (ketik lalu Enter)'>
                                    <div class='autocomplete-panel'></div>
                                </div>
                            </div>
                        </div>
						
					</div>
					<div class='col-sm-5' style='padding-right:0;'>
						<div class='input-group'>
							<input type='text' name='aturan_pakai[]' class='form-control' placeholder='Aturan pakai' value='".htmlspecialchars($rw['tindakan'])."'>
							<span class='input-group-btn'>
								<button type='button' class='btn btn-danger btn-remove-obat'>x</button>
							</span>
						</div>
					</div>
				</div>";
			}
			echo "
						</div>
						<button type='button' id='btn-tambah-obat-edit' class='btn btn-default btn-sm'>+Tambah Obat</button>
					</div>
				</div>
				<div class='form-group'>
					<label class='col-sm-2 control-label'>Saran Konsultasi</label>
					<div class='col-sm-4'>
						<textarea name='followup' class='form-control' rows='3'>".htmlspecialchars($rw['followup'])."</textarea>
					</div>
				</div>
				<div class='form-group'>
					<label class='col-sm-2 control-label'></label>
					<div class='col-sm-5'>
						<input class='btn btn-primary' type=submit value=UPDATE>
						<input class='btn btn-default' type=button value=BATAL onclick='self.history.back()'>
					</div>
				</div>
			</form>
			</div>
		</div>";
			break;
		case "edit": 
			$stmt = $db->prepare("SELECT * FROM pelanggan WHERE id_pelanggan = ?");
			$stmt->execute([$_GET['id']]);
			$r = $stmt->fetch(PDO::FETCH_ASSOC);
			$selected_pria = ($r['jenis_kelamin'] == 'PRIA') ? 'selected' : '';
			$selected_wanita = ($r['jenis_kelamin'] == 'WANITA') ? 'selected' : '';
			$selected_kosong = ($r['jenis_kelamin'] == '' || $r['jenis_kelamin'] === null) ? 'selected' : '';

			echo "
		  <div class='box box-primary box-solid'>
				<div class='box-header with-border'>
					<h3 class='box-title'>UBAH</h3>
					<div class='box-tools pull-right'>
						<button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
                    </div><!-- /.box-tools -->
				</div>
				<div class='box-body table-responsive'>
						<form method=POST method=POST action=$aksi?module=pelanggan&act=update_pelanggan  enctype='multipart/form-data' class='form-horizontal'>
							  <input type=hidden name=id value='$r[id_pelanggan]'>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Nama Pelanggan</label>        		
									 <div class='col-sm-4'>
										<input type=text name='nm_pelanggan' class='form-control' value='$r[nm_pelanggan]' autocomplete='off'>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Jenis Kelamin</label>        		
									 <div class='col-sm-4'>
										<select name='jenis_kelamin' id='jenis_kelamin' class='form-control' required='required'>
											<option value='' $selected_kosong>- Pilih -</option>
											<option value='PRIA' $selected_pria>PRIA</option>
											<option value='WANITA' $selected_wanita>WANITA</option>
										</select>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Tanggal lahir</label>        		
									 <div class='col-sm-4'>
										<input type='date' name='tanggal_lahir' class='form-control' value='$r[tanggal_lahir]' autocomplete='off'>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Telepon</label>        		
									 <div class='col-sm-4'>
										<input type=text name='tlp_pelanggan' class='form-control' value='$r[tlp_pelanggan]' autocomplete='off'>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Alamat</label>        		
									 <div class='col-sm-4'>
										<textarea name='alamat_pelanggan' class='form-control' rows='3'>$r[alamat_pelanggan]</textarea>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Keterangan</label>        		
									 <div class='col-sm-4'>
										<textarea name='ket_pelanggan' class='form-control' rows='3'>$r[ket_pelanggan]</textarea>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'></label>       
										<div class='col-sm-5'>
											<input class='btn btn-primary' type=submit value=SIMPAN>
								<input class='btn btn-primary' type=button value=BATAL onclick='self.history.back()'>
							  
				</div> 
				
			</div>";




			break;

	}
}
?>

<!-- Modal itemmat -->
<div id="ModalItem" class="modal fade" role="dialog">
    <div class="modal-lg modal-dialog" style="width:90%">
        <div class="modal-content table-responsive">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">PILIH ITEM BARANG</h4>

                <div id="box">
                    <CENTER><strong>MySIFA PROFIT ANALYSIS</strong></CENTER><br>
                    <center><button type="button" class="btn btn-info">PROFIT>30%</button>
                        <button type="button" class="btn btn-success">PROFIT = 25 - 30 % </button>
                        <button type="button" class="btn btn-warning">PROFIT = 20 - 25%"</button>
                        <button type="button" class="btn btn-danger">PROFIT < 20% </button>
                    </center>
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
                            <th style="vertical-align: middle; background-color: #008000; text-align: center; ">Harga Beli</th>
                            <th style="vertical-align: middle; background-color: #008000; text-align: center; width: 50px">Harga Jual</th>
                            <th style="vertical-align: middle; background-color: #008000; text-align: center; ">Komisi</th>
                            <th style="vertical-align: middle; background-color: #008000; text-align: center; ">Komposisi</th>
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


<script>
    /* =========================
       Modal Item Pop Up
    ========================= */
    $(document).on('click', '.item',function(){
        var dta = $(this).data('rowi');
        var $row = $(this).closest(".row-obat");
        var $item = $row.find("#item_"+dta);
        $item.addClass('popup');
    });
    
    $('#ModalItem').on('hidden.bs.modal', function() {
        $('.obat-nama').removeClass('popup');
    });
    
    $('#ModalItem').on('shown.bs.modal', function () {
        
        $("#example").DataTable().destroy();
    
        $("#example").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": "modul/mod_trkasir/barang-serverside.php?action=table_data",
                "dataType": "JSON",
                "type": "POST"
            },
            "rowCallback": function(row, data, index) {
                let q = (data['hrgjual_barang'] - data['hrgsat_barang']) / data['hrgsat_barang'];
    
                if (q <= 0.2) {
                    $(row).find('td:eq(6)').css('background-color', '#ff003f');
                    $(row).find('td:eq(6)').css('color', '#ffffff');
                } else if (q > 0.2 && q <= 0.25) {
                    $(row).find('td:eq(6)').css('background-color', '#f39c12');
                    $(row).find('td:eq(6)').css('color', '#ffffff');
    
                } else if (q > 0.25 && q <= 0.3) {
                    $(row).find('td:eq(6)').css('background-color', '#00ff3f');
                    $(row).find('td:eq(6)').css('color', '#ffffff');
    
                } else if (q > 0.3) {
                    $(row).find('td:eq(6)').css('background-color', '#00bfff');
                    $(row).find('td:eq(6)').css('color', '#ffffff');
    
                }
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
                    "visible": <?= ($_SESSION['level'] == 'pemilik') ? 'true' : 'false'; ?>,
                    "render": function(data, type, row) {
                        return formatRupiah(data);
                    }
                },
                {
                    "data": "hrgjual_barang",
                    "className": 'text-left',
                    // "render": function(data, type, row) {
                    //     return formatRupiah(data);
                    // }
                },
                {
                    "data": "komisi",
                    "className": 'text-right',
                    "render": function(data, type, row) {
                        return formatRupiah(data);
                    }
                },
                {
                    "data": "indikasi",
                    "className": 'text-center'
                },
                {
                    "data": "pilih",
                    "className": 'text-center'
                },
            ],
            "footerCallback": function(row, data, start, end, display) {
                    // console.log(row);
            }
        });
    });
    
    $(document).on('click', '#pilihbarang', function() {

        var kd_barang       = $(this).data('kd_barang');
        var nm_barang       = $(this).data('nm_barang');
        
        var input           = document.getElementsByClassName('popup');
        
        // var nama = $(this).data("nama");
        // var kode = $(this).data("kode");
    
        var $row = $(input).closest(".row-obat");
    
        $row.find(".popup").val(nm_barang);
        $row.find(".obat-kd").val(kd_barang);
        
        //hilangkan modal
        $(".close").click();

    });
$(document).ready(function() {
// 	function resolveObatByName($row) {
// 		var $nama = $row.find('.obat-nama');
// 		var namaVal = $.trim($nama.val());
// 		$row.find('.obat-kd').val('');

// 		if (namaVal === '') {
// 			return;
// 		}

// 		function setResolvedItem(data) {
// 			if (!data || !data.length) {
// 				return false;
// 			}
// 			var item = data[0];
// 			$row.find('.obat-kd').val(item.kd_barang);
// 			$nama.val(item.nm_barang);
// 			return true;
// 		}

// 		$.ajax({
// 			url: 'modul/mod_trkasir/autonamabarang_enter.php',
// 			type: 'post',
// 			dataType: 'json',
// 			data: {
// 				'nm_barang': namaVal
// 			}
// 		}).done(function(data) {
// 			if (setResolvedItem(data)) {
// 				return;
// 			}

// 			$.ajax({
// 				url: 'modul/mod_trkasir/autonamabarang.php',
// 				type: 'post',
// 				dataType: 'json',
// 				data: {
// 					'query': namaVal
// 				}
// 			}).done(function(listNama) {
// 				if (!listNama || !listNama.length) {
// 					return;
// 				}

// 				var namaTerpilih = $.trim(listNama[0]);
// 				if (namaTerpilih === '') {
// 					return;
// 				}

// 				$.ajax({
// 					url: 'modul/mod_trkasir/autonamabarang_enter.php',
// 					type: 'post',
// 					dataType: 'json',
// 					data: {
// 						'nm_barang': namaTerpilih
// 					}
// 				}).done(function(exactData) {
// 					setResolvedItem(exactData);
// 				});
// 			});
// 		});
// 	}

// 	function bindObatAutocomplete(contextSelector) {
// 		function activateFirstTypeaheadItem($input) {
// 			var $menu = $input.siblings('ul.typeahead.dropdown-menu:visible, .dropdown-menu.typeahead:visible, ul.typeahead:visible');
// 			if (!$menu.length) {
// 				$menu = $('ul.typeahead.dropdown-menu:visible, .dropdown-menu.typeahead:visible, ul.typeahead:visible').last();
// 			}
// 			if (!$menu.length) {
// 				return;
// 			}

// 			$menu.find('li.active').removeClass('active');
// 			var $first = $menu.find('li:visible:first');
// 			if ($first.length) {
// 				$first.addClass('active');
// 			}
// 		}

// 		function activateFirstJqueryUiItem($input) {
// 			if (!($.ui && $.ui.autocomplete) || !$input.data('ui-autocomplete')) {
// 				return;
// 			}

// 			var inst = $input.autocomplete('instance');
// 			if (!inst || !inst.menu || !inst.menu.element) {
// 				return;
// 			}

// 			var $firstItem = inst.menu.element.children(':visible:first');
// 			if ($firstItem.length) {
// 				inst.menu.focus($.Event('mouseenter'), $firstItem);
// 			}
// 		}

// 		function activateFirstSuggestion($input) {
// 			setTimeout(function() {
// 				activateFirstTypeaheadItem($input);
// 				activateFirstJqueryUiItem($input);
// 			}, 40);
// 		}

// 		function normalizeItems(data) {
// 			if (typeof data === 'string') {
// 				try {
// 					data = $.parseJSON(data);
// 				} catch (e) {
// 					data = [];
// 				}
// 			}
// 			if (!$.isArray(data)) {
// 				return [];
// 			}
// 			return data;
// 		}
    
        
// 		$(contextSelector).find('.obat-nama').each(function() {
// 			var $input = $(this);
// 			if ($input.data('obat-autocomplete-ready')) {
// 				return;
// 			}
// 			$input.data('obat-autocomplete-ready', true);

// 			if ($.isFunction($input.typeahead)) {
// 				$input.typeahead({
// 					autoSelect: true,
// 					source: function(query, process) {
// 						return $.post('modul/mod_trkasir/autonamabarang.php', {
// 							query: query
// 						}, function(data) {
// 							return process(normalizeItems(data));
// 						});
// 					},
// 					afterSelect: function(item) {
// 						$input.val(item);
// 						resolveObatByName($input.closest('.obat-row'));
// 					}
// 				});
// 				$input.on('keyup focus', function() {
// 					activateFirstSuggestion($input);
// 				});
// 			} else if ($.ui && $.ui.autocomplete) {
// 				$input.autocomplete({
// 					minLength: 1,
// 					open: function() {
// 						activateFirstSuggestion($input);
// 					},
// 					source: function(request, response) {
// 						$.ajax({
// 							url: 'modul/mod_trkasir/autonamabarang.php',
// 							type: 'post',
// 							data: {
// 								query: request.term
// 							},
// 							success: function(data) {
// 								response(normalizeItems(data));
// 							},
// 							error: function() {
// 								response([]);
// 							}
// 						});
// 					},
// 					select: function(event, ui) {
// 						$input.val(ui.item.value);
// 						resolveObatByName($input.closest('.obat-row'));
// 						return false;
// 					}
// 				});
// 			}

// 			$input.on('typeahead:selected typeahead:autocompleted', function() {
// 				resolveObatByName($input.closest('.obat-row'));
// 			});

// 			$input.on('keydown', function(e) {
// 				if (e.which === 13) {
// 					if ($('.ui-autocomplete:visible').length) {
// 						activateFirstSuggestion($input);
// 						setTimeout(function() {
// 							resolveObatByName($input.closest('.obat-row'));
// 						}, 80);
// 						return;
// 					}

// 					var $menu = $input.siblings('ul.typeahead.dropdown-menu:visible');
// 					if ($menu.length) {
// 						e.preventDefault();
// 						var $active = $menu.find('li.active');
// 						if ($active.length) {
// 							$input.val($.trim($active.text()));
// 						}
// 						setTimeout(function() {
// 							resolveObatByName($input.closest('.obat-row'));
// 						}, 50);
// 						return;
// 					}

// 					e.preventDefault();
// 					resolveObatByName($input.closest('.obat-row'));
// 				}
// 			});

// 			$input.on('blur', function() {
// 				resolveObatByName($input.closest('.obat-row'));
// 			});

// 			$input.on('input', function() {
// 				$input.closest('.obat-row').find('.obat-kd').val('');
// 			});
// 		});
// 	}

    
    // $(document).on("keyup", ".obat-nama", function(){
        
        
    //     var $row = $(this).closest(".row-obat");
    //     var $wrapper = $row.find(".autocomplete-wrapper");
    
    //     var keyword = $(this).val().trim();
    //     var panel = $wrapper.find(".autocomplete-panel");
    
    //     if(keyword.length < 2){
    //         panel.hide();
    //         return;
    //     }
    
    //     $.ajax({
    //         url: "modul/mod_pelanggan/autonamabarang.php",
    //         type: "POST",
    //         dataType: "json",
    //         data:{
    //             query: keyword
    //         },
    //         success:function(data){
    
    //             panel.empty();
    
    //             if(!data || data.length === 0){
    
    //                 panel.append('<div class="autocomplete-empty">Obat tidak ditemukan</div>');
    //                 panel.show();
    //                 return;
    
    //             }
    
    //             panel.append('<div class="autocomplete-header">Hasil Pencarian</div>');
    
    //             data.forEach(function(item){
    
    //                 var html = `
    //                 <div class="autocomplete-item"
    //                      data-kode="${item.kd_barang}"
    //                      data-nama="${item.nm_barang}">
    
    //                     💊 ${item.nm_barang}
    
    //                 </div>`;
    
    //                 panel.append(html);
    
    //             });
    
    //             panel.show();
    
    //         }
    //     });
    
    // });
    
    
    // $(document).on("click",".autocomplete-item",function(){

    //     var nama = $(this).data("nama");
    //     var kode = $(this).data("kode");
    
    //     var $row = $(this).closest(".row-obat");
    
    //     $row.find(".obat-nama").val(nama);
    //     $row.find(".obat-kd").val(kode);
    
    //     $row.find(".autocomplete-panel").hide();
    
    // });
    
    // $(document).click(function(e){

    //     if(!$(e.target).closest(".autocomplete-wrapper").length){
    //         $(".autocomplete-panel").hide();
    //     }
    
    // });
    
    var selectedIndex = -1;
    var delayTimer;
    
    
    /* =========================
       AUTOCOMPLETE SEARCH
    ========================= */
    
    $(document).on("keyup", ".obat-nama", function(e){
    
        // skip navigation keys
        if(e.keyCode == 38 || e.keyCode == 40 || e.keyCode == 13){
            return;
        }
    
        clearTimeout(delayTimer);
    
        var input = this;
    
        delayTimer = setTimeout(function(){
    
            var $row = $(input).closest(".row-obat");
            var $wrapper = $row.find(".autocomplete-wrapper");
    
            var keyword = $(input).val().trim();
            var panel = $wrapper.find(".autocomplete-panel");
    
            if(keyword.length < 2){
                panel.hide();
                return;
            }
    
            $.ajax({
                url: "modul/mod_pelanggan/autonamabarang.php",
                type: "POST",
                dataType: "json",
                data:{
                    query: keyword
                },
                success:function(data){
    
                    selectedIndex = -1;
    
                    panel.empty();
    
                    if(!data || data.length === 0){
    
                        panel.append('<div class="autocomplete-empty">Obat tidak ditemukan</div>');
                        panel.show();
                        return;
    
                    }
    
                    panel.append('<div class="autocomplete-header">Hasil Pencarian</div>');
    
                    data.forEach(function(item){
    
                        var html = `
                        <div class="autocomplete-item"
                             data-kode="${item.kd_barang}"
                             data-nama="${item.nm_barang}">
                             💊 ${item.nm_barang}
                        </div>`;
    
                        panel.append(html);
    
                    });
    
                    panel.show();
    
                }
            });
    
        },300);
    
    });



    /* =========================
       KEYBOARD NAVIGATION
    ========================= */
    
    $(document).on("keydown", ".obat-nama", function(e){
    
        var $row = $(this).closest(".row-obat");
        var panel = $row.find(".autocomplete-panel");
        var items = panel.find(".autocomplete-item");
    
        if(!panel.is(":visible")) return;
    
    
        // ARROW DOWN
        if(e.keyCode == 40){
    
            e.preventDefault();
    
            selectedIndex++;
    
            if(selectedIndex >= items.length){
                selectedIndex = 0;
            }
    
            items.removeClass("active");
    
            var activeItem = items.eq(selectedIndex);
    
            activeItem.addClass("active");
    
            panel.scrollTop(
                activeItem.position().top + panel.scrollTop()
            );
    
        }
    
    
        // ARROW UP
        if(e.keyCode == 38){
    
            e.preventDefault();
    
            selectedIndex--;
    
            if(selectedIndex < 0){
                selectedIndex = items.length - 1;
            }
    
            items.removeClass("active");
    
            var activeItem = items.eq(selectedIndex);
    
            activeItem.addClass("active");
    
            panel.scrollTop(
                activeItem.position().top + panel.scrollTop()
            );
    
        }
    
    
        // ENTER
        if(e.keyCode == 13){
    
            e.preventDefault();
    
            if(selectedIndex >= 0){
    
                items.eq(selectedIndex).click();
    
            }else if(items.length > 0){
    
                items.eq(0).click();
    
            }
    
            selectedIndex = -1;
    
        }
    
    });



    /* =========================
       CLICK RESULT
    ========================= */
    
    $(document).on("click",".autocomplete-item",function(){
    
        var nama = $(this).data("nama");
        var kode = $(this).data("kode");
    
        var $row = $(this).closest(".row-obat");
    
        $row.find(".obat-nama").val(nama);
        $row.find(".obat-kd").val(kode);
    
        $row.find(".autocomplete-panel").hide();
    
    });



    /* =========================
       CLOSE PANEL IF CLICK OUTSIDE
    ========================= */

    $(document).click(function(e){
    
        if(!$(e.target).closest(".autocomplete-wrapper").length){
            $(".autocomplete-panel").hide();
        }
    
    });

    var i = 1;
	function addObatRow(wrapperSelector) {
		var wrap = $(wrapperSelector);
		if (!wrap.length) {
			return;
		}
		
		i++;
		var rowHtml = ""
			+ "<div class='obat-row' style='margin-bottom:8px;'>"
			+ "<div class='col-sm-7' style='padding-left:0;'>"
			+ "<div id='containerObat'>"
            + "<div class='row-obat'>"
			+ "<div class='autocomplete-wrapper'>"
			+ "<input type='hidden' name='obat_kd[]' class='obat-kd' value=''>"
			+ "<div class='input-group'>"
            + "<input type='text' name='obat_nama[]' class='form-control obat-nama' placeholder='Nama obat (ketik lalu Enter)' id='item_"+i+"'>"
            + "<div class='input-group-addon'>"
            + "<button type='button' data-toggle='modal' data-target='#ModalItem' href='#' class='item' data-rowi='"+i+"'><span class='glyphicon glyphicon-search'></span></button>"
            + "</div>"
            + "</div>"
			+ "<div class='autocomplete-panel'></div>"
			+ "</div>"
			+ "</div>"
			+ "</div>"
			+ "</div>"
			+ "<div class='col-sm-5' style='padding-right:0;'>"
			+ "<div class='input-group'>"
			+ "<input type='text' name='aturan_pakai[]' class='form-control' placeholder='Aturan pakai'>"
			+ "<span class='input-group-btn'>"
			+ "<button type='button' class='btn btn-danger btn-remove-obat'>x</button>"
			+ "</span>"
			+ "</div>"
			+ "</div>"
			+ "</div>";


		wrap.append(rowHtml);
// 		bindObatAutocomplete(wrapperSelector);
	}

	$('#btn-tambah-obat').on('click', function() {
		addObatRow('#obat-wrap');
	});

	$('#btn-tambah-obat-edit').on('click', function() {
		addObatRow('#obat-wrap-edit');
	});

	$(document).on('click', '.btn-remove-obat', function() {
		var wrap = $(this).closest('[id^="obat-wrap"]');
		if (wrap.find('.obat-row').length <= 1) {
			return;
		}
		$(this).closest('.obat-row').remove();
	});

// 	bindObatAutocomplete('body');

    $(document).on('click', '.tgl_followup', function() {

        var id = $(this).data('id');
        $.ajax({
            url: "modul/mod_pelanggan/updateFollowUp.php",
            type: "POST",
            dataType: "json",
            data:{
                id: id
            },
            success:function(data){
                if(data.status === 'success'){
                    window.location.reload();
                }
            }
        });
    });
    
    
});
</script>
<style>
.typeahead.dropdown-menu > li.active > a,
ul.typeahead.dropdown-menu > li.active > a,
.dropdown-menu.typeahead > li.active > a {
	background-color: #2f86b8 !important;
	color: #fff !important;
}

.ui-autocomplete .ui-state-active,
.ui-autocomplete .ui-menu-item-wrapper.ui-state-active,
.ui-autocomplete .ui-menu-item-wrapper.ui-state-focus {
	background: #2f86b8 !important;
	border-color: #2f86b8 !important;
	color: #fff !important;
}
</style>