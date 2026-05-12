<?php
session_start();
include "../../../configurasi/koneksi.php";

$id_meso = isset($_GET['id']) ? intval($_GET['id']) : 0;
$data = null;
$data_pelanggan = null;
$alert = '';

$tampil_setheader = $db->query("SELECT * FROM setheader ORDER BY id_setheader");
$rk = $tampil_setheader->fetch(PDO::FETCH_ASSOC);

if ($id_meso > 0) {
    $stmt = $db->prepare("SELECT m.*, p.nm_pelanggan, p.alamat_pelanggan FROM meso m 
        LEFT JOIN pelanggan p ON m.id_pelanggan = p.id_pelanggan
        WHERE m.id_meso = ?");
    $stmt->execute([$id_meso]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        $alert = "Data MESO tidak ditemukan.";
    }
} else {
    $alert = "Parameter id meso belum diisi.";
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

// Parse data obat dari JSON
$data_obat = [];
if (!empty($data['data_obat'])) {
    $data_obat = json_decode($data['data_obat'], true);
    if (!is_array($data_obat)) {
        $data_obat = [];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Pelaporan ESO</title>
    <style>
        :root {
            --bg-yellow: #ffff00;
            --border-color: #000;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            padding: 20px;
            font-size: 12px;
        }

        .form-container {
            background-color: var(--bg-yellow);
            max-width: 900px;
            margin: 0 auto;
            border: 2px solid var(--border-color);
            padding: 10px;
        }

        .header {
            border-bottom: 2px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }

        .section-title {
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 10px;
            display: block;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        th, td {
            border: 1px solid var(--border-color);
            padding: 5px;
            vertical-align: top;
        }

        .grid-container {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 10px;
            margin-bottom: 15px;
        }

        .checkbox-group {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        input[type="text"], textarea {
            width: 100%;
            border: none;
            border-bottom: 1px dotted #000;
            background: transparent;
            font-family: inherit;
        }

        input[type="text"][readonly] {
            background-color: #ffffcc;
            font-weight: bold;
        }

        .obat-table th {
            font-size: 10px;
            text-align: center;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 20px;
            margin-top: 20px;
        }

        .signature-area {
            text-align: center;
            margin-top: 30px;
        }

        @media print {
            body { background: none; padding: 0; }
            .form-container { border: 1px solid #000; }
        }
    </style>
</head>
<body>

<?php if ($alert !== '') { ?>
    <div style="background-color: #f8d7da; color: #721c24; padding: 10px; margin: 10px auto; max-width: 900px; border: 1px solid #f5c6cb; border-radius: 4px;">
        <?php echo e($alert); ?>
    </div>
<?php } ?>

<div class="form-container">
    <div class="header">
        <strong>FORMULIR PELAPORAN EFEK SAMPING OBAT (ESO)</strong>
        <span>Kode Sumber Data : <?php echo e($data['kode_sumber_data'] ?? '_________'); ?></span>
    </div>

    <span class="section-title">PENDERITA</span>
    <table>
        <tr>
            <td>Nama (Singkatan): <input type="text" value="<?php echo e($data['nama_singkat'] ?? ''); ?>" readonly></td>
            <td>Umur: <input type="text" value="<?php echo e($data['umur'] ?? ''); ?>" readonly></td>
            <td>Suku: <input type="text" value="<?php echo e($data['suku'] ?? ''); ?>" readonly></td>
            <td>Berat Badan: <input type="text" value="<?php echo e($data['berat_badan'] ?? ''); ?>" readonly></td>
            <td>Pekerjaan: <input type="text" value="<?php echo e($data['pekerjaan'] ?? ''); ?>" readonly></td>
        </tr>
    </table>

    <div class="grid-container">
        <div class="checkbox-group">
            <strong>Kelamin (Beri Tanda √):</strong>
            <label><input type="checkbox" <?php echo (isset($data['jenis_kelamin']) && $data['jenis_kelamin'] == 'L') ? 'checked' : ''; ?>> Pria</label>
            <label><input type="checkbox" <?php echo (isset($data['jenis_kelamin']) && $data['jenis_kelamin'] == 'P') ? 'checked' : ''; ?>> Wanita</label>
            <label><input type="checkbox" <?php echo (isset($data['status_hamil']) && $data['status_hamil'] == 'hamil') ? 'checked' : ''; ?>> Hamil</label>
            <label><input type="checkbox" <?php echo (isset($data['status_hamil']) && $data['status_hamil'] == 'tidak_hamil') ? 'checked' : ''; ?>> Tidak Hamil</label>
            <label><input type="checkbox" <?php echo (isset($data['status_hamil']) && $data['status_hamil'] == 'tidak_tahu') ? 'checked' : ''; ?>> Tidak Tahu</label>
        </div>
        <div>
            <strong>Penyakit Utama:</strong>
            <textarea rows="3" readonly><?php echo e($data['penyakit_utama'] ?? ''); ?></textarea>
            <br><br>
            <strong>Penyakit / Kondisi Lain yang Menyertai:</strong>
            <div class="checkbox-group">
                <label><input type="checkbox" <?php echo (!empty($data['gangguan_ginjal'])) ? 'checked' : ''; ?>> Gangguan Ginjal</label>
                <label><input type="checkbox" <?php echo (!empty($data['gangguan_hati'])) ? 'checked' : ''; ?>> Gangguan Hati</label>
                <label><input type="checkbox" <?php echo (!empty($data['alergi'])) ? 'checked' : ''; ?>> Alergi</label>
                <label><input type="checkbox" <?php echo (!empty($data['kondisi_medis_lain'])) ? 'checked' : ''; ?>> Kondisi medis lainnya</label>
                <?php if (!empty($data['kondisi_medis_lain_ket'])) { ?>
                    <small style="display:block; margin-top:5px;"><?php echo e($data['kondisi_medis_lain_ket']); ?></small>
                <?php } ?>
            </div>
        </div>
        <div>
            <strong>Kesudahan Penyakit Utama:</strong>
            <div class="checkbox-group">
                <label><input type="checkbox" <?php echo (isset($data['kesudahan_penyakit']) && $data['kesudahan_penyakit'] == 'sembuh') ? 'checked' : ''; ?>> Sembuh</label>
                <label><input type="checkbox" <?php echo (isset($data['kesudahan_penyakit']) && $data['kesudahan_penyakit'] == 'sembuh_gejala_sisa') ? 'checked' : ''; ?>> Sembuh dengan gejala sisa</label>
                <label><input type="checkbox" <?php echo (isset($data['kesudahan_penyakit']) && $data['kesudahan_penyakit'] == 'belum_sembuh') ? 'checked' : ''; ?>> Belum sembuh</label>
                <label><input type="checkbox" <?php echo (isset($data['kesudahan_penyakit']) && $data['kesudahan_penyakit'] == 'meninggal') ? 'checked' : ''; ?>> Meninggal</label>
                <label><input type="checkbox" <?php echo (isset($data['kesudahan_penyakit']) && $data['kesudahan_penyakit'] == 'tidak_tahu') ? 'checked' : ''; ?>> Tidak Tahu</label>
            </div>
        </div>
    </div>

    <span class="section-title">EFEK SAMPING OBAT</span>
    <table class="eso-table">
        <tr>
            <th width="30%">Bentuk / Manifestasi ESO yang Terjadi / Keluhan Lain</th>
            <th width="20%">Masalah pada Mutu/Kualitas Produk Obat</th>
            <th width="20%">Saat/Tanggal Mula Terjadi</th>
            <th width="30%">Kesudahan ESO (Beri Tanda √)</th>
        </tr>
        <tr>
            <td><textarea rows="4" readonly><?php echo e($data['manifestasi_eso'] ?? ''); ?></textarea></td>
            <td><textarea rows="4" readonly><?php echo e($data['masalah_mutu_produk'] ?? ''); ?></textarea></td>
            <td><input type="text" value="<?php echo e($data['tanggal_mula_eso'] ?? ''); ?>" readonly></td>
            <td>
                <div class="checkbox-group">
                    <label><input type="checkbox" <?php echo (isset($data['kesudahan_eso']) && $data['kesudahan_eso'] == 'sembuh') ? 'checked' : ''; ?>> Sembuh</label>
                    <label><input type="checkbox" <?php echo (isset($data['kesudahan_eso']) && $data['kesudahan_eso'] == 'sembuh_gejala_sisa') ? 'checked' : ''; ?>> Sembuh dengan gejala sisa</label>
                    <label><input type="checkbox" <?php echo (isset($data['kesudahan_eso']) && $data['kesudahan_eso'] == 'belum_sembuh') ? 'checked' : ''; ?>> Belum sembuh</label>
                    <label><input type="checkbox" <?php echo (isset($data['kesudahan_eso']) && $data['kesudahan_eso'] == 'meninggal') ? 'checked' : ''; ?>> Meninggal</label>
                    <label><input type="checkbox" <?php echo (isset($data['kesudahan_eso']) && $data['kesudahan_eso'] == 'tidak_tahu') ? 'checked' : ''; ?>> Tidak Tahu</label>
                </div>
            </td>
        </tr>
    </table>
    <p>Riwayat ESO yang Pernah Dialami: <input type="text" value="<?php echo e($data['riwayat_eso'] ?? ''); ?>" readonly></p>

    <span class="section-title">OBAT</span>
    <table class="obat-table">
        <thead>
            <tr>
                <th rowspan="2">Nama (Dagang/Generik)</th>
                <th rowspan="2">Bentuk Sediaan</th>
                <th rowspan="2">Obat JKN (√)</th>
                <th rowspan="2">No. Batch</th>
                <th rowspan="2">Obat yang Dicurigai (√)</th>
                <th colspan="4">Pemberian</th>
                <th rowspan="2">Indikasi Penggunaan</th>
            </tr>
            <tr>
                <th>Cara</th>
                <th>Dosis</th>
                <th>Tgl Mula</th>
                <th>Tgl Akhir</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if (!empty($data_obat) && is_array($data_obat)) {
                foreach ($data_obat as $obat) {
            ?>
            <tr>
                <td><input type="text" value="<?php echo e($obat['nama'] ?? ''); ?>" readonly></td>
                <td><input type="text" value="<?php echo e($obat['bentuk'] ?? ''); ?>" readonly></td>
                <td><input type="checkbox" <?php echo (!empty($obat['jkn'])) ? 'checked' : ''; ?>></td>
                <td><input type="text" value="<?php echo e($obat['batch'] ?? ''); ?>" readonly></td>
                <td><input type="checkbox" <?php echo (!empty($obat['dicurigai'])) ? 'checked' : ''; ?>></td>
                <td><input type="text" value="<?php echo e($obat['cara'] ?? ''); ?>" readonly></td>
                <td><input type="text" value="<?php echo e($obat['dosis'] ?? ''); ?>" readonly></td>
                <td><input type="text" value="<?php echo e($obat['tgl_mula'] ?? ''); ?>" readonly></td>
                <td><input type="text" value="<?php echo e($obat['tgl_akhir'] ?? ''); ?>" readonly></td>
                <td><input type="text" value="<?php echo e($obat['indikasi'] ?? ''); ?>" readonly></td>
            </tr>
            <?php 
                }
            } else {
                echo '<tr><td colspan="10" style="text-align: center;">Tidak ada data obat</td></tr>';
            }
            ?>
        </tbody>
    </table>

    <div class="footer-grid">
        <div>
            <strong>Keterangan Tambahan:</strong>
            <textarea rows="6" readonly><?php echo e($data['keterangan_tambahan'] ?? ''); ?></textarea>
        </div>
        <div>
            <div style="border: 1px solid #000; padding: 10px;">
                <strong>Data Laboratorium (bila ada):</strong>
                <textarea rows="3" readonly><?php echo e($data['data_laboratorium'] ?? ''); ?></textarea>
                <p>Tgl Pemeriksaan: <input type="text" style="width: 50%;" value="<?php echo e($data['tanggal_pemeriksaan_lab'] ?? ''); ?>" readonly></p>
            </div>
            <div class="signature-area">
                <p><?php echo e($data['tanggal_laporan'] ?? '........., tgl .................... 20....'); ?></p>
                <p>Tanda Tangan Pelapor</p>
                <br><br>
                <p>( <?php echo e($data['nama_pelapor'] ?? '.......................................'); ?> )</p>
            </div>
        </div>
    </div>
</div>

</body>
</html>