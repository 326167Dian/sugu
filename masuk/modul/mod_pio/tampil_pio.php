<?php
session_start();
include "../../../configurasi/koneksi.php";

$id_pio = isset($_GET['id']) ? intval($_GET['id']) : 0;
$data = null;
$alert = '';

$tampil_setheader = $db->query("SELECT * FROM setheader ORDER BY id_setheader");
$rk = $tampil_setheader->fetch(PDO::FETCH_ASSOC);

if ($id_pio > 0) {
    $stmt = $db->prepare("SELECT pr.*, p.nm_pelanggan, p.alamat_pelanggan 
        FROM pio pr
        LEFT JOIN pelanggan p ON pr.id_pelanggan = p.id_pelanggan
        WHERE pr.id_pio = ?");
    $stmt->execute([$id_pio]);
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        $alert = "Data PIO tidak ditemukan.";
    }
} else {
    $alert = "Parameter id PIO belum diisi.";
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
    <title>Formulir 8. Dokumentasi PIO</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php if ($alert !== '') { ?>
    <div style="background-color: #f8d7da; color: #721c24; padding: 10px; margin: 10px auto; max-width: 900px; border: 1px solid #f5c6cb; border-radius: 4px;">
        <?php echo e($alert); ?>
    </div>
<?php } ?>

    <div class="container">
        <div class="header-label">Formulir 8. Dokumentasi PIO</div>
        
        <div class="main-border">
            <h3>DOKUMENTASI PELAYANAN INFORMASI OBAT (PIO) <?php echo e($rk['satu'] ?? ''); ?></h3>
            
            <div class="row section-top">
                <div style="flex: 1;">
                    <div class="col">No. <input type="text" class="input-line" style="width: 50px;" value="<?php echo e($data['no_pio'] ?? ''); ?>" readonly></div>
                    <div class="col">Tanggal: <input type="date" class="input-line" value="<?php echo e($data['tanggal'] ?? ''); ?>" readonly></div>
                    <div class="col">Waktu: <input type="time" class="input-line" value="<?php echo e($data['waktu'] ?? ''); ?>" readonly></div>
                    <div class="col">Metode: 
                        <input type="text" class="input-line" value="<?php echo e($data['metode'] ?? ''); ?>" readonly>
                    </div>
                </div>
                <?php if (!empty($rk['logo'])) { ?>
                    <div class="logo-container">
                        <img src="../../images/<?php echo e($rk['logo']); ?>" alt="Logo" class="logo">
                    </div>
                <?php } ?>
            </div>

            <div class="section-title">Identitas Penanya</div>
            <div class="content-row flex-row">
                <div style="flex: 2;">Nama: <input type="text" class="input-line" style="width: 80%;" value="<?php echo e($data['nama_penanya'] ?? ''); ?>" readonly></div>
                <div style="flex: 1;">No. Telp. <input type="text" class="input-line" style="width: 60%;" value="<?php echo e($data['no_telp_penanya'] ?? ''); ?>" readonly></div>
            </div>
            <div class="content-row">
                Status : 
                <label><input type="radio" name="status" <?php echo (isset($data['status_penanya']) && $data['status_penanya'] == 'Pasien') ? 'checked' : ''; ?>> Pasien</label> / 
                <label><input type="radio" name="status" <?php echo (isset($data['status_penanya']) && $data['status_penanya'] == 'Keluarga Pasien') ? 'checked' : ''; ?>> Keluarga Pasien</label> / 
                <label><input type="radio" name="status" <?php echo (isset($data['status_penanya']) && $data['status_penanya'] == 'Petugas Kesehatan') ? 'checked' : ''; ?>> Petugas Kesehatan</label>
                (<input type="text" class="input-line" style="width: 40%;" placeholder="instansi/jabatan" value="<?php echo e($data['status_penanya_ket'] ?? ''); ?>" readonly>)*
            </div>

            <div class="section-title">Data Pasien</div>
            <div class="content-row">
                Nama: <input type="text" class="input-line" style="width: 200px;" value="<?php echo e($data['nm_pelanggan'] ?? ''); ?>" readonly>
            </div>
            <div class="content-row">
                Umur: <input type="number" class="input-line" style="width: 40px;" value="<?php echo e($data['umur_pasien'] ?? ''); ?>" readonly> tahun; 
                Tinggi: <input type="number" class="input-line" style="width: 40px;" value="<?php echo e($data['tinggi_pasien'] ?? ''); ?>" readonly> cm; 
                Berat: <input type="number" class="input-line" style="width: 40px;" value="<?php echo e($data['berat_pasien'] ?? ''); ?>" readonly> kg;
            </div>
            <div class="content-row">
                Jenis kelamin: 
                <label><input type="radio" name="jk" <?php echo (isset($data['jenis_kelamin']) && $data['jenis_kelamin'] == 'L') ? 'checked' : ''; ?>> Laki-laki</label> / 
                <label><input type="radio" name="jk" <?php echo (isset($data['jenis_kelamin']) && $data['jenis_kelamin'] == 'P') ? 'checked' : ''; ?>> Perempuan</label> )*
            </div>
            <div class="content-row flex-row">
                <div style="flex: 1;">
                    Kehamilan: <label><input type="radio" name="hamil" <?php echo (!empty($data['kehamilan'])) ? 'checked' : ''; ?>> Ya</label> 
                    (<input type="text" class="input-line" style="width: 30px;" value="<?php echo e($data['kehamilan_minggu'] ?? ''); ?>" readonly> minggu) / 
                    <label><input type="radio" name="hamil" <?php echo (empty($data['kehamilan'])) ? 'checked' : ''; ?>> Tidak</label> )*
                </div>
                <div style="flex: 1;">
                    Menyusui: <label><input type="radio" name="busui" <?php echo (!empty($data['menyusui'])) ? 'checked' : ''; ?>> Ya</label> / 
                    <label><input type="radio" name="busui" <?php echo (empty($data['menyusui'])) ? 'checked' : ''; ?>> Tidak</label> )*
                </div>
            </div>

            <div class="section-title">Pertanyaan</div>
            <div class="content-row">Uraian Pertanyaan:</div>
            <textarea class="input-area" rows="3" readonly><?php echo e($data['uraian_pertanyaan'] ?? ''); ?></textarea>
            
            <div class="content-row" style="margin-top: 5px;">Jenis Pertanyaan:</div>
            <table class="question-grid">
                <tr>
                    <td class="checkbox-cell"><input type="checkbox" <?php echo (!empty($data['jenis_pertanyaan_identifikasi_obat'])) ? 'checked' : ''; ?>></td><td>Identifikasi Obat</td>
                    <td class="checkbox-cell"><input type="checkbox" <?php echo (!empty($data['jenis_pertanyaan_stabilitas'])) ? 'checked' : ''; ?>></td><td>Stabilitas</td>
                    <td class="checkbox-cell"><input type="checkbox" <?php echo (!empty($data['jenis_pertanyaan_farmakokinetika'])) ? 'checked' : ''; ?>></td><td>Farmakokinetika</td>
                </tr>
                <tr>
                    <td class="checkbox-cell"><input type="checkbox" <?php echo (!empty($data['jenis_pertanyaan_interaksi_obat'])) ? 'checked' : ''; ?>></td><td>Interaksi Obat</td>
                    <td class="checkbox-cell"><input type="checkbox" <?php echo (!empty($data['jenis_pertanyaan_dosis'])) ? 'checked' : ''; ?>></td><td>Dosis</td>
                    <td class="checkbox-cell"><input type="checkbox" <?php echo (!empty($data['jenis_pertanyaan_farmakodinamika'])) ? 'checked' : ''; ?>></td><td>Farmakodinamika</td>
                </tr>
                <tr>
                    <td class="checkbox-cell"><input type="checkbox" <?php echo (!empty($data['jenis_pertanyaan_harga_obat'])) ? 'checked' : ''; ?>></td><td>Harga Obat</td>
                    <td class="checkbox-cell"><input type="checkbox" <?php echo (!empty($data['jenis_pertanyaan_keracunan'])) ? 'checked' : ''; ?>></td><td>Keracunan</td>
                    <td class="checkbox-cell"><input type="checkbox" <?php echo (!empty($data['jenis_pertanyaan_ketersediaan_obat'])) ? 'checked' : ''; ?>></td><td>Ketersediaan Obat</td>
                </tr>
                <tr>
                    <td class="checkbox-cell"><input type="checkbox" <?php echo (!empty($data['jenis_pertanyaan_kontra_indikasi'])) ? 'checked' : ''; ?>></td><td>Kontra Indikasi</td>
                    <td class="checkbox-cell"><input type="checkbox" <?php echo (!empty($data['jenis_pertanyaan_efek_samping'])) ? 'checked' : ''; ?>></td><td>Efek Samping Obat</td>
                    <td class="checkbox-cell" rowspan="2" style="border-right:none;"><input type="checkbox" <?php echo (!empty($data['jenis_pertanyaan_lain_lain'])) ? 'checked' : ''; ?>></td>
                    <td rowspan="2" style="border-left:none;">Lain-lain<br><input type="text" class="input-line" style="width: 90%;" value="<?php echo e($data['jenis_pertanyaan_lain_lain_ket'] ?? ''); ?>" readonly></td>
                </tr>
                <tr>
                    <td class="checkbox-cell"><input type="checkbox" <?php echo (!empty($data['jenis_pertanyaan_cara_pemakaian'])) ? 'checked' : ''; ?>></td><td>Cara Pemakaian</td>
                    <td class="checkbox-cell"><input type="checkbox" <?php echo (!empty($data['jenis_pertanyaan_penggunaan_terapeutik'])) ? 'checked' : ''; ?>></td><td>Penggunaan Terapeutik</td>
                </tr>
            </table>

            <div class="section-title">Jawaban</div>
            <textarea class="input-area" rows="4" readonly><?php echo e($data['jawaban'] ?? ''); ?></textarea>

            <div class="section-title">Referensi</div>
            <textarea class="input-area" rows="2" readonly><?php echo e($data['referensi'] ?? ''); ?></textarea>

            <div class="section-row border-bottom">
                Penyampaian Jawaban: 
                <label><input type="radio" name="waktu" <?php echo (isset($data['penyampaian_jawaban']) && $data['penyampaian_jawaban'] == 'Segera') ? 'checked' : ''; ?>> Segera</label> / 
                <label><input type="radio" name="waktu" <?php echo (isset($data['penyampaian_jawaban']) && $data['penyampaian_jawaban'] == 'Dalam 24 jam') ? 'checked' : ''; ?>> Dalam 24 jam</label> / 
                <label><input type="radio" name="waktu" <?php echo (isset($data['penyampaian_jawaban']) && $data['penyampaian_jawaban'] == 'Lebih dari 24 jam') ? 'checked' : ''; ?>> Lebih dari 24 jam</label> )*
            </div>

            <div class="footer-section">
                <div class="content-row">Apoteker yang menjawab:</div>
                <input type="text" class="input-line" style="width: 50%; margin-left: 10px; font-weight: bold;" value="<?php echo e($data['apoteker_penjawab'] ?? ''); ?>" readonly>
                <div class="content-row flex-row" style="margin-top: 10px;">
                    <div style="flex: 1;">Tanggal: <input type="date" class="input-line" value="<?php echo e($data['tanggal_jawab'] ?? ''); ?>" readonly></div>
                    <div style="flex: 1;">Waktu: <input type="time" class="input-line" value="<?php echo e($data['waktu_jawab'] ?? ''); ?>" readonly></div>
                </div>
                <div class="content-row">
                    Metode Jawaban : 
                    <label><input type="radio" name="metode_jwb" <?php echo (isset($data['metode_jawab']) && $data['metode_jawab'] == 'Lisan') ? 'checked' : ''; ?>> Lisan</label> / 
                    <label><input type="radio" name="metode_jwb" <?php echo (isset($data['metode_jawab']) && $data['metode_jawab'] == 'Tertulis') ? 'checked' : ''; ?>> Tertulis</label> / 
                    <label><input type="radio" name="metode_jwb" <?php echo (isset($data['metode_jawab']) && $data['metode_jawab'] == 'Telepon') ? 'checked' : ''; ?>> Telepon</label> )*
                </div>
            </div>
        </div>

        <div class="no-print" style="margin-top: 20px; text-align: center;">
            <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">Cetak Formulir</button>
        </div>
    </div>

</body>
</html>