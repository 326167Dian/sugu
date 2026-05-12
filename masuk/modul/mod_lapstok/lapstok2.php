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
                    <center><button type="button" class="btn btn-info">LAKU</button>
                        <button type="button" class="btn btn-success">LANCAR </button>
                        <button type="button" class="btn btn-warning">SLOW</button>
                        <button type="button" class="btn btn-danger">MACET</button></center>
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
	
	case "tambah":
       
        echo "
		  <div class='box box-primary box-solid'>
				<div class='box-header with-border'>
					<h3 class='box-title'>TAMBAH DATA BARANG</h3>
					<div class='box-tools pull-right'>
						<button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
                    </div><!-- /.box-tools -->
				</div>
				<div class='box-body'>
				
						<form method=POST action='$aksi?module=barang&act=input_barang' enctype='multipart/form-data' class='form-horizontal'>
						
						<input type=hidden name='id_supplier' id='id_supplier'>
							  							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Kode Barang</label>        		
									 <div class='col-sm-3'>
										<input type=text name='kd_barang' class='form-control' autocomplete='off'>
									 </div>
							  </div>
							  
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Nama Barang</label>        		
									 <div class='col-sm-4'>
										<input type=text name='nm_barang' class='form-control' required='required' autocomplete='off'>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Qty/Stok</label>        		
									 <div class='col-sm-3'>
										<input type=number name='stok_barang' class='form-control' required='required' autocomplete='off'>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Stok Buffer</label>        		
									 <div class='col-sm-3'>
										<input type=number name='stok_buffer' class='form-control' required='required' autocomplete='off'>
									 </div>
							  </div>
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Satuan</label>        		
									 <div class='col-sm-3'>
										<select name='sat_barang' class='form-control' >";
											 $tampil=mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM satuan ORDER BY nm_satuan ASC");
											 while($rk=mysqli_fetch_array($tampil)){
											 echo "<option value=$rk[nm_satuan]>$rk[nm_satuan]</option>";
											 }
										echo "</select>
									 </div>
							  </div> 
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Jenis Obat</label>        		
									 <div class='col-sm-3'>
										<select name='jenis_obat' class='form-control' >";
											 $tampil=mysqli_query($GLOBALS["___mysqli_ston"], "SELECT * FROM jenis_obat ORDER BY jenisobat ASC");
											 while($rk=mysqli_fetch_array($tampil)){
											 echo "<option value=$rk[jenisobat]>$rk[jenisobat]</option>";
											 }
										echo "</select>
									 </div>
							  </div>
							  
							  

							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Harga Beli</label>        		
									 <div class='col-sm-3'>
										<input type=number name='hrgsat_barang' class='form-control' required='required' autocomplete='off'>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Harga Jual</label>        		
									 <div class='col-sm-3'>
										<input type=number name='hrgjual_barang' class='form-control' required='required' autocomplete='off'>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Expired Date</label>
										<div class='col-sm-4'>
											<div class='input-group date'>
												<div class='input-group-addon'>
													<span class='glyphicon glyphicon-th'></span>
												</div>
													<input type='text' class='datepicker' name='tgl_expired' required='required' autocomplete='off'>
											</div>
										</div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'>Keterangan Lain</label>        		
									 <div class='col-sm-4'>
										<textarea name='ket_barang' class='form-control' rows='3'></textarea>
									 </div>
							  </div>
							  
							  <div class='form-group'>
									<label class='col-sm-2 control-label'></label>       
										<div class='col-sm-4'>
											<input class='btn btn-primary' type=submit value=SIMPAN>
											<input class='btn btn-danger' type=button value=BATAL onclick=self.history.back()>
										</div>
								</div>
								
							  </form>
							  
				</div> 
				
			</div>";
					
	
    break;

  case "edit":

      $jokul = $_GET[id];
        $namabrg = mysqli_query($GLOBALS["___mysqli_ston"],"select * from barang where kd_barang='$jokul'");
        $p= mysqli_fetch_array($namabrg);
        $w=$p[nm_barang];

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
                               

                            </tr>
                            ";
                                                            }
                            echo "<tr> 
                                    <td></td>
                                  </tr> 
                              
                            ";
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