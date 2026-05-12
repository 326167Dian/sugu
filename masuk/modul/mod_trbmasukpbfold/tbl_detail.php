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
                <th style="vertical-align: middle; background-color: #008000; text-align: right; ">Qty</th>
                <th style="vertical-align: middle; background-color: #008000; text-align: center; ">Satuan</th>
                <th style="vertical-align: middle; background-color: #008000; text-align: center; ">No. Batch</th>
                <th style="vertical-align: middle; background-color: #008000; text-align: center; ">Exp. Date</th>
                <th style="vertical-align: middle; background-color: #008000; text-align: right; ">HNA</th>
                <th style="vertical-align: middle; background-color: #008000; text-align: right; ">Disc</th>
                <th style="vertical-align: middle; background-color: #008000; text-align: right; ">HNA+Disc</th>
                <th style="vertical-align: middle; background-color: #008000; text-align: right; ">Total</th>
                <th style="vertical-align: middle; background-color: #008000; text-align: center; ">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            include "../../../configurasi/koneksi.php";
            include "../../../configurasi/fungsi_rupiah.php";
            include "../../../configurasi/fungsi_indotgl.php";

            $kd_trbmasuk = isset($_POST['kd_trbmasuk'])? $_POST['kd_trbmasuk'] : '';

            //AMBIL DATA UNTUK FOOTER
            $dfoot = $db->prepare("SELECT * FROM trbmasuk WHERE kd_trbmasuk=?");
            $dfoot->execute([$kd_trbmasuk]);
            $rf = $dfoot->fetch(PDO::FETCH_ASSOC);
            /**$dp_bayar = format_rupiah($rf['dp_bayar']);
        $carabayar = $rf['carabayar'];**/


            $sumprice = $db->prepare("SELECT kd_trbmasuk, SUM(hrgttl_dtrbmasuk) as grandnya FROM trbmasuk_detail 
							            WHERE kd_trbmasuk=?");
			$sumprice->execute([$kd_trbmasuk]);
            $ttlprice = $sumprice->fetch(PDO::FETCH_ASSOC);
            // $grandnya = format_rupiah($ttlprice['grandnya'] * 1.11);
            $grandnya = format_rupiah($ttlprice['grandnya']);
            
            $sumprice1 = $db->prepare("SELECT ttl_trbmasuk,dp_bayar,sisa_bayar FROM trbmasuk 
                                        WHERE kd_trbmasuk=?");
            $sumprice1->execute([$kd_trbmasuk]);
            $ttlprice1 = $sumprice1->fetch(PDO::FETCH_ASSOC);
            // $grandnya  = format_rupiah($ttlprice1['ttl_trbmasuk']);
            $granddp   = format_rupiah($ttlprice1['dp_bayar']);

            $noreq = $db->prepare("SELECT * FROM trbmasuk_detail 
    							   WHERE kd_trbmasuk=?
    							   ORDER BY id_dtrbmasuk ASC");
    		$noreq->execute([$kd_trbmasuk]);
            $no = 1;
            $totalharga = array();
            while ($r = $noreq->fetch(PDO::FETCH_ASSOC)) {
                $brg = $db->prepare("SELECT hrgjual_barang FROM barang WHERE kd_barang =?");
                $brg->execute([$r['kd_barang']]);
				$rbrg = $brg->fetch(PDO::FETCH_ASSOC);
				$hrgjual = ($r['hrgjual_dtrbmasuk']==0)?$rbrg['hrgjual_barang']:$r['hrgjual_dtrbmasuk'];
				
                $hrgsat_dtrbmasuk = format_rupiah($r['hrgsat_dtrbmasuk']);
                $hrgjual_dtrbmasuk = format_rupiah($r['hrgjual_dtrbmasuk']);
                $hnasat_dtrbmasuk = format_rupiah($r['hnasat_dtrbmasuk']);
                // $hrgttl_dtrbmasuk = format_rupiah($r['hrgttl_dtrbmasuk'] /1.11);
                $hnadisc = $r['hnasat_dtrbmasuk'] * (1 - ($r['diskon'] / 100));
                $hnadisc1 = format_rupiah($hnadisc);
                // $totalharga[] = round($hnadisc * $r['qty_grosir']);
                $hrgttl_dtrbmasuk = round($hnadisc * $r['qty_grosir']);
                $hrgttl_dtrbmasukrp = format_rupiah($hrgttl_dtrbmasuk);
                $totalharga[] = $hrgttl_dtrbmasuk;
                echo "<tr style='font-size: 13px;'>
											<td align=center>$no</td>           
											 <td align=left>$r[kd_barang]</td>
											 <td>$r[nmbrg_dtrbmasuk]</td>
											 <td align=right>$r[qty_dtrbmasuk]</td>
											 <td align=center>$r[sat_dtrbmasuk]</td>
											 <td align=center>$r[no_batch]</td>
											 <td align=center>".tgl_indo($r['exp_date'])."</td>
											 <td align=right>$hnasat_dtrbmasuk</td>
											 <td align=right>$r[diskon]</td>
											 <td align=right>$hnadisc1</td>											
											 <td align=right>$hrgttl_dtrbmasukrp</td>
											 <td align=center>
											 <button class='btn btn-xs btn-danger' id='hapusdetail' 
												 data-id_dtrbmasuk='$r[id_dtrbmasuk]'>
												 <i class='glyphicon glyphicon-remove'></i>
												 </button>
												
											</td>
										</tr>";

                $no++;
            }

            $grandtotal = array_sum($totalharga);
            $tampiltotalharga = format_rupiah($grandtotal);
            echo "</tbody>
                        <tr>
                            <td colspan='7'><h4><center>Total Harga </center></h4>  </td>
                            <td colspan='3'><h4><strong> Rp. $tampiltotalharga  ,- </strong></h4></td> 
                        </tr>
</table>
						
							<p>
						<legend class='scheduler-border'></legend>
							<div class='col-md-6'>	
							
							</div>
							
							
							<div class='col-lg-6'>	
								
								<div class='text-right'>
									<label class='col-sm-6 control-label'>Total Harga + PPN</label>        		
									 <div class='col-sm-6'>
										<input type='text' name='ttl_trkasir' id='ttl_trkasir' value='$grandnya' class='form-control input-validation-error' style='font-size: 18px; color: #fff; font-weight: bold; text-align: right; background: #000000;' autocomplete='off' readonly>
										<input type='hidden' name='nilai_batch' id='nilai_batch' value='1'>
									 </div>
								</div>
								
								<div class='text-right'>
									<label class='col-sm-6 control-label'>DISKON % & Nominal</label>        		
									<div class='col-sm-6'>
    									 <div class='btn-group btn-group-justified' role='group' aria-label='...'>
                                            <div class='btn-group' role='group'>
                                                <input type='text' name='diskon2' id='diskon2' value='' class='form-control'  style='font-size: 18px; color: #000000; font-weight: bold; text-align: right;' autocomplete='off'>
                                            </div>
                                            <div class='btn-group' role='group'>
                                                <input type='text' name='dp_bayar' id='dp_bayar' value='$granddp' class='form-control'  style='font-size: 18px; color: #000000; font-weight: bold; text-align: right;' autocomplete='off'>
                                            </div>
                                            <div class='btn-group' role='group'>
                                                <button type='button' class='btn btn-primary' id='diskon_enter'>Enter</button>
                                              </div>
                                        </div>
                                    </div>
									
								</div>
								
								<div class='text-right'>
									<label class='col-sm-6 control-label'>Total Tagihan</label>        		
									 <div class='col-sm-6'>
										<input type='text' name='sisa_bayar' id='sisa_bayar' class='form-control' style='font-size: 18px; color: #fff; font-weight: bold; text-align: right; background: #000000;' autocomplete='off' readonly>
									 </div>
								</div>
								
								<div class='text-right'>
									<label class='col-sm-6 control-label'>CARA BAYAR</label>        		
									 <div class='col-sm-6'>
										<select name='carabayar' id='carabayar' class='form-control' 
										style='font-size: 13px; color: #000000; font-weight: bold;'>
										    <option value='KREDIT'>KREDIT</option>
										    <option value='LUNAS'>TUNAI</option>                                            
                                            <option value='KONSINYASI'>KONSINYASI</option>
                                         </select>  
										
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

                    var total2 = parseInt(res1x) - parseInt(res2x);

                    document.getElementById("dp_bayar").value = formatRupiah(dp_bayar);
                    document.getElementById("sisa_bayar").value = formatRupiah(total2);

                }
                //hitung diskon2
                $('#diskon2').keydown(function(e) {
                    if (e.which == 13) { // e.which == 13 merupakan kode yang mendeteksi ketika anda   // menekan tombol enter di keyboard
                        //letakan fungsi anda disini

                        hitungdiskon();

                    }
                });

                function hitungdiskon() {

                    var sisa_bayar = document.getElementById('sisa_bayar').value;
                    var diskon2 = document.getElementById('diskon2').value;

                    if (diskon2 == "") {
                        var diskon2 = "0";
                    } else {}

                    var res1 = sisa_bayar.replace(".", "");
                    var res4 = diskon2.replace(".", "");

                    var res1x = res1.replace(".", "");
                    var res4x = res4.replace(".", "");

                    var total5 = Math.ceil(parseInt(res1x) * (1 - (parseInt(res4x) / 100)));

                    document.getElementById("diskon2").value = formatRupiah(diskon2);
                    document.getElementById("sisa_bayar").value = formatRupiah(total5);


                }
                
                $('#diskon_enter').on('click', function(){
        		    let diskon = $('#dp_bayar').val();
        		    let diskon2 = $('#diskon2').val();
        		    
        		    if(diskon > 0 && diskon2 == 0){
            		    HitungDP();
            		    $('#dp_bayar').attr('disabled', true);
                        $('#diskon2').attr('disabled', true);
        		    } else if(diskon == 0 && diskon2 > 0){
        		        hitungdiskon();
        		        $('#dp_bayar').attr('disabled', true);
                        $('#diskon2').attr('disabled', true);
        		    } else {
        		        alert('Hanya dibolehkan 1 opsi diskon !!!')
        		    }
        		})
        		
            </script>