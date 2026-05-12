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
                <th style="vertical-align: middle; background-color: #008000; text-align: right; ">Qty Grosir</th>
                <th style="vertical-align: middle; background-color: #008000; text-align: center; ">Satuan Grosir</th>
                <th style="vertical-align: middle; background-color: #008000; text-align: center; ">No. Batch</th>
                <th style="vertical-align: middle; background-color: #008000; text-align: center; ">Exp. Date</th>
                <th style="vertical-align: middle; background-color: #008000; text-align: center; ">HNA</th>
                <th style="vertical-align: middle; background-color: #008000; text-align: right; ">Disc(%)</th>
                <th style="vertical-align: middle; background-color: #008000; text-align: right; ">HNA+Disc</th>
                <th style="vertical-align: middle; background-color: #008000; text-align: right; ">Hrg Jual Satuan</th>
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
            $stmt = $db->prepare("SELECT * FROM orders WHERE kd_trbmasuk=?");
            $stmt->execute([$kd_trbmasuk]);
            $rf = $stmt->fetch(PDO::FETCH_ASSOC);
            /**$dp_bayar = format_rupiah($rf['dp_bayar']);
        $carabayar = $rf['carabayar'];**/

            
            $stmt_trb = $db->prepare("SELECT * FROM trbmasuk_detail
                                                WHERE kd_orders =? ORDER BY id_dtrbmasuk ASC");
            $stmt_trb->execute([$kd_trbmasuk]);
            $ctrb = $stmt_trb->rowCount();
            $totalharga1= 0;
            if ($ctrb > 0) {
                while($trb = $stmt_trb->fetch(PDO::FETCH_ASSOC)){
                        
                        $stmt_brm = $db->prepare("SELECT * FROM barang WHERE kd_barang=?");
                        $stmt_brm->execute([$trb['kd_barang']]);
                        $brm = $stmt_brm->fetch(PDO::FETCH_ASSOC);
                        
                        $stmt_sum2 = $db->prepare("SELECT kd_trbmasuk, SUM(hrgttl_dtrbmasuk) as grandnya FROM trbmasuk_detail 
    							WHERE kd_orders=?");
                        $stmt_sum2->execute([$kd_trbmasuk]);
                        $ttlprice = $stmt_sum2->fetch(PDO::FETCH_ASSOC);
                        // $grandnya = format_rupiah($ttlprice['grandnya'] * 1.11);
                        $grandnya = format_rupiah($ttlprice['grandnya']);
                        // $grandnya += ($r['hnasat_dtrbmasuk'] * $r['qtygrosir_dtrbmasuk']) * (1-($r['diskon']/100)) * 1.11; 
            
            
                        // $hrgttl_dtrbmasuk1  = format_rupiah($trb['hrgttl_dtrbmasuk'] /1.11);
                        // $hrgttl_dtrbmasuk1  = format_rupiah(($trb['hnasat_dtrbmasuk'] * (1-($trb['diskon']/100)) * ($trb['qty_grosir'])));
                        $hrgttl_dtrbmasuk1  = format_rupiah(($trb['hnasat_dtrbmasuk'] * (1-($trb['diskon']/100)) * ($trb['qty_grosir'])));
                        // $hrgttl_dtrbmasuk1  = format_rupiah($trb['hrgttl_dtrbmasuk'] );
                        $hnadisc11          = $trb['hnasat_dtrbmasuk'] * (1 - ($trb['diskon'] / 100));
                        $hnadisc12          = format_rupiah($hnadisc11);
                        $hnasat_dtrbmasuk   = format_rupiah($trb['hnasat_dtrbmasuk']);
                        $hrgjual_dtrbmasuk  = format_rupiah($trb['hrgjual_dtrbmasuk']);
                        // $totalharga[]       = $trb['hrgttl_dtrbmasuk'];
                        // $totalharga[]       = ($trb['hnasat_dtrbmasuk'] * $trb['qty_grosir']) * (1-($trb['diskon']/100));
                        // $totalharga[]       = round($brm['hna'] * (1-($trb['diskon']/100)) * ($trb['qty_grosir']));
                        // $totalharga[]       = round(($trb['hnasat_dtrbmasuk'] * (1-($trb['diskon']/100)) * ($trb['qty_grosir'])));
                        $totalharga       = round(($trb['hnasat_dtrbmasuk'] * (1-($trb['diskon']/100)) * ($trb['qty_grosir'])));
                        $totalharga1      = $totalharga1 + $totalharga;
                        $nilai_batch = ($trb['no_batch'] != "")?($nilai_batch*1):($nilai_batch*0);
                        
                        echo "<tr style='font-size: 13px;'>
											<td align='center'>$no</td>           
											 <td align='left'>$trb[kd_barang]</td>
											 <td>$trb[nmbrg_dtrbmasuk]</td>
											 <td align='right'>
											    <input type='number' id='dqtygrosir_dtrbmasuk' value='$trb[qty_grosir]' style='width: 50px; text-align: center'
											    data-id_dtrbmasuk           = '$trb[id_dtrbmasuk]'
											    data-kd_barang              = '$trb[kd_barang]'
											    >
											
											 </td>
											 <td align='center'>$trb[satgrosir_dtrbmasuk]</td>
											 <td align='center'>
											    <input type='text' id='dno_batch' value='$trb[no_batch]' style='width: 100px'
											    data-id_dtrbmasuk           = '$trb[id_dtrbmasuk]'
											    data-kd_barang              = '$trb[kd_barang]'
											    data-qtygrosir_dtrbmasuk    = '$trb[qty_grosir]'
											    >
											    
											 </td>
											 <td align='center'>
											    <input type='date' id='dexp_date' value='$trb[exp_date]'
											    data-id_dtrbmasuk           = '$trb[id_dtrbmasuk]'
											    data-kd_barang              = '$trb[kd_barang]'
											    data-qtygrosir_dtrbmasuk    = '$trb[qty_grosir]'
											    >
											    
											 </td>
											 <td align='right'>
											    <input type='text' id='dhnasat_dtrbmasuk' value='$hnasat_dtrbmasuk' style='width: 100px'
											    data-id_dtrbmasuk           = '$trb[id_dtrbmasuk]'
											    data-kd_barang              = '$trb[kd_barang]'
											    data-qtygrosir_dtrbmasuk    = '$trb[qty_grosir]'
											    > 
											 
											 </td>
											 <td align='right'>
											    <input type='text' id='ddiskon' value='$trb[diskon]' style='width: 50px'
											    data-id_dtrbmasuk           = '$trb[id_dtrbmasuk]'
											    data-kd_barang              = '$trb[kd_barang]'
											    data-qtygrosir_dtrbmasuk    = '$trb[qty_grosir]'
											    >
											 
											 </td>
											 <td align='right'>$hnadisc12</td>											
											 <td align='right'>
											    <input type='text' id='dhrgjual_dtrbmasuk' value='$hrgjual_dtrbmasuk' style='width: 100px'
											    data-id_dtrbmasuk           = '$trb[id_dtrbmasuk]'
											    data-kd_barang              = '$trb[kd_barang]'
											    data-qtygrosir_dtrbmasuk    = '$trb[qty_grosir]'
											    >
											 </td>											
											 						
											 <td align='right'>$hrgttl_dtrbmasuk1</td>
											 <td align='center'>
    											 <button class='btn btn-xs btn-danger' id='hapusorder' 
    												 data-id_dtrbmasuk='$trb[id_dtrbmasuk]'>
    												 <i class='glyphicon glyphicon-remove'></i>
    											</button>
												
											</td>
										</tr>";
										
                        $no++;
                    }
            }
            
            $stmt = $db->prepare("SELECT * FROM ordersdetail 
							   WHERE kd_trbmasuk=?
							   AND masuk = '1'
							   ORDER BY id_dtrbmasuk ASC");
            $stmt->execute([$kd_trbmasuk]);
            $no = 1;
            $total = 0;
            $totalharga = array();
            $grandnya = 0;
            $nilai_batch = 1;
            
            $cord = $stmt->rowCount();
            if ($cord > 0) {
                while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    
                    $stmt_brg = $db->prepare("SELECT * FROM barang WHERE kd_barang=?");
                    $stmt_brg->execute([$r['kd_barang']]);
                    $bro = $stmt_brg->fetch(PDO::FETCH_ASSOC);
                        
                    $stmt_sum = $db->prepare("SELECT kd_trbmasuk, SUM(hrgttl_dtrbmasuk) as grandnya FROM ordersdetail 
    							WHERE kd_trbmasuk=?");
                    $stmt_sum->execute([$kd_trbmasuk]);
                    $ttlprice = $stmt_sum->fetch(PDO::FETCH_ASSOC);
                    $grandnya = format_rupiah($ttlprice['grandnya'] * 1.11);
                        
                    $stmt_brg2 = $db->prepare("SELECT * FROM barang WHERE kd_barang =?");
        			$stmt_brg2->execute([$r['kd_barang']]);
        			$rbrg = $stmt_brg2->fetch(PDO::FETCH_ASSOC);
        			$hrgjual = ($r['hrgjual_dtrbmasuk']==0)?$rbrg['hrgjual_barang']:$r['hrgjual_dtrbmasuk'];
        				
                    $hrgsat_dtrbmasuk = format_rupiah($r['hrgsat_dtrbmasuk']);
                    $hrgjual_dtrbmasuk = format_rupiah($rbrg['hrgjual_barang']);
                    $hnasat_dtrbmasuk = format_rupiah($rbrg['hna']);
                    // $hrgttl_dtrbmasuk = format_rupiah(round(($bro['hna'] * 1-($r['diskon']/100) * ($r['qtygrosir_dtrbmasuk']))));
                    $hrgttl_dtrbmasuk = ($bro['hna'] == 0)?format_rupiah(round($r['hrgttl_dtrbmasuk'])):format_rupiah(round(($bro['hna'] * 1-($r['diskon']/100) * ($r['qtygrosir_dtrbmasuk']))));
                    $hnadisc = $rbrg['hna'] * (1 - ($r['diskon'] / 100));
                    $hnadisc1 = format_rupiah($hnadisc);
                    // $totalharga[] = round(($bro['hna'] * 1-($r['diskon']/100) * ($r['qtygrosir_dtrbmasuk'])));
                    // $totalharga       = round(($bro['hna'] * 1-($r['diskon']/100) * ($r['qtygrosir_dtrbmasuk'])));
                    $totalharga       = ($bro['hna'] == 0)?(round($r['hrgttl_dtrbmasuk'])):(round(($bro['hna'] * 1-($r['diskon']/100) * ($r['qtygrosir_dtrbmasuk']))));
                    // $totalharga1      = $totalharga1 + $totalharga;
                        
                    $no_batch = $r['no_batch'];
                    $nilai_batch = ($r['no_batch'] != "")?($nilai_batch*1):($nilai_batch*0);
                        
                    echo "<tr style='font-size: 13px;'>
    						<td align='center'>$no</td>       
    						<td align='left'>$r[kd_barang]</td>
    						<td>$r[nmbrg_dtrbmasuk]</td>
    						<td align='right'>
    						    <input type='number' id='dqtygrosir_dtrbmasuk' value='$r[qtygrosir_dtrbmasuk]' style='width: 50px; text-align: center'
    							    data-id_dtrbmasuk           = '$r[id_dtrbmasuk]'
    								data-kd_barang              = '$r[kd_barang]'
    								>
    						</td>
    						<td align='center'>$r[satgrosir_dtrbmasuk]</td>
    						<td align='center'>
    							<input type='text' id='dno_batch' value='$no_batch' style='width: 100px' required
    							    data-id_dtrbmasuk           = '$r[id_dtrbmasuk]'
    								data-kd_barang              = '$r[kd_barang]'
    								data-qtygrosir_dtrbmasuk    = '$r[qtygrosir_dtrbmasuk]'
    								>
    											    
    						</td>
    						<td align='center'>
    							<input type='date' id='dexp_date' value='$r[exp_date]' required
    							data-id_dtrbmasuk           = '$r[id_dtrbmasuk]'
    							data-kd_barang              = '$r[kd_barang]'
    							data-qtygrosir_dtrbmasuk    = '$r[qtygrosir_dtrbmasuk]'
    							>
    											    
    						</td>
    						<td align='right'>
    							<input type='text' id='dhnasat_dtrbmasuk' value='$hnasat_dtrbmasuk' style='width: 100px'
    							data-id_dtrbmasuk           = '$r[id_dtrbmasuk]'
    							data-kd_barang              = '$r[kd_barang]'
    							data-qtygrosir_dtrbmasuk    = '$r[qtygrosir_dtrbmasuk]'
    							> 
    											 
    						</td>
    						<td align='right'>
    							<input type='number' id='ddiskon' value='$r[diskon]' style='width: 50px'
    							data-id_dtrbmasuk           = '$r[id_dtrbmasuk]'
    							data-kd_barang              = '$r[kd_barang]'
    							data-qtygrosir_dtrbmasuk    = '$r[qtygrosir_dtrbmasuk]'
    							>
    											 
    						</td>
    						<td align='right'>$hnadisc1</td>											
    						<td align='right'>
    							<input type='text' id='dhrgjual_dtrbmasuk' value='$hrgjual_dtrbmasuk' style='width: 100px'
    							data-id_dtrbmasuk           ='$r[id_dtrbmasuk]'
    							data-kd_barang              = '$r[kd_barang]'
    							data-qtygrosir_dtrbmasuk    = '$r[qtygrosir_dtrbmasuk]'
    						    >
    						</td>											
    											 						
    						<td align='right'>$hrgttl_dtrbmasuk</td>
    						<td align='center'>
        						<button class='btn btn-xs btn-danger' id='hapusorder' 
        							data-id_dtrbmasuk='$r[id_dtrbmasuk]'>
        						    <i class='glyphicon glyphicon-remove'></i>
        					    </button>
    												
    						</td>
    					</tr>";
    										
                        $no++;
                }
            }
            
            
            
            // $grandtotal = array_sum($totalharga);
            $grandtotal = $totalharga1;
            $tampiltotalharga = format_rupiah($grandtotal);
            // $tampiltotalharga = format_rupiah($total);
            // $grandnya = format_rupiah($grandnya);
            $grandnya = format_rupiah($grandtotal * 1.11);
            
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
										<input type='text' name='ttl_trkasir' id='ttl_trkasir' value='$grandnya' class='form-control input-validation-error' style='font-size: 18px; color: #fff; font-weight: bold; text-align: right; background: #000000;' autocomplete='off'>
										<input type='hidden' name='nilai_batch' id='nilai_batch' value='$nilai_batch'>
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
                                                <input type='text' name='dp_bayar' id='dp_bayar' value='' class='form-control'  style='font-size: 18px; color: #000000; font-weight: bold; text-align: right;' autocomplete='off'>
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
										<input type='text' name='sisa_bayar' id='sisa_bayar' class='form-control' style='font-size: 18px; color: #fff; font-weight: bold; text-align: right; background: #000000;' autocomplete='off'>
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
                    var table = $("#example5").DataTable()
                    
                    $('#example5 tbody').on('change', '#dqtygrosir_dtrbmasuk', function () {
            		    var id_dtrbmasuk        = $(this).data('id_dtrbmasuk');
            		    var kd_barang           = $(this).data('kd_barang');
            		    var qtygrosir_dtrbmasuk = $(this).val();
            		    var kd_orders           = $('#kd_trbmasuk').val();
            		    var kd_trbmasuk         = $('#kd_trbmasuk1').val();
            		    		
                        $.ajax({
                            url: 'modul/mod_trbmasukpbf/simpandetail_qtygrosir.php',
                            type: 'post',
                            data: {
                                'id_dtrbmasuk'          : id_dtrbmasuk,
                                'kd_trbmasuk'           : kd_trbmasuk,
                                'kd_orders'             : kd_orders,
                                'kd_barang'             : kd_barang,
                                'qtygrosir_dtrbmasuk'   : qtygrosir_dtrbmasuk,
                                
                            },
                            success: function (data) {
                                tabel_detail1();
                            }
                
                        });
                    });
            		
            		$('#example5 tbody').on('change', '#dhnasat_dtrbmasuk', function () {
            		    var id_dtrbmasuk        = $(this).data('id_dtrbmasuk');
            		    var kd_barang           = $(this).data('kd_barang');
            		    var qtygrosir_dtrbmasuk = $(this).data('qtygrosir_dtrbmasuk');
            		    var hnasat_dtrbmasuk    = $(this).val();
            		    var kd_orders           = $('#kd_trbmasuk').val();
            		    var kd_trbmasuk         = $('#kd_trbmasuk1').val();
            		    		
                        $.ajax({
                            url: 'modul/mod_trbmasukpbf/simpandetail_hna.php',
                            type: 'post',
                            data: {
                                'id_dtrbmasuk'          : id_dtrbmasuk,
                                'kd_trbmasuk'           : kd_trbmasuk,
                                'kd_orders'             : kd_orders,
                                'kd_barang'             : kd_barang,
                                'hnasat_dtrbmasuk'      : hnasat_dtrbmasuk,
                                'qtygrosir_dtrbmasuk'   : qtygrosir_dtrbmasuk
                            },
                            success: function (data) {
                                tabel_detail1();
                            }
                
                        });
                    });
                    
                    $('#example5 tbody').on('change', '#dno_batch', function () {
            		    var id_dtrbmasuk        = $(this).data('id_dtrbmasuk');
            		    var kd_barang           = $(this).data('kd_barang');
            		    var no_batch            = $(this).val();
            		    var qtygrosir_dtrbmasuk = $(this).data('qtygrosir_dtrbmasuk');
            		    var kd_orders           = $('#kd_trbmasuk').val();
            		    var kd_trbmasuk         = $('#kd_trbmasuk1').val();
            		    
            		    $.ajax({
                            url: 'modul/mod_trbmasukpbf/simpandetail_batch.php',
                            type: 'post',
                            data: {
                                'id_dtrbmasuk'          : id_dtrbmasuk,
                                'kd_trbmasuk'           : kd_trbmasuk,
                                'kd_orders'             : kd_orders,
                                'kd_barang'             : kd_barang,
                                'no_batch'              : no_batch,
                                'qtygrosir_dtrbmasuk'   : qtygrosir_dtrbmasuk,
                            },
                            success: function (data) {
                                tabel_detail1();
                            }
                
                        });
                    });
                    
                    $('#example5 tbody').on('change', '#dexp_date', function () {
            		    var id_dtrbmasuk        = $(this).data('id_dtrbmasuk');
            		    var kd_barang           = $(this).data('kd_barang');
            		    var qtygrosir_dtrbmasuk = $(this).data('qtygrosir_dtrbmasuk');
            		    var exp_date            = $(this).val();
            		    var kd_orders           = $('#kd_trbmasuk').val();
            		    var kd_trbmasuk         = $('#kd_trbmasuk1').val();
            		    		
                        $.ajax({
                            url: 'modul/mod_trbmasukpbf/simpandetail_expdate.php',
                            type: 'post',
                            data: {
                                'id_dtrbmasuk'          : id_dtrbmasuk,
                                'kd_trbmasuk'           : kd_trbmasuk,
                                'kd_orders'             : kd_orders,
                                'kd_barang'             : kd_barang,
                                'exp_date'              : exp_date,
                                'qtygrosir_dtrbmasuk'   : qtygrosir_dtrbmasuk
                            },
                            success: function (data) {
                                tabel_detail1();
                            }
                
                        });
                    });
                    
                    $('#example5 tbody').on('change', '#dhrgjual_dtrbmasuk', function () {
            		    var id_dtrbmasuk        = $(this).data('id_dtrbmasuk');
            		    var kd_barang           = $(this).data('kd_barang');
            		    var qtygrosir_dtrbmasuk = $(this).data('qtygrosir_dtrbmasuk');
            		    var hrgjual_dtrbmasuk   = $(this).val();
            		    var kd_orders           = $('#kd_trbmasuk').val();
            		    var kd_trbmasuk         = $('#kd_trbmasuk1').val();
            		    		
                        $.ajax({
                            url: 'modul/mod_trbmasukpbf/simpandetail_hrgjual.php',
                            type: 'post',
                            data: {
                                'id_dtrbmasuk'          : id_dtrbmasuk,
                                'kd_trbmasuk'           : kd_trbmasuk,
                                'kd_orders'             : kd_orders,
                                'kd_barang'             : kd_barang,
                                'hrgjual_dtrbmasuk'     : hrgjual_dtrbmasuk,
                                'qtygrosir_dtrbmasuk'   : qtygrosir_dtrbmasuk
                            },
                            success: function (data) {
                                tabel_detail1();
                            }
                
                        });
                    });
                    
                    $('#example5 tbody').on('change', '#ddiskon', function () {
            		    var id_dtrbmasuk        = $(this).data('id_dtrbmasuk');
            		    var kd_barang           = $(this).data('kd_barang');
            		    var qtygrosir_dtrbmasuk = $(this).val();
            		    var diskon              = $(this).val();
            		    var kd_orders           = $('#kd_trbmasuk').val();
            		    var kd_trbmasuk         = $('#kd_trbmasuk1').val();
            		    		
                        $.ajax({
                            url: 'modul/mod_trbmasukpbf/simpandetail_diskon.php',
                            type: 'post',
                            data: {
                                'id_dtrbmasuk'          : id_dtrbmasuk,
                                'kd_trbmasuk'           : kd_trbmasuk,
                                'kd_orders'             : kd_orders,
                                'kd_barang'             : kd_barang,
                                'diskon'                : diskon,
                                'qtygrosir_dtrbmasuk'   : qtygrosir_dtrbmasuk,
                            },
                            success: function (data) {
                                tabel_detail1();
                            }
                
                        });
                    });
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