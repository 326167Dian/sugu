<?php

function pto_h($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

if (empty($_SESSION['username']) and empty($_SESSION['passuser'])) {
    echo "<link href=../css/style.css rel=stylesheet type=text/css>";
    echo "<div class='error msg'>Untuk mengakses Modul anda harus login.</div>";
} else {

    $isPemilik = (isset($_SESSION['level']) && $_SESSION['level'] === 'pemilik');

    $act = isset($_GET['act']) ? $_GET['act'] : '';

    switch ($act) {
        case 'riwayat':
            $id_pelanggan = isset($_GET['id_pelanggan']) ? intval($_GET['id_pelanggan']) : 0;
            $tgl_awal = isset($_GET['tgl_awal']) ? trim($_GET['tgl_awal']) : '';
            $tgl_akhir = isset($_GET['tgl_akhir']) ? trim($_GET['tgl_akhir']) : '';

            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tgl_awal)) {
                $tgl_awal = '';
            }
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tgl_akhir)) {
                $tgl_akhir = '';
            }

            $pelanggan = null;

            if ($id_pelanggan > 0) {
                $stmtPelanggan = $db->prepare("SELECT id_pelanggan, nm_pelanggan FROM pelanggan WHERE id_pelanggan = ?");
                $stmtPelanggan->execute([$id_pelanggan]);
                $pelanggan = $stmtPelanggan->fetch(PDO::FETCH_ASSOC);
            }

            if (!$pelanggan) {
                echo "
                <div class='box box-danger box-solid'>
                    <div class='box-header with-border'>
                        <h3 class='box-title'>RIWAYAT PTO</h3>
                    </div>
                    <div class='box-body'>
                        <div class='alert alert-danger' style='margin-bottom:10px;'>Data pelanggan tidak ditemukan.</div>
                        <a class='btn btn-primary btn-flat' href='?module=pelanggan'>KEMBALI</a>
                    </div>
                </div>";
                break;
            }

            $sqlRiwayat = "SELECT * FROM pto WHERE id_pelanggan = ?";
            $paramsRiwayat = [$id_pelanggan];

            if ($tgl_awal !== '') {
                $sqlRiwayat .= " AND COALESCE(tanggal_1, DATE(created_at)) >= ?";
                $paramsRiwayat[] = $tgl_awal;
            }

            if ($tgl_akhir !== '') {
                $sqlRiwayat .= " AND COALESCE(tanggal_1, DATE(created_at)) <= ?";
                $paramsRiwayat[] = $tgl_akhir;
            }

            $sqlRiwayat .= " ORDER BY id_pto DESC";
            $stmtRiwayat = $db->prepare($sqlRiwayat);
            $stmtRiwayat->execute($paramsRiwayat);
            $riwayat = $stmtRiwayat->fetchAll(PDO::FETCH_ASSOC);
?>
            <div class="box box-primary box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">RIWAYAT PTO - <?php echo pto_h($pelanggan['nm_pelanggan']); ?></h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body table-responsive">
                    <a class='btn btn-success btn-flat' href='?module=pto&act=input_pto&id_pelanggan=<?php echo (int)$id_pelanggan; ?>'>INPUT PTO BARU</a>
                    <a class='btn btn-danger btn-flat' href='modul/mod_pemantauan_terapi_obat/cetak_pto_pdf.php?id_pelanggan=<?php echo (int)$id_pelanggan; ?>&tgl_awal=<?php echo urlencode($tgl_awal); ?>&tgl_akhir=<?php echo urlencode($tgl_akhir); ?>' target='_blank'>EXPORT PDF</a>
                    <a class='btn btn-primary btn-flat' href='?module=pelanggan'>KEMBALI KE PELANGGAN</a>
                    <br><br>

                    <form method="GET" class="form-inline" style="margin-bottom:12px;">
                        <input type="hidden" name="module" value="pto">
                        <input type="hidden" name="act" value="riwayat">
                        <input type="hidden" name="id_pelanggan" value="<?php echo (int)$id_pelanggan; ?>">

                        <div class="form-group" style="margin-right:8px;">
                            <label style="margin-right:6px;">Dari</label>
                            <input type="date" name="tgl_awal" class="form-control" value="<?php echo pto_h($tgl_awal); ?>">
                        </div>

                        <div class="form-group" style="margin-right:8px;">
                            <label style="margin-right:6px;">Sampai</label>
                            <input type="date" name="tgl_akhir" class="form-control" value="<?php echo pto_h($tgl_akhir); ?>">
                        </div>

                        <button type="submit" class="btn btn-default">FILTER</button>
                        <a class="btn btn-default" href="?module=pto&act=riwayat&id_pelanggan=<?php echo (int)$id_pelanggan; ?>">RESET</a>
                    </form>

                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width:60px;">No</th>
                                <th>Tanggal 1</th>
                                <th>Tanggal 2</th>
                                <th>Tempat TTD</th>
                                <th>Dibuat Oleh</th>
                                <th style="width:190px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($riwayat)) { ?>
                                <tr>
                                    <td colspan="6" class="text-center">Belum ada data PTO.</td>
                                </tr>
                            <?php } else {
                                $no = 1;
                                foreach ($riwayat as $item) {
                            ?>
                                    <tr>
                                        <td class="text-center"><?php echo $no; ?></td>
                                        <td><?php echo pto_h($item['tanggal_1']); ?></td>
                                        <td><?php echo pto_h($item['tanggal_2']); ?></td>
                                        <td><?php echo pto_h($item['tempat_ttd']); ?></td>
                                        <td><?php echo pto_h($item['created_by']); ?></td>
                                        <td>
                                            <a href="modul/mod_pemantauan_terapi_obat/tampil_pto.php?id_pto=<?php echo (int)$item['id_pto']; ?>" target="_blank" class="btn btn-info btn-xs">LIHAT</a>
                                            <?php if ($isPemilik) { ?>
                                                <a href="?module=pto&act=edit&id_pto=<?php echo (int)$item['id_pto']; ?>" class="btn btn-warning btn-xs">EDIT</a>
                                                <a href="javascript:confirmdelete('modul/mod_pemantauan_terapi_obat/simpan_pto.php?act=delete&id_pto=<?php echo (int)$item['id_pto']; ?>&id_pelanggan=<?php echo (int)$id_pelanggan; ?>')" class="btn btn-danger btn-xs">HAPUS</a>
                                            <?php } ?>
                                        </td>
                                    </tr>
                            <?php
                                    $no++;
                                }
                            } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php
            break;

        case 'edit':
            if (!$isPemilik) {
                echo "
                <div class='box box-danger box-solid'>
                    <div class='box-header with-border'>
                        <h3 class='box-title'>AKSES DITOLAK</h3>
                    </div>
                    <div class='box-body'>
                        <div class='alert alert-danger' style='margin-bottom:10px;'>Fitur edit PTO hanya untuk pemilik.</div>
                        <a class='btn btn-primary btn-flat' href='?module=pelanggan'>KEMBALI</a>
                    </div>
                </div>";
                break;
            }

            $id_pto = isset($_GET['id_pto']) ? intval($_GET['id_pto']) : 0;
            $stmtEdit = $db->prepare("SELECT pto.*, pl.nm_pelanggan AS nm_pelanggan_ref FROM pto LEFT JOIN pelanggan pl ON pl.id_pelanggan = pto.id_pelanggan WHERE pto.id_pto = ?");
            $stmtEdit->execute([$id_pto]);
            $dataPto = $stmtEdit->fetch(PDO::FETCH_ASSOC);

            if (!$dataPto) {
                echo "
                <div class='box box-danger box-solid'>
                    <div class='box-header with-border'>
                        <h3 class='box-title'>EDIT PTO</h3>
                    </div>
                    <div class='box-body'>
                        <div class='alert alert-danger' style='margin-bottom:10px;'>Data PTO tidak ditemukan.</div>
                        <a class='btn btn-primary btn-flat' href='?module=pelanggan'>KEMBALI</a>
                    </div>
                </div>";
                break;
            }
        ?>
            <div class="box box-primary box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">EDIT DOKUMENTASI PEMANTAUAN TERAPI OBAT (PTO)</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <form method="POST" action="modul/mod_pemantauan_terapi_obat/simpan_pto.php?act=update" target="_blank" class="form-horizontal">
                        <input type="hidden" name="id_pto" value="<?php echo (int)$dataPto['id_pto']; ?>">
                        <input type="hidden" name="id_pelanggan" value="<?php echo (int)$dataPto['id_pelanggan']; ?>">

                        <h4><u>DATA PASIEN</u></h4>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Nama Pasien</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" value="<?php echo pto_h($dataPto['nm_pelanggan']); ?>" readonly>
                                <input type="hidden" name="nm_pelanggan" value="<?php echo pto_h($dataPto['nm_pelanggan']); ?>">
                            </div>

                            <label class="col-sm-2 control-label">Jenis Kelamin</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" value="<?php echo pto_h($dataPto['jenis_kelamin']); ?>" readonly>
                                <input type="hidden" name="jenis_kelamin" value="<?php echo pto_h($dataPto['jenis_kelamin']); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Umur</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" value="<?php echo pto_h($dataPto['umur']); ?>" readonly>
                                <input type="hidden" name="umur" value="<?php echo pto_h($dataPto['umur']); ?>">
                            </div>

                            <label class="col-sm-2 control-label">No. Telepon</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" value="<?php echo pto_h($dataPto['tlp_pelanggan']); ?>" readonly>
                                <input type="hidden" name="tlp_pelanggan" value="<?php echo pto_h($dataPto['tlp_pelanggan']); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Alamat</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" rows="2" readonly><?php echo pto_h($dataPto['alamat_pelanggan']); ?></textarea>
                                <input type="hidden" name="alamat_pelanggan" value="<?php echo pto_h($dataPto['alamat_pelanggan']); ?>">
                            </div>
                        </div>

                        <hr>
                        <h4><u>ISI DOKUMENTASI PTO</u></h4>

                        <?php for ($i = 1; $i <= 2; $i++) { ?>
                            <div style="padding:10px; margin-bottom:12px; border:1px solid #eee; border-radius:4px;">
                                <h5><b>Baris <?php echo $i; ?></b></h5>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Tanggal</label>
                                    <div class="col-sm-3">
                                        <input type="date" name="tanggal_<?php echo $i; ?>" class="form-control" value="<?php echo pto_h($dataPto['tanggal_' . $i]); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Catatan Pengobatan</label>
                                    <div class="col-sm-10">
                                        <textarea name="catatan_<?php echo $i; ?>" class="form-control" rows="2"><?php echo pto_h($dataPto['catatan_' . $i]); ?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Nama Obat, Dosis, Cara Pemberian</label>
                                    <div class="col-sm-10">
                                        <textarea name="obat_<?php echo $i; ?>" class="form-control" rows="2"><?php echo pto_h($dataPto['obat_' . $i]); ?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Identifikasi Masalah</label>
                                    <div class="col-sm-10">
                                        <textarea name="masalah_<?php echo $i; ?>" class="form-control" rows="2"><?php echo pto_h($dataPto['masalah_' . $i]); ?></textarea>
                                    </div>
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="col-sm-2 control-label">Rekomendasi / Tindak Lanjut</label>
                                    <div class="col-sm-10">
                                        <textarea name="tindak_<?php echo $i; ?>" class="form-control" rows="2"><?php echo pto_h($dataPto['tindak_' . $i]); ?></textarea>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Tempat</label>
                            <div class="col-sm-4">
                                <input type="text" name="tempat_ttd" class="form-control" value="<?php echo pto_h($dataPto['tempat_ttd']); ?>">
                            </div>
                            <label class="col-sm-2 control-label">Tanggal TTD</label>
                            <div class="col-sm-4">
                                <input type="date" name="tanggal_ttd" class="form-control" value="<?php echo pto_h($dataPto['tanggal_ttd']); ?>">
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom:0;">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-warning">UPDATE & TAMPILKAN PTO</button>
                                <button type="button" class="btn btn-default" onclick="window.location='?module=pto&act=riwayat&id_pelanggan=<?php echo (int)$dataPto['id_pelanggan']; ?>'">KEMBALI</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php
            break;

        case 'input_pto':
            $id_pelanggan = isset($_GET['id_pelanggan']) ? intval($_GET['id_pelanggan']) : 0;
            $pelanggan = null;

            if ($id_pelanggan > 0) {
                $stmt = $db->prepare("SELECT * FROM pelanggan WHERE id_pelanggan = ?");
                $stmt->execute([$id_pelanggan]);
                $pelanggan = $stmt->fetch(PDO::FETCH_ASSOC);
            }

            if (!$pelanggan) {
                echo "
                <div class='box box-danger box-solid'>
                    <div class='box-header with-border'>
                        <h3 class='box-title'>PTO</h3>
                    </div>
                    <div class='box-body'>
                        <div class='alert alert-danger' style='margin-bottom:10px;'>Data pelanggan tidak ditemukan.</div>
                        <a class='btn btn-primary btn-flat' href='?module=pelanggan'>KEMBALI</a>
                    </div>
                </div>";
                break;
            }

            $umur = '';
            if (!empty($pelanggan['tanggal_lahir'])) {
                $tanggal_lahir = new DateTime($pelanggan['tanggal_lahir']);
                $today = new DateTime('today');
                $umur = $tanggal_lahir->diff($today)->y;
            }
        ?>
            <div class="box box-primary box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">INPUT DOKUMENTASI PEMANTAUAN TERAPI OBAT (PTO)</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body">
                    <a class='btn btn-default btn-flat' href='?module=pto&act=riwayat&id_pelanggan=<?php echo (int)$id_pelanggan; ?>'>RIWAYAT PTO</a>
                    <a class='btn btn-primary btn-flat' href='?module=pelanggan'>KEMBALI KE PELANGGAN</a>
                    <br><br>

                    <form method="POST" action="modul/mod_pemantauan_terapi_obat/simpan_pto.php" target="_blank" class="form-horizontal">
                        <input type="hidden" name="id_pelanggan" value="<?php echo (int)$id_pelanggan; ?>">

                        <h4><u>DATA PASIEN</u></h4>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">Nama Pasien</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" value="<?php echo pto_h($pelanggan['nm_pelanggan'] ?? ''); ?>" readonly>
                                <input type="hidden" name="nm_pelanggan" value="<?php echo pto_h($pelanggan['nm_pelanggan'] ?? ''); ?>">
                            </div>

                            <label class="col-sm-2 control-label">Jenis Kelamin</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" value="<?php echo pto_h($pelanggan['jenis_kelamin'] ?? ''); ?>" readonly>
                                <input type="hidden" name="jenis_kelamin" value="<?php echo pto_h($pelanggan['jenis_kelamin'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Umur</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" value="<?php echo $umur !== '' ? pto_h($umur . ' tahun') : ''; ?>" readonly>
                                <input type="hidden" name="umur" value="<?php echo $umur !== '' ? pto_h($umur . ' tahun') : ''; ?>">
                            </div>

                            <label class="col-sm-2 control-label">No. Telepon</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" value="<?php echo pto_h($pelanggan['tlp_pelanggan'] ?? ''); ?>" readonly>
                                <input type="hidden" name="tlp_pelanggan" value="<?php echo pto_h($pelanggan['tlp_pelanggan'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Alamat</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" rows="2" readonly><?php echo pto_h($pelanggan['alamat_pelanggan'] ?? ''); ?></textarea>
                                <input type="hidden" name="alamat_pelanggan" value="<?php echo pto_h($pelanggan['alamat_pelanggan'] ?? ''); ?>">
                            </div>
                        </div>

                        <hr>
                        <h4><u>ISI DOKUMENTASI PTO</u></h4>

                        <?php for ($i = 1; $i <= 2; $i++) { ?>
                            <div style="padding:10px; margin-bottom:12px; border:1px solid #eee; border-radius:4px;">
                                <h5><b>Baris <?php echo $i; ?></b></h5>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Tanggal</label>
                                    <div class="col-sm-3">
                                        <input type="date" name="tanggal_<?php echo $i; ?>" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Catatan Pengobatan</label>
                                    <div class="col-sm-10">
                                        <textarea name="catatan_<?php echo $i; ?>" class="form-control" rows="2" placeholder="Riwayat penyakit / penggunaan obat / alergi"></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Nama Obat, Dosis, Cara Pemberian</label>
                                    <div class="col-sm-10">
                                        <textarea name="obat_<?php echo $i; ?>" class="form-control" rows="2"></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-2 control-label">Identifikasi Masalah</label>
                                    <div class="col-sm-10">
                                        <textarea name="masalah_<?php echo $i; ?>" class="form-control" rows="2"></textarea>
                                    </div>
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label class="col-sm-2 control-label">Rekomendasi / Tindak Lanjut</label>
                                    <div class="col-sm-10">
                                        <textarea name="tindak_<?php echo $i; ?>" class="form-control" rows="2"></textarea>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <div class="form-group">
                            <label class="col-sm-2 control-label">Tempat</label>
                            <div class="col-sm-4">
                                <input type="text" name="tempat_ttd" class="form-control" placeholder="Contoh: Yogyakarta">
                            </div>
                            <label class="col-sm-2 control-label">Tanggal TTD</label>
                            <div class="col-sm-4">
                                <input type="date" name="tanggal_ttd" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                            </div>
                        </div>

                        <div class="form-group" style="margin-bottom:0;">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-success">SIMPAN & TAMPILKAN PTO</button>
                                <button type="button" class="btn btn-default" onclick="window.location='?module=pto&act=riwayat&id_pelanggan=<?php echo (int)$id_pelanggan; ?>'">BATAL</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php
            break;

        default:
            $tampilPto = $db->query("SELECT pto.*, p.nm_pelanggan AS nm_ref FROM pto LEFT JOIN pelanggan p ON p.id_pelanggan = pto.id_pelanggan ORDER BY pto.id_pto DESC");
?>
            <div class="box box-primary box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title">DATA PEMANTAUAN TERAPI OBAT (PTO)</h3>
                    <div class="box-tools pull-right">
                        <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                    </div>
                </div>
                <div class="box-body table-responsive">
                    <a class='btn btn-primary btn-flat' href='?module=pelanggan'>KEMBALI KE PELANGGAN</a>
                    <br><br>
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width:60px;">No</th>
                                <th>Nama Pelanggan</th>
                                <th>Tanggal 1</th>
                                <th>Tanggal 2</th>
                                <th>Dibuat Oleh</th>
                                <th style="width:190px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            while ($row = $tampilPto->fetch(PDO::FETCH_ASSOC)) {
                            ?>
                                <tr>
                                    <td class="text-center"><?php echo $no; ?></td>
                                    <td><?php echo pto_h($row['nm_pelanggan']); ?></td>
                                    <td><?php echo pto_h($row['tanggal_1']); ?></td>
                                    <td><?php echo pto_h($row['tanggal_2']); ?></td>
                                    <td><?php echo pto_h($row['created_by']); ?></td>
                                    <td>
                                        <a href="modul/mod_pemantauan_terapi_obat/tampil_pto.php?id_pto=<?php echo (int)$row['id_pto']; ?>" target="_blank" class="btn btn-info btn-xs">LIHAT</a>
                                        <?php if ($isPemilik) { ?>
                                            <a href="?module=pto&act=edit&id_pto=<?php echo (int)$row['id_pto']; ?>" class="btn btn-warning btn-xs">EDIT</a>
                                            <a href="javascript:confirmdelete('modul/mod_pemantauan_terapi_obat/simpan_pto.php?act=delete&id_pto=<?php echo (int)$row['id_pto']; ?>&id_pelanggan=<?php echo (int)$row['id_pelanggan']; ?>')" class="btn btn-danger btn-xs">HAPUS</a>
                                        <?php } ?>
                                        <a href="?module=pto&act=riwayat&id_pelanggan=<?php echo (int)$row['id_pelanggan']; ?>" class="btn btn-default btn-xs">RIWAYAT</a>
                                    </td>
                                </tr>
                            <?php
                                $no++;
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
