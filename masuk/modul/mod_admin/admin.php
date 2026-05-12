<?php
session_start();
if (empty($_SESSION['username']) and empty($_SESSION['passuser'])) {
	echo "<link href=../css/style.css rel=stylesheet type=text/css>";
	echo "<div class='error msg'>Untuk mengakses Modul anda harus login.</div>";
} else {

	$aksi = "modul/mod_admin/aksi_admin.php";
	switch ($_GET['act']) {
			// Tampil User
		default:
			if ($_SESSION['level'] == 'pemilik') {
				$tampil_admin = $db->query("SELECT * FROM admin where id_admin != 1 ORDER BY username");
?>
				<div class="box box-primary box-solid">
					<div class="box-header with-border">
						<h3 class="box-title">Operator</h3>
						<div class="box-tools pull-right">
							<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						</div><!-- /.box-tools -->
					</div>
					<div class="box-body table-responsive">
						<a class='btn  btn-success btn-flat' href='?module=admin&act=tambahadmin'>Tambah</a>
						<a class='btn  btn-info btn-flat' href='?module=admin&act=loglogin'>Log Login</a>
						<br><br>
						<table id="example1" class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>No</th>
									<th>Username</th>
									<th>Nama</th>
									<th>Telp/HP</th>
									<th>Blokir</th>
									<th>Komisi</th>
									<?php if ($_SESSION['level'] == 'pemilik') : ?>
										<th>Aksi</th>
									<?php endif; ?>
								</tr>
							</thead>
							<tbody>
								<?php
								$no = 1;
								while ($r = $tampil_admin->fetch(PDO::FETCH_ASSOC)) {
									echo "<tr><td>$no</td>
									 <td>$r[username]</td>
									 <td>$r[nama_lengkap]</td>
									 <td>$r[no_telp]</td>
									 <td>$r[blokir]</td>
									 <td>$r[komisi]</td>
									 <td>";
									if ($_SESSION['level'] == 'pemilik') :
										if ($r['id_admin'] == "1") {
											echo "<a href='?module=admin&act=editadmin&id=$r[id_admin]' title='Edit' class='btn btn-warning btn-xs'>EDIT</a>
									 ";
										} else {
											echo "
									 <a href='?module=admin&act=editadmin&id=$r[id_admin]' title='Edit' class='btn btn-warning btn-xs'>EDIT</a>
									 <a href=javascript:confirmdelete('$aksi?module=admin&act=hapus&id=$r[id_admin]') title='Hapus' class='btn btn-danger btn-xs'>HAPUS</a>
									 ";
										}
									endif;
									echo "
									 </td></tr>";
									$no++;
								}
								?>
							</tbody>
						</table>
					</div>
				</div>


<?php
			} else {
				echo "<link href=../css/style.css rel=stylesheet type=text/css>";
				echo "<div class='error msg'>Anda tidak berhak mengakses halaman ini.</div>";
			}
			break;

		case "loglogin":
			if ($_SESSION['level'] == 'pemilik') {
				$tampil_log = $db->query("SELECT * FROM user_login_logs ORDER BY login_time DESC");
?>
				<div class="box box-primary box-solid">
					<div class="box-header with-border">
						<h3 class="box-title">Log Login/Logout</h3>
						<div class="box-tools pull-right">
							<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						</div><!-- /.box-tools -->
					</div>
					<div class="box-body table-responsive">
						<table id="example1" class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>No</th>
									<th>Username</th>
									<th>Login Time</th>
									<th>Logout Time</th>
									<th>IP Address</th>
									<th>Duration</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$no = 1;
								while ($r = $tampil_log->fetch(PDO::FETCH_ASSOC)) {
									$logout_time = $r['logout_time'] ? $r['logout_time'] : 'Still Active';
									$duration = '';
									if ($r['logout_time']) {
										$login_ts = strtotime($r['login_time']);
										$logout_ts = strtotime($r['logout_time']);
										$diff = $logout_ts - $login_ts;
										$hours = floor($diff / 3600);
										$minutes = floor(($diff % 3600) / 60);
										$seconds = $diff % 60;
										$duration = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
									} else {
										$duration = 'Active';
									}
									echo "<tr><td>$no</td>
									 <td>$r[username]</td>
									 <td>$r[login_time]</td>
									 <td>$logout_time</td>
									 <td>$r[ip_address]</td>
									 <td>$duration</td>
									 </tr>";
									$no++;
								}
								?>
							</tbody>
						</table>
					</div>
				</div>
<?php
			} else {
				echo "<link href=../css/style.css rel=stylesheet type=text/css>";
				echo "<div class='error msg'>Anda tidak berhak mengakses halaman ini.</div>";
			}
			break;

		case "tambahadmin":
			if ($_SESSION['level'] == 'pemilik') {
				echo "
		  <div class='box box-primary box-solid'>
				<div class='box-header with-border'>
					<h3 class='box-title'>Tambah Operator</h3>
					<div class='box-tools pull-right'>
						<button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
                    </div><!-- /.box-tools -->
				</div>
				<div class='box-body'>
						<form method=POST action='$aksi?module=admin&act=input_admin' class='form-horizontal'>
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Username</label>        		
									 <div class='col-sm-3'>
										<input type=text name='username' class='form-control' Placeholder='Username' required='required'>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Password</label>        		
									 <div class='col-sm-3'>
										<input type=text name='password' class='form-control' Placeholder='Password' required='required'>
									 </div>
							  </div>
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Nama Lengkap</label>        		
									 <div class='col-sm-6'>
										<input type=text name='nama_lengkap' class='form-control' Placeholder='Nama Lengkap' required='required'>
									 </div>
							  </div>
							  
							   <div class='form-group'>
									<label class='col-sm-2 control-label'>Telp/HP</label>        		
									 <div class='col-sm-4'>
										<input type=text name='no_telp' class='form-control' Placeholder='No Telepon' required='required'>
									 </div>
							  </div>
							   <div class='form-group'>
									<label class='col-sm-2 control-label'>Akses Level</label>        		
									 <div class='col-sm-4'>
										<select class='form-control' name='level'>
											<option value='pemilik'>Pemilik</option>
											<option value='petugas'>Petugas</option>
										</select>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Akses Data Master</label>        		
									 <div class='col-sm-4'>
										<input type=checkbox name='mpengguna[]' value='Y' checked> Operator </br>
										<input type=checkbox name='mheader[]' value='Y' checked> Header Struk </br>
										<input type=checkbox name='mjenisbayar[]' value='Y' checked> Jenis Pembayaran </br>
										<input type=checkbox name='mpelanggan[]' value='Y' checked> Pelanggan </br>
										<input type=checkbox name='msupplier[]' value='Y' checked> Supplier </br>
										<input type=checkbox name='msatuan[]' value='Y' checked> Satuan </br>
										<input type=checkbox name='mjenisobat[]' value='Y' checked> Jenis Obat </br>
										<input type=checkbox name='mbarang[]' value='Y' checked> Item Barang </br>
										<input type=checkbox name='komisi[]' value='Y' checked> Komisi Pegawai </br>
									 </div>
							  </div>
							  
							   <div class='form-group'>
									<label class='col-sm-2 control-label'>Inventory</label>        		
									 <div class='col-sm-4'>
										<input type=checkbox name='mstok[]' value='Y' checked> Nilai Stok Barang </br>
										<input type=checkbox name='stok_kritis[]' value='Y' checked> Stok Kritis </br>
										<input type=checkbox name='orders[]' value='Y' checked> Pesan Barang </br>
										<input type=checkbox name='stokopname[]' value='Y' checked> Stok Opname </br>
										<input type=checkbox name='soharian[]' value='Y' checked> Stok Opname Harian </br>
										<input type=checkbox name='kartustok[]' value='Y' checked> Kartu Stok </br>																			
									 </div>
							  </div>
							  
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Akses Transaksi</label>        		
									 <div class='col-sm-4'>
										<input type=checkbox name='tbm[]' value='Y' checked> Barang Masuk Non PBF </br>
										<input type=checkbox name='tbmpbf[]' value='Y' checked> Barang Masuk dari PBF </br>
										<input type=checkbox name='byrkredit[]' value='Y' checked> Pembayaran kredit Barang Masuk</br>
										<input type=checkbox name='cekdarah[]' value='Y' checked> Cek Darah</br>
										<input type=checkbox name='shiftkerja[]' value='Y' checked> Buka/Tutup Kasir </br>
										<input type=checkbox name='tpk[]' value='Y' checked> Penjualan/Kasir </br>
										<input type=checkbox name='penjualansebelum[]' value='Y' checked> Penjualan Sebelum </br>
										<input type=checkbox name='catatan[]' value='Y' checked> Catatan </br>	
										<input type=checkbox name='jurnalkas[]' value='Y' checked> Jurnal Kas </br>	
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Akses Laporan</label>        		
									 <div class='col-sm-4'>
										<input type=checkbox name='lpitem[]' value='Y' checked> Laporan Item Barang </br>
										<input type=checkbox name='lpbrgmasuk[]' value='Y' checked> Laporan Barang Masuk </br>
										<input type=checkbox name='lpkasir[]' value='Y' checked> Laporan Penjualan/Kasir </br>
										<input type=checkbox name='labapenjualan[]' value='Y' checked> Laporan Laba Penjualan </br>
										<input type=checkbox name='labajenisobat[]' value='Y' checked> Laporan Laba Jenis Obat </br>
										<input type=checkbox name='lpsupplier[]' value='Y' checked> Laporan Data Supplier </br>
										<input type=checkbox name='lppelanggan[]' value='Y' checked> Laporan Data Pelanggan </br>
										<input type='checkbox' name='neraca[]' value='Y' checked> Neraca Laba Rugi </br>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Blokir</label>        		
									 <div class='col-sm-4'></br>
										<input type=radio name='blokir' value='Y'> Y 
										<input type=radio name='blokir' value='N' checked> N
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
			} else {
				echo "<link href=../css/style.css rel=stylesheet type=text/css>";
				echo "<div class='error msg'>Anda tidak berhak mengakses halaman ini.</div>";
			}
			break;



		case "editadmin":
			$edit = $db->query("SELECT * FROM admin WHERE id_admin='$_GET[id]'");
			$r = $edit->fetch(PDO::FETCH_ASSOC);

			echo "
		  <div class='box box-primary box-solid'>
				<div class='box-header with-border'>
					<h3 class='box-title'>Ubah Data Operator</h3>
					<div class='box-tools pull-right'>
						<button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
                    </div><!-- /.box-tools -->
				</div>
				<div class='box-body'>
				
				        <form method=POST action='$aksi?module=admin&act=update_admin' class='form-horizontal' id='frmEditAdmin'>
							  <input type='hidden' name='id' id='id' value='$r[id_admin]'>
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Username</label>        		
									 <div class='col-sm-4'>
										<input type=text name='username' id='username' class='form-control' Placeholder='Username' value='$r[username]'>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Password</label>        		
									 <div class='col-sm-4'>
										<input type=text name='password' id='password' class='form-control' Placeholder='Password' >
										<small>Apabila password tidak diubah, dikosongkan saja.</small>
									</div>
							  </div>
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Nama Lengkap</label>        		
									 <div class='col-sm-6'>
										<input type=text name='nama_lengkap' id='nama_lengkap' class='form-control' Placeholder='nama_lengkap' value='$r[nama_lengkap]'>
									 </div>
							  </div>
							  
							   <div class='form-group'>
									<label class='col-sm-2 control-label'>Telp/HP</label>        		
									 <div class='col-sm-4'>
										<input type=text name='no_telp' id='no_telp' class='form-control' Placeholder='No Telepon' value='$r[no_telp]'>
									 </div>
							  </div>
							  	<div class='form-group'>
									<label class='col-sm-2 control-label'>Akses Level</label>        		
									<div class='col-sm-4'>
										<select class='form-control' name='level' id='level'>
											<option value='$r[akses_level]'>$r[akses_level]</option>
											<option value='pemilik'>Pemilik</option>
											<option value='petugas'>Petugas</option>
										</select>
									</div>
								</div>
								
							 <div class='form-group'>
									<label class='col-sm-2 control-label'>Akses Data Master</label>        		
									 <div class='col-sm-4'>
										<input type=checkbox name='mpengguna[]' id='mpengguna' value='Y' ";	if ($r['mpengguna'] == "Y") {echo "checked";} echo "> Operator </br>
										<input type=checkbox name='mheader[]' id='mheader' value='Y' "; if ($r['mheader'] == "Y") { echo "checked"; } echo "> Header Struk </br>
										<input type=checkbox name='mjenisbayar[]' id='mjenisbayar' value='Y' "; if ($r['mjenisbayar'] == "Y") { echo "checked"; } echo "> Jenis Pembayaran </br>
										<input type=checkbox name='mpelanggan[]' value='Y' "; if ($r['mpelanggan'] == "Y") { echo "checked"; } echo "> Pelanggan </br> 
                                        <input type=checkbox name='msupplier[]' value='Y' "; if ($r['msupplier'] == "Y") { echo "checked"; } echo "> Supplier </br>
										<input type=checkbox name='mjenisobat[]' value='Y' "; if ($r['mjenisobat'] == "Y") { echo "checked"; } echo "> Jenis Obat </br>
										<input type=checkbox name='msatuan[]' value='Y' "; if ($r['msatuan'] == "Y") { echo "checked"; } echo "> Satuan </br>
										<input type=checkbox name='mbarang[]' value='Y' "; if ($r['mbarang'] == "Y") { echo "checked"; } echo "> Item Barang </br>
			                            <input type=checkbox name='komisi[]' value='Y' "; if ($r['komisi'] == "Y") { echo "checked"; }  echo "> Komisi Pegawai </br>
									 </div>
							  </div>
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Inventory</label>        		
									 <div class='col-sm-4'>
										<input type=checkbox name='mstok[]' value='Y' "; if ($r['mstok'] == "Y") { echo "checked";} echo "> Nilai Stok Barang </br>
										<input type=checkbox name='stok_kritis[]' value='Y' "; if ($r['stok_kritis'] == "Y") {echo "checked"; }	echo "> Stok Kritis </br>
										<input type=checkbox name='orders[]' value='Y' ";if ($r['orders'] == "Y") { echo "checked"; } echo "> Pesan Barang </br>
										<input type=checkbox name='stokopname[]' value='Y' "; if ($r['stokopname'] == "Y") { echo "checked"; } echo "> Stok Opname </br>
										<input type=checkbox name='soharian[]' value='Y' "; if ($r['soharian'] == "Y") { echo "checked"; } echo "> Stok Opname Harian</br>
										<input type=checkbox name='kartustok[]' value='Y' "; if ($r['kartustok'] == "Y") { echo "checked"; } echo "> Kartu Stok</br>										
									 </div>
							  </div> 
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Akses Transaksi</label>        		
									 <div class='col-sm-4'>
										<input type=checkbox name='tbm[]' value='Y' "; if ($r['tbm'] == "Y") {echo "checked"; } echo "> Barang Masuk non PBF </br>
										<input type=checkbox name='tbmpbf[]' value='Y' "; if ($r['tbmpbf'] == "Y") { echo "checked"; } echo "> Barang Masuk dari PBF </br>
										<input type=checkbox name='byrkredit[]' value='Y' "; if ($r['byrkredit'] == "Y") { echo "checked"; } echo "> Pembayaran Barang Kredit</br>
										<input type=checkbox name='cekdarah[]' value='Y' "; if ($r['cekdarah'] == "Y") { echo "checked"; } echo "> Cek Darah</br>
										<input type=checkbox name='shiftkerja[]' value='Y' "; if ($r['shiftkerja'] == "Y") { echo "checked"; } echo "> Buka/Tutup Kasir</br>
										<input type=checkbox name='tpk[]' value='Y' "; if ($r['tpk'] == "Y") { echo "checked"; } echo "> Penjualan/Kasir </br>
										<input type=checkbox name='penjualansebelum[]' value='Y' "; if ($r['penjualansebelum'] == "Y") { echo "checked";} echo "> Penjualan sebelum </br>
                                        <input type=checkbox name='catatan[]' value='Y' "; if ($r['catatan'] == "Y") { echo "checked"; } echo "> Catatan</br>
                                        <input type=checkbox name='jurnalkas[]' value='Y' "; if ($r['jurnalkas'] == "Y") { echo "checked"; } echo "> Jurnal Kas</br>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Akses Laporan</label>        		
									 <div class='col-sm-4'>
										<input type=checkbox name='lpitem[]' value='Y' "; if ($r['lpitem'] == "Y") { echo "checked"; } echo "> Laporan Item Barang </br>
										<input type=checkbox name='lpbrgmasuk[]' value='Y' "; if ($r['lpbrgmasuk'] == "Y") { echo "checked"; } echo "> Laporan Barang Masuk </br>
										<input type=checkbox name='lpkasir[]' value='Y' "; if ($r['lpkasir'] == "Y") { echo "checked"; } echo "> Laporan Penjualan/Kasir </br>
										<input type=checkbox name='labapenjualan[]' value='Y' "; if ($r['labapenjualan'] == "Y") { echo "checked"; } echo "> Laporan LABA Penjualan/Kasir </br>
										<input type=checkbox name='labajenisobat[]' value='Y' "; if ($r['labajenisobat'] == "Y") { echo "checked"; } echo "> Laporan LABA Jenis Obat </br>
										<input type=checkbox name='lpsupplier[]' value='Y' "; if ($r['lpsupplier'] == "Y") { echo "checked";} echo "> Laporan Data Supplier </br>
										<input type=checkbox name='lppelanggan[]' value='Y' "; if ($r['lppelanggan'] == "Y") { echo "checked"; } echo "> Laporan Data Pelanggan </br>
										<input type='checkbox' name='neraca[]' value='Y' "; if ($r['neraca'] == "Y") { echo "checked"; } echo "> Neraca Laba Rugi </br>
									 </div>
							  </div>
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Blokir</label>        		
									 <div class='col-sm-4'>";
			                            if ($r['blokir'] == "Y") {
				                            echo "
											<input type=radio name='blokir' value='Y' checked> Y 
										    <input type=radio name='blokir' value='N'> N
											";
			                            }
			                            else {
			                                echo "
											<input type=radio name='blokir' value='Y'> Y 
										    <input type=radio name='blokir' value='N' checked> N
											";
			                            }
			                                echo "
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'></label>       
										<div class='col-sm-5'>
											<input class='btn btn-primary' type=submit value='SIMPAN'>
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