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


            // $tampil_trbmasuk = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM trbmasuk 
            //             	  WHERE id_resto = 'pusat' and jenis = 'pbf'
            //             	  ORDER BY trbmasuk.id_trbmasuk DESC");


?>


            <div class="box box-primary box-solid table-responsive">
                <div class="box-header with-border">
                    <h3 class="box-title">TRANSAKSI BARANG MASUK DARI PBF</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div><!-- /.box-tools -->
                </div>
                <div class="box-body table-responsive">
                    <form action="modul/mod_trbmasukpbf/ubah_status_lunas.php" method="post">
                        <!--<a class='btn  btn-success btn-flat' href='?module=trbmasukpbf&act=tambah'>TAMBAH</a>-->
                        <a class='btn  btn-secondary btn-warning' href='?module=trbmasukpbf&act=orders'>Cek Pesanan</a>
                        <a class='btn  btn-secondary btn-success' href='?module=trbmasukpbf&act=evaluasi'>Evaluasi Barang Masuk</a>
                        <a class='btn  btn-info btn-flat' href='?module=trbmasukpbf&act=cari'>CARI NOMOR BATCH</a>
                        <a class='btn  btn-danger btn-flat' href='?module=trbmasukpbf&act=jatuhtempo'>Filter Jatuh Tempo</a>
                        <a class='btn  btn-primary btn-flat' href='?module=trbmasukpbf&act=pembelian'>Filter Pembelian</a>
                        <a class='btn  btn-secondary btn-success' href='?module=trbmasukpbf&act=distributor'>Filter Distributor</a>
                        <hr>
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
                                    <th>Kode Pesan</th>
                                    <th>Petugas input</th>
                                    <th>Tanggal Masuk</th>
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
                        <div style="text-align:center;">
                            <?php if ($_SESSION['level'] == 'pemilik' or $_SESSION['level'] == 'petugas'): ?>

                                <button class='btn  btn-success btn-flat' type='submit' onclick="return confirm('Apakah Faktur yang dipilih sudah LUNAS?')" id="hapus">SUBMIT PELUNASAN</button>

                            <?php endif; ?>
                        </div>
                    </form>
                </div>
            </div>

            <script>
                    $(document).ready(function() {
                        function format(d) {
        // `d` is the original data object for the row
                        return (
                            '<dl>' +
                            '<dt>Petugas Lunas :</dt>' +
                            '<dd>' +
                            d.petugas_lunas +
                            '</dd>' +
                            '<dt>Tanggal Lunas:</dt>' +
                            '<dd>' +
                            d.tgl_lunas +
                            '</dd>' +
                            '</dl>'
                        );
                    }

                    let table = $("#tes").DataTable({
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
                        columns: [
                            {
                                "data": "no",
                                "className": "text-center"
                            },
                            {
                                "data": "kd_trbmasuk",
                                "className": "text-left dt-control"
                            },
                            {
                                "data": "kd_orders",
                                "className": "text-left dt-control"
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
                    
                    table.on('click', 'tbody td.dt-control', function (e) {
                        let tr = e.target.closest('tr');
                        let row = table.row(tr);
                     
                        if (row.child.isShown()) {
                            // This row is already open - close it
                            row.child.hide();
                        }
                        else {
                            // Open this row
                            row.child(format(row.data())).show();
                        }
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
                $cekkd2 = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM kdbm WHERE kd_trbmasuk='$kdtransaksi'");
                $ketemucekkd2 = mysqli_num_rows($cekkd2);
                if ($ketemucekkd2 > 0) {
                    $kdunik2 = date('dmyhis')+1;
                    $kdtransaksi = "BMP-" . $kdunik2;
                }
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
        case "ubah":
            $petugas = $_SESSION['namalengkap'];
            
            $ubah = $db->prepare("SELECT * FROM trbmasuk 
	                                WHERE trbmasuk.id_trbmasuk=?");
	        $ubah->execute([$_GET['id']]);
            $re = $ubah->fetch(PDO::FETCH_ASSOC);
            
            if ($re['jenis'] == "nonpbf") {
                echo "<script type='text/javascript'>window.location='?module=byrkredit&act=ubah&id=".$_GET['id']."'</script>";
            }
            
            // $disabled = ($re['sisa_bayar']==0)?'disabled':'';
            $disabled = ($re['sisa_bayar']==0)?'':'';
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
												<button type='button' class='btn btn-primary right-block' onclick='simpan_transaksi();' $disabled>SIMPAN TRANSAKSI</button>
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
                                $tampil = $db->prepare("SELECT * FROM satuan ORDER BY nm_satuan ASC");
                                $tampil->execute();
                                while ($rk = $tampil->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<option value='$rk[nm_satuan]'>$rk[nm_satuan]</option>";
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
													<button type='button' class='btn btn-success right-block' onclick='simpan_detail();' $disabled>SIMPAN DETAIL</button>
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

            $ubah = $db->prepare("SELECT * FROM trbmasuk 
	                                WHERE trbmasuk.id_trbmasuk=?");
	        $ubah->execute([$_GET['id']]);
            $re = $ubah->fetch(PDO::FETCH_ASSOC);
            $totalharga = $re['ttl_trbmasuk'];
            // $totalharga1 = format_rupiah($totalharga /1.11);
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
                        $show = $db->prepare("SELECT * FROM trbmasuk_detail 
                                                WHERE kd_trbmasuk=?");
                        $show->execute([$re['kd_trbmasuk']]);
                        $no = 1;
                        $totalharga=0;
                        $totalharga1=0;
                        $totalharga_ppn=0;
                        while ($q = $show->fetch(PDO::FETCH_ASSOC)) {
                            $hnasat_dtrbmasuk = format_rupiah($q['hnasat_dtrbmasuk']);
                            // $hrgttl_dtrbmasuk = format_rupiah($q['hrgttl_dtrbmasuk'] /1.11);
                            $hrgttl_dtrbmasuk = format_rupiah(($q['hnasat_dtrbmasuk'] * (1 - ($q['diskon'] / 100)))* $q['qty_dtrbmasuk']);
                            // $hrgttl_dtrbmasuk = $hrgttl_dtrbmasuk * $q['qty_grosir'];
                            $subtotalharga  = round(($q['hnasat_dtrbmasuk'] * (1 - ($q['diskon'] / 100))) * $q['qty_grosir']);
                            $totalharga1    = ($totalharga1 + $subtotalharga);
                            $totalharga     = round($totalharga + (($q['hnasat_dtrbmasuk'] * (1 - ($q['diskon'] / 100))) * $q['qty_grosir']));
                            
                            echo " <tr style='font-size: 14px;'>
                                            <td>$no</td>
                                            <td>$q[kd_barang]</td>
                                            <td>$q[nmbrg_dtrbmasuk]</td>
                                            <td>$q[qty_dtrbmasuk]</td>
                                            <td>$q[sat_dtrbmasuk]</td>
                                            <td>$q[no_batch]</td>
                                            <td>" . tgl_indo($q['exp_date']) . "</td>
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
                        <td colspan='2'><strong> DISKON Rp. <?php echo $diskon1; ?>,- </strong></td>
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

            $caridetail = $db->prepare("SELECT * FROM trbmasuk_detail a 
                                        JOIN trbmasuk b ON a.kd_trbmasuk = b.kd_trbmasuk 
                                        WHERE a.no_batch=?");
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
                        <label for="staticEmail" class="col-sm-10 col-form-label">: <?= $row['nmbrg_dtrbmasuk'] ?></label>

                        <label for="staticEmail" class="col-sm-2 col-form-label">Satuan</label>
                        <label for="staticEmail" class="col-sm-10 col-form-label">: <?= $row['sat_dtrbmasuk'] ?></label>

                        <label for="staticEmail" class="col-sm-2 col-form-label">No. Batch</label>
                        <label for="staticEmail" class="col-sm-10 col-form-label">: <?= $row['no_batch'] ?></label>

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
        				                                    JOIN trbmasuk b ON a.kd_trbmasuk = b.kd_trbmasuk 
        				                                    WHERE a.no_batch=?");
        				    $caridetail1->execute([$nobatch]);

                            $no = 1;
                            while ($dt = $caridetail1->fetch(PDO::FETCH_ASSOC)):
                            ?>
                                <tr>
                                    <td class="text-center"><?= $no++ ?></t>
                                    <td class="text-left"><?= $dt['nm_supplier'] ?></td>
                                    <td class="text-center"><?= format_rupiah($dt['hrgsat_dtrbmasuk']) ?></td>
                                    <td class="text-center"><?= tgl_indo($dt['tgl_trbmasuk']) ?></td>
                                    <td class="text-center"><?= format_rupiah($dt['qty_dtrbmasuk']) ?></td>
                                    <td class="text-center"><?= tgl_indo($dt['exp_date']) ?></td>
                                    <td class="text-center"><?= $dt['petugas'] ?></td>
                                </tr>

                            <?php endwhile; ?>
                        </tbody>
                    </table>

                </div>
            </div>

        <?php
            break;
        case "jatuhtempo";

        ?>
            <div class="box box-primary box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Cek Pembelian Jatuh Tempo</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div><!-- /.box-tools  -->

                </div>
                <div class="box-body">

                    <form method="POST" action="?module=trbmasukpbf&act=tampiljatuhtempo" target="_blank"
                        enctype="multipart/form-data" class="form-horizontal">

                        </br></br>


                        <div class="form-group">
                            <label class="col-sm-2 control-label">Tanggal Awal</label>
                            <div class="col-sm-4">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <span class="glyphicon glyphicon-th"></span>
                                    </div>
                                    <input type="text" required="required" class="datepicker" id="tgl_awal" name="tgl_awal"
                                        autocomplete="off">
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
                                    <input type="text" required="required" class="datepicker" id="tgl_akhir" name="tgl_akhir"
                                        autocomplete="off">
                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-sm-2 control-label"></label>
                            <div class="buttons col-sm-4">
                                <input class="btn btn-primary" type="submit" name="btn"
                                    value="TAMPIL">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp

                                <a class='btn  btn-danger' href='?module=trbmasukpbf'>KEMBALI</a>
                            </div>
                        </div>

                    </form>
                </div>

            </div>

            <script type="text/javascript">
                $(function() {
                    $(".datepicker").datepicker({
                        format: 'yyyy-mm-dd',
                        autoclose: true,
                        todayHighlight: true,
                    });
                });
            </script>


        <?php
            break;
        case "tampiljatuhtempo":
            $tgl_awal = $_POST['tgl_awal'];
            $tgl_akhir = $_POST['tgl_akhir'];

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


                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>tanggal Jatuh Tempo</th>
                                <th>Distributor</th>
                                <th>Nilai Tagihan</th>
                                <th>Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $jatuh = $db->prepare("SELECT jatuhtempo,nm_supplier,id_supplier, sum(ttl_trbmasuk) as hutang FROM trbmasuk 
                                    WHERE carabayar !='LUNAS' 
                                    AND jatuhtempo BETWEEN '$tgl_awal' AND '$tgl_akhir' 
                                    GROUP BY id_supplier ORDER BY jatuhtempo ASC");
                            $jatuh->execute();
                            while ($te = $jatuh->fetch(PDO::FETCH_ASSOC)) {

                                $ttl = format_rupiah($te['hutang']);
                                echo "<tr class='warnabaris' >
											<td>$no</td>           
											 <td>$te[jatuhtempo]</td>											 
											 <td>$te[nm_supplier]</td>
											 <td style='text-align:right;'>Rp.  $ttl</td>								
											 <td align='center'><a href='?module=trbmasukpbf&act=detailhutang&tgl_awal=$tgl_awal&tgl_akhir=$tgl_akhir&id=$te[id_supplier]' target='_blanks'title='EDIT' class='btn btn-warning btn-xs'>TAMPIL</a> 	
										</tr>";
                                $total[] = $te['hutang'];
                                $no++;
                            }
                            echo "
						</tbody>
						<tfoot>
						";
                            $tus = format_rupiah(array_sum($total));
                            echo "
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
        case "detailhutang":
            $tgl_awal = $_GET['tgl_awal'];
            $tgl_akhir = $_GET['tgl_akhir'];
            $id_supplier = $_GET['id'];

            
        ?>
            <div class="box box-danger box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">DETAIL TAGIHAN JATUH TEMPO </h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div><!-- /.box-tools -->
                </div>
                <div class="box-body">

                    <form action="modul/mod_trbmasukpbf/ubah_status_lunas.php" method="post" target="_blank">
                        <hr>

                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>tanggal Jatuh Tempo</th>
                                    <th>Kode Transaksi</th>
                                    <th>No Faktur</th>
                                    <th>Distributor</th>
                                    <th>Nilai Tagihan</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $jatuh = $db->prepare("SELECT * FROM trbmasuk 
                                    WHERE id_supplier='$id_supplier' 
                                    AND carabayar !='LUNAS' 
                                    AND jatuhtempo BETWEEN '$tgl_awal' AND '$tgl_akhir' 
                                    ORDER BY jatuhtempo ASC");
                                $jatuh->execute();
                                while ($te = $jatuh->fetch(PDO::FETCH_ASSOC)) {

                                    $ttl = format_rupiah($te['ttl_trbmasuk']);
                                    echo "<tr class='warnabaris' >
									        <td><input type='checkbox' name='check[]' value='$te[kd_trbmasuk]'> $no</td>	                                       
											<td>$te[jatuhtempo]</td>
											 <td>$te[kd_trbmasuk]</td>											 
											 <td>$te[ket_trbmasuk]</td>											 
											 <td>$te[nm_supplier]</td>
											 <td style='text-align:right;'>Rp.  $ttl</td>								
											</tr>";
                                    $total[] = $te['ttl_trbmasuk'];
                                    $no++;
                                }
                                echo "
						</tbody>
						<tfoot>
						";
                                $tus = format_rupiah(array_sum($total));
                                echo "
						            <tr style='background: #00fafa; font-size: 4vh;'>
                                        <td colspan='4'>Total</td>
                                        <td style='text-align:right;' colspan='2'>Rp.  $tus</td>
                                    </tr>
						</tfoot>
						</table>";

                                ?>
                                <div style="text-align:center;">
                                    <?php if ($_SESSION['level'] == 'pemilik'): ?>

                                        <button class='btn  btn-success btn-flat' type='submit' onclick="return confirm('Apakah Faktur yang dipilih sudah LUNAS?')" id="hapus">SUBMIT PELUNASAN</button>

                                    <?php endif; ?>
                                </div>
                    </form>
                </div>
            <?php
            break;
        case "pembelian":
            ?>
                <div class="box box-primary box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Cek Total Pembelian Berdasarkan Tanggal</h3>
                        <div class="box-tools pull-right">
                            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        </div><!-- /.box-tools  -->

                    </div>
                    <div class="box-body">

                        <form method="POST" action="?module=trbmasukpbf&act=totalbeli" target="_blank" enctype="multipart/form-data"
                            class="form-horizontal">

                            </br></br>


                            <div class="form-group">
                                <label class="col-sm-2 control-label">Tanggal Awal</label>
                                <div class="col-sm-4">
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <span class="glyphicon glyphicon-th"></span>
                                        </div>
                                        <input type="text" required="required" class="datepicker" id="tgl_awal" name="tgl_awal"
                                            autocomplete="off">
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
                                        <input type="text" required="required" class="datepicker" id="tgl_akhir" name="tgl_akhir"
                                            autocomplete="off">
                                    </div>
                                </div>
                            </div>


                            <div class="form-group">
                                <label class="col-sm-2 control-label"></label>
                                <div class="buttons col-sm-4">
                                    <input class="btn btn-primary" type="submit" name="btn"
                                        value="TAMPIL">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp

                                    <a class='btn  btn-danger' href='?module=trbmasukpbf'>KEMBALI</a>
                                </div>
                            </div>

                        </form>
                    </div>

                </div>

                <script type="text/javascript">
                    $(function() {
                        $(".datepicker").datepicker({
                            format: 'yyyy-mm-dd',
                            autoclose: true,
                            todayHighlight: true,
                        });
                    });
                </script>


            <?php
            break;
        case "totalbeli":
            $tgl_awal = $_POST['tgl_awal'];
            $tgl_akhir = $_POST['tgl_akhir'];

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


                        <table id="example1" class="table table-bordered table-striped">
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
                                $no = 1;
                                $totalbeli = $db->prepare("SELECT * FROM trbmasuk WHERE tgl_trbmasuk BETWEEN '$tgl_awal' AND '$tgl_akhir' ");
                                $totalbeli->execute();
                                while ($te = $totalbeli->fetch(PDO::FETCH_ASSOC)) {

                                    $ttl = format_rupiah($te['ttl_trbmasuk']);
                                    echo "<tr class='warnabaris' >
											<td>$no</td>           
											 <td>$te[tgl_trbmasuk]</td>
											 <td>$te[kd_trbmasuk]</td>
											 <td>$te[nm_supplier]</td>
											 <td>$te[carabayar]</td>
											 <td style='text-align:right;'>Rp.  $ttl</td>								
											 
										</tr>";

                                    $total[] = $te['ttl_trbmasuk'];
                                    $no++;
                                }
                                echo "
						</tbody>
						<tfoot>
						";
                                $tus = format_rupiah(array_sum($total));
                                $totallunas = $db->prepare("SELECT SUM(ttl_trbmasuk) AS lunas FROM trbmasuk WHERE tgl_trbmasuk BETWEEN '$tgl_awal' AND '$tgl_akhir' AND carabayar='LUNAS'");
                                $totallunas->execute();
                                $lns = $totallunas->fetch(PDO::FETCH_ASSOC);
                                $lunas = format_rupiah($lns['lunas']);
                                
                                $belumlunas = $db->prepare("SELECT SUM(ttl_trbmasuk) AS belum FROM trbmasuk WHERE tgl_trbmasuk BETWEEN '$tgl_awal' AND '$tgl_akhir' AND carabayar='KREDIT'");
                                $belumlunas->execute();
                                $blm = $belumlunas->fetch(PDO::FETCH_ASSOC);
                                $belum = format_rupiah($blm['belum']);
                                
                                echo "
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
        case "distributor":
            ?>
                <div class="box box-primary box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Cek Total Pembelian Berdasarkan Distributor</h3>
                        <div class="box-tools pull-right">
                            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        </div><!-- /.box-tools  -->

                    </div>
                    <div class="box-body">

                        <form method="POST" action="?module=trbmasukpbf&act=tampil_distributor" target="_blank"
                            enctype="multipart/form-data" class="form-horizontal">

                            </br></br>


                            <div class="form-group">
                                <label class="col-sm-2 control-label">Tanggal Awal</label>
                                <div class="col-sm-4">
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <span class="glyphicon glyphicon-th"></span>
                                        </div>
                                        <input type="text" required="required" class="datepicker" id="tgl_awal" name="tgl_awal"
                                            autocomplete="off">
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
                                        <input type="text" required="required" class="datepicker" id="tgl_akhir" name="tgl_akhir"
                                            autocomplete="off">
                                    </div>
                                </div>
                            </div>


                            <div class="form-group">
                                <label class="col-sm-2 control-label"></label>
                                <div class="buttons col-sm-4">
                                    <input class="btn btn-primary" type="submit" name="btn"
                                        value="TAMPIL">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp

                                    <a class='btn  btn-danger' href='?module=trbmasukpbf'>KEMBALI</a>
                                </div>
                            </div>

                        </form>
                    </div>

                </div>

                <script type="text/javascript">
                    $(function() {
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

            
            ?>
                <div class="box box-primary box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Rekap Pembelian berdasarkan Distributor tanggal <?= $tgl_awal ?> hingga <?= $tgl_akhir ?>
                        </h3>
                        <div class="box-tools pull-right">
                            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        </div><!-- /.box-tools -->
                    </div>
                    <div class="box-body">

                        <br><br>


                        <table id="example2" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Distributor</th>
                                    <th>Lunas</th>
                                    <th>Belum Lunas</th>
                                    <th>Total</th>
                                    <th>Detail</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $list = $db->prepare("SELECT SUM(ttl_trbmasuk) AS total,trbmasuk.id_supplier,trbmasuk.nm_supplier FROM trbmasuk 
                                    JOIN supplier ON(trbmasuk.id_supplier=supplier.id_supplier) 
                                    WHERE trbmasuk.tgl_trbmasuk BETWEEN '$tgl_awal' AND '$tgl_akhir' 
                                    GROUP BY trbmasuk.id_supplier ORDER BY total DESC");
                                $list->execute();

                                while ($te = $list->fetch(PDO::FETCH_ASSOC)) {
                                    $totbel = $db->prepare("SELECT SUM(ttl_trbmasuk) AS tepo, kd_trbmasuk FROM trbmasuk 
                                                            WHERE id_supplier='$te[id_supplier]' 
                                                            AND tgl_trbmasuk BETWEEN '$tgl_awal' AND '$tgl_akhir'");
                                    $totbel->execute();
                                    $tb = $totbel->fetch(PDO::FETCH_ASSOC);
                                    
                                    $totbel2 = $db->prepare("SELECT SUM(ttl_trbmasuk) AS tepo2 FROM trbmasuk 
                                                            WHERE carabayar='LUNAS' 
                                                            AND id_supplier='$te[id_supplier]' 
                                                            AND tgl_trbmasuk BETWEEN '$tgl_awal' AND '$tgl_akhir'");
                                    $totbel2->execute();
                                    $tb2 = $totbel2->fetch(PDO::FETCH_ASSOC);
                                    
                                    $totbel3 = $db->prepare("SELECT SUM(ttl_trbmasuk) AS tepo3 FROM trbmasuk 
                                                            WHERE carabayar='KREDIT' 
                                                            AND id_supplier='$te[id_supplier]' 
                                                            AND tgl_trbmasuk BETWEEN '$tgl_awal' AND '$tgl_akhir'");
                                    $totbel3->execute();
                                    $tb3 = $totbel3->fetch(PDO::FETCH_ASSOC);
                                    
                                    $ttl = format_rupiah($te['total']);
                                    $ttl2 = format_rupiah($tb2['tepo2']);
                                    $ttl3 = format_rupiah($tb3['tepo3']);
                                    if ($tb['tepo' > 0]) {
                                        echo "<tr class='warnabaris' >
											<td>$no</td>           
											 <td>$te[nm_supplier]</td>											 
											 <td style='text-align:right;'>Rp. $ttl2</td>
											 <td style='text-align:right;color:red;'>Rp. $ttl3</td>
											 <td style='text-align:right;color:blue;'>Rp.  $ttl</td>
											 <td><a href='?module=trbmasukpbf&act=detail&id=$te[id_supplier]&tgl_awal=$tgl_awal&tgl_akhir=$tgl_akhir' title='detail' target='_blank' class='btn btn-success btn-xs'>SHOW</a>	</td>
										</tr>";
                                        $no++;
                                    }
                                    $total[] = $tb['tepo'];
                                }
                                echo "
						</tbody>
						<tfoot>
						";
                                $tus = format_rupiah(array_sum($total));
                                $totallunas = $db->prepare("SELECT SUM(ttl_trbmasuk) AS lunas FROM trbmasuk 
                                                            WHERE tgl_trbmasuk BETWEEN '$tgl_awal' AND '$tgl_akhir' 
                                                            AND carabayar='LUNAS'");
                                $totallunas->execute();
                                $lns = $totallunas->fetch(PDO::FETCH_ASSOC);
                                $lunas = format_rupiah($lns['lunas']);
                                
                                $belumlunas = $db->prepare("SELECT SUM(ttl_trbmasuk) AS belum FROM trbmasuk 
                                                            WHERE tgl_trbmasuk BETWEEN '$tgl_awal' AND '$tgl_akhir' 
                                                            AND carabayar='KREDIT'");
                                $belumlunas->execute();
                                $blm = $belumlunas->fetch(PDO::FETCH_ASSOC);
                                $belum = format_rupiah($blm['belum']);
                                echo "
						            <tr style='background: #00fafa; font-size: 4vh;'>
                                        <td colspan='3'>Lunas = Rp. $lunas , Belum Lunas = Rp. $belum  </td>
                                        <td style='text-align:right;' > Total</td>
                                        <td style='text-align:right;' colspan='2'>Rp.  $tus</td>
                                    </tr>
						</tfoot>
						</table>";

                                ?>
                    </div>

                </div>
            <?php
            break;
        case "detail":
            $supplier = $_GET['id'];
            $tgl_awal = $_GET['tgl_awal'];
            $tgl_akhir = $_GET['tgl_akhir'];

            
            ?>

                <div class="box box-primary box-solid">
                    <div class="box-header with-border">
                        <h3 class="box-title">Rekap Detail Pembelian dari <?= $dis['nm_supplier'] ?> tanggal <?= $tgl_awal ?> hingga
                            <?= $tgl_akhir ?>
                        </h3>
                        <div class="box-tools pull-right">
                            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        </div><!-- /.box-tools -->
                    </div>
                    <div class="box-body">

                        <br><br>


                        <table id="example2" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Transaksi</th>
                                    <th>No Faktur</th>
                                    <th>Tanggal</th>
                                    <th>Jatuh Tempo</th>
                                    <th>Nilai Transaksi</th>
                                    <th>Status Bayar</th>

                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                $detail = $db->prepare("SELECT * FROM trbmasuk 
                                    WHERE id_supplier=$supplier 
                                    AND tgl_trbmasuk BETWEEN '$tgl_awal' AND '$tgl_akhir'");
                                $detail->execute();

                                while ($te = $detail->fetch(PDO::FETCH_ASSOC)) {
                                    $tr = format_rupiah($te['ttl_trbmasuk']);
                                    echo "<tr class='warnabaris' >
											<td>$no</td>           
											 <td>$te[kd_trbmasuk]</td>											 
											 <td>$te[ket_trbmasuk]</td>											 
											 <td style='text-align:center;'>$te[tgl_trbmasuk] </td>
											 <td style='text-align:center;'>$te[jatuhtempo] </td>
											 <td style='text-align:right;color:red;'>$tr</td>
											 <td style='text-align:right;color:blue;'>$te[carabayar]</td>
											 </tr>";
                                    $no++;

                                    $total[] = $te['ttl_trbmasuk'];
                                }
                                echo "
						</tbody>
						<tfoot>
						";
                                $tus = format_rupiah(array_sum($total));
                                $totallunas = $db->prepare("SELECT SUM(ttl_trbmasuk) AS lunas FROM trbmasuk 
                                                            WHERE id_supplier =$supplier 
                                                            AND tgl_trbmasuk BETWEEN '$tgl_awal' AND '$tgl_akhir' 
                                                            AND carabayar='LUNAS'");
                                $totallunas->execute();
                                $lns = $totallunas->fetch(PDO::FETCH_ASSOC);
                                $lunas = format_rupiah($lns['lunas']);
                                
                                $belumlunas = $db->prepare("SELECT SUM(ttl_trbmasuk) AS belum FROM trbmasuk 
                                                            WHERE id_supplier =$supplier 
                                                            AND tgl_trbmasuk BETWEEN '$tgl_awal' AND '$tgl_akhir' 
                                                            AND carabayar='KREDIT'");
                                $belumlunas->execute();
                                $blm = $belumlunas->fetch(PDO::FETCH_ASSOC);
                                $belum = format_rupiah($blm['belum']);
                                echo "
						            <tr style='background: #00fafa; font-size: 4vh;'>
                                        <td colspan='3'>Lunas = Rp. $lunas , Belum Lunas = Rp. $belum  </td>
                                        <td style='text-align:right;' > Total</td>
                                        <td style='text-align:center;' colspan='2'>Rp.  $tus</td>
                                    </tr>
						</tfoot>
						</table>";

                                ?>
                    </div>

                </div>
    <?php

            break;
            
            case "orders":
            
            $tampil_trbmasuk = $db->prepare("SELECT * FROM orders 
                                        	  WHERE id_resto = 'pesan'
                                        	  ORDER BY orders.id_trbmasuk DESC");
            $tampil_trbmasuk->execute();
            ?>
            
            <div class="box box-primary box-solid">
				<div class="box-header with-border">
					<h3 class="box-title">PESANAN OBAT ATAU BARANG</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
					</div><!-- /.box-tools -->
					<div>
    				    <span class='btn btn-success btn-success'></span>
    				    Barang sudah masuk
    				</div>
				</div>
				
				
				<div class="box-body table-responsive">
    				
					<a class='btn btn-success btn-danger' onclick="javascript: self.history.back()">KEMBALI</a>
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
							
						</tbody>
					</table>
				</div>
			</div>
			
			<script>
			    $(document).ready(function() {
            		$("#tes_table").DataTable({
            			serverSide: true,
            			ajax: {
            				"url": "modul/mod_trbmasukpbf/orders_serverside.php?action=table_data",
            				"dataType": "JSON",
            				"type": "POST"
            			},
            			"rowCallback": function(row, data, index) {
                            
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
            				"className": "text-left",
            				"createdCell": function(td, cellData, rowData) {
                                let masuk  = rowData.masuk;
                                console.log(rowData);
                                if (masuk == 0) {
                                  $(td).css({ background:'#4cbb17', color:'#fff' });
                                } 
                            }
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
	
	   case "orders_detail":
        
            $ubah = $db->prepare("SELECT * FROM orders 
	                                WHERE orders.id_trbmasuk=?");
	        $ubah->execute([$_GET['id']]);
            $re = $ubah->fetch(PDO::FETCH_ASSOC);

            $cekkd = $db->prepare("SELECT * FROM kdbm 
                                    WHERE id_admin=? 
                                    AND id_resto=? 
                                    AND stt_kdbm=?");
            $cekkd->execute([$_SESSION['idadmin'], 'pusat', 'ON']);
            $ketemucekkd = $cekkd->rowCount();
            $hcekkd = $cekkd->fetch(PDO::FETCH_ASSOC);
            $petugas = $_SESSION['namalengkap'];

            if ($ketemucekkd > 0) {
                $kdtransaksi = $hcekkd['kd_trbmasuk'];
            } else {
                $kdunik = date('dmyhis');
                $kdtransaksi = "BMP-" . $kdunik;
                $cekkd2 = $db->prepare("SELECT * FROM kdbm 
                                    WHERE kd_trbmasuk=?");
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
            
            echo "
		  <div class='box box-primary box-solid'>
				<div class='box-header with-border'>
					<h3 class='box-title'>INPUT TRANSAKSI BARANG MASUK</h3>
					<div class='box-tools pull-right'>
						<button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
                    </div><!-- /.box-tools -->
				</div>
				<div class='box-body table-responsive'>
				
						<form onsubmit='return false;' method=POST action='$aksi?module=trbmasukpbf&act=ubah_trbmasuk' enctype='multipart/form-data' class='form-horizontal'>
						
						       <input type=hidden name='id_trbmasuk' id='id_trbmasuk' value='$re[id_trbmasuk]'>
							   <input type=hidden name='kd_trbmasuk' id='kd_trbmasuk' value='$re[kd_trbmasuk]'>
							   <input type=hidden name='kd_orders' id='kd_trbmasuk1' value='$kdtransaksi'>
							   <input type=hidden name='stt_aksi' id='stt_aksi' value='input_order_trbmasuk'>
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
													<input type='text' class='datepicker' name='tgl_trbmasuk' id='tgl_trbmasuk' required='required' value='$tglharini' autocomplete='off'>
											</div>
										</div>
										
									<label class='col-sm-4 control-label'>Kode Pesanan</label>        		
										<div class='col-sm-6'>
											<input type=text name='kd_hid' id='kd_hid' class='form-control' required='required' value='$re[kd_trbmasuk]' autocomplete='off' Disabled>
										</div>
									
									<label class='col-sm-4 control-label'>Kode Transaksi</label>        		
										<div class='col-sm-6'>
											<input type=text name='kd_hid1' id='kd_hid1' class='form-control' required='required' value='$kdtransaksi' autocomplete='off' Disabled>
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
									    <input type='text' name='sat_dtrbmasuk' id='sat_dtrbmasuk' class='form-control' autocomplete='off' readonly>
										
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
				
				<div id='tabeldata1'>
				
			</div>";
   
            break;
        
        case "evaluasi":
    ?>
            <div class="box box-primary box-solid table-responsive">
                <div class="box-header with-border">
                    <h3 class="box-title">TRANSAKSI BARANG MASUK DARI PBF</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div><!-- /.box-tools -->
                </div>
                <div class="box-body table-responsive">
                    <form action="modul/mod_trbmasukpbf/ubah_status_lunas.php" method="post">
                        <a class='btn  btn-secondary btn-danger' href='javascript:self.history.back()'>Kembali</a>
                        <hr>
                        <p>
                        <p>
                            <a class='btn  btn-warning  btn-flat' href='#'></a>
                            <small>* Pembayaran belum lunas</small>
                            <br><br>


                        <table id="tes1" class="table table-bordered table-striped">
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
                                
                            </tbody>
                        </table>
                        
                    </form>
                </div>
            </div>

            <script>
                $(document).ready(function() {
                    $("#tes1").DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: {
                            "url": "modul/mod_trbmasukpbf/evaluasi-serverside.php?action=table_data",
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
        
        case "evaluasi_tampil":
            $id = $_GET['id'];
            
            $trbmasuk   = $db->prepare("SELECT * FROM trbmasuk 
                            WHERE id_trbmasuk = '$id' 
                            AND kd_orders != ''");
            $trbmasuk->execute();
            $data       = $trbmasuk->fetch(PDO::FETCH_ASSOC);
    ?>
            
            <div class="box box-primary box-solid table-responsive">
                <div class="box-header with-border">
                    <h3 class="box-title">EVALUASI TRANSAKSI BARANG MASUK</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div><!-- /.box-tools -->
                </div>
                <div class="box-body table-responsive">
                    <form action="modul/mod_trbmasukpbf/ubah_status_lunas.php" method="post">
                        <a class='btn  btn-secondary btn-danger' href='javascript:self.history.back()'>Kembali</a>
                        <hr>
                        
                        
                        <div class="form-group row">
                            <label for="inputEmail3" class="col-sm-2 col-form-label">No Pesanan</label>
                            <div class="col-sm-10">
                                <label>: <?= $data['kd_orders'] ?></label>
                            </div>
                            
                            <label for="inputEmail3" class="col-sm-2 col-form-label">No Kode Masuk</label>
                            <div class="col-sm-10">
                                <label>: <?= $data['kd_trbmasuk'] ?></label>
                            </div>
                            
                            <label for="inputEmail3" class="col-sm-2 col-form-label">Supplier</label>
                            <div class="col-sm-10">
                                <label>: <?= $data['nm_supplier'] ?></label>
                            </div>
                            
                            <label for="inputEmail3" class="col-sm-2 col-form-label">Tgl Masuk</label>
                            <div class="col-sm-10">
                                <label>: <?= $data['tgl_trbmasuk'] ?></label>
                            </div>
                        </div>
                    
                        
                        <hr>
                        <table id="example1" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode Barang</th>
                                    <th>Nama Barang</th>
                                    <th>Satuan</th>
                                    <th>Qty Pesan</th>
                                    <th>Qty Masuk</th>
                                    <th>Harga Pesan</th>
                                    <th>Harga Masuk</th>
                                    <th>Total Harga Masuk</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    // $trbmasuk_detail   = $db->query("SELECT * FROM trbmasuk_detail 
                                    //                                     JOIN trbmasuk ON trbmasuk.kd_trbmasuk=trbmasuk_detail.kd_trbmasuk
                                    //                                 WHERE trbmasuk.id_trbmasuk = '$id' AND trbmasuk.kd_orders != ''
                                    //                                 ORDER BY trbmasuk_detail.nmbrg_dtrbmasuk ASC");
                                    $trbmasuk_detail   = $db->prepare("SELECT *, SUM(trbmasuk_detail.qty_dtrbmasuk) AS masuk
                                                                        FROM trbmasuk_detail 
                                                                        JOIN trbmasuk ON trbmasuk.kd_trbmasuk=trbmasuk_detail.kd_trbmasuk
                                                                    WHERE trbmasuk.id_trbmasuk = '$id' AND trbmasuk.kd_orders != ''
                                                                    GROUP BY trbmasuk_detail.kd_barang");
                                    $trbmasuk_detail->execute();
                                    $no = 1;
                                    $total = 0;
                                    while($detail = $trbmasuk_detail->fetch(PDO::FETCH_ASSOC)):
                                        $subtotal = $detail['hrgsat_dtrbmasuk'] * $detail['masuk'];
                                        $total  = $total + $subtotal;
                                        $orders = $db->prepare("SELECT * FROM ordersdetail 
                                                                    WHERE kd_trbmasuk = ? 
                                                                    AND id_barang = ?");
                                        $orders->execute([$detail['kd_orders'], $detail['id_barang']]);
                                        $order  = $orders->fetch(PDO::FETCH_ASSOC);                           
                                ?>
                                <tr>
                                    <td align="center"><?=$no;?></td>
                                    <td><?=$detail['kd_barang'];?></td>
                                    <td><?=$detail['nmbrg_dtrbmasuk'];?></td>
                                    <td><?=$detail['sat_dtrbmasuk'];?></td>
                                    <td align="center" >
                                        <?=$order['qty_dtrbmasuk'];?></td>
                                    <td align="center" <?=($order['qty_dtrbmasuk'] > $detail['masuk'])?'style="background-color:#f95959"':(($order['qty_dtrbmasuk'] < $detail['masuk'])?'style="background-color:#00bbf0"':'');?>>
                                        <?=$detail['masuk'];?></td>
                                    <td align="right"><?=format_rupiah($order['hrgsat_dtrbmasuk']);?></td>
                                    <td align="right" <?=($order['hrgsat_dtrbmasuk'] < $detail['hrgsat_dtrbmasuk'])?'style="background-color:#f95959"':(($order['hrgsat_dtrbmasuk'] > $detail['hrgsat_dtrbmasuk'])?'style="background-color:#00bbf0"':'');?>>
                                        <?=format_rupiah($detail['hrgsat_dtrbmasuk']);?></td>
                                    <td align=right><?=format_rupiah($subtotal);?></td>
                                    
                                </tr>
                                <?php
                                    $no++;
                                    endwhile;
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="8" align="right"><h4>Total</h4></td>
                                    <td align="right"><h4><?=format_rupiah($total)?></h4></td>
                                </tr>
                            </tfoot>
                        </table>
                        
                    </form>
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
                                <th style="vertical-align: middle; background-color: #008000; text-align: left; ">Nama
                                    Barang</th>
                                <th style="vertical-align: middle; background-color: #008000; text-align: right; ">Qty</th>
                                <th style="vertical-align: middle; background-color: #008000; text-align: center; ">Sat
                                    Grosir</th>
                                <th style="vertical-align: middle; background-color: #008000; text-align: center; ">Konversi
                                </th>
                                <th style="vertical-align: middle; background-color: #008000; text-align: right; ">HNA</th>
                                <th style="vertical-align: middle; background-color: #008000; text-align: center; ">Pilih
                                </th>
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
                                <th style="vertical-align: middle; background-color: #008000; text-align: left; ">Supplier
                                </th>
                                <th style="vertical-align: middle; background-color: #008000; text-align: left; ">Telepon
                                </th>
                                <th style="vertical-align: middle; background-color: #008000; text-align: left; ">Alamat
                                </th>
                                <th style="vertical-align: middle; background-color: #008000; text-align: center; ">Pilih
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $no = 1;
                            $tampil_dproyek = $db->prepare("SELECT * FROM supplier ORDER BY nm_supplier ASC");
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
            tabel_detail1();
            
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

        $('#nmbrg_dtrbmasuk_enter').on('click', function() {
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
            var stt_aksi = document.getElementById('stt_aksi').value;
            if(stt_aksi == 'input_order_trbmasuk'){
                var kd_trbmasuk1 = document.getElementById('kd_trbmasuk1').value;
            } else {
                var kd_trbmasuk1 = document.getElementById('kd_trbmasuk').value;
            }
            var kd_trbmasuk         = document.getElementById('kd_trbmasuk').value;
            var id_barang           = document.getElementById('id_barang').value;
            var kd_barang           = document.getElementById('kd_barang').value;
            var nmbrg_dtrbmasuk     = document.getElementById('nmbrg_dtrbmasuk').value;
            var stok_barang         = document.getElementById('stok_barang').value;
            var qty_dtrbmasuk       = document.getElementById('qty_dtrbmasuk').value;
            var sat_dtrbmasuk       = document.getElementById('sat_dtrbmasuk').value;
            var konversi            = document.getElementById('konversi').value;
            var hnasat_dtrbmasuk    = document.getElementById('hnasat_dtrbmasuk').value;
            var hrgjual_dtrbmasuk   = document.getElementById('hrgjual_dtrbmasuk').value;
            var diskon              = document.getElementById('diskon').value;
            var no_batch            = document.getElementById('no_batch').value;
            var exp_date            = document.getElementById('exp_date').value;

            if (nmbrg_dtrbmasuk == "") {
                alert('Belum ada Item terpilih');
            } else if (qty_dtrbmasuk == "") {
                alert('Qty tidak boleh kosong');
            } else if (konversi == 0) {
                alert('konversi tidak boleh kosong');
            } else if (hnasat_dtrbmasuk == "") {
                alert('Harga tidak boleh kosong');
            } else {

                $.ajax({
                    type: 'post',
                    url: "modul/mod_trbmasukpbf/simpandetail_tbm.php",
                    data: {
                        'kd_trbmasuk'       : kd_trbmasuk,
                        'kd_trbmasuk1'      : kd_trbmasuk1,
                        'id_barang'         : id_barang,
                        'kd_barang'         : kd_barang,
                        'nmbrg_dtrbmasuk'   : nmbrg_dtrbmasuk,
                        'qty_dtrbmasuk'     : qty_dtrbmasuk,
                        'sat_dtrbmasuk'     : sat_dtrbmasuk,
                        'konversi'          : konversi,
                        'hnasat_dtrbmasuk'  : hnasat_dtrbmasuk,
                        'hrgjual_dtrbmasuk' : hrgjual_dtrbmasuk,
                        'diskon'            : diskon,
                        'no_batch'          : no_batch,
                        'exp_date'          : exp_date,
                        'stt_aksi'          : stt_aksi
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
                        tabel_detail1();
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

        $(document).on('click', '#hapusorder', function () {

            var id_dtrbmasuk = $(this).data('id_dtrbmasuk');
    
            $.ajax({
                type: 'post',
                url: "modul/mod_trbmasukpbf/hapusdetail_order.php",
                data: {
                    id_dtrbmasuk: id_dtrbmasuk
                },
    
                success: function (data) {
                    //setelah simpan data, tabel_detail data terbaru
                    //alert('Hapus data detail berhasil');
                    tabel_detail1();
                    //hilangkan modal
                    // $(".close").click();
                    console.log(data);
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
        
        function tabel_detail1() {

            var kd_trbmasuk = document.getElementById('kd_trbmasuk').value;
    
            $.ajax({
                url: 'modul/mod_trbmasukpbf/tbl_detail1.php',
                type: 'post',
                data: {
                    'kd_trbmasuk': kd_trbmasuk
                },
                success: function (data) {
                    $('#tabeldata1').html(data);
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
            if(stt_aksi == 'input_order_trbmasuk'){
                var kd_trbmasuk1 = document.getElementById('kd_trbmasuk1').value;
            } else {
                var kd_trbmasuk1 = document.getElementById('kd_trbmasuk').value;
            }
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
            
            var nilai_batch = document.getElementById('nilai_batch').value;

            var ttl_trkasir1 = ttl_trkasir.replace(".", "");
            var dp_bayar1 = dp_bayar.replace(".", "");
            var sisa_bayar1 = sisa_bayar.replace(".", "");

            var ttl_trkasir1x = ttl_trkasir1.replace(".", "");
            var dp_bayar1x = dp_bayar1.replace(".", "");
            var sisa_bayar1x = sisa_bayar1.replace(".", "");

            if (nm_supplier == "") {
                alert('Belum ada data supplier');
            } else if (nilai_batch == "0") {
                alert('No. Batch tidak boleh kosong');
            } else {

                $.ajax({

                    type: 'post',
                    url: "modul/mod_trbmasukpbf/aksi_trbmasuk.php",

                    data: {
                        'id_trbmasuk': id_trbmasuk,
                        'kd_trbmasuk': kd_trbmasuk,
                        'kd_trbmasuk1': kd_trbmasuk1,
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