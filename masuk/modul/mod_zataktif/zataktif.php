<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// session_start(); // Sudah aktif di media_admin.php
if (empty($_SESSION['username']) and empty($_SESSION['passuser'])) {
    echo "<link href=../css/style.css rel=stylesheet type=text/css>";
    echo "<div class='error msg'>Untuk mengakses Modul anda harus login.</div>";
} else {

    $aksi = "modul/mod_zataktif/aksi_zataktif.php";
    $aksi_zataktif = "masuk/modul/mod_zataktif/aksi_zataktif.php";
    switch (isset($_GET['act']) ? $_GET['act'] : '') {
        default:
?>
            <div class="box box-primary box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">DATA ZAT AKTIF</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body table-responsive">
                    <a class='btn btn-success btn-flat' href='?module=zataktif&act=tambah'>TAMBAH</a>
                    <a class='btn btn-primary btn-flat' href='?module=barang'>KEMBALI</a>
                    <br><br>

                    <table id="tampil" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Zat Aktif</th>
                                <th>Indikasi</th>
                                <th>Aturan Pakai</th>
                                <th>Saran</th>
                                <th>User</th>
                                <th>Update</th>
                                <th width="70">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>

                    <script>
                        $(document).ready(function() {
                            $("#tampil").DataTable({
                                processing: true,
                                serverSide: true,
                                autoWidth: false,
                                ajax: {
                                    "url": "modul/mod_zataktif/zataktif_serverside.php?action=table_data",
                                    "dataType": "JSON",
                                    "type": "POST"
                                },
                                columns: [{
                                        "data": "no",
                                        "className": 'text-center'
                                    },
                                    {
                                        "data": "nm_zataktif"
                                    },
                                    {
                                        "data": "indikasi"
                                    },
                                    {
                                        "data": "aturanpakai"
                                    },
                                    {
                                        "data": "saran"
                                    },
                                    {
                                        "data": "user"
                                    },
                                    {
                                        "data": "updated_at"
                                    },
                                    {
                                        "data": "pilih",
                                        "className": 'text-center'
                                    }
                                ]
                            });
                        });
                    </script>
                </div>
            </div>
<?php
            break;

        case "tambah":
            echo "
          <div class='box box-primary box-solid'>
                <div class='box-header with-border'>
                    <h3 class='box-title'>TAMBAH ZAT AKTIF</h3>
                    <div class='box-tools pull-right'>
                        <button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
                    </div>
                </div>
                <div class='box-body table-responsive'>
                    <form method=POST action='$aksi?module=zataktif&act=input_zataktif' class='form-horizontal'>
                        <div class='form-group'>
                            <label class='col-sm-2 control-label'>Nama Zat Aktif</label>
                            <div class='col-sm-6'>
                                <input type=text name='nm_zataktif' class='form-control' required='required' autocomplete='off'>
                            </div>
                        </div>
                        <div class='form-group'>
                            <label class='col-sm-2 control-label'>Indikasi</label>
                            <div class='col-sm-6'>
                                <textarea name='indikasi' class='form-control' rows='2'></textarea>
                            </div>
                        </div>
                        <div class='form-group'>
                            <label class='col-sm-2 control-label'>Aturan Pakai</label>
                            <div class='col-sm-6'>
                                <textarea name='aturanpakai' class='form-control' rows='2'></textarea>
                            </div>
                        </div>
                        <div class='form-group'>
                            <label class='col-sm-2 control-label'>Saran</label>
                            <div class='col-sm-6'>
                                <textarea name='saran' class='form-control' rows='2'></textarea>
                            </div>
                        </div>
                        <div class='form-group'>
                            <label class='col-sm-2 control-label'></label>
                            <div class='col-sm-6'>
                                <input class='btn btn-info' type=submit value=SIMPAN>
                                <input class='btn btn-primary' type=button value=BATAL onclick='self.history.back()'>
                            </div>
                        </div>
                    </form>
                </div>
            </div>";
            break;

        case "edit":
            $stmt = $db->prepare("SELECT * FROM zataktif WHERE id_zataktif = ?");
            $stmt->execute([$_GET['id']]);
            $r = $stmt->fetch(PDO::FETCH_ASSOC);

            echo "
          <div class='box box-primary box-solid'>
                <div class='box-header with-border'>
                    <h3 class='box-title'>UBAH ZAT AKTIF</h3>
                    <div class='box-tools pull-right'>
                        <button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
                    </div>
                </div>
                <div class='box-body table-responsive'>
                    <form method=POST action='$aksi?module=zataktif&act=update_zataktif' class='form-horizontal'>
                        <input type=hidden name='id' value='$r[id_zataktif]'>
                        <div class='form-group'>
                            <label class='col-sm-2 control-label'>Nama Zat Aktif</label>
                            <div class='col-sm-6'>
                                <input type=text name='nm_zataktif' class='form-control' required='required' value='$r[nm_zataktif]' autocomplete='off'>
                            </div>
                        </div>
                        <div class='form-group'>
                            <label class='col-sm-2 control-label'>Indikasi</label>
                            <div class='col-sm-6'>
                                <textarea name='indikasi' class='form-control' rows='2'>$r[indikasi]</textarea>
                            </div>
                        </div>
                        <div class='form-group'>
                            <label class='col-sm-2 control-label'>Aturan Pakai</label>
                            <div class='col-sm-6'>
                                <textarea name='aturanpakai' class='form-control' rows='2'>$r[aturanpakai]</textarea>
                            </div>
                        </div>
                        <div class='form-group'>
                            <label class='col-sm-2 control-label'>Saran</label>
                            <div class='col-sm-6'>
                                <textarea name='saran' class='form-control' rows='2'>$r[saran]</textarea>
                            </div>
                        </div>
                        <div class='form-group'>
                            <label class='col-sm-2 control-label'></label>
                            <div class='col-sm-6'>
                                <input class='btn btn-info' type=submit value=SIMPAN>
                                <input class='btn btn-primary' type=button value=BATAL onclick='self.history.back()'>
                            </div>
                        </div>
                    </form>
                </div>
            </div>";
            break;
    }
}
?>
