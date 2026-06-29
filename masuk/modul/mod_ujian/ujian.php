<?php
session_start();
if (empty($_SESSION['username']) and empty($_SESSION['passuser'])) {
    echo "<link href=../css/style.css rel=stylesheet type=text/css>";
    echo "<div class='error msg'>Untuk mengakses Modul anda harus login.</div>";
} else {
    $canAccessUjian = (isset($_SESSION['ujian']) && strtoupper(trim((string) $_SESSION['ujian'])) === 'Y');
    if (!$canAccessUjian) {
        echo "<link href=../css/style.css rel=stylesheet type=text/css>";
        echo "<div class='error msg'>Anda tidak berhak mengakses halaman ini.</div>";
    } else {
        include "../../../configurasi/koneksi.php";

        function render_pertanyaan_html($html)
        {
            $clean = strip_tags((string) $html, '<p><br><strong><b><em><i><u><ol><ul><li><sub><sup><span><div>');

            // Jika input berupa teks biasa, pertahankan baris baru agar tampil per baris.
            if (strpos($clean, '<') === false) {
                return nl2br(htmlspecialchars($clean), false);
            }

            return $clean;
        }

        $isPemilik = (isset($_SESSION['level']) && $_SESSION['level'] === 'pemilik');
        $act = isset($_GET['act']) ? $_GET['act'] : '';
        $aksi = "modul/mod_ujian/aksi_ujian.php";
        $selected_ujian_id = isset($_GET['ujian_id']) ? (int) $_GET['ujian_id'] : 0;
        $prefill_ujian_id = isset($_GET['ujian_id']) ? (int) $_GET['ujian_id'] : 0;
        $edit_header_id = isset($_GET['edit_header_id']) ? (int) $_GET['edit_header_id'] : 0;

        if (in_array($act, array('kelola', 'tambahsoal', 'editsoal', 'hasilujian'), true) && !$isPemilik) {
            echo "<link href=../css/style.css rel=stylesheet type=text/css>";
            echo "<div class='error msg'>Fitur CRUD soal hanya untuk status pemilik.</div>";
            return;
        }

        $daftar_soal = array();
        $daftar_ujian = array();
        $ujian_aktif = null;
        $header_edit = null;
        $error_load_soal = "";
        $error_load_hasil = "";
        $daftar_hasil = array();

        try {
            $stmtUjian = $db->query("SELECT id_soal, nm_ujian, durasi FROM soal_header ORDER BY id_soal DESC");
            $daftar_ujian = $stmtUjian->fetchAll(PDO::FETCH_ASSOC);

            if ($selected_ujian_id <= 0 && isset($_SESSION['ujian_aktif_id'])) {
                $selected_ujian_id = (int) $_SESSION['ujian_aktif_id'];
            }
            if ($prefill_ujian_id <= 0 && isset($_SESSION['ujian_aktif_id'])) {
                $prefill_ujian_id = (int) $_SESSION['ujian_aktif_id'];
            }
            if ($selected_ujian_id <= 0 && !empty($daftar_ujian)) {
                $selected_ujian_id = (int) $daftar_ujian[0]['id_soal'];
            }
            if ($prefill_ujian_id <= 0 && !empty($daftar_ujian)) {
                $prefill_ujian_id = (int) $daftar_ujian[0]['id_soal'];
            }
            if ($selected_ujian_id > 0) {
                $_SESSION['ujian_aktif_id'] = $selected_ujian_id;
            }

            if ($selected_ujian_id > 0) {
                $stmtUjianAktif = $db->prepare("SELECT id_soal, nm_ujian, durasi FROM soal_header WHERE id_soal = ? LIMIT 1");
                $stmtUjianAktif->execute(array($selected_ujian_id));
                $ujian_aktif = $stmtUjianAktif->fetch(PDO::FETCH_ASSOC);
            }

            if ($edit_header_id > 0) {
                $stmtHeaderEdit = $db->prepare("SELECT id_soal, nm_ujian, durasi FROM soal_header WHERE id_soal = ? LIMIT 1");
                $stmtHeaderEdit->execute(array($edit_header_id));
                $header_edit = $stmtHeaderEdit->fetch(PDO::FETCH_ASSOC);
            }

            if ($act === 'kelola') {
                $stmt = $db->query("SELECT s.id, s.id_soal, h.nm_ujian, h.durasi, s.pertanyaan, s.opsi_a, s.opsi_b, s.opsi_c, s.jawaban_benar FROM soal s LEFT JOIN soal_header h ON h.id_soal = s.id_soal ORDER BY h.nm_ujian ASC, s.id ASC");
                $daftar_soal = $stmt->fetchAll(PDO::FETCH_ASSOC);
            } elseif ($act === 'hasilujian') {
                try {
                    if ($selected_ujian_id > 0) {
                        $stmtHasil = $db->prepare("SELECT nama_lengkap, nama_ujian, total_soal, jawaban_benar, jawaban_salah, tidak_dijawab, nilai_akhir FROM hasil_ujian WHERE ujian_id = ? ORDER BY id_hasil DESC LIMIT 500");
                        $stmtHasil->execute(array($selected_ujian_id));
                        $daftar_hasil = $stmtHasil->fetchAll(PDO::FETCH_ASSOC);
                    } else {
                        $stmtHasil = $db->query("SELECT nama_lengkap, nama_ujian, total_soal, jawaban_benar, jawaban_salah, tidak_dijawab, nilai_akhir FROM hasil_ujian ORDER BY id_hasil DESC LIMIT 500");
                        $daftar_hasil = $stmtHasil->fetchAll(PDO::FETCH_ASSOC);
                    }
                } catch (Exception $e) {
                    $error_load_hasil = "Tabel hasil ujian belum siap. Jalankan migrasi hasil ujian terlebih dahulu.";
                }
            } elseif ($selected_ujian_id > 0) {
                $stmt = $db->prepare("SELECT id, id_soal, pertanyaan, opsi_a, opsi_b, opsi_c, jawaban_benar FROM soal WHERE id_soal = ? ORDER BY id ASC");
                $stmt->execute(array($selected_ujian_id));
                $daftar_soal = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }
        } catch (Exception $e) {
            $error_load_soal = "Data soal belum tersedia atau tabel soal belum dibuat.";
        }

        $durasi_ujian_menit = ($ujian_aktif && (int) $ujian_aktif['durasi'] > 0) ? (int) $ujian_aktif['durasi'] : 15;
        $durasi_ujian_detik = $durasi_ujian_menit * 60;

        if ($act === 'kelola') {
?>

<div class="box box-primary box-solid table-responsive">
    <style>
        #example1 th,
        #example1 td {
            padding: 6px 8px;
            vertical-align: top;
        }

        .pertanyaan-html {
            white-space: pre-line;
            line-height: 9pt;
            letter-spacing: normal;
        }

        .pertanyaan-html p {
            margin: 0 0 4px;
        }

        .pertanyaan-html p:last-child {
            margin-bottom: 0;
        }
    </style>

    <div class="box-header with-border">
        <h3 class="box-title">Kelola Soal Ujian</h3>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-md-8">
                <form method="POST" action="<?php echo $aksi; ?>?module=ujian&act=simpanheader" class="form-inline" style="margin-bottom:15px;">
                    <?php if ($header_edit) { ?>
                        <input type="hidden" name="id_soal" value="<?php echo (int) $header_edit['id_soal']; ?>">
                    <?php } ?>
                    <div class="form-group" style="margin-right:10px;">
                        <label for="nm_ujian" style="margin-right:8px;">Nama Ujian (nm_ujian) =</label>
                        <input type="text" name="nm_ujian" id="nm_ujian" class="form-control" value="<?php echo $header_edit ? htmlspecialchars($header_edit['nm_ujian']) : ''; ?>" required>
                    </div>
                    <div class="form-group" style="margin-right:10px;">
                        <label for="durasi" style="margin-right:8px;">Durasi (menit) menunjukkan lama ujian =</label>
                        <input type="number" name="durasi" id="durasi" class="form-control" min="1" value="<?php echo $header_edit ? (int) $header_edit['durasi'] : ''; ?>" required>
                    </div>
                    <?php if ($header_edit) { ?>
                        <button type="submit" formaction="<?php echo $aksi; ?>?module=ujian&act=updateheader" class="btn btn-warning">Update Ujian</button>
                        <a href="?module=ujian&act=kelola" class="btn btn-default">Batal</a>
                    <?php } else { ?>
                        <button type="submit" class="btn btn-primary">Simpan Ujian</button>
                    <?php } ?>
                </form>
            </div>
        </div>

        <?php if (!empty($daftar_ujian)) { ?>
            <div class="table-responsive" style="margin-bottom:15px;">
                <table class="table table-bordered table-condensed">
                    <thead>
                        <tr>
                            <th width="60">ID</th>
                            <th>Nama Ujian</th>
                            <th width="120">Durasi (menit)</th>
                            <th width="120">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($daftar_ujian as $u) { ?>
                            <tr>
                                <td><?php echo (int) $u['id_soal']; ?></td>
                                <td><?php echo htmlspecialchars($u['nm_ujian']); ?></td>
                                <td><?php echo (int) $u['durasi']; ?></td>
                                <td>
                                    <a href="?module=ujian&act=kelola&edit_header_id=<?php echo (int) $u['id_soal']; ?>" class="btn btn-warning btn-xs">Edit</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } ?>

        <?php
            $default_tambah_ujian_id = 0;
            if ($selected_ujian_id > 0) {
                $default_tambah_ujian_id = $selected_ujian_id;
            } elseif (!empty($daftar_ujian)) {
                $default_tambah_ujian_id = (int) $daftar_ujian[0]['id_soal'];
            }
            $tambah_soal_link = "?module=ujian&act=tambahsoal";
            if ($default_tambah_ujian_id > 0) {
                $tambah_soal_link .= "&ujian_id=" . $default_tambah_ujian_id;
            }
        ?>

        <a href="<?php echo $tambah_soal_link; ?>" class="btn btn-success btn-flat">Tambah Soal</a>
        <a href="?module=ujian" class="btn btn-default btn-flat">Kembali ke Ujian</a>
        <br><br>

        <?php if (!empty($error_load_soal)) { ?>
            <div class="alert alert-warning"><?php echo $error_load_soal; ?></div>
        <?php } elseif (empty($daftar_soal)) { ?>
            <div class="alert alert-info">Belum ada soal.</div>
        <?php } else { ?>
            <table id="example1" class="table table-bordered table-striped ">
                <thead>
                    <tr>
                        <th width="45">No</th>
                        <th>Nama Ujian</th>
                        <th width="85">Durasi</th>
                        <th>Soal</th>
                        <th width="110">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($daftar_soal as $i => $soal) { ?>
                        <tr>
                            <td><?php echo $i + 1; ?></td>
                            <td><?php echo htmlspecialchars((string) $soal['nm_ujian']); ?></td>
                            <td><?php echo (int) $soal['durasi']; ?> menit</td>
                            <td>
                                <div class="pertanyaan-html"><?php echo render_pertanyaan_html($soal['pertanyaan']); ?></div>
                                <div class="pertanyaan-html" style="margin-top:8px;">
                                    A. <?php echo htmlspecialchars((string) $soal['opsi_a']); ?><br>
                                    B. <?php echo htmlspecialchars((string) $soal['opsi_b']); ?><br>
                                    C. <?php echo htmlspecialchars((string) $soal['opsi_c']); ?><br>
                                    <strong>Jawaban : <?php echo strtoupper(htmlspecialchars((string) $soal['jawaban_benar'])); ?></strong>
                                </div>
                            </td>
                            <td>
                                <a href="?module=ujian&act=editsoal&id=<?php echo (int) $soal['id']; ?>&ujian_id=<?php echo (int) $soal['id_soal']; ?>" class="btn btn-warning btn-xs">Edit</a>
                                <a href="<?php echo $aksi; ?>?module=ujian&act=hapussoal&id=<?php echo (int) $soal['id']; ?>&ujian_id=<?php echo (int) $soal['id_soal']; ?>" class="btn btn-danger btn-xs" onclick="return confirm('Hapus soal ini?');">Hapus</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php } ?>

    </div>
</div>

<?php
        } elseif ($act === 'hasilujian') {
?>

<div class="box box-primary box-solid">
    <div class="box-header with-border">
        <h3 class="box-title">Hasil Akhir Ujian</h3>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <form method="GET" class="form-inline" style="margin-bottom:15px;">
            <input type="hidden" name="module" value="ujian">
            <input type="hidden" name="act" value="hasilujian">
            <div class="form-group" style="margin-right:10px;">
                <label for="ujian_id_hasil" style="margin-right:8px;">Nama Ujian</label>
                <select name="ujian_id" id="ujian_id_hasil" class="form-control">
                    <option value="">Semua Ujian</option>
                    <?php foreach ($daftar_ujian as $u) { ?>
                        <option value="<?php echo (int) $u['id_soal']; ?>" <?php if ($selected_ujian_id === (int) $u['id_soal']) { echo 'selected'; } ?>><?php echo htmlspecialchars($u['nm_ujian']); ?></option>
                    <?php } ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="?module=ujian" class="btn btn-default">Kembali</a>
        </form>

        <?php if (!empty($error_load_hasil)) { ?>
            <div class="alert alert-warning"><?php echo $error_load_hasil; ?></div>
        <?php } elseif (empty($daftar_hasil)) { ?>
            <div class="alert alert-info">Belum ada hasil ujian.</div>
        <?php } else { ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="example1">
                    <thead>
                        <tr>
                            <th>Nama Lengkap</th>
                            <th>Nama Ujian</th>
                            <th>Total Soal</th>
                            <th>Jawaban Benar</th>
                            <th>Jawaban Salah</th>
                            <th>Tidak dijawab</th>
                            <th>Nilai akhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($daftar_hasil as $h) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars((string) $h['nama_lengkap']); ?></td>
                                <td><?php echo htmlspecialchars((string) $h['nama_ujian']); ?></td>
                                <td><?php echo (int) $h['total_soal']; ?></td>
                                <td><?php echo (int) $h['jawaban_benar']; ?></td>
                                <td><?php echo (int) $h['jawaban_salah']; ?></td>
                                <td><?php echo (int) $h['tidak_dijawab']; ?></td>
                                <td><?php echo (float) $h['nilai_akhir']; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } ?>
    </div>
</div>

<?php
        } elseif ($act === 'tambahsoal') {
?>

<div class="box box-primary box-solid">
    <div class="box-header with-border">
        <h3 class="box-title">Tambah Soal Ujian</h3>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <form method="POST" action="<?php echo $aksi; ?>?module=ujian&act=simpansoal" class="form-horizontal">
            <?php
                $prefill_ujian = null;
                if ($prefill_ujian_id > 0) {
                    foreach ($daftar_ujian as $u) {
                        if ((int) $u['id_soal'] === $prefill_ujian_id) {
                            $prefill_ujian = $u;
                            break;
                        }
                    }
                }

                // Fallback ke ujian terbaru jika ujian_id tidak valid/tidak dikirim.
                if (!$prefill_ujian && !empty($daftar_ujian)) {
                    $prefill_ujian = $daftar_ujian[0];
                    $_SESSION['ujian_aktif_id'] = (int) $prefill_ujian['id_soal'];
                }
            ?>

            <?php if ($prefill_ujian) { ?>
                <input type="hidden" name="id_soal" value="<?php echo (int) $prefill_ujian['id_soal']; ?>">
                <div class="form-group">
                    <label class="col-sm-2 control-label">Nama Ujian  =</label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($prefill_ujian['nm_ujian']); ?>" readonly>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">Durasi (menit) menunjukkan lama ujian =</label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control" value="<?php echo (int) $prefill_ujian['durasi']; ?>" readonly>
                    </div>
                </div>
            <?php } else { ?>
                <div class="alert alert-warning">Belum ada Nama Ujian. Silakan simpan Nama Ujian terlebih dahulu pada halaman Kelola Soal Ujian.</div>
            <?php } ?>
            <div class="form-group">
                <label class="col-sm-2 control-label">Pertanyaan</label>
                <div class="col-sm-8">
                    <textarea name="pertanyaan" id="pertanyaan_tambah" class="form-control" rows="6" required></textarea>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">Opsi A</label>
                <div class="col-sm-6">
                    <input type="text" name="opsi_a" class="form-control" required>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">Opsi B</label>
                <div class="col-sm-6">
                    <input type="text" name="opsi_b" class="form-control" required>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">Opsi C</label>
                <div class="col-sm-6">
                    <input type="text" name="opsi_c" class="form-control" required>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">Kunci Jawaban</label>
                <div class="col-sm-2">
                    <select name="jawaban_benar" class="form-control" required>
                        <option value="a">A</option>
                        <option value="b">B</option>
                        <option value="c">C</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label"></label>
                <div class="col-sm-6">
                    <button type="submit" class="btn btn-primary" <?php if (!$prefill_ujian) { echo 'disabled'; } ?>>Simpan</button>
                    <a href="?module=ujian&act=kelola&ujian_id=<?php echo (int) ($prefill_ujian ? $prefill_ujian['id_soal'] : $selected_ujian_id); ?>" class="btn btn-default">Kembali</a>
                </div>
            </div>
        </form>

        <script>
            if (typeof CKEDITOR !== 'undefined') {
                if (CKEDITOR.instances.pertanyaan_tambah) {
                    CKEDITOR.instances.pertanyaan_tambah.destroy(true);
                }
                CKEDITOR.replace('pertanyaan_tambah', {
                    height: 180
                });
            }
        </script>
    </div>
</div>

<?php
        } elseif ($act === 'editsoal') {
            $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
            $stmtEdit = $db->prepare("SELECT id, id_soal, pertanyaan, opsi_a, opsi_b, opsi_c, jawaban_benar FROM soal WHERE id = ? LIMIT 1");
            $stmtEdit->execute(array($id));
            $soalEdit = $stmtEdit->fetch(PDO::FETCH_ASSOC);

            if (!$soalEdit) {
                echo "<div class='alert alert-warning'>Soal tidak ditemukan.</div>";
            } else {
?>

<div class="box box-primary box-solid">
    <div class="box-header with-border">
        <h3 class="box-title">Edit Soal Ujian</h3>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <form method="POST" action="<?php echo $aksi; ?>?module=ujian&act=updatesoal" class="form-horizontal">
            <input type="hidden" name="id" value="<?php echo (int) $soalEdit['id']; ?>">
            <div class="form-group">
                <label class="col-sm-2 control-label">Nama Ujian (nm_ujian) =</label>
                <div class="col-sm-5">
                    <select name="id_soal" id="id_soal_edit" class="form-control" required>
                        <option value="">-- Pilih Nama Ujian --</option>
                        <?php foreach ($daftar_ujian as $u) { ?>
                            <option value="<?php echo (int) $u['id_soal']; ?>" data-durasi="<?php echo (int) $u['durasi']; ?>" <?php if ((int) $soalEdit['id_soal'] === (int) $u['id_soal']) { echo 'selected'; } ?>><?php echo htmlspecialchars($u['nm_ujian']); ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">Durasi (menit) menunjukkan lama ujian =</label>
                <div class="col-sm-2">
                    <input type="text" id="durasi_edit" class="form-control" readonly>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">Pertanyaan</label>
                <div class="col-sm-8">
                    <textarea name="pertanyaan" id="pertanyaan_edit" class="form-control" rows="6" required><?php echo htmlspecialchars($soalEdit['pertanyaan']); ?></textarea>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">Opsi A</label>
                <div class="col-sm-6">
                    <input type="text" name="opsi_a" class="form-control" value="<?php echo htmlspecialchars($soalEdit['opsi_a']); ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">Opsi B</label>
                <div class="col-sm-6">
                    <input type="text" name="opsi_b" class="form-control" value="<?php echo htmlspecialchars($soalEdit['opsi_b']); ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">Opsi C</label>
                <div class="col-sm-6">
                    <input type="text" name="opsi_c" class="form-control" value="<?php echo htmlspecialchars($soalEdit['opsi_c']); ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">Kunci Jawaban</label>
                <div class="col-sm-2">
                    <select name="jawaban_benar" class="form-control" required>
                        <option value="a" <?php if ($soalEdit['jawaban_benar'] === 'a') { echo 'selected'; } ?>>A</option>
                        <option value="b" <?php if ($soalEdit['jawaban_benar'] === 'b') { echo 'selected'; } ?>>B</option>
                        <option value="c" <?php if ($soalEdit['jawaban_benar'] === 'c') { echo 'selected'; } ?>>C</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label"></label>
                <div class="col-sm-6">
                    <button type="submit" class="btn btn-primary">Update</button>
                    <a href="?module=ujian&act=kelola&ujian_id=<?php echo (int) $soalEdit['id_soal']; ?>" class="btn btn-default">Kembali</a>
                </div>
            </div>
        </form>

        <script>
            if (typeof CKEDITOR !== 'undefined') {
                if (CKEDITOR.instances.pertanyaan_edit) {
                    CKEDITOR.instances.pertanyaan_edit.destroy(true);
                }
                CKEDITOR.replace('pertanyaan_edit', {
                    height: 180
                });
            }

            (function () {
                var selectEl = document.getElementById('id_soal_edit');
                var durasiEl = document.getElementById('durasi_edit');
                if (!selectEl || !durasiEl) {
                    return;
                }

                function updateDurasi() {
                    var opt = selectEl.options[selectEl.selectedIndex];
                    var durasi = opt ? (opt.getAttribute('data-durasi') || '') : '';
                    durasiEl.value = durasi;
                }

                selectEl.addEventListener('change', updateDurasi);
                updateDurasi();
            })();
        </script>
    </div>
</div>

<?php
            }
        } else {
?>

<div class="box box-primary box-solid">
    <div class="box-header with-border">
        <h3 class="box-title">Pengerjaan Ujian</h3>
        <div class="box-tools pull-right">
            <button class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
        </div>
    </div>
    <div class="box-body">
        <?php if ($isPemilik) { ?>
            <div class="alert alert-info" style="margin-bottom: 15px;">
                Anda login sebagai <strong>pemilik</strong>. Kelola soal melalui tombol berikut.
                <a href="?module=ujian&act=kelola" class="btn btn-success btn-xs" style="margin-left:10px;">Kelola Soal</a>
            </div>
        <?php } ?>

        <form method="GET" class="form-horizontal" style="margin-bottom: 15px;">
            <input type="hidden" name="module" value="ujian">
            <div class="form-group">
                <label class="col-sm-2 control-label">Nama Ujian (nm_ujian) =</label>
                <div class="col-sm-5">
                    <select name="ujian_id" id="ujian_id_filter" class="form-control" required>
                        <option value="">-- Pilih Ujian --</option>
                        <?php foreach ($daftar_ujian as $u) { ?>
                            <option value="<?php echo (int) $u['id_soal']; ?>" data-durasi="<?php echo (int) $u['durasi']; ?>" <?php if ($selected_ujian_id === (int) $u['id_soal']) { echo 'selected'; } ?>><?php echo htmlspecialchars($u['nm_ujian']); ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-sm-2">
                    <button type="submit" class="btn btn-primary">Tampilkan Soal</button>
                    <button type="submit" name="act" value="hasilujian" class="btn btn-success">Hasil Ujian</button>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label">Durasi (menit) menunjukkan lama ujian =</label>
                <div class="col-sm-2">
                    <input type="text" id="durasi_filter" class="form-control" value="<?php echo (int) $durasi_ujian_menit; ?>" readonly>
                </div>
            </div>
        </form>

        <script>
            (function () {
                var selectEl = document.getElementById('ujian_id_filter');
                var durasiEl = document.getElementById('durasi_filter');
                if (!selectEl || !durasiEl) {
                    return;
                }

                function updateDurasi() {
                    var opt = selectEl.options[selectEl.selectedIndex];
                    var durasi = opt ? (opt.getAttribute('data-durasi') || '') : '';
                    durasiEl.value = durasi;
                }

                selectEl.addEventListener('change', updateDurasi);
                updateDurasi();
            })();
        </script>

        <?php if (!empty($error_load_soal)) { ?>
            <div class="alert alert-warning"><?php echo $error_load_soal; ?></div>
        <?php } elseif ($selected_ujian_id <= 0) { ?>
            <div class="alert alert-info">Silakan pilih Nama Ujian terlebih dahulu.</div>
        <?php } elseif (empty($daftar_soal)) { ?>
            <div class="alert alert-info">Belum ada soal ujian yang bisa dikerjakan.</div>
        <?php } else { ?>
            <?php
                $soal_ujian = $daftar_soal;
                shuffle($soal_ujian);
                $waktu_mulai_unix = time();
            ?>

            <div class="alert alert-danger">
                Nama Ujian: <strong><?php echo htmlspecialchars((string) $ujian_aktif['nm_ujian']); ?></strong><br>
                Batas Waktu Ujian: <strong><?php echo (int) $durasi_ujian_menit; ?> menit</strong><br>
                Sisa Waktu: <strong><span id="timer-countdown">--:--</span></strong>
            </div>

            <form method="POST" action="modul/mod_ujian/proses.php" id="form-ujian">
                <input type="hidden" name="ujian_id" value="<?php echo (int) $selected_ujian_id; ?>">
                <input type="hidden" name="exam_started_at" value="<?php echo $waktu_mulai_unix; ?>">
                <input type="hidden" name="exam_duration_seconds" value="<?php echo (int) $durasi_ujian_detik; ?>">
                <input type="hidden" name="auto_submitted" id="auto_submitted" value="0">

                <?php foreach ($soal_ujian as $index => $soal) { ?>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="pertanyaan-html"><strong><?php echo ($index + 1) . ". "; ?></strong><?php echo render_pertanyaan_html($soal['pertanyaan']); ?></div>

                            <div class="radio">
                                <label>
                                    <input type="radio" name="jawaban[<?php echo (int) $soal['id']; ?>]" value="a" required>
                                    <?php echo htmlspecialchars($soal['opsi_a']); ?>
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="jawaban[<?php echo (int) $soal['id']; ?>]" value="b">
                                    <?php echo htmlspecialchars($soal['opsi_b']); ?>
                                </label>
                            </div>
                            <div class="radio">
                                <label>
                                    <input type="radio" name="jawaban[<?php echo (int) $soal['id']; ?>]" value="c">
                                    <?php echo htmlspecialchars($soal['opsi_c']); ?>
                                </label>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <button type="submit" class="btn btn-primary">Kirim Jawaban</button>
                <a href="?module=home" class="btn btn-default">Kembali</a>
            </form>

            <script>
                (function () {
                    var durationSeconds = <?php echo (int) $durasi_ujian_detik; ?>;
                    var endAt = Math.floor(Date.now() / 1000) + durationSeconds;
                    var countdownEl = document.getElementById('timer-countdown');
                    var form = document.getElementById('form-ujian');
                    var autoSubmittedEl = document.getElementById('auto_submitted');

                    function formatTime(seconds) {
                        var m = Math.floor(seconds / 60);
                        var s = seconds % 60;
                        return String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
                    }

                    function tick() {
                        var now = Math.floor(Date.now() / 1000);
                        var remain = endAt - now;

                        if (remain <= 0) {
                            countdownEl.textContent = '00:00';
                            autoSubmittedEl.value = '1';
                            alert('Waktu ujian habis. Jawaban akan dikirim otomatis.');
                            form.submit();
                            return;
                        }

                        countdownEl.textContent = formatTime(remain);
                    }

                    tick();
                    setInterval(tick, 1000);
                })();
            </script>
        <?php } ?>
    </div>
</div>

<?php
        }
    }
}
?>