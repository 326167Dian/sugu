<?php
session_start();
if (empty($_SESSION['username']) and empty($_SESSION['passuser'])) {
    echo "<link href=../css/style.css rel=stylesheet type=text/css>";
    echo "<div class='error msg'>Untuk mengakses Modul anda harus login.</div>";
} else {

    $aksi = "modul/mod_kartustok/aksi_kartustok.php";
    $aksi_barang = "masuk/modul/mod_barang/aksi_barang.php";
    switch ($_GET['act']) {
            // Tampil barang
        default:


            // $tampil_barang = $db->query("SELECT * FROM barang ORDER BY stok_barang DESC");

?>

            <div class="box box-primary box-solid table-responsive">
                <div class="box-header with-border">
                    <h3 class="box-title">Data Barang / Kartu Stok</h3>
                    <!--<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>-->

                    <div class="box-tools pull-center">

                    </div><!-- /.box-tools -->
                </div>
                <div class="box-body">

                    <table id="tono1" class="table table-bordered table-striped">
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
                        
                    </table>
                </div>
            </div>


        <?php

            break;

        case "view":

            $kdbarang = $_GET['id'];
            $tampil_barang = $db->prepare("SELECT * FROM barang WHERE kd_barang = ? ");
            $tampil_barang->execute([$kdbarang]);
            $tampil = $tampil_barang->fetch(PDO::FETCH_ASSOC);

        ?>


            <div class="box box-primary box-solid table-responsive">
                <div class="box-header with-border">
                    <h3 class="box-title">KARTU STOK</h3>
                    <!--<button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>-->

                    <div class="box-tools pull-center">

                    </div><!-- /.box-tools -->
                </div>
                <div class="box-body">

                    <div class="form-group row">
                        <label for="inputEmail3" class="col-sm-1 col-form-label">Nama Barang</label>
                        <div class="col-sm-10">
                            <!-- <input type="text" class="form-control" id="inputEmail3"> -->
                            <label>: <?= $tampil['nm_barang'] ?></label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="inputPassword3" class="col-sm-1 col-form-label">Satuan</label>
                        <div class="col-sm-10">
                            <!-- <input type="text" class="form-control" id="inputPassword3"> -->
                            <label>: <?= $tampil['sat_barang'] ?></label>
                        </div>
                    </div>
                    <hr />


                    <!--<table id="tes" class="table table-bordered table-striped" >-->
                    <table id="example1" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="text-align: center; ">No</th>
                                <th style="text-align: center; ">Current Time</th>
                                <th style="text-align: center; ">Bulan</th>
                                <th style="text-align: center; ">Nomor Transaksi</th>
                                <th style="text-align: center; ">Qty Masuk (Pembelian)</th>
                                <th style="text-align: center; ">no_batch & ED masuk</th>
                                <th style="text-align: center; ">Qty Keluar (Penjualan)</th>
                                <th style="text-align: center; ">no_batch & ED keluar</th>
                                <th style="text-align: center; ">Total (Qty Masuk - Qty Keluar)</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $getlogs = $db->prepare("SELECT *, b.no_batch as batch_masuk, b.exp_date as ed_masuk,
                            c.no_batch as batch_keluar, c.exp_date as ed_keluar FROM kartu_stok a 
                            LEFT JOIN trbmasuk_detail b ON a.kode_transaksi=b.kd_trbmasuk
                            LEFT JOIN trkasir_detail c ON a.kode_transaksi=c.kd_trkasir
                            WHERE b.kd_barang = ? OR c.kd_barang = ? ORDER BY tgl_sekarang ASC");
                            $getlogs->execute([$kdbarang, $kdbarang]);

                            $total = 0;
                            while ($r = $getlogs->fetch(PDO::FETCH_ASSOC)) :
                                if ($r['qty_dtrbmasuk']) {
                                    $total += $r['qty_dtrbmasuk'];
                                } elseif ($r['qty_dtrkasir']) {
                                    $total -= $r['qty_dtrkasir'];
                                }

                            ?>
                                <tr>
                                    <td class="text-center"><?= $no; ?>.</td>
                                    <td><?= date('Y-m-d H:i:s', strtotime($r['tgl_sekarang'])); ?></td>
                                    <td class="text-center"><?= date('F', strtotime($r['tgl_sekarang'])); ?></td>
                                    <td><?= $r['kode_transaksi']; ?></td>
                                    <td class="text-center"><?= ($r['qty_dtrbmasuk'] != null) ? $r['qty_dtrbmasuk'] : 0; ?></td>
                                    <td class="text-center"><?= ($r['batch_masuk'] != null) ? $r['batch_masuk'] : 0; ?> & <?= ($r['ed_masuk'] != null) ? $r['ed_masuk'] : 0; ?></td>
                                    <td class="text-center"><?= ($r['qty_dtrkasir'] != null) ? $r['qty_dtrkasir'] : 0; ?></td>
                                    <td class="text-center"><?= ($r['batch_keluar'] != null) ? $r['batch_keluar'] : 0; ?> & <?= ($r['ed_keluar'] != null) ? $r['ed_keluar'] : 0; ?></td>
                                    <td class="text-center"><?= $total; ?></td>
                                </tr>
                            <?php
                                $no++;
                            endwhile; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-center">
                                    <h3>Total Stok Barang</h3>
                                </td>
                                <td colspan="3" class="text-left">
                                    <h3><?= $total; ?></h3>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>


<?php


            break;
    }
}
?>


<script type="text/javascript">
    $(document).ready(function(){
        var start = '<?php echo date("Y-m-d", strtotime("-30 days")); ?>';
        var finish = '<?php echo date("Y-m-d"); ?>';

        $('#tono1').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: 'modul/mod_kartustok/kartu_stok_serverside.php?action=table_data&start='+start+'&finish='+finish,
                type: 'POST'
            },
            columns: [
                { data: 'no' },
                { data: 'kd_barang' },
                { data: 'nm_barang' },
                { data: 'stok_barang', className: 'text-right' },
                { data: 't30', className: 'text-right' },
                { data: 't60', className: 'text-right' },
                { data: 'gr', className: 'text-right' },
                { data: 'q30', className: 'text-right' },
                { data: 'satuan', className: 'text-center' },
                { data: 'harga_beli', className: 'text-right' },
                { data: 'nilai_barang', className: 'text-center' },
                { data: 'kartu_stok', orderable: false, searchable: false }
            ]
        });
    })
    
    $(function() {
        $(".datepicker").datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            todayHighlight: true,
        });
    });
    
</script>