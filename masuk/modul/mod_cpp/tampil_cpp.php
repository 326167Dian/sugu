<?php
session_start();
include "../../../configurasi/koneksi.php";

$id_cpp = isset($_GET['id']) ? intval($_GET['id']) : 0;
$data = null;
$details = [];
$alert = '';

// Get setheader data
$tampil_setheader = $db->query("SELECT * FROM setheader ORDER BY id_setheader");
$rk = $tampil_setheader->fetch(PDO::FETCH_ASSOC);

if ($id_cpp > 0) {
    // Get CPP main data
    $stmt = $db->prepare("SELECT c.*, p.nm_pelanggan, p.alamat_pelanggan, p.tlp_pelanggan 
        FROM cpp c
        LEFT JOIN pelanggan p ON c.id_pelanggan = p.id_pelanggan
        WHERE c.id_cpp = ?");
    $stmt->execute([$id_cpp]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($data) {
        // Get CPP details
        $stmtDetail = $db->prepare("SELECT * FROM cpp_detail WHERE id_cpp = ? ORDER BY no_urut");
        $stmtDetail->execute([$id_cpp]);
        $details = $stmtDetail->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $alert = "Data CPP tidak ditemukan.";
    }
} else {
    $alert = "Parameter id CPP belum diisi.";
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Catatan Pengobatan Pasien</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php if ($alert !== '') { ?>
    <div style="background-color: #f8d7da; color: #721c24; padding: 10px; margin: 10px auto; max-width: 900px; border: 1px solid #f5c6cb; border-radius: 4px;">
        <?php echo e($alert); ?>
    </div>
<?php } ?>

    <div class="page-container" id="printable-area">
        <div class="header-section">
            <div class="title-container">
                <h2 class="main-title">CATATAN PENGOBATAN PASIEN <br><?php echo !empty($rk['satu']) ? ' ' . e($rk['satu']) : ''; ?></h2>
            </div>
            <?php if (!empty($rk['logo'])) { ?>
                <div class="logo-container">
                    <img src="../../images/<?php echo e($rk['logo']); ?>" alt="Logo" class="logo">
                </div>
            <?php } ?>
        </div>

        <div class="identity-section">
            <div class="info-group">
                <div class="label">Nama Pasien</div>
                <div class="colon">:</div>
                <div class="value"><input type="text" class="fill-input" value="<?php echo e($data['nama_pasien'] ?? ''); ?>" readonly></div>
            </div>
            <div class="info-group">
                <div class="label">Jenis Kelamin</div>
                <div class="colon">:</div>
                <div class="value">
                    <input type="text" class="fill-input" style="width: 150px;" value="<?php echo e($data['jk'] ?? ''); ?>" readonly>
                    <span style="margin-left: 100px;">Umur : </span>
                    <input type="text" class="fill-input" style="width: 80px;" value="<?php echo e($data['umur'] ?? ''); ?>" readonly>
                </div>
            </div>
            <div class="info-group">
                <div class="label">Alamat</div>
                <div class="colon">:</div>
                <div class="value"><input type="text" class="fill-input" value="<?php echo e($data['alamat'] ?? ''); ?>" readonly></div>
            </div>
            <div class="info-group">
                <div class="label">No. Telepon</div>
                <div class="colon">:</div>
                <div class="value"><input type="text" class="fill-input" value="<?php echo e($data['telp'] ?? ''); ?>" readonly></div>
            </div>
        </div>

        <table class="medication-table">
            <thead>
                <tr>
                    <th style="width: 40px;">No.</th>
                    <th style="width: 100px;">Tanggal</th>
                    <th style="width: 150px;">Nama Dokter</th>
                    <th>Nama Obat/ Dosis/ Cara Pemberian</th>
                </tr>
            </thead>
            <tbody id="table-body">
                <?php 
                if (!empty($details)) {
                    foreach ($details as $idx => $detail) {
                        ?>
                        <tr>
                            <td style="text-align: center;"><?php echo $idx + 1; ?></td>
                            <td><input type="text" class="table-input" value="<?php echo e($detail['tanggal']); ?>" readonly></td>
                            <td><input type="text" class="table-input" value="<?php echo e($detail['nama_dokter']); ?>" readonly></td>
                            <td>
                                <textarea class="table-area" rows="4" readonly><?php echo e($detail['nama_obat_dosis']); ?></textarea>
                                <?php if (!empty($detail['catatan'])) { ?>
                                <div style="margin-top: 8px; padding-top: 8px; border-top: 1px solid #ddd;">
                                    <strong style="font-size: 11px;">Catatan:</strong><br>
                                    <textarea class="table-area" rows="2" readonly style="margin-top: 3px;"><?php echo e($detail['catatan']); ?></textarea>
                                </div>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    // Show 3 empty rows if no data
                    for ($i = 1; $i <= 3; $i++) {
                        ?>
                        <tr>
                            <td style="text-align: center;"><?php echo $i; ?></td>
                            <td><input type="text" class="table-input" readonly></td>
                            <td><input type="text" class="table-input" readonly></td>
                            <td><textarea class="table-area" rows="4" readonly></textarea></td>
                        </tr>
                        <?php
                    }
                }
                ?>
            </tbody>
        </table>

        <div class="signature-section">
            <p>Bekasi, <input type="text" class="input-line" style="width: 150px;" value="<?php echo e($data['tgl_ttd'] ?? ''); ?>" readonly> 20<input type="text" class="input-line" style="width: 30px;" value="<?php echo e($data['thn_ttd'] ?? ''); ?>" readonly></p>
            
            <div class="signature-box">
                <input type="text" class="fill-input" style="text-align: center; font-weight: bold; text-decoration: underline;" value="<?php echo e($data['nama_apoteker'] ?? ''); ?>" readonly>
                <p>SIPA. <input type="text" class="fill-input" style="text-align: center; width: 200px;" value="<?php echo e($data['sipa_apoteker'] ?? ''); ?>" readonly></p>
            </div>
        </div>
    </div>

    <div class="no-print control-panel">
        <button onclick="window.print()" class="btn-print">Cetak Manual</button>
        <button onclick="window.close()" class="btn-reset">Tutup</button>
    </div>

</body>
</html>
