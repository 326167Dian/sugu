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
                    <h3 class="box-title">Stok Opname Bulanan</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div><!-- /.box-tools
                    -->
                </div>
                <div class="box-body">

                    <form method="POST" action="modul/mod_laporan/tampil_stokopname.php" target="_blank" enctype="multipart/form-data" class="form-horizontal">

                        </br></br>

                        <input type="hidden" name="time" id="time" value="<?= date('Y-m-d H:i:s', time()); ?>">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Tanggal</label>
                            <div class="col-sm-4">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <span class="glyphicon glyphicon-th"></span>
                                    </div>
                                    <input type="text" required="required" class="datepicker" name="tgl_awal" id="tgl_awal" autocomplete="off">
                                </div>
                            </div>
                        </div>

                        <!-- <div class="form-group">
                            <label class="col-sm-2 control-label">Tanggal Akhir</label>
                            <div class="col-sm-4">
                                <div class="input-group date">
                                    <div class="input-group-addon">
                                        <span class="glyphicon glyphicon-th"></span>
                                    </div>
                                    <input type="text" required="required" class="datepicker" name="tgl_akhir" id="tgl_akhir" autocomplete="off">
                                </div>
                            </div>
                        </div> -->

                        <div class='form-group'>
                            <label class='col-sm-2 control-label'>Nama Rak Obat</label>
                            <div class='col-sm-3'>

                                <select name='jenisobat' id='jenisobat' class='form-control'>";
                                    <?php
                                    // $tampil = $db->prepare("SELECT * FROM jenis_obat ORDER BY jenisobat ASC");
                                    $tampil = $db->prepare("SELECT DISTINCT jenisobat FROM barang ORDER BY jenisobat ASC");
                                    $tampil->execute();
                                    while ($rk = $tampil->fetch(PDO::FETCH_ASSOC)) {
                                        echo "<option value='$rk[jenisobat]'>$rk[jenisobat]</option>";
                                    }
                                    ?>
                                </select>

                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label"></label>
                            <div class="buttons col-sm-4">
                                <!-- <input class="btn btn-primary" type="submit" name="btn" value="TAMPIL">&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp -->

                                <a class='btn  btn-primary' onclick='javascript:tampil()' target='_blank'>
                                    TAMPIL
                                </a>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp

                                <a class='btn  btn-success' onclick='javascript:exportExcel()' target='_blank'>
                                    <i class='fa fa-fw fa-file-excel-o'></i>EXPORT EXCEL
                                </a>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp

                                <a class='btn  btn-danger' href='?module=home'>KEMBALI</a>
                            </div>
                        </div>

                    </form>
                </div>

            </div>

            <script>
                function exportExcel() {
                    let jenisobat = $('#jenisobat').val();
                    let tgl_akhir = $('#tgl_akhir').val();
                    let tgl_awal = $('#tgl_awal').val();
                    // window.location = 'modul/mod_lapstok/stokopname_excel.php?jenisobat='+jenisobat
                    window.open('modul/mod_lapstok/stokopname_excel.php?jenisobat=' + jenisobat + '&start=' + tgl_awal + '&finish=' + tgl_akhir, '_blank');
                }

                function tampil() {
                    let jenisobat = $('#jenisobat').val();
                    // let tgl_akhir = $('#tgl_akhir').val();
                    let tgl_awal = $('#tgl_awal').val();
                    let time = $('#time').val();

                    // window.location = 'modul/mod_lapstok/stokopname_excel.php?jenisobat='+jenisobat
                    window.open('?module=stokopname&act=tampil&jenisobat=' + jenisobat + '&tgl=' + tgl_awal, '_blank');
                }
            </script>

        <?php

            break;

        case "tampil":
            $tgl_awal   = $_GET['tgl'];
            // $tgl_akhir  = $_GET['finish'];
            $jenisobat  = $_GET['jenisobat'];

        ?>
            <input type="hidden" id="tgl_awal" value="<?= $tgl_awal ?>">
            <input type="hidden" id="jenisobat" value="<?= $jenisobat ?>">

            <div class="box box-primary box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">Stok Opname Bulanan</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div><!-- /.box-tools -->
                </div>
                <div class="box-body table-responsive">
                    <form>
                        <div class="form-group row">
                            <label class="col-sm-1 col-form-label">PETUGAS</label>
                            <label class="col-sm-6 col-form-label">: <?= $_SESSION['namauser']; ?></label>

                        </div>
                        <div class="form-group row">
                            <label class="col-sm-1 col-form-label">TIME</label>
                            <label class="col-sm-6 col-form-label">: <?= date('d M Y - H:i:s', time()); ?></label>

                        </div>
                    </form>
                    <CENTER><strong>STOK OPNAME RAK <?= $jenisobat ?><br>Tanggal : <?= $tgl_awal; ?></strong></CENTER>

                    <hr>
                    <div id="tabel_stokopname"></div>

                    <hr>

                    <CENTER><strong>REKAP STOK OPNAME</strong></CENTER>

                    <hr>
                    <div id="tabel_stokopname_rekap"></div>
                </div>

            </div>


            <script>
                $(document).ready(function() {
                    tabel_stokopname();
                    tabel_stokopname_rekap();
                });

                function simpan_stok_opname(param) {
                    let stok_fisik = $('#stok_fisik_' + param).val();
                    let exp_date = $('#exp_date_' + param).val();
                    let jml = $('#jml_' + param).val();
                    // let stok_sistem = $('#pilih_' + param).data('stok_sistem');
                    let id_barang = $('#pilih_' + param).data('id_barang');
                    let kd_barang = $('#pilih_' + param).data('kd_barang');
                    let hrgsat_barang = $('#pilih_' + param).data('hrgsat_barang');
                    let tgl_awal = $('#tgl_awal').val();

                    // console.log('Stok Fisik : ' + stok_fisik + '\nStok Sistem : ' + stok_sistem + '\nId Barang : ' + id_barang + '\nKode Barang : ' + kd_barang + '\nTanggal : ' + tgl);

                    $.ajax({
                        type: 'post',
                        url: 'modul/mod_lapstok/simpan_stokopname.php',
                        data: {
                            'id_barang': id_barang,
                            'kd_barang': kd_barang,
                            'stok_fisik': stok_fisik,
                            'exp_date': exp_date,
                            'jml': jml,
                            'hrgsat_barang': hrgsat_barang,
                            'tgl_awal': tgl_awal
                        },
                        success: function(response) {
                            tabel_stokopname();
                            tabel_stokopname_rekap();
                        }
                    });
                }

                function hapus_stok_opname(param) {

                    let id_stok = $('#hapus_' + param).data('id_stok');
                    $.ajax({
                        type: 'post',
                        url: 'modul/mod_lapstok/hapus_stokopname.php',
                        data: {
                            'id_stok': id_stok
                        },
                        success: function(response) {
                            tabel_stokopname();
                            tabel_stokopname_rekap();
                        }
                    });
                }

                function tabel_stokopname() {

                    let tgl_awal = $('#tgl_awal').val();
                    let jenisobat = $('#jenisobat').val();

                    $.ajax({
                        url: 'modul/mod_lapstok/tabel_stokopname.php',
                        type: 'post',
                        data: {
                            'tgl_awal': tgl_awal,
                            'jenisobat': jenisobat,
                        },
                        success: function(data) {
                            $('#tabel_stokopname').html(data);
                        }

                    });
                }

                function tabel_stokopname_rekap() {

                    let tgl_awal = $('#tgl_awal').val();
                    let jenisobat = $('#jenisobat').val();

                    $.ajax({
                        url: 'modul/mod_lapstok/tabel_stokopname_rekap.php',
                        type: 'post',
                        data: {
                            'tgl_awal': tgl_awal,
                            'jenisobat': jenisobat
                        },
                        success: function(data) {
                            $('#tabel_stokopname_rekap').html(data);
                        }

                    });
                }
            </script>

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
</script>