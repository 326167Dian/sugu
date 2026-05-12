<?php
include "../../../configurasi/koneksi.php";

$kode       = $_POST['kode'];
$tampildata = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM orders WHERE kd_trbmasuk='$kode'");
$td         = mysqli_fetch_array($tampildata);

?>
<form onsubmit='return false;' method="POST" action='#' enctype='multipart/form-data' class='form-horizontal'>
							 
					<div class='col-lg-6'>
                        <div class='form-group'>
						    <label class='col-sm-4 control-label'>Tanggal</label>
							    <div class='col-sm-8'>
								    <div class='input-group date'>
									    <div class='input-group-addon'>
										    <span class='glyphicon glyphicon-th'></span>
										</div>
										<input type='text' class='datepicker' name='tgl_trbmasuk' id='tgl_trbmasuk' required='required' value='<?= $td['tgl_trbmasuk']; ?>' autocomplete='off'>
									</div>
								</div>
										
								<label class='col-sm-4 control-label'>Kode Transaksi</label>        		
								<div class='col-sm-8'>
								    <input type=text name='kd_hid' id='kd_hid' class='form-control' required='required' value='<?= $td['kd_trbmasuk']; ?>' autocomplete='off' Disabled>
								</div>
										
								<label class='col-sm-4 control-label'>Supplier</label>        		
								<div class='col-sm-8'>
								    <!--<div class='input-group'>-->
									    <input type='text' class='form-control' name='nm_supplier' id='nm_supplier' value='<?= $td['nm_supplier']; ?>' required='required' autocomplete='off' Disabled>
										<!--<div class='input-group-addon'>-->
										<!--    <button type=button data-toggle='modal' data-target='#ModalSupplier' href='#'><span class='glyphicon glyphicon-search'></span></button>-->
										<!--</div>-->
									<!--</div>-->
								</div>
									
								<label class='col-sm-4 control-label'>Telepon</label>        		
								<div class='col-sm-8'>
								    <input type='text' name='tlp_supplier' id='tlp_supplier' value='<?= $td['tlp_supplier']; ?>' class='form-control' autocomplete='off'>
								</div>
										
								<label class='col-sm-4 control-label'>Alamat</label>        		
								<div class='col-sm-8'>
								    <textarea name='alamat_supplier' id='alamat_supplier' class='form-control' rows='2' style="resize:none"><?= $td['alamat_trbmasuk']; ?></textarea>
								</div>
										
								<label class='col-sm-4 control-label'>Jenis Pesanan</label>        		
								<div class='col-sm-8'>
								    <textarea name='ket_trbmasuk' id='ket_trbmasuk' class='form-control' rows='2' style="resize:none"><?= $td['ket_trbmasuk']; ?></textarea>
								</div>	
							</div>
										
						</div>
							  
					</div>
						
				</form>