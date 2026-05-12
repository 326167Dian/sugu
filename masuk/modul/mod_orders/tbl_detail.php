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
				<th style="vertical-align: middle; background-color: #008000; text-align: right; ">Qty Retail</th>
				<th style="vertical-align: middle; background-color: #008000; text-align: center; ">Sat Retail</th>
				<th style="vertical-align: middle; background-color: #008000; text-align: center; ">Konversi</th>
				<th style="vertical-align: middle; background-color: #008000; text-align: center; ">Qty Grosir</th>
				<th style="vertical-align: middle; background-color: #008000; text-align: center; ">Sat Grosir</th>
				<th style="vertical-align: middle; background-color: #008000; text-align: right; ">Hrg Beli</th>
				<th style="vertical-align: middle; background-color: #008000; text-align: right; ">Total</th>
				<th style="vertical-align: middle; background-color: #008000; text-align: center; ">Aksi</th>
			</tr>
		</thead>
		<tbody>
			<?php
			include "../../../configurasi/koneksi.php";
			include "../../../configurasi/fungsi_rupiah.php";

			$kd_trbmasuk = isset($_POST['kd_trbmasuk']) ? $_POST['kd_trbmasuk'] : '';

			//AMBIL DATA UNTUK FOOTER
			$dfoot = $db->prepare("SELECT * FROM orders WHERE kd_trbmasuk=?");
			$dfoot->execute([$kd_trbmasuk]);
			$rf = $dfoot->fetch(PDO::FETCH_ASSOC);
			if ($rf && isset($rf['dp_bayar'])) {
				$dp_bayar = format_rupiah($rf['dp_bayar']);
			} else {
				$dp_bayar = format_rupiah(0);
			}

			$sumprice = $db->prepare("SELECT kd_trbmasuk, SUM(hrgttl_dtrbmasuk) as grandnya FROM ordersdetail 
							WHERE kd_trbmasuk=?");
			$sumprice->execute([$kd_trbmasuk]);
			$ttlprice = $sumprice->fetch(PDO::FETCH_ASSOC);
			$grandRaw = isset($ttlprice['grandnya']) ? $ttlprice['grandnya'] : 0;
			$grandnya = format_rupiah($grandRaw);

			$noreq = $db->prepare("SELECT * FROM ordersdetail 
							   WHERE kd_trbmasuk=?
							   ORDER BY id_dtrbmasuk ASC");
			$noreq->execute([$kd_trbmasuk]);
			$no = 1;
			while ($r = $noreq->fetch(PDO::FETCH_ASSOC)) {

				$hrgsat_dtrbmasuk = format_rupiah($r['hrgsat_dtrbmasuk']);
				$hrgttl_dtrbmasuk = format_rupiah($r['hrgttl_dtrbmasuk']);

				echo "<tr style='font-size: 13px;'>
											<td align=center>$no</td>           
											 <td align=left>$r[kd_barang]</td>
											 <td>$r[nmbrg_dtrbmasuk]</td>
											 <td align=right>$r[qty_dtrbmasuk]</td>
											 <td align=center>$r[sat_dtrbmasuk]</td>
											 <td align=center>$r[konversi]</td>
											 <td align=center>$r[qtygrosir_dtrbmasuk]</td>
											 <td align=center>$r[satgrosir_dtrbmasuk]</td>
											 <td align=right>$hrgsat_dtrbmasuk</td>
											 <td align=right>$hrgttl_dtrbmasuk</td>
											 <td align=center>
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
			</script>