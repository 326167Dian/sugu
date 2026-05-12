<?php
session_start();
include "../../../configurasi/koneksi.php";

$id_konseling = isset($_GET['id']) ? intval($_GET['id']) : 0;
$data = null;
$alert = '';

$tampil_setheader = $db->query("SELECT * FROM setheader ORDER BY id_setheader ");
$rk = $tampil_setheader->fetch(PDO::FETCH_ASSOC);




if ($id_konseling > 0) {
    $stmt = $db->prepare("SELECT k.*, p.jenis_kelamin, p.tanggal_lahir, p.alamat_pelanggan
        FROM konseling k
        LEFT JOIN pelanggan p ON p.id_pelanggan = k.id_pelanggan
        WHERE k.id_konseling = ?");
    $stmt->execute([$id_konseling]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        $alert = "Data konseling tidak ditemukan.";
    }
} else {
    $alert = "Parameter id konseling belum diisi.";
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$visite_text = '';
if (!empty($data['visite'])) {
    $raw_visite = strtoupper(trim($data['visite']));
    if (in_array($raw_visite, ['YA', 'Y', 'YES', '1'], true)) {
        $visite_text = 'YA';
    } elseif (in_array($raw_visite, ['TIDAK', 'T', 'NO', '0'], true)) {
        $visite_text = 'TIDAK';
    } else {
        $visite_text = $data['visite'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dokumentasi Konseling</title>
    <link rel="stylesheet" href="style.css">
    
</head>
<body>

    <div class="container">
        <?php if ($alert !== '') { ?>
            <div class="alert alert-danger"><?php echo e($alert); ?></div>
        <?php } ?>
      
        
        <div class="border-box">
            <?php if (!empty($rk['logo'])) { ?>
                <img src="../../images/<?php echo e($rk['logo']); ?>" alt="Logo" class="logo">
            <?php } ?>
            <h1>DOKUMENTASI KONSELING <?php echo e($rk['satu'] ?? ''); ?></h1>

            <table class="form-table">
                <tr>
                    <td width="35%">Nama Pasien</td>
                    <td width="2%">:</td>
                    <td><?php echo e($data['nm_pelanggan'] ?? ''); ?></td>
                </tr>
                <tr>
                    <td>Jenis kelamin</td>
                    <td>:</td>
                    <td><?php echo e($data['jenis_kelamin'] ?? ''); ?></td>
                </tr>
                <tr>
                    <td>Tanggal lahir</td>
                    <td>:</td>
                    <td><?php echo e($data['tanggal_lahir'] ?? ''); ?></td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>:</td>
                    <td><?php echo e($data['alamat_pelanggan'] ?? ''); ?></td>
                </tr>
                <tr>
                    <td>Tanggal konseling</td>
                    <td>:</td>
                    <td><?php echo e($data['tgl_konseling'] ?? ''); ?></td>
                </tr>
                <tr>
                    <td>Nama Dokter</td>
                    <td>:</td>
                    <td><?php echo e($data['nama_dokter'] ?? ''); ?></td>
                </tr>
                <tr>
                    <td>Diagnosa</td>
                    <td>:</td>
                    <td><?php echo e($data['diagnosa'] ?? ''); ?></td>
                </tr>
                <tr class="tall-row">
                    <td colspan="3">
                        Riwayat Penyakit / Pemberian obat sebelumnya :
                        <div class="content-area"><?php echo e($data['riwayat_penyakit'] ?? ''); ?></div>
                    </td>
                </tr>
                <tr>
                    <td>Riwayat alergi</td>
                    <td>:</td>
                    <td><?php echo e($data['riwayat_alergi'] ?? ''); ?></td>
                </tr>
                <tr class="tall-row">
                    <td colspan="3">
                        Keluhan :
                        <div class="content-area"><?php echo e($data['keluhan'] ?? ''); ?></div>
                    </td>
                </tr>
                <tr>
                    <td>Kapan Pasien Terakhir berkunjung ke Apotek</td>
                    <td>:</td>
                    <td><?php echo e($visite_text); ?></td>
                </tr>
                <tr class="tall-row">
                    <td colspan="3">
                        Tindak lanjut :
                        <div class="content-area"><?php echo e($data['tindakan'] ?? ''); ?></div>
                    </td>
                </tr>
            </table>

            <div class="signature-section">
                <!-- <div class="signature-box">
                    <p>Pasien</p>
                    <div class="dot-line">...........................</div>
                </div> -->
                <div class="signature-box">
                    <p>Apoteker</p>
                    <div class="dot-line"><?php echo e($rk['empat'] ?? ''); ?></div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
