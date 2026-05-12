<?php
session_start();
if (empty($_SESSION['username']) and empty($_SESSION['passuser'])) {
    echo "<link href=../css/style.css rel=stylesheet type=text/css>";
    echo "<div class='error msg'>Untuk mengakses Modul anda harus login.</div>";
} else {

    switch ($_GET['act']) {
        default:

            ?>


            <div class="box box-primary box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">LAPORAN STOK OPNAME</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div><!-- /.box-tools
                    -->
                </div>
                <div class="box-body">

                    <!-- <form method="POST" action="modul/mod_laporan/tampil_lap_stokopname.php" target="_blank" enctype="multipart/form-data" class="form-horizontal"> -->
                    <form method="POST" action="?module=lapstokopname&act=laporanbulanan" enctype="multipart/form-data"
                        class="form-horizontal">


                        </br></br>


                        <div class="form-group">
                            <label class="col-sm-2 control-label">Tanggal Awal</label>
                            <div class="col-sm-4">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <span class="glyphicon glyphicon-th"></span>
                                    </div>
                                    <input type="text" required="required" class="datepicker" name="tgl_awal" id="tgl_awal" autocomplete="off">
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
                                    <input type="text" required="required" class="datepicker" name="tgl_akhir" id="tgl_akhir" autocomplete="off">
                                </div>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label class='col-sm-2 control-label'>WAKTU STOK OPNAME</label>
                            <div class='col-sm-3'>
                                <select name='shift' class='form-control' id="shift">
                                    <option value="0">SO BULANAN</option>
                                    <option value="1">Pagi</option>
                                    <option value="2">Sore</option>
                                    <option value="3">Malam</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label"></label>
                            <div class="buttons col-sm-6">
                                <input class="btn btn-primary" type="submit" name="btn"
                                    value="TAMPIL">&nbsp
                                    <a  class ='btn  btn-success' onclick='javascript:exportExcel()' target='_blank'><i class='fa fa-fw fa-file-excel-o'></i>EXPORT EXCEL</a>&nbsp
                                <a class='btn  btn-danger' href='?module=home'>KEMBALI</a>
                                
                            </div>
                        </div>

                    </form>
                </div>
<div>
     <div>
        <p style="text-align:center">Ringkasan Stok Opname 3 bulan yang lalu</p>
    </div>
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
							<tr>
                                <td>No.</td>
                                <td>Tanggal SO</td>
                                <td>Jenis SO</td>
                                <td>Minus</td>
                                <td>Lebih</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $tgl_awal = date('Y-m-d', time());                            
                            $tgl_akhir = date('Y-m-d', strtotime('-120 days', strtotime( $tgl_awal)));      
                            $so = $db->prepare("SELECT DISTINCT tgl_stokopname, shift FROM stok_opname
                                    WHERE tgl_stokopname BETWEEN '$tgl_akhir' AND '$tgl_awal'
                                    ORDER BY tgl_stokopname DESC");    
                            $so->execute();
                            while ($r = $so->fetch(PDO::FETCH_ASSOC)) {
                                if($r['shift']==0){
                                    $jenis="SO BULANAN";
                                }elseif($r['shift']==1){
                                    $jenis="PAGI";
                                }elseif($r['shift']==2){
                                    $jenis="SORE";
                                }elseif($r['shift']==3){
                                    $jenis="MALAM";
                                }
                                $minus = $db->prepare("select sum(ttl_hrgbrg) as min from stok_opname
                                            where ttl_hrgbrg<0 and shift='$shift' and tgl_stokopname='$r[tgl_stokopname]' ");
                                $minus->execute();
                                $kurang = $minus->fetch(PDO::FETCH_ASSOC);
                                $kurmin = format_rupiah(round($kurang['min'], 0));
                                
                                $lebih = $db->prepare("select sum(ttl_hrgbrg) as plus from stok_opname
                                            where ttl_hrgbrg>0 and shift='$shift' and tgl_stokopname='$r[tgl_stokopname]' ");
                                $lebih->execute();
                                $lbh = $lebih->fetch(PDO::FETCH_ASSOC);
                                $plus = format_rupiah(round($lbh['plus'], 0));
                                echo "
                                <tr>
                                    <td>$no</td>
                                    <td>$r[tgl_stokopname]</td>
                                    <td>$jenis</td>
                                    <td>$kurmin</td>
                                    <td>$plus</td>
                                </tr>
                                ";
                                $no++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <script>
                function exportExcel() {
                    let tgl_awal = $('#tgl_awal').val()
                    let tgl_akhir = $('#tgl_akhir').val()
                    let shift = $('#shift').val()

                    window.open('modul/mod_laporan/cetak_stokopname_excel.php?tgl_awal=' + tgl_awal + '&tgl_akhir=' + tgl_akhir +'&shift='+shift, '_blank');
                }
            </script>

            <?php
            break;
        case "laporanbulanan":
            $tgl_awal = $_POST['tgl_awal'];
            $tgl_akhir = $_POST['tgl_akhir'];
            $shift = $_POST['shift'];
            $so = $db->prepare("SELECT * FROM stok_opname 
	JOIN barang ON barang.id_barang = stok_opname.id_barang 
	LEFT JOIN admin ON admin.id_admin = stok_opname.id_admin WHERE stok_opname.shift='$shift' and stok_opname.tgl_stokopname ='$tgl_awal' ORDER BY barang.nm_barang ASC");
	        $so->execute();

            ?>
            <div class="box box-primary box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">LAPORAN STOK OPNAME TANGGAL <?= $tgl_awal ?> SD <?= $tgl_akhir ?></h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div><!-- /.box-tools -->
                </div>
                <?php
                echo "
                <a class='btn  btn-success btn-flat' target='_blank' href='?module=lapstokopname&act=sinkron_min&tgl_awal=$tgl_awal&tgl_akhir=$tgl_akhir&shift=$shift'>SINKRONISASI STOK MINUS</a> 
                <a class='btn  btn-success btn-warning' target='_blank' href='?module=lapstokopname&act=sinkron_plus&tgl_awal=$tgl_awal&tgl_akhir=$tgl_akhir&shift=$shift'>SINKRONISASI STOK PLUS</a>                 
                ";
                ?>
                <div class="box-body table-responsive">
                    <form action="modul/mod_barang/hapus_barang.php" method="post">



                        <table id="example2" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Petugas</th>
                                    <th style="text-align: left; ">Nama Barang</th>
                                    <th style="text-align: right; ">Satuan</th>
                                    <th style="text-align: center; ">Exp</th>
                                    <th style="text-align: right; ">JmlED</th>
                                    <th style="text-align: right; ">SS</th>
                                    <th style="text-align: center; ">SF</th>
                                    <th style="text-align: center; ">Hasil</th>
                                    <th style="text-align: right; ">Harga</th>
                                    <th style="text-align: right; ">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $no = 1;
                                while ($r = $so->fetch(PDO::FETCH_ASSOC)) {
                                    $hargabeli = format_rupiah($r['hrgsat_barang']);
                                    $total = format_rupiah($r['ttl_hrgbrg']);
                                    $petugas = $db->prepare("select nama_lengkap from admin where id_admin='$r[id_admin]'");
                                    $petugas->execute();
                                    $ptg = $petugas->fetch(PDO::FETCH_ASSOC);
                                    if ($r['ttl_hrgbrg'] < 0) {
                                        echo "
                                <tr style='background-color:#FF00FF'>
                                <td>$no</td>
                                <td>$ptg[nama_lengkap]</td>                               
                                <td>$r[nm_barang]</td>
                                <td>$r[sat_barang]</td>
                                <td>$r[exp_date]</td>
                                <td style='text-align: center;'>$r[jml]</td>
                                <td style='text-align: center;'>$r[stok_sistem]</td>
                                <td style='text-align: center;'>$r[stok_fisik]</td>
                                <td style='text-align: center;'>$r[selisih]</td>                                
                                <td style='text-align: right;'>$hargabeli</td>
                                <td style='text-align: right;font-weight:bold;'>$total</td>
                                </tr>
                                ";
                                    } else {
                                        echo "
                                <tr>
                                <td>$no</td>
                                <td>$ptg[nama_lengkap]</td>                               
                                <td>$r[nm_barang]</td>
                                <td>$r[sat_barang]</td>
                                <td>$r[exp_date]</td>
                                <td style='text-align: center;'>$r[jml]</td>
                                <td style='text-align: center;'>$r[stok_sistem]</td>
                                <td style='text-align: center;'>$r[stok_fisik]</td>
                                <td style='text-align: center;'>$r[selisih]</td>                                
                                <td style='text-align: right;'>$hargabeli</td>
                                <td style='text-align: right;font-weight:bold;'>$total</td>
                                </tr>
                                ";
                                    }

                                    $no++;
                                }
                                ?>
                            </tbody>
                            <tfoot style="background-color:aqua; font-weight:bold; font-size:large;">
                                <?php
                                $minus = $db->prepare("select sum(ttl_hrgbrg) as min from stok_opname
                                        where ttl_hrgbrg<0 and shift='$shift' and tgl_stokopname between '$tgl_awal' and '$tgl_akhir' ");
                                $minus->execute();
                                $kurang = $minus->fetch(PDO::FETCH_ASSOC);
                                $kurmin = format_rupiah(round($kurang['min'], 0));

                                $lebih = $db->prepare("select sum(ttl_hrgbrg) as plus from stok_opname
                                        where ttl_hrgbrg>0 and shift='$shift' and tgl_stokopname between '$tgl_awal' and '$tgl_akhir' ");
                                $lebih->execute();
                                $lbh = $lebih->fetch(PDO::FETCH_ASSOC);
                                $plus = format_rupiah(round($lbh['plus'], 0));


                                echo "
                                <tr>
                                    <td colspan='9' style='text-align: right;' >Total Barang Minus</td>
                                    <td colspan='2' style='text-align: right;'>Rp. &nbsp; $kurmin</td>
                                </tr>
                                <tr>
                                    <td colspan='9' style='text-align: right;'>Total Barang Lebih</td>
                                    <td colspan='2' style='text-align: right;'>Rp. &nbsp; $plus</td>
                                </tr>
                                ";
                                ?>
                            </tfoot>

                        </table>
                    </form>
                </div>
            </div>
            <?PHP
            break;
        case "sinkron_min":
            $tgl_awal = $_GET['tgl_awal'];
            $tgl_akhir = $_GET['tgl_akhir'];
            $shift = $_GET['shift'];
            $kdunik = date('dmyHis');
            $kdtransaksi = "MINUS-" . $kdunik;
            $petugas = $_SESSION['namalengkap'];

            $sominus = $db->prepare("SELECT * FROM stok_opname 
                WHERE ttl_hrgbrg<0 AND shift='$shift' AND tgl_stokopname BETWEEN '$tgl_awal' AND '$tgl_akhir' ");
            $sominus->execute();
            $no1 = 1;
            $tgl_sekarang = date("Y-m-d H:i:s", time());
            
            while ($so = $sominus->fetch(PDO::FETCH_ASSOC)) {
                $barang = $db->prepare("SELECT nm_barang,sat_barang,hrgjual_barang FROM barang WHERE id_barang=?");
                $barang->execute([$so['id_barang']]);
                $brg = $barang->fetch(PDO::FETCH_ASSOC);
                $qtymin = abs($so['selisih']);
                $hrgtot = $brg['hrgjual_barang'] * $qtymin;
                
                $inserttrkasir = $db->prepare("INSERT INTO trkasir_detail(
                                            kd_trkasir,
                                            id_barang,
                                            kd_barang,
                                            nmbrg_dtrkasir,
                                            qty_dtrkasir,
                                            sat_dtrkasir,
                                            hrgjual_dtrkasir,
                                            hrgttl_dtrkasir,
                                            waktu)
                                        VALUES(?,?,?,?,?,?,?,?,?)");
                $inserttrkasir->execute([$kdtransaksi, $so['id_barang'], $so['kd_barang'], $brg['nm_barang'], $qtymin, $brg['sat_barang'], $brg['hrgjual_barang'], $hrgtot,$tgl_sekarang]);
                $no1++;
            }

            $tglharini = date('Y-m-d');
            $cekshift = $db->prepare("SELECT * FROM waktukerja WHERE tanggal = '$tglharini'
                            AND status='ON' ");
            $cekshift->execute();
            $sshift = $cekshift->fetch(PDO::FETCH_ASSOC);
            $shiftin = $sshift['shift'];

            $db->prepare("INSERT INTO trkasir(
                                kd_trkasir,
                                petugas,
                                shift,
                                tgl_trkasir,
                                nm_pelanggan,
                                id_carabayar)
                            VALUES(?,?,?,?,?,?)
            ")->execute([$kdtransaksi, $petugas, $shiftin, $tglharini, 'SINKRONISASI MINUS', '1']);

            echo '<script>window.location.href = "?module=trkasir";</script>';
            break;
        case "sinkron_plus":
            $tgl_awal = $_GET['tgl_awal'];
            $tgl_akhir = $_GET['tgl_akhir'];
            $shift = $_GET['shift'];
            $kdunik = date('dmyHis');
            $kdtransaksi = "PLUS-" . $kdunik;
            $petugas = $_SESSION['namalengkap'];

            $soplus = $db->prepare("SELECT * FROM stok_opname 
                WHERE selisih>0 AND shift='$shift' AND tgl_stokopname BETWEEN '$tgl_awal' AND '$tgl_akhir' ");
            $soplus->execute();
            $no1 = 1;
            $tgl_sekarang = date("Y-m-d H:i:s", time());
            while ($so = $soplus->fetch(PDO::FETCH_ASSOC)) {
                $barang = $db->prepare("SELECT nm_barang,sat_barang FROM barang WHERE id_barang=?");
                $barang->execute([$so['id_barang']]);
                $brg = $barang->fetch(PDO::FETCH_ASSOC);

                $inserttrbmasuk = $db->prepare("INSERT INTO trbmasuk_detail(
                                            kd_trbmasuk,
                                            id_barang,
                                            kd_barang,
                                            nmbrg_dtrbmasuk,
                                            qty_dtrbmasuk,
                                            sat_dtrbmasuk,
                                            hrgsat_dtrbmasuk,
                                            hrgttl_dtrbmasuk,
                                            waktu)
                                        VALUES(?,?,?,?,?,?,?,?,?)");
                $inserttrbmasuk->execute([$kdtransaksi, $so['id_barang'], $so['kd_barang'], $brg['nm_barang'], $so['selisih'], $brg['sat_barang'], $so['hrgsat_barang'], $so['ttl_hrgbrg'],$tgl_sekarang]);
                $no1++;
            }

            $tglharini = date('Y-m-d');

            $db->prepare("INSERT INTO trbmasuk(
                                id_resto,
                                kd_trbmasuk,
                                petugas,                                
                                tgl_trbmasuk,
                                id_supplier,
                                nm_supplier,
                                carabayar,
                                jenis)
                            VALUES(?,?,?,?,?,?,?,?)
            ")->execute(['pusat', $kdtransaksi, $petugas, $tglharini, '0', 'SINKRONISASI PLUS', 'TUNAI', 'nonpbf']);
            echo '<script>window.location.href = "?module=byrkredit";</script>';
            break;
    }
}

?>


<script type="text/javascript">
    $(function () {
        $(".datepicker").datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
        });
    });
</script>