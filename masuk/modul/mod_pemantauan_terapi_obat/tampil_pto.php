<?php
function e($value)
{
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

$input = $_POST;
if (empty($input)) {
    $input = $_GET;
}

if (empty($_POST) && isset($_GET['id_pto'])) {
    include "../../../configurasi/koneksi.php";
    $id_pto = intval($_GET['id_pto']);
    if ($id_pto > 0) {
        $stmt = $db->prepare("SELECT * FROM pto WHERE id_pto = ?");
        $stmt->execute([$id_pto]);
        $dataPto = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($dataPto) {
            $input = $dataPto;
        }
    }
}

$nm_pelanggan = $input['nm_pelanggan'] ?? '';
$jenis_kelamin_raw = strtoupper(trim($input['jenis_kelamin'] ?? ''));
$jenis_kelamin = $input['jenis_kelamin'] ?? '';
if ($jenis_kelamin_raw === 'PRIA') {
    $jenis_kelamin = 'Laki-laki';
} elseif ($jenis_kelamin_raw === 'WANITA') {
    $jenis_kelamin = 'Perempuan';
}

$umur = $input['umur'] ?? '';
$alamat_pelanggan = $input['alamat_pelanggan'] ?? '';
$tlp_pelanggan = $input['tlp_pelanggan'] ?? '';

$rows = [];
for ($i = 1; $i <= 2; $i++) {
    $rows[] = [
        'no' => $i,
        'tanggal' => $input['tanggal_' . $i] ?? '',
        'catatan' => $input['catatan_' . $i] ?? '',
        'obat' => $input['obat_' . $i] ?? '',
        'masalah' => $input['masalah_' . $i] ?? '',
        'tindak' => $input['tindak_' . $i] ?? ''
    ];
}

$tempat_ttd = $input['tempat_ttd'] ?? '........................';
$tanggal_ttd = $input['tanggal_ttd'] ?? '';
if (!empty($tanggal_ttd)) {
    $tanggal_ttd = date('d-m-Y', strtotime($tanggal_ttd));
} else {
    $tanggal_ttd = '20....';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Pemantauan Terapi Obat</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container">
        <div class="header">
            <p class="form-number">Formulir 9</p>
            <h1>DOKUMENTASI PEMANTAUAN TERAPI OBAT</h1>
        </div>

        <div class="patient-data">
            <table>
                <tr><td>Nama Pasien</td><td>:</td><td><?php echo e($nm_pelanggan); ?></td></tr>
                <tr><td>Jenis Kelamin</td><td>:</td><td><?php echo e($jenis_kelamin); ?></td></tr>
                <tr><td>Umur</td><td>:</td><td><?php echo e($umur); ?></td></tr>
                <tr><td>Alamat</td><td>:</td><td><?php echo e($alamat_pelanggan); ?></td></tr>
                <tr><td>No. Telepon</td><td>:</td><td><?php echo e($tlp_pelanggan); ?></td></tr>
            </table>
        </div>

        <table class="main-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 10%;">Tanggal</th>
                    <th style="width: 25%;">Catatan Pengobatan Pasien</th>
                    <th style="width: 20%;">Nama Obat, Dosis, Cara Pemberian</th>
                    <th style="width: 20%;">Identifikasi Masalah terkait Obat</th>
                    <th style="width: 20%;">Rekomendasi/ Tindak Lanjut</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $row) { ?>
                    <tr>
                        <td class="center"><?php echo e($row['no']); ?></td>
                        <td><?php echo e($row['tanggal']); ?></td>
                        <td class="no-padding">
                            <?php if (trim($row['catatan']) !== '') { ?>
                                <div class="sub-row no-border"><?php echo nl2br(e($row['catatan'])); ?></div>
                            <?php } else { ?>
                                <div class="sub-row">Riwayat penyakit</div>
                                <div class="sub-row">Riwayat penggunaan obat</div>
                                <div class="sub-row no-border">Riwayat alergi</div>
                            <?php } ?>
                        </td>
                        <td><?php echo nl2br(e($row['obat'])); ?></td>
                        <td><?php echo nl2br(e($row['masalah'])); ?></td>
                        <td><?php echo nl2br(e($row['tindak'])); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class="footer-sign">
            <p><?php echo e($tempat_ttd); ?>, <?php echo e($tanggal_ttd); ?></p>
            <br><br><br>
            <p>Apoteker</p>
        </div>
    </div>

</body>
</html>