<?php
session_start();
if (empty($_SESSION['username']) and empty($_SESSION['passuser'])) {
	echo "<link href=../css/style.css rel=stylesheet type=text/css>";
	echo "<div class='error msg'>Untuk mengakses Modul anda harus login.</div>";
} else {

	$aksi = "modul/mod_barang/aksi_barang.php";
	$aksi_barang = "masuk/modul/mod_barang/aksi_barang.php";
	switch ($_GET['act']) {
			// Tampil barang
		default:
?>
			<div class="box box-primary box-solid">
				<div class="box-header with-border">
					<h3 class="box-title">STOK KRITIS</h3>
					<!--<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>-->

					<div class="box-tools pull-center">

					</div><!-- /.box-tools -->
				</div>
				<div class="box-body table-responsive">
					<!--<a  class ='btn  btn-success btn-flat' href='?module=barang&act=tambah'>TAMBAH</a>-->
					<CENTER><h2><strong>STOK KRITIS</strong></h2></CENTER><br>
                    <div class="col-sm-12" style="text-align:center">
                        Analisa estimasi pengadaan barang berdasarkan frekuensi transaksi 30 hari terakhir <br>
                        <a class='btn btn-primary' href='?module=stok_kritis&act=kritis30'>PROSES ANALISA DATA</a><br><br>
                        <a class='btn btn-success' href='?module=stok_kritis&act=tampil30'>TAMPILKAN ESTIMASI STOK KRITIS</a><br><br>
                        <a class='btn btn-warning' href='?module=stok_kritis&act=over30'>TAMPILKAN OVERSTOK</a>

                    </div>
                    
                </div>
            </div>

		<?php
			break;

		case "kritis30":

			//$tampil_barang = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM barang where stok_barang <= stok_buffer ORDER BY barang.stok_barang ");

		?>


			<div class="box box-primary box-solid table-responsive">
				<div class="box-header with-border">
					<h3 class="box-title">STOK KRITIS</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div><!-- /.box-tools -->
				</div>
				<div class="box-body table-responsive">
					<!--<a  class ='btn  btn-success btn-flat' href='?module=barang&act=tambah'>TAMBAH</a>-->
					<br><br>

                        <?php
						$tgl_awal = date('Y-m-d');
						$tgl_akhir = date('Y-m-d', strtotime('-30 days', strtotime($tgl_awal)));

						$updateAnalisa = $db->prepare("UPDATE barang b
							JOIN (
								SELECT DISTINCT kd_barang
								FROM trbmasuk_detail
								WHERE kd_trbmasuk IS NOT NULL
							) bm ON bm.kd_barang = b.kd_barang
							LEFT JOIN (
								SELECT td.kd_barang,
									   COUNT(*) AS t30,
									   COALESCE(SUM(td.qty_dtrkasir), 0) AS q30
								FROM trkasir_detail td
								JOIN trkasir t ON t.kd_trkasir = td.kd_trkasir
								WHERE t.tgl_trkasir BETWEEN ? AND ?
								GROUP BY td.kd_barang
							) s ON s.kd_barang = b.kd_barang
							SET b.t30 = COALESCE(s.t30, 0),
								b.q30 = COALESCE(s.q30, 0)");
						$updateAnalisa->execute(array($tgl_akhir, $tgl_awal));
                       // header('location:../../media_admin.php?module=stok_kritis&act=tampil30');
                        echo '<script>window.location.href = "?module=stok_kritis&act=tampil30";</script>';

                        ?>
				</div>
			</div>
        <?php
        break;
        case "tampil30" :

        ?>
           <div class="box-body table-responsive">
               <form method="post" action="modul/mod_lapstok/aksi_simpan_orders.php" id="frmAddOrder">
               <span class="btn btn-primary">LAKU</span> Jumlah Transaksi > 10 per 30 hari 
               <span class="btn btn-success">LANCAR</span> Jumlah Transaksi 6 - 10 per 30 hari 
               <span class="btn btn-warning">SLOW</span> Jumlah Transaksi 1 - 5 per 30 hari
                <hr>
               
                <div class='form-group row'>
    				<label class='col-sm-3 control-label'>Supplier :</label>
    			    <input type="hidden" id="id_supplier" name="id_supplier">
    			    
    		    </div>
    			<div class='form-group row'>
        		    <div class='col-sm-8'>
        			    <div class='input-group'>
    					    <input type='text' class='form-control' name='nm_supplier' id='nm_supplier' required='required' autocomplete='off' readonly>
    						<div class='input-group-addon'>
    						    <button type='button' data-toggle='modal' data-target='#ModalSupplier' id="idmodal" href='#'><span class='glyphicon glyphicon-search'></span></button>
    						</div>
    					</div>
        			</div>
    			</div>
    			
    			<a class='btn  btn-danger btn-flat' href='modul/mod_laporan/cetak_stokkritis.php' target='_blank' style="margin-bottom: 10px">EXPORT TO EXCEL</a>
                <button class='btn btn-success btn-flat' type='submit' id="btn_order" style="margin-bottom: 10px">ADD TO ORDER</button>
                <hr>
    				    
                <table id="example1" class="table table-bordered table-striped table-responsive">
                    <thead>
                        <tr style="text-align:center;">
                            <th>No</th>
                            <th>Kategori</th>
                            <th>Nama Barang</th>
                            <th style="text-align: right; ">Qty/Stok</th>
                            <th style="text-align: right; ">T30</th>                          
                            <th style="text-align: center; ">Q30</th>
                            <th style="text-align: center; ">SFC max30</th>
                            <th style="text-align: center; ">SFCmax/ week</th>
                            <th style="text-align: right; ">Satuan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $hasil30 = $db->prepare("select id_barang,
                                                                kd_barang,
                                                                nm_barang,
                                                                stok_barang,
                                                                sat_barang,
                                                                t30,
                                                                q30
                                                         from barang ");
                            $hasil30->execute();                             
                            $no=1;
                            while($tp30 = $hasil30->fetch(PDO::FETCH_ASSOC)) {
                                $sfc = $tp30['t30'] - $tp30['stok_barang'];
                                $tweek = round((($tp30['t30']-$tp30['stok_barang'])/4),0);
                                $qfc = $tp30['t30'] - $tp30['stok_barang'];
                                $qweek = round((($tp30['q30']-$tp30['stok_barang'])/4 ),0);
                                $idbarang = $tp30['id_barang'];
                                $kdbarang = $tp30['kd_barang'];
                                
                                if( $tp30['t30']>0 && ($tp30['stok_barang']<=(0.25*$tp30['t30']))) {
                                    echo "
                                    <tr>
                                        <td><input type='checkbox' name='check[]' value='$kdbarang'> $no</td>";
                                    if ($tp30['t30'] <= "0") {
                                        echo " <td style='background-color:#ff003f;'align='center'><strong>MACET</strong></td> ";
                                    } elseif ($tp30['t30'] > 0 && $tp30['t30'] <= 5) {
                                        echo "  <td style='background-color:#FFA500;' align='center'><strong>SLOW</strong></td>";
                                    } elseif ($tp30['t30'] > 5 && $tp30['t30'] <= 10) {
                                        echo "  <td style='background-color:#00ff3f;' align='center'><strong>LANCAR</strong></td>";
                                    } elseif ($tp30['t30'] > 10) {
                                        echo "  <td style='background-color:#00bfff;' align='center'><strong>LAKU</strong></td>";
                                    }
                                    echo "                                                         
                                        <td>$tp30[nm_barang]</td>
                                        <td>$tp30[stok_barang]</td>
                                        <td style='text-align:center;'>$tp30[t30]</td>
                                        <td style='text-align:center;'>$tp30[q30]</td>
                                        <td style='text-align:center;'>$qfc</td>                                        
                                        <td style='text-align:center;'>$qweek</td>
                                        <td style='text-align:center;'>$tp30[sat_barang]</td> 
                                     </tr>";
                                    $no++;
                                }
                            }
                            ?>
                    </tbody>
                </table>
                </form>
           </div>
            <div style="text-align:center;">
            <a class="btn btn-success" onclick="self.history.back()">KEMBALI</a>
            </div>
<?php

			break;
		case "over30":
?>
           <div class="box-body table-responsive">
                <table id="example1" class="table table-bordered table-striped table-responsive">
                    <thead>
                    <tr style="text-align:center;">
                        <th>No</th>
                        <th>Kategori</th>
                        <th>Nama Barang</th>
                        <th style="text-align: right; ">Qty/Stok</th>
                        <th style="text-align: right; ">T30</th>
                        <th style="text-align: center; ">Q30</th>
                        <th style="text-align: center; ">On->T</th>
                        <th style="text-align: right; ">Satuan</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $hasil30 = $db->prepare("select id_barang,
                                                                kd_barang,
                                                                nm_barang,
                                                                stok_barang,
                                                                sat_barang,
                                                                hrgsat_barang,
                                                                t30,
                                                                q30
                                                         from barang ");
                    $hasil30->execute();
                    $no=1;
                    while($tp30 = $hasil30->fetch(PDO::FETCH_ASSOC)) {
                        $OnT = $tp30['stok_barang'] - $tp30['t30'] ;
                        $OnQ = $tp30['stok_barang'] - $tp30['q30'] ;
                        if( $tp30['t30']>=0 && ($tp30['stok_barang']>(2*$tp30['q30']))) {
                            echo "
                                    <tr>
                                        <td>$no</td>";
                            if ($tp30['t30'] <= "0") {
                                echo " <td style='background-color:#ff003f;'align='center'><strong>MACET</strong></td> ";
                            } elseif ($tp30['t30'] > 0 && $tp30['t30'] <= 5) {
                                echo "  <td style='background-color:#EDFF00;' align='center'><strong>SLOW</strong></td>";
                            } elseif ($tp30['t30'] > 5 && $tp30['t30'] <= 10) {
                                echo "  <td style='background-color:#00ff3f;' align='center'><strong>LANCAR</strong></td>";
                            } elseif ($tp30['t30'] > 10) {
                                echo "  <td style='background-color:#00bfff;' align='center'><strong>LAKU</strong></td>";
                            }
                            $nilaibarang[] = $tp30['hrgsat_barang'];
                            echo "                                                         
                                        <td>$tp30[nm_barang]</td>
                                        <td>$tp30[stok_barang]</td>
                                        <td>$tp30[t30]</td>
                                        <td>$tp30[q30]</td>
                                        <td>$OnT</td>                                        
                                        <td>$tp30[sat_barang]</td> 
                                     </tr>";                                      
                            $no++;
                        }
                    }
                    ?>
                    </tbody>
                </table>
           </div>
                <div style="text-align:center;">
                    <a class="btn btn-success" onclick="self.history.back()">KEMBALI</a>
                </div>
<?php
			break;
			
		case "orders":
			
			$kdtransaksi = $_GET['id'];
			$ubah = $db->prepare("SELECT * FROM orders 
	                             WHERE orders.kd_trbmasuk =?");
	        $ubah->execute([$kdtransaksi]);
			$re = $ubah->fetch(PDO::FETCH_ASSOC);
			$petugas = $_SESSION['namalengkap'];
			$tglnow = date('Y-m-d', time());
			
			echo "
		  <div class='box box-primary box-solid'>
				<div class='box-header with-border'>
					<h3 class='box-title'>TAMBAH PESANAN BARANG</h3>
					<div class='box-tools pull-right'>
						<button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
                    </div><!-- /.box-tools -->
				</div>
				<div class='box-body table-responsive'>
				
						<form onsubmit='return false;' method='POST' action='$aksi?module=orders&act=input_trbmasuk' enctype='multipart/form-data' class='form-horizontal'>
						
						        <input type='hidden' name='id_trbmasuk' id='id_trbmasuk' value='$re[id_trbmasuk]'>
							   <input type='hidden' name='kd_trbmasuk' id='kd_trbmasuk' value='$kdtransaksi'>
							   <input type='hidden' name='stt_aksi' id='stt_aksi' value='input_trbmasuk'>
							    <input type='hidden' name='id_supplier' id='id_supplier' value='$re[id_supplier]'>
							    <input type=hidden name='petugas' id='petugas' value='$petugas'>
							 
						<div class='col-lg-6'>

							  <div class='form-group'>
							  
									<label class='col-sm-4 control-label'>Tanggal</label>
										<div class='col-sm-6'>
											<div class='input-group date'>
												<div class='input-group-addon'>
													<span class='glyphicon glyphicon-th'></span>
												</div>
													<input type='text' class='datepicker' name='tgl_trbmasuk' id='tgl_trbmasuk' required='required' value='$tglnow' autocomplete='off'>
											</div>
										</div>
										
									<label class='col-sm-4 control-label'>Kode Transaksi</label>        		
										<div class='col-sm-6'>
											<input type=text name='kd_hid' id='kd_hid' class='form-control' required='required' value='$kdtransaksi' autocomplete='off' Disabled>
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
											<select name='ket_trbmasuk' id='ket_trbmasuk' class='form-control' >
    											<option value='REGULER'>REGULER </option>
    											<option value='PREKURSOR'>PREKURSOR </option>
    											<option value='OOT'>OOT</option>
    											<option value='ALKES'>ALKES</option>
    										</select>
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
														<button type=button data-toggle='modal' data-target='#ModalItem' id='kode' href='#'><span class='glyphicon glyphicon-search'></span></button>
													</div>
											</div>
										</div>
									
									<label class='col-sm-4 control-label'>Nama Barang</label>        		
										<div class='col-sm-7'>
											<input type=text name='nmbrg_dtrbmasuk' id='nmbrg_dtrbmasuk' class='typeahead form-control' autocomplete='off'>
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
									    <input type='text' name='satgrosir_dtrbmasuk' id='satgrosir_dtrbmasuk' class='form-control' autocomplete='off' readonly>
										
									 </div>
							
									
										
									<label class='col-sm-4 control-label'>Harga Beli</label>        		
										<div class='col-sm-7'>
											<input type=text name='hrgsat_dtrbmasuk' id='hrgsat_dtrbmasuk' class='form-control' autocomplete='off'>
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
	}
}
?>

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
		        <table id="example4" class="table table-condensed table-bordered table-striped table-hover" >
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
						$tampil_dproyek = $db->prepare("SELECT * FROM supplier ORDER BY nm_supplier ASC");
						$tampil_dproyek->execute();
						while ($rd = $tampil_dproyek->fetch(PDO::FETCH_ASSOC)){
								
						    echo    "<tr style='font-size: 13px;'> 
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
				<table id="tes2" class="table table-condensed table-bordered table-striped table-hover">

					<thead>
						<tr class="judul-table">
							<th style="vertical-align: middle; background-color: #008000; text-align: center; ">No</th>
							<th style="vertical-align: middle; background-color: #008000; text-align: left; ">Kode</th>
							<th style="vertical-align: middle; background-color: #008000; text-align: left; ">Nama Barang</th>
							<th style="vertical-align: middle; background-color: #008000; text-align: right; ">Qty</th>
							<th style="vertical-align: middle; background-color: #008000; text-align: center; ">Satuan</th>
							<th style="vertical-align: middle; background-color: #008000; text-align: center; ">T30</th>
							<th style="vertical-align: middle; background-color: #008000; text-align: center; ">Q30</th>
							<!--<th style="vertical-align: middle; background-color: #008000; text-align: center; ">SF</th>-->
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



                <script>
                    $(document).ready(function() {
                        var start = '<?= $start_date ?>';
                        var finish = '<?= $finish_date ?>';

                        $('#tes').DataTable({
                            processing: true,
                            serverSide: true,
                            ajax: {
                                "url": "modul/mod_lapstok/stokkritis-serverside.php?action=table_data&start=" + start + "&finish=" + finish,
                                "dataType": "JSON",
                                "type": "POST"
                            },
                            "rowCallback": function(row, data, index) {
                                // warna for nomor
                                if (data['t30'] <= 0) {
                                    $(row).find('td:eq(1)').css('background-color', '#dd4b39');
                                    $(row).find('td:eq(1)').css('color', '#ffffff');
                                } else if (data['t30'] > 0 && data['t30'] <= 5) {
                                    $(row).find('td:eq(0)').css('background-color', '#f39c12');
                                    $(row).find('td:eq(0)').css('color', '#ffffff');
                                } else if (data['t30'] > 0 && data['t30'] <= 10) {
                                    $(row).find('td:eq(0)').css('background-color', '#00a65a');
                                    $(row).find('td:eq(0)').css('color', '#ffffff');
                                } else if (data['t30'] > 10) {
                                    $(row).find('td:eq(0)').css('background-color', '#00c0ef');
                                }

                            },
                            columns: [{
                                "data": "no",
                                "className": 'text-center',
                            },
                                {
                                    "data": "kategori",
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
                                    "data": "stok_buffer",
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
                                    "data": "satuan",
                                    "className": 'text-center',
                                },
                                {
                                    "data": "harga_beli",
                                    "className": 'text-right',
                                    "render": function(data, type, row) {
                                        return formatRupiah(data);
                                    }
                                },
                            ],

                        });

                    });
                    
                    // item barang per supplie
                	$(document).on('click', '#kode', function() {
                		$("#tes2").DataTable().destroy();
                	    var start = '<?= $start_date ?>';
                        var finish = '<?= $finish_date ?>';
                        
                	    $('#tes2').DataTable({
                			processing: true,
                			serverSide: true,
                			ajax: {
                				"url": "modul/mod_lapstok/barangorder-serverside.php?action=table_data&start=" + start + "&finish=" + finish,
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
                	
                	$(document).on('click', '#pilihbarang', function() {

                		var nm_barang = $(this).data('nm_barang');
                        $.ajax({
            				url: 'modul/mod_lapstok/autonamabarang_enter.php',
            				type: 'post',
            				data: {
            					'nm_barang': nm_barang
            				},
            			}).success(function(data) {
            			    
                            var json = data;
            				// //replace array [] menjadi '' 
            				var res1 = json.replace("[", "");
            				var res2 = res1.replace("]", "");
            				// // //INI CONTOH ARRAY JASON const json = '{"result":true, "count":42}';
            				var datab = JSON.parse(res2);
            				
            				document.getElementById('id_barang').value              = datab.id_barang;
            				document.getElementById('kd_barang').value              = datab.kd_barang;
            				document.getElementById('nmbrg_dtrbmasuk').value        = datab.nm_barang;
            				document.getElementById('stok_barang').value            = datab.stok_barang;
            				document.getElementById('qty_dtrbmasuk').value          = datab.konversi;
            				document.getElementById('sat_dtrbmasuk').value          = datab.sat_barang;
            				document.getElementById('konversi').value               = datab.konversi;
            				document.getElementById('qtygrosir_dtrbmasuk').value    = "1";
            				document.getElementById('satgrosir_dtrbmasuk').value    = datab.sat_grosir;
            	
            				document.getElementById('hrgsat_dtrbmasuk').value       = datab.hrgsat_barang;
            				
                            $(".close").click();
            			});
                	});
                	
                	$('#kd_barang').keydown(function(e) {
                		if (e.which == 13) { // e.which == 13 merupakan kode yang mendeteksi ketika anda   // menekan tombol enter di keyboard
                			//letakan fungsi anda disini
                
                			var kd_brg = $("#kd_barang").val();
                			$.ajax({
                				url: 'modul/mod_lapstok/autobarang.php',
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
                				var datab = JSON.parse(res2);
                				document.getElementById('id_barang').value              = datab.id_barang;
                				document.getElementById('kd_barang').value              = datab.kd_barang;
                				document.getElementById('nmbrg_dtrbmasuk').value        = datab.nm_barang;
                				document.getElementById('stok_barang').value            = datab.stok_barang;
                				document.getElementById('qty_dtrbmasuk').value          = datab.konversi;
                				document.getElementById('sat_dtrbmasuk').value          = datab.sat_barang;
                				document.getElementById('konversi').value               = datab.konversi;
                				document.getElementById('qtygrosir_dtrbmasuk').value    = "1";
                				document.getElementById('satgrosir_dtrbmasuk').value    = datab.sat_grosir;
                	
                				document.getElementById('hrgsat_dtrbmasuk').value       = datab.hrgsat_barang;
            				
                			});
                		}
                	});
                </script>
                
<script type="text/javascript">
	$(function() {
		$(".datepicker").datepicker({
			format: 'yyyy-mm-dd',
			autoclose: true,
			todayHighlight: true,
		});
	});
	
// 	$(document).ready(function() {
// 		get_form_order();

// 	});

	// pilih supplier
    $(document).on('click', '#pilihsupplier', function(){

	    var id_supplier = $(this).data('id_supplier');
	    var nm_supplier = $(this).data('nm_supplier');
		 
		document.getElementById('id_supplier').value= id_supplier ;
		document.getElementById('nm_supplier').value= nm_supplier ;
		
		//hilangkan modal
		$(".close").click();

	});
    
    $(document).ready(function() {
		tabel_detail();
        
        
	});
	
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

	$('#nmbrg_dtrbmasuk').keydown(function(e) {
		if (e.which == 13) { // e.which == 13 merupakan kode yang mendeteksi ketika anda   // menekan tombol enter di keyboard
			//letakan fungsi anda disini

			var nm_barang = $("#nmbrg_dtrbmasuk").val();
			$.ajax({
				url: 'modul/mod_lapstok/autonamabarang_enter.php',
				type: 'post',
				data: {
					'nm_barang': nm_barang
				},
			}).success(function(data) {
			    
                var json = data;
				// //replace array [] menjadi '' 
				var res1 = json.replace("[", "");
				var res2 = res1.replace("]", "");
				// // //INI CONTOH ARRAY JASON const json = '{"result":true, "count":42}';
				var datab = JSON.parse(res2);
				
				document.getElementById('id_barang').value              = datab.id_barang;
				document.getElementById('kd_barang').value              = datab.kd_barang;
				document.getElementById('nmbrg_dtrbmasuk').value        = datab.nm_barang;
				document.getElementById('stok_barang').value            = datab.stok_barang;
				document.getElementById('qty_dtrbmasuk').value          = datab.konversi;
				document.getElementById('sat_dtrbmasuk').value          = datab.sat_barang;
				document.getElementById('konversi').value               = datab.konversi;
				document.getElementById('qtygrosir_dtrbmasuk').value    = "1";
				document.getElementById('satgrosir_dtrbmasuk').value    = datab.sat_grosir;
	
				document.getElementById('hrgsat_dtrbmasuk').value       = datab.hrgsat_barang;
				

			});

		}
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

		var ttl_trkasir1 = ttl_trkasir.replace(".", "");
		var dp_bayar1 = dp_bayar.replace(".", "");
		var sisa_bayar1 = sisa_bayar.replace(".", "");

		var ttl_trkasir1x = ttl_trkasir1.replace(".", "");
		var dp_bayar1x = dp_bayar1.replace(".", "");
		var sisa_bayar1x = sisa_bayar1.replace(".", "");

        var nilai_konversi = document.getElementById('nilai_konversi').value;
        var petugas = document.getElementById('petugas').value;
        
		if (nm_supplier == "") {
			alert('Belum ada data supplier');
		} else if (nilai_konversi == "0") {
			alert('Konversi tidak boleh kosong');
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
					'petugas': petugas
				},
				success: function(data) {
					alert('Proses berhasil !');
					window.location = 'media_admin.php?module=orders';
				}
			});
		}
	}
	
	//fungsi tabel detail
	function tabel_detail() {

		var kd_trbmasuk = document.getElementById('kd_trbmasuk').value;

		$.ajax({
			url: 'modul/mod_lapstok/tbl_detail.php',
			type: 'post',
			data: {
				'kd_trbmasuk': kd_trbmasuk
			},
			success: function(data) {
				$('#tabeldata').html(data);
			}

		});
	}
	
	$(document).on('change', '#qty_dtrbmasuk', function(){
        var qty_dtrbmasuk       = $('#qty_dtrbmasuk').val();
        // var qtygrosir_dtrbmasuk = $('#qtygrosir_dtrbmasuk').val();
        var konversi            = $('#konversi').val();
        
        document.getElementById("qtygrosir_dtrbmasuk").value = (qty_dtrbmasuk/konversi);
	});
	
	$(document).on('change', '#qtygrosir_dtrbmasuk', function(){
        // var qty_dtrbmasuk       = $('#qty_dtrbmasuk').val();
        var qtygrosir_dtrbmasuk = $('#qtygrosir_dtrbmasuk').val();
        var konversi            = $('#konversi').val();
        
        document.getElementById("qty_dtrbmasuk").value = (qtygrosir_dtrbmasuk * konversi);
	});
	
	$(document).on('change', '#konversi', function(){
        // var qty_dtrbmasuk       = $('#qty_dtrbmasuk').val();
        var qtygrosir_dtrbmasuk = $('#qtygrosir_dtrbmasuk').val();
        var konversi            = $('#konversi').val();
        
        document.getElementById("qty_dtrbmasuk").value = (qtygrosir_dtrbmasuk * konversi);
	});
</script>