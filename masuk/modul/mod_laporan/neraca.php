<?php
session_start();
if (empty($_SESSION['username']) and empty($_SESSION['passuser'])) {
    echo "<link href='../css/style.css' rel='stylesheet' type='text/css'>";
    echo "<div class='error msg'>Untuk mengakses Modul anda harus login.</div>";
} else {

    switch ($_GET['act']) {
        default:

?>


            <div class="box box-primary box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">NERACA LABA RUGI</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div><!-- /.box-tools -->
                </div>
                <div class="box-body">

                    <form method="POST" action="?module=neraca&act=tes" target="_blank" enctype="multipart/form-data" class="form-horizontal">

                        </br></br>


                        <div class="form-group">
                            <label class="col-sm-2 control-label">Tanggal Awal</label>
                            <div class="col-sm-4">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <span class="glyphicon glyphicon-th"></span>
                                    </div>
                                    <input type="text" class="datepicker" name="tgl_awal" required="required" autocomplete="off" id="awal">
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
                                    <input type="text" class="datepicker" name="tgl_akhir" required="required" autocomplete="off" id="akhir">
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

                        <div class="table-responsive">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th colspan="3">NERACA LABA RUGI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center" width="50px">1.</td>
                                        <td width="250px">Penjualan</td>
                                        <td>Rp 0</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center" width="50px">2.</td>
                                        <td width="250px">Pembelian Cash</td>
                                        <td>Rp 0</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">3.</td>
                                        <td>Piutang<br>Total Penjualan Belum Dibayar.</td>
                                        <td>Rp 0</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">4.</td>
                                        <td>Hutang<br>Total Pembelian Belum Dibayar.</td>
                                        <td>Rp 0</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center" width="50px">5.</td>
                                        <td width="250px">Total Asset Lancar</td>
                                        <td>Rp 0</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">6.</td>
                                        <td>Total Asset Tidak Lancar</td>
                                        <td>Rp 0</td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">7.</td>
                                        <td>Neraca Laba/Rugi</td>
                                        <td>Rp 0</td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                        <hr>

                    </form>
                </div>

            </div>


        <?php

            break;
        case "tes":
            $d = $_GET['range'];
            $de = explode("/", $d);
            $awal = date("Y-m-d", strtotime($de[0]));
            $akhir = date("Y-m-d", strtotime($de[1]));

            // Query total penjualan dari trkasir_detail
            $query1 = $db->prepare("SELECT SUM(trkasir_detail.hrgttl_dtrkasir) AS penjualan FROM trkasir_detail 
                        JOIN trkasir ON trkasir_detail.kd_trkasir = trkasir.kd_trkasir
                        WHERE trkasir.tgl_trkasir BETWEEN '" . $awal . "' AND '" . $akhir . "'");
            $query1->execute();

            // Query untuk penjualan Reguler (tipe = 1)
            $query_reguler = $db->prepare("SELECT SUM(trkasir_detail.hrgttl_dtrkasir) AS reguler FROM trkasir_detail 
                        JOIN trkasir ON trkasir_detail.kd_trkasir = trkasir.kd_trkasir
                        WHERE trkasir.tgl_trkasir BETWEEN '" . $awal . "' AND '" . $akhir . "' AND trkasir_detail.tipe = '1'");
            $query_reguler->execute();

            // Query untuk penjualan Member (tipe = 2)
            $query_member = $db->prepare("SELECT SUM(trkasir_detail.hrgttl_dtrkasir) AS member FROM trkasir_detail 
                        JOIN trkasir ON trkasir_detail.kd_trkasir = trkasir.kd_trkasir
                        WHERE trkasir.tgl_trkasir BETWEEN '" . $awal . "' AND '" . $akhir . "' AND trkasir_detail.tipe = '2'");
            $query_member->execute();

            // Query untuk penjualan Marketplace (tipe = 3)
            $query_marketplace = $db->prepare("SELECT SUM(trkasir_detail.hrgttl_dtrkasir) AS marketplace FROM trkasir_detail 
                        JOIN trkasir ON trkasir_detail.kd_trkasir = trkasir.kd_trkasir
                        WHERE trkasir.tgl_trkasir BETWEEN '" . $awal . "' AND '" . $akhir . "' AND trkasir_detail.tipe = '3'");
            $query_marketplace->execute();
            // $query1 = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT SUM(trkasir_detail.hrgttl_dtrkasir) AS penjualan FROM trkasir_detail 
            // JOIN trkasir ON trkasir_detail.kd_trkasir = trkasir.kd_trkasir WHERE trkasir.tgl_trkasir BETWEEN '" . $awal . "' AND '" . $akhir . "'");

            $query2 = $db->prepare("SELECT SUM(stok_barang*hrgsat_barang) AS aset_tdk_lancar FROM barang");
            $query2->execute();

            $query3 = $db->prepare("SELECT SUM(trkasir.ttl_trkasir) AS piutang FROM trkasir 
                        WHERE trkasir.id_carabayar = '3' AND trkasir.tgl_trkasir BETWEEN '" . $awal . "' AND '" . $akhir . "'");
            $query3->execute();
            // $query3 = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT SUM(trkasir_detail.hrgttl_dtrkasir) AS piutang FROM trkasir_detail 
            // JOIN trkasir ON trkasir.kd_trkasir=trkasir_detail.kd_trkasir WHERE trkasir.id_carabayar = '3' AND trkasir.tgl_trkasir BETWEEN '" . $awal . "' AND '" . $akhir . "'");

            $query4 = $db->prepare("SELECT SUM(trbmasuk.ttl_trbmasuk) AS hutang FROM trbmasuk 
                        WHERE trbmasuk.carabayar = 'KREDIT' ");
            $query4->execute();
            
            $query5 = $db->prepare("SELECT SUM(trbmasuk.ttl_trbmasuk) AS pembelian_cash FROM trbmasuk 
                        WHERE trbmasuk.carabayar = 'LUNAS' AND trbmasuk.tgl_trbmasuk BETWEEN '" . $awal . "' AND '" . $akhir . "'");
            $query5->execute();
            // $query5 = mysqli_query($GLOBALS["___mysqli_ston"], "SELECT SUM(trbmasuk_detail.hrgttl_dtrbmasuk) AS pembelian_cash FROM trbmasuk_detail 
            // JOIN trbmasuk ON trbmasuk.kd_trbmasuk=trbmasuk_detail.kd_trbmasuk WHERE trbmasuk.carabayar = 'LUNAS' AND trbmasuk.tgl_trbmasuk BETWEEN '" . $awal . "' AND '" . $akhir . "'");

            $p = $query1->fetch(PDO::FETCH_ASSOC);
            $a = $query_reguler->fetch(PDO::FETCH_ASSOC);
            $b = $query_member->fetch(PDO::FETCH_ASSOC);
            $c = $query_marketplace->fetch(PDO::FETCH_ASSOC);
            $o = $query5->fetch(PDO::FETCH_ASSOC);
            $x = $query3->fetch(PDO::FETCH_ASSOC);
            $y = $query4->fetch(PDO::FETCH_ASSOC);
            $asettdklancar = $query2->fetch(PDO::FETCH_ASSOC);

            $asetlancar = ($p['penjualan'] - $o['pembelian_cash'] - $y['hutang']);
            $neraca = ($p['penjualan'] - $x['piutang'] - $y['hutang'] - $o['pembelian_cash']);
        ?>


            <div class="box box-primary box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">NERACA LABA RUGI</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div><!-- /.box-tools -->
                </div>
                <div class="box-body">

                    <form method="POST" action="?module=neraca&act=tes" target="_blank" enctype="multipart/form-data" class="form-horizontal">

                        </br></br>


                        <div class="form-group">
                            <label class="col-sm-2 control-label">Tanggal Awal</label>
                            <div class="col-sm-4">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <span class="glyphicon glyphicon-th"></span>
                                    </div>
                                    <input type="text" class="datepicker" name="tgl_awal" required="required" autocomplete="off" value="<?= $awal; ?>" id="awal">
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
                                    <input type="text" class="datepicker" name="tgl_akhir" required="required" autocomplete="off" value="<?= $akhir; ?>" id="akhir">
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label"></label>
                            <div class="buttons col-sm-4">
                                <input class="btn btn-primary" type="button" id="submit" name="btn" value="SUBMIT">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                 <a class='btn btn-success' target='_blank' href='modul/mod_laporan/tampil_neraca.php?range=<?= $awal . "/" . $akhir; ?>'>PRINT</a>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
                                <a class='btn  btn-danger' href='?module=home'>KEMBALI</a>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table id="example1" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th colspan="3">NERACA LABA RUGI Range: <?= $awal . " s/d " . $akhir; ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td class="text-center" width="50px">1.</td>
                                        <td width="250px">Penjualan<br>
                                        Penjualan Reguler <br>
                                        Penjualan Member <br>
                                        Penjualan Marketplace <br>
                                        </td>
                                        
                                        <td><?= "Rp " . format_rupiah($p['penjualan']); ?>
                                            <br>
                                            <?= "Rp " . format_rupiah($a['reguler']); ?><br>
                                            <?= "Rp " . format_rupiah($b['member']); ?><br>
                                            <?= "Rp " . format_rupiah($c['marketplace']); ?>
                                    </td>
                                    </tr>
                                    <tr>
                                        <td class="text-center" width="50px">2.</td>
                                        <td width="250px">Pembelian Cash</td>
                                        <td><?= "Rp " . format_rupiah($o['pembelian_cash']); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">3.</td>
                                        <td>Piutang<br>Total Penjualan Belum Dibayar.</td>
                                        <td><?= "Rp " . format_rupiah($x['piutang']); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">4.</td>
                                        <td>Hutang<br>Total Pembelian Belum Dibayar.</td>
                                        <td><?= "Rp " . format_rupiah($y['hutang']); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-center" width="50px">5.</td>
                                        <td width="250px">Total Asset Lancar</td>
                                        <td><?= "Rp " . format_rupiah($asetlancar); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">6.</td>
                                        <td>Total Asset Tidak Lancar</td>
                                        <td><?= "Rp " . format_rupiah($asettdklancar['aset_tdk_lancar']); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="text-center">7.</td>
                                        <td>Neraca Laba/Rugi</td>
                                        <td><?= "Rp " . format_rupiah($neraca); ?></td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                        <hr>

                    </form>
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
        location.href = "?module=neraca&act=tes&range=" + awal + "/" + akhir;
    });
</script>