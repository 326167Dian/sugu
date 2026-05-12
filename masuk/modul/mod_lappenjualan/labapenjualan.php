<?php
session_start();
if (empty($_SESSION['username']) AND empty($_SESSION['passuser'])){
    echo "<link href=../css/style.css rel=stylesheet type=text/css>";
    echo "<div class='error msg'>Untuk mengakses Modul anda harus login.</div>";
}
else{

    switch($_GET['act']){
        default:

            ?>


            <div class="box box-success box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">LAPORAN LABA PENJUALAN PRODUK</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div><!-- /.box-tools
                    -->
                </div>
                <div class="box-body">

                    <form method="POST" action="modul/mod_laporan/tampil_labapenjualan.php" target="_blank" enctype="multipart/form-data" class="form-horizontal">

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
                                    <option value= "0">Semua Shift</option>
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

                    window.open('modul/mod_lappenjualan/laplabapenjualan_excel.php?tgl_awal='+tgl_awal+'&tgl_akhir='+tgl_akhir, '_blank');
                }
            </script>


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
