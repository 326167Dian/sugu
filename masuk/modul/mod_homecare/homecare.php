<?php
session_start();
include "../../../configurasi/koneksi.php";
if (empty($_SESSION['username']) and empty($_SESSION['passuser'])) {
    echo "<link href=../css/style.css rel=stylesheet type=text/css>";
    echo "<div class='error msg'>Untuk mengakses Modul anda harus login.</div>";
} else {

$aksi = "modul/mod_homecare/aksi_homecare.php";
$act = isset($_GET['act']) ? $_GET['act'] : '';

switch ($act) {
    case 'input_homecare':
        $id_pelanggan = isset($_GET['id_pelanggan']) ? intval($_GET['id_pelanggan']) : 0;
        
        // Get customer data
        $pelanggan = null;
        if ($id_pelanggan > 0) {
            $stmt = $db->prepare("SELECT * FROM pelanggan WHERE id_pelanggan = ?");
            $stmt->execute([$id_pelanggan]);
            $pelanggan = $stmt->fetch(PDO::FETCH_ASSOC);
        }
        
        // Generate HOMECARE number
        $getHC = $db->query("SELECT no_homecare FROM homecare ORDER BY id_homecare DESC LIMIT 1");
        if ($getHC->rowCount() > 0) {
            $dataHC = $getHC->fetch(PDO::FETCH_ASSOC);
            $lastNo = intval(substr($dataHC['no_homecare'], 2));
            $newNo = $lastNo + 1;
        } else {
            $newNo = 1;
        }
        $no_homecare = 'HC' . str_pad($newNo, 4, '0', STR_PAD_LEFT);
        
        // Calculate age from birthdate
        $umur = '';
        if ($pelanggan && !empty($pelanggan['tanggal_lahir'])) {
            $birthDate = new DateTime($pelanggan['tanggal_lahir']);
            $today = new DateTime('today');
            $umur = $birthDate->diff($today)->y . ' tahun';
        }
        ?>
        <div class="inner">
            <div class="row">
                <div class="col-lg-12">
                    <h2>Input Home Pharmacy Care</h2>
                </div>
            </div>
            <hr />
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Form Input Home Care
                        </div>
                        <div class="panel-body">
                            <form action="modul/mod_homecare/aksi_homecare.php?module=homecare&act=input" method="POST">
                                <input type="hidden" name="id_pelanggan" value="<?php echo $id_pelanggan; ?>">
                                <input type="hidden" name="created_by" value="<?php echo $_SESSION['namalengkap'] ?? ''; ?>">
                                
                                <div class="form-group">
                                    <label>No. Home Care</label>
                                    <input type="text" name="no_homecare" class="form-control" value="<?php echo $no_homecare; ?>" readonly />
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
                                            <label>Umur</label>
                                            <input type="text" name="umur" class="form-control" value="<?php echo $umur; ?>" readonly />
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
                                <h4>Detail Kunjungan</h4>
                                <div id="detail-container">
                                    <div class="detail-row panel panel-default" style="margin-bottom: 15px; padding: 15px;">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <div class="form-group">
                                                    <label>Tanggal Kunjungan</label>
                                                    <input type="text" name="tgl_kunjungan[]" class="form-control" placeholder="DD/MM/YYYY" />
                                                </div>
                                            </div>
                                            <div class="col-md-9">
                                                <div class="form-group">
                                                    <label>Catatan Apoteker</label>
                                                    <textarea name="catatan_apoteker[]" class="form-control" rows="3"></textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <button type="button" class="btn btn-info btn-sm" onclick="addDetailRow()">
                                    <i class="fa fa-plus"></i> Tambah Baris Kunjungan
                                </button>
                                
                                <div class="form-group" style="margin-top: 20px;">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-save"></i> Simpan Home Care
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
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Tanggal Kunjungan</label>
                            <input type="text" name="tgl_kunjungan[]" class="form-control" placeholder="DD/MM/YYYY" />
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Catatan Apoteker</label>
                            <textarea name="catatan_apoteker[]" class="form-control" rows="3"></textarea>
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
        $id_homecare = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        // Get HOMECARE data
        $stmt = $db->prepare("SELECT h.*, p.nm_pelanggan, p.alamat_pelanggan, p.tlp_pelanggan 
            FROM homecare h
            LEFT JOIN pelanggan p ON h.id_pelanggan = p.id_pelanggan
            WHERE h.id_homecare = ?");
        $stmt->execute([$id_homecare]);
        $homecare = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$homecare) {
            echo '<div class="alert alert-danger">Data Home Care tidak ditemukan.</div>';
            break;
        }
        
        // Get detail data
        $stmtDetail = $db->prepare("SELECT * FROM homecare_detail WHERE id_homecare = ? ORDER BY no_urut");
        $stmtDetail->execute([$id_homecare]);
        $details = $stmtDetail->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <div class="inner">
            <div class="row">
                <div class="col-lg-12">
                    <h2>Edit Home Pharmacy Care</h2>
                </div>
            </div>
            <hr />
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Form Edit Home Care
                        </div>
                        <div class="panel-body">
                            <form action="modul/mod_homecare/aksi_homecare.php?module=homecare&act=update" method="POST">
                                <input type="hidden" name="id_homecare" value="<?php echo $id_homecare; ?>">
                                
                                <div class="form-group">
                                    <label>No. Home Care</label>
                                    <input type="text" name="no_homecare" class="form-control" value="<?php echo htmlspecialchars($homecare['no_homecare']); ?>" readonly />
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Nama Pasien *</label>
                                            <input type="text" name="nama_pasien" class="form-control" 
                                                value="<?php echo htmlspecialchars($homecare['nama_pasien']); ?>" required />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Umur</label>
                                            <input type="text" name="umur" class="form-control" value="<?php echo htmlspecialchars($homecare['umur']); ?>" />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label>Alamat</label>
                                    <input type="text" name="alamat" class="form-control" 
                                        value="<?php echo htmlspecialchars($homecare['alamat']); ?>" />
                                </div>
                                
                                <div class="form-group">
                                    <label>No. Telepon</label>
                                    <input type="text" name="telp" class="form-control" 
                                        value="<?php echo htmlspecialchars($homecare['telp']); ?>" />
                                </div>
                                
                                <hr>
                                <h4>Detail Kunjungan</h4>
                                <div id="detail-container">
                                    <?php 
                                    if (empty($details)) {
                                        // Add one empty row if no details
                                        ?>
                                        <div class="detail-row panel panel-default" style="margin-bottom: 15px; padding: 15px;">
                                            <input type="hidden" name="id_detail[]" value="0">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label>Tanggal Kunjungan</label>
                                                        <input type="text" name="tgl_kunjungan[]" class="form-control" placeholder="DD/MM/YYYY" />
                                                    </div>
                                                </div>
                                                <div class="col-md-9">
                                                    <div class="form-group">
                                                        <label>Catatan Apoteker</label>
                                                        <textarea name="catatan_apoteker[]" class="form-control" rows="3"></textarea>
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
                                                    <div class="col-md-3">
                                                        <div class="form-group">
                                                            <label>Tanggal Kunjungan</label>
                                                            <input type="text" name="tgl_kunjungan[]" class="form-control" 
                                                                value="<?php echo htmlspecialchars($detail['tgl_kunjungan']); ?>" placeholder="DD/MM/YYYY" />
                                                        </div>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <div class="form-group">
                                                            <label>Catatan Apoteker</label>
                                                            <textarea name="catatan_apoteker[]" class="form-control" rows="3"><?php echo htmlspecialchars($detail['catatan_apoteker']); ?></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1">
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
                                    <i class="fa fa-plus"></i> Tambah Baris Kunjungan
                                </button>
                                
                                <div class="form-group" style="margin-top: 20px;">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-save"></i> Update Home Care
                                    </button>
                                    <a href="?module=homecare" class="btn btn-default">
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
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Tanggal Kunjungan</label>
                            <input type="text" name="tgl_kunjungan[]" class="form-control" placeholder="DD/MM/YYYY" />
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Catatan Apoteker</label>
                            <textarea name="catatan_apoteker[]" class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="col-md-1">
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
                    <h2>Daftar Home Pharmacy Care</h2>
                </div>
            </div>
            <hr />
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Data Home Care
                        </div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover" id="dataTables-homecare">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>No. Home Care</th>
                                            <th>Nama Pasien</th>
                                            <th>Umur</th>
                                            <th>Alamat</th>
                                            <th>Tanggal Dibuat</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = $db->query("SELECT h.*, p.nm_pelanggan 
                                            FROM homecare h
                                            LEFT JOIN pelanggan p ON h.id_pelanggan = p.id_pelanggan
                                            ORDER BY h.id_homecare DESC");
                                        $no = 1;
                                        while ($data = $query->fetch(PDO::FETCH_ASSOC)) {
                                            ?>
                                            <tr>
                                                <td><?php echo $no++; ?></td>
                                                <td><?php echo htmlspecialchars($data['no_homecare']); ?></td>
                                                <td><?php echo htmlspecialchars($data['nama_pasien']); ?></td>
                                                <td><?php echo htmlspecialchars($data['umur']); ?></td>
                                                <td><?php echo htmlspecialchars($data['alamat']); ?></td>
                                                <td><?php echo $data['created_at']; ?></td>
                                                <td>
                                                    <a href="?module=homecare&act=edit&id=<?php echo $data['id_homecare']; ?>" 
                                                        class="btn btn-info btn-xs" title="Edit">
                                                        <i class="fa fa-edit"></i> Edit
                                                    </a>
                                                    <a href="modul/mod_homecare/tampil_homecare_new.php?id=<?php echo $data['id_homecare']; ?>" 
                                                        target="_blank" class="btn btn-success btn-xs" title="Lihat/Cetak">
                                                        <i class="fa fa-print"></i> Cetak
                                                    </a>
                                                    <a href="javascript:confirmdelete('modul/mod_homecare/aksi_homecare.php?module=homecare&act=hapus&id=<?php echo $data['id_homecare']; ?>')" 
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
            $('#dataTables-homecare').DataTable({
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
