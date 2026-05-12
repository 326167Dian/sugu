<style>
.table-condensed {
font-size: 13px;
}

.table-akum {
font-size: 11px;
}

.judul-table{

text-align: center; 
font-weight: bold; 
font-size: 13px;
background-color: #008000;
color: white;

}

</style>
			<div class="box-body table-responsive">
					<table id="example5" class="table table-condensed table-bordered table-striped table-hover" >
						<thead>
							<tr class="judul-table">
								<th style="vertical-align: middle; background-color: #008000; text-align: center; ">No</th>
								<th style="vertical-align: middle; background-color: #008000; text-align: center; ">Jenis</th>
								<th style="vertical-align: middle; background-color: #008000; text-align: center; ">Resep</th>
								<th style="vertical-align: middle; background-color: #008000; text-align: left; ">Kode Barang</th>
								<th style="vertical-align: middle; background-color: #008000; text-align: left; ">Nama Barang</th>
								<th style="vertical-align: middle; background-color: #008000; text-align: right; ">Qty</th>
								<th style="vertical-align: middle; background-color: #008000; text-align: center; ">Satuan</th>
								<th style="vertical-align: middle; background-color: #008000; text-align: center; ">batch</th>
								<th style="vertical-align: middle; background-color: #008000; text-align: center; ">Exp</th>
								<th style="vertical-align: middle; background-color: #008000; text-align: right; ">Harga</th>
								<th style="vertical-align: middle; background-color: #008000; text-align: right; ">Disc</th>
								<th style="vertical-align: middle; background-color: #008000; text-align: right; ">Total</th>
								<th style="vertical-align: middle; background-color: #008000; text-align: center; ">Aksi</th>
							</tr>
						</thead>
						<tbody>
						<?php 
						include "../../../configurasi/koneksi.php";
						include "../../../configurasi/fungsi_rupiah.php";
						$aksi = "modul/mod_trkasir/aksi_trkasir.php";
    					$aksi_trkasir = "masuk/modul/mod_trkasir/aksi_trkasir.php";
						
							$stt_aksi = isset($_POST['stt_aksi'])? $_POST['stt_aksi'] : '';
                            $kd_trkasir = isset($_POST['kd_trkasir'])? $_POST['kd_trkasir'] : '';
							
							//AMBIL DATA UNTUK FOOTER
							$dfoot = $db->prepare("SELECT * FROM trkasir 
                        							JOIN carabayar on trkasir.id_carabayar = carabayar.id_carabayar
                        							WHERE trkasir.kd_trkasir=?");
                        	$dfoot->execute([$kd_trkasir]);
							$rf = $dfoot->fetch(PDO::FETCH_ASSOC);
							$diskon = 0 ;
                            if ($rf) {
                                $carabayar = $rf['id_carabayar'];
                            } else {
                                $carabayar = "";
                            }
							
							   
							$sumprice = $db->prepare("SELECT kd_trkasir, SUM(hrgttl_dtrkasir) as grandnya FROM trkasir_detail 
							                            WHERE kd_trkasir=?");
							$sumprice->execute([$kd_trkasir]);
							$ttlprice = $sumprice->fetch(PDO::FETCH_ASSOC);
							$grandnya = format_rupiah($ttlprice['grandnya']);
						
						    $noreq = $db->prepare("SELECT * FROM trkasir_detail
                    							   WHERE trkasir_detail.kd_trkasir=?
                    							   ORDER BY trkasir_detail.id_dtrkasir ASC");
                    		$noreq->execute([$kd_trkasir]);
							$no=1;
							while ($r = $noreq->fetch(PDO::FETCH_ASSOC)){
								
								$hrgjual_dtrkasir = format_rupiah($r['hrgjual_dtrkasir']);
								$hrgttl_dtrkasir = format_rupiah($r['hrgttl_dtrkasir']);
								$resep_val = (isset($r['resep']) && $r['resep'] == 'YA') ? 'YA' : 'TIDAK';
                                if($tipe = ($r['tipe']==1)){ $tipe = '<span class="btn btn-xs btn-primary">Reguler</span>';}
                                else if($tipe = ($r['tipe']==2)){ $tipe = '<span class="btn btn-xs btn-success">Resep</span>';}
                                else if($tipe = ($r['tipe']==3)){ $tipe = '<span class="btn btn-xs btn-info">Marketplace</span>';}
                                else if($tipe = ($r['tipe']==4)){ $tipe = '<span class="btn btn-xs btn-warning">Market Place</span>';}
                                else if($tipe = ($r['tipe']==5)){ $tipe = '<span class="btn btn-xs btn-warning">Lazada</span>';}
                                else if($tipe = ($r['tipe']==6)){ $tipe = '<span class="btn btn-xs btn-warning">Shopee</span>';}

                                $resep_inline = "<select class='form-control input-sm inline-resep'>
                                                    <option value='TIDAK' " . ($resep_val == 'TIDAK' ? 'selected' : '') . ">TIDAK</option>
                                                    <option value='YA' " . ($resep_val == 'YA' ? 'selected' : '') . ">YA</option>
                                                 </select>";

                                $qty_inline = "<input type='number' min='1' class='form-control input-sm inline-qty text-right' value='".$r['qty_dtrkasir']."'>";

                                    echo "<tr style='font-size: 13px;' data-id_dtrkasir='$r[id_dtrkasir]'>
											<td align=center>$no</td>           
											<td align=center>$tipe</td>           
											<td align=center>$resep_inline</td>
											<td align=left>$r[kd_barang]</td>
											<td>$r[nmbrg_dtrkasir]</td>
											<td align=right>$qty_inline</td>
											<td align=center>$r[sat_dtrkasir]</td>
											<td align=center>$r[no_batch]</td>
											<td align=center>$r[exp_date]</td>
											<td align=right>$hrgjual_dtrkasir</td>
											<td align=right>$r[disc]</td>
											<td align=right>$hrgttl_dtrkasir</td>
											<td align=center>
											<small class='inline-status text-muted' style='display:block;margin-top:3px;'></small>
											
											<button class='btn btn-xs btn-danger' id='hapusdetail' 
												data-id_dtrkasir='$r[id_dtrkasir]' data-id_barang='$r[id_barang]'>
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
										</p>
											<div class='buttons'>
												<button type='button' class='btn btn-primary right-block' onclick='simpan_transaksi();'>[F3] SIMPAN TRANSAKSI</button>
												&nbsp
												<input class='btn btn-danger' type='button' value=BATAL onclick=self.history.back()>
											</div>
											";
                                            
											if($stt_aksi == "input_trkasir"){
											}else{
											echo"
											<p>
											<div class='buttons'>
												<button type='button' class='btn btn-success right-block' onclick='cetakstruk();'>CETAK STRUK</button>
												<button type='button' class='btn btn-success right-block' onclick='cetakstrukresep();'>CETAK STRUK RESEP</button>
												<a href=javascript:confirmdelete('$aksi?module=trkasir&act=hapus&id=$rf[id_trkasir]') title='HAPUS' class='btn btn-danger right-block'>HAPUS</a> 
											</div>
											";
											}
											echo"
								
								
							</div>
							
							
							<div class='col-lg-6'>	
								
								<div class='text-right'>
									<label class='col-sm-6 control-label'>SUB TOTAL</label>        		
									 <div class='col-sm-6'>
										<input type='text' name='ttl_trkasir' id='ttl_trkasir' value='$grandnya' class='form-control input-validation-error' style='font-size: 18px; color: #fff; font-weight: bold; text-align: right; background: #000000;' autocomplete='off' >
									 </div>
								</div>
								
								<div class='text-right'>
									<label class='col-sm-6 control-label'>DISKON (% & Nominal)</label>        		
									 <div class='col-sm-2'>
										<input type='text' name='diskon' id='diskon' value='' class='form-control'  style='font-size: 18px; color: #000000; font-weight: bold; text-align: right;' autocomplete='off'>
									 </div>
									 <div class='col-sm-4'>
										<input type='text' name='diskon2' id='diskon2' value='' class='form-control'  style='font-size: 18px; color: #000000; font-weight: bold; text-align: right;' autocomplete='off'>
									 </div>
								</div>	
								
								<div class='text-right'>
									<label class='col-sm-6 control-label'>JUMLAH BAYAR</label>        		
									 <div class='col-sm-6'>
										<input type='text' name='dp_bayar' id='dp_bayar' value='' class='form-control'  style='font-size: 18px; color: #000000; font-weight: bold; text-align: right;' autocomplete='off'>
									 </div>	 
								</div>
													
							
								
								
								<div class='text-right'>
									<label class='col-sm-6 control-label'>JENIS BAYAR</label>        		
									 <div class='col-sm-6'>
										<select name='id_carabayar' id='id_carabayar' class='form-control' style='font-size: 13px; color: #000000; font-weight: bold;'>";
											 if($stt_aksi == "input_trkasir"){
											 
												 $tampil = $db->prepare("SELECT * FROM carabayar ORDER BY id_carabayar ASC");
												 $tampil->execute();
												 while($rk = $tampil->fetch(PDO::FETCH_ASSOC)){
												 echo "<option value='$rk[id_carabayar]'>$rk[nm_carabayar]</option>";
												 }
												 
											 }else{
												
                                                 $datacarabayar = $db->prepare("SELECT * FROM carabayar where id_carabayar=?");
                                                 $datacarabayar->execute([$carabayar]);
                                                 $tampilcarabayar = $datacarabayar->fetch(PDO::FETCH_ASSOC);

												 echo"
												 <option value='$carabayar'>$tampilcarabayar[nm_carabayar]</option>
												 ";
												 $tampil = $db->prepare("SELECT * FROM carabayar ORDER BY id_carabayar ASC");
												 $tampil->execute();
												 while($rk = $tampil->fetch(PDO::FETCH_ASSOC)){
												    echo "<option value='$rk[id_carabayar]'>$rk[nm_carabayar]</option>";
												 }
											 
											 }
											 
										echo "
										</select>
									 </div>
							  </div>
								
								<div class='text-right'>
									<label class='col-sm-6 control-label'>KEMBALIAN</label>        		
									 <div class='col-sm-6'>
										<input type='text' name='sisa_bayar' id='sisa_bayar' class='form-control' style='font-size: 18px; color: #fff; font-weight: bold; text-align: right; background: #000000;' autocomplete='off' readonly>
									 </div>
								</div>
								
							</div>
						      
							  </div>";
							  ?>
<script>


$(document).ready(function () {
        HitungOngkirDanDP();
        hitungdiskon();
        hitungdiskon2();
        $("#example5").DataTable();
        
        $('#dp_bayar').mask('000.000.000', {reverse: true});
    });

		
		
		function validasiJumlahBayar() {
		    var ttl_trkasir = document.getElementById('ttl_trkasir').value;
		    ttl_trkasir = ttl_trkasir.replace(/\./g, "");
		    ttl_trkasir = parseInt(ttl_trkasir || 0, 10);

		    var dp_bayar = document.getElementById('dp_bayar').value;
		    dp_bayar = dp_bayar.replace(/\./g, "");
		    dp_bayar = parseInt(dp_bayar || 0, 10);

		    if (dp_bayar < ttl_trkasir) {
		        alert(' Jumlah bayar lebih kecil dari Total Bayar..');
		    }
		}

		// hitung otomatis saat mengetik jumlah bayar
		$('#dp_bayar').on('input keyup change', function() {
		    HitungOngkirDanDP();
		});

		// tetap support Enter untuk validasi
		$('#dp_bayar').keydown(function(e) {
	    		if (e.which == 13) {
	    			e.preventDefault();
	    			validasiJumlahBayar();
	    			HitungOngkirDanDP();
	    		}
		});
		
		

		//hitung diskon
		$('#diskon').keydown(function(e) {
		if (e.which == 13) { // e.which == 13 merupakan kode yang mendeteksi ketika anda   // menekan tombol enter di keyboard
			//letakan fungsi anda disini

			hitungdiskon();

		}
		});
		
		//hitung diskon
		$('#diskon2').keydown(function(e) {
		if (e.which == 13) { // e.which == 13 merupakan kode yang mendeteksi ketika anda   // menekan tombol enter di keyboard
			//letakan fungsi anda disini

			hitungdiskon2();

		}
		});
		
		//rubah format rupiah
			function formatRupiah(angka){
			 var reverse = angka.toString().split('').reverse().join(''),
			 ribuan = reverse.match(/\d{1,3}/g);
			 ribuan = ribuan.join('.').split('').reverse().join('');
			 return ribuan;
			}

			
			
	function HitungOngkirDanDP(){
	
			var ttl_trkasir = document.getElementById('ttl_trkasir').value;
			var dp_bayar = document.getElementById('dp_bayar').value;
			dp_bayar = dp_bayar.replace(/\./g, "");
			
			if(ttl_trkasir == ""){
			    var ttl_trkasir = "0";
			}
			
			if(dp_bayar == ""){
			    var dp_bayar = "0";
			}
			
			
					// 	var res1 = ttl_trkasir.replace(".", "");
				// 	var res3 = dp_bayar.replace(".", "");
					var res3 = ttl_trkasir.replace(/\./g, "");

				// 	var res1x = res1.replace(".", "");
				// 	var res3x = res3.replace(".", "");
					var res3x = dp_bayar.replace(/\./g, "");
					
// 			var total2 = parseInt(res1x) - parseInt(res3x);
			var total2 = parseInt(res3x) - parseInt(res3);
			
			document.getElementById("dp_bayar").value = formatRupiah(dp_bayar);
			document.getElementById("sisa_bayar").value = formatRupiah(total2);
// 			document.getElementById("sisa_bayar").value = total2;
	
	}

	function hitungdiskon(){

        var ttl_trkasir = document.getElementById('ttl_trkasir').value;
        var diskon = document.getElementById('diskon').value;

        if(diskon == ""){
            var diskon = "0";
        }else{
        }

        var res1 = ttl_trkasir.replace(".", "");
        var res4 = diskon.replace(".", "");

        var res1x = res1.replace(".", "");
        var res4x = res4.replace(".", "");

        var total5 = parseInt(res1x) * ( 1- (parseInt(res4x)/100));

        document.getElementById("diskon").value = formatRupiah(diskon);
        document.getElementById("ttl_trkasir").value = formatRupiah(total5);


	}

	function hitungdiskon2(){

        var ttl_trkasir = document.getElementById('ttl_trkasir').value;
        var diskon2 = document.getElementById('diskon2').value;

        if(diskon2 == ""){
            var diskon2 = "0";
        }else{
        }

        var res1 = ttl_trkasir.replace(".", "");
        var res2 = diskon2.replace(".", "");

        var res1x = res1.replace(".", "");
        var res2x = res2.replace(".", "");

        var total6 = parseInt(res1x) - parseInt(res2x);

        document.getElementById("diskon2").value = formatRupiah(diskon2);
        document.getElementById("ttl_trkasir").value = formatRupiah(total6);


	}

	function simpanInlineDetail(row) {
		var id_dtrkasir = row.data('id_dtrkasir');
		var qty = row.find('.inline-qty').val();
		var resep = row.find('.inline-resep').val();
		var statusEl = row.find('.inline-status');

		if (qty == '' || parseInt(qty) < 1) {
			statusEl.removeClass('text-success text-muted').addClass('text-danger').text('Qty minimal 1');
			alert('Qty minimal 1');
			return;
		}

		statusEl.removeClass('text-success text-danger').addClass('text-muted').text('Menyimpan...');

		$.ajax({
			type: 'post',
			url: 'modul/mod_trkasir/update_detail_inline.php',
			dataType: 'json',
			data: {
				id_dtrkasir: id_dtrkasir,
				qty_dtrkasir: qty,
				resep: resep
			},
			success: function(response) {
				if (response && response.status == 'success') {
					statusEl.removeClass('text-danger text-muted').addClass('text-success').text('Tersimpan');
					tabel_detail();
				} else {
					statusEl.removeClass('text-success text-muted').addClass('text-danger').text('Gagal simpan');
					alert(response.message ? response.message : 'Gagal update detail');
				}
			},
			error: function() {
				statusEl.removeClass('text-success text-muted').addClass('text-danger').text('Gagal simpan');
				alert('Gagal update detail');
			}
		});
	}

	var inlineSaveTimers = {};
	function scheduleInlineSave(row, delayMs) {
		var id_dtrkasir = row.data('id_dtrkasir');
		if (!id_dtrkasir) {
			return;
		}

		if (inlineSaveTimers[id_dtrkasir]) {
			clearTimeout(inlineSaveTimers[id_dtrkasir]);
		}

		inlineSaveTimers[id_dtrkasir] = setTimeout(function() {
			simpanInlineDetail(row);
			delete inlineSaveTimers[id_dtrkasir];
		}, delayMs || 400);
	}

	$(document).on('change', '.inline-resep', function() {
		scheduleInlineSave($(this).closest('tr'), 350);
	});

	$(document).on('blur', '.inline-qty', function() {
		scheduleInlineSave($(this).closest('tr'), 350);
	});

	$(document).on('keydown', '.inline-qty', function(e) {
		if (e.which == 13) {
			e.preventDefault();
			scheduleInlineSave($(this).closest('tr'), 0);
		}
	});
	
</script>
