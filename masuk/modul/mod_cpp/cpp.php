<?php
session_start();
include "../../../configurasi/koneksi.php";
if (empty($_SESSION['username']) and empty($_SESSION['passuser'])) {
    echo "<link href=../css/style.css rel=stylesheet type=text/css>";
    echo "<div class='error msg'>Untuk mengakses Modul anda harus login.</div>";
} else {

$aksi = "modul/mod_cpp/aksi_cpp.php";
$act = isset($_GET['act']) ? $_GET['act'] : '';

switch ($act) {
    case 'input_cpp':
        $id_pelanggan = isset($_GET['id_pelanggan']) ? intval($_GET['id_pelanggan']) : 0;
        
        // Get customer data
        $pelanggan = null;
        if ($id_pelanggan > 0) {
            $stmt = $db->prepare("SELECT * FROM pelanggan WHERE id_pelanggan = ?");
            $stmt->execute([$id_pelanggan]);
            $pelanggan = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        // Generate CPP number
        $getCPP = $db->query("SELECT no_cpp FROM cpp ORDER BY id_cpp DESC LIMIT 1");
        if ($getCPP->rowCount() > 0) {
            $dataCPP = $getCPP->fetch(PDO::FETCH_ASSOC);
            $lastNo = intval(substr($dataCPP['no_cpp'], 3));
            $newNo = $lastNo + 1;
        } else {
            $newNo = 1;
        }
        $no_cpp = 'CPP' . str_pad($newNo, 4, '0', STR_PAD_LEFT);
        
        // Get setheader data for apoteker
        $getSetheader = $db->query("SELECT * FROM setheader ORDER BY id_setheader LIMIT 1");
        $setheader = $getSetheader->fetch(PDO::FETCH_ASSOC);
        $nama_apoteker = $setheader['empat'] ?? '';
        $sipa_apoteker = $setheader['tujuh'] ?? '';
        
        // Get jenis kelamin from pelanggan
        $jenis_kelamin = '';
        if ($pelanggan && !empty($pelanggan['jenis_kelamin'])) {
            // Map PRIA/WANITA to Laki-laki/Perempuan
            if ($pelanggan['jenis_kelamin'] == 'PRIA') {
                $jenis_kelamin = 'Laki-laki';
            } elseif ($pelanggan['jenis_kelamin'] == 'WANITA') {
                $jenis_kelamin = 'Perempuan';
            }
        }
        
        // Calculate age from birthdate
        $umur = '';
        if ($pelanggan && !empty($pelanggan['tanggal_lahir'])) {
            $birthDate = new DateTime($pelanggan['tanggal_lahir']);
            $today = new DateTime('today');
            $umur = $birthDate->diff($today)->y . ' tahun';
        }
        
        // Generate tanggal TTD (format: 14 Februari 2026)
        $bulan_indonesia = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $tanggal_sekarang = new DateTime();
        $tgl_ttd = $tanggal_sekarang->format('d') . ' ' . $bulan_indonesia[(int)$tanggal_sekarang->format('n')] . ' ' . $tanggal_sekarang->format('Y');
        $thn_ttd = $tanggal_sekarang->format('y'); // 2 digit tahun
        ?>
        <div class="inner">
            
            <div class="row">
                <div class="col-lg-12">
                    <h2>Input Catatan Pengobatan Pasien (CPP)</h2>
                </div>
            </div>
            <hr />
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Form Input CPP
                        </div>
                        <div class="panel-body">
                            <form action="modul/mod_cpp/aksi_cpp.php?module=cpp&act=input" method="POST">
                                <input type="hidden" name="id_pelanggan" value="<?php echo $id_pelanggan; ?>">
                                <input type="hidden" name="created_by" value="<?php echo $_SESSION['namalengkap'] ?? ''; ?>">
                                
                                <div class="form-group">
                                    <label>No. CPP</label>
                                    <input type="text" name="no_cpp" class="form-control" value="<?php echo $no_cpp; ?>" readonly />
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Nama Pasien *</label>
                                            <input type="text" name="nama_pasien" class="form-control" 
                                                value="<?php echo $pelanggan ? htmlspecialchars($pelanggan['nm_pelanggan']) : ''; ?>" required />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Jenis Kelamin *</label>
                                            <select name="jk" class="form-control" required>
                                                <option value="">Pilih</option>
                                                <option value="Laki-laki" <?php echo ($jenis_kelamin == 'Laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
                                                <option value="Perempuan" <?php echo ($jenis_kelamin == 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Umur</label>
                                            <input type="text" name="umur" class="form-control" value="<?php echo $umur; ?>" placeholder="contoh: 25 tahun" readonly />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label>Alamat</label>
                                    <input type="text" name="alamat" class="form-control" 
                                        value="<?php echo $pelanggan ? htmlspecialchars($pelanggan['alamat_pelanggan']) : ''; ?>" />
                                </div>
                                
                                <div class="form-group">
                                    <label>No. Telepon</label>
                                    <input type="text" name="telp" class="form-control" 
                                        value="<?php echo $pelanggan ? htmlspecialchars($pelanggan['tlp_pelanggan']) : ''; ?>" />
                                </div>
                                
                                <hr>
                                <h4>Detail Obat</h4>
                                <div id="detail-container">
                                    <div class="detail-row panel panel-default" style="margin-bottom: 15px; padding: 15px;">
                                        <div class="row">
                                            <div class="col-md-2">
                                                <div class="form-group">
                                                    <label>Tanggal</label>
                                                    <input type="text" name="tanggal[]" class="form-control" placeholder="DD/MM/YYYY" />
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Nama Dokter</label>
                                                    <input type="text" name="nama_dokter[]" class="form-control" />
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <label>Nama Obat/Dosis/Cara Pemberian</label>
                                                    <textarea name="nama_obat_dosis[]" class="form-control" rows="3"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Catatan</label>
                                                    <textarea name="catatan[]" class="form-control" rows="3"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="button" class="btn btn-info btn-sm" onclick="addDetailRow()">
                                    <i class="fa fa-plus"></i> Tambah Baris Obat
                                </button>
                                
                                <hr>
                                <h4>Tanda Tangan</h4>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Tanggal TTD</label>
                                            <input type="text" name="tgl_ttd" class="form-control" value="<?php echo htmlspecialchars($tgl_ttd); ?>" placeholder="contoh: 15 Februari 2025" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Tahun (2 digit)</label>
                                            <input type="text" name="thn_ttd" class="form-control" value="<?php echo htmlspecialchars($thn_ttd); ?>" placeholder="25" maxlength="2" />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Nama Apoteker</label>
                                            <input type="text" name="nama_apoteker" class="form-control" value="<?php echo htmlspecialchars($nama_apoteker); ?>" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>No. SIPA</label>
                                            <input type="text" name="sipa_apoteker" class="form-control" value="<?php echo htmlspecialchars($sipa_apoteker); ?>" />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group" style="margin-top: 20px;">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-save"></i> Simpan CPP
                                    </button>
                                    <a href="?module=pelanggan" class="btn btn-default">
                                        <i class="fa fa-arrow-left"></i> Kembali
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
        function addDetailRow() {
            var container = document.getElementById('detail-container');
            var newRow = document.createElement('div');
            newRow.className = 'detail-row panel panel-default';
            newRow.style.marginBottom = '15px';
            newRow.style.padding = '15px';
            newRow.innerHTML = `
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Tanggal</label>
                            <input type="text" name="tanggal[]" class="form-control" placeholder="DD/MM/YYYY" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Nama Dokter</label>
                            <input type="text" name="nama_dokter[]" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Nama Obat/Dosis/Cara Pemberian</label>
                            <textarea name="nama_obat_dosis[]" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Catatan</label>
                            <textarea name="catatan[]" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <div class="form-group">
                            <label>&nbsp;</label><br>
                            <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            container.appendChild(newRow);
        }
        
        function removeRow(btn) {
            btn.closest('.detail-row').remove();
        }
        </script>
        <?php
        break;

    case 'edit':
        $id_cpp = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        // Get CPP data
        $stmt = $db->prepare("SELECT c.*, p.nm_pelanggan, p.alamat_pelanggan, p.tlp_pelanggan 
            FROM cpp c
            LEFT JOIN pelanggan p ON c.id_pelanggan = p.id_pelanggan
            WHERE c.id_cpp = ?");
        $stmt->execute([$id_cpp]);
        $cpp = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$cpp) {
            echo '<div class="alert alert-danger">Data CPP tidak ditemukan.</div>';
            break;
        }
        
        // Get detail data
        $stmtDetail = $db->prepare("SELECT * FROM cpp_detail WHERE id_cpp = ? ORDER BY no_urut");
        $stmtDetail->execute([$id_cpp]);
        $details = $stmtDetail->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <div class="inner">
            <div class="row">
                <div class="col-lg-12">
                    <h2>Edit Catatan Pengobatan Pasien (CPP)</h2>
                </div>
            </div>
            <hr />
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Form Edit CPP
                        </div>
                        <div class="panel-body">
                            <form action="modul/mod_cpp/aksi_cpp.php?module=cpp&act=update" method="POST">
                                <input type="hidden" name="id_cpp" value="<?php echo $id_cpp; ?>">
                                
                                <div class="form-group">
                                    <label>No. CPP</label>
                                    <input type="text" name="no_cpp" class="form-control" value="<?php echo htmlspecialchars($cpp['no_cpp']); ?>" readonly />
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Nama Pasien *</label>
                                            <input type="text" name="nama_pasien" class="form-control" 
                                                value="<?php echo htmlspecialchars($cpp['nama_pasien']); ?>" required />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Jenis Kelamin *</label>
                                            <select name="jk" class="form-control" required>
                                                <option value="">Pilih</option>
                                                <option value="Laki-laki" <?php echo ($cpp['jk'] == 'Laki-laki') ? 'selected' : ''; ?>>Laki-laki</option>
                                                <option value="Perempuan" <?php echo ($cpp['jk'] == 'Perempuan') ? 'selected' : ''; ?>>Perempuan</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Umur</label>
                                            <input type="text" name="umur" class="form-control" value="<?php echo htmlspecialchars($cpp['umur']); ?>" />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label>Alamat</label>
                                    <input type="text" name="alamat" class="form-control" 
                                        value="<?php echo htmlspecialchars($cpp['alamat']); ?>" />
                                </div>
                                
                                <div class="form-group">
                                    <label>No. Telepon</label>
                                    <input type="text" name="telp" class="form-control" 
                                        value="<?php echo htmlspecialchars($cpp['telp']); ?>" />
                                </div>
                                
                                <hr>
                                <h4>Detail Obat</h4>
                                <div id="detail-container">
                                    <?php 
                                    if (empty($details)) {
                                        // Add one empty row if no details
                                        ?>
                                        <div class="detail-row panel panel-default" style="margin-bottom: 15px; padding: 15px;">
                                            <input type="hidden" name="id_detail[]" value="0">
                                            <div class="row">
                                                <div class="col-md-2">
                                                    <div class="form-group">
                                                        <label>Tanggal</label>
                                                        <input type="text" name="tanggal[]" class="form-control" placeholder="DD/MM/YYYY" />
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Nama Dokter</label>
                                                        <input type="text" name="nama_dokter[]" class="form-control" />
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label>Nama Obat/Dosis/Cara Pemberian</label>
                                                        <textarea name="nama_obat_dosis[]" class="form-control" rows="3"></textarea>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Catatan</label>
                                                        <textarea name="catatan[]" class="form-control" rows="3"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                    } else {
                                        foreach ($details as $idx => $detail) {
                                            ?>
                                            <div class="detail-row panel panel-default" style="margin-bottom: 15px; padding: 15px;">
                                                <input type="hidden" name="id_detail[]" value="<?php echo $detail['id_detail']; ?>">
                                                <div class="row">
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>Tanggal</label>
                                                            <input type="text" name="tanggal[]" class="form-control" 
                                                                value="<?php echo htmlspecialchars($detail['tanggal']); ?>" placeholder="DD/MM/YYYY" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Nama Dokter</label>
                                                            <input type="text" name="nama_dokter[]" class="form-control" 
                                                                value="<?php echo htmlspecialchars($detail['nama_dokter']); ?>" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Nama Obat/Dosis/Cara Pemberian</label>
                                                            <textarea name="nama_obat_dosis[]" class="form-control" rows="3"><?php echo htmlspecialchars($detail['nama_obat_dosis']); ?></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>Catatan</label>
                                                            <textarea name="catatan[]" class="form-control" rows="3"><?php echo htmlspecialchars($detail['catatan']); ?></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <div class="form-group">
                                                            <label>&nbsp;</label><br>
                                                            <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">
                                                                <i class="fa fa-trash"></i> Hapus
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    }
                                    ?>
                                </div>
                                
                                <button type="button" class="btn btn-info btn-sm" onclick="addDetailRow()">
                                    <i class="fa fa-plus"></i> Tambah Baris Obat
                                </button>
                                
                                <hr>
                                <h4>Tanda Tangan</h4>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Tanggal TTD</label>
                                            <input type="text" name="tgl_ttd" class="form-control" 
                                                value="<?php echo htmlspecialchars($cpp['tgl_ttd']); ?>" placeholder="contoh: 15 Februari 2025" />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Tahun (2 digit)</label>
                                            <input type="text" name="thn_ttd" class="form-control" 
                                                value="<?php echo htmlspecialchars($cpp['thn_ttd']); ?>" placeholder="25" maxlength="2" />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Nama Apoteker</label>
                                            <input type="text" name="nama_apoteker" class="form-control" 
                                                value="<?php echo htmlspecialchars($cpp['nama_apoteker']); ?>" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>No. SIPA</label>
                                            <input type="text" name="sipa_apoteker" class="form-control" 
                                                value="<?php echo htmlspecialchars($cpp['sipa_apoteker']); ?>" />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group" style="margin-top: 20px;">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-save"></i> Update CPP
                                    </button>
                                    <a href="?module=cpp" class="btn btn-default">
                                        <i class="fa fa-arrow-left"></i> Kembali
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
        function addDetailRow() {
            var container = document.getElementById('detail-container');
            var newRow = document.createElement('div');
            newRow.className = 'detail-row panel panel-default';
            newRow.style.marginBottom = '15px';
            newRow.style.padding = '15px';
            newRow.innerHTML = `
                <input type="hidden" name="id_detail[]" value="0">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Tanggal</label>
                            <input type="text" name="tanggal[]" class="form-control" placeholder="DD/MM/YYYY" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Nama Dokter</label>
                            <input type="text" name="nama_dokter[]" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Nama Obat/Dosis/Cara Pemberian</label>
                            <textarea name="nama_obat_dosis[]" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Catatan</label>
                            <textarea name="catatan[]" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label><br>
                            <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">
                                <i class="fa fa-trash"></i> Hapus
                            </button>
                        </div>
                    </div>
                </div>
            `;
            container.appendChild(newRow);
        }
        
        function removeRow(btn) {
            btn.closest('.detail-row').remove();
        }
        </script>
        <?php
        break;

    default:
        // List view
        ?>
        <div class="inner">
            <a class='btn btn-primary btn-flat' href='?module=pelanggan'>KEMBALI</a>
            <div class="row">
                <div class="col-lg-12">
                    <h2>Daftar Catatan Pengobatan Pasien (CPP)</h2>
                </div>
            </div>
            <hr />
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Data CPP
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-cpp">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>No. CPP</th>
                                            <th>Nama Pasien</th>
                                            <th>Jenis Kelamin</th>
                                            <th>Umur</th>
                                            <th>Tanggal Dibuat</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = $db->query("SELECT c.*, p.nm_pelanggan 
                                            FROM cpp c
                                            LEFT JOIN pelanggan p ON c.id_pelanggan = p.id_pelanggan
                                            ORDER BY c.id_cpp DESC");
                                        $no = 1;
                                        while ($data = $query->fetch(PDO::FETCH_ASSOC)) {
                                            ?>
                                            <tr>
                                                <td><?php echo $no++; ?></td>
                                                <td><?php echo htmlspecialchars($data['no_cpp']); ?></td>
                                                <td><?php echo htmlspecialchars($data['nama_pasien']); ?></td>
                                                <td><?php echo htmlspecialchars($data['jk']); ?></td>
                                                <td><?php echo htmlspecialchars($data['umur']); ?></td>
                                                <td><?php echo $data['created_at']; ?></td>
                                                <td>
                                                    <a href="?module=cpp&act=edit&id=<?php echo $data['id_cpp']; ?>" 
                                                        class="btn btn-info btn-xs" title="Edit">
                                                        <i class="fa fa-edit"></i> Edit
                                                    </a>
                                                    <a href="modul/mod_cpp/tampil_cpp.php?id=<?php echo $data['id_cpp']; ?>" 
                                                        target="_blank" class="btn btn-success btn-xs" title="Lihat/Cetak">
                                                        <i class="fa fa-print"></i> Cetak
                                                    </a>
                                                    <a href="javascript:confirmdelete('modul/mod_cpp/aksi_cpp.php?module=cpp&act=hapus&id=<?php echo $data['id_cpp']; ?>')" 
                                                        class="btn btn-danger btn-xs" title="Hapus">
                                                        <i class="fa fa-trash"></i> Hapus
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
        $(document).ready(function() {
            $('#dataTables-cpp').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Indonesian.json"
                }
            });
        });
        </script>
        <?php
        break;
}
?>
<?php
}
?>
