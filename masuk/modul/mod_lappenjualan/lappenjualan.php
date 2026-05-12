<?php
// session_start(); // Sudah aktif di media_admin.php
if (empty($_SESSION['username']) AND empty($_SESSION['passuser'])){
    echo "<link href=../css/style.css rel=stylesheet type=text/css>";
    echo "<div class='error msg'>Untuk mengakses Modul anda harus login.</div>";
}
else{

    switch($_GET['act']){
        default:

            ?>


            <div class="box box-primary box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">LAPORAN PENJUALAN PRODUK</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div><!-- /.box-tools
                    -->
                </div>
                <div class="box-body">

                    <form method="POST" action="?module=lappenjualan&act=view" target="_blank" enctype="multipart/form-data" class="form-horizontal">

                        </br></br>


                        <div class="form-group">
                            <label class="col-sm-2 control-label">Tanggal Awal</label>
                            <div class="col-sm-4">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <span class="glyphicon glyphicon-th"></span>
                                    </div>
                                    <input type="text" required="required" class="datepicker" id="tgl_awal" name="tgl_awal" autocomplete="off">
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
                                    <input type="text" required="required" class="datepicker" id="tgl_akhir" name="tgl_akhir" autocomplete="off">
                                </div>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label class='col-sm-2 control-label'>SHIFT</label>
                            <div class='col-sm-3'>
                                <select name='shift' class='form-control' id="shift" >
                                    <option value="1">Pagi</option>
                                    <option value="2">Sore</option>
                                    <option value="3">Malam</option>
                                    <option value= "4">Semua Shift</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label"></label>
                            <div class="buttons col-sm-4">
                                <input class="btn btn-primary" type="submit" name="btn" value="TAMPIL">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                <a  class ='btn  btn-success' onclick='javascript:exportExcel()' target='_blank'><i class='fa fa-fw fa-file-excel-o'></i>EXPORT EXCEL</a>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                <a  class ='btn  btn-danger' href='?module=home'>KEMBALI</a>
                            </div>
                        </div>

                    </form>
                </div>

            </div>

            <script>
                function exportExcel(){
                    let tgl_awal = $('#tgl_awal').val()
                    let tgl_akhir = $('#tgl_akhir').val()
                    let shift = $('#shift').val()

                    window.open('modul/mod_lappenjualan/lappenjualan_excel.php?tgl_awal='+tgl_awal+'&tgl_akhir='+tgl_akhir+'&shift='+shift, '_blank');
                }
            </script>


            <?php

            break;
            case "view":

                $tgl_awal  = $_POST['tgl_awal'];
                $tgl_akhir = $_POST['tgl_akhir'];
                $shift     = $_POST['shift'];
                if ($_POST['shift']<4){
	                $shift = $_POST['shift'];
                    
                } else {
                    $shift=("1,2,3");
                }
                ?>

                <div class="box box-primary box-solid table-responsive">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            TAMPIL PENJUALAN PRODUK SHIFT <?php echo $shift; ?> TANGGAL <?php echo $tgl_awal; ?> s/d <?php echo $tgl_akhir; ?>
                        </h3>
                        <div class="box-tools pull-right">
                            <button class="btn btn-box-tool" data-widget="collapse">
                                <i class="fa fa-minus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="box-body">

                        <?php
                        $tabelbesar = $db->prepare("
                            SELECT * FROM trkasir WHERE shift in ($shift) AND tgl_trkasir BETWEEN '$tgl_awal' AND '$tgl_akhir'
                        ");
                        $tabelbesar->execute();

                        $no = 1;

                        while ($rb = $tabelbesar->fetch(PDO::FETCH_ASSOC)) {
                            $carabayar = $db->prepare("SELECT * FROM carabayar WHERE id_carabayar='$rb[id_carabayar]'");  
                            $carabayar->execute();
                            $bc = $carabayar->fetch(PDO::FETCH_ASSOC);
                            
                            $waktu  = date('d-F-Y H:i:s', strtotime($rb['waktu_trx']));
                            
                            echo "
                            <table style='margin-bottom:10px;'>
                                <tr><td style='width:40%;'>No</td><td>:</td><td>$no</td></tr>
                                <tr><td>Nama Pelanggan</td><td>:</td><td>$rb[nm_pelanggan]</td></tr>
                                <tr><td>Kode Transaksi</td><td>:</td><td>$rb[kd_trkasir]</td></tr>
                                <tr><td>Metode Bayar</td><td>:</td><td>$bc[nm_carabayar]</td></tr>                                
                                <tr><td>Waktu Transaksi</td><td>:</td><td>$waktu</td></tr>                                
                            </table>
                            ";

                            echo "
                            <table class='table table-bordered table-striped'>
                                <thead>
                                    <tr>
                                        <th style='text-align:center;'>No</th>
                                        <th style='text-align:center;'>Nama Barang</th>
                                        <th style='text-align:center;'>Qty</th>
                                        <th style='text-align:center;'>Satuan</th>
                                        <th style='text-align:center;'>Harga Jual</th>
                                        <th style='text-align:center;'>Total Harga</th>
                                    </tr>
                                </thead>
                                <tbody>
                            ";

                            $no2 = 1;
                            $tabelkecil = $db->prepare("
                                SELECT * FROM trkasir_detail 
                                WHERE kd_trkasir = '$rb[kd_trkasir]'
                            ");
                            $tabelkecil->execute();

                            while ($rk = $tabelkecil->fetch(PDO::FETCH_ASSOC)) {
                                $harga_jual = format_rupiah($rk['hrgjual_dtrkasir']);
                                $subtotal = format_rupiah($rk['qty_dtrkasir'] * $rk['hrgjual_dtrkasir']);                              
                                echo "
                                    <tr>
                                        <td>$no2</td>
                                        <td>$rk[nmbrg_dtrkasir]</td>
                                        <td style='text-align:center;'>$rk[qty_dtrkasir]</td>
                                        <td style='text-align:center;'>$rk[sat_dtrkasir]</td>
                                        <td style='text-align:right;'>$harga_jual</td>
                                        <td style='text-align:right;'>$subtotal</td>
                                    </tr>
                                ";

                                $no2++;
                            }

                            echo "
                                </tbody>
                            ";

                            $diskon1 = $db->prepare("select sum(hrgttl_dtrkasir) as total1 from trkasir_detail where kd_trkasir='$rb[kd_trkasir]'");
                            $diskon1->execute();
                            $diskon2 = $diskon1->fetch(PDO::FETCH_ASSOC);
                            $diskon = $diskon2['total1'] - $rb['ttl_trkasir'];
                            echo "    
                               
                                    <tr>
                                        <th colspan='5' style='text-align:center;'>Sub Total</th>
                                        <th style='text-align:right;'>" . format_rupiah($diskon2['total1']) . "</th>
                                    </tr>
                                    <tr>
                                        <th colspan='5' style='text-align:center;'>Diskon</th>
                                        <th style='text-align:right;'>" . format_rupiah($diskon) . "</th>
                                    </tr>
                                    <tr>
                                        <th colspan='5' style='text-align:center;'>TOTAL</th>
                                        <th style='text-align:right;'>" . format_rupiah($rb['ttl_trkasir']) . "</th>
                                    </tr>
                               
                            </table>
                            <hr>
                            ";

                            $no++;
                        }
                        ?>

                    </div>
                </div>

                <?php
              $tamtot = $db->prepare("select * from carabayar ");
              $tamtot->execute();
              $no3 = 1;
            //   if ($_POST['shift']<4){
	           // $shift = $_POST['shift'];
                  
            //   } else {
            //     $shift = ("1,2,3");
            //   }
              
            //     $grandtotal = 0;
            //     while ($tt=mysqli_fetch_array($tamtot)){
            //     $tcb= $db->query( "SELECT id_trkasir, kd_trkasir, SUM(ttl_trkasir) as ttlskrg1
            //                             FROM trkasir WHERE shift in ($shift) and tgl_trkasir  BETWEEN '$tgl_awal' AND '$tgl_akhir' 
            //                             AND id_carabayar='$tt[id_carabayar]'   ");
            //     $tamtcb = $tcb->fetch_array();
            //     $dtamtcb = format_rupiah($tamtcb['ttlskrg1']);
            //     echo "
            //         <p style='font-weight:bold;'>Pembayaran $tt[nm_carabayar] : Rp. $dtamtcb </p>
            //     ";  
            //     $no3++;
            //     // $grandtotal += $tamtcb['ttlskrg1'];
            //     $grandtotal = $grandtotal + $tamtcb['ttlskrg1'];
            //     }
            //     $dtgrandtotal = format_rupiah($grandtotal);
            //     echo "
            //         <p style='font-weight:bold; font-size:16px;'>GRAND TOTAL PENJUALAN SHIFT $shift: Rp. $dtgrandtotal </p>";
            
            // Ambil shift
            if (isset($_POST['shift']) && $_POST['shift'] < 4) {
                $shiftArr = [(int)$_POST['shift']];
            } else {
                $shiftArr = [1,2,3];
            }
            $shiftList = implode(',', $shiftArr);
            
            // Query gabungan
            $sql = "
                SELECT 
                    cb.nm_carabayar,
                    SUM(tk.ttl_trkasir) AS total
                FROM carabayar cb
                LEFT JOIN trkasir tk 
                    ON tk.id_carabayar = cb.id_carabayar
                    AND tk.shift IN ($shiftList)
                    AND tk.tgl_trkasir BETWEEN '$tgl_awal' AND '$tgl_akhir'
                GROUP BY cb.id_carabayar
            ";
            
            $result = $db->prepare($sql);
            $result->execute();
            $grandtotal = 0;
            
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                $total = $row['total'] ?? 0;
                $grandtotal += $total;
            
                echo "<p style='font-weight:bold;'>
                        Pembayaran {$row['nm_carabayar']} : Rp. " . format_rupiah($total) . "
                      </p>";
            }
            
            echo "<p style='font-weight:bold; font-size:16px;'>
                    GRAND TOTAL PENJUALAN SHIFT ".implode(',',$shiftArr)." : Rp. ".format_rupiah($grandtotal)."
                  </p>";

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
