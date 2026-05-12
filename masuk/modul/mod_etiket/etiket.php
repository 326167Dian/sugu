<?php
include_once '../../../configurasi/koneksi.php';

$module = isset($_GET['module']) ? $_GET['module'] : '';
$kd_trkasir = isset($_GET['kd_trkasir']) ? trim($_GET['kd_trkasir']) : '';
$aturan_kali = isset($_GET['aturan_kali']) ? trim($_GET['aturan_kali']) : '';
$aturan_dosis = isset($_GET['aturan_dosis']) ? trim($_GET['aturan_dosis']) : '';
$obat_luar = isset($_GET['obat_luar']) ? 1 : 0;
$jumlah_etiket = isset($_GET['jumlah_etiket']) ? (int) $_GET['jumlah_etiket'] : 1;

if ($jumlah_etiket < 1) {
    $jumlah_etiket = 1;
}

if ($jumlah_etiket > 200) {
    $jumlah_etiket = 200;
}

$dataTrx = null;
$error = '';

$ah = $db->prepare("SELECT * FROM setheader ");
$ah->execute();
$rh = $ah->fetch(PDO::FETCH_ASSOC);
$myImage = "../../images/".$rh['logo'];

if ($kd_trkasir !== '') {
    $stmt = $db->prepare("SELECT kd_trkasir, tgl_trkasir, nm_pelanggan FROM trkasir WHERE kd_trkasir = ? LIMIT 1");
    $stmt->execute([$kd_trkasir]);
    $dataTrx = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$dataTrx) {
        $error = 'Data transaksi tidak ditemukan untuk kode: ' . htmlspecialchars($kd_trkasir, ENT_QUOTES, 'UTF-8');
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Etiket</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 0;
        }

        body {
            margin: 0;
            padding: 12px;
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
        }

        .input-wrapper {
            max-width: 420px;
            margin-bottom: 12px;
            background: #fff;
            border: 1px solid #ccc;
            padding: 12px;
        }

        .input-wrapper label {
            display: block;
            margin-bottom: 6px;
            font-size: 13px;
            font-weight: bold;
        }

        .input-wrapper input {
            width: 100%;
            box-sizing: border-box;
            padding: 8px;
            border: 1px solid #aaa;
            margin-bottom: 8px;
        }

        .checkbox-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 10px;
        }

        .checkbox-row label {
            margin: 0;
            font-size: 13px;
            font-weight: bold;
        }

        .checkbox-row input {
            width: auto;
            margin: 0;
            padding: 0;
            border: 0;
        }

        .input-wrapper button {
            padding: 8px 12px;
            border: 0;
            background: #337ab7;
            color: #fff;
            cursor: pointer;
        }

        .error {
            color: #b90000;
            font-size: 12px;
            margin-top: 6px;
        }

        .label-container {
            width: 70mm;
            height: 38mm;
            background-color: white;
            border: 1px solid #000;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            position: relative;
            page-break-inside: avoid;
        }

        .label-container.obat-luar {
            background-color: #d9f2ff;
        }

        .obat-luar-badge {
            font-size: 6pt;
            font-weight: bold;
            border: 1px solid #000;
            padding: 0.4mm 1.2mm;
            background: #fff;
            display: inline-block;
            line-height: 1.1;
        }

        .header {
            display: flex;
            align-items: center;
            padding: 2mm;
            border-bottom: 1.5px solid #000;
            height: 14mm;
        }

        .logo-box {
            width: 12mm;
            margin-right: 2mm;
        }

        .logo-box img {
            width: 100%;
            height: auto;
        }

        .pharmacy-info {
            flex-grow: 1;
            text-align: center;
        }

        .pharmacy-name {
            font-size: 10pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }

        .pharmacy-address,
        .pharmacy-contact {
            font-size: 5.5pt;
            margin: 1px 0;
            line-height: 1.1;
        }

        .body-content {
            padding: 2mm;
            flex-grow: 1;
            font-size: 7pt;
            display: flex;
            flex-direction: column;
            gap: 2mm;
        }

        .row-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 2mm;
        }

        .row-top .left-col,
        .row-top .right-col {
            white-space: nowrap;
        }

        .row-top .mid-col {
            flex: 1;
            text-align: center;
        }

        .input-line {
            border-bottom: 1px solid #000;
            display: inline-block;
            min-width: 15mm;
            padding: 0 1px;
        }

        .patient-section {
            text-align: center;
            margin-top: 0;
            position: relative;
            top: -1.5mm;
        }

        .patient-name {
            font-size: 9pt;
            font-weight: bold;
            display: block;
            border-bottom: 1px solid #000;
            margin: 1mm auto;
            width: 80%;
            min-height: 4mm;
        }

        .usage-section {
            font-weight: bold;
            font-size: 8pt;
            margin-top: 1mm;
        }

        .usage-line {
            border-bottom: 1px solid #000;
            width: 10mm;
            display: inline-block;
            text-align: center;
        }

        .print-sheet {
            width: 210mm;
            display: grid;
            grid-template-columns: repeat(3, 70mm);
            grid-auto-rows: 38mm;
            justify-content: start;
            align-content: start;
            margin: 0;
            padding: 0;
        }

        @media print {
            body {
                background: #fff;
                padding: 0;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .print-sheet {
                width: 210mm;
                margin: 0;
                padding: 0;
            }

            .input-wrapper {
                display: none;
            }

            .label-container,
            .label-container * {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .label-container.obat-luar {
                background-color: #d9f2ff !important;
            }
        }
    </style>
</head>
<body>

    <div class="input-wrapper">
        <form method="GET" action="">
            <?php if ($module !== '') { ?>
                <input type="hidden" name="module" value="<?php echo htmlspecialchars($module, ENT_QUOTES, 'UTF-8'); ?>">
            <?php } ?>
            <label for="kd_trkasir">Input KD Transaksi Kasir (kd_trkasir)</label>
            <input type="text" id="kd_trkasir" name="kd_trkasir" value="<?php echo htmlspecialchars($kd_trkasir, ENT_QUOTES, 'UTF-8'); ?>" required>

            <label for="aturan_dosis">Aturan Pakai misal 1 tablet tiap 8 jam</label>
            <input type="text" id="aturan_dosis" name="aturan_dosis" value="<?php echo htmlspecialchars($aturan_dosis, ENT_QUOTES, 'UTF-8'); ?>" placeholder="contoh: 1">

            <label for="jumlah_etiket">Jumlah Etiket Print</label>
            <input type="number" id="jumlah_etiket" name="jumlah_etiket" min="1" max="200" value="<?php echo (int) $jumlah_etiket; ?>" required>

            <div class="checkbox-row">
                <input type="checkbox" id="obat_luar" name="obat_luar" value="1" <?php echo $obat_luar ? 'checked' : ''; ?>>
                <label for="obat_luar">OBAT LUAR</label>
            </div>

            <button type="submit">Tampilkan Etiket</button>
            <?php if ($dataTrx) { ?>
                <button type="button" onclick="window.print()" style="margin-left:8px; background:#28a745;">Print Etiket</button>
            <?php } ?>
            <?php if ($error !== '') { ?>
                <div class="error"><?php echo $error; ?></div>
            <?php } ?>
        </form>
    </div>

    <?php if ($dataTrx) { ?>

        <div class="print-sheet">
            <?php for ($i = 1; $i <= $jumlah_etiket; $i++) { ?>
                <div class="label-container <?php echo $obat_luar ? 'obat-luar' : ''; ?>">
                    <div class="header">
                        <div class="logo-box">
                            <img src="<?php echo htmlspecialchars($myImage, ENT_QUOTES, 'UTF-8'); ?>" alt="Logo">
                        </div>
                        <div class="pharmacy-info">
                            <p class="pharmacy-name"><?php echo htmlspecialchars($rh['satu'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <p class="pharmacy-address"><?php echo htmlspecialchars($rh['dua'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <p class="pharmacy-address"><?php echo htmlspecialchars('SIA No. : '. $rh['lima'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <p class="pharmacy-address"><?php echo htmlspecialchars('APJ : '. $rh['empat'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <p class="pharmacy-address"><?php echo htmlspecialchars('Telp : '. $rh['enam'], ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>
                    </div>

                    <div class="body-content">
                        <div class="row-top">
                            <span class="left-col">No. <span class="input-line"><?php echo htmlspecialchars($dataTrx['kd_trkasir'], ENT_QUOTES, 'UTF-8'); ?></span></span>
                            <span class="mid-col"><?php if ($obat_luar) { ?><span class="obat-luar-badge">OBAT LUAR</span><?php } ?></span>
                            <span class="right-col">Tgl. <span class="input-line"><?php echo htmlspecialchars($dataTrx['tgl_trkasir'], ENT_QUOTES, 'UTF-8'); ?></span></span>
                        </div>

                        <div class="patient-section">
                            <span>Kepada Yth :</span>
                            <span class="patient-name"><?php echo htmlspecialchars($dataTrx['nm_pelanggan'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <span><?php echo htmlspecialchars($aturan_dosis, ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>

                        <div class="usage-section">
                            Sehari <span class="usage-line"><?php echo htmlspecialchars($aturan_kali, ENT_QUOTES, 'UTF-8'); ?></span> x <span class="usage-line"><?php echo htmlspecialchars($aturan_dosis, ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } ?>

</body>
</html>