<?php
session_start();
 if (empty($_SESSION['username']) AND empty($_SESSION['passuser'])){
  echo "<link href=../css/style.css rel=stylesheet type=text/css>";
  echo "<div class='error msg'>Untuk mengakses Modul anda harus login.</div>";
}
else{

$aksi="modul/mod_barang/aksi_barang.php";
$aksi_barang = "masuk/modul/mod_barang/aksi_barang.php";
switch($_GET[act]){
  // Tampil barang
  default:

  
      $tampil_barang = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM barang ORDER BY barang.id_barang ");
      
	  ?>
			
			
			<div class="box box-primary box-solid">
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
                        <a  class ='btn  btn-primary btn-flat' href='?module=lapstok&act=laku'>LAKU</a>
                        <a  class ='btn  btn-success btn-flat' href='?module=lapstok&act=lancar'>LANCAR</a>
                        <a  class ='btn  btn-warning btn-flat' href='?module=lapstok&act=slow'>SLOW</a>
                        <a  class ='btn  btn-danger btn-flat' href='?module=lapstok&act=macet'>MACET</a>
                        </center>
					<br><br>
					
					
					<table id="example1" class="table table-bordered table-striped" >
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
								$no=1;
								while ($r=mysqli_fetch_assoc($tampil_barang)){
                                    $t30 = $r[kd_barang];
                                    $tgl_awal = date('Y-m-d');
                                    $tgl_akhir = date('Y-m-d', strtotime('-30 days', strtotime( $tgl_awal)));
                                    $hargabeli = format_rupiah($r['hrgsat_barang']);
                                    $hargajual = format_rupiah($r['hrgjual_barang']);

                                    $pass = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM trkasir JOIN trkasir_detail
                                        ON (trkasir.kd_trkasir=trkasir_detail.kd_trkasir)
                                        WHERE kd_barang = '$t30' AND (tgl_trkasir BETWEEN '$tgl_akhir' and '$tgl_awal')");
                                    $pass1 = mysqli_num_rows($pass);

                                    $pass2 = mysqli_fetch_array($pass);

                                    $tot =mysqli_query($GLOBALS["___mysqli_ston"], "SELECT
                                        trkasir_detail.kd_barang,
                                        trkasir_detail.id_dtrkasir,
                                        trkasir_detail.kd_trkasir,
                                        SUM(trkasir_detail.qty_dtrkasir) as pw,
                                        trkasir.kd_trkasir,
                                        trkasir.tgl_trkasir                            
                                        FROM trkasir_detail 
                                        JOIN trkasir ON (trkasir_detail.kd_trkasir=trkasir.kd_trkasir)
                                        WHERE kd_barang = '$pass2[kd_barang]' AND (tgl_trkasir BETWEEN '$tgl_akhir' and '$tgl_awal')") ;

                                    $t2 = mysqli_fetch_array($tot);
                                    $q30 = $t2['pw'];
								$hargabeli = format_rupiah($r['hrgsat_barang']);
								$hargajual = format_rupiah($r['hrgjual_barang']);
								$nilaibarang = format_rupiah($r['hrgsat_barang'] * $r[stok_barang]);
								$nilaibarang2 = $r['hrgsat_barang'] * $r[stok_barang];
                                $totalbarang += $nilaibarang2;
                                $tb = format_rupiah($totalbarang);


                                    echo "<tr class='warnabaris' >";
                                        if( $pass1 <= "0"){
                                            echo" <td style='background-color:#dd4b39;'>$no</td> ";                                                            }
                                        elseif ($pass1 > "0" && $pass1 <= "5"){
                                            echo"  <td style='background-color:#f39c12;'>$no</td>"; }
                                        elseif ($pass1 > "0" && $pass1 <= "10"){
                                            echo"  <td style='background-color:#00a65a;'>$no</td>"; }
                                        elseif ($pass1 > "10" ){
                                            echo"  <td style='background-color:#00c0ef;'>$no</td>"; }

                                    echo"    											         
											 <td>$r[kd_barang]</td>
											 <td>$r[nm_barang]</td>
											 <td align=right>$r[stok_barang]</td>
											 <td align=right>$r[stok_buffer]</td>
											 <td align=right>$pass1</td>";

                                    if($q30<"0")
                                        {echo "<td align=right> 0 </td>";}
									else {echo "<td align=right>$q30</td>";}

									echo"											
											 <td align=center>$r[sat_barang]</td>
											
											 <td align=right>$hargabeli</td>
											 <td align=right> $nilaibarang </td>
											 <td><a href='?module=lapstok&act=edit&id=$r[kd_barang]' title='Riwayat' class='btn btn-warning btn-xs'>Riwayat</a> 
											 <!-- barang tidak boleh didelete
											 <a href=javascript:confirmdelete('$aksi?module=barang&act=hapus&id=$r[id_barang]') title='HAPUS' class='btn btn-danger btn-xs'>HAPUS</a>
											 -->
											</td>
											
											 
											</td>
										</tr>";
								$no++;
								}

						echo "</tbody>
                        <tr>
                            <td colspan='7'><h3><center>Total</center></h3>  </td>
                            <td colspan='3'><h3><strong> Rp. $tb  ,- </strong></h3></td> 
                        </tr>
                        </table>";

					?>
				</div>
			</div>	
             

<?php
    
    break;
	
	case "laku":


        $tampil_barang = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM barang ORDER BY barang.id_barang ");

        ?>


        <div class="box box-primary box-solid">
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
                    <a  class ='btn  btn-info btn-flat' href='?module=lapstok&act=default'>GLOBAL</a>
                    <a  class ='btn  btn-success btn-flat' href='?module=lapstok&act=lancar'>LANCAR</a>
                    <a  class ='btn  btn-warning btn-flat' href='?module=lapstok&act=slow'>SLOW</a>
                    <a  class ='btn  btn-danger btn-flat' href='?module=lapstok&act=macet'>MACET</a>
                </center>
                <br><br>


                <table id="example1" class="table table-bordered table-striped" >
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
                    $no=1;
                    while ($r=mysqli_fetch_assoc($tampil_barang)){
                        $t30 = $r[kd_barang];
                        $tgl_awal = date('Y-m-d');
                        $tgl_akhir = date('Y-m-d', strtotime('-30 days', strtotime( $tgl_awal)));
                        $hargabeli = format_rupiah($r['hrgsat_barang']);
                        $hargajual = format_rupiah($r['hrgjual_barang']);

                        $pass = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM trkasir JOIN trkasir_detail
                                        ON (trkasir.kd_trkasir=trkasir_detail.kd_trkasir)
                                        WHERE kd_barang = '$t30' AND (tgl_trkasir BETWEEN '$tgl_akhir' and '$tgl_awal')");
                        $pass1 = mysqli_num_rows($pass);

                        $pass2 = mysqli_fetch_array($pass);

                        $tot =mysqli_query($GLOBALS["___mysqli_ston"], "SELECT
                                        trkasir_detail.kd_barang,
                                        trkasir_detail.id_dtrkasir,
                                        trkasir_detail.kd_trkasir,
                                        SUM(trkasir_detail.qty_dtrkasir) as pw,
                                        trkasir.kd_trkasir,
                                        trkasir.tgl_trkasir                            
                                        FROM trkasir_detail 
                                        JOIN trkasir ON (trkasir_detail.kd_trkasir=trkasir.kd_trkasir)
                                        WHERE kd_barang = '$pass2[kd_barang]' AND (tgl_trkasir BETWEEN '$tgl_akhir' and '$tgl_awal')") ;
                        if($pass1>10){
                        $t2 = mysqli_fetch_array($tot);
                        $q30 = $t2['pw'];
                        $hargabeli = format_rupiah($r['hrgsat_barang']);
                        $hargajual = format_rupiah($r['hrgjual_barang']);
                        $nilaibarang = format_rupiah($r['hrgsat_barang'] * $r[stok_barang]);
                        $nilaibarang2 = $r['hrgsat_barang'] * $r[stok_barang];
                        $totalbarang += $nilaibarang2;
                        $tb = format_rupiah($totalbarang);


                        echo "<tr class='warnabaris' >";
                        if( $pass1 <= "0"){
                            echo" <td style='background-color:#dd4b39;'>$no</td> ";                                                            }
                        elseif ($pass1 > "0" && $pass1 <= "5"){
                            echo"  <td style='background-color:#f39c12;'>$no</td>"; }
                        elseif ($pass1 > "0" && $pass1 <= "10"){
                            echo"  <td style='background-color:#00a65a;'>$no</td>"; }
                        elseif ($pass1 > "10" ){
                            echo"  <td style='background-color:#00c0ef;'>$no</td>"; }

                        echo"    											         
											 <td>$r[kd_barang]</td>
											 <td>$r[nm_barang]</td>
											 <td align=right>$r[stok_barang]</td>
											 <td align=right>$r[stok_buffer]</td>
											 <td align=right>$pass1</td>";

                        if($q30<"0")
                        {echo "<td align=right> 0 </td>";}
                        else {echo "<td align=right>$q30</td>";}

                        echo"											
											 <td align=center>$r[sat_barang]</td>
											
											 <td align=right>$hargabeli</td>
											 <td align=right> $nilaibarang </td>
											 <td><a href='?module=lapstok&act=edit&id=$r[kd_barang]' title='Riwayat' class='btn btn-warning btn-xs'>Riwayat</a> 
											 <!-- barang tidak boleh didelete
											 <a href=javascript:confirmdelete('$aksi?module=barang&act=hapus&id=$r[id_barang]') title='HAPUS' class='btn btn-danger btn-xs'>HAPUS</a>
											 -->
											</td>
											
											 
											</td>
										</tr>";
                        $no++;
                    }}

                    echo "</tbody>
                        <tr>
                            <td colspan='7'><h3><center>Total</center></h3>  </td>
                            <td colspan='3'><h3><strong> Rp. $tb  ,- </strong></h3></td> 
                        </tr>
                        </table>";

                    ?>
            </div>
        </div>

<?php
break;
    case "lancar":


        $tampil_barang = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM barang ORDER BY barang.id_barang ");

        ?>


        <div class="box box-success box-solid">
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
                    <a  class ='btn  btn-primary btn-flat' href='?module=lapstok&act=laku'>LAKU</a>
                    <a  class ='btn  btn-success btn-flat' href='?module=lapstok&act=default'>GLOBAL</a>
                    <a  class ='btn  btn-warning btn-flat' href='?module=lapstok&act=slow'>SLOW</a>
                    <a  class ='btn  btn-danger btn-flat' href='?module=lapstok&act=macet'>MACET</a></center>
                <br><br>


                <table id="example1" class="table table-bordered table-striped" >
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
                    $no=1;
                    while ($r=mysqli_fetch_assoc($tampil_barang)){
                        $t30 = $r[kd_barang];
                        $tgl_awal = date('Y-m-d');
                        $tgl_akhir = date('Y-m-d', strtotime('-30 days', strtotime( $tgl_awal)));
                        $hargabeli = format_rupiah($r['hrgsat_barang']);
                        $hargajual = format_rupiah($r['hrgjual_barang']);

                        $pass = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM trkasir JOIN trkasir_detail
                                        ON (trkasir.kd_trkasir=trkasir_detail.kd_trkasir)
                                        WHERE kd_barang = '$t30' AND (tgl_trkasir BETWEEN '$tgl_akhir' and '$tgl_awal')");
                        $pass1 = mysqli_num_rows($pass);

                        $pass2 = mysqli_fetch_array($pass);

                        $tot =mysqli_query($GLOBALS["___mysqli_ston"], "SELECT
                                        trkasir_detail.kd_barang,
                                        trkasir_detail.id_dtrkasir,
                                        trkasir_detail.kd_trkasir,
                                        SUM(trkasir_detail.qty_dtrkasir) as pw,
                                        trkasir.kd_trkasir,
                                        trkasir.tgl_trkasir                            
                                        FROM trkasir_detail 
                                        JOIN trkasir ON (trkasir_detail.kd_trkasir=trkasir.kd_trkasir)
                                        WHERE kd_barang = '$pass2[kd_barang]' AND (tgl_trkasir BETWEEN '$tgl_akhir' and '$tgl_awal')") ;

                        if($pass1>5 && $pass1<11){
                        $t2 = mysqli_fetch_array($tot);
                        $q30 = $t2['pw'];
                        $hargabeli = format_rupiah($r['hrgsat_barang']);
                        $hargajual = format_rupiah($r['hrgjual_barang']);
                        $nilaibarang = format_rupiah($r['hrgsat_barang'] * $r[stok_barang]);
                        $nilaibarang2 = $r['hrgsat_barang'] * $r[stok_barang];
                        $totalbarang += $nilaibarang2;
                        $tb = format_rupiah($totalbarang);


                            echo "<tr class='warnabaris' >";
                            if( $pass1 <= "0"){
                                echo" <td style='background-color:#dd4b39;'>$no</td> ";                                                            }
                            elseif ($pass1 > "0" && $pass1 <= "5"){
                                echo"  <td style='background-color:#f39c12;'>$no</td>"; }
                            elseif ($pass1 > "0" && $pass1 <= "10"){
                                echo"  <td style='background-color:#00a65a;'>$no</td>"; }
                            elseif ($pass1 > "10" ){
                                echo"  <td style='background-color:#00c0ef;'>$no</td>"; }

                            echo"    											         
											 <td>$r[kd_barang]</td>
											 <td>$r[nm_barang]</td>
											 <td align=right>$r[stok_barang]</td>
											 <td align=right>$r[stok_buffer]</td>
											 <td align=right>$pass1</td>";

                            if($q30<"0")
                            {echo "<td align=right> 0 </td>";}
                            else {echo "<td align=right>$q30</td>";}

                            echo"											
											 <td align=center>$r[sat_barang]</td>
											
											 <td align=right>$hargabeli</td>
											 <td align=right> $nilaibarang </td>
											 <td><a href='?module=lapstok&act=edit&id=$r[kd_barang]' title='Riwayat' class='btn btn-warning btn-xs'>Riwayat</a> 
											 <!-- barang tidak boleh didelete
											 <a href=javascript:confirmdelete('$aksi?module=barang&act=hapus&id=$r[id_barang]') title='HAPUS' class='btn btn-danger btn-xs'>HAPUS</a>
											 -->
											</td>
											
											 
											</td>
										</tr>";
                            $no++;
                        }}

                    echo "</tbody>
                        <tr>
                            <td colspan='7'><h3><center>Total</center></h3>  </td>
                            <td colspan='3'><h3><strong> Rp. $tb  ,- </strong></h3></td> 
                        </tr>
                        </table>";

                    ?>
            </div>
        </div>
<?php

    break;
    case "slow" :

        $tampil_barang = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM barang ORDER BY barang.id_barang ");

        ?>


        <div class="box box-warning box-solid">
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
                    <a  class ='btn  btn-primary btn-flat' href='?module=lapstok&act=laku'>LAKU</a>
                    <a  class ='btn  btn-success btn-flat' href='?module=lapstok&act=lancar'>LANCAR</a>
                    <a  class ='btn  btn-warning btn-flat' href='?module=lapstok&act=default'>GLOBAL</a>
                    <a  class ='btn  btn-danger btn-flat' href='?module=lapstok&act=macet'>MACET</a>
                <br><br>


                <table id="example1" class="table table-bordered table-striped" >
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
                    $no=1;
                    while ($r=mysqli_fetch_assoc($tampil_barang)){
                        $t30 = $r[kd_barang];
                        $tgl_awal = date('Y-m-d');
                        $tgl_akhir = date('Y-m-d', strtotime('-30 days', strtotime( $tgl_awal)));
                        $hargabeli = format_rupiah($r['hrgsat_barang']);
                        $hargajual = format_rupiah($r['hrgjual_barang']);

                        $pass = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM trkasir JOIN trkasir_detail
                                        ON (trkasir.kd_trkasir=trkasir_detail.kd_trkasir)
                                        WHERE kd_barang = '$t30' AND (tgl_trkasir BETWEEN '$tgl_akhir' and '$tgl_awal')");
                        $pass1 = mysqli_num_rows($pass);

                        $pass2 = mysqli_fetch_array($pass);

                        $tot =mysqli_query($GLOBALS["___mysqli_ston"], "SELECT
                                        trkasir_detail.kd_barang,
                                        trkasir_detail.id_dtrkasir,
                                        trkasir_detail.kd_trkasir,
                                        SUM(trkasir_detail.qty_dtrkasir) as pw,
                                        trkasir.kd_trkasir,
                                        trkasir.tgl_trkasir                            
                                        FROM trkasir_detail 
                                        JOIN trkasir ON (trkasir_detail.kd_trkasir=trkasir.kd_trkasir)
                                        WHERE kd_barang = '$pass2[kd_barang]' AND (tgl_trkasir BETWEEN '$tgl_akhir' and '$tgl_awal')") ;

                        if($pass1>0 && $pass1<6){
                        $t2 = mysqli_fetch_array($tot);
                        $q30 = $t2['pw'];
                        $hargabeli = format_rupiah($r['hrgsat_barang']);
                        $hargajual = format_rupiah($r['hrgjual_barang']);
                        $nilaibarang = format_rupiah($r['hrgsat_barang'] * $r[stok_barang]);
                        $nilaibarang2 = $r['hrgsat_barang'] * $r[stok_barang];
                        $totalbarang += $nilaibarang2;
                        $tb = format_rupiah($totalbarang);


                            echo "<tr class='warnabaris' >";
                            if( $pass1 <= "0"){
                                echo" <td style='background-color:#dd4b39;'>$no</td> ";                                                            }
                            elseif ($pass1 > "0" && $pass1 <= "5"){
                                echo"  <td style='background-color:#f39c12;'>$no</td>"; }
                            elseif ($pass1 > "0" && $pass1 <= "10"){
                                echo"  <td style='background-color:#00a65a;'>$no</td>"; }
                            elseif ($pass1 > "10" ){
                                echo"  <td style='background-color:#00c0ef;'>$no</td>"; }

                            echo"    											         
											 <td>$r[kd_barang]</td>
											 <td>$r[nm_barang]</td>
											 <td align=right>$r[stok_barang]</td>
											 <td align=right>$r[stok_buffer]</td>
											 <td align=right>$pass1</td>";

                            if($q30<"0")
                            {echo "<td align=right> 0 </td>";}
                            else {echo "<td align=right>$q30</td>";}

                            echo"											
											 <td align=center>$r[sat_barang]</td>
											
											 <td align=right>$hargabeli</td>
											 <td align=right> $nilaibarang </td>
											 <td><a href='?module=lapstok&act=edit&id=$r[kd_barang]' title='Riwayat' class='btn btn-warning btn-xs'>Riwayat</a> 
											 <!-- barang tidak boleh didelete
											 <a href=javascript:confirmdelete('$aksi?module=barang&act=hapus&id=$r[id_barang]') title='HAPUS' class='btn btn-danger btn-xs'>HAPUS</a>
											 -->
											</td>
											
											 
											</td>
										</tr>";
                            $no++;
                        }}

                    echo "</tbody>
                        <tr>
                            <td colspan='7'><h3><center>Total</center></h3>  </td>
                            <td colspan='3'><h3><strong> Rp. $tb  ,- </strong></h3></td> 
                        </tr>
                        </table>";

                    ?>
            </div>
        </div>

<?php
    break;
    case "macet":

        $tampil_barang = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM barang ORDER BY barang.id_barang ");

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
                    <a  class ='btn  btn-primary btn-flat' href='?module=lapstok&act=laku'>LAKU</a>
                    <a  class ='btn  btn-success btn-flat' href='?module=lapstok&act=lancar'>LANCAR</a>
                    <a  class ='btn  btn-warning btn-flat' href='?module=lapstok&act=slow'>SLOW</a>
                    <a  class ='btn  btn-danger btn-flat' href='?module=lapstok&act=default'>GLOBAL</a>
                    <br><br>


                    <table id="example1" class="table table-bordered table-striped" >
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
                        $no=1;
                        while ($r=mysqli_fetch_assoc($tampil_barang)){
                            $t30 = $r[kd_barang];
                            $tgl_awal = date('Y-m-d');
                            $tgl_akhir = date('Y-m-d', strtotime('-30 days', strtotime( $tgl_awal)));
                            $hargabeli = format_rupiah($r['hrgsat_barang']);
                            $hargajual = format_rupiah($r['hrgjual_barang']);

                            $pass = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM trkasir JOIN trkasir_detail
                                        ON (trkasir.kd_trkasir=trkasir_detail.kd_trkasir)
                                        WHERE kd_barang = '$t30' AND (tgl_trkasir BETWEEN '$tgl_akhir' and '$tgl_awal')");
                            $pass1 = mysqli_num_rows($pass);

                            $pass2 = mysqli_fetch_array($pass);

                            $tot =mysqli_query($GLOBALS["___mysqli_ston"], "SELECT
                                        trkasir_detail.kd_barang,
                                        trkasir_detail.id_dtrkasir,
                                        trkasir_detail.kd_trkasir,
                                        SUM(trkasir_detail.qty_dtrkasir) as pw,
                                        trkasir.kd_trkasir,
                                        trkasir.tgl_trkasir                            
                                        FROM trkasir_detail 
                                        JOIN trkasir ON (trkasir_detail.kd_trkasir=trkasir.kd_trkasir)
                                        WHERE kd_barang = '$pass2[kd_barang]' AND (tgl_trkasir BETWEEN '$tgl_akhir' and '$tgl_awal')") ;

                            if($pass1<1){
                            $t2 = mysqli_fetch_array($tot);
                            $q30 = $t2['pw'];
                            $hargabeli = format_rupiah($r['hrgsat_barang']);
                            $hargajual = format_rupiah($r['hrgjual_barang']);
                            $nilaibarang = format_rupiah($r['hrgsat_barang'] * $r[stok_barang]);
                            $nilaibarang2 = $r['hrgsat_barang'] * $r[stok_barang];
                            $totalbarang += $nilaibarang2;
                            $tb = format_rupiah($totalbarang);


                                echo "<tr class='warnabaris' >";
                                if( $pass1 <= "0"){
                                    echo" <td style='background-color:#dd4b39;'>$no</td> ";                                                            }
                                elseif ($pass1 > "0" && $pass1 <= "5"){
                                    echo"  <td style='background-color:#f39c12;'>$no</td>"; }
                                elseif ($pass1 > "0" && $pass1 <= "10"){
                                    echo"  <td style='background-color:#00a65a;'>$no</td>"; }
                                elseif ($pass1 > "10" ){
                                    echo"  <td style='background-color:#00c0ef;'>$no</td>"; }

                                echo"    											         
											 <td>$r[kd_barang]</td>
											 <td>$r[nm_barang]</td>
											 <td align=right>$r[stok_barang]</td>
											 <td align=right>$r[stok_buffer]</td>
											 <td align=right>$pass1</td>";

                                if($q30<"0")
                                {echo "<td align=right> 0 </td>";}
                                else {echo "<td align=right>$q30</td>";}

                                echo"											
											 <td align=center>$r[sat_barang]</td>
											
											 <td align=right>$hargabeli</td>
											 <td align=right> $nilaibarang </td>
											 <td><a href='?module=lapstok&act=edit&id=$r[kd_barang]' title='Riwayat' class='btn btn-warning btn-xs'>Riwayat</a> 
											 <!-- barang tidak boleh didelete
											 <a href=javascript:confirmdelete('$aksi?module=barang&act=hapus&id=$r[id_barang]') title='HAPUS' class='btn btn-danger btn-xs'>HAPUS</a>
											 -->
											</td>
											
											 
											</td>
										</tr>";
                                $no++;
                            }}

                        echo "</tbody>
                        <tr>
                            <td colspan='7'><h3><center>Total</center></h3>  </td>
                            <td colspan='3'><h3><strong> Rp. $tb  ,- </strong></h3></td> 
                        </tr>
                        </table>";

                        ?>
            </div>
        </div>
<?php
        break;
  case "edit":
      $jokul = $_GET[id];

    $jual = "SELECT trkasir.tgl_trkasir,
	    trkasir.kd_trkasir,
		trkasir_detail.nmbrg_dtrkasir,
        trkasir_detail.sat_dtrkasir,
        trkasir_detail.qty_dtrkasir
        FROM trkasir_detail join trkasir on (trkasir_detail.kd_trkasir=trkasir.kd_trkasir)
        WHERE kd_barang =$jokul order by trkasir.tgl_trkasir desc " ;
    $penjualan=mysqli_query($GLOBALS["___mysqli_ston"], $jual);
  
   
	?>

		  <div class='box box-primary box-solid'>
				<div class='box-header with-border'>
					<h3 class='box-title'><?php echo "Riwayat Penjualan $w"; ?></h3>
					<div class='box-tools pull-right'>
						<button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
                    </div><!-- /.box-tools -->
				</div>

				<div class='box-body'>
                    <table id="example1" class="table table-condensed table-bordered table-striped table-hover">
						<thead>
							<tr>
								<th style="text-align: center; ">No Transaksi</th>
								<th style="text-align: center; ">Keterangan</th>
								<th style="text-align: center; ">Tanggal dan Waktu</th>
								<th style="text-align: center; ">Keluar</th>

							</tr>
                        </thead>
						<tbody>
                        <?php
                            while ($r=mysqli_fetch_array($penjualan)) {
                                echo "
                            <tr>
                                <td align=center>$r[kd_trkasir]</td>";
                            if($r[kd_trkasir]="TKP")
                                {echo "<td align=center>PENJUALAN</td>";}
                            else { echo "";}
                            echo"                                   
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
                trbmasuk_detail.nmbrg_dtrbmasuk,
                trbmasuk_detail.sat_dtrbmasuk,
                trbmasuk_detail.qty_dtrbmasuk
                FROM trbmasuk_detail join trbmasuk on (trbmasuk_detail.kd_trbmasuk=trbmasuk.kd_trbmasuk)
                WHERE kd_barang =$jokul order by trbmasuk.tgl_trbmasuk desc " ;
                $pembelian=mysqli_query($GLOBALS["___mysqli_ston"], $beli);
               

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
								<th style="text-align: center; ">Keterangan</th>
								<th style="text-align: center; ">Tanggal dan Waktu</th>
								<th style="text-align: center; ">Masuk</th>

							</tr>
                        </thead>
						<tbody>
                        <?php
                            while ($q=mysqli_fetch_array($pembelian)) {
                                echo "
                            <tr>
                                <td align=center >$q[kd_trbmasuk]</td>";
                            if($q[kd_trbmasuk]="BMP")
                                {echo "<td align=center>PEMBELIAN</td>";}
                            else { echo "";}
                            echo"                                   
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
 $(function(){
  $(".datepicker").datepicker({
      format: 'yyyy-mm-dd',
      autoclose: true,
      todayHighlight: true,
  });
 });
</script>