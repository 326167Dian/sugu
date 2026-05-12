<?php
session_start();
include "../../../configurasi/koneksi.php";

if (empty($_SESSION['username']) && empty($_SESSION['passuser'])) {
    echo "<link href=../css/style.css rel=stylesheet type=text/css>";
    echo "<div class='error msg'>Untuk mengakses Modul anda harus login.</div>";
} else {

    $aksi = "modul/mod_konseling/aksi_konseling.php";
    $act = isset($_GET['act']) ? $_GET['act'] : '';

    switch ($act) {
        default:
            $id_pelanggan_param = isset($_GET['id_pelanggan']) ? intval($_GET['id_pelanggan']) : 0;
            $tambah_link = "?module=konseling&act=tambah";
            if ($id_pelanggan_param > 0) {
                $tambah_link .= "&id_pelanggan=" . $id_pelanggan_param;
            }
            ?>

            <div class="box box-primary box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">DATA KONSELING</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body table-responsive">
                    <a class='btn btn-success btn-flat' href='<?php echo $tambah_link; ?>'>TAMBAH</a>
                    <a class='btn btn-primary btn-flat' href='?module=pelanggan'>KEMBALI</a>
                    <br><br>

                    <table id="konseling" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal</th>
                                <th>Nama Pelanggan</th>
                                <th>Dokter</th>
                                <th>Diagnosa</th>
                                <th>Tindakan</th>
                                <th>Updated</th>
                                <th width="70">Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

                    <script>
                        $(document).ready(function() {
                            $("#konseling").DataTable({
                                processing: true,
                                serverSide: true,
                                autoWidth: false,
                                ajax: {
                                    "url": "modul/mod_konseling/konseling_serverside.php?action=table_data",
                                    "dataType": "JSON",
                                    "type": "POST"
                                },
                                columns: [
                                    { "data": "no", "className": "text-center" },
                                    { "data": "tgl_konseling" },
                                    { "data": "nm_pelanggan" },
                                    { "data": "nama_dokter" },
                                    { "data": "diagnosa" },
                                    { "data": "tindakan" },
                                    { "data": "updated_at" },
                                    { "data": "aksi", "className": "text-center" }
                                ]
                            });
                        });
                    </script>
                </div>
            </div>

<?php
            break;

        case "tambah":
            $selected_pelanggan = isset($_GET['id_pelanggan']) ? intval($_GET['id_pelanggan']) : 0;
            echo "
            <div class='box box-primary box-solid'>
                <div class='box-header with-border'>
                    <h3 class='box-title'>TAMBAH KONSELING</h3>
                    <div class='box-tools pull-right'>
                        <button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
                    </div>
                </div>
                <div class='box-body table-responsive'>
                    <form method=POST action='$aksi?module=konseling&act=input_konseling' class='form-horizontal'>
                        <div class='form-group'>
                            <label class='col-sm-2 control-label'>Nama Pelanggan</label>
                            <div class='col-sm-4'>
                                <select name='id_pelanggan' class='form-control' required='required'>";
                                $tampil = $db->query("SELECT id_pelanggan, nm_pelanggan FROM pelanggan ORDER BY nm_pelanggan ASC");
                                while ($rk = $tampil->fetch(PDO::FETCH_ASSOC)) {
                                    $sel = ($rk['id_pelanggan'] == $selected_pelanggan) ? "selected" : "";
                                    echo "<option value='$rk[id_pelanggan]' $sel>$rk[nm_pelanggan]</option>";
                                }
                                echo "</select>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label class='col-sm-2 control-label'>Tanggal Konseling</label>
                            <div class='col-sm-4'>
                                <input type='date' name='tgl_konseling' class='form-control' required='required' value='" . date('Y-m-d') . "'>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label class='col-sm-2 control-label'>Nama Dokter</label>
                            <div class='col-sm-6'>
                                <input type='text' name='nama_dokter' class='form-control' required='required' autocomplete='off'>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label class='col-sm-2 control-label'>Diagnosa</label>
                            <div class='col-sm-8'>
                                <textarea name='diagnosa' class='form-control' rows='2' required='required'></textarea>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label class='col-sm-2 control-label'>Riwayat Penyakit</label>
                            <div class='col-sm-8'>
                                <textarea name='riwayat_penyakit' class='form-control' rows='2' required='required'></textarea>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label class='col-sm-2 control-label'>Riwayat Alergi</label>
                            <div class='col-sm-8'>
                                <textarea name='riwayat_alergi' class='form-control' rows='2' required='required'></textarea>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label class='col-sm-2 control-label'>Keluhan</label>
                            <div class='col-sm-8'>
                                <textarea name='keluhan' class='form-control' rows='2' required='required'></textarea>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label class='col-sm-2 control-label'>Visite Sebelumnya</label>
                            <div class='col-sm-6'>
                                <input type='text' name='visite' class='form-control' required='required' autocomplete='off'>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label class='col-sm-2 control-label'>Tindakan</label>
                            <div class='col-sm-8'>
                                <textarea name='tindakan' class='form-control' rows='2' required='required'></textarea>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label class='col-sm-2 control-label'></label>
                            <div class='col-sm-5'>
                                <input class='btn btn-info' type=submit value=SIMPAN>
                                <input class='btn btn-primary' type=button value=BATAL onclick='self.history.back()'>
                            </div>
                        </div>
                    </form>
                </div>
            </div>";
            break;

        case "edit":
            $stmt = $db->prepare("SELECT * FROM konseling WHERE id_konseling = ?");
            $stmt->execute([$_GET['id']]);
            $r = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$r) {
                echo "<div class='alert alert-danger'>Data konseling tidak ditemukan.</div>";
                break;
            }

            echo "
            <div class='box box-primary box-solid'>
                <div class='box-header with-border'>
                    <h3 class='box-title'>UBAH KONSELING</h3>
                    <div class='box-tools pull-right'>
                        <button class='btn btn-box-tool' data-widget='collapse'><i class='fa fa-minus'></i></button>
                    </div>
                </div>
                <div class='box-body table-responsive'>
                    <form method=POST action='$aksi?module=konseling&act=update_konseling' class='form-horizontal'>
                        <input type=hidden name=id value='$r[id_konseling]'>
                        <div class='form-group'>
                            <label class='col-sm-2 control-label'>Nama Pelanggan</label>
                            <div class='col-sm-4'>
                                <select name='id_pelanggan' class='form-control' required='required'>";
                                $tampil = $db->query("SELECT id_pelanggan, nm_pelanggan FROM pelanggan ORDER BY nm_pelanggan ASC");
                                while ($rk = $tampil->fetch(PDO::FETCH_ASSOC)) {
                                    $sel = ($rk['id_pelanggan'] == $r['id_pelanggan']) ? "selected" : "";
                                    echo "<option value='$rk[id_pelanggan]' $sel>$rk[nm_pelanggan]</option>";
                                }
                                echo "</select>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label class='col-sm-2 control-label'>Tanggal Konseling</label>
                            <div class='col-sm-4'>
                                <input type='date' name='tgl_konseling' class='form-control' required='required' value='$r[tgl_konseling]'>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label class='col-sm-2 control-label'>Nama Dokter</label>
                            <div class='col-sm-6'>
                                <input type='text' name='nama_dokter' class='form-control' required='required' value='$r[nama_dokter]' autocomplete='off'>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label class='col-sm-2 control-label'>Diagnosa</label>
                            <div class='col-sm-8'>
                                <textarea name='diagnosa' class='form-control' rows='2' required='required'>$r[diagnosa]</textarea>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label class='col-sm-2 control-label'>Riwayat Penyakit</label>
                            <div class='col-sm-8'>
                                <textarea name='riwayat_penyakit' class='form-control' rows='2' required='required'>$r[riwayat_penyakit]</textarea>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label class='col-sm-2 control-label'>Riwayat Alergi</label>
                            <div class='col-sm-8'>
                                <textarea name='riwayat_alergi' class='form-control' rows='2' required='required'>$r[riwayat_alergi]</textarea>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label class='col-sm-2 control-label'>Keluhan</label>
                            <div class='col-sm-8'>
                                <textarea name='keluhan' class='form-control' rows='2' required='required'>$r[keluhan]</textarea>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label class='col-sm-2 control-label'>Visite</label>
                            <div class='col-sm-6'>
                                <input type='text' name='visite' class='form-control' required='required' value='$r[visite]' autocomplete='off'>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label class='col-sm-2 control-label'>Tindakan</label>
                            <div class='col-sm-8'>
                                <textarea name='tindakan' class='form-control' rows='2' required='required'>$r[tindakan]</textarea>
                            </div>
                        </div>

                        <div class='form-group'>
                            <label class='col-sm-2 control-label'></label>
                            <div class='col-sm-5'>
                                <input class='btn btn-primary' type=submit value=UPDATE>
                                <input class='btn btn-default' type=button value=BATAL onclick='self.history.back()'>
                            </div>
                        </div>
                    </form>
                </div>
            </div>";
            break;
    }
}
?>
