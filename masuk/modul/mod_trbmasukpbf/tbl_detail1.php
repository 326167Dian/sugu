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
                <th style="vertical-align: middle; background-color: #008000; text-align: center; ">Konversi</th>
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
            include "../../../configurasi/fungsi_perubahan_trbmasuk.php";
            pastikan_kolom_tipe_barang_trbmasuk($db);

            $kd_trbmasuk = isset($_POST['kd_trbmasuk'])? $_POST['kd_trbmasuk'] : '';

            //AMBIL DATA UNTUK FOOTER
            $stmt = $db->prepare("SELECT * FROM orders WHERE kd_trbmasuk=?");
            $stmt->execute([$kd_trbmasuk]);
            $rf = $stmt->fetch(PDO::FETCH_ASSOC);
            /**$dp_bayar = format_rupiah($rf['dp_bayar']);
        $carabayar = $rf['carabayar'];**/

            
            // dibatasi jenis='pbf' supaya item yang sudah diterima lewat modul trbmasuk (non PBF) tidak ikut tampil/bisa diedit di sini.
            // LEFT JOIN (bukan JOIN) karena baris header `trbmasuk` baru dibuat saat SIMPAN TRANSAKSI --
            // selama masih draft (item sudah dipindah ke trbmasuk_detail tapi transaksi belum final-submit),
            // header itu belum ada sama sekali. Kalau pakai INNER JOIN, item yang baru saja diterima
            // langsung hilang dari tabel & tidak ikut kehitung di Total Harga selama masih draft.
            $stmt_trb = $db->prepare("SELECT trbmasuk_detail.* FROM trbmasuk_detail
                                        LEFT JOIN trbmasuk ON trbmasuk.kd_trbmasuk = trbmasuk_detail.kd_trbmasuk
                                        WHERE trbmasuk_detail.kd_orders = ?
                                        AND (trbmasuk.jenis = 'pbf' OR trbmasuk.kd_trbmasuk IS NULL)
                                        ORDER BY trbmasuk_detail.nmbrg_dtrbmasuk ASC");
            $stmt_trb->execute([$kd_trbmasuk]);
            $ctrb = $stmt_trb->rowCount();
            $no = 1;
            $total = 0;
            $totalharga = array();
            $grandnya = 0;
            $nilai_batch = 1;
            
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
											    data-no_batch               = '$trb[no_batch]'
											    >

											 </td>
											 <td align='center'>$trb[satgrosir_dtrbmasuk]</td>
											 <td align='center'>
											    <input type='number' id='dkonversi' value='$trb[konversi]' style='width: 60px; text-align: center'
											    data-id_dtrbmasuk           = '$trb[id_dtrbmasuk]'
											    data-kd_barang              = '$trb[kd_barang]'
											    data-qtygrosir_dtrbmasuk    = '$trb[qty_grosir]'
											    data-no_batch               = '$trb[no_batch]'
											    >
											 </td>
											 <td align='center'>
											    <input type='text' id='dno_batch' value='$trb[no_batch]' style='width: 100px'
											    data-id_dtrbmasuk           = '$trb[id_dtrbmasuk]'
											    data-kd_barang              = '$trb[kd_barang]'
											    data-qtygrosir_dtrbmasuk    = '$trb[qty_grosir]'
											    >

											 </td>
											 <td align='center'>
											    <input type='text' class='datepicker' id='dexp_date' value='$trb[exp_date]'
											    data-id_dtrbmasuk           = '$trb[id_dtrbmasuk]'
											    data-kd_barang              = '$trb[kd_barang]'
											    data-qtygrosir_dtrbmasuk    = '$trb[qty_grosir]'
											    data-no_batch               = '$trb[no_batch]'
											    >

											 </td>
											 <td align='right'>
											    <input type='text' id='dhnasat_dtrbmasuk' value='$hnasat_dtrbmasuk' style='width: 100px'
											    data-id_dtrbmasuk           = '$trb[id_dtrbmasuk]'
											    data-kd_barang              = '$trb[kd_barang]'
											    data-qtygrosir_dtrbmasuk    = '$trb[qty_grosir]'
											    data-no_batch               = '$trb[no_batch]'
											    >

											 </td>
											 <td align='right'>
											    <input type='text' id='ddiskon' value='$trb[diskon]' style='width: 50px'
											    data-id_dtrbmasuk           = '$trb[id_dtrbmasuk]'
											    data-kd_barang              = '$trb[kd_barang]'
											    data-qtygrosir_dtrbmasuk    = '$trb[qty_grosir]'
											    data-no_batch               = '$trb[no_batch]'
											    >

											 </td>
											 <td align='right'>$hnadisc12</td>
											 <td align='right'>
											    <input type='text' id='dhrgjual_dtrbmasuk' value='$hrgjual_dtrbmasuk' style='width: 100px'
											    data-id_dtrbmasuk           = '$trb[id_dtrbmasuk]'
											    data-kd_barang              = '$trb[kd_barang]'
											    data-qtygrosir_dtrbmasuk    = '$trb[qty_grosir]'
											    data-no_batch               = '$trb[no_batch]'
											    >
											 </td>
											 						
											 <td align='right'>$hrgttl_dtrbmasuk1</td>
											 <td align='center'>
    											 <button class='btn btn-xs btn-danger' id='hapusorder'
    												 data-id_dtrbmasuk='$trb[id_dtrbmasuk]'
    												 data-kd_barang='$trb[kd_barang]'>
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
    							   ORDER BY nmbrg_dtrbmasuk ASC");
            $stmt->execute([$kd_trbmasuk]);
            
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
    								data-no_batch               = '$no_batch'
    								>
    						</td>
    						<td align='center'>$r[satgrosir_dtrbmasuk]</td>
    						<td align='center'>
    							<input type='number' id='dkonversi' value='$r[konversi]' style='width: 60px; text-align: center'
    							    data-id_dtrbmasuk           = '$r[id_dtrbmasuk]'
    								data-kd_barang              = '$r[kd_barang]'
    								data-qtygrosir_dtrbmasuk    = '$r[qtygrosir_dtrbmasuk]'
    								data-no_batch               = '$no_batch'
    								>
    						</td>
    						<td align='center'>
    							<input type='text' id='dno_batch' value='$no_batch' style='width: 100px' required
    							    data-id_dtrbmasuk           = '$r[id_dtrbmasuk]'
    								data-kd_barang              = '$r[kd_barang]'
    								data-qtygrosir_dtrbmasuk    = '$r[qtygrosir_dtrbmasuk]'
    								>

    						</td>
    						<td align='center'>
    							<input type='text' class='datepicker' id='dexp_date' value='$r[exp_date]' required
    							data-id_dtrbmasuk           = '$r[id_dtrbmasuk]'
    							data-kd_barang              = '$r[kd_barang]'
    							data-qtygrosir_dtrbmasuk    = '$r[qtygrosir_dtrbmasuk]'
    							data-no_batch               = '$no_batch'
    							>

    						</td>
    						<td align='right'>
    							<input type='text' id='dhnasat_dtrbmasuk' value='$hnasat_dtrbmasuk' style='width: 100px'
    							data-id_dtrbmasuk           = '$r[id_dtrbmasuk]'
    							data-kd_barang              = '$r[kd_barang]'
    							data-qtygrosir_dtrbmasuk    = '$r[qtygrosir_dtrbmasuk]'
    							data-no_batch               = '$no_batch'
    							>

    						</td>
    						<td align='right'>
    							<input type='number' id='ddiskon' value='$r[diskon]' style='width: 50px'
    							data-id_dtrbmasuk           = '$r[id_dtrbmasuk]'
    							data-kd_barang              = '$r[kd_barang]'
    							data-qtygrosir_dtrbmasuk    = '$r[qtygrosir_dtrbmasuk]'
    							data-no_batch               = '$no_batch'
    							>

    						</td>
    						<td align='right'>$hnadisc1</td>
    						<td align='right'>
    							<input type='text' id='dhrgjual_dtrbmasuk' value='$hrgjual_dtrbmasuk' style='width: 100px'
    							data-id_dtrbmasuk           ='$r[id_dtrbmasuk]'
    							data-kd_barang              = '$r[kd_barang]'
    							data-qtygrosir_dtrbmasuk    = '$r[qtygrosir_dtrbmasuk]'
    							data-no_batch               = '$no_batch'
    						    >
    						</td>
    											 						
    						<td align='right'>$hrgttl_dtrbmasuk</td>
    						<td align='center'>
        						<button class='btn btn-xs btn-warning' id='batalkanorder' title='Batalkan item ini dari pesanan (tidak jadi dikirim)'
        							data-kd_barang='$r[kd_barang]'>
        						    <i class='glyphicon glyphicon-ban-circle'></i>
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
                            <td colspan='8'><h4><center>Total Harga </center></h4>  </td>
                            <td colspan='3'><h4><strong> Rp. <span id='ttl_harga_display'>$tampiltotalharga</span>  ,- </strong></h4></td>
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
                // Update sel-sel yang terkena dampak pada baris yang diedit, tanpa reload seluruh tabel
                // (supaya baris lain / posisi halaman DataTable tidak ikut berubah)
                function applyRowUpdatePbf($input, resp) {
                    var $row = $input.closest('tr');

                    $row.find('[data-id_dtrbmasuk]').each(function() {
                        $(this).data('id_dtrbmasuk', resp.id_dtrbmasuk);
                        $(this).attr('data-id_dtrbmasuk', resp.id_dtrbmasuk);
                    });

                    if (typeof resp.qty_grosir !== 'undefined') {
                        $row.find('[data-qtygrosir_dtrbmasuk]').each(function() {
                            $(this).data('qtygrosir_dtrbmasuk', resp.qty_grosir);
                            $(this).attr('data-qtygrosir_dtrbmasuk', resp.qty_grosir);
                        });
                    }

                    // no_batch dipakai sebagai bagian dari identitas baris (kd_barang+kd_trbmasuk+no_batch)
                    // supaya edit selanjutnya di baris ini tidak nyasar ke baris batch lain barang yang sama
                    if (typeof resp.no_batch !== 'undefined') {
                        $row.find('[data-no_batch]').each(function() {
                            $(this).data('no_batch', resp.no_batch);
                            $(this).attr('data-no_batch', resp.no_batch);
                        });
                        $row.find('#dno_batch').data('original-value', resp.no_batch);
                    }

                    if (typeof resp.hnadisc_text !== 'undefined') {
                        $row.find('td').eq(10).text(resp.hnadisc_text);
                    }
                    if (typeof resp.total_text !== 'undefined') {
                        $row.find('td').eq(12).text(resp.total_text);
                    }

                    if (typeof resp.subtotal !== 'undefined') {
                        document.getElementById('ttl_harga_display').textContent = resp.subtotal;
                        var ppn = Math.round(parseFloat(resp.subtotal.split('.').join('')) * 1.11);
                        document.getElementById('ttl_trkasir').value = formatRupiah(ppn);
                        HitungDP();
                    }
                }

                function tampilkanErrorPbf(xhr, $input, originalValue) {
                    var msg = 'Gagal menyimpan perubahan';
                    try {
                        var parsed = JSON.parse(xhr.responseText);
                        if (parsed && parsed.message) {
                            msg = parsed.message;
                        }
                    } catch (e) {}
                    alert(msg);
                    if ($input && typeof originalValue !== 'undefined') {
                        $input.val(originalValue);
                    }
                }

                $(document).ready(function() {
                    HitungDP();
                    var table = $("#example5").DataTable()

                    // simpan nilai awal saat fokus, untuk rollback jika gagal / tidak valid
                    $('#example5 tbody').on('focus', 'input', function() {
                        $(this).data('original-value', $(this).val());
                    });

                    $('#example5 tbody').on('change', '#dqtygrosir_dtrbmasuk', function () {
                        var $input = $(this);
                        var originalValue      = $input.data('original-value');
            		    var id_dtrbmasuk        = $input.data('id_dtrbmasuk');
            		    var kd_barang           = $input.data('kd_barang');
            		    var qtygrosir_dtrbmasuk = $input.val();
            		    var kd_orders           = $('#kd_trbmasuk').val();
            		    var kd_trbmasuk         = $('#kd_trbmasuk1').val();
            		    var no_batch_asal       = $input.data('no_batch');

                        if (qtygrosir_dtrbmasuk === '' || isNaN(qtygrosir_dtrbmasuk) || parseFloat(qtygrosir_dtrbmasuk) <= 0) {
                            alert('Qty Grosir harus diisi angka lebih dari 0');
                            $input.val(originalValue);
                            return;
                        }

                        $.ajax({
                            url: 'modul/mod_trbmasukpbf/simpandetail_qtygrosir.php',
                            type: 'post',
                            dataType: 'json',
                            data: {
                                'id_dtrbmasuk'          : id_dtrbmasuk,
                                'kd_trbmasuk'           : kd_trbmasuk,
                                'kd_orders'             : kd_orders,
                                'kd_barang'             : kd_barang,
                                'qtygrosir_dtrbmasuk'   : qtygrosir_dtrbmasuk,
                                'no_batch_asal'         : no_batch_asal,
                            },
                            success: function (resp) {
                                if (resp.status !== 'ok') {
                                    alert(resp.message || 'Gagal menyimpan perubahan');
                                    $input.val(originalValue);
                                    return;
                                }
                                applyRowUpdatePbf($input, resp);
                            },
                            error: function (xhr) {
                                tampilkanErrorPbf(xhr, $input, originalValue);
                            }
                        });
                    });

            		$('#example5 tbody').on('change', '#dkonversi', function () {
                        var $input = $(this);
                        var originalValue = $input.data('original-value');
            		    var id_dtrbmasuk        = $input.data('id_dtrbmasuk');
            		    var kd_barang           = $input.data('kd_barang');
            		    var konversi            = $input.val();
            		    var kd_orders           = $('#kd_trbmasuk').val();
            		    var kd_trbmasuk         = $('#kd_trbmasuk1').val();
            		    var no_batch_asal       = $input.data('no_batch');

                        if (konversi === '' || isNaN(konversi) || parseFloat(konversi) <= 0) {
                            alert('Konversi harus diisi angka lebih dari 0');
                            $input.val(originalValue);
                            return;
                        }

                        $.ajax({
                            url: 'modul/mod_trbmasukpbf/simpandetail_konversi.php',
                            type: 'post',
                            dataType: 'json',
                            data: {
                                'id_dtrbmasuk'          : id_dtrbmasuk,
                                'kd_trbmasuk'           : kd_trbmasuk,
                                'kd_orders'             : kd_orders,
                                'kd_barang'             : kd_barang,
                                'konversi'              : konversi,
                                'no_batch_asal'         : no_batch_asal,
                            },
                            success: function (resp) {
                                if (resp.status !== 'ok') {
                                    alert(resp.message || 'Gagal menyimpan perubahan');
                                    $input.val(originalValue);
                                    return;
                                }
                                applyRowUpdatePbf($input, resp);
                            },
                            error: function (xhr) {
                                tampilkanErrorPbf(xhr, $input, originalValue);
                            }
                        });
                    });

            		$('#example5 tbody').on('change', '#dhnasat_dtrbmasuk', function () {
                        var $input = $(this);
                        var originalValue = $input.data('original-value');
            		    var id_dtrbmasuk        = $input.data('id_dtrbmasuk');
            		    var kd_barang           = $input.data('kd_barang');
            		    var qtygrosir_dtrbmasuk = $input.data('qtygrosir_dtrbmasuk');
            		    var hnasat_dtrbmasuk    = $input.val();
            		    var kd_orders           = $('#kd_trbmasuk').val();
            		    var kd_trbmasuk         = $('#kd_trbmasuk1').val();
            		    var no_batch_asal       = $input.data('no_batch');

                        $.ajax({
                            url: 'modul/mod_trbmasukpbf/simpandetail_hna.php',
                            type: 'post',
                            dataType: 'json',
                            data: {
                                'id_dtrbmasuk'          : id_dtrbmasuk,
                                'kd_trbmasuk'           : kd_trbmasuk,
                                'kd_orders'             : kd_orders,
                                'kd_barang'             : kd_barang,
                                'hnasat_dtrbmasuk'      : hnasat_dtrbmasuk,
                                'qtygrosir_dtrbmasuk'   : qtygrosir_dtrbmasuk,
                                'no_batch_asal'         : no_batch_asal
                            },
                            success: function (resp) {
                                if (resp.status !== 'ok') {
                                    alert(resp.message || 'Gagal menyimpan perubahan');
                                    $input.val(originalValue);
                                    return;
                                }
                                applyRowUpdatePbf($input, resp);
                            },
                            error: function (xhr) {
                                tampilkanErrorPbf(xhr, $input, originalValue);
                            }
                        });
                    });

                    $('#example5 tbody').on('change', '#dno_batch', function () {
                        var $input = $(this);
                        var originalValue = $input.data('original-value');
            		    var id_dtrbmasuk        = $input.data('id_dtrbmasuk');
            		    var kd_barang           = $input.data('kd_barang');
            		    var no_batch            = $input.val();
            		    var qtygrosir_dtrbmasuk = $input.data('qtygrosir_dtrbmasuk');
            		    var kd_orders           = $('#kd_trbmasuk').val();
            		    var kd_trbmasuk         = $('#kd_trbmasuk1').val();
            		    var no_batch_asal       = originalValue;

            		    $.ajax({
                            url: 'modul/mod_trbmasukpbf/simpandetail_batch.php',
                            type: 'post',
                            dataType: 'json',
                            data: {
                                'id_dtrbmasuk'          : id_dtrbmasuk,
                                'kd_trbmasuk'           : kd_trbmasuk,
                                'kd_orders'             : kd_orders,
                                'kd_barang'             : kd_barang,
                                'no_batch'              : no_batch,
                                'no_batch_asal'         : no_batch_asal,
                                'qtygrosir_dtrbmasuk'   : qtygrosir_dtrbmasuk,
                            },
                            success: function (resp) {
                                if (resp.status !== 'ok') {
                                    alert(resp.message || 'Gagal menyimpan perubahan');
                                    $input.val(originalValue);
                                    return;
                                }
                                applyRowUpdatePbf($input, resp);
                            },
                            error: function (xhr) {
                                tampilkanErrorPbf(xhr, $input, originalValue);
                            }
                        });
                    });

                    $('#example5 tbody').on('change', '#dexp_date', function () {
                        var $input = $(this);
                        var originalValue = $input.data('original-value');
            		    var id_dtrbmasuk        = $input.data('id_dtrbmasuk');
            		    var kd_barang           = $input.data('kd_barang');
            		    var qtygrosir_dtrbmasuk = $input.data('qtygrosir_dtrbmasuk');
            		    var exp_date            = $input.val();
            		    var kd_orders           = $('#kd_trbmasuk').val();
            		    var kd_trbmasuk         = $('#kd_trbmasuk1').val();
            		    var no_batch_asal       = $input.data('no_batch');
            		    var tgl_trbmasuk = document.getElementById('tgl_trbmasuk').value;
            		    var min_exp_date = document.getElementById('min_exp_date').value;

                        const tglAwal = new Date(tgl_trbmasuk);
                        const tglAkhir = new Date(exp_date);
                        const selisih = hitungSelisihBulan(tglAwal, tglAkhir);
                        if(parseInt(selisih) < parseInt(min_exp_date)){
                            alert('Minimum Expired Date '+min_exp_date+' Hari dari Hari Ini!');
                            $input.val(originalValue);
                            return false;
                        }

                        $.ajax({
                            url: 'modul/mod_trbmasukpbf/simpandetail_expdate.php',
                            type: 'post',
                            dataType: 'json',
                            data: {
                                'id_dtrbmasuk'          : id_dtrbmasuk,
                                'kd_trbmasuk'           : kd_trbmasuk,
                                'kd_orders'             : kd_orders,
                                'kd_barang'             : kd_barang,
                                'exp_date'              : exp_date,
                                'qtygrosir_dtrbmasuk'   : qtygrosir_dtrbmasuk,
                                'no_batch_asal'         : no_batch_asal
                            },
                            success: function (resp) {
                                if (resp.status !== 'ok') {
                                    alert(resp.message || 'Gagal menyimpan perubahan');
                                    $input.val(originalValue);
                                    return;
                                }
                                applyRowUpdatePbf($input, resp);
                            },
                            error: function (xhr) {
                                tampilkanErrorPbf(xhr, $input, originalValue);
                            }
                        });
                    });

                    $('#example5 tbody').on('change', '#dhrgjual_dtrbmasuk', function () {
                        var $input = $(this);
                        var originalValue = $input.data('original-value');
            		    var id_dtrbmasuk        = $input.data('id_dtrbmasuk');
            		    var kd_barang           = $input.data('kd_barang');
            		    var qtygrosir_dtrbmasuk = $input.data('qtygrosir_dtrbmasuk');
            		    var hrgjual_dtrbmasuk   = $input.val();
            		    var kd_orders           = $('#kd_trbmasuk').val();
            		    var kd_trbmasuk         = $('#kd_trbmasuk1').val();
            		    var no_batch_asal       = $input.data('no_batch');

                        $.ajax({
                            url: 'modul/mod_trbmasukpbf/simpandetail_hrgjual.php',
                            type: 'post',
                            dataType: 'json',
                            data: {
                                'id_dtrbmasuk'          : id_dtrbmasuk,
                                'kd_trbmasuk'           : kd_trbmasuk,
                                'kd_orders'             : kd_orders,
                                'kd_barang'             : kd_barang,
                                'hrgjual_dtrbmasuk'     : hrgjual_dtrbmasuk,
                                'qtygrosir_dtrbmasuk'   : qtygrosir_dtrbmasuk,
                                'no_batch_asal'         : no_batch_asal
                            },
                            success: function (resp) {
                                if (resp.status !== 'ok') {
                                    alert(resp.message || 'Gagal menyimpan perubahan');
                                    $input.val(originalValue);
                                    return;
                                }
                                applyRowUpdatePbf($input, resp);
                            },
                            error: function (xhr) {
                                tampilkanErrorPbf(xhr, $input, originalValue);
                            }
                        });
                    });

                    $('#example5 tbody').on('change', '#ddiskon', function () {
                        var $input = $(this);
                        var originalValue = $input.data('original-value');
            		    var id_dtrbmasuk        = $input.data('id_dtrbmasuk');
            		    var kd_barang           = $input.data('kd_barang');
            		    var qtygrosir_dtrbmasuk = $input.data('qtygrosir_dtrbmasuk');
            		    var diskon              = $input.val();
            		    var kd_orders           = $('#kd_trbmasuk').val();
            		    var kd_trbmasuk         = $('#kd_trbmasuk1').val();
            		    var no_batch_asal       = $input.data('no_batch');

                        $.ajax({
                            url: 'modul/mod_trbmasukpbf/simpandetail_diskon.php',
                            type: 'post',
                            dataType: 'json',
                            data: {
                                'id_dtrbmasuk'          : id_dtrbmasuk,
                                'kd_trbmasuk'           : kd_trbmasuk,
                                'kd_orders'             : kd_orders,
                                'kd_barang'             : kd_barang,
                                'diskon'                : diskon,
                                'qtygrosir_dtrbmasuk'   : qtygrosir_dtrbmasuk,
                                'no_batch_asal'         : no_batch_asal,
                            },
                            success: function (resp) {
                                if (resp.status !== 'ok') {
                                    alert(resp.message || 'Gagal menyimpan perubahan');
                                    $input.val(originalValue);
                                    return;
                                }
                                applyRowUpdatePbf($input, resp);
                            },
                            error: function (xhr) {
                                tampilkanErrorPbf(xhr, $input, originalValue);
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