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


            // $tampil_barang = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM barang ORDER BY barang.id_barang ");
            $start_date = date('Y-m-d', strtotime('-30 days', strtotime($tgl_awal)));
            $finish_date = date('Y-m-d', time());
?>


            <div class="box box-primary box-solid table-responsive">
                <div class="box-header with-border">
                    <h3 class="box-title">NILAI DAN KATEGORI BARANG</h3>
                    <!--<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>-->

                    <div class="box-tools pull-center">

                    </div><!-- /.box-tools -->
                </div>
                <div class="box-body">
                    <!--<a  class ='btn  btn-success btn-flat' href='?module=barang&act=tambah'>TAMBAH</a>-->
                    <CENTER><strong>MySIFA TRAFFIC ANALYSIS</strong></CENTER><br>
                    <center>
                        <a class='btn  btn-primary btn-flat' href='?module=lapstok&act=laku'>LAKU</a>
                        <a class='btn  btn-success btn-flat' href='?module=lapstok&act=lancar'>LANCAR</a>
                        <a class='btn  btn-warning btn-flat' href='?module=lapstok&act=slow'>SLOW</a>
                        <a class='btn  btn-danger btn-flat' href='?module=lapstok&act=macet'>MACET</a>
                    </center>
                    <br><br>


                    <table id="tes" class="table table-bordered table-striped">
                        <!--<table id="example1" class="table table-bordered table-striped" >-->
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Nama Barang</th>
                                <th style="text-align: right; ">Qty/Stok</th>
                                <th style="text-align: right; ">T30</th>
                                <th style="text-align: right;">T60</th>
                                <th style="text-align: right;">gr(%)</th>
                                <th style="text-align: right; ">Q30</th>
                                <th style="text-align: center; ">Satuan</th>
                                <th style="text-align: right; ">Harga Beli</th>
                                <th style="text-align: center; ">Nilai Barang</th>
                                <th width="70">Kartu Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            //                     $no=1;
                            //                     while ($r=mysqli_fetch_assoc($tampil_barang)){
                            //                         $t30 = $r[kd_barang];
                            //                         $tgl_awal = date('Y-m-d');
                            //                         $tgl_akhir = date('Y-m-d', strtotime('-30 days', strtotime( $tgl_awal)));
                            //                         $tgl60 = date('Y-m-d', strtotime('-60 days', strtotime( $tgl_awal)));
                            //                         $hargabeli = format_rupiah($r['hrgsat_barang']);
                            //                         $hargajual = format_rupiah($r['hrgjual_barang']);

                            //                         $pass = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM trkasir JOIN trkasir_detail
                            //                                     ON (trkasir.kd_trkasir=trkasir_detail.kd_trkasir)
                            //                                     WHERE kd_barang = '$t30' AND (tgl_trkasir BETWEEN '$tgl_akhir' and '$tgl_awal')");
                            //                         $pass1 = mysqli_num_rows($pass);

                            //                         $pass3 = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM trkasir JOIN trkasir_detail
                            //                                     ON (trkasir.kd_trkasir=trkasir_detail.kd_trkasir)
                            //                                     WHERE kd_barang = '$t30' AND (tgl_trkasir BETWEEN '$tgl60' and '$tgl_awal')");
                            //                         $pass4 = mysqli_num_rows($pass3);
                            //                         $pass5 = $pass4 - $pass1;
                            //                         $pass6 = intval(round(($pass1/$pass5*100) - 100));


                            //                         $pass2 = mysqli_fetch_array($pass);
                            //                         $tot =mysqli_query($GLOBALS["___mysqli_ston"], "SELECT
                            //                                     trkasir_detail.kd_barang,
                            //                                     trkasir_detail.id_dtrkasir,
                            //                                     trkasir_detail.kd_trkasir,
                            //                                     SUM(trkasir_detail.qty_dtrkasir) as pw,
                            //                                     trkasir.kd_trkasir,
                            //                                     trkasir.tgl_trkasir                            
                            //                                     FROM trkasir_detail 
                            //                                     JOIN trkasir ON (trkasir_detail.kd_trkasir=trkasir.kd_trkasir)
                            //                                     WHERE kd_barang = '$pass2[kd_barang]' AND (tgl_trkasir BETWEEN '$tgl_akhir' and '$tgl_awal')") ;

                            //                         $t2 = mysqli_fetch_array($tot);
                            //                         $q30 = $t2['pw'];
                            //                         $hargabeli = format_rupiah($r['hrgsat_barang']);
                            //                         $hargajual = format_rupiah($r['hrgjual_barang']);
                            //                         $nilaibarang = format_rupiah($r['hrgsat_barang'] * $r[stok_barang]);
                            //                         $nilaibarang2 = $r['hrgsat_barang'] * $r[stok_barang];
                            //                         $totalbarang += $nilaibarang2;
                            //                         $tb = format_rupiah($totalbarang);


                            //                         echo "<tr class='warnabaris' >";
                            //                         if( $pass1 <= "0"){
                            //                             echo" <td style='background-color:#dd4b39;'>$no</td> ";                                                            }
                            //                         elseif ($pass1 > "0" && $pass1 <= "5"){
                            //                             echo"  <td style='background-color:#f39c12;'>$no</td>"; }
                            //                         elseif ($pass1 > "0" && $pass1 <= "10"){
                            //                             echo"  <td style='background-color:#00a65a;'>$no</td>"; }
                            //                         elseif ($pass1 > "10" ){
                            //                             echo"  <td style='background-color:#00c0ef;'>$no</td>"; }

                            //                         echo"    											         
                            // 							 <td>$r[kd_barang]</td>
                            // 							 <td>$r[nm_barang]</td>
                            // 							 <td align=right>$r[stok_barang]</td>

                            // 							 <td align=right>$pass1</td>
                            // 							 <td align=right>$pass5</td>
                            // 							 ";

                            // 			if($pass6 < -30){
                            // 			    echo "<td align=right style='background-color:#dd4b39; color:#ffffff'>$pass6</td>"; 
                            // 			} elseif($pass6 < 0 && $pass >= -30){
                            // 				echo "<td align=right style='background-color:#f39c12; color:#ffffff'>$pass6</td>"; 
                            // 			} else {
                            // 			    echo "<td align=right>$pass6</td>"; 
                            // 			}


                            //                         if($q30 < "0"){
                            //                             echo "<td align='right'> 0 </td>";
                            //                         }else {
                            //                             echo "<td align='right'>$q30</td>";

                            //                         }

                            //                         echo"											
                            // 							 <td align=center>$r[sat_barang]</td>

                            // 							 <td align=right>$hargabeli</td>
                            // 							 <td align=right> $nilaibarang </td>
                            // 							 <td>
                            // 							 <a href='?module=lapstok&act=edit&id=$r[kd_barang]' title='Riwayat' class='btn btn-warning btn-xs'>Riwayat</a> 
                            // 							 <!-- barang tidak boleh didelete
                            // 							 <a href=javascript:confirmdelete('$aksi?module=barang&act=hapus&id=$r[id_barang]') title='HAPUS' class='btn btn-danger btn-xs'>HAPUS</a>
                            // 							 -->
                            // 							</td>


                            // 						</tr>";
                            //                         $no++;
                            //                     }

                            //                     echo "</tbody>
                            //                     <tr>
                            //                         <td colspan='7'><h3><center>Total</center></h3>  </td>
                            //                         <td colspan='3'><h3><strong> Rp. $tb  ,- </strong></h3></td> 
                            //                     </tr>
                            //                     </table>";

                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="7">
                                    <h3>
                                        <center>Total</center>
                                    </h3>
                                </td>
                                <td colspan="5">
                                    <h3><strong> Rp. <span id="totalRupiah">0</span>,- </strong></h3>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <script>
                $(document).ready(function() {
                    var start = '<?= $start_date ?>';
                    var finish = '<?= $finish_date ?>';
                    $('#tes').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: {
                            "url": "modul/mod_lapstok/barang-serverside.php?action=table_data&start=" + start + "&finish=" + finish,
                            "dataType": "JSON",
                            "type": "POST"
                        },
                        "rowCallback": function(row, data, index) {
                            // warna for nomor
                            if (data['t30'] <= 0) {
                                $(row).find('td:eq(0)').css('background-color', '#dd4b39');
                                $(row).find('td:eq(0)').css('color', '#ffffff');
                            } else if (data['t30'] > 0 && data['t30'] <= 5) {
                                $(row).find('td:eq(0)').css('background-color', '#f39c12');
                                $(row).find('td:eq(0)').css('color', '#ffffff');
                            } else if (data['t30'] > 0 && data['t30'] <= 10) {
                                $(row).find('td:eq(0)').css('background-color', '#00a65a');
                                $(row).find('td:eq(0)').css('color', '#ffffff');
                            } else if (data['t30'] > 10) {
                                $(row).find('td:eq(0)').css('background-color', '#00c0ef');
                            }

                            // warna for gr
                            if (data['gr'] < -30) {
                                $(row).find('td:eq(6)').css('background-color', '#dd4b39');
                                $(row).find('td:eq(6)').css('color', '#ffffff');
                            } else if (data['gr'] < 0 && data['gr'] >= -30) {
                                $(row).find('td:eq(6)').css('background-color', '#f39c12');
                                $(row).find('td:eq(6)').css('color', '#ffffff');
                            }

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
                                "data": "t30",
                                "className": 'text-center',
                            },
                            {
                                "data": "t60",
                                "className": 'text-center',
                            },
                            {
                                "data": "gr",
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
                            {
                                "data": "nilai_barang",
                                "className": 'text-right',
                                "render": function(data, type, row) {
                                    return formatRupiah(data);
                                }
                            },
                            {
                                "data": "kartu_stok",
                                "className": 'text-center'
                            },
                        ],
                        "footerCallback": function(row, data, start, end, display) {
                            let api = this.api();
                            let json = api.ajax.json();
                            $('#totalRupiah').html(formatRupiah(json.totalStok));
                        }
                    });

                });
            </script>
        <?php

            break;

        case "laku":

            // $tampil_barang = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM barang ORDER BY barang.id_barang ");
            $start_date = ($_GET['start'] != "") ? date("Y-m-d", strtotime($_GET['start'])) : date("Y-m-d", time());
            $finish_date = ($_GET['finish'] != "") ? date("Y-m-d", strtotime($_GET['finish'])) : date("Y-m-d", time());
        ?>


            <div class="box box-primary box-solid table-responsive">
                <div class="box-header with-border">
                    <h3 class="box-title">BARANG LAKU</h3>
                    <!--<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>-->

                    <div class="box-tools pull-center">

                    </div><!-- /.box-tools -->
                </div>
                <div class="box-body">
                    <!--<a  class ='btn  btn-success btn-flat' href='?module=barang&act=tambah'>TAMBAH</a>-->
                    <CENTER><strong>MySIFA TRAFFIC ANALYSIS</strong></CENTER><br>
                    <center>
                        <a class='btn  btn-info btn-flat' href='?module=lapstok&act=default'>GLOBAL</a>
                        <a class='btn  btn-success btn-flat' href='?module=lapstok&act=lancar'>LANCAR</a>
                        <a class='btn  btn-warning btn-flat' href='?module=lapstok&act=slow'>SLOW</a>
                        <a class='btn  btn-danger btn-flat' href='?module=lapstok&act=macet'>MACET</a>
                    </center>
                    <br><br>

                    <form method="POST" action="#" target="_blank" enctype="multipart/form-data" class="form-horizontal">

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Tanggal Awal</label>
                            <div class="col-sm-4">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <span class="glyphicon glyphicon-th"></span>
                                    </div>
                                    <input type="text" class="datepicker" name="tgl_awal" required="required" autocomplete="off" id="awal" value="<?= $_GET['start'] ?>">
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
                                    <input type="text" class="datepicker" name="tgl_akhir" required="required" autocomplete="off" id="akhir" value="<?= $_GET['finish'] ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label"></label>
                            <div class="buttons col-sm-4">
                                <input class="btn btn-primary" type="button" id="submit" name="btn" value="SUBMIT">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                <a class='btn  btn-danger' href='?module=home'>KEMBALI</a>
                            </div>
                        </div>
                    </form>
                    <hr />


                    <table id="tes" class="table table-bordered table-striped">
                        <!--<table id="example1" class="table table-bordered table-striped" >-->
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Nama Barang</th>
                                <th style="text-align: right; ">Qty/Stok</th>
                                <th style="text-align: right; ">Buffer</th>
                                <th style="text-align: right; ">T30</th>
                                <th style="text-align: right; ">Q30</th>
                                <th style="text-align: right; ">OM30</th>
                                <th style="text-align: right; ">L30</th>
                                <th style="text-align: center; ">Satuan</th>
                                <th style="text-align: right; ">Harga Beli</th>
                                <th style="text-align: center; ">Nilai Barang</th>
                                <th width="70">Kartu Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            //             $no = 1;
                            //                 while ($r = mysqli_fetch_assoc($tampil_barang)) {
                            //                     $t30 = $r['kd_barang'];
                            //                     $tgl_awal = date('Y-m-d');
                            //                     $tgl_akhir = date('Y-m-d', strtotime('-30 days', strtotime( $tgl_awal)));
                            //                     $hargabeli = format_rupiah($r['hrgsat_barang']);
                            //                     $hargajual = format_rupiah($r['hrgjual_barang']);

                            //                     $pass = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM trkasir JOIN trkasir_detail
                            //                                 ON (trkasir.kd_trkasir=trkasir_detail.kd_trkasir)
                            //                                 WHERE kd_barang = '$t30' AND (tgl_trkasir BETWEEN '$start_date' and '$finish_date')");
                            //                     $pass1 = mysqli_num_rows($pass);

                            //                     $pass2 = mysqli_fetch_array($pass);

                            //                     $tot =mysqli_query($GLOBALS["___mysqli_ston"], "SELECT
                            //                                 trkasir_detail.kd_barang,
                            //                                 trkasir_detail.id_dtrkasir,
                            //                                 trkasir_detail.kd_trkasir,
                            //                                 SUM(trkasir_detail.qty_dtrkasir) as pw,
                            //                                 SUM(trkasir_detail.hrgttl_dtrkasir) as om,
                            //                                 trkasir.kd_trkasir,
                            //                                 trkasir.tgl_trkasir                            
                            //                                 FROM trkasir_detail 
                            //                                 JOIN trkasir ON (trkasir_detail.kd_trkasir=trkasir.kd_trkasir)
                            //                                 WHERE kd_barang = '$pass2[kd_barang]' AND (tgl_trkasir BETWEEN '$start_date' and '$finish_date')") ;
                            //                     if($pass1>10){
                            //                         $t2 = mysqli_fetch_array($tot);
                            //                         $q30 = $t2['pw'];
                            //                         $om30 = format_rupiah($t2['om']);
                            //                         $l30 = $t2['om']-($q30 * $r['hrgsat_barang']);
                            //                         $lb30 =  format_rupiah($l30);
                            //                         $totalom30 += $t2['om'];
                            //                         $tom30 = format_rupiah($totalom30);
                            //                         $totallb30 += $l30;
                            //                         $tlb30 = format_rupiah($totallb30);

                            //                         $hargabeli = format_rupiah($r['hrgsat_barang']);
                            //                         $hargajual = format_rupiah($r['hrgjual_barang']);
                            //                         $nilaibarang = format_rupiah($r['hrgsat_barang'] * $r[stok_barang]);
                            //                         $nilaibarang2 = $r['hrgsat_barang'] * $r[stok_barang];
                            //                         $totalbarang += $nilaibarang2;
                            //                         $tb = format_rupiah($totalbarang);


                            //                         echo "<tr class='warnabaris' >";
                            //                         if( $pass1 <= "0"){
                            //                             echo" <td style='background-color:#dd4b39;'>$no</td> ";                                                            }
                            //                         elseif ($pass1 > "0" && $pass1 <= "5"){
                            //                             echo"  <td style='background-color:#f39c12;'>$no</td>"; }
                            //                         elseif ($pass1 > "0" && $pass1 <= "10"){
                            //                             echo"  <td style='background-color:#00a65a;'>$no</td>"; }
                            //                         elseif ($pass1 > "10" ){
                            //                             echo"  <td style='background-color:#00c0ef;'>$no</td>"; }

                            //                         echo"    											         
                            // 			 <td>$r[kd_barang]</td>
                            // 			 <td>$r[nm_barang]</td>
                            // 			 <td align=right>$r[stok_barang]</td>
                            // 			 <td align=right>$r[stok_buffer]</td>
                            // 			 <td align=right>$pass1</td>";

                            //                         if($q30<"0")
                            //                         {echo "<td align=right> 0 </td>";}
                            //                         else {echo "<td align=right>$q30</td>";}

                            //                         echo"											
                            // 			 <td align=center>$om30</td>
                            // 			 <td align=center>$lb30</td>
                            // 			 <td align=center>$r[sat_barang]</td>

                            // 			 <td align=right>$hargabeli</td>
                            // 			 <td align=right> $nilaibarang </td>
                            // 			 <td><a href='?module=lapstok&act=edit&id=$r[kd_barang]' title='Riwayat' class='btn btn-warning btn-xs'>Riwayat</a> 
                            // 			 <!-- barang tidak boleh didelete
                            // 			 <a href=javascript:confirmdelete('$aksi?module=barang&act=hapus&id=$r[id_barang]') title='HAPUS' class='btn btn-danger btn-xs'>HAPUS</a>
                            // 			 -->
                            // 			</td>


                            // 			</td>
                            // 		</tr>";
                            //                         $no++;
                            //                     }
                            //                 }

                            //                 echo "</tbody>
                            //             <tr>
                            //                 <td colspan='7'>
                            //                     <h3>Total Stok Tersedia</h3>  
                            //                     <h3>Total Omzet Barang LAKU dari $_GET[start] s/d $_GET[finish]</h3>  
                            //                     <h3>Laba Atas Penjualan Item Pareto dari $_GET[start] s/d $_GET[finish]</h3>  
                            //                 </td>
                            //                 <td colspan='3'>
                            //                     <h3><strong> Rp. $tb  ,- </strong></h3>
                            //                     <h3><strong> Rp. $tom30  ,- </strong></h3>
                            //                     <h3><strong> Rp. $tlb30  ,- </strong></h3>
                            //                 </td> 
                            //                 <td><a class ='btn btn-primary btn-flat' href='modul/mod_lapstok/SO_baranglaku.php' target='_blank'>STOK <br>OPNAME</a></td>
                            //             </tr>
                            //             </table>";

                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan='7'>
                                    <h3>Total Stok Tersedia</h3>
                                    <h3>Total Omzet Barang LAKU dari <?= $_GET['start'] . " s/d " . $_GET['finish'] ?></h3>
                                    <h3>Laba Atas Penjualan Item Pareto dari <?= $_GET['start'] . " s/d " . $_GET['finish'] ?></h3>
                                </td>
                                <td colspan='3'>
                                    <h3><strong> Rp. <span id="nilaiStok">0</span> ,- </strong></h3>
                                    <h3><strong> Rp. <span id="nilaiOmset">0</span> ,- </strong></h3>
                                    <h3><strong> Rp. <span id="nilaiLaba">0</span> ,- </strong></h3>
                                </td>
                                <td><a class='btn btn-primary btn-flat' href='modul/mod_lapstok/SO_baranglaku.php' target='_blank'>STOK <br>OPNAME</a></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <script>
                $(document).ready(function() {
                    var start = '<?= $start_date ?>';
                    var finish = '<?= $finish_date ?>';

                    $('#tes').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: {
                            "url": "modul/mod_lapstok/brglaku-serverside.php?action=table_data&start=" + start + "&finish=" + finish,
                            "dataType": "JSON",
                            "type": "POST"
                        },
                        "rowCallback": function(row, data, index) {
                            // warna for nomor
                            if (data['t30'] <= 0) {
                                $(row).find('td:eq(0)').css('background-color', '#dd4b39');
                                $(row).find('td:eq(0)').css('color', '#ffffff');
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
                                "className": "text-center"
                            },
                            {
                                "data": "kd_barang"
                            },
                            {
                                "data": "nm_barang"
                            },
                            {
                                "data": "stok_barang",
                                "className": "text-center"
                            },
                            {
                                "data": "stok_buffer",
                                "className": "text-center"
                            },
                            {
                                "data": "t30",
                                "className": "text-center"
                            },
                            {
                                "data": "q30",
                                "className": "text-center"
                            },
                            {
                                "data": "om30",
                                "className": "text-center",
                                "render": function(data, type, row) {
                                    return formatRupiah(data);
                                }
                            },
                            {
                                "data": "l30",
                                "className": "text-center",
                                "render": function(data, type, row) {
                                    return formatRupiah(data);
                                }
                            },
                            {
                                "data": "satuan",
                                "className": "text-center"
                            },
                            {
                                "data": "harga_beli",
                                "className": "text-right",
                                "render": function(data, type, row) {
                                    return formatRupiah(data);
                                }
                            },
                            {
                                "data": "nilai_barang",
                                "className": "text-right",
                                "render": function(data, type, row) {
                                    return formatRupiah(data);
                                }
                            },
                            {
                                "data": "kartu_stok",
                                "className": "text-center"
                            },
                        ],
                        "footerCallback": function(row, data, start, end, display) {
                            let api = this.api();
                            let json = api.ajax.json();
                            $("#nilaiStok").text(formatRupiah(json.totalStok));
                            $("#nilaiOmset").text(formatRupiah(json.totalOm30));
                            $("#nilaiLaba").text(formatRupiah(json.totalL30));
                        }
                    });
                });
            </script>
        <?php
            break;
        case "lancar":


            // $tampil_barang = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM barang ORDER BY barang.id_barang ");
            $start_date = ($_GET['start'] != "") ? date("Y-m-d", strtotime($_GET['start'])) : date("Y-m-d", time());
            $finish_date = ($_GET['finish'] != "") ? date("Y-m-d", strtotime($_GET['finish'])) : date("Y-m-d", time());
        ?>


            <div class="box box-success box-solid table-responsive">
                <div class="box-header with-border">
                    <h3 class="box-title">BARANG LANCAR</h3>
                    <!--<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>-->

                    <div class="box-tools pull-center">

                    </div><!-- /.box-tools -->
                </div>
                <div class="box-body">
                    <!--<a  class ='btn  btn-success btn-flat' href='?module=barang&act=tambah'>TAMBAH</a>-->
                    <CENTER><strong>MySIFA TRAFFIC ANALYSIS</strong></CENTER><br>
                    <center>
                        <a class='btn  btn-primary btn-flat' href='?module=lapstok&act=laku'>LAKU</a>
                        <a class='btn  btn-success btn-flat' href='?module=lapstok&act=default'>GLOBAL</a>
                        <a class='btn  btn-warning btn-flat' href='?module=lapstok&act=slow'>SLOW</a>
                        <a class='btn  btn-danger btn-flat' href='?module=lapstok&act=macet'>MACET</a>
                    </center>
                    <br><br>

                    <form method="POST" action="#" target="_blank" enctype="multipart/form-data" class="form-horizontal">

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Tanggal Awal</label>
                            <div class="col-sm-4">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <span class="glyphicon glyphicon-th"></span>
                                    </div>
                                    <input type="text" class="datepicker" name="tgl_awal" required="required" autocomplete="off" id="awal" value="<?= $_GET['start'] ?>">
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
                                    <input type="text" class="datepicker" name="tgl_akhir" required="required" autocomplete="off" id="akhir" value="<?= $_GET['finish'] ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label"></label>
                            <div class="buttons col-sm-4">
                                <input class="btn btn-primary" type="button" id="submit" name="btn" value="SUBMIT">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                <a class='btn  btn-danger' href='?module=home'>KEMBALI</a>
                            </div>
                        </div>
                    </form>
                    <hr />


                    <table id="tes" class="table table-bordered table-striped">
                        <!--<table id="example1" class="table table-bordered table-striped" >-->
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Nama Barang</th>
                                <th style="text-align: right; ">Qty/Stok</th>
                                <th style="text-align: right; ">Buffer</th>
                                <th style="text-align: right; ">T30</th>
                                <th style="text-align: right; ">Q30</th>
                                <th style="text-align: right; ">OM30</th>
                                <th style="text-align: right; ">L30</th>
                                <th style="text-align: center; ">Satuan</th>
                                <th style="text-align: right; ">Harga Beli</th>
                                <th style="text-align: center; ">Nilai Barang</th>
                                <th width="70">Kartu Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            //             $no = 1;
                            //                 while ($r = mysqli_fetch_assoc($tampil_barang)) {
                            //                     $t30 = $r[kd_barang];
                            //                     $tgl_awal = date('Y-m-d');
                            //                     $tgl_akhir = date('Y-m-d', strtotime('-30 days', strtotime( $tgl_awal)));
                            //                     $hargabeli = format_rupiah($r['hrgsat_barang']);
                            //                     $hargajual = format_rupiah($r['hrgjual_barang']);

                            //                     $pass = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM trkasir JOIN trkasir_detail
                            //                                 ON (trkasir.kd_trkasir=trkasir_detail.kd_trkasir)
                            //                                 WHERE kd_barang = '$t30' AND (tgl_trkasir BETWEEN '$start_date' and '$finish_date')");
                            //                     $pass1 = mysqli_num_rows($pass);

                            //                     $pass2 = mysqli_fetch_array($pass);

                            //                     $tot =mysqli_query($GLOBALS["___mysqli_ston"], "SELECT
                            //                                 trkasir_detail.kd_barang,
                            //                                 trkasir_detail.id_dtrkasir,
                            //                                 trkasir_detail.kd_trkasir,
                            //                                 SUM(trkasir_detail.qty_dtrkasir) as pw,
                            //                                 SUM(trkasir_detail.hrgttl_dtrkasir) as om,
                            //                                 trkasir.kd_trkasir,
                            //                                 trkasir.tgl_trkasir                            
                            //                                 FROM trkasir_detail 
                            //                                 JOIN trkasir ON (trkasir_detail.kd_trkasir=trkasir.kd_trkasir)
                            //                                 WHERE kd_barang = '$pass2[kd_barang]' AND (tgl_trkasir BETWEEN '$start_date' and '$finish_date')") ;

                            //                     if($pass1>5 && $pass1<11){
                            //                         $t2 = mysqli_fetch_array($tot);
                            //                         $q30 = $t2['pw'];
                            //                         $om30 = format_rupiah($t2['om']);
                            //                         $l30 = $t2['om']-($q30 * $r['hrgsat_barang']);
                            //                         $lb30 =  format_rupiah($l30);
                            //                         $totalom30 += $t2['om'];
                            //                         $tom30 = format_rupiah($totalom30);
                            //                         $totallb30 += $l30;
                            //                         $tlb30 = format_rupiah($totallb30);

                            //                         $hargabeli = format_rupiah($r['hrgsat_barang']);
                            //                         $hargajual = format_rupiah($r['hrgjual_barang']);
                            //                         $nilaibarang = format_rupiah($r['hrgsat_barang'] * $r[stok_barang]);
                            //                         $nilaibarang2 = $r['hrgsat_barang'] * $r[stok_barang];
                            //                         $totalbarang += $nilaibarang2;
                            //                         $tb = format_rupiah($totalbarang);


                            //                         echo "<tr class='warnabaris' >";
                            //                         if( $pass1 <= "0"){
                            //                             echo" <td style='background-color:#dd4b39;'>$no</td> ";                                                            }
                            //                         elseif ($pass1 > "0" && $pass1 <= "5"){
                            //                             echo"  <td style='background-color:#f39c12;'>$no</td>"; }
                            //                         elseif ($pass1 > "0" && $pass1 <= "10"){
                            //                             echo"  <td style='background-color:#00a65a;'>$no</td>"; }
                            //                         elseif ($pass1 > "10" ){
                            //                             echo"  <td style='background-color:#00c0ef;'>$no</td>"; }

                            //                         echo"    											         
                            // 			 <td>$r[kd_barang]</td>
                            // 			 <td>$r[nm_barang]</td>
                            // 			 <td align=right>$r[stok_barang]</td>
                            // 			 <td align=right>$r[stok_buffer]</td>
                            // 			 <td align=right>$pass1</td>";

                            //                         if($q30<"0")
                            //                         {echo "<td align=right> 0 </td>";}
                            //                         else {echo "<td align=right>$q30</td>";}

                            //                         echo"											
                            // 			 <td align=center>$om30</td>
                            // 			 <td align=center>$lb30</td>
                            // 			 <td align=center>$r[sat_barang]</td>

                            // 			 <td align=right>$hargabeli</td>
                            // 			 <td align=right> $nilaibarang </td>
                            // 			 <td><a href='?module=lapstok&act=edit&id=$r[kd_barang]' title='Riwayat' class='btn btn-warning btn-xs'>Riwayat</a> 
                            // 			 <!-- barang tidak boleh didelete
                            // 			 <a href=javascript:confirmdelete('$aksi?module=barang&act=hapus&id=$r[id_barang]') title='HAPUS' class='btn btn-danger btn-xs'>HAPUS</a>
                            // 			 -->
                            // 			</td>


                            // 			</td>
                            // 		</tr>";
                            //                         $no++;
                            //                     }
                            //                 }

                            //                 echo "</tbody>
                            //             <tr>
                            //                 <td colspan='7'>
                            //                     <h3>Total Stok Tersedia</h3>  
                            //                     <h3>Total Omzet Barang LANCAR dari $_GET[start] s/d $_GET[finish]</h3>  
                            //                     <h3>Laba Atas Penjualan Item Pareto dari $_GET[start] s/d $_GET[finish]</h3>  
                            //                 </td>
                            //                 <td colspan='3'>
                            //                     <h3><strong> Rp. $tb  ,- </strong></h3>
                            //                     <h3><strong> Rp. $tom30  ,- </strong></h3>
                            //                     <h3><strong> Rp. $tlb30  ,- </strong></h3>
                            //                 </td>
                            //                 <td><a class ='btn btn-success btn-flat' href='modul/mod_lapstok/SO_baranglancar.php' target='_blank'>STOK <br>OPNAME</a></td> 
                            //             </tr>
                            //             </table>";

                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan='7'>
                                    <h3>Total Stok Tersedia</h3>
                                    <h3>Total Omzet Barang LANCAR dari <?= $_GET['start'] . " s/d " . $_GET['finish'] ?></h3>
                                    <h3>Laba Atas Penjualan Item Pareto dari <?= $_GET['start'] . " s/d " . $_GET['finish'] ?></h3>
                                </td>
                                <td colspan='3'>
                                    <h3><strong> Rp. <span id="nilaiStok">0</span> ,- </strong></h3>
                                    <h3><strong> Rp. <span id="nilaiOmset">0</span> ,- </strong></h3>
                                    <h3><strong> Rp. <span id="nilaiLaba">0</span> ,- </strong></h3>
                                </td>
                                <td><a class='btn btn-success btn-flat' href='modul/mod_lapstok/SO_baranglancar.php' target='_blank'>STOK <br>OPNAME</a></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <script>
                $(document).ready(function() {
                    var start = '<?= $start_date ?>';
                    var finish = '<?= $finish_date ?>';

                    $('#tes').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: {
                            "url": "modul/mod_lapstok/brglancar-serverside.php?action=table_data&start=" + start + "&finish=" + finish,
                            "dataType": "JSON",
                            "type": "POST"
                        },
                        "rowCallback": function(row, data, index) {
                            // warna for nomor
                            if (data['t30'] <= 0) {
                                $(row).find('td:eq(0)').css('background-color', '#dd4b39');
                                $(row).find('td:eq(0)').css('color', '#ffffff');
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
                                "className": "text-center"
                            },
                            {
                                "data": "kd_barang"
                            },
                            {
                                "data": "nm_barang"
                            },
                            {
                                "data": "stok_barang",
                                "className": "text-center"
                            },
                            {
                                "data": "stok_buffer",
                                "className": "text-center"
                            },
                            {
                                "data": "t30",
                                "className": "text-center"
                            },
                            {
                                "data": "q30",
                                "className": "text-center"
                            },
                            {
                                "data": "om30",
                                "className": "text-center",
                                "render": function(data, type, row) {
                                    return formatRupiah(data);
                                }
                            },
                            {
                                "data": "l30",
                                "className": "text-center",
                                "render": function(data, type, row) {
                                    return formatRupiah(data);
                                }
                            },
                            {
                                "data": "satuan",
                                "className": "text-center"
                            },
                            {
                                "data": "harga_beli",
                                "className": "text-right",
                                "render": function(data, type, row) {
                                    return formatRupiah(data);
                                }
                            },
                            {
                                "data": "nilai_barang",
                                "className": "text-right",
                                "render": function(data, type, row) {
                                    return formatRupiah(data);
                                }
                            },
                            {
                                "data": "kartu_stok",
                                "className": "text-center"
                            },
                        ],
                        "footerCallback": function(row, data, start, end, display) {
                            let api = this.api();
                            let json = api.ajax.json();
                            $("#nilaiStok").text(formatRupiah(json.totalStok));
                            $("#nilaiOmset").text(formatRupiah(json.totalOm30));
                            $("#nilaiLaba").text(formatRupiah(json.totalL30));
                        }
                    });
                });
            </script>
        <?php

            break;
        case "slow":

            // $tampil_barang = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM barang ORDER BY barang.id_barang ");
            $start_date = ($_GET['start'] != "") ? date("Y-m-d", strtotime($_GET['start'])) : date("Y-m-d", time());
            $finish_date = ($_GET['finish'] != "") ? date("Y-m-d", strtotime($_GET['finish'])) : date("Y-m-d", time());
        ?>


            <div class="box box-warning box-solid table-responsive">
                <div class="box-header with-border">
                    <h3 class="box-title">BARANG SLOW</h3>
                    <!--<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>-->

                    <div class="box-tools pull-center">

                    </div><!-- /.box-tools -->
                </div>
                <div class="box-body">
                    <!--<a  class ='btn  btn-success btn-flat' href='?module=barang&act=tambah'>TAMBAH</a>-->
                    <CENTER><strong>MySIFA TRAFFIC ANALYSIS</strong></CENTER><br>
                    <center>
                        <a class='btn  btn-primary btn-flat' href='?module=lapstok&act=laku'>LAKU</a>
                        <a class='btn  btn-success btn-flat' href='?module=lapstok&act=lancar'>LANCAR</a>
                        <a class='btn  btn-warning btn-flat' href='?module=lapstok&act=default'>GLOBAL</a>
                        <a class='btn  btn-danger btn-flat' href='?module=lapstok&act=macet'>MACET</a>
                    </center>
                    <br><br>

                    <form method="POST" action="#" target="_blank" enctype="multipart/form-data" class="form-horizontal">

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Tanggal Awal</label>
                            <div class="col-sm-4">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <span class="glyphicon glyphicon-th"></span>
                                    </div>
                                    <input type="text" class="datepicker" name="tgl_awal" required="required" autocomplete="off" id="awal" value="<?= $_GET['start'] ?>">
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
                                    <input type="text" class="datepicker" name="tgl_akhir" required="required" autocomplete="off" id="akhir" value="<?= $_GET['finish'] ?>">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label"></label>
                            <div class="buttons col-sm-4">
                                <input class="btn btn-primary" type="button" id="submit" name="btn" value="SUBMIT">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                <a class='btn  btn-danger' href='?module=home'>KEMBALI</a>
                            </div>
                        </div>
                    </form>
                    <hr />


                    <table id="tes" class="table table-bordered table-striped">
                        <!--<table id="example1" class="table table-bordered table-striped" >-->
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Kode</th>
                                <th>Nama Barang</th>
                                <th style="text-align: right; ">Qty/Stok</th>
                                <th style="text-align: right; ">Buffer</th>
                                <th style="text-align: right; ">T30</th>
                                <th style="text-align: right; ">Q30</th>
                                <th style="text-align: right; ">OM30</th>
                                <th style="text-align: right; ">L30</th>
                                <th style="text-align: center; ">Satuan</th>
                                <th style="text-align: right; ">Harga Beli</th>
                                <th style="text-align: center; ">Nilai Barang</th>
                                <th width="70">Kartu Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            //                     $no = 1;
                            //                     while ($r = mysqli_fetch_assoc($tampil_barang)) {
                            //                         $t30 = $r['kd_barang'];
                            //                         $tgl_awal = date('Y-m-d');
                            //                         $tgl_akhir = date('Y-m-d', strtotime('-30 days', strtotime( $tgl_awal)));
                            //                         $hargabeli = format_rupiah($r['hrgsat_barang']);
                            //                         $hargajual = format_rupiah($r['hrgjual_barang']);

                            //                         $pass = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM trkasir JOIN trkasir_detail
                            //                                 ON (trkasir.kd_trkasir=trkasir_detail.kd_trkasir)
                            //                                 WHERE kd_barang = '$t30' AND (tgl_trkasir BETWEEN '$start_date' and '$finish_date')");
                            //                         $pass1 = mysqli_num_rows($pass);

                            //                         $pass2 = mysqli_fetch_array($pass);

                            //                         $tot =mysqli_query($GLOBALS["___mysqli_ston"], "SELECT
                            //                                 trkasir_detail.kd_barang,
                            //                                 trkasir_detail.id_dtrkasir,
                            //                                 trkasir_detail.kd_trkasir,
                            //                                 SUM(trkasir_detail.qty_dtrkasir) as pw,
                            //                                 SUM(trkasir_detail.hrgttl_dtrkasir) as om,
                            //                                 trkasir.kd_trkasir,
                            //                                 trkasir.tgl_trkasir                            
                            //                                 FROM trkasir_detail 
                            //                                 JOIN trkasir ON (trkasir_detail.kd_trkasir=trkasir.kd_trkasir)
                            //                                 WHERE kd_barang = '$pass2[kd_barang]' AND (tgl_trkasir BETWEEN '$start_date' and '$finish_date')") ;

                            //                         if($pass1>0 && $pass1<6){
                            //                             $t2 = mysqli_fetch_array($tot);
                            //                             $q30 = $t2['pw'];
                            //                             $om30 = format_rupiah($t2['om']);
                            //                             $l30 = $t2['om']-($q30 * $r['hrgsat_barang']);
                            //                             $lb30 =  format_rupiah($l30);
                            //                             $totalom30 += $t2['om'];
                            //                             $tom30 = format_rupiah($totalom30);
                            //                             $totallb30 += $l30;
                            //                             $tlb30 = format_rupiah($totallb30);

                            //                             $hargabeli = format_rupiah($r['hrgsat_barang']);
                            //                             $hargajual = format_rupiah($r['hrgjual_barang']);
                            //                             $nilaibarang = format_rupiah($r['hrgsat_barang'] * $r[stok_barang]);
                            //                             $nilaibarang2 = $r['hrgsat_barang'] * $r[stok_barang];
                            //                             $totalbarang += $nilaibarang2;
                            //                             $tb = format_rupiah($totalbarang);


                            //                             echo "<tr class='warnabaris' >";
                            //                             if( $pass1 <= "0"){
                            //                                 echo" <td style='background-color:#dd4b39;'>$no</td> ";                                                            }
                            //                             elseif ($pass1 > "0" && $pass1 <= "5"){
                            //                                 echo"  <td style='background-color:#f39c12;'>$no</td>"; }
                            //                             elseif ($pass1 > "0" && $pass1 <= "10"){
                            //                                 echo"  <td style='background-color:#00a65a;'>$no</td>"; }
                            //                             elseif ($pass1 > "10" ){
                            //                                 echo"  <td style='background-color:#00c0ef;'>$no</td>"; }

                            //                             echo"    											         
                            // 			 <td>$r[kd_barang]</td>
                            // 			 <td>$r[nm_barang]</td>
                            // 			 <td align=right>$r[stok_barang]</td>
                            // 			 <td align=right>$r[stok_buffer]</td>
                            // 			 <td align=right>$pass1</td>";

                            //                             if($q30<"0")
                            //                             {echo "<td align=right> 0 </td>";}
                            //                             else {echo "<td align=right>$q30</td>";}

                            //                             echo"											
                            // 			 <td align=center>$om30</td>
                            // 			 <td align=center>$lb30</td>
                            // 			 <td align=center>$r[sat_barang]</td>

                            // 			 <td align=right>$hargabeli</td>
                            // 			 <td align=right> $nilaibarang </td>
                            // 			 <td><a href='?module=lapstok&act=edit&id=$r[kd_barang]' title='Riwayat' class='btn btn-warning btn-xs'>Riwayat</a> 
                            // 			 <!-- barang tidak boleh didelete
                            // 			 <a href=javascript:confirmdelete('$aksi?module=barang&act=hapus&id=$r[id_barang]') title='HAPUS' class='btn btn-danger btn-xs'>HAPUS</a>
                            // 			 -->
                            // 			</td>


                            // 			</td>
                            // 		</tr>";
                            //                             $no++;
                            //                         }
                            //                     }

                            //                     echo "</tbody>
                            //                 <tr>
                            //                     <td colspan='7'>
                            //                         <h3>Total Stok Tersedia</h3>  
                            //                         <h3>Total Omzet Barang SLOW MOVING dari $_GET[start] s/d $_GET[finish]</h3>  
                            //                         <h3>Laba Atas Penjualan Item Pareto dari $_GET[start] s/d $_GET[finish]</h3>  
                            //                     </td>
                            //                     <td colspan='3'>
                            //                         <h3><strong> Rp. $tb  ,- </strong></h3>
                            //                         <h3><strong> Rp. $tom30  ,- </strong></h3>
                            //                         <h3><strong> Rp. $tlb30  ,- </strong></h3>
                            //                     </td>
                            //                     <td><a class ='btn btn-warning btn-flat' href='modul/mod_lapstok/SO_barangslow.php' target='_blank'>STOK <br>OPNAME</a></td>
                            //                 </tr>
                            //                 </table>";

                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan='7'>
                                    <h3>Total Stok Tersedia</h3>
                                    <h3>Total Omzet Barang SLOW MOVING dari <?= $_GET['start'] . " s/d " . $_GET['finish'] ?></h3>
                                    <h3>Laba Atas Penjualan Item Pareto dari <?= $_GET['start'] . " s/d " . $_GET['finish'] ?></h3>
                                </td>
                                <td colspan='3'>
                                    <h3><strong> Rp. <span id="nilaiStok">0</span> ,- </strong></h3>
                                    <h3><strong> Rp. <span id="nilaiOmset">0</span> ,- </strong></h3>
                                    <h3><strong> Rp. <span id="nilaiLaba">0</span> ,- </strong></h3>
                                </td>
                                <td><a class='btn btn-warning btn-flat' href='modul/mod_lapstok/SO_barangslow.php' target='_blank'>STOK <br>OPNAME</a></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>


            <script>
                var start = '<?= $start_date ?>';
                var finish = '<?= $finish_date ?>';
                $(document).ready(function() {
                    $('#tes').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: {
                            "url": "modul/mod_lapstok/brgslow-serverside.php?action=table_data&start=" + start + "&finish=" + finish,
                            "dataType": "JSON",
                            "type": "POST"
                        },
                        "rowCallback": function(row, data, index) {
                            // warna for nomor
                            if (data['t30'] <= 0) {
                                $(row).find('td:eq(0)').css('background-color', '#dd4b39');
                                $(row).find('td:eq(0)').css('color', '#ffffff');
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
                                "className": "text-center"
                            },
                            {
                                "data": "kd_barang"
                            },
                            {
                                "data": "nm_barang"
                            },
                            {
                                "data": "stok_barang",
                                "className": "text-center"
                            },
                            {
                                "data": "stok_buffer",
                                "className": "text-center"
                            },
                            {
                                "data": "t30",
                                "className": "text-center"
                            },
                            {
                                "data": "q30",
                                "className": "text-center"
                            },
                            {
                                "data": "om30",
                                "className": "text-center",
                                "render": function(data, type, row) {
                                    return formatRupiah(data);
                                }
                            },
                            {
                                "data": "l30",
                                "className": "text-center",
                                "render": function(data, type, row) {
                                    return formatRupiah(data);
                                }
                            },
                            {
                                "data": "satuan",
                                "className": "text-center"
                            },
                            {
                                "data": "harga_beli",
                                "className": "text-right",
                                "render": function(data, type, row) {
                                    return formatRupiah(data);
                                }
                            },
                            {
                                "data": "nilai_barang",
                                "className": "text-right",
                                "render": function(data, type, row) {
                                    return formatRupiah(data);
                                }
                            },
                            {
                                "data": "kartu_stok",
                                "className": "text-center"
                            },
                        ],
                        "footerCallback": function(row, data, start, end, display) {
                            let api = this.api();
                            let json = api.ajax.json();
                            $("#nilaiStok").html(json.totalStok);
                            $("#nilaiOmset").html(json.totalOm30);
                            $("#nilaiLaba").html(json.totalL30);
                        }
                    });
                });
            </script>
        <?php
            break;
        case "macet":

            // $tampil_barang = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM barang ORDER BY barang.id_barang ");
            $finish_date = ($_GET['finish'] != "") ? date("Y-m-d", strtotime($_GET['finish'])) : date("Y-m-d", time());
            $start_date = ($_GET['start'] != "") ? date("Y-m-d", strtotime($_GET['start'])) : date('Y-m-d', strtotime('-30 days', strtotime($finish_date)));;
        ?>


            <div class="box box-danger box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">BARANG MACET</h3>
                    <!--<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>-->

                    <div class="box-tools pull-center">

                    </div><!-- /.box-tools -->
                </div>
                <div class="box-body">
                    <!--<a  class ='btn  btn-success btn-flat' href='?module=barang&act=tambah'>TAMBAH</a>-->
                    <CENTER><strong>MySIFA TRAFFIC ANALYSIS</strong></CENTER><br>
                    <center>
                        <a class='btn  btn-primary btn-flat' href='?module=lapstok&act=laku'>LAKU</a>
                        <a class='btn  btn-success btn-flat' href='?module=lapstok&act=lancar'>LANCAR</a>
                        <a class='btn  btn-warning btn-flat' href='?module=lapstok&act=slow'>SLOW</a>
                        <a class='btn  btn-danger btn-flat' href='?module=lapstok&act=default'>GLOBAL</a>
                        <br><br>


                        <table id="tes" class="table table-bordered table-striped">
                            <!--<table id="example1" class="table table-bordered table-striped" >-->
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Kode</th>
                                    <th>Nama Barang</th>
                                    <th style="text-align: right; ">Qty/Stok</th>
                                    <th style="text-align: right; ">Buffer</th>
                                    <th style="text-align: right; ">T30</th>
                                    <th style="text-align: right; ">Q30</th>
                                    <th style="text-align: center; ">Satuan</th>
                                    <th style="text-align: right; ">Harga Beli</th>
                                    <th style="text-align: center; ">Nilai Barang</th>
                                    <th width="70">Kartu Stok</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                //                     $no=1;
                                //                     while ($r=mysqli_fetch_assoc($tampil_barang)){
                                //                         $t30 = $r[kd_barang];
                                //                         $tgl_awal = date('Y-m-d');
                                //                         $tgl_akhir = date('Y-m-d', strtotime('-30 days', strtotime( $tgl_awal)));
                                //                         $hargabeli = format_rupiah($r['hrgsat_barang']);
                                //                         $hargajual = format_rupiah($r['hrgjual_barang']);

                                //                         $pass = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM trkasir JOIN trkasir_detail
                                //                                 ON (trkasir.kd_trkasir=trkasir_detail.kd_trkasir)
                                //                                 WHERE kd_barang = '$t30' AND (tgl_trkasir BETWEEN '$tgl_akhir' and '$tgl_awal')");
                                //                         $pass1 = mysqli_num_rows($pass);

                                //                         $pass2 = mysqli_fetch_array($pass);

                                //                         $tot =mysqli_query($GLOBALS["___mysqli_ston"], "SELECT
                                //                                 trkasir_detail.kd_barang,
                                //                                 trkasir_detail.id_dtrkasir,
                                //                                 trkasir_detail.kd_trkasir,
                                //                                 SUM(trkasir_detail.qty_dtrkasir) as pw,
                                //                                 trkasir.kd_trkasir,
                                //                                 trkasir.tgl_trkasir                            
                                //                                 FROM trkasir_detail 
                                //                                 JOIN trkasir ON (trkasir_detail.kd_trkasir=trkasir.kd_trkasir)
                                //                                 WHERE kd_barang = '$pass2[kd_barang]' AND (tgl_trkasir BETWEEN '$tgl_akhir' and '$tgl_awal')") ;

                                //                         if($pass1<1){
                                //                             $t2 = mysqli_fetch_array($tot);
                                //                             $q30 = $t2['pw'];
                                //                             $hargabeli = format_rupiah($r['hrgsat_barang']);
                                //                             $hargajual = format_rupiah($r['hrgjual_barang']);
                                //                             $nilaibarang = format_rupiah($r['hrgsat_barang'] * $r[stok_barang]);
                                //                             $nilaibarang2 = $r['hrgsat_barang'] * $r[stok_barang];
                                //                             $totalbarang += $nilaibarang2;
                                //                             $tb = format_rupiah($totalbarang);


                                //                             echo "<tr class='warnabaris' >";
                                //                             if( $pass1 <= "0"){
                                //                                 echo" <td style='background-color:#dd4b39;'>$no</td> ";                                                            }
                                //                             elseif ($pass1 > "0" && $pass1 <= "5"){
                                //                                 echo"  <td style='background-color:#f39c12;'>$no</td>"; }
                                //                             elseif ($pass1 > "0" && $pass1 <= "10"){
                                //                                 echo"  <td style='background-color:#00a65a;'>$no</td>"; }
                                //                             elseif ($pass1 > "10" ){
                                //                                 echo"  <td style='background-color:#00c0ef;'>$no</td>"; }

                                //                             echo"    											         
                                // 			 <td>$r[kd_barang]</td>
                                // 			 <td>$r[nm_barang]</td>
                                // 			 <td align=right>$r[stok_barang]</td>
                                // 			 <td align=right>$r[stok_buffer]</td>
                                // 			 <td align=right>$pass1</td>";

                                //                             if($q30<"0")
                                //                             {echo "<td align=right> 0 </td>";}
                                //                             else {echo "<td align=right>$q30</td>";}

                                //                             echo"											
                                // 			 <td align=center>$r[sat_barang]</td>

                                // 			 <td align=right>$hargabeli</td>
                                // 			 <td align=right> $nilaibarang </td>
                                // 			 <td><a href='?module=lapstok&act=edit&id=$r[kd_barang]' title='Riwayat' class='btn btn-warning btn-xs'>Riwayat</a> 
                                // 			 <!-- barang tidak boleh didelete
                                // 			 <a href=javascript:confirmdelete('$aksi?module=barang&act=hapus&id=$r[id_barang]') title='HAPUS' class='btn btn-danger btn-xs'>HAPUS</a>
                                // 			 -->
                                // 			</td>


                                // 			</td>
                                // 		</tr>";
                                //                             $no++;
                                //                         }}

                                //                     echo "</tbody>
                                //                 <tr>
                                //                     <td colspan='6'><h3><center>Total</center></h3>  </td>
                                //                     <td colspan='3'><h3><strong> Rp. $tb  ,- </strong></h3></td> 
                                //                     <td><a class ='btn btn-danger btn-flat' href='modul/mod_lapstok/SO_barangmacet.php' target='_blank'>STOK <br>OPNAME</a></td> 
                                //                     <td><a class ='btn btn-success btn-flat' href='modul/mod_lapstok/SO_barangmacet_excel.php' target='_blank'>EXPORT <br>TO EXCEL</a></td> 

                                //                 </tr>
                                //                 </table>";

                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan='6'>
                                        <h3>
                                            <center>Total</center>
                                        </h3>
                                    </td>
                                    <td colspan='3'>
                                        <h3><strong> Rp. <span id="nilaiStok">0</span> ,- </strong></h3>
                                    </td>
                                    <td><a class='btn btn-danger btn-flat' href='modul/mod_lapstok/SO_barangmacet.php' target='_blank'>STOK <br>OPNAME</a></td>
                                    <td><a class='btn btn-success btn-flat' href='modul/mod_lapstok/SO_barangmacet_excel.php' target='_blank'>EXPORT <br>TO EXCEL</a></td>

                                </tr>
                            </tfoot>
                        </table>
                </div>
            </div>

            <script>
                var start = '<?= $start_date ?>';
                var finish = '<?= $finish_date ?>';
                $(document).ready(function() {
                    $('#tes').DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: {
                            "url": "modul/mod_lapstok/brgmacet-serverside.php?action=table_data&start=" + start + "&finish=" + finish,
                            "dataType": "JSON",
                            "type": "POST"
                        },
                        "rowCallback": function(row, data, index) {
                            // warna for nomor
                            if (data['t30'] <= 0) {
                                $(row).find('td:eq(0)').css('background-color', '#dd4b39');
                                $(row).find('td:eq(0)').css('color', '#ffffff');
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
                                "className": "text-center"
                            },
                            {
                                "data": "kd_barang"
                            },
                            {
                                "data": "nm_barang"
                            },
                            {
                                "data": "stok_barang",
                                "className": "text-center"
                            },
                            {
                                "data": "stok_buffer",
                                "className": "text-center"
                            },
                            {
                                "data": "t30",
                                "className": "text-center"
                            },
                            {
                                "data": "q30",
                                "className": "text-center"
                            },
                            {
                                "data": "satuan",
                                "className": "text-center"
                            },
                            {
                                "data": "harga_beli",
                                "className": "text-right",
                                "render": function(data, type, row) {
                                    return formatRupiah(data);
                                }
                            },
                            {
                                "data": "nilai_barang",
                                "className": "text-right",
                                "render": function(data, type, row) {
                                    return formatRupiah(data);
                                }
                            },
                            {
                                "data": "kartu_stok",
                                "className": "text-center"
                            },
                        ],
                        "footerCallback": function(row, data, start, end, display) {
                            let api = this.api();
                            let json = api.ajax.json();
                            $("#nilaiStok").html(formatRupiah(json.totalStok));
                        }
                    });


                });
            </script>
        <?php
            break;
        case "edit":
            $jokul = $_GET['id'];
            $k = mysqli_query($GLOBALS["___mysqli_ston"], "select * from barang where kd_barang='$jokul'");
            $k2 = mysqli_fetch_assoc($k);
            $w = $k2['nm_barang'];
            $w2 = $k2['stok_barang'];

            $jual = "SELECT trkasir.tgl_trkasir,
            trkasir.kd_trkasir,
            trkasir.nm_pelanggan,
            trkasir_detail.nmbrg_dtrkasir,
            trkasir_detail.sat_dtrkasir,
            trkasir_detail.hrgjual_dtrkasir,
            trkasir_detail.qty_dtrkasir
            FROM trkasir_detail join trkasir on (trkasir_detail.kd_trkasir=trkasir.kd_trkasir)
            WHERE kd_barang =$jokul order by trkasir.tgl_trkasir desc ";
            $penjualan = mysqli_query($GLOBALS["___mysqli_ston"], $jual);


        ?>

            <div class='box box-primary box-solid'>
                <div class='box-header with-border'>
                    <h3 class='box-title'><?php echo "Riwayat Penjualan $w (stok = $w2)"; ?></h3>
                    <div class='box-tools pull-right'>
                        <button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
                    </div><!-- /.box-tools -->
                </div>

                <div class='box-body'>
                    <table id="example1" class="table table-condensed table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th style="text-align: center; ">No Transaksi</th>
                                <th style="text-align: center; ">Konsumen</th>
                                <th style="text-align: center; ">Harga Jual</th>
                                <th style="text-align: center; ">Tanggal dan Waktu</th>
                                <th style="text-align: center; ">Keluar</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($r = mysqli_fetch_array($penjualan)) {
                                $w = $r['nmbrg_dtrkasir'];
                                echo "
                            <tr>
                                <td align=center>$r[kd_trkasir]</td>
                                <td align=center>$r[nm_pelanggan]</td>                            
                                <td align=center>$r[hrgjual_dtrkasir]</td>                               
                                <td align=center>$r[tgl_trkasir]</td>                               
                                <td align=center>$r[qty_dtrkasir]</td>
                               

                            </tr>";
                            }
                            ?>
                        </tbody>

                    </table>


                </div>

            </div>
            <?php

            $beli = "SELECT trbmasuk.tgl_trbmasuk,
                trbmasuk.kd_trbmasuk,
                trbmasuk.nm_supplier,
                trbmasuk_detail.nmbrg_dtrbmasuk,
                trbmasuk_detail.sat_dtrbmasuk,
                trbmasuk_detail.qty_dtrbmasuk,
                trbmasuk_detail.hrgsat_dtrbmasuk
                FROM trbmasuk_detail join trbmasuk on (trbmasuk_detail.kd_trbmasuk=trbmasuk.kd_trbmasuk)
                WHERE kd_barang =$jokul order by trbmasuk.tgl_trbmasuk desc ";
            $pembelian = mysqli_query($GLOBALS["___mysqli_ston"], $beli);


            ?>
            <div class='box box-primary box-solid'>
                <div class='box-header with-border'>
                    <h3 class='box-title'><?php echo "Riwayat Pembelian $w"; ?></h3>
                    <div class='box-tools pull-right'>
                        <button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
                    </div><!-- /.box-tools -->
                </div>

                <div class='box-body'>
                    <table id="example1" class="table table-condensed table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th style="text-align: center; ">No Transaksi</th>
                                <th style="text-align: center; ">Suplier</th>
                                <th style="text-align: center; ">Harga Beli</th>
                                <th style="text-align: center; ">Tanggal dan Waktu</th>
                                <th style="text-align: center; ">Masuk</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            while ($q = mysqli_fetch_array($pembelian)) {
                                echo "
                            <tr>
                                <td align=center >$q[kd_trbmasuk]</td>                          
                                <td align=center>$q[nm_supplier]</td>                                                        
                                <td align=center>$q[hrgsat_dtrbmasuk]</td>                               
                                <td align=center>$q[tgl_trbmasuk]</td>                               
                                <td align=center>$q[qty_dtrbmasuk]</td>
                               

                            </tr>";
                            }
                            ?>
                        </tbody>

                    </table>


                </div>

            </div>

<?php


            break;
    }
}
?>


<script type="text/javascript">
    $(function() {
        $(".datepicker").datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
        });
    });

    $("#submit").on("click", function() {
        var awal = $("#awal").val();
        var akhir = $("#akhir").val();
        location.href = "?module=lapstok&act=<?= $_GET['act'] ?>&start=" + awal + "&finish=" + akhir;
    });
</script>