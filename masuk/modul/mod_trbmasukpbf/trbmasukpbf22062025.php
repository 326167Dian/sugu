<?php
session_start();
if (empty($_SESSION['username']) and empty($_SESSION['passuser'])) {
    echo "<link href=../css/style.css rel=stylesheet type=text/css>";
    echo "<div class='error msg'>Untuk mengakses Modul anda harus login.</div>";
} else {

    $aksi = "modul/mod_trbmasukpbf/aksi_trbmasuk.php";
    $aksi_trbmasuk = "masuk/modul/mod_trbmasukpbf/aksi_trbmasuk.php";
    switch ($_GET['act']) {
            // Tampil barang
        default:


            $tampil_trbmasuk = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM trbmasuk 
	  WHERE id_resto = 'pusat' and jenis = 'pbf'
	  ORDER BY trbmasuk.id_trbmasuk DESC");

       
?>


            <div class="box box-primary box-solid table-responsive">
                <div class="box-header with-border">
                    <h3 class="box-title">TRANSAKSI BARANG MASUK DARI PBF</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div><!-- /.box-tools -->
                </div>
                <div class="box-body table-responsive">
                    <a class='btn  btn-success btn-flat' href='?module=trbmasukpbf&act=tambah'>TAMBAH</a>
                    <a class='btn  btn-info btn-flat' href='?module=trbmasukpbf&act=cari'>CARI NOMOR BATCH</a>
                    <a class='btn  btn-danger btn-flat' href='?module=trbmasukpbf&act=jatuhtempo'>Filter Jatuh Tempo</a>
                    <a class='btn  btn-primary btn-flat' href='?module=trbmasukpbf&act=pembelian'>Filter Pembelian</a>
                    <a class='btn  btn-secondary btn-success' href='?module=trbmasukpbf&act=distributor'>Filter Distributor</a>
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
                                <th>Jatuh Tempo</th>
                                <th>Total Tagihan</th>
                                <th>Status Pembayaran</th>
                                <th width="70">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // $no = 1;
                            // while ($r = mysqli_fetch_array($tampil_trbmasuk)) {
                            //     $ttl_trbmasuknya = format_rupiah($r['ttl_trbmasuk']);
                            //     $dp_bayar = format_rupiah($r['dp_bayar']);
                            //     $sisa_bayar = format_rupiah($r['sisa_bayar']);

                            //     echo "<tr class='warnabaris' >";

                            //     if ($r['carabayar'] == "LUNAS") {
                            //         echo "
                            // 					<td>$no</td>           
                            // 					<td>$r[kd_trbmasuk]</td>
                            // 				";
                            //     } else {

                            //         echo "
                            // 					<td style='background-color:#ffbf00;'>$no</td>           
                            // 					<td style='background-color:#ffbf00;'>$r[kd_trbmasuk]</td>
                            // 				";
                            //     }
                            //     echo "               
                            // 				 <td>$r[petugas]</td>											
                            // 				 <td>$r[tgl_trbmasuk]</td>											
                            // 				 <td>$r[nm_supplier]</td>
                            // 				 <td>$r[ket_trbmasuk]</td>											
                            // 				 <td>$r[jatuhtempo]</td>											
                            // 				<td align=right>$sisa_bayar</td>											 
                            // 				<td align=center>$r[carabayar]</td>											 
                            // 				 <td align='center'><a href='?module=trbmasuk&act=ubah&id=$r[id_trbmasuk]' title='EDIT' class='btn btn-warning btn-xs'>TAMPIL</a> 
                            // 				 <!-- tidak boleh di hapus
                            // 				 <a href=javascript:confirmdelete('$aksi?module=trbmasuk&act=hapus&id=$r[id_trbmasuk]') title='HAPUS' class='btn btn-danger btn-xs'>HAPUS</a>
                            // 				 -->
                            // 				</td>
                            // 			</tr>";
                            //     $no++;
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
                            "url": "modul/mod_trbmasukpbf/trbmasuk-serverside.php?action=table_data",
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
                                "data": "jatuh_tempo",
                                "className": "text-center"
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
            $cekkd = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM kdbm WHERE id_admin='$_SESSION[idadmin]' AND id_resto='pusat' AND stt_kdbm='ON'");
            $ketemucekkd = mysqli_num_rows($cekkd);
            $hcekkd = mysqli_fetch_array($cekkd);
            $petugas = $_SESSION['namalengkap'];

            if ($ketemucekkd > 0) {
                $kdtransaksi = $hcekkd['kd_trbmasuk'];
            } else {
                $kdunik = date('dmyhis');
                $kdtransaksi = "BMP-" . $kdunik;
                mysqli_query($GLOBALS["___mysqli_ston"], "INSERT INTO kdbm(kd_trbmasuk,id_resto,id_admin) VALUES('$kdtransaksi','pusat','$_SESSION[idadmin]')");
            }

            $tglharini = date('Y-m-d');

            echo "
		  <div class='box box-primary box-solid table-responsive'>
				<div class='box-header with-border'>
					<h3 class='box-title'>TAMBAH TRANSAKSI BARANG MASUK DARI PBF</h3>
					<div class='box-tools pull-right'>
						<button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
                    </div><!-- /.box-tools -->
				</div>
				<div class='box-body table-responsive'>
				
						<form onsubmit='return false;' method=POST action='$aksi?module=trbmasukpbf&act=input_trbmasuk' enctype='multipart/form-data' class='form-horizontal'>
						
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
										</div>
									
									<label class='col-sm-4 control-label'>Jatuh Tempo</label>
										<div class='col-sm-6'>
											<div class='input-group date'>
												<div class='input-group-addon'>
													<span class='glyphicon glyphicon-th'></span>
												</div>
													<input type='text' class='datepicker' name='jatuhtempo' id='jatuhtempo' required='required'  autocomplete='off'>
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
											<div class='btn-group btn-group-justified' role='group' aria-label='...'>
                                                <div class='btn-group' role='group'>
											        <input type=text name='nmbrg_dtrbmasuk' id='nmbrg_dtrbmasuk' class='typeahead form-control' autocomplete='off'>
                                                    
                                                </div>
                                                <div class='btn-group' role='group'>
                                                    <button type='button' class='btn btn-primary' id='nmbrg_dtrbmasuk_enter'>Enter</button>
                                                </div>
                                            </div>
										</div>
										
									<label class='col-sm-4 control-label'>Qty Grosir</label>        		
										<div class='col-sm-7'>
											<input type='number' name='qty_dtrbmasuk' id='qty_dtrbmasuk' class='form-control' autocomplete='off'>
										</div>
									
									<label class='col-sm-4 control-label'>Satuan Grosir</label>        		
									 <div class='col-sm-7'>
										<select name='sat_dtrbmasuk' id='sat_dtrbmasuk' class='form-control' >";
                                        $tampil = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM satuan ORDER BY nm_satuan ASC");
                                        while ($rk = mysqli_fetch_array($tampil)) {
                                            echo "<option value=$rk[nm_satuan]>$rk[nm_satuan]</option>";
                                        }
                                        echo "
                                        </select>
									 </div>
																		
									
									<label class='col-sm-4 control-label'>Konversi</label>        		
										<div class='col-sm-7'>
											<input type=number name='konversi' id='konversi' class='form-control' autocomplete='off' required>
											
										</div>
											
									<label class='col-sm-4 control-label'>HNA Grosir</label>        		
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
        case "ubah" :
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
													<input type='text' class='datepicker' name='jatuhtempo' id='jatuhtempo' required='required' value='$re[jatuhtempo]' autocomplete='off'>
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
											<div class='btn-group btn-group-justified' role='group' aria-label='...'>
                                                <div class='btn-group' role='group'>
											        <input type=text name='nmbrg_dtrbmasuk' id='nmbrg_dtrbmasuk' class='typeahead form-control' autocomplete='off'>
                                                    
                                                </div>
                                                <div class='btn-group' role='group'>
                                                    <button type='button' class='btn btn-primary' id='nmbrg_dtrbmasuk_enter'>Enter</button>
                                                </div>
                                            </div>
										</div>
										
									<label class='col-sm-4 control-label'>Qty Grosir</label>        		
										<div class='col-sm-7'>
											<input type='number' name='qty_dtrbmasuk' id='qty_dtrbmasuk' class='form-control' autocomplete='off'>
										</div>
									
									<label class='col-sm-4 control-label'>Satuan Grosir</label>        		
									 <div class='col-sm-7'>
										<select name='sat_dtrbmasuk' id='sat_dtrbmasuk' class='form-control' >";
                                        $tampil = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM satuan ORDER BY nm_satuan ASC");
                                        while ($rk = mysqli_fetch_array($tampil)) {
                                            echo "<option value=$rk[nm_satuan]>$rk[nm_satuan]</option>";
                                        }
                                        echo "
                                        </select>
									 </div>


									<label class='col-sm-4 control-label'>Konversi</label>
										<div class='col-sm-7'>
											<input type=number name='konversi' id='konversi' class='form-control' autocomplete='off' required>

										</div>

									<label class='col-sm-4 control-label'>HNA Grosir</label>
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
						</form>
							  
				</div> 
				
				<div id='tabeldata'>
				
			</div>";

            break;
        case "tampil":
            //cek apakah ada kode transaksi ON berdasarkan user

            $ubah = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM trbmasuk 
	WHERE trbmasuk.id_trbmasuk='$_GET[id]'");
            $re = mysqli_fetch_array($ubah);
            $totalharga = $re['ttl_trbmasuk'];
            $totalharga1 = format_rupiah($totalharga);
            $sisabayar = $re['sisa_bayar'];
            $diskon = $totalharga - $sisabayar;

            $diskon1 = format_rupiah($diskon);
            $sisabayar1 = format_rupiah($sisabayar);


            echo "
		  <div class='box box-primary box-solid table-responsive'>
				<div class='box-header with-border'>
					<h3 class='box-title'>REVIEW TRANSAKSI BARANG MASUK DARI PBF</h3>
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
											<!--
											  <button type='button' class='btn btn-primary right-block' onclick='simpan_transaksi();'>SIMPAN TRANSAKSI</button>
												&nbsp&nbsp&nbsp
											-->
												
											</div>
								  
										</div>
										
									<label class='col-sm-4 control-label'>Jatuh Tempo</label>
										<div class='col-sm-6'>
												<div class='input-group date'>
                                                    <div class='input-group-addon'>
                                                        <span class='glyphicon glyphicon-th'></span>
                                                    </div>
													<input type='text' class='datepicker' name='tgl_trbmasuk' id='tgl_trbmasuk' required='required' value='$re[jatuhtempo]' autocomplete='off'>
											    </div>								
											<input class='btn btn-primary' type='button' value=TUTUP onclick=self.history.back()>
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
														<button type=button data-toggle='modal' data-target='#ModalItem' href='#'><span class='glyphicon glyphicon-search'></span></button>
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
                        <th>HNA</th>
                        <th>Disc</th>
                        <th>Sub Total</th>
                    </thead>
                    <tbody>
                        <?php
                        $show = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM trbmasuk_detail 
                                        WHERE kd_trbmasuk='$re[kd_trbmasuk]'");
                        $no = 1;
                        while ($q = mysqli_fetch_array($show)) {
                            $hnasat_dtrbmasuk = format_rupiah($q['hnasat_dtrbmasuk']);
                            $hrgttl_dtrbmasuk = format_rupiah($q['hrgttl_dtrbmasuk']);

                            echo " <tr style='font-size: 14px;'>
                                            <td>$no</td>
                                            <td>$q[kd_barang]</td>
                                            <td>$q[nmbrg_dtrbmasuk]</td>
                                            <td>$q[qty_dtrbmasuk]</td>
                                            <td>$q[sat_dtrbmasuk]</td>
                                            <td>$q[no_batch]</td>
                                            <td>".tgl_indo($q['exp_date'])."</td>
                                            <td align='right'>$hnasat_dtrbmasuk</td>
                                            <td align='right'>$q[diskon]</td>
                                            <td align='right'>$hrgttl_dtrbmasuk</td>
                                         </tr>";

                            $no++;
                        }
                        ?>
                    </tbody>

                    <tr>
                        <td align='center' colspan='5'><strong>TOTAL Rp. <?php echo format_rupiah($totalharga1); ?> </strong> </td>
                        <td colspan='2'><strong> DISKON Rp. <?php echo $diskon1;  ?>,- </strong></td>
                    </tr>
                    <tr>
                        <td colspan='5'>
                            <h3>
                                <center>Total Tagihan + PPN</center>
                            </h3>
                        </td>
                        <td colspan='2'>
                            <h3><strong> Rp. <?php echo $sisabayar1 ?> ,- </strong></h3>
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
    			    <form method="post" action="?module=trbmasukpbf&act=carinobatch">
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
            
            $caridetail = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM trbmasuk_detail a 
            JOIN trbmasuk b ON a.kd_trbmasuk = b.kd_trbmasuk WHERE a.no_batch='$nobatch'");
			
			$row = mysqli_fetch_array($caridetail);
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
        				        $caridetail1 = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM trbmasuk_detail a 
        				        JOIN trbmasuk b ON a.kd_trbmasuk = b.kd_trbmasuk WHERE a.no_batch='$nobatch'");
    			
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
         case "jatuhtempo" ;

            ?>
                <div class="box box-primary box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Cek Pembelian Jatuh Tempo</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div><!-- /.box-tools  -->

                </div>
                <div class="box-body">

                    <form method="POST" action="?module=trbmasukpbf&act=tampiljatuhtempo" target="_blank" enctype="multipart/form-data" class="form-horizontal">

                        </br></br>


                        <div class="form-group">
                            <label class="col-sm-2 control-label">Tanggal Awal</label>
                            <div class="col-sm-4">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <span class="glyphicon glyphicon-th"></span>
                                    </div>
                                    <input type="text" required="required" class="datepicker" id="tgl_awal" name="tgl_awal" autocomplete="off">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Tanggal Akhir</label>
                            <div class="col-sm-4">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <span class="glyphicon glyphicon-th"></span>
                                    </div>
                                    <input type="text" required="required" class="datepicker" id="tgl_akhir" name="tgl_akhir" autocomplete="off">
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-sm-2 control-label"></label>
                            <div class="buttons col-sm-4">
                                <input class="btn btn-primary" type="submit" name="btn" value="TAMPIL">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp

                        <a  class ='btn  btn-danger' href='?module=trbmasukpbf'>KEMBALI</a>
                            </div>
                        </div>

                    </form>
                </div>

            </div>

                <script type="text/javascript">
                    $(function(){
                        $(".datepicker").datepicker({
                            format: 'yyyy-mm-dd',
                            autoclose: true,
                            todayHighlight: true,
                        });
                    });
                </script>


                <?php
            break;
        case "tampiljatuhtempo" :
          $tgl_awal = $_POST['tgl_awal'];
          $tgl_akhir = $_POST['tgl_akhir'];

          $jatuh = $db->query("select * from trbmasuk where carabayar !='LUNAS' and jatuhtempo between '$tgl_awal' and '$tgl_akhir' 
                                order by jatuhtempo asc");

          ?>
          <div class="box box-danger box-solid">
				<div class="box-header with-border">
					<h3 class="box-title">TAGIHAN JATUH TEMPO</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div><!-- /.box-tools -->
				</div>
				<div class="box-body">

					<br><br>


					<table id="example1" class="table table-bordered table-striped" >
						<thead>
							<tr>
								<th>No</th>
								<th>tanggal Jatuh Tempo</th>
								<th>Kode Transaksi</th>
								<th>Distributor</th>
                                <th>Nilai Faktur</th>
							</tr>
						</thead>
						<tbody>
						<?php
								$no=1;
								while ($te = $jatuh->fetch_array()){

                                    $ttl= format_rupiah($te['ttl_trbmasuk']);
									echo "<tr class='warnabaris' >
											<td>$no</td>           
											 <td>$te[jatuhtempo]</td>
											 <td>$te[kd_trbmasuk]</td>
											 <td>$te[nm_supplier]</td>
											 <td style='text-align:right;'>Rp.  $ttl</td>								
											 
										</tr>";
									$total[]=$te['ttl_trbmasuk'];
								$no++;
								}
							echo"
						</tbody>
						<tfoot>
						"; $tus = format_rupiah(array_sum($total)); 
						echo"
						            <tr style='background: #00fafa; font-size: 4vh;'>
                                        <td colspan='3'>Total</td>
                                        <td style='text-align:right;' colspan='2'>Rp.  $tus</td>
                                    </tr>
						</tfoot>
						</table>";
						
					?>
				</div>

			</div>
<?php
break;

case "pembelian" :
            ?>
            <div class="box box-primary box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Cek Total Pembelian Berdasarkan Tanggal</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div><!-- /.box-tools  -->

                </div>
                <div class="box-body">

                    <form method="POST" action="?module=trbmasukpbf&act=totalbeli" target="_blank" enctype="multipart/form-data" class="form-horizontal">

                        </br></br>


                        <div class="form-group">
                            <label class="col-sm-2 control-label">Tanggal Awal</label>
                            <div class="col-sm-4">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <span class="glyphicon glyphicon-th"></span>
                                    </div>
                                    <input type="text" required="required" class="datepicker" id="tgl_awal" name="tgl_awal" autocomplete="off">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Tanggal Akhir</label>
                            <div class="col-sm-4">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <span class="glyphicon glyphicon-th"></span>
                                    </div>
                                    <input type="text" required="required" class="datepicker" id="tgl_akhir" name="tgl_akhir" autocomplete="off">
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-sm-2 control-label"></label>
                            <div class="buttons col-sm-4">
                                <input class="btn btn-primary" type="submit" name="btn" value="TAMPIL">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp

                                <a  class ='btn  btn-danger' href='?module=trbmasukpbf'>KEMBALI</a>
                            </div>
                        </div>

                    </form>
                </div>

            </div>

            <script type="text/javascript">
                $(function(){
                    $(".datepicker").datepicker({
                        format: 'yyyy-mm-dd',
                        autoclose: true,
                        todayHighlight: true,
                    });
                });
            </script>


            <?php
        break;
        case "totalbeli" :
            $tgl_awal = $_POST['tgl_awal'];
            $tgl_akhir = $_POST['tgl_akhir'];

            $totalbeli = $db->query("select * from trbmasuk where tgl_trbmasuk between '$tgl_awal' and '$tgl_akhir' ");

            ?>
            <div class="box box-primary box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Rekap Pembelian berdasarkan Tanggal</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div><!-- /.box-tools -->
                </div>
                <div class="box-body">

                    <br><br>


                    <table id="example1" class="table table-bordered table-striped" >
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>tanggal Pembelian</th>
                            <th>Kode Transaksi</th>
                            <th>Distributor</th>
                            <th>Status Pembayaran</th>
                            <th>Nilai Faktur</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $no=1;
                        while ($te = $totalbeli->fetch_array()){

                            $ttl= format_rupiah($te['ttl_trbmasuk']);
                            echo "<tr class='warnabaris' >
											<td>$no</td>           
											 <td>$te[tgl_trbmasuk]</td>
											 <td>$te[kd_trbmasuk]</td>
											 <td>$te[nm_supplier]</td>
											 <td>$te[carabayar]</td>
											 <td style='text-align:right;'>Rp.  $ttl</td>								
											 
										</tr>";

                            $total[]=$te['ttl_trbmasuk'];
                            $no++;
                        }
                        echo"
						</tbody>
						<tfoot>
						"; $tus = format_rupiah(array_sum($total));
                        $totallunas = $db->query("select sum(ttl_trbmasuk) as lunas from trbmasuk where tgl_trbmasuk between '$tgl_awal' and '$tgl_akhir' and carabayar='LUNAS' ");
                        $lns = $totallunas ->fetch_array();
                        $lunas = format_rupiah($lns['lunas']);
                        $belumlunas = $db->query("select sum(ttl_trbmasuk) as belum from trbmasuk where tgl_trbmasuk between '$tgl_awal' and '$tgl_akhir' and carabayar='KREDIT'");
                        $blm = $belumlunas ->fetch_array();
                        $belum = format_rupiah($blm['belum']);
                        echo"
						            <tr style='background: #00fafa; font-size: 4vh;'>
                                        <td colspan='4'>Lunas = Rp. $lunas , Belum Lunas = Rp. $belum  => Total</td>
                                        <td style='text-align:right;' colspan='2'>Rp.  $tus</td>
                                    </tr>
						</tfoot>
						</table>";

                        ?>
                </div>

            </div>
            <?php
        break;
        case "distributor" :
            ?>
            <div class="box box-primary box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Cek Total Pembelian Berdasarkan Distributor</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div><!-- /.box-tools  -->

                </div>
                <div class="box-body">

                    <form method="POST" action="?module=trbmasukpbf&act=tampil_distributor" target="_blank" enctype="multipart/form-data" class="form-horizontal">

                        </br></br>


                        <div class="form-group">
                            <label class="col-sm-2 control-label">Tanggal Awal</label>
                            <div class="col-sm-4">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <span class="glyphicon glyphicon-th"></span>
                                    </div>
                                    <input type="text" required="required" class="datepicker" id="tgl_awal" name="tgl_awal" autocomplete="off">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Tanggal Akhir</label>
                            <div class="col-sm-4">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <span class="glyphicon glyphicon-th"></span>
                                    </div>
                                    <input type="text" required="required" class="datepicker" id="tgl_akhir" name="tgl_akhir" autocomplete="off">
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-sm-2 control-label"></label>
                            <div class="buttons col-sm-4">
                                <input class="btn btn-primary" type="submit" name="btn" value="TAMPIL">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp

                                <a  class ='btn  btn-danger' href='?module=trbmasukpbf'>KEMBALI</a>
                            </div>
                        </div>

                    </form>
                </div>

            </div>

            <script type="text/javascript">
                $(function(){
                    $(".datepicker").datepicker({
                        format: 'yyyy-mm-dd',
                        autoclose: true,
                        todayHighlight: true,
                    });
                });
            </script>


            <?php
            break;
            case "tampil_distributor":
            $tgl_awal = $_POST['tgl_awal'];
            $tgl_akhir = $_POST['tgl_akhir'];

            $list = $db->query("select * from supplier ");

            ?>
            <div class="box box-primary box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Rekap Pembelian berdasarkan Distributor tanggal <?= $tgl_awal ?> hingga <?= $tgl_akhir ?></h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div><!-- /.box-tools -->
                </div>
                <div class="box-body">

                    <br><br>


                    <table id="example2" class="table table-bordered table-striped" >
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Distributor</th>                            
                            <th>Lunas</th>
                            <th>Belum Lunas</th>
                            <th>Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $no=1;
                        while ($te = $list->fetch_array()){
                            $totbel = $db->query("select sum(ttl_trbmasuk) as tepo from trbmasuk where id_supplier='$te[id_supplier]' and tgl_trbmasuk between '$tgl_awal' and '$tgl_akhir' ");
                            $tb=$totbel->fetch_array();
                            $totbel2 = $db->query("select sum(ttl_trbmasuk) as tepo2 from trbmasuk where carabayar='LUNAS' and id_supplier='$te[id_supplier]' and tgl_trbmasuk between '$tgl_awal' and '$tgl_akhir'");
                            $tb2= $totbel2->fetch_array();
                            $totbel3 = $db->query("select sum(ttl_trbmasuk) as tepo3 from trbmasuk where carabayar='KREDIT' and id_supplier='$te[id_supplier]' and tgl_trbmasuk between '$tgl_awal' and '$tgl_akhir'");
                            $tb3= $totbel3->fetch_array();
                            $ttl= format_rupiah($tb['tepo']);
                            $ttl2= format_rupiah($tb2['tepo2']);
                            $ttl3= format_rupiah($tb3['tepo3']);
                            echo "<tr class='warnabaris' >
											<td>$no</td>           
											 <td>$te[nm_supplier]</td>											 
											 <td style='text-align:right;'>Rp. $ttl2</td>
											 <td style='text-align:right;color:red;'>Rp. $ttl3</td>
											 <td style='text-align:right;color:blue;'>Rp.  $ttl</td>
										</tr>";

                            $total[]=$tb['tepo'];
                            $no++;
                        }
                        echo"
						</tbody>
						<tfoot>
						"; $tus = format_rupiah(array_sum($total));
                        $totallunas = $db->query("select sum(ttl_trbmasuk) as lunas from trbmasuk where tgl_trbmasuk between '$tgl_awal' and '$tgl_akhir' and carabayar='LUNAS' ");
                        $lns = $totallunas ->fetch_array();
                        $lunas = format_rupiah($lns['lunas']);
                        $belumlunas = $db->query("select sum(ttl_trbmasuk) as belum from trbmasuk where tgl_trbmasuk between '$tgl_awal' and '$tgl_akhir' and carabayar='KREDIT'");
                        $blm = $belumlunas ->fetch_array();
                        $belum = format_rupiah($blm['belum']);
                        echo"
						            <tr style='background: #00fafa; font-size: 4vh;'>
                                        <td colspan='3'>Lunas = Rp. $lunas , Belum Lunas = Rp. $belum  => Total</td>
                                        <td style='text-align:right;' colspan='2'>Rp.  $tus</td>
                                    </tr>
						</tfoot>
						</table>";

                        ?>
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
                            <th style="vertical-align: middle; background-color: #008000; text-align: center; ">Sat Grosir</th>
                            <th style="vertical-align: middle; background-color: #008000; text-align: center; ">Konversi</th>
                            <th style="vertical-align: middle; background-color: #008000; text-align: right; ">HNA</th>
                            <th style="vertical-align: middle; background-color: #008000; text-align: center; ">Pilih</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // $no = 1;
                        // $tampil_dproyek = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM barang ORDER BY id_barang ASC");
                        // while ($rd = mysqli_fetch_array($tampil_dproyek)) {

                        //     $stok1 = format_rupiah($rd['stok_barang']);
                        //     $harga1 = format_rupiah($rd['hna']);

                        //     echo "<tr style='font-size: 13px;'> 
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
                        // 	 data-hna='$rd[hna]'>
                        // 	 <i class='fa fa-check'></i>
                        // 	 </button>

                        // 					</td>
                        // 				</tr>";
                        //     $no++;
                        // }
                        // echo "</tbody></table>";
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
                        $tampil_dproyek = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM supplier ORDER BY nm_supplier ASC");
                        while ($rd = mysqli_fetch_array($tampil_dproyek)) {

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
    // $(function() {
    //     $(".datepicker").datepicker({
    //         format: 'yyyy-mm-dd',
    //         autoclose: true,
    //         todayHighlight: true,
    //     });
    // });
</script>

<script>
    $(document).ready(function() {
        tabel_detail();
        $(".datepicker").datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
        });
    });

    // Autocomplete nama obat
	$('#nmbrg_dtrbmasuk').typeahead({
		source: function(query, process) {
			return $.post('modul/mod_trbmasukpbf/autonamabarang.php', {
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
					url: 'modul/mod_trbmasukpbf/autonamabarang_enter.php',
					type: 'post',
					data: {
						'nm_barang': nm_barang
					},
				}).success(function(response) {
					let data = $.parseJSON(response);
					// let data = JSON.parse(response)
					let qty_default = "1";
					let diskon_default = "0";

					for (let i = 0; i < data.length; i++) {
						data = data[i];
						document.getElementById('id_barang').value = data.id_barang;
						document.getElementById('kd_barang').value = data.kd_barang;
						document.getElementById('nmbrg_dtrbmasuk').value = data.nm_barang;
						document.getElementById('stok_barang').value = data.stok_barang;
						document.getElementById('qty_dtrbmasuk').value = qty_default;
						document.getElementById('sat_dtrbmasuk').value = data.sat_grosir;
						document.getElementById('konversi').value = data.konversi;
						document.getElementById('hnasat_dtrbmasuk').value = data.hna;
						document.getElementById('hrgjual_dtrbmasuk').value = data.hrgjual_barang;
						document.getElementById('diskon').value = diskon_default;
					}

				});
			}
		})
	});

    $('#nmbrg_dtrbmasuk_enter').on('click', function(){
	    let nm_barang = $('#nmbrg_dtrbmasuk').val();
		$.ajax({
			url: 'modul/mod_trbmasukpbf/autonamabarang_enter.php',
			type: 'post',
			data: {
				'nm_barang': nm_barang
			},
		}).success(function(response) {
			let data = $.parseJSON(response);
			// let data = JSON.parse(response)
			let qty_default = "1";
			let diskon_default = "0";

			for (let i = 0; i < data.length; i++) {
				data = data[i];
				document.getElementById('id_barang').value = data.id_barang;
				document.getElementById('kd_barang').value = data.kd_barang;
				document.getElementById('nmbrg_dtrbmasuk').value = data.nm_barang;
				document.getElementById('stok_barang').value = data.stok_barang;
				document.getElementById('qty_dtrbmasuk').value = qty_default;
				document.getElementById('sat_dtrbmasuk').value = data.sat_grosir;
				document.getElementById('konversi').value = data.konversi;
				document.getElementById('hnasat_dtrbmasuk').value = data.hna;
				document.getElementById('hrgjual_dtrbmasuk').value = data.hrgjual_barang;
				document.getElementById('diskon').value = diskon_default;
			}

		});  
	})


    $(document).on('click', '#kode', function() {
        $("#example").DataTable().destroy();

        $("#example").DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": "modul/mod_trbmasukpbf/barang-serverside.php?action=table_data",
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
                    "data": "sat_grosir",
                    "className": 'text-center',
                },
                {
                    "data": "konversi",
                    "className": 'text-center',
                },
                {
                    "data": "hna",
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
        var sat_grosir = $(this).data('sat_grosir');
        var konversi = $(this).data('konversi');
        var hna = $(this).data('hna');
        var hrgjual_dtrbmasuk = $(this).data('hrgjual_barang');
        var diskon = $(this).data('diskon');
        var qty_default = "1"
        var diskon_default = "0";

        document.getElementById('id_barang').value = id_barang;
        document.getElementById('kd_barang').value = kd_barang;
        document.getElementById('nmbrg_dtrbmasuk').value = nm_barang;
        document.getElementById('stok_barang').value = stok_barang;
        document.getElementById('qty_dtrbmasuk').value = qty_default;
        document.getElementById('sat_dtrbmasuk').value = sat_grosir;
        document.getElementById('konversi').value = konversi;
        document.getElementById('hnasat_dtrbmasuk').value = hna;
        document.getElementById('hrgjual_dtrbmasuk').value = hrgjual_dtrbmasuk;
        document.getElementById('diskon').value = diskon_default;

        //hilangkan modal
        $(".close").click();

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
        var konversi = document.getElementById('konversi').value;
        var hnasat_dtrbmasuk = document.getElementById('hnasat_dtrbmasuk').value;
        var hrgjual_dtrbmasuk = document.getElementById('hrgjual_dtrbmasuk').value;
        var diskon = document.getElementById('diskon').value;
        var no_batch = document.getElementById('no_batch').value;
        var exp_date = document.getElementById('exp_date').value;
        
        if (nmbrg_dtrbmasuk == "") {
            alert('Belum ada Item terpilih');
        } else if (qty_dtrbmasuk == "") {
            alert('Qty tidak boleh kosong');
        } else if (konversi == 0 ) {
            alert('konversi tidak boleh kosong');
        } else if (hnasat_dtrbmasuk == "") {
            alert('Harga tidak boleh kosong');
        } 
        
        else {

               $.ajax({
                type: 'post',
                url: "modul/mod_trbmasukpbf/simpandetail_tbm.php",
                data: {
                    'kd_trbmasuk': kd_trbmasuk,
                    'id_barang': id_barang,
                    'kd_barang': kd_barang,
                    'nmbrg_dtrbmasuk': nmbrg_dtrbmasuk,
                    'qty_dtrbmasuk': qty_dtrbmasuk,
                    'sat_dtrbmasuk': sat_dtrbmasuk,
                    'konversi': konversi,
                    'hnasat_dtrbmasuk': hnasat_dtrbmasuk,
                    'hrgjual_dtrbmasuk': hrgjual_dtrbmasuk,
                    'diskon': diskon,
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
                    document.getElementById("konversi").value = "";
                    document.getElementById("hnasat_dtrbmasuk").value = "";
                    document.getElementById("hrgjual_dtrbmasuk").value = "";
                    document.getElementById("diskon").value = "";
                    document.getElementById("no_batch").value = "";
                    document.getElementById("exp_date").value = "";
                    tabel_detail();
                }
            });

        }



    }


    $(document).on('click', '#hapusdetail', function() {

        var id_dtrbmasuk = $(this).data('id_dtrbmasuk');

        $.ajax({
            type: 'post',
            url: "modul/mod_trbmasukpbf/hapusdetail_tbm.php",
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
            url: 'modul/mod_trbmasukpbf/tbl_detail.php',
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
                url: 'modul/mod_trbmasukpbf/autobarang.php',
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
                document.getElementById('sat_dtrbmasuk').value = datab.sat_grosir;
                document.getElementById('hnasat_dtrbmasuk').value = datab.hna;
                document.getElementById('diskon').value = datab.diskon;
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
        var jatuhtempo = document.getElementById('jatuhtempo').value;
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
                url: "modul/mod_trbmasukpbf/aksi_trbmasuk.php",

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
                    'jatuhtempo': jatuhtempo,
                    'ttl_trkasir': ttl_trkasir1x,
                    'dp_bayar': dp_bayar1x,
                    'sisa_bayar': sisa_bayar1x,
                    'carabayar': carabayar
                },
                success: function(data) {
                    alert('Proses berhasil !');
                    window.location = 'media_admin.php?module=trbmasukpbf';
                }
            });
        }
    }
</script>