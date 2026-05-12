<style>
	.table-condensed {
		font-size: 13px;
	}

	.table-akum {
		font-size: 11px;
	}

	.judul-table {

		text-align: center;
		font-weight: bold;
		font-size: 13px;
		background-color: #008000;
		color: white;

	}
</style>
<div class="box-body table-responsive">
	<table id="example5" class="table table-condensed table-bordered table-striped table-hover">
		<thead>
			<tr class="judul-table">
				<th style="vertical-align: middle; background-color: #008000; text-align: center; ">No</th>
				<th style="vertical-align: middle; background-color: #008000; text-align: left; ">Kode Barang</th>
				<th style="vertical-align: middle; background-color: #008000; text-align: left; ">Nama Barang</th>
				<th style="vertical-align: middle; background-color: #008000; text-align: center; width: 70px">Qty Retail</th>
				<th style="vertical-align: middle; background-color: #008000; text-align: center; ">Satuan Retail</th>
				<th style="vertical-align: middle; background-color: #008000; text-align: center; ">Konversi</th>
				<th style="vertical-align: middle; background-color: #008000; text-align: center; width: 70px">Qty Grosir</th>
				<th style="vertical-align: middle; background-color: #008000; text-align: center; ">Satuan Grosir</th>
				<th style="vertical-align: middle; background-color: #008000; text-align: right; ">Hrg Beli</th>
				<th style="vertical-align: middle; background-color: #008000; text-align: right; ">Total</th>
				<th style="vertical-align: middle; background-color: #008000; text-align: center; ">Aksi</th>
			</tr>
		</thead>
		<tbody>
			<?php
			include "../../../configurasi/koneksi.php";
			include "../../../configurasi/fungsi_rupiah.php";

			$kd_trbmasuk = $_POST['kd_trbmasuk'];

			//AMBIL DATA UNTUK FOOTER
			$dfoot = $db->prepare("SELECT * FROM orders WHERE kd_trbmasuk=?");
			$dfoot->execute([$kd_trbmasuk]);
			$rf = $dfoot->fetch(PDO::FETCH_ASSOC);
			$dp_bayar = format_rupiah($rf['dp_bayar']);

			$sumprice = $db->prepare("SELECT kd_trbmasuk, SUM(hrgttl_dtrbmasuk) as grandnya FROM ordersdetail 
							WHERE kd_trbmasuk=?");
			$sumprice->execute([$kd_trbmasuk]);
			$ttlprice = $sumprice->fetch(PDO::FETCH_ASSOC);
			$grandnya = format_rupiah($ttlprice['grandnya']);

			$noreq = $db->prepare("SELECT * FROM ordersdetail 
							   WHERE kd_trbmasuk=?
							   ORDER BY id_dtrbmasuk ASC");
			$noreq->execute([$kd_trbmasuk]);
			$no = 1;
			$nilai_konversi = 1;
			while ($r = $noreq->fetch(PDO::FETCH_ASSOC)) {

				$hrgsat_dtrbmasuk = format_rupiah($r['hrgsat_dtrbmasuk']);
				$hrgttl_dtrbmasuk = format_rupiah($r['hrgttl_dtrbmasuk']);
				$nilai_konversi = $nilai_konversi * $r['konversi'];

				echo "<tr style='font-size: 13px;'>
											<td align='center'>$no</td>           
											 <td align='left'>$r[kd_barang]</td>
											 <td>$r[nmbrg_dtrbmasuk]</td>
											 <td align='center'>
											    <input type='number' id='qty_i_dtrbmasuk' value='$r[qty_dtrbmasuk]' style='width: 50px'
											    data-id_dtrbmasuk       = '$r[id_dtrbmasuk]'
											    data-hrgsat_dtrbmasuk   = '$r[hrgsat_dtrbmasuk]'
											    data-konversi           = '$r[konversi]'
											    data-qty_dtrbmasuk      = '$r[qty_dtrbmasuk]'
											    >
											 
											 </td>
											 <td align='center'>$r[sat_dtrbmasuk]</td>
											 <td align='center'>
											    <input type='number' id='i_konversi' value='$r[konversi]' style='width: 50px'
											    data-id_dtrbmasuk       = '$r[id_dtrbmasuk]'
											    data-hrgsat_dtrbmasuk   = '$r[hrgsat_dtrbmasuk]'
											    data-qty_dtrbmasuk      = '$r[qty_dtrbmasuk]'
											    data-konversi           = '$r[konversi]'
											    >
											 </td>
											 <td align='center'>
											    $r[qtygrosir_dtrbmasuk]
											    
											 </td>
											 <td align='center'>
											    <select id='i_satgrosir_dtrbmasuk' style='height: 25px;'  
											        data-id_dtrbmasuk = '$r[id_dtrbmasuk]'
											        data-id_barang = '$r[id_barang]'>
											        <option value='$r[satgrosir_dtrbmasuk]'>$r[satgrosir_dtrbmasuk]</option>
											 ";
											    $satuan = $db->prepare("SELECT * FROM satuan ORDER BY id_satuan ASC");
											    $satuan->execute();
											    while($st = $satuan->fetch(PDO::FETCH_ASSOC)){
											        echo "<option value='$st[nm_satuan]'>$st[nm_satuan]</option>";
											    }
											 echo "
											    </select>
											    
											 </td>
											 <td align='right'>$hrgsat_dtrbmasuk</td>
											 <td align='right'>$hrgttl_dtrbmasuk</td>
											 <td align='center'>
											    <button class='btn btn-xs btn-danger' id='hapusdetail' 
												 data-id_dtrbmasuk='$r[id_dtrbmasuk]'>
												     <i class='glyphicon glyphicon-remove'></i>
												 </button>
												
											</td>
										</tr>";

				$no++;
			}
			echo "</tbody></table>
						
							<p>
						<legend class='scheduler-border'></legend>
							<div class='col-md-6'>	
							
							</div>
							
							
							<div class='col-lg-6'>	
								
								<div class='text-right'>
									<label class='col-sm-6 control-label'>SUB TOTAL</label>        		
									 <div class='col-sm-6'>
										<input type='text' name='ttl_trkasir' id='ttl_trkasir' value='$grandnya' class='form-control input-validation-error' style='font-size: 18px; color: #fff; font-weight: bold; text-align: right; background: #000000;' autocomplete='off'>
										<input type='hidden' name='nilai_konversi' id='nilai_konversi' value='$nilai_konversi'>
									 </div>
								</div>
								
								<div class='text-right'>
									<label class='col-sm-6 control-label'>DISKON</label>        		
									 <div class='col-sm-6'>
										<input type='text' name='dp_bayar' id='dp_bayar' value='$dp_bayar' class='form-control'  style='font-size: 18px; color: #000000; font-weight: bold; text-align: right;' autocomplete='off'>
									 </div>
								</div>
								
								<div class='text-right'>
									<label class='col-sm-6 control-label'>GRAND TOTAL</label>        		
									 <div class='col-sm-6'>
										<input type='text' name='sisa_bayar' id='sisa_bayar' class='form-control' style='font-size: 18px; color: #fff; font-weight: bold; text-align: right; background: #000000;' autocomplete='off'>
									 </div>
								</div>
								
							</div>
						      
					</div>";
			?>
			<script>
				$(document).ready(function() {
					HitungDP();
					$("#example5").DataTable()
				});


				//hitung dp
				$('#dp_bayar').keydown(function(e) {
					if (e.which == 13) { // e.which == 13 merupakan kode yang mendeteksi ketika anda   // menekan tombol enter di keyboard
						//letakan fungsi anda disini

						HitungDP();

					}
				});

				//rubah format rupiah
				function formatRupiah(angka) {
					var reverse = angka.toString().split('').reverse().join(''),
						ribuan = reverse.match(/\d{1,3}/g);
					ribuan = ribuan.join('.').split('').reverse().join('');
					return ribuan;
				}


				function HitungDP() {

					var ttl_trkasir = document.getElementById('ttl_trkasir').value;
					var dp_bayar = document.getElementById('dp_bayar').value;

					if (ttl_trkasir == "") {
						var ttl_trkasir = "0";
					} else {}

					if (dp_bayar == "") {
						var dp_bayar = "0";
					} else {}

					var res1 = ttl_trkasir.replace(".", "");
					var res2 = dp_bayar.replace(".", "");

					var res1x = res1.replace(".", "");
					var res2x = res2.replace(".", "");

					var total2 = parseInt(res1x) * (1 - (parseInt(res2x) / 100));


					document.getElementById("dp_bayar").value = formatRupiah(dp_bayar);
					document.getElementById("sisa_bayar").value = formatRupiah(total2.toFixed(0));

				}
				
				// field edit qty
                // $('#example5 tbody').on('change', '#qty_i_dtrbmasuk', function () {
                //     var qty_dtrbmasuk       = $(this).val();
                //     var id_dtrbmasuk        = $(this).data('id_dtrbmasuk');
                                
                //     // $.ajax({
                //     //     type: "POST",
                //     //     url: "modul/mod_lapstok/update_orderskritis.php",
                //     //     data: { 
                //     //         "id_dtrbmasuk"  : id_dtrbmasuk,
                //     //         "qty_dtrbmasuk" : qty_dtrbmasuk
                //     //     },
                //     //     success: function(data)
                //     //     {
                //     //         tabel_detail();
                //     //         console.log(data);
                //     //     }
                //     // });
                //     console.log('id_dtrbmasuk = '+id_dtrbmasuk+'\nqty_dtrbmasuk = '+qty_dtrbmasuk)            
                // });
                
                $('#example5 tbody').on('change', '#qty_i_dtrbmasuk', function () {
        		    var qty_dtrbmasuk       = $(this).val();
        		    var qty_old_dtrbmasuk   = $(this).data('qty_dtrbmasuk');
                    var id_dtrbmasuk        = $(this).data('id_dtrbmasuk');
                    var konversi            = $(this).data('konversi');
        		    
        		    if(qty_dtrbmasuk > qty_old_dtrbmasuk){
        		        var qty_grosir          = Math.ceil(qty_dtrbmasuk/konversi);
        		        var qty_retail          = qty_grosir * konversi;
        		    } else {
        		        var qty_grosir          = Math.floor(qty_dtrbmasuk/konversi);
        		        var qty_retail          = qty_grosir * konversi;
        		    }
        		    
        		  //  console.log('qty_retail => '+qty_retail+'\nqty_grosir => '+qty_grosir);
                    $.ajax({
                        url: 'modul/mod_lapstok/update_orderskritis.php?act=update_order',
                        type: 'post',
                        data: {
                            'id_dtrbmasuk'          : id_dtrbmasuk,
                            'qty_dtrbmasuk'         : qty_retail,
                            'konversi'              : konversi,
                            'qtygrosir_dtrbmasuk'   : qty_grosir
                        },
                        success: function (data) {
                            // $('#tabeldata1').html(data);
                            $(this).val(qty_retail);
                            tabel_detail();
                        }
            
                    });
                });
                
                $('#example5 tbody').on('change', '#i_konversi', function () {
        		    var konversi            = $(this).val();
        		    var old_konversi        = $(this).data('konversi');
                    var id_dtrbmasuk        = $(this).data('id_dtrbmasuk');
                    var qty_dtrbmasuk       = $(this).data('qty_dtrbmasuk');
                    
                    
        		    if(konversi > old_konversi){
        		        var qty_grosir          = Math.ceil(qty_dtrbmasuk/konversi);
        		        var qty_retail          = qty_grosir * konversi;
        		    } else {
        		        var qty_grosir          = Math.floor(qty_dtrbmasuk/konversi);
        		        var qty_retail          = qty_grosir * konversi;
        		    }
        		    
        		  //  console.log('qty_retail => '+qty_retail+'\nqty_grosir => '+qty_grosir);
                    $.ajax({
                        url: 'modul/mod_lapstok/update_orderskritis.php?act=update_order',
                        type: 'post',
                        data: {
                            'id_dtrbmasuk'          : id_dtrbmasuk,
                            'qty_dtrbmasuk'         : qty_retail,
                            'konversi'              : konversi,
                            'qtygrosir_dtrbmasuk'   : qty_grosir
                        },
                        success: function (data) {
                            // $('#tabeldata1').html(data);
                            tabel_detail();
                        }
            
                    });
                });
                
                $('#example5 tbody').on('change', '#i_satgrosir_dtrbmasuk', function () {
        		    var satgrosir_dtrbmasuk = $(this).val();
                    var id_dtrbmasuk        = $(this).data('id_dtrbmasuk');
                    var id_barang           = $(this).data('id_barang');
                    
                    $.ajax({
                        url: 'modul/mod_lapstok/update_orderskritis.php?act=satgrosir',
                        type: 'post',
                        data: {
                            'id_dtrbmasuk'          : id_dtrbmasuk,
                            'satgrosir_dtrbmasuk'   : satgrosir_dtrbmasuk,
                            'id_barang'             : id_barang
                        },
                        success: function (data) {
                            tabel_detail();
                        }
            
                    });
                });
			</script>