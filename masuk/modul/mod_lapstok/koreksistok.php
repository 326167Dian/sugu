<?php
session_start();
 if (empty($_SESSION['username']) AND empty($_SESSION['passuser'])){
  echo "<link href=../css/style.css rel=stylesheet type=text/css>";
  echo "<div class='error msg'>Untuk mengakses Modul anda harus login.</div>";
}
else{

$aksi="modul/mod_lapstok/aksi_barang.php";
$aksi_barang = "masuk/modul/mod_lapstok/aksi_barang.php";
switch($_GET['act']){
  // Tampil barang
  default:

  
      $tampil_barang = $db->prepare("SELECT * FROM barang ORDER BY barang.id_barang");
      $tampil_barang->execute();
      
	  ?>
			
			
			<div class="box box-primary box-solid table-responsive">
				<div class="box-header with-border">
					<h3 class="box-title">KOREKSI STOK</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						
                    </div><!-- /.box-tools -->
				</div>
				<div class="box-body">
					<a  class ='btn  btn-success btn-flat' href='modul/mod_lapstok/sinkronisasi_stok.php'>SINKRONISASI</a>
                        <a class='btn  btn-danger btn-flat' href='?module=koreksistok&act=edit'>KOREKSI STOK AWAL</a>
                       
					<table id="example1" class="table table-bordered table-striped" >
						<thead>
							<tr>
								<th>No</th>
								<th>Kode</th>
								<th>Nama Barang</th>
								<th style="text-align: right; ">Masuk</th>
								<th style="text-align: right; ">Keluar</th>
								<th style="text-align: center; ">Selisih</th>
								<th style="text-align: center; ">Stok</br>Barang</th>
								<th width="70">Koreksi Stok</th>
							</tr>
						</thead>
						<tbody>
						<?php 
							$no=1;
							while ($r = $tampil_barang->fetch(PDO::FETCH_ASSOC)){

                                $beli = "SELECT trbmasuk.tgl_trbmasuk,                                           
                                       SUM(trbmasuk_detail.qty_dtrbmasuk) AS totalbeli                                            
                                       FROM trbmasuk_detail join trbmasuk 
                                       on (trbmasuk_detail.kd_trbmasuk=trbmasuk.kd_trbmasuk)
                                       WHERE kd_barang = ?" ;
                                $buy = $db->prepare($beli);
                                $buy->execute([$r['kd_barang']]);
                                $buy2 = $buy->fetch(PDO::FETCH_ASSOC);

                                $jual = "SELECT trkasir.tgl_trkasir,                                
                                        sum(trkasir_detail.qty_dtrkasir) AS totaljual
                                        FROM trkasir_detail join trkasir 
                                        on (trkasir_detail.kd_trkasir=trkasir.kd_trkasir)
                                        WHERE kd_barang = ?" ;
                                $jokul = $db->prepare($jual);
                                $jokul->execute([$r['kd_barang']]);
                                $sell = $jokul->fetch(PDO::FETCH_ASSOC);
                                $selisih = $buy2['totalbeli']-$sell['totaljual'];


									echo "<tr class='warnabaris' >
                                             <td>$no</td>                                    										     
											 <td>$r[kd_barang]</td>
											 <td>$r[nm_barang]</td>";
									if($buy2['totalbeli']<"0")
                                    {echo"<td align=center> 0 </td>";}
									else{echo "<td align=center>$buy2[totalbeli]</td>";}

									if($sell['totaljual']<"0")
                                    {echo"<td align=center> 0 </td>";}
									else{echo "<td align=center>$sell[totaljual]</td>";}
									echo" <td align=center>$selisih</td>";

									if($selisih==$r['stok_barang'])
                                        {echo "<td align=right>$r[stok_barang]</td>";}
									else{echo"<td style='background-color:#ffbf00; text-align: right;'>$r[stok_barang]</td>";}
									echo"	 	
											 <td style='text-align: center;'><a href='?module=koreksistok&act=edit&id=$r[id_barang]' title='EDIT' class='btn btn-primary btn-xs'>KOREKSI</a> 	
											</td>
										</tr>";
								$no++;
							}



						echo "</tbody></table>";
					    ?>
				</div>
			</div>	
             

<?php

	
    break;

  case "edit":
      $tampil_barang = $db->prepare("SELECT * FROM barang ORDER BY barang.id_barang ");
      $tampil_barang->execute();
      ?>

    <div class="box box-success box-solid table-responsive">
				<div class="box-header with-border">
					<h3 class="box-title">KOREKSI STOK AWAL</h3>
					<div class="box-tools pull-right">
						<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						
                    </div><!-- /.box-tools -->
				</div>
				<div class="box-body">
                        <a class='btn  btn-primary btn-flat' href='modul/mod_lapstok/sinkronisasi_stok_awal.php'>SINKRONISASI STOK AWAL</a>
                        <input class='btn btn-danger' type='button' value=KEMBALI onclick=self.history.back()>
                        <BR> KOREKSI STOK AWAL HANYA BISA <STRONG>SEKALI</STRONG> SETELAH SEMUA ITEM DIINPUT DENGAN LENGKAP
					<table id="example1" class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>No</th>
								<th>Kode</th>
								<th>Nama Barang</th>
								<th style="text-align: center; ">Penjualan lebih cepat</th>
								<th style="text-align: center; ">Stok Masuk</th>
								<th style="text-align: center; ">Penjualan Setelah<br> Stok Masuk</th>
								<th style="text-align: center; ">Stok Barang Real</th>
								<th width="70">Koreksi Stok</th>
							</tr>
						</thead>
						<tbody>
							<?php
							$no = 1;
							while ($r = $tampil_barang->fetch(PDO::FETCH_ASSOC)) {
                                $r1 = $r['kd_barang'];
                                $tampilmasuk = $db->prepare("SELECT SUM(qty_dtrbmasuk) AS subtotal,MIN(waktu) AS masukawal FROM trbmasuk_detail
                                                            WHERE kd_barang = ?");
                                $tampilmasuk->execute([$r1]);
                                $masuk = $tampilmasuk->fetch(PDO::FETCH_ASSOC);
                                $masuk1 = $masuk['subtotal'];
                                $masuk2 = $masuk['masukawal'];

                                $tampilkeluar = $db->prepare("SELECT MIN(waktu) AS keluarawal, MAX(waktu) AS keluarakhir FROM trkasir_detail 
                                                            WHERE kd_barang = ?");
                                $tampilkeluar->execute([$r1]);
                                $keluar = $tampilkeluar->fetch(PDO::FETCH_ASSOC);
                                $keluar1= $keluar['keluarawal'];
                                $keluar2= $keluar['keluarakhir'];

                                if ($keluar1<$masuk2)
                                  { $patokan = $masuk2;}
                                else
                                  {$patokan = $keluar1;}
                                $transaksi_atas = $db->prepare("SELECT SUM(qty_dtrkasir) AS qty_atas FROM trkasir_detail 
                                                                WHERE kd_barang = ? 
                                                                AND waktu BETWEEN ? AND ?");
                                $transaksi_atas->execute([$r1, $keluar1, $masuk2]);
                                $qty_atas2= $transaksi_atas->fetch(PDO::FETCH_ASSOC);
                                $qty_atas3= $qty_atas2['qty_atas'];

                                $transaksi_bawah = $db->prepare("SELECT SUM(qty_dtrkasir) AS qty_bawah FROM trkasir_detail 
                                                                WHERE kd_barang = ? 
                                                                AND waktu BETWEEN ? AND ?");
                                $transaksi_bawah->execute([$r1, $masuk2, $keluar2]);
                                $qty_bawah2= $transaksi_bawah->fetch(PDO::FETCH_ASSOC);
                                $qty_bawah3= $qty_bawah2['qty_bawah'];
                                $stok_real = $qty_atas3 + $masuk1 - ($qty_atas3+$qty_bawah3);

								echo "<tr class='warnabaris' >
                                             <td>$no</td>                                    										     
											 <td>$r[kd_barang]</td>
											 <td>$r[nm_barang]</td>
											 <td align='center'>$qty_atas3</td>
											 <td align='center'>$masuk1</td>
											 <td align='center'>$qty_bawah3</td>
											 <td align='center'>$stok_real</td>
											 <td style='text-align: center;'><a href='?module=koreksistok&act=edit&id=$r[id_barang]' title='EDIT' class='btn btn-primary btn-xs'>KOREKSI</a> 	
											</td>
										</tr>";
								$no++;
							}



							echo "</tbody></table>";
							?>
				</div>
			</div>

<?php

    
    break;


}
}
?>



